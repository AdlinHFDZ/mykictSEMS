@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Smart Examination Management System (SEMS)</h2>

    @if ($role_id == 1)
        <form method="GET" action="{{ route('SEMS.dashboard') }}">
            <div class="form-group">
                <label for="viewSelect">Select a View:</label>
                <select name="view" id="viewSelect" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Choose a page --</option>
                    <option value="assign-vetters" {{ request('view') == 'assign-vetters' ? 'selected' : '' }}>Assign Vetters</option>
                    <option value="cc-dashboard" {{ request('view') == 'cc-dashboard' ? 'selected' : '' }}>CC Dashboard</option>
                    <option value="vetters-page" {{ request('view') == 'vetters-page' ? 'selected' : '' }}>Vetters Page</option>
                </select>
            </div>
        </form>

        @if (request('view') == 'assign-vetters')
            @include('SEMS.assign-vetters')
        @elseif (request('view') == 'cc-dashboard')
            @include('SEMS.CC-dashboard')
        @elseif (request('view') == 'vetters-page')
            @include('SEMS.vetters-page')
        @endif
    @elseif ($role_id == 3)
        @include('SEMS.assign-vetters')
    @elseif ($role_id == 5)
        @include('SEMS.CC-dashboard')
    @elseif ($role_id == 2)
        @include('SEMS.vetters-page')
    @endif
</div>
@endsection
