<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use App\Models\CCAssignment;
use App\Models\VetterAssignment;
use App\Models\Course;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\ExamStatus;

class ExamController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Exam Creation and Management
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'section'   => 'required|string|max:255',
        ]);

        $course = Course::find($validated['course_id']);
        $semester = Semester::where('is_active', true)->first();

        Exam::create([
            'course_id'    => $course->id,
            'course_code'  => $course->course_code,
            'course_name'  => $course->course_name,
            'section'      => $validated['section'],
            'tos'          => $course->tos,
            'status'       => ExamStatus::ASSIGN_COORDINATOR,
            'created_by'   => auth()->id(),
            'semester_id'  => $semester?->id,
        ]);

        return redirect()->route('HOD.dashboard')->with('success', 'Exam slot created successfully!');
    }

    public function showCreateExamForm()
    {
        $courses = Course::all();
        return view('SEMS.create-exam', compact('courses'));
    }

    /*
    |--------------------------------------------------------------------------
    | Role Assignments (Coordinator and Vetter)
    |--------------------------------------------------------------------------
    */

    public function assignCoordinator(Request $request)
    {
        if (!in_array(auth()->user()->role_id, [1, 3])) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'user_id' => 'required|exists:users,id',
        ]);

        CCAssignment::create($validated);

        Exam::find($validated['exam_id'])->update(['status' => ExamStatus::DRAFT_QUESTION]);

        return back()->with('success', 'Course Coordinator assigned successfully!');
    }

    public function assignVetter(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $exam = Exam::findOrFail($validated['exam_id']);

        // Decode questions
        $questions = json_decode($exam->questions, true) ?? [];

        if (
            $exam->status !== ExamStatus::DRAFT_QUESTION_COMPLETE ||
            empty($questions) ||
            (is_array($questions) && count(array_filter($questions, function ($q) {
                return !empty($q['question']);
            })) === 0)
        ) //{
        //     return back()->with('error', 'Cannot assign vetter. Exam must have draft complete status with real questions.');
        // }

        if (VetterAssignment::where('exam_id', $exam->id)->exists()) {
            return back()->with('error', 'This exam already has a vetter assigned.');
        }

        VetterAssignment::create($validated);

        $exam->update(['status' => ExamStatus::VETTING]);

        return back()->with('success', 'Vetter assigned successfully!');
    }


    public function assignRole(Request $request)
    {
        $validated = $request->validate([
            'exam_id'   => 'required|exists:exams,id',
            'user_id'   => 'required|exists:users,id',
            'role_type' => 'required|in:cc,vetter',
        ]);

        $exam = Exam::find($validated['exam_id']);

        if ($validated['role_type'] === 'cc') {
            if (CCAssignment::where('exam_id', $exam->id)->exists()) {
                return back()->with('error', 'This exam is already assigned to a Course Coordinator.');
            }

            CCAssignment::create($validated);
            $exam->update(['status' => ExamStatus::DRAFT_QUESTION]);

            return back()->with('success', 'Course Coordinator assigned successfully!');
        }

        if ($validated['role_type'] === 'vetter') {
            if ($exam->status !== ExamStatus::DRAFT_QUESTION_COMPLETE || empty($exam->questions)) {
                return back()->with('error', 'Vetter can only be assigned after CC submits the completed draft question.');
            }

            VetterAssignment::create($validated);
            $exam->update(['status' => ExamStatus::VETTING]);

            return back()->with('success', 'Vetter assigned successfully!');
        }

        return back()->with('error', 'Invalid role type.');
    }

    /*
    |--------------------------------------------------------------------------
    | Question Submission
    |--------------------------------------------------------------------------
    */

    public function submitQuestion(Request $request)
{
    $validated = $request->validate([
        'exam_id' => 'required|exists:exams,id',
        // questions validations...
    ]);

    $exam = Exam::findOrFail($validated['exam_id']);

    $exam->questions = json_encode([
        ['question' => $request->question1, 'answer' => $request->answer1],
        ['question' => $request->question2, 'answer' => $request->answer2],
        ['question' => $request->question3, 'answer' => $request->answer3],
        ['question' => $request->question4, 'answer' => $request->answer4],
    ]);

    // Save draft without changing status
    if ($request->input('action') === 'draft') {
        $exam->save();
        return redirect()->route('CC.dashboard')->with('success', 'Draft saved successfully.');
    }

    if ($exam->status === ExamStatus::VETTED->value) {
        $exam->status = ExamStatus::PENDING_APPROVAL->value;
    } elseif (
        $exam->status === ExamStatus::DRAFT_QUESTION->value ||
        $exam->status === ExamStatus::REVISE_REQUESTED->value
    ) {
        $exam->status = ExamStatus::DRAFT_QUESTION_COMPLETE->value;

        $tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
        foreach ($tos as &$row) {
            $row['cc'] = 1;
        }
        $exam->tos = $tos;
    }

    $exam->save();

    return redirect()->route('CC.dashboard')->with('success', 'Question submitted successfully.');
}

    public function showCreateQuestionForm(Request $request)
    {
        $exam = Exam::findOrFail($request->exam_id);
        $vetterComments = $exam->vetter_comments ?? [];

        return view('SEMS.create-question', compact('exam', 'vetterComments'));
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboards (HOD, CC, Vetter, SEMS)
    |--------------------------------------------------------------------------
    */

    public function hodDashboard(Request $request)
    {
        if (auth()->user()->role_id !== 3 && auth()->user()->role_id !== 1) {
            abort(403, 'Unauthorized.');
        }

        $activeSemester = Semester::where('is_active', true)->first();
        $semesters = Semester::all();
        $status = $request->input('status');

        $query = Exam::where('semester_id', $activeSemester->id);

        if ($status) {
            $query->where('status', $status);
        }

        $exams = (auth()->user()->role_id == 1)
            ? $query->get()
            : $query->where('created_by', auth()->id())->get();

        $academicians = User::where('role_id', 5)->get();
        $assignedCCIds = CCAssignment::pluck('user_id')->toArray();
        $assignedVetterIds = VetterAssignment::pluck('user_id')->toArray();

        $availableCCs = $academicians->whereNotIn('id', $assignedCCIds);
        $availableVetters = $academicians->whereNotIn('id', $assignedVetterIds);

        return view('SEMS.HOD-dashboard', compact('exams', 'academicians', 'availableCCs', 'availableVetters', 'semesters', 'activeSemester'));
    }

    public function ccDashboard()
    {
        if (auth()->user()->role_id !== 5 && auth()->user()->role_id !== 1) {
            abort(403, 'Unauthorized.');
        }

        $user = auth()->user();
        $activeSemesterId = $this->getActiveSemesterId();

        $exams = ($user->role_id == 1)
            ? Exam::where('semester_id', $activeSemesterId)->get()
            : Exam::whereIn('id', CCAssignment::where('user_id', $user->id)->pluck('exam_id'))
                  ->where('semester_id', $activeSemesterId)
                  ->get();

        $semesters = Semester::all();
        $activeSemester = Semester::where('is_active', true)->first();

        return view('SEMS.CC-dashboard', compact('exams', 'semesters', 'activeSemester'));
    }

    public function vetterDashboard()
    {
        if (auth()->user()->role_id !== 5 && auth()->user()->role_id !== 1) {
            abort(403, 'Unauthorized.');
        }

        $user = auth()->user();
        $activeSemesterId = $this->getActiveSemesterId();

        $exams = ($user->role_id == 1)
            ? Exam::where('semester_id', $activeSemesterId)->get()
            : Exam::whereIn('id', VetterAssignment::where('user_id', $user->id)->pluck('exam_id'))
                  ->where('semester_id', $activeSemesterId)
                  ->get();

        $semesters = Semester::all();
        $activeSemester = Semester::where('is_active', true)->first();

        return view('SEMS.vetters-dashboard', compact('exams', 'semesters', 'activeSemester'));
    }

    public function showSEMSDashboard()
    {
        if (auth()->user()->role_id != 1) {
            abort(403, 'Unauthorized.');
        }

        $semesters = Semester::all();
        $activeSemester = Semester::where('is_active', true)->first();
        $exams = Exam::all();

        return view('SEMS.SEMS-dashboard', compact('exams', 'semesters', 'activeSemester'));
    }

    /*
    |--------------------------------------------------------------------------
    | Vetter Review
    |--------------------------------------------------------------------------
    */

    public function showVetterReview(Request $request)
    {
        $exam = Exam::findOrFail($request->exam_id);
        return view('SEMS.question-review', compact('exam'));
    }

    public function submitVetterReview(Request $request)
{
    $validated = $request->validate([
        'exam_id' => 'required|exists:exams,id',
        'comments' => 'nullable|array',
        'tos' => 'nullable|array',
    ]);

    $exam = Exam::findOrFail($validated['exam_id']);

    // Decode original data
    $originalTos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
    $submittedTos = $validated['tos'] ?? [];

    foreach ($originalTos as $index => &$row) {
        $row['vetter'] = isset($submittedTos[$index]['vetter']) ? 1 : 0;
    }

    $exam->tos = $originalTos;

    // Handle vetter comments
    $newComments = $validated['comments'] ?? [];

    // Get existing comment history (array of arrays)
    $existingHistory = json_decode($exam->vetter_comments, true) ?? [];

    // Append new structured logs
    foreach ($newComments as $index => $commentText) {
        $existingHistory[$index][] = [
            'name' => auth()->user()->name,
            'timestamp' => now()->toDateTimeString(),
            'comment' => $commentText,
        ];
    }

    $exam->vetter_comments = $existingHistory;
    $exam->status = ExamStatus::VETTED;
    $exam->save();

    return redirect()->route('vetters.dashboard')->with('success', 'Review submitted successfully!');
}


    public function vetterReviewPage(Request $request)
    {
        $exam = Exam::findOrFail($request->query('exam_id'));
        return view('SEMS.question-review', compact('exam'));
    }

    /*
    |--------------------------------------------------------------------------
    | Semester Management
    |--------------------------------------------------------------------------
    */

    public function activate(Request $request, Semester $semester)
    {
        Semester::query()->update(['is_active' => false]);
        $semester->is_active = true;
        $semester->save();

        return back()->with('success', 'Semester activated!');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    private function getActiveSemesterId()
    {
        return Semester::where('is_active', true)->value('id');
    }

    public function approveExam(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'tos' => 'nullable|array',
        ]);

        $exam = Exam::findOrFail($validated['exam_id']);
        $originalTos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
        $submittedTos = $validated['tos'] ?? [];

        foreach ($originalTos as $index => &$row) {
            $row['hod'] = isset($submittedTos[$index]['hod']) ? 1 : 0;
        }

        $exam->tos = $originalTos;

        // Handle button action
        if ($request->input('action') === 'draft') {
            $exam->status = ExamStatus::PENDING_APPROVAL;
            $exam->save();
            return back()->with('success', 'Saved as draft. You can continue reviewing later.');
        }

        // Default: final approval
        $exam->status = ExamStatus::APPROVED;
        $exam->save();

        return redirect()->route('HOD.dashboard')->with('success', 'Exam approved successfully!');
    }


    public function denyQuestion(Request $request)
    {
        $exam = Exam::findOrFail($request->exam_id);

        // ❌ Remove previous vetter assignments
        \App\Models\VetterAssignment::where('exam_id', $exam->id)->delete();

        // 🔁 Reset status so CC can revise
        $exam->status = \App\Enums\ExamStatus::DRAFT_QUESTION;

        $exam->save();

        return back()->with('error', 'Exam denied. Sent back to CC for revision.');
    }


    public function showApprovalQuestion(Request $request)
    {
        $examId = $request->query('exam_id');
        $exam = Exam::findOrFail($examId);

        $exam->tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
        $exam->vetter_comments = is_array($exam->vetter_comments) ? $exam->vetter_comments : json_decode($exam->vetter_comments, true) ?? [];

        // Optional history log
        $exam->vetter_comments_log = is_array($exam->vetter_comments_log)
            ? $exam->vetter_comments_log
            : json_decode($exam->vetter_comments_log, true) ?? [];

        return view('SEMS.approval-question', [
            'exam' => $exam,
            'vetterCommentsLog' => $exam->vetter_comments_log,
        ]);
    }


    public function viewQuestion(Request $request)
{
    $exam = Exam::findOrFail($request->exam_id);
    $questions = json_decode($exam->questions, true) ?? [];
    $tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
    $vetterComments = is_array($exam->vetter_comments) ? $exam->vetter_comments : json_decode($exam->vetter_comments, true) ?? [];

    return view('SEMS.view-question', compact('exam', 'questions', 'tos', 'vetterComments'));
}

}
