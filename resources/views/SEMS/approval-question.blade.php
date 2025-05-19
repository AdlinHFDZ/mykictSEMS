@extends('layouts.master')

@section('content')
@php
    use App\Enums\ExamStatus;
@endphp

<div class="content container-fluid">
    <div class="page-header">
        <h3 class="text-center">Review Exam (Final Approval)</h3>
        <ul class="breadcrumb justify-content-center">
            <li class="breadcrumb-item"><a href="{{ route('SEMS.dashboard') }}">SEMS</a></li>
            <li class="breadcrumb-item active">Approval Review</li>
        </ul>
    </div>

    <form method="POST" action="{{ route('exam.approve') }}">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">

        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">

                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-2">
                                {{ $exam->course_name }}
                                <small class="text-muted">({{ $exam->course_code }})</small>
                            </h4>
                            <p class="mb-1"><strong>Section:</strong> {{ $exam->section }}</p>
                            <p class="mb-1"><strong>Status:</strong>
                                <span class="badge bg-{{ $exam->status == 'vetting' ? 'warning text-dark' : 'secondary' }}">
                                    {{ ucfirst($exam->status) }}
                                </span>
                            </p>
                            <p class="mb-0"><strong>Semester:</strong> {{ $exam->semester->name ?? 'N/A' }}</p>
                        </div>

                        <hr class="mb-4">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-journal-text me-1"></i> Course Name</label>
                                <input type="text" class="form-control bg-light" value="{{ $exam->course_name }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-code-slash me-1"></i> Course Code</label>
                                <input type="text" class="form-control bg-light" value="{{ $exam->course_code }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-layers me-1"></i> Section</label>
                                <input type="text" class="form-control bg-light" value="{{ $exam->section }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-person-badge me-1"></i> Coordinator</label>
                                <input type="text" class="form-control bg-light" value="{{ $exam->createdBy->name ?? 'N/A' }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-calendar-event me-1"></i> Semester</label>
                                <input type="text" class="form-control bg-light" value="{{ $exam->semester->name ?? 'N/A' }}" disabled>
                            </div>

                            @if ($exam->exam_date)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold"><i class="bi bi-calendar-check me-1"></i> Exam Date</label>
                                    <input type="text" class="form-control bg-light" value="{{ \Carbon\Carbon::parse($exam->exam_date)->format('d/m/Y') }}" disabled>
                                </div>
                            @endif
                            @if ($exam->exam_time)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold"><i class="bi bi-clock me-1"></i> Exam Time</label>
                                    <input type="text" class="form-control bg-light" value="{{ $exam->exam_time }}" disabled>
                                </div>
                            @endif
                            @if ($exam->duration)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold"><i class="bi bi-hourglass me-1"></i> Duration</label>
                                    <input type="text" class="form-control bg-light" value="{{ $exam->duration }}" disabled>
                                </div>
                            @endif
                            @if ($exam->instruction)
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-semibold"><i class="bi bi-info-circle me-1"></i> Instructions</label>
                                    <textarea class="form-control bg-light" rows="3" disabled>{{ $exam->instruction }}</textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $questions = json_decode($exam->questions, true) ?? [];
            $vetterComments = is_array($exam->vetter_comments) ? $exam->vetter_comments : json_decode($exam->vetter_comments, true) ?? [];
            $tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
        @endphp

        <!-- TOS Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Table of Specification (TOS)</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>TOS Specification</th>
                            <th>CC</th>
                            <th>Vetter</th>
                            <th>HOD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tos as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><input type="text" class="form-control" value="{{ $row['spec'] ?? '' }}" readonly></td>
                                <td><input type="checkbox" disabled {{ !empty($row['cc']) ? 'checked' : '' }}></td>
                                <td><input type="checkbox" disabled {{ !empty($row['vetter']) ? 'checked' : '' }}></td>
                                <td><input type="checkbox" name="tos[{{ $index }}][hod]" value="1" {{ !empty($row['hod']) ? 'checked' : '' }}></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted">No TOS specs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Questions and Vetter History -->
@foreach ($questions as $index => $q)
    <div class="card mb-4">
        <div class="card-header">
            <strong>Question {{ $index + 1 }}</strong>
        </div>
        <div class="card-body">
            <p><strong>Question:</strong> {!! $q['question'] ?? 'N/A' !!}</p>
            <p><strong>Answer:</strong> {!! $q['answer'] ?? 'N/A' !!}</p>

            {{-- Sub-Questions --}}
            @if (!empty($q['sub_questions']))
                <div class="ms-4 mt-3">
                    <strong>Sub-Questions:</strong>
                    @foreach ($q['sub_questions'] as $subIdx => $subQ)
                        <div class="mb-2">
                            <span class="fw-bold">{{ is_numeric($subIdx) ? chr(97 + $loop->index) : $subIdx }})</span>
                            {!! $subQ['question'] ?? '' !!}
                            @if (!empty($subQ['answer']))
                                <div><strong>Answer:</strong> {!! $subQ['answer'] !!}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4">
                <h6 class="mb-3 text-primary d-flex justify-content-between align-items-center">
                    📝 Vetter Review History
                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#vetterLog{{ $index }}">
                        Toggle History
                    </button>
                </h6>
                <div class="collapse show" id="vetterLog{{ $index }}">
                    <ul class="list-group">
                        @if (isset($vetterComments[$index]) && is_array($vetterComments[$index]))
                            @foreach ($vetterComments[$index] as $log)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $log['name'] ?? 'Unknown Vetter' }}</strong>
                                            <div class="text-muted small">{{ $log['timestamp'] ?? 'No timestamp' }}</div>
                                        </div>
                                        <span class="badge bg-secondary">Cycle {{ $loop->iteration }}</span>
                                    </div>
                                    <hr class="my-2" />
                                    <div class="fst-italic">{{ $log['comment'] ?? 'No comment provided.' }}</div>
                                </li>
                            @endforeach
                        @else
                            <li class="list-group-item text-muted">No previous review history.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endforeach


 <!-- Buttons -->
<div class="text-center mt-4 d-flex flex-wrap justify-content-center gap-2">
    <button type="submit" name="action" value="approve" class="btn btn-success">
        ✅ Approve
    </button>
    <button type="submit" name="action" value="deny" class="btn btn-danger">
        ❌ Deny
    </button>
    <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
        💾 Save as Draft
    </button>
    <a href="{{ route('HOD.dashboard') }}" class="btn btn-secondary">
        ⬅ Back
    </a>
</div>

</div>
@endsection
