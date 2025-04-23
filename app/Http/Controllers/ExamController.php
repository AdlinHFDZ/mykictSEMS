<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use App\Models\CCAssignment;
use App\Models\VetterAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:255',
            'section' => 'required|string|max:255',
        ]);

        $validated['status'] = 'assign Coordinator';
        $validated['created_by'] = Auth::id();

        Exam::create($validated);

        return redirect()->route('HOD.dashboard')->with('success', 'Exam slot created successfully!');
    }

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

        Exam::where('id', $request->exam_id)->update(['status' => 'draft question']);

        return redirect()->back()->with('success', 'Course Coordinator assigned successfully!');
    }

    public function assignVetter(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $exam = Exam::find($request->exam_id);

        // ✅ Check condition: only allow if draft & question is filled
        if ($exam->status !== 'draft question' || empty($exam->questions)) {
            return redirect()->back()->with('error', 'Cannot assign vetter. The exam must be in draft status with questions submitted.');
        }

        VetterAssignment::create([
            'exam_id' => $request->exam_id,
            'user_id' => $request->user_id,
        ]);

        $exam->update(['status' => 'vetting']);

        return redirect()->back()->with('success', 'Vetter assigned successfully!');
    }


    public function hodDashboard(Request $request)
    {
        $status = $request->input('status');
        $query = Exam::query();

        if ($status) {
            $query->where('status', $status);
        }

        $exams = auth()->user()->role_id == 1
            ? $query->get()
            : $query->where('created_by', auth()->id())->get();

        $academicians = User::where('role_id', 5)->get();

        return view('SEMS.HOD-dashboard', compact('exams', 'academicians'));
    }


    public function assignRole(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'user_id' => 'required|exists:users,id',
            'role_type' => 'required|in:cc,vetter',
        ]);

        $exam = Exam::find($request->exam_id);

        if ($request->role_type === 'cc') {
            // Prevent duplicate CC assignment
            $alreadyAssigned = \App\Models\CCAssignment::where('exam_id', $exam->id)->exists();

            if ($alreadyAssigned) {
                return redirect()->back()->with('error', 'This exam is already assigned to a Course Coordinator.');
            }

            \App\Models\CCAssignment::create([
                'exam_id' => $exam->id,
                'user_id' => $request->user_id,
            ]);

            $exam->update(['status' => 'draft question']);

            return redirect()->back()->with('success', 'Course Coordinator assigned successfully!');
        }

        if ($request->role_type === 'vetter') {
            if ($exam->status !== 'draft question complete' || empty($exam->questions)) {
                return redirect()->back()->with('error', 'Vetter can only be assigned after CC submits the completed draft question.');
            }

            \App\Models\VetterAssignment::create([
                'exam_id' => $exam->id,
                'user_id' => $request->user_id,
            ]);

            $exam->update(['status' => 'vetting']);

            return redirect()->back()->with('success', 'Vetter assigned successfully!');
        }

        return redirect()->back()->with('error', 'Invalid role type.');
    }


    public function ccDashboard()
    {
        $user = auth()->user();

        if ($user->role_id == 1) {
            // ✅ Super Admin sees all exams
            $exams = Exam::all();
        } else {
            // ✅ CC sees only their assigned exams
            $assignedExamIds = CCAssignment::where('user_id', $user->id)->pluck('exam_id')->toArray();
            $exams = Exam::whereIn('id', $assignedExamIds)->get();
        }

        return view('SEMS.CC-dashboard', compact('exams'));
    }


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

    // Save TOS and questions as JSON
    $exam->tos = $request->tos ?? [];
    $exam->questions = json_encode([
        ['question' => $request->question1, 'answer' => $request->answer1],
        ['question' => $request->question2, 'answer' => $request->answer2],
        ['question' => $request->question3, 'answer' => $request->answer3],
        ['question' => $request->question4, 'answer' => $request->answer4],
    ]);

    $exam->status = 'draft question complete';
    $exam->save();

    return redirect()->route('CC.dashboard')->with('success', 'Question submitted successfully.');
}

public function showCreateQuestionForm(Request $request)
{
    $exam = Exam::findOrFail($request->exam_id);
    return view('SEMS.create-question', compact('exam'));
}


}
