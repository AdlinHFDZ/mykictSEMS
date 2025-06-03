@extends('layouts.master')

@section('content')
@php
    use App\Enums\ExamStatus;
@endphp

<style>
    .card-table {
        box-shadow: 0 4px 18px rgba(0,0,0,0.07), 0 1.5px 7px rgba(0,0,0,0.03);
        border-radius: 1rem;
    }
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
    }
    .table-hover tbody tr:hover {
        background-color: #f7fafc !important; /* soft blue/gray */
        transition: background 0.15s;
    }
    .badge {
        font-size: 0.97em;
        padding: 0.38em 1.1em;
        border-radius: 1.3em;
        letter-spacing: 0.025em;
    }
    .btn-sm, .btn-outline-primary, .btn-outline-info, .btn-outline-warning, .btn-primary, .btn-outline-success, .btn-outline-secondary {
        border-radius: 1.3em !important;
        font-size: 0.98em;
        margin-bottom: 3px;
    }
    .btn-group .btn { margin-right: 4px; }
    .btn-group .btn:last-child { margin-right: 0; }
    .d-flex.gap-1 > * { margin-right: 6px !important; }
    .d-flex.gap-1 > *:last-child { margin-right: 0 !important; }
    .page-header h3 {
        letter-spacing: 0.01em;
        font-weight: 700;
        margin-bottom: 0.2em;
    }
    .breadcrumb {
        margin-bottom: 0;
    }
    @media (max-width: 700px) {
        .page-header h3 { font-size: 1.12em; }
        .btn-sm { font-size: 0.93em; }
        .table-responsive { font-size: 0.94em; }
        .card-table { padding: 0.2rem; }
    }
</style>

<div class="content container-fluid">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col text-center">
                <h3 class="page-title mb-1">Vetter Dashboard</h3>
                <ul class="breadcrumb justify-content-center" style="list-style: none; padding: 0; margin-top: 20px;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('SEMS.dashboard') }}">SEMS</a>
                    </li>
                    <li class="breadcrumb-item active">Review Assigned Exams</li>
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
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
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
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $exam->course_code }}</td>
                                        <td>{{ $exam->course_name }}</td>
                                        <td>{{ $exam->section }}</td>
                                        <td>
                                            <span class="badge {{ $statusBadge }}">
                                                {{ ucwords(str_replace('_', ' ', $exam->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap">
                                                {{-- Vetter Actions --}}
                                                @if ($exam->status === ExamStatus::VETTING->value)
                                                    <a href="{{ route('question.review', ['exam_id' => $exam->id]) }}"
                                                       class="btn btn-sm btn-outline-primary d-flex align-items-center"
                                                       title="Review Questions">
                                                        <i class="fa fa-edit me-1"></i> Review
                                                    </a>
                                                @elseif ($exam->status === ExamStatus::VETTED->value)
                                                    <button class="btn btn-sm btn-outline-success" style="pointer-events: none; opacity: 1;" disabled title="Reviewed">
                                                        <i class="bi bi-check-circle me-1"></i> Reviewed
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline-secondary" style="pointer-events: none; opacity: 1;" disabled title="No Action">
                                                        <i class="bi bi-dash-circle me-1"></i> No Action
                                                    </button>
                                                @endif

                                                {{-- Other actions grouped --}}
                                                <div class="btn-group" role="group" aria-label="Exam Actions">
                                                    <a href="{{ route('view.question', ['exam_id' => $exam->id]) }}"
                                                       class="btn btn-sm btn-outline-info"
                                                       title="View Question">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('pdf.view', $exam->id) }}"
                                                       class="btn btn-sm btn-outline-primary"
                                                       target="_blank"
                                                       title="View Exam PDF">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                    </a>
                                                    <a href="{{ route('pdf.download', $exam->id) }}"
                                                       class="btn btn-sm btn-primary"
                                                       target="_blank"
                                                       title="Download Exam PDF">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <a href="{{ route('exam.view-answer-scheme', $exam->id) }}"
                                                       class="btn btn-sm btn-outline-warning"
                                                       target="_blank"
                                                       title="View Answer Scheme">
                                                        <i class="bi bi-eye"></i> Scheme
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-muted text-center">No assigned exams yet.</td>
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
