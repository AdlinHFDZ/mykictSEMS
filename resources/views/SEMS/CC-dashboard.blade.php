@extends('layouts.master')

@section('content')
<style>
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
    }
</style>

<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col text-center">
                <h3 class="page-title">Welcome, Course Coordinator!</h3>
                <ul class="breadcrumb justify-content-center" style="list-style: none; padding: 0; margin-top: 20px;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('SEMS.dashboard') }}" style="color: #000000; text-decoration: none;">SEMS</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: #000000;">Your Assigned Exams</li>
                </ul>
                <p class="text-muted">Logged in as: {{ auth()->user()->name }} (ID: {{ auth()->id() }})</p>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                            <thead class="student-thread">
                                <tr>
                                    <th></th>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Section</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($exams as $exam)
                                    @php
                                        $assignedToCurrentUser = \App\Models\CCAssignment::where('exam_id', $exam->id)
                                            ->where('user_id', auth()->id())
                                            ->exists();
                                    @endphp

                                    @if ($assignedToCurrentUser || auth()->user()->role_id == 1)
                                        <tr>
                                            <td>
                                                <div class="form-check check-tables">
                                                    <input class="form-check-input" type="checkbox" value="{{ $exam->id }}">
                                                </div>
                                            </td>
                                            <td>{{ $exam->course_code }}</td>
                                            <td>{{ $exam->course_name }}</td>
                                            <td>{{ $exam->section }}</td>
                                            <td>{{ ucfirst($exam->status) }}</td>
                                            <td>
                                                @if ($exam->status === 'draft question')
                                                    <a href="{{ route('create.question', ['exam_id' => $exam->id]) }}" class="btn btn-sm bg-primary-light d-flex align-items-center justify-content-center">
                                                        <i class="fa fa-edit me-0"></i>
                                                        <span>Create Question</span>
                                                    </a>
                                                @else
                                                    <span class="text-muted">Submitted</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-muted">No exams assigned.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
