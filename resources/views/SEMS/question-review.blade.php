@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Review Exam: {{ $exam->course_name }} ({{ $exam->course_code }})</h2>

    <div class="mb-4">
        <strong>Section:</strong> {{ $exam->section }} <br>
        <strong>Status:</strong>
        <span class="badge bg-{{ $exam->status == 'vetting' ? 'warning' : 'success' }}">
            {{ ucfirst($exam->status) }}
        </span>
    </div>

    <form method="POST" action="{{ route('question.review.submit') }}">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">

        @php
            $questions = json_decode($exam->questions, true) ?? [];
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
                                    <input type="checkbox" name="tos[{{ $index }}][vetter]" value="1" {{ !empty($row['vetter']) ? 'checked' : '' }}>
                                </td>
                                <td>
                                    <input type="checkbox" disabled {{ !empty($row['hod']) ? 'checked' : '' }}>
                                </td>
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

        <!-- Questions -->
        @foreach ($questions as $index => $q)
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Question {{ $index + 1 }}</strong>
                </div>
                <div class="card-body">
                    <p><strong>Question:</strong> {!! $q['question'] ?? 'N/A' !!}</p>
                    <p><strong>Answer:</strong> {!! $q['answer'] ?? 'N/A' !!}</p>

                    <div class="form-group mt-3">
                        <label for="comment{{ $index }}">Your Comment:</label>
                        <textarea name="comments[{{ $index }}]" id="comment{{ $index }}" rows="3" class="form-control" placeholder="Suggest edits, corrections..."></textarea>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success">Submit Review ✅</button>
            <a href="{{ route('vetters.dashboard') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
