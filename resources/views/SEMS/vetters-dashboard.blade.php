@extends('layouts.master')

@section('content')
@php
    use App\Enums\ExamStatus;
@endphp

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
                <h3 class="page-title">Vetter Dashboard</h3>
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

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">
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
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $exam->course_code }}</td>
                                        <td>{{ $exam->course_name }}</td>
                                        <td>{{ $exam->section }}</td>
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
                                            <td>
                                                <span class="badge {{ $statusBadge }}">
                                                    {{ ucwords(str_replace('_', ' ', $exam->status)) }}
                                                </span>
                                            </td>
                                        <td>
                                            @if ($exam->status === ExamStatus::VETTING->value)
                                                <a href="{{ route('question.review', ['exam_id' => $exam->id]) }}" class="btn btn-sm btn-primary">
                                                    Review Questions
                                                </a>
                                            @elseif ($exam->status === ExamStatus::VETTED->value)
                                                <span class="text-muted">Reviewed ✅</span>
                                            @else
                                                <span class="text-muted">No Action</span>
                                            @endif
                                            <a href="{{ route('view.question', ['exam_id' => $exam->id]) }}" class="btn btn-sm btn-outline-info">
                                                👁 View
                                            </a>
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
