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
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col text-center">
                <h1 class="page-title">HOD SEMS DASHBOARD, Welcome Dr Khairul!</h1>
                <ul class="breadcrumb justify-content-center" style="list-style: none; padding: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('SEMS.dashboard') }}">SEMS</a></li>
                    <li class="breadcrumb-item active">Manage Exams</li>
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
                    @foreach ([
                        ExamStatus::ASSIGN_COORDINATOR,
                        ExamStatus::DRAFT_QUESTION,
                        ExamStatus::DRAFT_QUESTION_COMPLETE,
                        ExamStatus::VETTING,
                        ExamStatus::VETTED,
                        ExamStatus::PENDING_APPROVAL,
                        ExamStatus::APPROVED
                    ] as $status)
                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                            {{ ucfirst($status->value) }}
                        </option>
                    @endforeach
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
                                <a href="{{ route('courses.index') }}" class="btn btn-outline-primary">📚 Course List</a>
                                <a href="{{ route('exam.create') }}" class="btn btn-success me-2">
                                    <i class="fas fa-plus"></i> Create Exam
                                </a>
                                <a href="{{ route('courses.create') }}" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-book"></i> Manage Courses
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Section</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($exams as $exam)
                                    @php
                                        $ccAssignedId = optional($exam->ccAssignment)->user_id;
                                        $vetterAssignedIds = $exam->vetterAssignments->pluck('user_id')->toArray();
                                        $badge = match($exam->status) {
                                            ExamStatus::ASSIGN_COORDINATOR->value      => 'bg-warning text-dark',
                                            ExamStatus::DRAFT_QUESTION->value          => 'bg-secondary',
                                            ExamStatus::DRAFT_QUESTION_COMPLETE->value => 'bg-info text-white',
                                            ExamStatus::VETTING->value                 => 'bg-secondary',
                                            ExamStatus::VETTED->value                  => 'bg-dark',
                                            ExamStatus::PENDING_APPROVAL->value        => 'bg-primary',
                                            ExamStatus::APPROVED->value                => 'bg-success',
                                            default                                     => 'bg-light text-muted'
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $exam->course_code }}</td>
                                        <td>{{ $exam->course_name }}</td>
                                        <td>{{ $exam->section }}</td>
                                        <td><span class="badge {{ $badge }}">{{ ucfirst($exam->status) }}</span></td>
                                        <td>
                                            @if ($ccAssignedId)
                                                <strong>CC:</strong> {{ optional($exam->ccAssignment->user)->name }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif

                                            @if (count($vetterAssignedIds))
                                                <br><strong>Vetter:</strong><br>
                                                {!! implode('<br>', $exam->vetterAssignments->pluck('user.name')->filter()->toArray()) !!}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-grid gap-1">
                                            @if ($exam->status === ExamStatus::ASSIGN_COORDINATOR->value)
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignCCModal{{ $exam->id }}">
                                                    Assign CC
                                                </button>

                                                <!-- Assign CC Modal -->
                                                <div class="modal fade" id="assignCCModal{{ $exam->id }}" tabindex="-1" aria-labelledby="assignCCModalLabel{{ $exam->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="POST" action="{{ route('assign.role') }}">
                                                                @csrf
                                                                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                                                <input type="hidden" name="role_type" value="cc">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="assignCCModalLabel{{ $exam->id }}">Assign Course Coordinator</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <select name="user_id" class="form-select" required>
                                                                        <option value="">Select CC</option>
                                                                        @foreach ($availableAcademicians as $user)
                                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @if ($availableAcademicians->isEmpty())
                                                                        <div class="text-danger mt-2">No available academicians.</div>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                                                    <button type="submit" class="btn btn-primary">Assign</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                            @elseif ($exam->status === ExamStatus::DRAFT_QUESTION_COMPLETE->value)
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#assignVetterModal{{ $exam->id }}">
                                                    Assign Vetter
                                                </button>

                                                <!-- Assign Vetter Modal -->
                                                <div class="modal fade" id="assignVetterModal{{ $exam->id }}" tabindex="-1" aria-labelledby="assignVetterModalLabel{{ $exam->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="POST" action="{{ route('assign.vetter') }}">
                                                                @csrf
                                                                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Assign Vetter for {{ $exam->course_code }}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <select name="user_id" class="form-select" required>
                                                                        <option value="">Select Vetter</option>
                                                                        @foreach ($availableAcademicians as $user)
                                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @if ($availableAcademicians->isEmpty())
                                                                        <div class="text-danger mt-2">No available academicians.</div>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                                                                    <button type="submit" class="btn btn-warning">Assign</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif ($exam->status === ExamStatus::PENDING_APPROVAL->value)
                                                <a href="{{ route('approval.question', ['exam_id' => $exam->id]) }}" class="btn btn-sm btn-info">View Question</a>

                                            @else
                                                <span class="text-muted">
                                                        <i class="fas fa-check-circle me-1 text-success"></i> Assigned
                                                </span>
                                            @endif

                                            <a href="{{ route('pdf.view', $exam->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">View PDF</a>
                                            <a href="{{ route('pdf.download', $exam->id) }}" class="btn btn-sm btn-outline-success">Download PDF</a>
                                            <a href="{{ route('view.question', ['exam_id' => $exam->id]) }}" class="btn btn-sm btn-outline-info">👁 View</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-muted text-center">No exams found.</td>
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
