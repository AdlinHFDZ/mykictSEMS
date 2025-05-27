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

{{-- Updated TOS Table --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Table of Specification (TOS)</h5>
        @if ($exam->course && $exam->course->tos_pdf)
            <a href="{{ asset('storage/' . $exam->course->tos_pdf) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-file-pdf me-1"></i> View TOS PDF
            </a>
        @endif
    </div>
    <div class="card-body table-responsive">
        @if (!empty($tos))
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>PLO</th>
                    <th>CLO</th>
                    <th>Learning Outcome</th>
                    <th>Assessment Method</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tos as $index => $entry)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $entry['plo'] ?? '-' }}</td>
                        <td>{{ $entry['clo'] ?? '-' }}</td>
                        <td class="text-start">{{ $entry['learning_outcome'] ?? '-' }}</td>
                        <td>{{ $entry['assessment'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @else
            <p class="text-muted fst-italic mb-0">No TOS data available.</p>
        @endif
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
                            <textarea class="tinymce form-control" id="question-{{ $i }}" name="questions[{{ $i }}][question]">{{ $questions[$i]['question'] ?? '' }}</textarea>
<div class="dropdown mt-2">
  <button class="btn btn-outline-dark dropdown-toggle btn-sm" type="button" id="semsAiDropdown-{{ $i }}" data-bs-toggle="dropdown" aria-expanded="false">
    SEMS AI
  </button>
  <ul class="dropdown-menu" aria-labelledby="semsAiDropdown-{{ $i }}">
    <li><a class="dropdown-item ask-ai-btn" href="#" data-target="question-{{ $i }}">Ask AI for Suggestion</a></li>
    <li><a class="dropdown-item clarify-ai-btn" href="#" data-target="question-{{ $i }}">Improve Clarity</a></li>
    <li><a class="dropdown-item answer-ai-btn" href="#" data-target="question-{{ $i }}">Generate Answer</a></li>
    <li><a class="dropdown-item similarity-ai-btn" href="#" data-target="question-{{ $i }}">Check Similarity</a></li>
  </ul>
</div>




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
        @php $subLabel = is_numeric($subIndex) ? chr(97 + $loop->index) : $subIndex; @endphp
        <div class="sub-question-block mb-3 input-group align-items-start">
            <span class="input-group-text">{{ $subLabel }})</span>
            <div class="flex-grow-1 me-2">
                <textarea class="form-control tinymce mb-2" id="sub-q-{{ $i }}-{{ $subLabel }}"
                    name="questions[{{ $i }}][sub_questions][{{ $subLabel }}][question]"
                    rows="2" placeholder="Sub-question">{!! $subQ['question'] ?? '' !!}</textarea>

                <div class="dropdown mb-2">
                    <button class="btn btn-outline-dark dropdown-toggle btn-sm" type="button"
                        id="subSemsAiDropdown-{{ $i }}-{{ $subLabel }}"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        SEMS AI
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="subSemsAiDropdown-{{ $i }}-{{ $subLabel }}">
                        <li><a class="dropdown-item ask-ai-btn" href="#" data-target="sub-q-{{ $i }}-{{ $subLabel }}">Ask AI for Suggestion</a></li>
                        <li><a class="dropdown-item clarify-ai-btn" href="#" data-target="sub-q-{{ $i }}-{{ $subLabel }}">Improve Clarity</a></li>
                        <li><a class="dropdown-item answer-ai-btn" href="#" data-target="sub-q-{{ $i }}-{{ $subLabel }}">Generate Answer</a></li>
                        <li><a class="dropdown-item similarity-ai-btn" href="#" data-target="sub-q-{{ $i }}-{{ $subLabel }}">Check Similarity</a></li>
                    </ul>
                </div>

                <input type="number" class="form-control mb-2"
                    name="questions[{{ $i }}][sub_questions][{{ $subLabel }}][mark]"
                    value="{{ $subQ['mark'] ?? '' }}" placeholder="Mark" min="0" style="max-width: 120px;">
                <textarea class="form-control tinymce"
                    name="questions[{{ $i }}][sub_questions][{{ $subLabel }}][answer]"
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
let lastEditorId = null;
let lastAISuggestion = '';

function initAllTinyMCE() {
  document.querySelectorAll('textarea.tinymce').forEach((el) => {
    if (tinymce.get(el.id)) {
      tinymce.get(el.id).remove();
    }
  });

  tinymce.init({
    selector: 'textarea.tinymce',
    plugins: [
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace',
      'table', 'visualblocks', 'wordcount', 'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed',
      'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage',
      'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect',
      'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: '{{ Auth::user()->name ?? "Author" }}',
    mergetags_list: [{ value: 'First.Name', title: 'First Name' }, { value: 'Email', title: 'Email' }],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
  });
}

function attachAIListeners() {
  document.body.addEventListener('click', function (e) {
    if (e.target.classList.contains('ask-ai-btn')) {
      e.preventDefault();
      runAI(e.target, 'question');
    }
    if (e.target.classList.contains('clarify-ai-btn')) {
      e.preventDefault();
      runAI(e.target, 'clarify');
    }
    if (e.target.classList.contains('answer-ai-btn')) {
      e.preventDefault();
      runAI(e.target, 'answer');
    }
    if (e.target.classList.contains('similarity-ai-btn')) {
      e.preventDefault();
      runSimilarityCheck(e.target);
    }
  });
}

async function runAI(button, type) {
  const targetId = button.getAttribute('data-target');
  const editor = tinymce.get(targetId);
  if (!editor) return alert('Editor not found.');

  const content = editor.getContent({ format: 'text' });
  if (!content.trim()) return alert('Please enter some content.');

  let prompt = content;
  if (type === 'clarify') {
    prompt = `Rewrite the following exam question using clear, formal, academic English:\n\n"${content}"`;
  } else if (type === 'answer') {
    prompt = `Provide a clear and concise model answer for the following exam question:\n\n"${content}"`;
  }

  button.innerText = 'Processing...';
  button.disabled = true;

  try {
    const res = await fetch('http://127.0.0.1:11434/api/generate', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ model: 'llama3', prompt: prompt, stream: false })
    });
    const data = await res.json();

    lastEditorId = targetId;
    lastAISuggestion = data.response;
    document.getElementById('aiModalBody').innerText = data.response;
    new bootstrap.Modal(document.getElementById('aiModal')).show();
  } catch (err) {
    alert('❌ AI Server Error: ' + err.message);
  }

  button.innerText = type === 'clarify' ? 'Clarify' : type === 'answer' ? 'Answer' : 'Ask AI';
  button.disabled = false;
}

