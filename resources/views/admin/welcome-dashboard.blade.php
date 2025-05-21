@extends('layouts.master')

@section('content')
<div class="content container-fluid d-flex align-items-center justify-content-center" style="height: 100vh; background: url('{{ asset('assets/img/background-kict.jpg') }}') no-repeat center center/cover;">

    <div class="page-header text-center" style="color: #7e4c4c;">
        <h3 class="page-title" style="font-size: 4.5rem; font-weight: bold; text-transform: uppercase; color: #fff; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">
            Welcome to MYKICT
        </h3>
        <p style="margin: 20px auto 0; font-size: 1.5rem; font-style: italic; max-width: 600px; color: #f8f9fa; background: rgba(0, 0, 0, 0.5); padding: 10px 20px; border-radius: 10px; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7); text-align: center;">
            "Codes to Heaven, Live for the Afterlife"
        </p>
        <ul class="breadcrumb justify-content-center" style="list-style: none; padding: 0; margin-top: 20px;">
            <li class="breadcrumb-item">
                <a href="dashboard" style="color: #ffffff; text-decoration: none;">Home</a>
            </li>
            <li class="breadcrumb-item active" style="color: #ffffff;">welcome</li>
        </ul>
        <!-- SEMS Button Only -->
        <div class="mt-4">
            <a href="{{ url('SEMS-dashboard') }}" class="btn btn-primary btn-lg shadow" style="font-size: 1.5rem; padding: 0.8rem 2.5rem; border-radius: 50px;">
                SMART EXAMINATION MANAGEMENT SYSTEM
            </a>
        </div>
    </div>
</div>
@endsection
