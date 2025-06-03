@extends('layouts.master')

@section('content')
@php
    use App\Enums\ExamStatus;
@endphp

<style>
    .card-table {
        box-shadow: 0 4px 24px rgba(0,0,0,0.07), 0 1.5px 6px rgba(0,0,0,0.03);
        border-radius: 1rem;
    }
    .table th, .table td {
        vertical-align: middle !important;
        text-align: center;
    }
    .table-hover tbody tr:hover {
        background-color: #f0f4f8 !important;
        transition: background 0.2s;
    }
    .badge {
        font-size: 0.98em;
        padding: 0.4em 1em;
        border-radius: 1.2em;
        letter-spacing: 0.03em;
    }
    .btn-action {
        min-width: 36px;
        margin-right: 6px;
        margin-bottom: 3px;
        transition: all 0.13s;
    }
    .btn-action:last-child {
        margin-right: 0;
    }
    .btn-outline-primary, .btn-outline-info, .btn-outline-warning, .btn-outline-secondary {
        border-radius: 1.3em !important;
    }
    .datatable th, .datatable td {
        font-size: 1.04em;
    }
    @media (max-width: 700px) {
        .table-responsive { font-size: 0.92em; }
        .card-table { padding: 0.2rem; }
        .btn-action { margin-right: 2px; }
    }
</style>

<div class="content container-fluid">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col text-center">
                <h3 class="page-title fw-bold mb-0" style="letter-spacing: 0.03em;">Welcome, Course Coordinator!</h3>
                <ul class="breadcrumb justify-content-center mt-2" style="background: none; list-style: none; padding: 0;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('SEMS.dashboard') }}" style="color: #007bff; text-decoration: none;">SEMS</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: #333;">Your Assigned Exams</li>
                </ul>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card card-table">
                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                            <thead class="student-thread align-middle" style="background:#f7fafc;">
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

                                        $statusBadge = match($exam->status) {
                                            ExamStatus::ASSIGN_COORDINATOR->value      => 'bg-warning text-dark',
                                            ExamStatus::DRAFT_QUESTION->value          => 'bg-secondary text-white',
                                            ExamStatus::DRAFT_QUESTION_COMPLETE->value => 'bg-info text-white',
                                            ExamStatus::VETTING->value                 => 'bg-secondary text-white',
                                            ExamStatus::VETTED->value                  => 'bg-dark text-white',
                                            ExamStatus::REVISE_REQUESTED->value        => 'bg-danger text-white',
                                            ExamStatus::PENDING_APPROVAL->value        => 'bg-primary text-white',
                                            ExamStatus::APPROVED->value                => 'bg-success text-white',
                                            default                                     => 'bg-light text-muted'
                                        };

                                        $editableStatuses = [
                                            ExamStatus::DRAFT_QUESTION->value,
                                            ExamStatus::REVISE_REQUESTED->value,
                                            ExamStatus::VETTED->value
                                        ];
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
                                            <td>
                                                <span class="badge {{ $statusBadge }}">
                                                    {{ ucwords(str_replace('_', ' ', $exam->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap justify-content-center align-items-center gap-1">
                                                    {{-- Create/Edit Question or Show Submitted --}}
                                                    @if (in_array($exam->status, $editableStatuses))
                                                        <a href="{{ route('create.question', ['exam_id' => $exam->id]) }}"
                                                           class="btn btn-sm btn-outline-primary btn-action d-flex align-items-center"
                                                           title="Create or Edit Question">
                                                           <i class="fa fa-edit me-1"></i>
                                                           {{
                                                               $exam->status === ExamStatus::VETTED->value ? 'Edit Vetted' :
                                                               ($exam->status === ExamStatus::REVISE_REQUESTED->value ? 'Revise Denied' : 'Create Question')
                                                           }}
                                                        </a>
                                                    @else
                                                        <button class="btn btn-sm btn-outline-secondary btn-action d-flex align-items-center" style="pointer-events: none; opacity: 1;" disabled title="Already Submitted">
                                                            Submitted
                                                        </button>
                                                    @endif

                                                    {{-- View Question --}}
                                                    <a href="{{ route('view.question', ['exam_id' => $exam->id]) }}"
                                                       class="btn btn-sm btn-outline-info btn-action d-flex align-items-center"
                                                       title="View Question">
                                                       <i class="bi bi-eye"></i>
                                                    </a>

                                                    {{-- View PDF --}}
                                                    <a href="{{ route('pdf.view', $exam->id) }}"
                                                       class="btn btn-sm btn-outline-primary btn-action d-flex align-items-center"
                                                       target="_blank"
                                                       title="View Exam PDF">
                                                       <i class="bi bi-file-earmark-text"></i>
                                                    </a>
                                                    <a href="{{ route('pdf.download', $exam->id) }}"
                                                       class="btn btn-sm btn-outline-primary btn-action d-flex align-items-center"
                                                       target="_blank"
                                                       title="Download Exam PDF">
                                                       <i class="bi bi-download"></i>
                                                    </a>
                                                    {{-- View Answer Scheme --}}
                                                    <a href="{{ route('exam.view-answer-scheme', $exam->id) }}"
                                                       class="btn btn-sm btn-outline-warning btn-action d-flex align-items-center"
                                                       target="_blank"
                                                       title="View Answer Scheme">
                                                       <i class="bi bi-eye"></i> <span class="ms-1">Scheme</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-muted text-center">
                                            No exams assigned yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <p class="text-muted mt-3 small">Logged in as: {{ auth()->user()->name }} (ID: {{ auth()->id() }})</p>
        </div>
    </div>
</div>
@endsection
