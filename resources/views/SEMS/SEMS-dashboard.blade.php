@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Smart Examination Management System (SEMS)</h2>

    @php
        use App\Models\CCAssignment;
        use App\Models\VetterAssignment;

        // Provide fallback if $exams is not passed
        $exams = $exams ?? \App\Models\Exam::where('semester_id', $activeSemester->id)->get();


        $userId = auth()->id();
        $isCC = CCAssignment::where('user_id', $userId)->exists();
        $isVetter = VetterAssignment::where('user_id', $userId)->exists();
    @endphp

    {{-- Super Admin View --}}
    @if ($role_id == 1)
        <div class="btn-group mb-4" role="group">
            <a href="{{ route('HOD.dashboard') }}" class="btn btn-outline-primary">HOD Dashboard</a>
            <a href="{{ route('CC.dashboard') }}" class="btn btn-outline-primary">CC Dashboard</a>
            <a href="{{ route('vetters.dashboard') }}" class="btn btn-outline-primary">Vetters Page</a>
        </div>
        <div>             @include('components.semester-switcher', [
            'semesters' => $semesters,
            'activeSemester' => $activeSemester
        ])
        </div>

        <p class="text-muted">
            You are logged in as Super Admin. Use the buttons above to switch views.
        </p>

    {{-- HOD View --}}
    @elseif ($role_id == 3)
    @include('SEMS.HOD-dashboard', [
        'exams' => $exams,
        'academicians' => \App\Models\User::where('role_id', 5)->get(),
        'semesters' => \App\Models\Semester::all(),
        'activeSemester' => \App\Models\Semester::where('is_active', true)->first()
    ])

    {{-- Academician View --}}
    @elseif ($role_id == 5)
        @if ($isCC)
            @include('SEMS.CC-dashboard')
        @elseif ($isVetter)
            @include('SEMS.vetters-dashboard')
        @else
            <p class="text-muted">You are not assigned to any paper yet.</p>
        @endif

    {{-- General Office View --}}
    @elseif ($role_id == 2)
        @include('SEMS.vetters-page')
    @endif
</div>
@endsection
