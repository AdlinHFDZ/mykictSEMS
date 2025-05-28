@extends('layouts.master')

@section('content')
@php
    use App\Enums\ExamStatus;
    $questions = is_array($exam->questions) ? $exam->questions : json_decode($exam->questions, true) ?? [];
    $vetterComments = is_array($exam->vetter_comments) ? $exam->vetter_comments : json_decode($exam->vetter_comments, true) ?? [];
    $tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
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

        {{-- Exam Overview --}}
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
                                <label class="form-label"><i class="bi bi-journal-text me-1"></i> Course Name</label>
                                <input type="text" class="form-control" value="{{ $exam->course_name }}" disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="bi bi-code-slash me-1"></i> Course Code</label>
                                <input type="text" class="form-control" value="{{ $exam->course_code }}" disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="bi bi-layers me-1"></i> Section</label>
                                <input type="text" class="form-control" value="{{ $exam->section }}" disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="bi bi-person-badge me-1"></i> Coordinator</label>
                                <input type="text" class="form-control"
                                       value="{{ $exam->ccAssignment && $exam->ccAssignment->user ? $exam->ccAssignment->user->name : 'N/A' }}"
                                       disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="bi bi-person-check me-1"></i> Vetters</label>
                                <input type="text" class="form-control"
                                    value="{{ $exam->vetterAssignments->count()
                                        ? $exam->vetterAssignments->pluck('user.name')->join(', ')
                                        : 'No vetters assigned.' }}"
                                    disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><i class="bi bi-calendar-event me-1"></i> Semester</label>
                                <input type="text" class="form-control" value="{{ $exam->semester->name ?? 'N/A' }}" disabled readonly>
                            </div>
                            @if ($exam->exam_date)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><i class="bi bi-calendar-check me-1"></i> Exam Date</label>
                                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($exam->exam_date)->format('d/m/Y') }}" disabled readonly>
                                </div>
                            @endif
                            @if ($exam->exam_time)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><i class="bi bi-clock me-1"></i> Exam Time</label>
                                    <input type="text" class="form-control" value="{{ $exam->exam_time }}" disabled readonly>
                                </div>
                            @endif
                            @if ($exam->duration)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"><i class="bi bi-hourglass me-1"></i> Duration</label>
                                    <input type="text" class="form-control" value="{{ $exam->duration }}" disabled readonly>
                                </div>
                            @endif
                            @if ($exam->instruction)
                                <div class="col-md-12 mb-3">
                                    <label class="form-label"><i class="bi bi-info-circle me-1"></i> Instructions</label>
                                    <textarea class="form-control" rows="3" disabled readonly>{{ $exam->instruction }}</textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOS Table --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Table of Specification (TOS)</h5>
                @if ($exam->course && $exam->course->tos_pdf)
                    <a href="{{ asset('storage/' . $exam->course->tos_pdf) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-file-pdf me-1"></i> View TOS PDF
                    </a>
                @endif
            </div>
            <div class="card-body table-responsive">
                @if (!empty($tos))
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>PLO</th>
                            <th>CLO</th>
                            <th>Learning Outcome</th>
                            <th>Assessment Method</th>
                            <th>Approve (HOD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tos as $index => $entry)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $entry['plo'] ?? '-' }}</td>
                                <td>{{ $entry['clo'] ?? '-' }}</td>
                                <td class="text-start">{{ $entry['learning_outcome'] ?? '-' }}</td>
                                <td>{{ $entry['assessment'] ?? '-' }}</td>
                                <td>
                                    <input type="checkbox" name="tos[{{ $index }}][hod]" value="1"
                                           {{ !empty($entry['hod']) ? 'checked' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    <p class="text-muted fst-italic mb-0">No TOS data available.</p>
                @endif
            </div>
        </div>

        <!-- Questions and Vetter History -->
        @foreach ($questions as $qIdx => $q)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <strong>Question {{ $loop->iteration }}</strong>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <span class="fw-bold">Main Question:</span> {!! $q['question'] ?? '<span class="text-muted">N/A</span>' !!}
                    </div>
                    @if (isset($q['mark']))
                        <div class="mb-2">
                            <span class="fw-bold">Mark:</span> {{ $q['mark'] }}
                        </div>
                    @endif
                    @if (isset($q['answer']))
                        <div class="mb-2">
                            <span class="fw-bold">Answer:</span> {!! $q['answer'] !!}
                        </div>
                    @endif

                    {{-- Sub-Questions --}}
                    @if (!empty($q['sub_questions']))
                        <div class="ps-3 border-start mb-2">
                            <div class="fw-semibold mb-1">Sub-Questions:</div>
                            @foreach ($q['sub_questions'] as $subIdx => $subQ)
                                <div class="mb-3 ps-3 border-start">
                                    <div>
                                        <span class="fw-bold">{{ is_numeric($subIdx) ? chr(97 + $loop->index) : $subIdx }})</span>
                                        {!! $subQ['question'] ?? '<span class="text-muted">N/A</span>' !!}
                                    </div>
                                    <div>
                                        <span class="fw-bold">Mark:</span> {{ $subQ['mark'] ?? '-' }}
                                    </div>
                                    @if (!empty($subQ['answer']))
                                        <div>
                                            <span class="fw-bold">Answer:</span> {!! $subQ['answer'] !!}
                                        </div>
                                    @endif

                                    {{-- Breakdown --}}
                                    @if (!empty($subQ['breakdowns']))
                                        <div class="ps-3 border-start mt-2">
                                            <span class="fw-semibold">Breakdowns:</span>
                                            @foreach ($subQ['breakdowns'] as $bIdx => $break)
                                                <div class="mb-2 ps-3 border-start">
                                                    <span class="fw-bold">{{ is_numeric($bIdx) ? ['i','ii','iii','iv'][$loop->index] : $bIdx }})</span>
                                                    {!! $break['question'] ?? '<span class="text-muted">N/A</span>' !!}
                                                    <span class="fw-bold ms-2">Mark:</span> {{ $break['mark'] ?? '-' }}
                                                    @if (!empty($break['answer']))
                                                        <div>
                                                            <span class="fw-bold">Answer:</span> {!! $break['answer'] !!}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Vetter Comment History --}}
                    <div class="mt-4">
                        <h6 class="mb-3 text-primary">Vetter Comment History</h6>
                        <div class="d-flex flex-column gap-3">
                            @if ($exam->vetterAssignments->count())
                                @foreach ($exam->vetterAssignments as $vetterAssignment)
                                    @php
                                        $vetterName = $vetterAssignment->user->name ?? 'Unknown Vetter';
                                        $vetterLogs = collect($vetterComments[$qIdx] ?? [])->filter(function($log) use ($vetterName) {
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
    </form>
</div>
@endsection
