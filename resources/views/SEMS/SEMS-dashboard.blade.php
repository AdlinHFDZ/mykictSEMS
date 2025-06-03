@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Smart Examination Management System (SEMS)</h2>

    @php
        // These should ideally be passed from controller!
        use App\Models\Semester;
        use App\Models\CCAssignment;
        use App\Models\VetterAssignment;

        $semesters = \App\Models\Semester::all();
        $activeSemester = Semester::where('is_active', true)->first();

        $userId = auth()->id();
        $roleId = auth()->user()->role_id;

        $isCC = CCAssignment::where('user_id', $userId)->exists();
        $isVetter = VetterAssignment::where('user_id', $userId)->exists();
    @endphp

    {{-- Super Admin View --}}
    @if ($roleId == 1)
        <div class="btn-group mb-4" role="group">
            <a href="{{ route('HOD.dashboard') }}" class="btn btn-outline-primary">HOD Dashboard</a>
            <a href="{{ route('CC.dashboard') }}" class="btn btn-outline-primary">CC Dashboard</a>
            <a href="{{ route('vetters.dashboard') }}" class="btn btn-outline-primary">Vetters Page</a>
            <a href="{{ route('generalOffice.dashboard') }}" class="btn btn-outline-primary">GO dashboard</a>
        </div>
        <div>
            @include('components.semester-switcher', [
                'semesters' => $semesters,
                'activeSemester' => $activeSemester
            ])
        </div>
        <p class="text-muted">
            You are logged in as Super Admin. Use the buttons above to switch views.
        </p>

    {{-- HOD View --}}
    @elseif ($roleId == 3)
        <div class="alert alert-info">
            Redirecting to HOD Dashboard...
        </div>
        <script>
            window.location.href = "{{ route('HOD.dashboard') }}";
        </script>

    {{-- Course Coordinator / Vetter --}}
    @elseif ($roleId == 5)
        @if ($isCC)
            <div class="alert alert-info">
                Redirecting to CC Dashboard...
            </div>
            <script>
                window.location.href = "{{ route('CC.dashboard') }}";
            </script>
        @elseif ($isVetter)
            <div class="alert alert-info">
                Redirecting to Vetters Dashboard...
            </div>
            <script>
                window.location.href = "{{ route('vetters.dashboard') }}";
            </script>
        @else
            <p class="text-muted">You are not assigned to any paper yet.</p>
        @endif

    {{-- Vetters --}}
    @elseif ($roleId == 4)
        <div class="alert alert-info">
            Redirecting to Vetters Dashboard...
        </div>
        <script>
            window.location.href = "{{ route('vetters.dashboard') }}";
        </script>

    {{-- General Office --}}
    @elseif ($roleId == 2)
        <div class="alert alert-info">
            Redirecting to General Office Dashboard...
        </div>
        <script>
            window.location.href = "{{ route('generalOffice.dashboard') }}";
        </script>
    @endif

</div>
@endsection
