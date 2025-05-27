@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Add New Course</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('SEMS.dashboard') }}">SEMS</a></li>
                    <li class="breadcrumb-item active">Create Course</li>
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

    <div class="row">
        <div class="col-md-10 offset-md-1">
            <form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Course Code</label>
                    <input type="text" name="course_code" class="form-control" required value="{{ old('course_code') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Course Name</label>
                    <input type="text" name="course_name" class="form-control" required value="{{ old('course_name') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload TOS PDF</label>
                    <input type="file" name="tos_pdf" accept="application/pdf" class="form-control">
                </div>

                <h5 class="mt-4">Table of Specification (TOS)</h5>

                <div id="tos-table">
                    <div class="row fw-bold mb-2 text-center">
                        <div class="col-md-1">#</div>
                        <div class="col-md-2">PLO</div>
                        <div class="col-md-2">CLO</div>
                        <div class="col-md-4">Learning Outcome</div>
                        <div class="col-md-3">Assessment Method</div>
                    </div>

                    <div id="tos-rows">
                        <!-- Dynamic rows -->
                    </div>

                    <div class="text-start mt-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addTosRow()">+ Add Row</button>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success">Save Course</button>
                    <a href="{{ route('courses.index') }}" class="btn btn-secondary">Back to Course List</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let tosIndex = 0;

function addTosRow() {
    const container = document.getElementById('tos-rows');
    const row = document.createElement('div');
    row.classList.add('row', 'mb-2');

    row.innerHTML = `
        <div class="col-md-1 text-center">${tosIndex + 1}</div>
        <div class="col-md-2">
            <input type="number" name="tos[${tosIndex}][plo]" class="form-control" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="tos[${tosIndex}][clo]" class="form-control" required>
        </div>
        <div class="col-md-4">
            <textarea name="tos[${tosIndex}][learning_outcome]" class="form-control" rows="2" required></textarea>
        </div>
        <div class="col-md-3">
            <input type="text" name="tos[${tosIndex}][assessment]" class="form-control" required>
        </div>
    `;
    container.appendChild(row);
    tosIndex++;
}

window.onload = function () {
    addTosRow();
};
</script>
@endpush
