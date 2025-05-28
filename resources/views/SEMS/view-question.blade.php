@extends('layouts.master')

@section('content')

@php
    use Illuminate\Support\Str;
    $questions = is_array($exam->questions) ? $exam->questions : json_decode($exam->questions, true) ?? [];
    $tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
    $vetterComments = is_array($exam->vetter_comments) ? $exam->vetter_comments : json_decode($exam->vetter_comments, true) ?? [];
@endphp

<div class="content container-fluid">
    <div class="page-header">
        <h3 class="text-center">View Exam</h3>
        <ul class="breadcrumb justify-content-center">
            <li class="breadcrumb-item"><a href="{{ route('SEMS.dashboard') }}">SEMS</a></li>
            <li class="breadcrumb-item active">View</li>
        </ul>
    </div>

    {{-- Exam Overview --}}
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-2">{{ $exam->course_name }} <small class="text-muted">({{ $exam->course_code }})</small></h4>
                        <p class="mb-1"><strong>Section:</strong> {{ $exam->section }}</p>
                        <p class="mb-1"><strong>Status:</strong>
                            <span class="badge bg-{{ $exam->status == 'vetting' ? 'warning text-dark' : 'secondary' }}">
                                {{ ucfirst($exam->status) }}
                            </span>
                        </p>
                        <p class="mb-0"><strong>Semester:</strong> {{ $exam->semester->name ?? 'N/A' }}</p>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" class="form-control" value="{{ $exam->course_name }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course Code</label>
                            <input type="text" class="form-control" value="{{ $exam->course_code }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Section</label>
                            <input type="text" class="form-control" value="{{ $exam->section }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Coordinator</label>
                            <input type="text" class="form-control" value="{{ $exam->ccAssignment->user->name ?? 'N/A' }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vetters</label>
                            <input type="text" class="form-control"
                                value="{{ $exam->vetterAssignments->pluck('user.name')->join(', ') ?: 'No vetters assigned.' }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Semester</label>
                            <input type="text" class="form-control" value="{{ $exam->semester->name ?? 'N/A' }}" disabled>
                        </div>
                        @if ($exam->exam_date)
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Exam Date</label>
                                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($exam->exam_date)->format('d/m/Y') }}" disabled>
                            </div>
                        @endif
                        @if ($exam->exam_time)
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Exam Time</label>
                                <input type="text" class="form-control" value="{{ $exam->exam_time }}" disabled>
                            </div>
                        @endif
                        @if ($exam->duration)
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Duration</label>
                                <input type="text" class="form-control" value="{{ $exam->duration }}" disabled>
                            </div>
                        @endif
                        @if ($exam->instruction)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Instructions</label>
                                <textarea class="form-control" rows="3" disabled readonly>{{ $exam->instruction }}</textarea>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Updated TOS --}}
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @else
                <p class="text-muted fst-italic mb-0">No TOS data available.</p>
            @endif
        </div>
    </div>

    {{-- Questions & Comments --}}
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
                    @forelse ($exam->vetterAssignments as $vetterAssignment)
                        @php
                            $vetterName = $vetterAssignment->user->name ?? 'Unknown';
                            $vetterId = $vetterAssignment->user_id ?? null;
                            $logs = collect($vetterComments[$qIdx] ?? [])->filter(fn($log) => $log['user_id'] == $vetterId);
                        @endphp
                        <div class="mb-3">
                            <div class="fw-bold mb-2">
                                <span class="bg-secondary text-white rounded-circle d-inline-flex justify-content-center align-items-center me-2" style="width:32px;height:32px;">
                                    {{ strtoupper(substr($vetterName, 0, 1)) }}
                                </span>
                                {{ $vetterName }}
                            </div>
                            @forelse ($logs as $log)
                                <div class="p-3 bg-light border-start border-primary border-4 rounded mb-2">
                                    <small class="text-muted">{{ $log['timestamp'] }}</small>
                                    <div class="fst-italic">{{ $log['comment'] }}</div>
                                </div>
                            @empty
                                <div class="text-muted fst-italic">No comments from this vetter.</div>
                            @endforelse
                        </div>
                    @empty
                        <div class="text-muted fst-italic">No vetters assigned.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endforeach

    <div class="text-center mt-4">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">⬅ Back</a>
    </div>
</div>
@endsection
