@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit Course</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('SEMS.dashboard') }}">SEMS</a></li>
                    <li class="breadcrumb-item active">Edit Course</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Edit Course Form --}}
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form method="POST" action="{{ route('courses.update', $course->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Course Code</label>
                    <input type="text" name="course_code" class="form-control" required value="{{ old('course_code', $course->course_code) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Course Name</label>
                    <input type="text" name="course_name" class="form-control" required value="{{ old('course_name', $course->course_name) }}">
                </div>

                <h5 class="mt-4">Table of Specification (TOS)</h5>

                <div id="tos-table">
                    <div class="row mb-2">
                        <div class="col-md-1 text-center"><strong>No.</strong></div>
                        <div class="col-md-6"><strong>TOS Spec</strong></div>
                        <div class="col-md-1 text-center"><strong>CC</strong></div>
                        <div class="col-md-1 text-center"><strong>Vetter</strong></div>
                        <div class="col-md-1 text-center"><strong>HOD</strong></div>
                    </div>

                    <div id="tos-rows">
                        @php
                            $tos = is_array($course->tos) ? $course->tos : json_decode($course->tos, true) ?? [];
                        @endphp

                        @foreach ($tos as $index => $row)
                            <div class="row mb-2">
                                <div class="col-md-1 text-center">{{ $index + 1 }}</div>
                                <div class="col-md-6">
                                    <input type="text" name="tos[{{ $index }}][spec]" class="form-control" value="{{ $row['spec'] ?? '' }}" required>
                                </div>
                                <div class="col-md-1 text-center">
                                    <input type="checkbox" disabled {{ !empty($row['cc']) ? 'checked' : '' }}>
                                </div>
                                <div class="col-md-1 text-center">
                                    <input type="checkbox" disabled {{ !empty($row['vetter']) ? 'checked' : '' }}>
                                </div>
                                <div class="col-md-1 text-center">
                                    <input type="checkbox" disabled {{ !empty($row['hod']) ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="text-start mt-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addTosRow()">+ Add Row</button>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Update Course</button>
                    <a href="{{ route('courses.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let tosIndex = {{ count($tos) }};

function addTosRow() {
    const container = document.getElementById('tos-rows');
    const row = document.createElement('div');
    row.classList.add('row', 'mb-2');

    row.innerHTML = `
        <div class="col-md-1 text-center">${tosIndex + 1}</div>
        <div class="col-md-6">
            <input type="text" name="tos[${tosIndex}][spec]" class="form-control" required>
        </div>
        <div class="col-md-1 text-center">
            <input type="checkbox" disabled>
        </div>
        <div class="col-md-1 text-center">
            <input type="checkbox" disabled>
        </div>
        <div class="col-md-1 text-center">
            <input type="checkbox" disabled>
        </div>
    `;
    container.appendChild(row);
    tosIndex++;
}
</script>
@endpush
