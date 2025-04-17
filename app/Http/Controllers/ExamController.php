<?php

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
            'coordinator_name' => 'nullable|string|max:255',
            // Add other validations...
            'tos' => 'nullable|array',
        ]);

        $validated['tos'] = $request->input('tos', []);

        Exam::create($validated);

        return redirect()->back()->with('success', 'Exam created successfully!');
    }
}
