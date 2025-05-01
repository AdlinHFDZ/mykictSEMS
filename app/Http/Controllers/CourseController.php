<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return view('SEMS.courses-index', compact('courses')); // ✅ matches SEMS/courses-index.blade.php
    }

    public function create()
    {
        return view('SEMS.courses-create'); // ✅ matches SEMS/courses-create.blade.php
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_code' => 'required|string|max:255',
            'course_name' => 'required|string|max:255',
            'tos' => 'nullable|array',
            'tos.*.spec' => 'nullable|string|max:255',
        ]);

        Course::create([
            'course_code' => $validated['course_code'],
            'course_name' => $validated['course_name'],
            'tos' => json_encode($validated['tos'] ?? []),
        ]);

        return redirect()->route('courses.index')->with('success', 'Course added successfully.');
    }


    public function edit(Course $course)
{
    return view('SEMS.courses-edit', compact('course'));
}

public function update(Request $request, Course $course)
{
    $validated = $request->validate([
        'course_code' => 'required|string|max:255',
        'course_name' => 'required|string|max:255',
        'tos' => 'nullable|array',
    ]);

    $course->update([
        'course_code' => $validated['course_code'],
        'course_name' => $validated['course_name'],
        'tos' => json_encode($validated['tos']),
    ]);

    return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
}

public function destroy(Course $course)
{
    $course->delete();
    return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
}


}
