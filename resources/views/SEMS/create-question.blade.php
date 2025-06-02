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
        $tos = is_array($exam->tos) ? $exam->tos : json_decode($exam->tos, true) ?? [];
    @endphp

    <form action="{{ route('exam.submit-question') }}" method="POST">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">

        {{-- Exam Info --}}
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
                                <label class="form-label">Course Name</label>
                                <input type="text" class="form-control" value="{{ $exam->course_name }}" disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Course Code</label>
                                <input type="text" class="form-control" value="{{ $exam->course_code }}" disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Section</label>
                                <input type="text" class="form-control" value="{{ $exam->section }}" disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Coordinator</label>
                                <input type="text" class="form-control"
                                    value="{{ $exam->ccAssignment && $exam->ccAssignment->user ? $exam->ccAssignment->user->name : 'N/A' }}"
                                    disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vetters</label>
                                <input type="text" class="form-control"
                                    value="{{ $exam->vetterAssignments->count()
                                        ? $exam->vetterAssignments->pluck('user.name')->join(', ')
                                        : 'No vetters assigned.' }}"
                                    disabled readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Semester</label>
                                <input type="text" class="form-control" value="{{ $exam->semester->name ?? 'N/A' }}" disabled readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOS Table --}}
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

{{-- Dynamic Question List --}}
<div id="question-container">
@if (!empty($questions))
    @foreach ($questions as $qIdx => $question)
        @php
            $mainQId = "question-main-{$qIdx}-" . \Illuminate\Support\Str::random(6);
        @endphp
        <div class="card mb-4 question-block">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Question {{ $qIdx + 1 }}</h5>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion(this)">Remove</button>
            </div>
            <div class="card-body">
                <div class="form-group row mb-2 align-items-center">
                    <label class="col-form-label col-md-2">Main Question</label>
                    <div class="col-md-10">
                        <textarea class="form-control tinymce mb-1"
                            name="questions[{{ $qIdx }}][question]"
                            id="{{ $mainQId }}"
                            style="min-height: 64px">{!! $question['question'] ?? '' !!}</textarea>
                        <input type="number"
                            name="questions[{{ $qIdx }}][mark]"
                            placeholder="Mark"
                            value="{{ $question['mark'] ?? '' }}"
                            class="form-control form-control-sm mt-2"
                            style="max-width: 120px;">
                        <div class="d-flex flex-row align-items-center gap-2 mt-1">
                            {{-- AI dropdown and Bloom fields --}}
                            <div class="btn-group btn-group-sm" role="group" style="margin-left: 8px;">
                                <button id="btnGroupDrop{{ $mainQId }}" type="button" class="btn btn-outline-dark btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    SEMS AI
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnGroupDrop{{ $mainQId }}">
                                    <li><a class="dropdown-item ask-ai-btn" href="#" data-target="{{ $mainQId }}">Ask AI for Suggestion</a></li>
                                    <li><a class="dropdown-item clarify-ai-btn" href="#" data-target="{{ $mainQId }}">Improve Clarity</a></li>
                                    <li><a class="dropdown-item answer-ai-btn" href="#" data-target="{{ $mainQId }}">Generate Answer</a></li>
                                    <li><a class="dropdown-item similarity-ai-btn" href="#" data-target="{{ $mainQId }}">Check Similarity</a></li>
                                </ul>
                            </div>
                            <div class="d-flex gap-2 align-items-center bloom-taxonomy-group" style="margin-left:4px;">
                                <input type="text" class="form-control form-control-sm bloom-topic"
                                    placeholder="Topic"
                                    style="max-width:120px;"
                                    value="{{ $question['bloom_topic'] ?? '' }}">
                                <select class="form-select form-select-sm bloom-domain"
                                    style="max-width:90px;" onchange="updateBloomLevels(this)">
                                    <option value="">Domain</option>
                                    <option value="Cognitive" {{ ($question['bloom_domain'] ?? '') == 'Cognitive' ? 'selected' : '' }}>Cognitive</option>
                                    <option value="Affective" {{ ($question['bloom_domain'] ?? '') == 'Affective' ? 'selected' : '' }}>Affective</option>
                                    <option value="Psychomotor" {{ ($question['bloom_domain'] ?? '') == 'Psychomotor' ? 'selected' : '' }}>Psychomotor</option>
                                </select>
                                <select class="form-select form-select-sm bloom-level" style="max-width:100px;">
                                    <option value="">Level</option>
                                    {{-- Fill by JS --}}
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-primary" title="AI Suggestion"
                                    onclick="generateBloomsQuestion(this, '{{ $mainQId }}')">
                                    <i class="fas fa-magic"></i>
                                </button>
                            </div>
                        </div>
                        {{-- Vetter Comments --}}
                        @if (!empty($vetterComments[$qIdx]))
                            <div class="mt-2 border rounded p-2 bg-light">
                                <strong>Vetter Comments:</strong>
                                <ul>
                                    @foreach ($vetterComments[$qIdx] as $comment)
                                        <li>
                                            <span class="text-muted">{{ $comment['name'] ?? 'Vetter' }} ({{ $comment['timestamp'] ?? '' }}):</span>
                                            <br>
                                            {{ $comment['comment'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
                {{-- Sub-Questions --}}
                <div class="mt-3">
                    <h6 class="mb-2" style="font-size:1rem;">Sub-Questions</h6>
                    <div id="sub-questions-{{ $qIdx }}">
                        @if (!empty($question['sub_questions']))
                            @foreach ($question['sub_questions'] as $subLabel => $subQ)
                                @php
                                    $uniqueId = "sub-{$qIdx}-{$subLabel}-{$loop->index}";
                                    $qEditorId = "{$uniqueId}-q";
                                @endphp
                                <div class="card border mt-3 sub-question-block">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <label><strong>{{ $subLabel }})</strong></label>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeSubQuestion(this)">Remove Sub-question</button>
                                        </div>
                                        <textarea class="form-control tinymce mb-2"
                                            name="questions[{{ $qIdx }}][sub_questions][{{ $subLabel }}][question]"
                                            id="{{ $qEditorId }}">{{ $subQ['question'] ?? '' }}</textarea>
<div class="d-flex flex-wrap align-items-center gap-2 mt-1 bloom-taxonomy-group">
    <input type="text" class="form-control form-control-sm bloom-topic"
        placeholder="Topic"
        style="max-width:120px;"
        value="{{ $subQ['bloom_topic'] ?? '' }}">
    <select class="form-select form-select-sm bloom-domain"
        style="max-width:110px;" onchange="updateBloomLevels(this)">
        <option value="">Domain</option>
        <option value="Cognitive" {{ ($subQ['bloom_domain'] ?? '') == 'Cognitive' ? 'selected' : '' }}>Cognitive</option>
        <option value="Affective" {{ ($subQ['bloom_domain'] ?? '') == 'Affective' ? 'selected' : '' }}>Affective</option>
        <option value="Psychomotor" {{ ($subQ['bloom_domain'] ?? '') == 'Psychomotor' ? 'selected' : '' }}>Psychomotor</option>
    </select>
    <select class="form-select form-select-sm bloom-level" style="max-width:110px;">
        <option value="">Level</option>
    </select>
    <button type="button" class="btn btn-sm btn-outline-primary" title="AI Suggestion"
        onclick="generateBloomsQuestion(this, '{{ $qEditorId }}')">
        <i class="fas fa-magic"></i>
    </button>
    <div class="btn-group btn-group-sm" role="group" style="margin-left: 8px;">
        <button id="btnGroupDrop{{ $qEditorId }}" type="button" class="btn btn-outline-dark btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            SEMS AI
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnGroupDrop{{ $qEditorId }}">
            <li><a class="dropdown-item ask-ai-btn" href="#" data-target="{{ $qEditorId }}">Ask AI for Suggestion</a></li>
            <li><a class="dropdown-item clarify-ai-btn" href="#" data-target="{{ $qEditorId }}">Improve Clarity</a></li>
            <li><a class="dropdown-item answer-ai-btn" href="#" data-target="{{ $qEditorId }}">Generate Answer</a></li>
            <li><a class="dropdown-item similarity-ai-btn" href="#" data-target="{{ $qEditorId }}">Check Similarity</a></li>
        </ul>
    </div>
</div>

                                        <input type="number"
                                            name="questions[{{ $qIdx }}][sub_questions][{{ $subLabel }}][mark]"
                                            value="{{ $subQ['mark'] ?? '' }}"
                                            placeholder="Mark"
                                            class="form-control mb-2"
                                            style="max-width: 120px;">
                                        <textarea class="form-control tinymce mb-3"
                                            name="questions[{{ $qIdx }}][sub_questions][{{ $subLabel }}][answer]">{{ $subQ['answer'] ?? '' }}</textarea>

                                        {{-- Breakdown Section --}}
                                        @if (!empty($subQ['breakdowns']))
                                            @foreach ($subQ['breakdowns'] as $breakdownLabel => $breakdown)
                                                @php
                                                    $breakdownEditorId = "break-{$qIdx}-{$subLabel}-{$breakdownLabel}-{$loop->index}-q";
                                                @endphp
                                                <div class="mb-2 ms-3 border p-2 rounded breakdown-block">
                                                    <div class="d-flex justify-content-between">
                                                        <label><em>{{ $breakdownLabel }})</em></label>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBreakdown(this)">Remove Breakdown</button>
                                                    </div>
                                                    <textarea class="form-control tinymce mb-1"
                                                        id="{{ $breakdownEditorId }}"
                                                        name="questions[{{ $qIdx }}][sub_questions][{{ $subLabel }}][breakdowns][{{ $breakdownLabel }}][question]">{{ $breakdown['question'] ?? '' }}</textarea>
<div class="d-flex flex-wrap align-items-center gap-2 mt-1 bloom-taxonomy-group">
    <input type="text"
        class="form-control form-control-sm bloom-topic"
        placeholder="Topic"
        style="max-width:120px;"
        value="{{ $breakdown['bloom_topic'] ?? '' }}">
    <select class="form-select form-select-sm bloom-domain"
        style="max-width:110px;"
        onchange="updateBloomLevels(this)">
        <option value="">Domain</option>
        <option value="Cognitive" {{ ($breakdown['bloom_domain'] ?? '') == 'Cognitive' ? 'selected' : '' }}>Cognitive</option>
        <option value="Affective" {{ ($breakdown['bloom_domain'] ?? '') == 'Affective' ? 'selected' : '' }}>Affective</option>
        <option value="Psychomotor" {{ ($breakdown['bloom_domain'] ?? '') == 'Psychomotor' ? 'selected' : '' }}>Psychomotor</option>
    </select>
    <select class="form-select form-select-sm bloom-level" style="max-width:110px;">
        <option value="">Level</option>
        {{-- Let JS populate --}}
    </select>
    <button type="button" class="btn btn-sm btn-outline-primary" title="AI Suggestion"
        onclick="generateBloomsQuestion(this, '{{ $breakdownEditorId }}')">
        <i class="fas fa-magic"></i>
    </button>
    <div class="btn-group btn-group-sm" role="group" style="margin-left: 8px;">
        <button id="btnGroupDrop{{ $breakdownEditorId }}" type="button" class="btn btn-outline-dark btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            SEMS AI
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnGroupDrop{{ $breakdownEditorId }}">
            <li><a class="dropdown-item ask-ai-btn" href="#" data-target="{{ $breakdownEditorId }}">Ask AI for Suggestion</a></li>
            <li><a class="dropdown-item clarify-ai-btn" href="#" data-target="{{ $breakdownEditorId }}">Improve Clarity</a></li>
            <li><a class="dropdown-item answer-ai-btn" href="#" data-target="{{ $breakdownEditorId }}">Generate Answer</a></li>
            <li><a class="dropdown-item similarity-ai-btn" href="#" data-target="{{ $breakdownEditorId }}">Check Similarity</a></li>
        </ul>
    </div>
</div>

                                                    <input type="number"
                                                        class="form-control mb-1"
                                                        name="questions[{{ $qIdx }}][sub_questions][{{ $subLabel }}][breakdowns][{{ $breakdownLabel }}][mark]"
                                                        value="{{ $breakdown['mark'] ?? '' }}"
                                                        placeholder="Mark" min="0" style="max-width: 120px;">
                                                    <textarea class="form-control tinymce"
                                                        name="questions[{{ $qIdx }}][sub_questions][{{ $subLabel }}][breakdowns][{{ $breakdownLabel }}][answer]">{{ $breakdown['answer'] ?? '' }}</textarea>
                                                </div>
                                            @endforeach
                                        @endif

                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addBreakdown({{ $qIdx }}, '{{ $subLabel }}')">+ Add Breakdown</button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addSubQuestion({{ $qIdx }})">+ Add Sub-question</button>
                </div>
            </div>
        </div>
    @endforeach
@endif
</div>

<div class="text-center">
    <button type="button" class="btn btn-success mb-4" onclick="addQuestion()">+ Add Question</button>
</div>

{{-- Action Buttons --}}
<div class="text-center mb-5">
    <button type="submit" class="btn btn-primary">Send to Department</button>
    <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">Save as Draft</button>
</div>

</form>

<!-- AI Modal -->
<div class="modal fade" id="aiModal" tabindex="-1" aria-labelledby="aiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="aiModalLabel">SEMS AI Assistant</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="aiModalBody">
        <!-- AI response will be injected here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-success" onclick="copyAISuggestion()">Copy</button>
        <button type="button" class="btn btn-primary" onclick="insertAISuggestion()">Insert</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/d6b2sr6wvk401h8i55fuufj8wlc5pouxeasair9hg4a8zwfy/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<script>
let lastEditorId = null;
let lastAISuggestion = '';

/** Initialize TinyMCE on all editors */
function initAllTinyMCE() {
  document.querySelectorAll('textarea.tinymce').forEach((el) => {
    if (tinymce.get(el.id)) tinymce.get(el.id).remove();
  });
  tinymce.init({
    selector: 'textarea.tinymce',
 plugins: [
  'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace',
  'table', 'visualblocks', 'wordcount'
],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: @json(Auth::user()->name ?? "Author"),
    mergetags_list: [{ value: 'First.Name', title: 'First Name' }, { value: 'Email', title: 'Email' }],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
  });
}


function attachAIListeners() {
  document.body.addEventListener('click', function (e) {
    if (e.target.classList.contains('ask-ai-btn')) {
      e.preventDefault(); runAI(e.target, 'question');
    }
    if (e.target.classList.contains('clarify-ai-btn')) {
      e.preventDefault(); runAI(e.target, 'clarify');
    }
    if (e.target.classList.contains('answer-ai-btn')) {
      e.preventDefault(); runAI(e.target, 'answer');
    }
    if (e.target.classList.contains('similarity-ai-btn')) {
      e.preventDefault(); runSimilarityCheck(e.target);
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
  if (type === 'clarify') prompt = `Rewrite the following exam question using clear, formal, academic English:\n\n"${content}"`;
  else if (type === 'answer') prompt = `Provide a clear and concise model answer for the following exam question:\n\n"${content}"`;
  button.innerText = 'Processing...'; button.disabled = true;
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
  button.innerText = 'Checking...'; button.disabled = true;
  try {
    const examId = document.querySelector('input[name="exam_id"]').value;
    const res = await fetch(@json(route('exam.check-similarity')), {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": @json(csrf_token()), },
      body: JSON.stringify({ question: content, exam_id: examId }),
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

function renderAIDropdown(editorId) {
  return `
    <div class="btn-group btn-group-sm" role="group" style="margin-left: 8px;">
      <button id="btnGroupDrop${editorId}" type="button" class="btn btn-outline-dark btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        SEMS AI
      </button>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnGroupDrop${editorId}">
        <li><a class="dropdown-item ask-ai-btn" href="#" data-target="${editorId}">Ask AI for Suggestion</a></li>
        <li><a class="dropdown-item clarify-ai-btn" href="#" data-target="${editorId}">Improve Clarity</a></li>
        <li><a class="dropdown-item answer-ai-btn" href="#" data-target="${editorId}">Generate Answer</a></li>
        <li><a class="dropdown-item similarity-ai-btn" href="#" data-target="${editorId}">Check Similarity</a></li>
      </ul>
    </div>`;
}


// -- Dynamic Card Adders --

function addQuestion() {
  const questionCount = document.querySelectorAll('.question-block').length;
  const container = document.getElementById('question-container');
  const qIdx = questionCount;
  const mainQId = `question-main-${qIdx}-${Date.now()}`;
  const block = document.createElement('div');
  block.className = 'card mb-4 question-block';
block.innerHTML = `
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title mb-0">Question ${qIdx + 1}</h5>
    <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion(this)">Remove</button>
  </div>
  <div class="card-body">
    <div class="form-group row mb-2 align-items-center">
      <label class="col-form-label col-md-2">Main Question</label>
      <div class="col-md-10">
        <textarea class="form-control tinymce mb-1" name="questions[${qIdx}][question]" id="${mainQId}" style="min-height: 64px"></textarea>
        <input type="number" name="questions[${qIdx}][mark]" placeholder="Mark" class="form-control form-control-sm mt-2" style="max-width: 120px;">
        <div class="d-flex flex-row align-items-center gap-2 mt-1">
          ${renderAIDropdown(mainQId)}
          <div class="d-flex gap-2 align-items-center bloom-taxonomy-group" style="margin-left:4px;">
            <input type="text" class="form-control form-control-sm bloom-topic" placeholder="Topic" style="max-width:120px;">
            <select class="form-select form-select-sm bloom-domain" style="max-width:90px;" onchange="updateBloomLevels(this)">
              <option value="">Domain</option>
              <option value="Cognitive">Cognitive</option>
              <option value="Affective">Affective</option>
              <option value="Psychomotor">Psychomotor</option>
            </select>
            <select class="form-select form-select-sm bloom-level" style="max-width:100px;">
              <option value="">Level</option>
            </select>
            <button type="button" class="btn btn-sm btn-outline-primary" title="AI Suggestion" onclick="generateBloomsQuestion(this, ${qIdx})">
              <i class="fas fa-magic"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-3">
      <h6 class="mb-2" style="font-size:1rem;">Sub-Questions</h6>
      <div id="sub-questions-${qIdx}"></div>
      <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addSubQuestion(${qIdx})">+ Add Sub-question</button>
    </div>
  </div>
`;
  container.appendChild(block);
  initAllTinyMCE();
}

function addSubQuestion(questionIndex) {
  const container = document.getElementById(`sub-questions-${questionIndex}`);
  if (!container) return;
  const count = container.querySelectorAll('.sub-question-block').length;
  const subLabel = String.fromCharCode(97 + count); // a, b, c, d...
  const timestamp = Date.now();
  const uniqueId = `sub-${questionIndex}-${subLabel}-${timestamp}`;
  const qEditorId = `${uniqueId}-q`;
  const block = document.createElement('div');
  block.className = 'card border mt-3 sub-question-block';
block.innerHTML = `
  <div class="card-body">
    <div class="d-flex justify-content-between">
      <label><strong>${subLabel})</strong></label>
      <button type="button" class="btn btn-sm btn-danger" onclick="removeSubQuestion(this)">Remove Sub-question</button>
    </div>
    <textarea class="form-control tinymce mb-2" name="questions[${questionIndex}][sub_questions][${subLabel}][question]" id="${qEditorId}"></textarea>
<div class="d-flex flex-wrap align-items-center gap-2 mt-1 bloom-taxonomy-group">
      <input type="text" class="form-control form-control-sm bloom-topic" placeholder="Topic" style="max-width:120px;">
      <select class="form-select form-select-sm bloom-domain" style="max-width:110px;" onchange="updateBloomLevels(this)">
        <option value="">Domain</option>
        <option value="Cognitive">Cognitive</option>
        <option value="Affective">Affective</option>
        <option value="Psychomotor">Psychomotor</option>
      </select>
      <select class="form-select form-select-sm bloom-level" style="max-width:110px;">
        <option value="">Level</option>
      </select>
      <button type="button" class="btn btn-sm btn-outline-primary" title="AI Suggestion" onclick="generateBloomsQuestion(this, '${qEditorId}')">
        <i class="fas fa-magic"></i>
      </button>
      ${renderAIDropdown(qEditorId)}
    </div>
    <input type="number" name="questions[${questionIndex}][sub_questions][${subLabel}][mark]" placeholder="Mark" class="form-control mb-2" style="max-width: 120px">
    <textarea class="form-control tinymce mb-3" name="questions[${questionIndex}][sub_questions][${subLabel}][answer]"></textarea>
    <h6 class="text-muted">Breakdowns:</h6>
    <div id="breakdowns-${questionIndex}-${subLabel}"></div>
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addBreakdown(${questionIndex}, '${subLabel}')">+ Add Breakdown</button>
  </div>
`;
  container.appendChild(block);
  initAllTinyMCE();
}

function addBreakdown(questionIndex, subKey) {
  const containerId = `breakdowns-${questionIndex}-${subKey}`;
  const container = document.getElementById(containerId);
  if (!container) return;
  const count = container.querySelectorAll('.breakdown-block').length;
  const breakdownLabel = ['i', 'ii', 'iii', 'iv'][count] || `x${count + 1}`;
  const timestamp = Date.now();
  const uniqueId = `break-${questionIndex}-${subKey}-${breakdownLabel}-${timestamp}`;
  const qEditorId = `${uniqueId}-q`;
  const block = document.createElement('div');
  block.className = 'mb-2 ms-3 border p-2 rounded breakdown-block';
block.innerHTML = `
  <div class="d-flex justify-content-between">
    <label><em>${breakdownLabel})</em></label>
    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBreakdown(this)">Remove Breakdown</button>
  </div>
  <textarea class="form-control tinymce mb-1" id="${qEditorId}" name="questions[${questionIndex}][sub_questions][${subKey}][breakdowns][${breakdownLabel}][question]"></textarea>
 <div class="d-flex flex-wrap align-items-center gap-2 mt-1 bloom-taxonomy-group">
    <input type="text" class="form-control form-control-sm bloom-topic" placeholder="Topic" style="max-width:120px;">
    <select class="form-select form-select-sm bloom-domain" style="max-width:110px;" onchange="updateBloomLevels(this)">
      <option value="">Domain</option>
      <option value="Cognitive">Cognitive</option>
      <option value="Affective">Affective</option>
      <option value="Psychomotor">Psychomotor</option>
    </select>
    <select class="form-select form-select-sm bloom-level" style="max-width:110px;">
      <option value="">Level</option>
    </select>
    <button type="button" class="btn btn-sm btn-outline-primary" title="AI Suggestion" onclick="generateBloomsQuestion(this, '${qEditorId}')">
      <i class="fas fa-magic"></i>
    </button>
    ${renderAIDropdown(qEditorId)}
  </div>
  <input type="number" class="form-control mb-1" name="questions[${questionIndex}][sub_questions][${subKey}][breakdowns][${breakdownLabel}][mark]" placeholder="Mark" min="0" style="max-width: 120px;">
  <textarea class="form-control tinymce" name="questions[${questionIndex}][sub_questions][${subKey}][breakdowns][${breakdownLabel}][answer]"></textarea>
`;
  container.appendChild(block);
  initAllTinyMCE();
}

function removeQuestion(button) {
  const block = button.closest('.question-block');
  if (block) block.remove();
}
function removeSubQuestion(button) {
  const subCard = button.closest('.sub-question-block');
  if (subCard) subCard.remove();
}
function removeBreakdown(button) {
  const breakdownBlock = button.closest('.breakdown-block');
  if (breakdownBlock) breakdownBlock.remove();
}

document.addEventListener('DOMContentLoaded', function () {
  initAllTinyMCE();
  attachAIListeners();
});

const bloomLevels = {
  Cognitive: [
    { code: "C1", label: "Remember" },
    { code: "C2", label: "Understand" },
    { code: "C3", label: "Apply" },
    { code: "C4", label: "Analyse" },
    { code: "C5", label: "Evaluate" },
    { code: "C6", label: "Create" }
  ],
  Affective: [
    { code: "A1", label: "Receiving Phenomena" },
    { code: "A2", label: "Responding to Phenomena" },
    { code: "A3", label: "Valuing" },
    { code: "A4", label: "Organising Values" },
    { code: "A5", label: "Internalising Values" }
  ],
  Psychomotor: [
    { code: "P1", label: "Perception" },
    { code: "P2", label: "Set" },
    { code: "P3", label: "Guided Response" },
    { code: "P4", label: "Mechanism" },
    { code: "P5", label: "Complex Overt Response" },
    { code: "P6", label: "Adaption" },
    { code: "P7", label: "Origination" }
  ]
};

function updateBloomLevels(domainSelect) {
  const domain = domainSelect.value;
  const group = domainSelect.closest('.bloom-taxonomy-group');
  const levelSelect = group.querySelector('.bloom-level');
  let options = '<option value="">Level</option>';
  if (bloomLevels[domain]) {
    bloomLevels[domain].forEach(level => {
      options += `<option value="${level.code} - ${level.label}">${level.code} - ${level.label}</option>`;
    });
  }
  levelSelect.innerHTML = options;
}

function generateBloomsQuestion(button, editorId) {
  const group = button.closest('.bloom-taxonomy-group');
  const topic = group.querySelector('.bloom-topic').value.trim();
  const domain = group.querySelector('.bloom-domain').value;
  const level = group.querySelector('.bloom-level').value;

  if (!topic || !domain || !level) {
    alert('Please fill in the topic, domain, and level.');
    return;
  }

  const [levelCode, levelName] = level.split(' - ');
  const prompt = `Generate an exam question on the topic of "${topic}" at the ${levelCode} (${levelName}) level in the ${domain} domain of Bloom's Taxonomy.`;

  const editor = tinymce.get(editorId);
  if (!editor) {
    alert('Editor not found.');
    return;
  }

  button.innerHTML = `<span class="spinner-border spinner-border-sm"></span>`;

  fetch('http://127.0.0.1:11434/api/generate', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      model: 'llama3',
      prompt: prompt,
      stream: false
    })
  })
  .then(response => response.json())
  .then(data => {
    // Set for modal
    lastEditorId = editorId;
    lastAISuggestion = data.response || '';
    document.getElementById('aiModalBody').innerText = data.response || '';
    // Show the modal
    new bootstrap.Modal(document.getElementById('aiModal')).show();
    button.innerHTML = `<i class="fas fa-magic"></i>`;
  })
  .catch(() => {
    alert('AI request failed');
    button.innerHTML = `<i class="fas fa-magic"></i>`;
  });
}


document.addEventListener('DOMContentLoaded', function () {
  initAllTinyMCE();
  attachAIListeners();

  // Populate Bloom Level dropdowns for all pre-filled domains
  document.querySelectorAll('.bloom-domain').forEach(function(domainSelect){
      if (domainSelect.value) updateBloomLevels(domainSelect);
  });
});


</script>
@endpush
