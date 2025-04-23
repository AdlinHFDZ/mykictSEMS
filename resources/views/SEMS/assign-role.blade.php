@extends('layouts.master')

@section('content')
<div class="container">
    <h3 class="mb-4">Assign Course Coordinator / Vetter</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('assign.role') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="exam_id">Select Exam Slot</label>
            <select name="exam_id" class="form-control" required>
                <option value="">-- Choose an exam --</option>
                @foreach ($exams as $exam)
                    <option value="{{ $exam->id }}">
                        {{ $exam->course_code }} - {{ $exam->course_name }} (Section {{ $exam->section }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="user_id">Select Academician</label>
            <select name="user_id" class="form-control" required>
                <option value="">-- Choose a lecturer --</option>
                @foreach ($academicians as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="role_type">Assign As:</label>
            <select name="role_type" class="form-control" required>
                <option value="">-- Choose a role --</option>
                <option value="cc">Course Coordinator</option>
                <option value="vetter">Vetter</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Assign</button>
    </form>
</div>
@endsection
