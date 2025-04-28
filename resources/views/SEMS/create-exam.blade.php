@extends('layouts.master')

@section('content')
<div class="container">
    <h3 class="mb-4">Create New Exam Slot</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('exam.store') }}">
        @csrf
        <div class="form-group mb-3">
            <label for="course_id">Select Course</label>
            <select name="course_id" class="form-control" required>
                <option value="">-- Choose a Course --</option>
                @foreach($courses as $course)
                <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->course_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="section">Section</label>
            <input type="text" class="form-control" name="section" required>
        </div>

        <button type="submit" class="btn btn-success">Create Slot</button>
    </form>
</div>
@endsection
