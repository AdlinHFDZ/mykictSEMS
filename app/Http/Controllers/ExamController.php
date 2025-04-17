<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use App\Models\CCAssignment;
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

    public function showAssignCoordinatorForm()
    {
        $exams = Exam::where('status', 'assign Coordinator')->get();
        $academicians = User::where('role_id', 5)->get();

        return view('SEMS.assign-cc', compact('exams', 'academicians'));
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

    public function hodDashboard()
    {
        if (auth()->user()->role_id == 1) {
            $exams = Exam::all(); // Super Admin sees all
        } else {
            $exams = Exam::where('created_by', auth()->id())->get(); // HOD sees their own
        }

        $academicians = User::where('role_id', 5)->get(); // Add this

        return view('SEMS.HOD-dashboard', compact('exams', 'academicians'));
    }

}
