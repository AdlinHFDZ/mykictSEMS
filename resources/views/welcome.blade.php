@extends('layouts.master')

@section('content')
<!-- Top Brand/Partners Bar -->
<div class="w-100 py-2 px-3" style="background: #f6f7f8; border-bottom: 1px solid #e0e3e7;">
    <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
        <img src="{{ asset('assets/img/LOGO-KICT.png') }}" alt="KICT" height="34">
        <span class="mx-2" style="font-weight:500; color: #365c77;">Smart Examination Management System (SEMS) • Kulliyyah of ICT, IIUM</span>
        {{-- Add more logos if desired --}}
    </div>
</div>

<!-- Main Portal Section with Custom Background -->
<div class="container-fluid d-flex flex-column align-items-center justify-content-center position-relative p-0"
     style="background: url('{{ asset('assets/img/background-kict.jpg') }}') no-repeat center center/cover; min-height: 92vh;">

    <!-- Overlay for better text contrast -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background:rgba(255,255,255,0.8); z-index:1;"></div>

    <!-- Main Content (above overlay) -->
    <div class="w-100 position-relative" style="z-index:2;">
        <!-- Headline -->
        <h1 class="text-center fw-bold mt-5" style="font-size:2.5rem; color: #222;">A Smarter Way to Prepare & Manage Exams</h1>
        <p class="lead text-center mb-4" style="color:#555;">
    SEMS simplifies the entire exam workflow for KICT lecturers, vetters, and HODs.<br>
    <span style="font-size:1.05em;">
        No more scattered files, missed deadlines, or paper wastage—<br>
        just smooth, secure, and collaborative exam management.
    </span>
</p>


        <!-- Process Visual (Example with icons) -->
        <div class="row justify-content-center my-4">
            <div class="col-md-10">
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-4">
                    <!-- Step 1 -->
                    <div class="text-center">
                        <img src="{{ asset('assets/img/sems-draft.png') }}" alt="Draft" width="80">
                        <div class="fw-semibold mt-2">Create & Draft</div>
                        <small>Course Coordinator drafts questions</small>
                    </div>
                    <i class="bi bi-arrow-right fs-2 text-primary"></i>
                    <!-- Step 2 -->
                    <div class="text-center">
                        <img src="{{ asset('assets/img/sems-assign.png') }}" alt="Assign" width="80">
                        <div class="fw-semibold mt-2">Assign & Review</div>
                        <small>HOD assigns vetters, feedback collected</small>
                    </div>
                    <i class="bi bi-arrow-right fs-2 text-primary"></i>
                    <!-- Step 3 -->
                    <div class="text-center">
                        <img src="{{ asset('assets/img/sems-approve.png') }}" alt="Approve" width="80">
                        <div class="fw-semibold mt-2">Revise & Approve</div>
                        <small>Revisions done, final approval granted</small>
                    </div>
                    <i class="bi bi-arrow-right fs-2 text-primary"></i>
                    <!-- Step 4 -->
                    <div class="text-center">
                        <img src="{{ asset('assets/img/sems-pdf.png') }}" alt="PDF" width="80">
                        <div class="fw-semibold mt-2">Export PDF</div>
                        <small>Print-ready & archived</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Highlights -->
        <div class="row justify-content-center my-4">
            <div class="col-md-10">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-4 rounded shadow-sm bg-white h-100 text-center">
                            <i class="bi bi-hdd-network-fill display-5 text-primary"></i>
                            <h5 class="mt-3 mb-2">Centralized Question Bank</h5>
                            <p class="mb-0">Store, organize, and reuse exam questions securely in one place.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 rounded shadow-sm bg-white h-100 text-center">
                            <i class="bi bi-people-fill display-5 text-success"></i>
                            <h5 class="mt-3 mb-2">Role-Based Workflow</h5>
                            <p class="mb-0">Smooth collaboration for CC, Vetters, HOD, and Admin—everyone sees what they need.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 rounded shadow-sm bg-white h-100 text-center">
                            <i class="bi bi-file-earmark-pdf-fill display-5 text-danger"></i>
                            <h5 class="mt-3 mb-2">PDF Export & Notifications</h5>
                            <p class="mb-0">Generate formatted papers, get instant status updates & alerts.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call-to-action Button -->
        <div class="my-4 text-center">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5" style="border-radius: 30px; font-size:1.3rem;">
                Login to SEMS
            </a>
        </div>

        <!-- About & Credits -->
        <div class="text-center mt-3 mb-5" style="color: #888; font-size: 0.97rem;">
            <div>
                <b>Project by:</b> Adam Bin Othman & Adlin Hafidz Bin Jamal Shupardi &bull; INFO 4401 FYP KICT IIUM <br>
                <b>Supervisor:</b> Dr. Khairul Azmi
            </div>
            <div class="mt-2">
                <small>Powered by Laravel • Secure • Sustainable • IIUM KICT</small>
            </div>
        </div>
    </div>
</div>
@endsection
