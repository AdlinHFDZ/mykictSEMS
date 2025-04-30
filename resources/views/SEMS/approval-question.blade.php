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

        <div class="card p-4">
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-form-label col-md-4">Course Name</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" value="{{ $exam->course_name }}" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4">Course Code</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" value="{{ $exam->course_code }}" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4">Section</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" value="{{ $exam->section }}" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4">Semester</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" value="{{ $exam->semester->name ?? 'N/A' }}" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            @php
                $questions = json_decode($exam->questions, true) ?? [];
                $vetterComments = is_array($exam->vetter_comments) ? $exam->vetter_comments : json_decode($exam->vetter_comments, true) ?? [];
                $tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
                $vetterCommentsLog = $vetterCommentsLog ?? [];
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
                                    <td>
                                        <input type="text" class="form-control" value="{{ $row['spec'] ?? '' }}" readonly>
                                    </td>
                                    <td>
                                        <input type="checkbox" disabled {{ !empty($row['cc']) ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <input type="checkbox" disabled {{ !empty($row['vetter']) ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="tos[{{ $index }}][hod]" value="1" {{ !empty($row['hod']) ? 'checked' : '' }}>
                                    </td>
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

            <!-- Questions -->
            @foreach ($questions as $index => $q)
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Question {{ $index + 1 }}</strong>
                </div>
                <div class="card-body">
                    <p><strong>Question:</strong> {!! $q['question'] ?? 'N/A' !!}</p>
                    <p><strong>Answer:</strong> {!! $q['answer'] ?? 'N/A' !!}</p>

                    <!-- ✅ Vetter Review History -->
                    <div class="mt-4">
                        <h6 class="mb-3 text-primary">📝 Vetter Review History</h6>
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
            @endforeach

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-success">Approve ✅</button>
                <a href="{{ route('HOD.dashboard') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
