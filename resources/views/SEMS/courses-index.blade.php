@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">All Courses</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('SEMS.dashboard') }}">SEMS</a></li>
                    <li class="breadcrumb-item active">Course List</li>
                </ul>
            </div>
            <div class="col-auto text-end">
                <a href="{{ route('courses.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Course
                </a>
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

    {{-- Courses Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>#</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Table of Specification (TOS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($courses as $course)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $course->course_code }}</td>
                            <td>{{ $course->course_name }}</td>
                            <td>
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>CO / C</th>
                                            <th>C1</th>
                                            <th>C2</th>
                                            <th>C3</th>
                                            <th>C4</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(['CO1','CO2','CO3'] as $co)
                                        <tr>
                                            <td>{{ $co }}</td>
                                            @for ($i = 1; $i <= 4; $i++)
                                                <td>
                                                    @if(optional($course->tos)[$co]['C'.$i] ?? false)
                                                        ✅
                                                    @else
                                                        ❌
                                                    @endif
                                                </td>
                                            @endfor
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No courses found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
