<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semester;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::all();
        $activeSemester = Semester::where('is_active', true)->first();

        return view('SEMS.semesters.index', compact('semesters', 'activeSemester'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|string|max:20',
        ]);

        Semester::create([
            'name' => $request->name,
            'year' => $request->year,
            'is_active' => false,
        ]);

        return back()->with('success', 'Semester created successfully.');
    }

    public function activate(Semester $semester)
    {
        Semester::query()->update(['is_active' => false]);

        $semester->is_active = true;
        $semester->save();

        return back()->with('success', 'Semester activated successfully.');
    }
}