async function runSimilarityCheck(button) {
  const targetId = button.getAttribute('data-target');
  const editor = tinymce.get(targetId);
  if (!editor) return alert('Editor not found.');

  const content = editor.getContent({ format: 'text' });
  if (!content.trim()) return alert('Please enter a question to check.');

  button.innerText = 'Checking...';
  button.disabled = true;

  try {
    const examId = document.querySelector('input[name="exam_id"]').value;
    const res = await fetch("{{ route('exam.check-similarity') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": '{{ csrf_token() }}',
      },
      body: JSON.stringify({
        question: content,
        exam_id: examId,
      }),
    });
    const data = await res.json();
    let msg = '';
    if (data.best_match) {
      msg = `Most similar past question (${data.score}% match):\n\n"${data.best_match}"\n\n`;
      if (data.exam) {
        msg += `Course: ${data.exam.course_name}\nSection: ${data.exam.section}\nSemester: ${data.exam.semester}`;
      }
    } else {
      msg = "No similar question found in past exams.";
    }
    document.getElementById('aiModalBody').innerText = msg;
    new bootstrap.Modal(document.getElementById('aiModal')).show();
  } catch (err) {
    alert("❌ Error: " + err.message);
  }
  button.innerText = 'Check Similarity';
  button.disabled = false;
}

function copyAISuggestion() {
  navigator.clipboard.writeText(lastAISuggestion).then(() => alert('Copied to clipboard.'));
}

function insertAISuggestion() {
  if (lastEditorId && tinymce.get(lastEditorId)) {
    tinymce.get(lastEditorId).setContent(lastAISuggestion);
    bootstrap.Modal.getInstance(document.getElementById('aiModal')).hide();
  } else {
    alert('Editor not found.');
  }
}

function addSubQuestion(questionIndex) {
  const subQuestionsContainer = document.getElementById(`sub-questions-${questionIndex}`);
  const subIndex = subQuestionsContainer.querySelectorAll('.sub-question-block').length;
  const subLabel = String.fromCharCode(97 + subIndex);
  const uniqueId = `sub-q-${questionIndex}-${subLabel}-${Date.now()}`;

  const subBlock = document.createElement('div');
  subBlock.className = 'sub-question-block mb-2 input-group align-items-start';
  subBlock.innerHTML = `
    <span class="input-group-text">${subLabel})</span>
    <div class="flex-grow-1 me-2">
      <textarea class="form-control tinymce mb-1" id="${uniqueId}"
        name="questions[${questionIndex}][sub_questions][${subLabel}][question]"
        rows="2" placeholder="Sub-question"></textarea>
      <div class="dropdown my-1">
        <button class="btn btn-sm btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          SEMS AI
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item ask-ai-btn" href="#" data-target="${uniqueId}">Ask AI for Suggestion</a></li>
          <li><a class="dropdown-item clarify-ai-btn" href="#" data-target="${uniqueId}">Improve Clarity</a></li>
          <li><a class="dropdown-item answer-ai-btn" href="#" data-target="${uniqueId}">Generate Answer</a></li>
          <li><a class="dropdown-item similarity-ai-btn" href="#" data-target="${uniqueId}">Check Similarity</a></li>
        </ul>
      </div>
      <input type="number" class="form-control mb-1"
        name="questions[${questionIndex}][sub_questions][${subLabel}][mark]"
        placeholder="Mark" min="0" style="max-width: 120px;">
      <textarea class="form-control tinymce"
        name="questions[${questionIndex}][sub_questions][${subLabel}][answer]"
        rows="2" placeholder="Answer"></textarea>
    </div>
    <button type="button" class="btn btn-danger align-middle-self-stretch" onclick="removeSubQuestion(this)">Remove</button>
  `;

  subQuestionsContainer.appendChild(subBlock);
  initAllTinyMCE();
}

function removeSubQuestion(button) {
  const block = button.closest('.sub-question-block');
  if (block) block.remove();
}

document.addEventListener('DOMContentLoaded', function () {
  initAllTinyMCE();
  attachAIListeners();
});
</script>

<!-- AI Modal -->
<div class="modal fade" id="aiModal" tabindex="-1" aria-labelledby="aiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="aiModalLabel">AI Suggestion</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="aiModalBody" style="white-space: pre-wrap; font-size: 14px; max-height: 400px; overflow-y: auto;">
        Loading...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" onclick="copyAISuggestion()">📋 Copy</button>
        <button type="button" class="btn btn-success" onclick="insertAISuggestion()">⬇ Insert into Editor</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endpush

