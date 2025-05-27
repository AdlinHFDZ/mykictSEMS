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
            <div class="col-auto">
                <a href="{{ route('courses.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> Add New Course
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
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Table of Specification</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $course)
                            @php
                                $tos = is_array($course->tos) ? $course->tos : json_decode($course->tos, true) ?? [];
                                $modalId = 'tosModal-' . $course->id;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $course->course_code }}</td>
                                <td>{{ $course->course_name }}</td>
                                <td>
                                    @if (!empty($tos))
                                        <button class="btn btn-sm btn-outline-primary mb-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#{{ $modalId }}">
                                            <i class="fas fa-list-alt me-1"></i> View TOS
                                        </button>
                                    @else
                                        <span class="text-muted fst-italic">No TOS</span>
                                    @endif

                                    @if ($course->tos_pdf)
                                        <a href="{{ asset('storage/' . $course->tos_pdf) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-success mt-1">
                                            <i class="fas fa-file-pdf me-1"></i> View PDF
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('courses.destroy', $course->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this course?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Modal for TOS --}}
                            @if (!empty($tos))
                            <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="{{ $modalId }}Label">
                                                TOS: {{ $course->course_code }} - {{ $course->course_name }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
<ol class="ps-3">
    @foreach ($tos as $item)
        <li class="mb-2">
            <strong>PLO{{ $item['plo'] ?? '-' }} / CLO{{ $item['clo'] ?? '-' }}</strong><br>
            <strong>learning outcome: </strong>{{ $item['learning_outcome'] ?? '-' }}<br>
            <strong>Assessment: </strong>{{ $item['assessment'] ?? '-' }}
        </li>
    @endforeach
</ol>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

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
