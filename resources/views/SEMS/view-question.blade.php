@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">View Exam: {{ $exam->course_name }} ({{ $exam->course_code }})</h2>

    <div class="mb-4">
        <strong>Section:</strong> {{ $exam->section }} <br>
        <strong>Status:</strong>
        <span class="badge bg-secondary">{{ ucfirst($exam->status) }}</span> <br>
        <strong>Semester:</strong> {{ $exam->semester->name ?? 'N/A' }}
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

    <!-- Questions Section -->
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
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">⬅ Back</a>
    </div>
</div>
@endsection
