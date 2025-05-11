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
                <h3 class="page-title">General Office Dashboard</h3>
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
        <span class="badge bg-info fs-6"> {{ $activeSemester->name ?? 'N/A' }}</span>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Section</th>
                                    <th>Created By</th>
                                    <th>Semester</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($exams as $index => $exam)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $exam->course_code }}</td>
                                        <td>{{ $exam->course_name }}</td>
                                        <td>{{ $exam->section }}</td>
                                        <td>{{ $exam->createdBy->name ?? '-' }}</td>
                                        <td>{{ $exam->semester->name ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('pdf.view', $exam->id) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                                👁 View
                                            </a>
                                            <a href="{{ route('pdf.download', $exam->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                                ⬇️ Download
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-muted text-center">No approved exams available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted mt-3 text-end">Logged in as: {{ auth()->user()->name }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
