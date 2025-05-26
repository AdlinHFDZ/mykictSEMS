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
                        <p><strong>Section:</strong> {{ $exam->section }}</p>
                        <p><strong>Status:</strong>
                            <span class="badge bg-{{ $exam->status == 'vetting' ? 'warning text-dark' : 'secondary' }}">
                                {{ ucfirst($exam->status) }}
                            </span>
                        </p>
                        <p><strong>Semester:</strong> {{ $exam->semester->name ?? 'N/A' }}</p>
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
                            <input type="text" class="form-control" value="{{ $exam->vetterAssignments->pluck('user.name')->join(', ') ?: 'No vetters assigned.' }}" disabled>
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

    {{-- TOS --}}
    <div class="card mb-4">
        <div class="card-header"><h5 class="card-title">Table of Specification (TOS)</h5></div>
        <div class="card-body table-responsive">
            <table class="table table-bordered text-center">
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

    {{-- Questions & Comments --}}
    @foreach ($questions as $index => $q)
        <div class="card mb-4">
            <div class="card-header"><strong>Question {{ $index + 1 }}</strong></div>
            <div class="card-body">
                <p><strong>Question:</strong> {!! $q['question'] ?? 'N/A' !!}</p>
                <p><strong>Mark:</strong> {{ $q['mark'] ?? '-' }}</p>
                <p><strong>Answer:</strong> {!! $q['answer'] ?? 'N/A' !!}</p>

                {{-- Sub-Questions --}}
                @if (!empty($q['sub_questions']))
                    <div class="ms-4 mt-3">
                        <strong>Sub-Questions:</strong>
                        @foreach ($q['sub_questions'] as $subIdx => $subQ)
                            <div class="mb-2">
                                <span class="fw-bold">{{ is_numeric($subIdx) ? chr(97 + $loop->index) : $subIdx }})</span>
                                {!! $subQ['question'] ?? '' !!}
                                <span class="ms-2"><strong>Mark:</strong> {{ $subQ['mark'] ?? '-' }}</span>
                                @if (!empty($subQ['answer']))
                                    <div><strong>Answer:</strong> {!! $subQ['answer'] !!}</div>
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
                            $logs = collect($vetterComments[$index] ?? [])->filter(fn($log) => $log['user_id'] == $vetterId);
                        @endphp
                        <div class="mb-3">
                            <div class="fw-bold">{{ $vetterName }}</div>
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
