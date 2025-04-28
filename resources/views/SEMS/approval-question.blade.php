@extends('layouts.master')

@section('content')
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
            <h5><strong>Course:</strong> {{ $exam->course_name }} ({{ $exam->course_code }})</h5>
            <p><strong>Section:</strong> {{ $exam->section }}</p>

            <hr>

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

                        @if(isset($vetterComments[$index]))
                            <div class="alert alert-warning mt-2">
                                <strong>Vetter Comment:</strong> {{ $vetterComments[$index] }}
                            </div>
                        @endif
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
