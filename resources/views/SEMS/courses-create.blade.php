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

    {{-- Course Form --}}
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form method="POST" action="{{ route('courses.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Course Code</label>
                    <input type="text" name="course_code" class="form-control" required value="{{ old('course_code') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Course Name</label>
                    <input type="text" name="course_name" class="form-control" required value="{{ old('course_name') }}">
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
                        <!-- Dynamic TOS rows will be inserted here -->
                    </div>
                </div>

                <div class="text-start mt-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addTosRow()">+ Add Row</button>
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

// Auto add 1 row when page loads
window.onload = function() {
    addTosRow();
};
</script>
@endpush
