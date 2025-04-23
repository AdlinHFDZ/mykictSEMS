@extends('layouts.master')

@section('content')
<style>
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
    }
</style>

<div class="content container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

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

    <!-- Filter by status -->
    <form method="GET" action="{{ route('HOD.dashboard') }}" class="mb-3">
        <div class="row justify-content-end align-items-center">
            <div class="col-auto">
                <label for="statusFilter" class="form-label">Filter by Status:</label>
            </div>
            <div class="col-auto">
                <select name="status" id="statusFilter" class="form-select" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="assign Coordinator" {{ request('status') == 'assign Coordinator' ? 'selected' : '' }}>Assign Coordinator</option>
                    <option value="draft question" {{ request('status') == 'draft question' ? 'selected' : '' }}>Draft Question</option>
                    <option value="vetted" {{ request('status') == 'vetted' ? 'selected' : '' }}>Vetted</option>
                    <option value="pending approval" {{ request('status') == 'pending approval' ? 'selected' : '' }}>Pending Approval</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">
                    <div class="page-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="page-title">Exam Slots</h3>
                            </div>
                            <div class="col-auto text-end ms-auto">
                                <a href="#" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <a href="{{ route('exam.create') }}" class="btn btn-success me-2">
                                    <i class="fas fa-plus"></i> Create Exam
                                </a>
                                <a href="{{ route('assign.role.form') }}" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Assign CC
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Table -->
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
                                            ($exam->status === 'vetted' ? 'info' :
                                            ($exam->status === 'pending approval' ? 'secondary' :
                                            ($exam->status === 'approved' ? 'success' : 'dark')))) }}">
                                            {{ ucfirst($exam->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($exam->status === 'assign Coordinator')
                                            <!-- Assign CC Inline -->
                                            <form method="POST" action="{{ route('assign.role') }}" class="d-flex align-items-center justify-content-center mb-2">
                                                @csrf
                                                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                                <input type="hidden" name="role_type" value="cc">
                                                <select name="user_id" class="form-select form-select-sm me-2" required>
                                                    <option value="">Select CC</option>
                                                    @foreach ($academicians as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">Assign</button>
                                            </form>

                                        @elseif ($exam->status === 'draft question complete' && !empty($exam->questions))
                                            <!-- Assign Vetter Button -->
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#assignVetterModal{{ $exam->id }}">
                                                Assign Vetter
                                            </button>

                                            <!-- Vetter Modal -->
                                            <div class="modal fade" id="assignVetterModal{{ $exam->id }}" tabindex="-1" aria-labelledby="assignVetterModalLabel{{ $exam->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('assign.vetter') }}">
                                                            @csrf
                                                            <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="assignVetterModalLabel{{ $exam->id }}">Assign Vetter for {{ $exam->course_code }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <select name="user_id" class="form-select" required>
                                                                    <option value="">Select Vetter</option>
                                                                    @foreach ($academicians as $user)
                                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-warning">Assign</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Assigned</span>
                                        @endif
                                    </td>
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
