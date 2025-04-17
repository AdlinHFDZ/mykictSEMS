@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Smart Examination Management System (SEMS)</h2>

    @php
        // Provide fallback for exams if not passed
        $exams = $exams ?? \App\Models\Exam::all();
    @endphp

    @if ($role_id == 1)
        {{-- Super Admin View Switcher --}}
        <div class="btn-group mb-4" role="group">
            <a href="{{ route('HOD.dashboard') }}" class="btn btn-outline-primary">HOD Dashboard</a>
            <a href="{{ route('CC.dashboard') }}" class="btn btn-outline-primary">CC Dashboard</a>
            <a href="{{ route('vetters.dashboard') }}" class="btn btn-outline-primary">Vetters Page</a>
        </div>
        <p class="text-muted">You are logged in as Super Admin. Use the buttons above to switch views.</p>
    @elseif ($role_id == 3)
        {{-- HOD View --}}
        @include('SEMS.HOD-dashboard', ['exams' => $exams])
    @elseif ($role_id == 5)
        {{-- Academician View (CC or Vetter) --}}
        @include('SEMS.CC-dashboard')
    @elseif ($role_id == 2)
        {{-- General Office or Vetters --}}
        @include('SEMS.vetters-page')
    @endif
</div>
@endsection
