@extends('layouts.master')

@section('content')
@php
    use App\Enums\ExamStatus;
@endphp

<style>
    .card-table {
        box-shadow: 0 4px 20px rgba(0,0,0,0.08), 0 1.5px 6px rgba(0,0,0,0.03);
        border-radius: 1.1rem;
    }
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
    }
    .table-hover tbody tr:hover {
        background-color: #f6faff !important;
        transition: background 0.17s;
    }
    .badge {
        font-size: 1em;
        padding: 0.45em 1.1em;
        border-radius: 1.4em;
        letter-spacing: 0.04em;
    }
    .btn-sm, .btn-outline-info, .btn-primary {
        border-radius: 1.25em !important;
        font-size: 1em;
        margin-bottom: 2px;
    }
    .page-header h3 {
        letter-spacing: 0.01em;
        font-weight: 700;
        margin-bottom: 0.3em;
    }
    .breadcrumb {
        margin-bottom: 0;
    }
    @media (max-width: 700px) {
        .page-header h3 { font-size: 1.12em; }
        .btn-sm { font-size: 0.97em; }
        .table-responsive { font-size: 0.96em; }
        .card-table { padding: 0.2rem; }
    }
</style>

<div class="content container-fluid">
    <div class="page-header mb-3">
        <div class="row align-items-center">
            <div class="col text-center">
                <h3 class="page-title mb-1">General Office Dashboard</h3>
                <ul class="breadcrumb justify-content-center" style="list-style: none; padding: 0; margin-top: 20px;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('SEMS.dashboard') }}">SEMS</a>
                    </li>
                    <li class="breadcrumb-item active">Approved Exam Papers</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="text-center mb-4">
        <span class="badge bg-info fs-6">
            {{ $activeSemester->name ?? 'N/A' }}
        </span>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card card-table">
                <div class="card-body py-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Section</th>
                                    <th>Semester</th>
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
                                        <td>{{ $exam->semester->name ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('pdf.view', $exam->id) }}" class="btn btn-sm btn-outline-info" target="_blank" title="View PDF">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <a href="{{ route('pdf.download', $exam->id) }}" class="btn btn-sm btn-primary" target="_blank" title="Download PDF">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-muted text-center">No approved exams available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted mt-3 text-end mb-0 small">Logged in as: {{ auth()->user()->name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
