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
        'tos.*.plo' => 'nullable|numeric',
        'tos.*.clo' => 'nullable|numeric',
        'tos.*.learning_outcome' => 'nullable|string',
        'tos.*.assessment' => 'nullable|string',
        'tos_pdf' => 'nullable|mimes:pdf|max:10240',
    ]);

    $tosPdfPath = null;
    if ($request->hasFile('tos_pdf')) {
        $file = $request->file('tos_pdf');
        $filename = time() . '_' . $file->getClientOriginalName();
        $tosPdfPath = $file->storeAs('tos_pdfs', $filename, 'public');
    }

    Course::create([
        'course_code' => $validated['course_code'],
        'course_name' => $validated['course_name'],
        'tos' => json_encode($validated['tos'] ?? []),
        'tos_pdf' => $tosPdfPath,
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
        'tos.*.plo' => 'nullable|numeric',
'tos.*.clo' => 'nullable|numeric',
'tos.*.learning_outcome' => 'nullable|string',
'tos.*.assessment' => 'nullable|string',
        'tos_pdf' => 'nullable|mimes:pdf|max:10240',
    ]);

    $data = [
        'course_code' => $validated['course_code'],
        'course_name' => $validated['course_name'],
        'tos' => json_encode($validated['tos'] ?? []),
    ];

    if ($request->hasFile('tos_pdf')) {
        $file = $request->file('tos_pdf');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('tos_pdfs', $filename, 'public');
        $data['tos_pdf'] = $path;
    }

    // Remove existing PDF if requested
if ($request->has('remove_pdf') && $course->tos_pdf) {
    \Storage::disk('public')->delete($course->tos_pdf);
    $data['tos_pdf'] = null;
}

    $course->update($data);

    return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
}

public function destroy(Course $course)
{
    // Delete the PDF file if it exists
    if ($course->tos_pdf) {
        \Storage::disk('public')->delete($course->tos_pdf);
    }

    // Delete the course record
    $course->delete();

    return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
}


}
