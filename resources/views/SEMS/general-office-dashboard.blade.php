@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">General Office – Approved Exam Papers</h2>

    <div class="mb-3">
        <strong>Active Semester:</strong> {{ $activeSemester->name ?? 'N/A' }}
    </div>

    @if($exams->isEmpty())
        <div class="alert alert-info">No approved exams available for printing.</div>
    @else
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Section</th>
                    <th>Created By</th>
                    <th>Semester</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                    @foreach($exams as $exam)
                        <tr>
                            <td>{{ $exam->course_code }}</td>
                            <td>{{ $exam->course_name }}</td>
                            <td>{{ $exam->section }}</td>
                            <td>{{ optional($exam->createdBy)->name ?? 'N/A' }}</td>
                            <td>{{ optional($exam->semester)->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('pdf.download', $exam->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                    Download PDF
                                </a>
                            </td>
                        </tr>
                    @endforeach
                <!-- @foreach($exams as $exam)
                <tr>
                    <td>{{ $exam->course_code }}</td>
                    <td>{{ $exam->course_name }}</td>
                    <td>{{ $exam->section }}</td>
                    <td>{{ $exam->createdBy->name ?? '-' }}</td>
                    <td>{{ $exam->semester->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('pdf.download', $exam->id) }}" class="btn btn-sm btn-primary" target="_blank">
                            Download PDF
                        </a>
                    </td>
                </tr>
                @endforeach -->
            </tbody>
        </table>
    @endif
</div>
@endsection
