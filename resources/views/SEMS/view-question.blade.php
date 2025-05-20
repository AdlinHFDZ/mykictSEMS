@extends('layouts.master')

@section('content')

<div class="content container-fluid">
    <div class="page-header">
        <h3 class="text-center">View Exam</h3>
        <ul class="breadcrumb justify-content-center">
            <li class="breadcrumb-item"><a href="{{ route('SEMS.dashboard') }}">SEMS</a></li>
            <li class="breadcrumb-item active">View</li>
        </ul>
    </div>

    {{-- Exam Overview Summary --}}
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">

                    {{-- Title & Status --}}
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

                    {{-- Exam Details --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label exam-details-label"><i class="bi bi-journal-text me-1"></i> Course Name</label>
                            <input type="text" class="form-control exam-details-input" value="{{ $exam->course_name }}" disabled readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label exam-details-label"><i class="bi bi-code-slash me-1"></i> Course Code</label>
                            <input type="text" class="form-control exam-details-input" value="{{ $exam->course_code }}" disabled readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label exam-details-label"><i class="bi bi-layers me-1"></i> Section</label>
                            <input type="text" class="form-control exam-details-input" value="{{ $exam->section }}" disabled readonly>
                        </div>
                        <!-- Coordinator -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label exam-details-label"><i class="bi bi-person-badge me-1"></i> Coordinator</label>
                            <input type="text" class="form-control exam-details-input"
                                   value="{{ $exam->ccAssignment && $exam->ccAssignment->user ? $exam->ccAssignment->user->name : 'N/A' }}"
                                   disabled readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label exam-details-label">
                                <i class="bi bi-person-check me-1"></i> Vetters
                            </label>
                            <input
                                type="text"
                                class="form-control exam-details-input"
                                style="text-align: left !important; direction: ltr; padding-left: 18px;"
                                value="{{ $exam->vetterAssignments->count()
                                    ? $exam->vetterAssignments->pluck('user.name')->join(', ')
                                    : 'No vetters assigned.' }}"
                                disabled
                                readonly
                            >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label exam-details-label"><i class="bi bi-calendar-event me-1"></i> Semester</label>
                            <input type="text" class="form-control exam-details-input" value="{{ $exam->semester->name ?? 'N/A' }}" disabled readonly>
                        </div>
                        {{-- Optional: Exam Settings --}}
                        @if ($exam->exam_date)
                            <div class="col-md-4 mb-3">
                                <label class="form-label exam-details-label"><i class="bi bi-calendar-check me-1"></i> Exam Date</label>
                                <input type="text" class="form-control exam-details-input" value="{{ \Carbon\Carbon::parse($exam->exam_date)->format('d/m/Y') }}" disabled readonly>
                            </div>
                        @endif
                        @if ($exam->exam_time)
                            <div class="col-md-4 mb-3">
                                <label class="form-label exam-details-label"><i class="bi bi-clock me-1"></i> Exam Time</label>
                                <input type="text" class="form-control exam-details-input" value="{{ $exam->exam_time }}" disabled readonly>
                            </div>
                        @endif
                        @if ($exam->duration)
                            <div class="col-md-4 mb-3">
                                <label class="form-label exam-details-label"><i class="bi bi-hourglass me-1"></i> Duration</label>
                                <input type="text" class="form-control exam-details-input" value="{{ $exam->duration }}" disabled readonly>
                            </div>
                        @endif
                        @if ($exam->instruction)
                            <div class="col-md-12 mb-3">
                                <label class="form-label exam-details-label"><i class="bi bi-info-circle me-1"></i> Instructions</label>
                                <textarea class="form-control exam-details-textarea" rows="3" disabled readonly>{{ $exam->instruction }}</textarea>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                            <td><input type="checkbox" disabled {{ !empty($row['hod']) ? 'checked' : '' }}></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted">No TOS items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Questions Section -->
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
    <h6 class="mb-3 text-primary">Vetter Comment History</h6>
    <div class="d-flex flex-column gap-3">
        @if ($exam->vetterAssignments->count())
            @foreach ($exam->vetterAssignments as $vetterAssignment)
                @php
                    $vetterName = $vetterAssignment->user->name ?? 'Unknown Vetter';
                    // Find all comments for this vetter for this question index
                    $vetterLogs = collect($vetterComments[$index] ?? [])->filter(function($log) use ($vetterName) {
                        return isset($log['name']) && $log['name'] === $vetterName;
                    });
                @endphp
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                             style="width: 38px; height: 38px; font-size: 18px;">
                            {{ strtoupper(substr($vetterName, 0, 1)) }}
                        </div>
                        <span class="fw-bold">{{ $vetterName }}</span>
                    </div>
                    @if ($vetterLogs->count())
                        @foreach ($vetterLogs as $cycle => $log)
                            <div class="d-flex align-items-start shadow-sm p-3 bg-white rounded mb-2"
                                 style="border-left: 6px solid #0d6efd;">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="badge bg-secondary ms-0">Cycle {{ $loop->iteration }}</span>
                                        </div>
                                        <small class="text-muted">{{ $log['timestamp'] ?? 'No timestamp' }}</small>
                                    </div>
                                    <div class="mt-2 fst-italic" style="white-space: pre-line;">{{ $log['comment'] ?? 'No comment provided.' }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted fst-italic mb-3">No comments from this vetter.</div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="text-muted fst-italic">No vetters assigned.</div>
        @endif
    </div>
</div>

            </div>
        </div>
    @endforeach

    <div class="text-center mt-4">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">⬅ Back</a>
    </div>
</div>

@endsection
