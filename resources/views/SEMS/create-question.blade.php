@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h3 class="text-center">Create Question</h3>
        <ul class="breadcrumb justify-content-center">
            <li class="breadcrumb-item"><a href="{{ route('SEMS.dashboard') }}">SEMS</a></li>
            <li class="breadcrumb-item active">Create</li>
        </ul>
    </div>

    @php
        $questions = json_decode($exam->questions, true) ?? [];
        $vetterComments = is_array($exam->vetter_comments) ? $exam->vetter_comments : json_decode($exam->vetter_comments, true) ?? [];
        $tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
    @endphp

    <form action="{{ route('exam.submit-question') }}" method="POST">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">

        {{-- Exam Overview Summary --}}
<div class="row justify-content-center mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                {{-- Title & Status --}}
                <div class="text-center mb-4">
                    <h4 class="fw-bold mb-2">{{ $exam->course_name }} <small class="text-muted">({{ $exam->course_code }})</small></h4>
                    <p class="mb-1"><strong>Section:</strong> {{ $exam->section }}</p>
                    <p class="mb-1"><strong>Status:</strong>
                        <span class="badge bg-{{ $exam->status == 'vetting' ? 'warning text-dark' : 'secondary' }}">
                            {{ ucfirst($exam->status) }}
                        </span>
                    </p>
                    <p class="mb-0"><strong>Semester:</strong> {{ $exam->semester->name ?? 'N/A' }}</p>
                </div>

                <hr class="mb-4">

                {{-- Detail Info --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-journal-text me-1"></i> Course Name</label>
                        <input type="text" class="form-control bg-light" value="{{ $exam->course_name }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-code-slash me-1"></i> Course Code</label>
                        <input type="text" class="form-control bg-light" value="{{ $exam->course_code }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-layers me-1"></i> Section</label>
                        <input type="text" class="form-control bg-light" value="{{ $exam->section }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-person-badge me-1"></i> Coordinator</label>
                        <input type="text" class="form-control bg-light" value="{{ $exam->createdBy->name ?? Auth::user()->name }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold"><i class="bi bi-calendar-event me-1"></i> Semester</label>
                        <input type="text" class="form-control bg-light" value="{{ $exam->semester->name ?? 'N/A' }}" disabled>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


        {{-- TOS Table --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Table of Specification (TOS)</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
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
                                    <input type="checkbox" name="tos[{{ $index }}][cc]" value="1" {{ !empty($row['cc']) ? 'checked' : '' }}>
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

        <div class="card p-3 mb-4">
    <h5>Exam Settings</h5>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label for="exam_date" class="form-label">Exam Date</label>
            <input type="date" name="exam_date" id="exam_date"
                value="{{ old('exam_date', $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d') : '') }}"
                class="form-control">
        </div>
        <div class="col-md-4 mb-3">
            <label for="exam_time" class="form-label">Exam Time</label>
            <input type="text" name="exam_time" id="exam_time"
                   value="{{ old('exam_time', $exam->exam_time) }}"
                   placeholder="e.g. 9:00 AM – 12:00 PM"
                   class="form-control">
        </div>
        <div class="col-md-4 mb-3">
            <label for="duration" class="form-label">Duration</label>
            <input type="text" name="duration" id="duration"
                   value="{{ old('duration', $exam->duration) }}"
                   placeholder="e.g. 3 Hours"
                   class="form-control">
        </div>
        <div class="col-md-12 mb-3">
            <label for="instruction" class="form-label">Exam Instructions</label>
            <textarea name="instruction" id="instruction" class="form-control"
                      rows="4" placeholder="Write instructions here...">{{ old('instruction', $exam->instruction) }}</textarea>
        </div>
    </div>
</div>

        {{-- Question Editor Section --}}
        @for ($i = 0; $i < 4; $i++)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Question {{ $i + 1 }}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label col-md-2">Question</label>
                        <div class="col-md-10">
                            <textarea class="tinymce form-control" name="question{{ $i + 1 }}">{{ $questions[$i]['question'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row mt-3">
                        <label class="col-form-label col-md-2">Answer</label>
                        <div class="col-md-10">
                            <textarea class="tinymce form-control" name="answer{{ $i + 1 }}">{{ $questions[$i]['answer'] ?? '' }}</textarea>
                        </div>
                    </div>

                    {{-- Vetter Comments (Collapsible) --}}
                    <div class="mt-4">
                        <h6 class="d-flex justify-content-between align-items-center text-primary">
                            📝 Vetter Review History
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#vetterLog{{ $i }}">
                                Toggle History
                            </button>
                        </h6>
                        <div class="collapse show" id="vetterLog{{ $i }}">
                            <ul class="list-group">
                                @if (isset($vetterComments[$i]) && is_array($vetterComments[$i]))
                                    @foreach ($vetterComments[$i] as $log)
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
            </div>
        @endfor

        {{-- Action Buttons --}}
        <div class="text-center mb-5">
            <button type="submit" class="btn btn-primary">Send to Department</button>
            <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">Save as Draft</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- Uncomment this if TinyMCE is used --}}
{{--
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: 'textarea.tinymce',
        height: 200,
        menubar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
    });
</script>
--}}
@endpush
