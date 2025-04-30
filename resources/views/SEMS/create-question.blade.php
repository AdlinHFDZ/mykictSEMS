@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col">
                <h3 class="page-title">Create Question</h3>
                <ul class="breadcrumb justify-content-center" style="list-style: none; padding: 0; margin-top: 20px;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('SEMS.dashboard') }}" style="color: #000000; text-decoration: none;">SEMS</a>
                    </li>
                    <li class="breadcrumb-item active" style="color: #000000;">Create Question</li>
                </ul>
            </div>
        </div>
    </div>

    @php
        $questions = json_decode($exam->questions, true) ?? [];
        $vetterComments = is_array($exam->vetter_comments) ? $exam->vetter_comments : json_decode($exam->vetter_comments, true) ?? [];
        $tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
    @endphp

    <form action="{{ route('exam.submit-question') }}" method="POST">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">

        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Course Name</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="{{ $exam->course_name }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Course Code</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="{{ $exam->course_code }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Section</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="{{ $exam->section }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Coordinator</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-4">Semester</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" value="{{ $exam->semester->name ?? 'N/A' }}" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ TOS TABLE Section -->
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

        <!-- Questions Section -->
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
                    <div class="form-group row">
                        <label class="col-form-label col-md-2">Answer</label>
                        <div class="col-md-10">
                            <textarea class="tinymce form-control" name="answer{{ $i + 1 }}">{{ $questions[$i]['answer'] ?? '' }}</textarea>
                        </div>
                    </div>

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
        @endfor

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Send to Department</button>
            <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">Save as Draft</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
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
</script> --}}
@endpush
