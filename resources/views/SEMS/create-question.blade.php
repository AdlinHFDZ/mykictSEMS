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

        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label exam-details-label"><i class="bi bi-journal-text me-1"></i> Course Name</label>
                                <input type="text" class="form-control exam-details-input" value="{{ $exam->course_name }}" disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label exam-details-label"><i class="bi bi-code-slash me-1"></i> Course Code</label>
                                <input type="text" class="form-control exam-details-input" value="{{ $exam->course_code }}" disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label exam-details-label"><i class="bi bi-layers me-1"></i> Section</label>
                                <input type="text" class="form-control exam-details-input" value="{{ $exam->section }}" disabled readonly>
                            </div>
                            <!-- Coordinator -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label exam-details-label"><i class="bi bi-person-badge me-1"></i> Coordinator</label>
                                <input type="text" class="form-control exam-details-input"
                                    value="{{ $exam->ccAssignment && $exam->ccAssignment->user ? $exam->ccAssignment->user->name : 'N/A' }}"
                                    disabled readonly>
                            </div>
                            <!-- Vetters (comma-separated) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label exam-details-label"><i class="bi bi-person-check me-1"></i> Vetters</label>
                                <input
                                    type="text"
                                    class="form-control exam-details-input"
                                    style="text-align: left !important; direction: ltr; padding-left: 18px;"
                                    value="{{ $exam->vetterAssignments->count()
                                        ? $exam->vetterAssignments->pluck('user.name')->join(', ')
                                        : 'No vetters assigned.' }}"
                                    disabled
                                    readonly
                                >
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label exam-details-label"><i class="bi bi-calendar-event me-1"></i> Semester</label>
                                <input type="text" class="form-control exam-details-input" value="{{ $exam->semester->name ?? 'N/A' }}" disabled readonly>
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

        {{-- Exam Settings --}}
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
                    {{-- Main Question --}}
                    <div class="form-group row">
                        <label class="col-form-label col-md-2">Question</label>
                        <div class="col-md-10">
                            <textarea class="tinymce form-control" name="questions[{{ $i }}][question]">{{ $questions[$i]['question'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row mt-2">
                        <label class="col-form-label col-md-2">Mark</label>
                        <div class="col-md-10">
                            <input type="number" class="form-control" name="questions[{{ $i }}][mark]" min="0"
                                value="{{ $questions[$i]['mark'] ?? '' }}" placeholder="Enter mark for this question">
                        </div>
                    </div>
                    <div class="form-group row mt-3">
                        <label class="col-form-label col-md-2">Answer</label>
                        <div class="col-md-10">
                            <textarea class="tinymce form-control" name="questions[{{ $i }}][answer]">{{ $questions[$i]['answer'] ?? '' }}</textarea>
                        </div>
                    </div>

                    {{-- Sub-Questions --}}
                    <hr>
                    <h6>Sub-Questions</h6>
                    <div id="sub-questions-{{ $i }}">
                        @php
                            $subQuestions = $questions[$i]['sub_questions'] ?? [];
                        @endphp
                        @foreach ($subQuestions as $subIndex => $subQ)
                            <div class="sub-question-block mb-2 input-group align-items-start">
                                <span class="input-group-text">{{ is_numeric($subIndex) ? chr(97 + $loop->index) : $subIndex }})</span>
                                <div class="flex-grow-1 me-2">
                                    <textarea class="form-control tinymce mb-1"
                                        name="questions[{{ $i }}][sub_questions][{{ is_numeric($subIndex) ? chr(97 + $subIndex) : $subIndex }}][question]"
                                        rows="2" placeholder="Sub-question">{!! $subQ['question'] ?? '' !!}</textarea>
                                    <input type="number" class="form-control mb-1"
                                        name="questions[{{ $i }}][sub_questions][{{ is_numeric($subIndex) ? chr(97 + $subIndex) : $subIndex }}][mark]"
                                        value="{{ $subQ['mark'] ?? '' }}" placeholder="Mark" min="0" style="max-width: 120px;">
                                    <textarea class="form-control tinymce"
                                        name="questions[{{ $i }}][sub_questions][{{ is_numeric($subIndex) ? chr(97 + $subIndex) : $subIndex }}][answer]"
                                        rows="2" placeholder="Answer">{!! $subQ['answer'] ?? '' !!}</textarea>
                                </div>
                                <button type="button" class="btn btn-danger align-middle-self-stretch" onclick="removeSubQuestion(this)">Remove</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addSubQuestion({{ $i }})">+ Add Sub-question</button>

                    {{-- Vetter Comment History for this question --}}
                    <div class="mt-4">
                        <h6 class="mb-3 text-primary">Vetter Comment History</h6>
                        <div class="d-flex flex-column gap-3">
                            @if ($exam->vetterAssignments->count())
                                @foreach ($exam->vetterAssignments as $vetterAssignment)
                                    @php
                                        $vetterName = $vetterAssignment->user->name ?? 'Unknown Vetter';
                                        // Use $i as the question index!
                                        $vetterLogs = collect($vetterComments[$i] ?? [])->filter(function($log) use ($vetterName) {
                                            return isset($log['name']) && $log['name'] === $vetterName;
                                        });
                                    @endphp
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 38px; height: 38px; font-size: 18px;">
                                                {{ strtoupper(substr($vetterName, 0, 1)) }}
                                            </div>
                                            <span class="fw-bold">{{ $vetterName }}</span>
                                        </div>
                                        @if ($vetterLogs->count())
                                            @foreach ($vetterLogs as $cycle => $log)
                                                <div class="d-flex align-items-start shadow-sm p-3 bg-white rounded mb-2"
                                                    style="border-left: 6px solid #0d6efd;">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <span class="badge bg-secondary ms-0">Cycle {{ $loop->iteration }}</span>
                                                            </div>
                                                            <small class="text-muted">{{ $log['timestamp'] ?? 'No timestamp' }}</small>
                                                        </div>
                                                        <div class="mt-2 fst-italic" style="white-space: pre-line;">{{ $log['comment'] ?? 'No comment provided.' }}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-muted fst-italic mb-3">No comments from this vetter.</div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted fst-italic">No vetters assigned.</div>
                            @endif
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
<script src="https://cdn.tiny.cloud/1/d6b2sr6wvk401h8i55fuufj8wlc5pouxeasair9hg4a8zwfy/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  // Initial TinyMCE setup for all .tinymce fields
  function initAllTinyMCE() {
    tinymce.init({
      selector: 'textarea.tinymce',
      plugins: [
        'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
        'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
      ],
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
      tinycomments_mode: 'embedded',
      tinycomments_author: '{{ Auth::user()->name ?? "Author" }}',
      mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
      ],
      ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initAllTinyMCE();
  });

  function addSubQuestion(qIdx) {
    var container = document.getElementById('sub-questions-' + qIdx);
    var count = container.children.length;
    var nextChar = String.fromCharCode(97 + count); // 'a', 'b', etc.
    var questionId = 'subq-q-' + qIdx + '-' + nextChar;
    var answerId = 'subq-a-' + qIdx + '-' + nextChar;

    var html = `
        <div class="sub-question-block mb-2 input-group align-items-start">
            <span class="input-group-text">${nextChar})</span>
            <div class="flex-grow-1 me-2">
                <textarea id="${questionId}" class="form-control tinymce mb-1" name="questions[${qIdx}][sub_questions][${nextChar}][question]" rows="2" placeholder="Sub-question"></textarea>
                <input type="number" class="form-control mb-1" name="questions[${qIdx}][sub_questions][${nextChar}][mark]" placeholder="Mark" min="0" style="max-width: 120px;">
                <textarea id="${answerId}" class="form-control tinymce" name="questions[${qIdx}][sub_questions][${nextChar}][answer]" rows="2" placeholder="Answer"></textarea>
            </div>
            <button type="button" class="btn btn-danger align-self-stretch" onclick="removeSubQuestion(this)">Remove</button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);

    setTimeout(function() {
        if (tinymce.get(questionId)) tinymce.get(questionId).remove();
        if (tinymce.get(answerId)) tinymce.get(answerId).remove();
        tinymce.init({
            selector: `#${questionId}, #${answerId}`,
            plugins: [
              'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
              'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: '{{ Auth::user()->name ?? "Author" }}'
        });
    }, 100);
  }

  function removeSubQuestion(btn) {
    btn.closest('.sub-question-block').remove();
  }
</script>
@endpush
