@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">All Courses</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('SEMS.dashboard') }}">SEMS</a></li>
                    <li class="breadcrumb-item active">Course List</li>
                </ul>
            </div>
            <div class="col-auto text-end">
                <a href="{{ route('courses.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Course
                </a>
            </div>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Courses Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>TOS</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $course)
                            @php
                                $tos = is_array($course->tos) ? $course->tos : json_decode($course->tos, true) ?? [];
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $course->course_code }}</td>
                                <td>{{ $course->course_name }}</td>
                                <td>
                                    @if (!empty($tos))
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                View TOS
                                            </button>
                                            <ul class="dropdown-menu">
                                                @foreach ($tos as $item)
                                                    <li class="dropdown-item">{{ $item['spec'] ?? '-' }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <span class="text-muted">No TOS</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No courses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
