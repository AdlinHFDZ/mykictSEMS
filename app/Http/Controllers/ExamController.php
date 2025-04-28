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
            'status'       => 'assign Coordinator',
            'created_by'   => auth()->id(),
            'semester_id'  => $semester?->id,
        ]);

        return redirect()->route('HOD.dashboard')->with('success', 'Exam slot created!');
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
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'user_id' => 'required|exists:users,id',
        ]);

        CCAssignment::create([
            'exam_id' => $request->exam_id,
            'user_id' => $request->user_id,
        ]);

        Exam::find($request->exam_id)->update(['status' => 'draft question']);

        return back()->with('success', 'Course Coordinator assigned successfully!');
    }

    public function assignVetter(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $exam = Exam::find($request->exam_id);

        if ($exam->status !== 'draft question complete' || empty($exam->questions)) {
            return back()->with('error', 'Cannot assign vetter. Exam must be in draft complete status with questions.');
        }

        $alreadyAssigned = VetterAssignment::where('exam_id', $exam->id)->exists();
        if ($alreadyAssigned) {
            return back()->with('error', 'This exam already has a vetter assigned.');
        }

        VetterAssignment::create([
            'exam_id' => $request->exam_id,
            'user_id' => $request->user_id,
        ]);

        $exam->update(['status' => 'vetting']);

        return back()->with('success', 'Vetter assigned successfully!');
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'exam_id'   => 'required|exists:exams,id',
            'user_id'   => 'required|exists:users,id',
            'role_type' => 'required|in:cc,vetter',
        ]);

        $exam = Exam::find($request->exam_id);

        if ($request->role_type === 'cc') {
            $alreadyAssigned = CCAssignment::where('exam_id', $exam->id)->exists();
            if ($alreadyAssigned) {
                return back()->with('error', 'This exam is already assigned to a Course Coordinator.');
            }

            CCAssignment::create([
                'exam_id' => $exam->id,
                'user_id' => $request->user_id,
            ]);

            $exam->update(['status' => 'draft question']);

            return back()->with('success', 'Course Coordinator assigned successfully!');
        }

        if ($request->role_type === 'vetter') {
            if ($exam->status !== 'draft question complete' || empty($exam->questions)) {
                return back()->with('error', 'Vetter can only be assigned after CC submits the completed draft question.');
            }

            VetterAssignment::create([
                'exam_id' => $exam->id,
                'user_id' => $request->user_id,
            ]);

            $exam->update(['status' => 'vetting']);

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
            'tos' => 'nullable|array',
            'question1' => 'nullable|string',
            'answer1' => 'nullable|string',
            'question2' => 'nullable|string',
            'answer2' => 'nullable|string',
            'question3' => 'nullable|string',
            'answer3' => 'nullable|string',
            'question4' => 'nullable|string',
            'answer4' => 'nullable|string',
        ]);

        $exam = Exam::find($request->exam_id);

        $exam->tos = $request->tos ?? [];
        $exam->questions = json_encode([
            ['question' => $request->question1, 'answer' => $request->answer1],
            ['question' => $request->question2, 'answer' => $request->answer2],
            ['question' => $request->question3, 'answer' => $request->answer3],
            ['question' => $request->question4, 'answer' => $request->answer4],
        ]);

        // ✅ Detect if this is resubmission after vetting
        if ($exam->status === 'vetted') {
            $exam->status = 'pending approval'; // Skip vetter and go directly to HOD
        } else {
            $exam->status = 'draft question complete'; // First time submission goes to vetter
        }

        $exam->save();

        return redirect()->route('CC.dashboard')->with('success', 'Question submitted successfully.');
    }



    public function showCreateQuestionForm(Request $request)
    {
        $exam = Exam::findOrFail($request->exam_id);

        $vetterComments = $exam->vetter_comments ?? []; // Fetch vetter comments

        return view('SEMS.create-question', compact('exam', 'vetterComments'));
    }


    /*
    |--------------------------------------------------------------------------
    | Dashboards (HOD, CC, Vetter, SEMS)
    |--------------------------------------------------------------------------
    */

    public function hodDashboard(Request $request)
    {
        $activeSemester = Semester::where('is_active', true)->first();
        $semesters = Semester::all();
        $status = $request->input('status');

        $query = Exam::where('semester_id', $activeSemester->id);

        if ($status) {
            $query->where('status', $status);
        }

        $exams = auth()->user()->role_id == 1
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
        $user = auth()->user();
        $activeSemesterId = Semester::where('is_active', true)->value('id');

        $exams = $user->role_id == 1
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
        $user = auth()->user();
        $activeSemesterId = Semester::where('is_active', true)->value('id');

        $exams = $user->role_id == 1
            ? Exam::where('semester_id', $activeSemesterId)->get()
            : Exam::whereIn('id', VetterAssignment::where('user_id', $user->id)->pluck('exam_id'))->get();

        $semesters = Semester::all();
        $activeSemester = Semester::where('is_active', true)->first();

        return view('SEMS.vetters-dashboard', compact('exams', 'semesters', 'activeSemester'));
    }

    public function showSEMSDashboard()
    {
        $user = auth()->user();
        $semesters = Semester::all();
        $activeSemester = Semester::where('is_active', true)->first();
        $exams = Exam::all(); // Or you can filter by role if needed

        return view('SEMS.SEMS-dashboard', compact('role_id', 'exams', 'semesters', 'activeSemester'));
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
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'comments' => 'nullable|array',
            'tos' => 'nullable|array',
        ]);

        $exam = Exam::findOrFail($request->exam_id);

        // ✅ Update only Vetter TOS, keep CC, HOD, Spec
        $originalTos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
        $submittedTos = $request->tos ?? [];

        foreach ($originalTos as $index => &$row) {
            if (isset($submittedTos[$index]['vetter'])) {
                $row['vetter'] = 1;
            } else {
                $row['vetter'] = 0;
            }
        }
        $exam->tos = $originalTos;

        // Update Comments
        $exam->vetter_comments = $request->comments ?? [];
        $exam->status = 'vetted'; // After Vetter
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

    function getActiveSemesterId()
    {
        return Semester::where('is_active', true)->value('id');
    }


    public function approveExam(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'tos' => 'nullable|array',
        ]);

        $exam = Exam::findOrFail($request->exam_id);

        // ✅ Update only HOD TOS, keep CC, Vetter, Spec
        $originalTos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
        $submittedTos = $request->tos ?? [];

        foreach ($originalTos as $index => &$row) {
            if (isset($submittedTos[$index]['hod'])) {
                $row['hod'] = 1;
            } else {
                $row['hod'] = 0;
            }
        }
        $exam->tos = $originalTos;

        $exam->status = 'approved'; // After HOD Approval
        $exam->save();

        return redirect()->route('HOD.dashboard')->with('success', 'Exam approved successfully!');
    }



    public function denyQuestion(Request $request)
    {
        $exam = Exam::findOrFail($request->exam_id);
        $exam->status = 'draft question';
        $exam->save();

        return back()->with('error', 'Exam denied. Sent back to CC.');
    }

    public function showApprovalQuestion(Request $request)
    {
        $examId = $request->query('exam_id');
        $exam = Exam::findOrFail($examId);

        // Decode TOS properly before passing to Blade
        $exam->tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];

        return view('SEMS.approval-question', compact('exam'));
    }

}
