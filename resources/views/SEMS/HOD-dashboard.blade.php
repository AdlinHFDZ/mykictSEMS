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
                <h1 class="page-title">HOD SEMS DASHBOARD, Welcome Dr Khairul!</h1>
                <ul class="breadcrumb justify-content-center" style="list-style: none; padding: 0; margin-top: 20px;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('SEMS.dashboard') }}" style="color: #000000; text-decoration: none;">SEMS</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: #000000;">Manage Exams</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Exam List Card -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    <!-- Page Header Actions -->
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="page-title">Exam Slots</h3>
                            </div>
                            <div class="col-auto text-end ms-auto">
                                <a href="#" class="btn btn-outline-primary me-2" title="Download all exams">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <a href="{{ route('exam.create') }}" class="btn btn-success me-2" title="Create a new exam slot">
                                    <i class="fas fa-plus"></i> Create Exam
                                </a>
                                <a href="{{ route('assign.cc.form') }}" class="btn btn-primary" title="Manual CC assignment (bulk)">
                                    <i class="fas fa-user-plus"></i> Assign CC
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Exam Table -->
                    <div class="table-responsive">
                        <table class="table border-0 table-hover table-center mb-0 datatable table-striped">
                            <thead class="student-thread">
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
                                    <td>
                                        <span class="badge bg-{{
                                            $exam->status === 'assign Coordinator' ? 'warning' :
                                            ($exam->status === 'draft question' ? 'primary' :
                                            ($exam->status === 'approved' ? 'success' : 'secondary')) }}">
                                            {{ ucfirst($exam->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('assign.cc') }}" class="d-flex align-items-center justify-content-center">
                                            @csrf
                                            <input type="hidden" name="exam_id" value="{{ $exam->id }}">

                                            @if ($exam->status === 'assign Coordinator')
                                                <select name="user_id" class="form-select form-select-sm me-2" required>
                                                    <option value="">Select CC</option>
                                                    @foreach ($academicians as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>

                                                <button type="submit" class="btn btn-sm btn-primary">Assign</button>
                                            @else
                                                <span class="text-muted">CC Assigned</span>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-muted">No exams found.</td>
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
