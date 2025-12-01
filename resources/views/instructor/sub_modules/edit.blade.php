@extends('layouts.instructor')

@section('title','Edit Sub-Module')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $subModule->module->course->id) }}">{{ $subModule->module->course->judul }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.modules.show', $subModule->module) }}">{{ $subModule->module->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sub-Module Edit</li>
  </ol>
  {{-- Binding: $subModule --}}
</nav>
@endsection

@section('content')
@php
  $subModule->load(['contents', 'quizzes.questions.answerOptions']);
@endphp
<div class="container-fluid">
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Sub-Module Edit Wizard</h5>
      <small>Edit sub-module lengkap dengan contents dalam satu alur</small>
    </div>
    <div class="card-body">
      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Error!</strong> {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      
      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Validation Errors:</strong>
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      
      <form action="{{ route('instructor.sub_modules.update', $subModule->id) }}" method="post" enctype="multipart/form-data" id="subModuleWizardForm">
        @csrf
        @method('PUT')
        
        <!-- Step 1: Sub-Module Information -->
        <div class="wizard-step" id="step1">
          <h4 class="mb-4">Step 1: Sub-Module Information</h4>
          
          <div class="mb-3">
            <label class="form-label">Judul Sub-Module <span class="text-danger">*</span></label>
            <input type="text" name="judul" value="{{ old('judul', $subModule->judul) }}" class="form-control" required>
            @error('judul')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $subModule->deskripsi) }}</textarea>
            @error('deskripsi')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Urutan <span class="text-danger">*</span></label>
            <input type="number" name="urutan" value="{{ old('urutan', $subModule->urutan) }}" class="form-control" min="1" required>
            @error('urutan')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="d-flex justify-content-end mt-4">
            <button type="button" class="btn btn-primary" onclick="nextStep(2)">Next: Manage Contents →</button>
          </div>
        </div>

        <!-- Step 2: Contents -->
        <div class="wizard-step" id="step2" style="display: none;">
          <h4 class="mb-4">Step 2: Contents</h4>
          <p class="text-muted mb-4">Kelola contents untuk sub-module ini.</p>
          
          <div id="contentsContainer">
            <!-- Existing and new contents will be added here dynamically -->
          </div>

          <button type="button" class="btn btn-success mb-4" onclick="addContent()">
            <i class="bi bi-plus-circle"></i> Tambah Content Baru
          </button>

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="prevStep(1)">← Back</button>
            <button type="button" class="btn btn-primary" onclick="nextStep(3)">Next: Manage Quizzes →</button>
          </div>
        </div>

        <!-- Step 3: Quizzes -->
        <div class="wizard-step" id="step3" style="display: none;">
          <h4 class="mb-4">Step 3: Quizzes</h4>
          <p class="text-muted mb-4">Kelola quizzes untuk sub-module ini.</p>
          
          <div id="quizzesContainer">
            <!-- Existing and new quizzes will be added here dynamically -->
          </div>

          <button type="button" class="btn btn-success mb-4" onclick="addQuiz()">
            <i class="bi bi-plus-circle"></i> Tambah Quiz Baru
          </button>

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="prevStep(2)">← Back</button>
            <button type="button" class="btn btn-primary" onclick="nextStep(4)">Next: Review →</button>
          </div>
        </div>

        <!-- Step 4: Review and Submit -->
        <div class="wizard-step" id="step4" style="display: none;">
          <h4 class="mb-4">Step 4: Review and Submit</h4>
          <p class="text-muted mb-4">Tinjau informasi sub-module Anda sebelum menyimpan.</p>
          
          <div class="card mb-3">
            <div class="card-header">Sub-Module Information</div>
            <div class="card-body">
              <p><strong>Judul:</strong> <span id="reviewJudul"></span></p>
              <p><strong>Deskripsi:</strong> <span id="reviewDeskripsi"></span></p>
              <p><strong>Urutan:</strong> <span id="reviewUrutan"></span></p>
            </div>
          </div>

          <div class="card mb-3">
            <div class="card-header">Contents Summary</div>
            <div class="card-body" id="reviewContents">
              <!-- Contents summary will be shown here -->
            </div>
          </div>

          <div class="card mb-3">
            <div class="card-header">Quizzes Summary</div>
            <div class="card-body" id="reviewQuizzes">
              <!-- Quizzes summary will be shown here -->
            </div>
          </div>

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="prevStep(3)">← Back</button>
            <button type="submit" class="btn btn-success" id="submitBtn" onclick="return handleFormSubmit(event);">
              <i class="bi bi-check-circle"></i> Update Sub-Module
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Content Template (Hidden) -->
<template id="contentTemplate">
  <div class="card mb-3 content-item" data-content-index="" data-content-id="">
    <div class="card-header bg-warning d-flex justify-content-between align-items-center">
      <span>Content <span class="content-number"></span> <span class="content-status-badge"></span></span>
      <button type="button" class="btn btn-sm btn-danger" onclick="removeContent(this)">
        <i class="bi bi-trash"></i> Hapus
      </button>
    </div>
    <div class="card-body">
      <input type="hidden" name="contents[][id]" class="content-id" value="">
      <div class="mb-3">
        <label class="form-label">Judul Content <span class="text-danger">*</span></label>
        <input type="text" name="contents[][judul]" class="form-control content-judul" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Tipe Content <span class="text-danger">*</span></label>
        <select name="contents[][tipe]" class="form-control content-tipe" required onchange="toggleContentFields(this)">
          <option value="">Pilih Tipe</option>
          <option value="text">Text (Plain Text)</option>
          <option value="html">HTML (Rich Text)</option>
          <option value="video">Video</option>
          <option value="youtube">YouTube Video</option>
          <option value="audio">Audio</option>
          <option value="pdf">PDF File</option>
          <option value="image">Image</option>
          <option value="link">External Link (PDF/File)</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Urutan <span class="text-danger">*</span></label>
        <input type="number" name="contents[][urutan]" class="form-control content-urutan" min="1" required>
      </div>
      
      <!-- HTML Content Field -->
      <div class="mb-3 content-html-field" style="display: none;">
        <label class="form-label">HTML Content <span class="text-danger">*</span></label>
        <textarea name="contents[][html_content]" class="form-control" rows="5"></textarea>
      </div>
      
      <!-- File Upload Field -->
      <div class="mb-3 content-file-field" style="display: none;">
        <div class="current-file-info" style="display: none;">
          <label class="form-label">File Saat Ini</label>
          <div class="mb-2">
            <a href="" class="current-file-link" target="_blank"></a>
          </div>
        </div>
        <label class="form-label">Upload File Baru (opsional)</label>
        <input type="file" name="contents[][file_path]" class="form-control content-file">
        <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
      </div>
      
      <!-- External URL Field -->
      <div class="mb-3 content-url-field" style="display: none;">
        <label class="form-label">External URL <span class="text-danger">*</span></label>
        <input type="url" name="contents[][external_url]" class="form-control" placeholder="https://example.com/file.pdf">
      </div>
      
      <!-- YouTube URL Field -->
      <div class="mb-3 content-youtube-field" style="display: none;">
        <label class="form-label">YouTube URL <span class="text-danger">*</span></label>
        <input type="url" name="contents[][youtube_url]" class="form-control" placeholder="https://www.youtube.com/watch?v=VIDEO_ID atau https://youtu.be/VIDEO_ID">
      </div>
      
      <!-- Required Duration Field (for YouTube videos) -->
      <div class="mb-3 content-required-duration-field" style="display: none;">
        <label class="form-label">Durasi Video yang Diperlukan (detik) <span class="text-danger">*</span></label>
        <input type="number" name="contents[][required_duration]" class="form-control" min="1" placeholder="Contoh: 300 (untuk 5 menit)">
        <small class="text-muted">Masukkan durasi video dalam detik. Siswa harus menonton video selama durasi ini sebelum dapat melanjutkan ke konten berikutnya.</small>
      </div>
    </div>
  </div>
</template>

<!-- Quiz Template (Hidden) -->
<template id="quizTemplate">
  <div class="card mb-3 quiz-item" data-quiz-index="" data-quiz-id="">
    <div class="card-header bg-info d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center">
        <button type="button" class="btn btn-sm btn-link text-white p-0 me-2" onclick="toggleQuizCollapse(this)" style="text-decoration: none;">
          <i class="bi bi-chevron-down quiz-toggle-icon"></i>
        </button>
        <span>Quiz <span class="quiz-number"></span> <span class="quiz-status-badge"></span></span>
      </div>
      <div>
        <button type="button" class="btn btn-sm btn-danger" onclick="removeQuiz(this)" title="Hapus Quiz">
          <i class="bi bi-trash"></i> Hapus
        </button>
      </div>
    </div>
    <div class="card-body quiz-body">
      <input type="hidden" name="quizzes[][id]" class="quiz-id" value="">
      <div class="mb-3">
        <label class="form-label">Judul Quiz <span class="text-danger">*</span></label>
        <input type="text" name="quizzes[][judul]" class="form-control quiz-judul" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="quizzes[][deskripsi]" class="form-control quiz-deskripsi" rows="2"></textarea>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Nilai Minimum <span class="text-danger">*</span></label>
          <input type="number" name="quizzes[][nilai_minimum]" class="form-control quiz-nilai-minimum" min="0" max="100" step="0.01" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Maks Attempts <span class="text-danger">*</span></label>
          <input type="number" name="quizzes[][max_attempts]" class="form-control quiz-max-attempts" min="1" value="3" required>
        </div>
      </div>
      
      <!-- Questions Section -->
      <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="mb-0">Pertanyaan</h6>
          <button type="button" class="btn btn-sm btn-primary" onclick="addQuestion(this)">
            <i class="bi bi-plus-circle"></i> Tambah Pertanyaan
          </button>
        </div>
        <div class="questions-container"></div>
      </div>
    </div>
  </div>
</template>

<!-- Question Template (Hidden) -->
<template id="questionTemplate">
  <div class="card mb-3 question-item" data-question-index="" data-question-id="">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
      <span>Pertanyaan <span class="question-number"></span></span>
      <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion(this)">
        <i class="bi bi-trash"></i> Hapus
      </button>
    </div>
    <div class="card-body">
      <input type="hidden" name="quizzes[][questions][][id]" class="question-id" value="">
      <div class="mb-3">
        <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
        <textarea name="quizzes[][questions][][pertanyaan]" class="form-control question-pertanyaan" rows="2" required></textarea>
      </div>
      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Tipe <span class="text-danger">*</span></label>
          <select name="quizzes[][questions][][tipe]" class="form-control question-tipe" required onchange="toggleAnswerOptions(this)">
            <option value="multiple_choice">Multiple Choice</option>
            <option value="true_false">True/False</option>
            <option value="essay">Essay</option>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Bobot <span class="text-danger">*</span></label>
          <input type="number" name="quizzes[][questions][][bobot]" class="form-control question-bobot" min="1" value="1" required>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Urutan <span class="text-danger">*</span></label>
          <input type="number" name="quizzes[][questions][][urutan]" class="form-control question-urutan" min="1" required>
        </div>
      </div>
      
      <!-- Answer Options -->
      <div class="mt-3 answer-options-section">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <label class="form-label mb-0">Opsi Jawaban <span class="text-danger">*</span></label>
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAnswerOption(this)">
            <i class="bi bi-plus"></i> Tambah Opsi
          </button>
        </div>
        <div class="answer-options-container"></div>
        <small class="text-muted">Minimal 2 opsi. Centang salah satu sebagai jawaban benar.</small>
      </div>
    </div>
  </div>
</template>

<!-- Answer Option Template (Hidden) -->
<template id="answerOptionTemplate">
  <div class="input-group mb-2 answer-option-item" data-option-index="">
    <input type="text" name="quizzes[][questions][][answer_options][][teks_jawaban]" class="form-control answer-option-text" placeholder="Teks jawaban" required>
    <div class="input-group-text">
      <div class="form-check">
        <input class="form-check-input answer-option-correct" type="checkbox" name="quizzes[][questions][][answer_options][][is_correct]" value="1" onchange="validateCorrectAnswer(this)">
        <label class="form-check-label">Benar</label>
      </div>
    </div>
    <button type="button" class="btn btn-outline-danger" onclick="removeAnswerOption(this)">
      <i class="bi bi-trash"></i>
    </button>
  </div>
</template>

<script>
let contentIndex = {{ $subModule->contents->count() }};
let quizIndex = {{ $subModule->quizzes->count() }};
let questionIndex = {};
let answerOptionIndex = {};
const existingContents = @json($subModule->contents);
const existingQuizzes = @json($subModule->quizzes);

function nextStep(step) {
  if (step === 2) {
    if (!validateStep1()) {
      return;
    }
  } else if (step === 3) {
    if (!validateStep2()) {
      return;
    }
  } else if (step === 4) {
    if (!validateStep3()) {
      return;
    }
    updateReview();
  }
  
  document.querySelectorAll('.wizard-step').forEach(s => s.style.display = 'none');
  document.getElementById('step' + step).style.display = 'block';
}

function prevStep(step) {
  document.querySelectorAll('.wizard-step').forEach(s => s.style.display = 'none');
  document.getElementById('step' + step).style.display = 'block';
}

function validateStep1() {
  const judul = document.querySelector('input[name="judul"]').value;
  const urutan = document.querySelector('input[name="urutan"]').value;
  
  if (!judul || !urutan) {
    alert('Please fill in all required fields in Step 1.');
    return false;
  }
  return true;
}

function validateStep2() {
  // Contents are optional, so validation passes
  return true;
}

function validateStep3() {
  // Quizzes are optional, so validation passes
  return true;
}

function loadExistingContents() {
  existingContents.forEach((content, idx) => {
    addContentFromData(content, idx);
  });
}

function addContentFromData(contentData, index) {
  const template = document.getElementById('contentTemplate');
  const clone = template.content.cloneNode(true);
  const contentItem = clone.querySelector('.content-item');
  contentItem.setAttribute('data-content-index', index);
  contentItem.setAttribute('data-content-id', contentData.id);
  contentItem.querySelector('.content-number').textContent = index + 1;
  contentItem.querySelector('.content-status-badge').innerHTML = '<span class="badge bg-info">Existing</span>';
  
  const idInput = contentItem.querySelector('.content-id');
  const judulInput = contentItem.querySelector('.content-judul');
  const tipeInput = contentItem.querySelector('.content-tipe');
  const urutanInput = contentItem.querySelector('.content-urutan');
  const htmlContentInput = contentItem.querySelector('textarea[name*="html_content"]');
  const fileInput = contentItem.querySelector('.content-file');
  const urlInput = contentItem.querySelector('input[name*="external_url"]');
  const youtubeUrlInput = contentItem.querySelector('input[name*="youtube_url"]');
  const requiredDurationInput = contentItem.querySelector('input[name*="required_duration"]');
  const currentFileInfo = contentItem.querySelector('.current-file-info');
  const currentFileLink = contentItem.querySelector('.current-file-link');
  
  idInput.name = `contents[${index}][id]`;
  idInput.value = contentData.id;
  judulInput.name = `contents[${index}][judul]`;
  judulInput.value = contentData.judul;
  tipeInput.name = `contents[${index}][tipe]`;
  tipeInput.value = contentData.tipe;
  urutanInput.name = `contents[${index}][urutan]`;
  urutanInput.value = contentData.urutan;
  
  if (htmlContentInput) {
    htmlContentInput.name = `contents[${index}][html_content]`;
    htmlContentInput.value = contentData.html_content || '';
  }
  if (fileInput) {
    fileInput.name = `contents[${index}][file_path]`;
    if (contentData.file_path) {
      currentFileInfo.style.display = 'block';
      currentFileLink.href = `/instructor/contents/${contentData.id}/download`;
      currentFileLink.textContent = contentData.file_path.split('/').pop();
    }
  }
  if (urlInput) {
    urlInput.name = `contents[${index}][external_url]`;
    urlInput.value = contentData.external_url || '';
  }
  if (youtubeUrlInput) {
    youtubeUrlInput.name = `contents[${index}][youtube_url]`;
    youtubeUrlInput.value = contentData.youtube_url || '';
  }
  if (requiredDurationInput) {
    requiredDurationInput.name = `contents[${index}][required_duration]`;
    requiredDurationInput.value = contentData.required_duration || '';
  }
  
  toggleContentFields(tipeInput);
  
  document.getElementById('contentsContainer').appendChild(clone);
}

function addContent() {
  const template = document.getElementById('contentTemplate');
  const clone = template.content.cloneNode(true);
  const contentItem = clone.querySelector('.content-item');
  contentItem.setAttribute('data-content-index', contentIndex);
  contentItem.setAttribute('data-content-id', '');
  contentItem.querySelector('.content-number').textContent = contentIndex + 1;
  contentItem.querySelector('.content-status-badge').innerHTML = '<span class="badge bg-success">New</span>';
  
  const idInput = contentItem.querySelector('.content-id');
  const judulInput = contentItem.querySelector('.content-judul');
  const tipeInput = contentItem.querySelector('.content-tipe');
  const urutanInput = contentItem.querySelector('.content-urutan');
  const htmlContentInput = contentItem.querySelector('textarea[name*="html_content"]');
  const fileInput = contentItem.querySelector('.content-file');
  const urlInput = contentItem.querySelector('input[name*="external_url"]');
  const youtubeUrlInput = contentItem.querySelector('input[name*="youtube_url"]');
  const requiredDurationInput = contentItem.querySelector('input[name*="required_duration"]');
  
  idInput.name = `contents[${contentIndex}][id]`;
  idInput.value = '';
  judulInput.name = `contents[${contentIndex}][judul]`;
  tipeInput.name = `contents[${contentIndex}][tipe]`;
  urutanInput.name = `contents[${contentIndex}][urutan]`;
  urutanInput.value = contentIndex + 1;
  
  if (htmlContentInput) {
    htmlContentInput.name = `contents[${contentIndex}][html_content]`;
  }
  if (fileInput) {
    fileInput.name = `contents[${contentIndex}][file_path]`;
  }
  if (urlInput) {
    urlInput.name = `contents[${contentIndex}][external_url]`;
  }
  if (youtubeUrlInput) {
    youtubeUrlInput.name = `contents[${contentIndex}][youtube_url]`;
  }
  if (requiredDurationInput) {
    requiredDurationInput.name = `contents[${contentIndex}][required_duration]`;
  }
  
  document.getElementById('contentsContainer').appendChild(clone);
  contentIndex++;
}

function removeContent(btn) {
  const contentItem = btn.closest('.content-item');
  contentItem.remove();
  renumberContents();
}

function renumberContents() {
  const contents = document.querySelectorAll('.content-item');
  contents.forEach((content, index) => {
    content.setAttribute('data-content-index', index);
    content.querySelector('.content-number').textContent = index + 1;
    const idInput = content.querySelector('.content-id');
    const judulInput = content.querySelector('.content-judul');
    const tipeInput = content.querySelector('.content-tipe');
    const urutanInput = content.querySelector('.content-urutan');
    const htmlContentInput = content.querySelector('textarea[name*="html_content"]');
    const fileInput = content.querySelector('.content-file');
    const urlInput = content.querySelector('input[name*="external_url"]');
    const youtubeUrlInput = content.querySelector('input[name*="youtube_url"]');
    const requiredDurationInput = content.querySelector('input[name*="required_duration"]');
    
    const contentIdx = index;
    if (idInput) {
      idInput.name = `contents[${contentIdx}][id]`;
    }
    judulInput.name = `contents[${contentIdx}][judul]`;
    tipeInput.name = `contents[${contentIdx}][tipe]`;
    urutanInput.name = `contents[${contentIdx}][urutan]`;
    urutanInput.value = contentIdx + 1;
    
    if (htmlContentInput) {
      htmlContentInput.name = `contents[${contentIdx}][html_content]`;
    }
    if (fileInput) {
      fileInput.name = `contents[${contentIdx}][file_path]`;
    }
    if (urlInput) {
      urlInput.name = `contents[${contentIdx}][external_url]`;
    }
    if (youtubeUrlInput) {
      youtubeUrlInput.name = `contents[${contentIdx}][youtube_url]`;
    }
    if (requiredDurationInput) {
      requiredDurationInput.name = `contents[${contentIdx}][required_duration]`;
    }
  });
  contentIndex = contents.length;
}

function toggleContentFields(select) {
  const contentItem = select.closest('.content-item');
  const htmlField = contentItem.querySelector('.content-html-field');
  const fileField = contentItem.querySelector('.content-file-field');
  const urlField = contentItem.querySelector('.content-url-field');
  const youtubeField = contentItem.querySelector('.content-youtube-field');
  const requiredDurationField = contentItem.querySelector('.content-required-duration-field');
  
  htmlField.style.display = 'none';
  fileField.style.display = 'none';
  urlField.style.display = 'none';
  if (youtubeField) youtubeField.style.display = 'none';
  if (requiredDurationField) requiredDurationField.style.display = 'none';
  
  const type = select.value;
  if (type === 'text' || type === 'html') {
    htmlField.style.display = 'block';
  } else if (type === 'video' || type === 'audio' || type === 'pdf' || type === 'image') {
    fileField.style.display = 'block';
    const fileInput = fileField.querySelector('input[type="file"]');
    if (type === 'video') fileInput.setAttribute('accept', 'video/*');
    else if (type === 'audio') fileInput.setAttribute('accept', 'audio/*');
    else if (type === 'pdf') fileInput.setAttribute('accept', '.pdf');
    else if (type === 'image') fileInput.setAttribute('accept', 'image/*');
  } else if (type === 'link') {
    urlField.style.display = 'block';
  } else if (type === 'youtube') {
    if (youtubeField) youtubeField.style.display = 'block';
    if (requiredDurationField) requiredDurationField.style.display = 'block';
  }
}

// Quiz Management Functions
function loadExistingQuizzes() {
  existingQuizzes.forEach((quiz, idx) => {
    addQuizFromData(quiz, idx);
  });
}

function addQuiz() {
  const template = document.getElementById('quizTemplate');
  const clone = template.content.cloneNode(true);
  const quizItem = clone.querySelector('.quiz-item');
  quizItem.setAttribute('data-quiz-index', quizIndex);
  quizItem.setAttribute('data-quiz-id', '');
  quizItem.querySelector('.quiz-number').textContent = quizIndex + 1;
  quizItem.querySelector('.quiz-status-badge').innerHTML = '<span class="badge bg-success">New</span>';
  
  updateQuizNames(quizItem, quizIndex);
  
  document.getElementById('quizzesContainer').appendChild(clone);
  quizIndex++;
  
  // Initialize question index for this quiz
  if (!questionIndex[quizIndex - 1]) {
    questionIndex[quizIndex - 1] = 0;
  }
}

function addQuizFromData(quizData, index) {
  const template = document.getElementById('quizTemplate');
  const clone = template.content.cloneNode(true);
  const quizItem = clone.querySelector('.quiz-item');
  quizItem.setAttribute('data-quiz-index', index);
  quizItem.setAttribute('data-quiz-id', quizData.id);
  quizItem.querySelector('.quiz-number').textContent = index + 1;
  quizItem.querySelector('.quiz-status-badge').innerHTML = '<span class="badge bg-info">Existing</span>';
  
  const idInput = quizItem.querySelector('.quiz-id');
  const judulInput = quizItem.querySelector('.quiz-judul');
  const deskripsiInput = quizItem.querySelector('.quiz-deskripsi');
  const nilaiMinimumInput = quizItem.querySelector('.quiz-nilai-minimum');
  const maxAttemptsInput = quizItem.querySelector('.quiz-max-attempts');
  
  idInput.name = `quizzes[${index}][id]`;
  idInput.value = quizData.id;
  judulInput.name = `quizzes[${index}][judul]`;
  judulInput.value = quizData.judul;
  deskripsiInput.name = `quizzes[${index}][deskripsi]`;
  deskripsiInput.value = quizData.deskripsi || '';
  nilaiMinimumInput.name = `quizzes[${index}][nilai_minimum]`;
  nilaiMinimumInput.value = quizData.nilai_minimum;
  maxAttemptsInput.name = `quizzes[${index}][max_attempts]`;
  maxAttemptsInput.value = quizData.max_attempts || 3;
  
  // Load existing questions
  if (quizData.questions && quizData.questions.length > 0) {
    if (!questionIndex[index]) {
      questionIndex[index] = 0;
    }
    quizData.questions.forEach((questionData, qIdx) => {
      addQuestionFromData(questionData, index, qIdx);
      questionIndex[index]++;
    });
  }
  
  updateQuizNames(quizItem, index);
  document.getElementById('quizzesContainer').appendChild(clone);
}

function updateQuizNames(quizItem, quizIdx) {
  const idInput = quizItem.querySelector('.quiz-id');
  const judulInput = quizItem.querySelector('.quiz-judul');
  const deskripsiInput = quizItem.querySelector('.quiz-deskripsi');
  const nilaiMinimumInput = quizItem.querySelector('.quiz-nilai-minimum');
  const maxAttemptsInput = quizItem.querySelector('.quiz-max-attempts');
  
  if (idInput) {
    idInput.name = `quizzes[${quizIdx}][id]`;
  }
  judulInput.name = `quizzes[${quizIdx}][judul]`;
  deskripsiInput.name = `quizzes[${quizIdx}][deskripsi]`;
  nilaiMinimumInput.name = `quizzes[${quizIdx}][nilai_minimum]`;
  maxAttemptsInput.name = `quizzes[${quizIdx}][max_attempts]`;
  
  // Update question names
  const questions = quizItem.querySelectorAll('.question-item');
  questions.forEach((question, qIdx) => {
    updateQuestionNames(question, quizIdx, qIdx);
  });
}

function removeQuiz(btn) {
  if (confirm('Apakah Anda yakin ingin menghapus quiz ini? Semua pertanyaan yang terkait juga akan dihapus.')) {
    const quizItem = btn.closest('.quiz-item');
    const quizIdx = parseInt(quizItem.getAttribute('data-quiz-index'));
    
    // Remove from questionIndex
    delete questionIndex[quizIdx];
    
    quizItem.remove();
    
    // Renumber remaining quizzes
    const quizzes = document.querySelectorAll('.quiz-item');
    quizzes.forEach((quiz, index) => {
      const newIdx = index;
      quiz.setAttribute('data-quiz-index', newIdx);
      quiz.querySelector('.quiz-number').textContent = newIdx + 1;
      updateQuizNames(quiz, newIdx);
    });
    
    quizIndex = quizzes.length;
  }
}

function toggleQuizCollapse(btn) {
  const quizItem = btn.closest('.quiz-item');
  const quizBody = quizItem.querySelector('.quiz-body');
  const icon = btn.querySelector('.quiz-toggle-icon');
  
  if (quizBody.style.display === 'none') {
    quizBody.style.display = 'block';
    icon.classList.remove('bi-chevron-right');
    icon.classList.add('bi-chevron-down');
  } else {
    quizBody.style.display = 'none';
    icon.classList.remove('bi-chevron-down');
    icon.classList.add('bi-chevron-right');
  }
}

// Question Management Functions
function addQuestion(btn) {
  const quizItem = btn.closest('.quiz-item');
  const quizIdx = parseInt(quizItem.getAttribute('data-quiz-index'));
  const questionsContainer = quizItem.querySelector('.questions-container');
  
  if (!questionIndex[quizIdx]) {
    questionIndex[quizIdx] = 0;
  }
  const qIdx = questionIndex[quizIdx];
  
  const template = document.getElementById('questionTemplate');
  const clone = template.content.cloneNode(true);
  const questionItem = clone.querySelector('.question-item');
  questionItem.setAttribute('data-question-index', qIdx);
  questionItem.setAttribute('data-quiz-index', quizIdx);
  questionItem.setAttribute('data-question-id', '');
  questionItem.querySelector('.question-number').textContent = qIdx + 1;
  
  updateQuestionNames(questionItem, quizIdx, qIdx);
  
  questionsContainer.appendChild(clone);
  questionIndex[quizIdx]++;
  
  // Initialize answer options index for this question
  const questionKey = `${quizIdx}_${qIdx}`;
  if (!answerOptionIndex[questionKey]) {
    answerOptionIndex[questionKey] = 0;
  }
  
  // Add 3 answer options by default for multiple choice
  const answerOptionsContainer = questionItem.querySelector('.answer-options-container');
  const addOptionBtn = questionItem.querySelector('button[onclick*="addAnswerOption"]');
  for (let i = 0; i < 3; i++) {
    if (addOptionBtn) {
      addAnswerOption(addOptionBtn);
    }
  }
  
  toggleAnswerOptions(questionItem.querySelector('.question-tipe'));
}

function addQuestionFromData(questionData, quizIdx, qIdx) {
  const quizItem = document.querySelector(`[data-quiz-index="${quizIdx}"]`);
  if (!quizItem) return;
  const questionsContainer = quizItem.querySelector('.questions-container');
  
  const template = document.getElementById('questionTemplate');
  const clone = template.content.cloneNode(true);
  const questionItem = clone.querySelector('.question-item');
  questionItem.setAttribute('data-question-index', qIdx);
  questionItem.setAttribute('data-quiz-index', quizIdx);
  questionItem.setAttribute('data-question-id', questionData.id);
  questionItem.querySelector('.question-number').textContent = qIdx + 1;
  
  const idInput = questionItem.querySelector('.question-id');
  const pertanyaanInput = questionItem.querySelector('.question-pertanyaan');
  const bobotInput = questionItem.querySelector('.question-bobot');
  const urutanInput = questionItem.querySelector('.question-urutan');
  const tipeInput = questionItem.querySelector('.question-tipe');
  
  idInput.name = `quizzes[${quizIdx}][questions][${qIdx}][id]`;
  idInput.value = questionData.id;
  pertanyaanInput.name = `quizzes[${quizIdx}][questions][${qIdx}][pertanyaan]`;
  pertanyaanInput.value = questionData.pertanyaan;
  bobotInput.name = `quizzes[${quizIdx}][questions][${qIdx}][bobot]`;
  bobotInput.value = questionData.bobot || 1;
  urutanInput.name = `quizzes[${quizIdx}][questions][${qIdx}][urutan]`;
  urutanInput.value = questionData.urutan || qIdx + 1;
  tipeInput.name = `quizzes[${quizIdx}][questions][${qIdx}][tipe]`;
  tipeInput.value = questionData.tipe || 'multiple_choice';
  
  // Load existing answer options
  if (questionData.answer_options && questionData.answer_options.length > 0) {
    const questionKey = `${quizIdx}_${qIdx}`;
    if (!answerOptionIndex[questionKey]) {
      answerOptionIndex[questionKey] = 0;
    }
    questionData.answer_options.forEach((optionData, optIdx) => {
      addAnswerOptionFromData(optionData, quizIdx, qIdx, optIdx);
      answerOptionIndex[questionKey]++;
    });
  }
  
  updateQuestionNames(questionItem, quizIdx, qIdx);
  toggleAnswerOptions(tipeInput);
  questionsContainer.appendChild(clone);
}

function updateQuestionNames(questionItem, quizIdx, qIdx) {
  const idInput = questionItem.querySelector('.question-id');
  const pertanyaanInput = questionItem.querySelector('.question-pertanyaan');
  const bobotInput = questionItem.querySelector('.question-bobot');
  const urutanInput = questionItem.querySelector('.question-urutan');
  const tipeInput = questionItem.querySelector('.question-tipe');
  
  if (idInput) {
    idInput.name = `quizzes[${quizIdx}][questions][${qIdx}][id]`;
  }
  pertanyaanInput.name = `quizzes[${quizIdx}][questions][${qIdx}][pertanyaan]`;
  bobotInput.name = `quizzes[${quizIdx}][questions][${qIdx}][bobot]`;
  urutanInput.name = `quizzes[${quizIdx}][questions][${qIdx}][urutan]`;
  tipeInput.name = `quizzes[${quizIdx}][questions][${qIdx}][tipe]`;
  
  // Update answer option names
  const answerOptions = questionItem.querySelectorAll('.answer-option-item');
  answerOptions.forEach((option, optIdx) => {
    updateAnswerOptionNames(option, quizIdx, qIdx, optIdx);
  });
}

function removeQuestion(btn) {
  const questionItem = btn.closest('.question-item');
  const quizIdx = parseInt(questionItem.getAttribute('data-quiz-index'));
  const qIdx = parseInt(questionItem.getAttribute('data-question-index'));
  
  const questionKey = `${quizIdx}_${qIdx}`;
  delete answerOptionIndex[questionKey];
  
  questionItem.remove();
  
  // Renumber remaining questions
  const quizItem = questionItem.closest('.quiz-item');
  const questions = quizItem.querySelectorAll('.question-item');
  questions.forEach((q, index) => {
    const newQIdx = index;
    q.setAttribute('data-question-index', newQIdx);
    q.querySelector('.question-number').textContent = newQIdx + 1;
    updateQuestionNames(q, quizIdx, newQIdx);
  });
  
  if (questionIndex[quizIdx] > qIdx) {
    questionIndex[quizIdx]--;
  }
}

// Answer Option Management Functions
function addAnswerOption(btn) {
  const questionItem = btn.closest('.question-item');
  const answerOptionsContainer = questionItem.querySelector('.answer-options-container');
  const quizIdx = parseInt(questionItem.getAttribute('data-quiz-index'));
  const qIdx = parseInt(questionItem.getAttribute('data-question-index'));
  
  const questionKey = `${quizIdx}_${qIdx}`;
  if (!answerOptionIndex[questionKey]) {
    answerOptionIndex[questionKey] = 0;
  }
  const optIdx = answerOptionIndex[questionKey];
  
  // Check max 5 options
  const existingOptions = answerOptionsContainer.querySelectorAll('.answer-option-item');
  if (existingOptions.length >= 5) {
    alert('Maksimal 5 opsi jawaban per pertanyaan.');
    return;
  }
  
  const template = document.getElementById('answerOptionTemplate');
  const clone = template.content.cloneNode(true);
  const optionItem = clone.querySelector('.answer-option-item');
  optionItem.setAttribute('data-option-index', optIdx);
  
  updateAnswerOptionNames(optionItem, quizIdx, qIdx, optIdx);
  
  answerOptionsContainer.appendChild(clone);
  answerOptionIndex[questionKey]++;
}

function addAnswerOptionFromData(optionData, quizIdx, qIdx, optIdx) {
  const quizItem = document.querySelector(`[data-quiz-index="${quizIdx}"]`);
  if (!quizItem) return;
  const questionItem = quizItem.querySelector(`[data-question-index="${qIdx}"]`);
  if (!questionItem) return;
  const answerOptionsContainer = questionItem.querySelector('.answer-options-container');
  
  const template = document.getElementById('answerOptionTemplate');
  const clone = template.content.cloneNode(true);
  const optionItem = clone.querySelector('.answer-option-item');
  optionItem.setAttribute('data-option-index', optIdx);
  
  const textInput = optionItem.querySelector('.answer-option-text');
  const correctInput = optionItem.querySelector('.answer-option-correct');
  
  textInput.name = `quizzes[${quizIdx}][questions][${qIdx}][answer_options][${optIdx}][teks_jawaban]`;
  textInput.value = optionData.teks_jawaban || '';
  correctInput.name = `quizzes[${quizIdx}][questions][${qIdx}][answer_options][${optIdx}][is_correct]`;
  correctInput.checked = optionData.is_correct == 1 || optionData.is_correct === true;
  
  updateAnswerOptionNames(optionItem, quizIdx, qIdx, optIdx);
  answerOptionsContainer.appendChild(clone);
}

function updateAnswerOptionNames(optionItem, quizIdx, qIdx, optIdx) {
  const textInput = optionItem.querySelector('.answer-option-text');
  const correctInput = optionItem.querySelector('.answer-option-correct');
  
  textInput.name = `quizzes[${quizIdx}][questions][${qIdx}][answer_options][${optIdx}][teks_jawaban]`;
  correctInput.name = `quizzes[${quizIdx}][questions][${qIdx}][answer_options][${optIdx}][is_correct]`;
}

function removeAnswerOption(btn) {
  const optionItem = btn.closest('.answer-option-item');
  const questionItem = optionItem.closest('.question-item');
  const quizIdx = parseInt(questionItem.getAttribute('data-quiz-index'));
  const qIdx = parseInt(questionItem.getAttribute('data-question-index'));
  
  optionItem.remove();
  
  // Renumber remaining options
  const answerOptionsContainer = questionItem.querySelector('.answer-options-container');
  const options = answerOptionsContainer.querySelectorAll('.answer-option-item');
  options.forEach((option, index) => {
    updateAnswerOptionNames(option, quizIdx, qIdx, index);
  });
}

function toggleAnswerOptions(select) {
  const questionItem = select.closest('.question-item');
  const answerOptionsSection = questionItem.querySelector('.answer-options-section');
  const tipe = select.value;
  
  if (tipe === 'essay') {
    answerOptionsSection.style.display = 'none';
  } else {
    answerOptionsSection.style.display = 'block';
  }
}

function validateCorrectAnswer(checkbox) {
  const questionItem = checkbox.closest('.question-item');
  const tipe = questionItem.querySelector('.question-tipe').value;
  
  if (tipe === 'multiple_choice' || tipe === 'true_false') {
    const answerOptions = questionItem.querySelectorAll('.answer-option-correct');
    const checkedCount = Array.from(answerOptions).filter(cb => cb.checked).length;
    
    if (checkedCount > 1) {
      alert('Hanya satu jawaban yang benar untuk pertanyaan pilihan ganda.');
      checkbox.checked = false;
    }
  }
}

function handleFormSubmit(event) {
  event.preventDefault();
  event.stopPropagation();
  
  // Show all wizard steps so all form fields are visible
  document.querySelectorAll('.wizard-step').forEach(step => {
    step.style.display = 'block';
  });
  
  // Show all hidden content fields
  document.querySelectorAll('.content-html-field, .content-file-field, .content-url-field, .content-youtube-field, .content-required-duration-field').forEach(field => {
    field.style.display = 'block';
  });
  
  // Show all answer options sections
  document.querySelectorAll('.answer-options-section').forEach(field => {
    field.style.display = 'block';
  });
  
  // Validate required fields
  if (!validateStep1()) {
    alert('Please fill in all required fields before submitting.');
    return false;
  }
  
  // Submit the form
  const form = document.getElementById('subModuleWizardForm');
  if (form) {
    form.submit();
  }
  
  return false;
}

function updateReview() {
  document.getElementById('reviewJudul').textContent = document.querySelector('input[name="judul"]').value;
  document.getElementById('reviewDeskripsi').textContent = document.querySelector('textarea[name="deskripsi"]').value || '(Tidak ada deskripsi)';
  document.getElementById('reviewUrutan').textContent = document.querySelector('input[name="urutan"]').value;
  
  const contentsContainer = document.getElementById('reviewContents');
  contentsContainer.innerHTML = '';
  const contents = document.querySelectorAll('.content-item');
  if (contents.length === 0) {
    contentsContainer.innerHTML = '<p class="text-muted">Tidak ada content</p>';
  } else {
    contents.forEach((content, idx) => {
      const judul = content.querySelector('.content-judul').value;
      const tipe = content.querySelector('.content-tipe').options[content.querySelector('.content-tipe').selectedIndex].text;
      const isExisting = content.getAttribute('data-content-id') !== '';
      const status = isExisting ? '(Existing)' : '(New)';
      const contentDiv = document.createElement('div');
      contentDiv.className = 'mb-2';
      contentDiv.innerHTML = `<strong>Content ${idx + 1}:</strong> ${judul} (${tipe}) ${status}`;
      contentsContainer.appendChild(contentDiv);
    });
  }
  
  const quizzesContainer = document.getElementById('reviewQuizzes');
  quizzesContainer.innerHTML = '';
  const quizzes = document.querySelectorAll('.quiz-item');
  if (quizzes.length === 0) {
    quizzesContainer.innerHTML = '<p class="text-muted">Tidak ada quiz</p>';
  } else {
    quizzes.forEach((quiz, idx) => {
      const judul = quiz.querySelector('.quiz-judul').value;
      const isExisting = quiz.getAttribute('data-quiz-id') !== '';
      const status = isExisting ? '(Existing)' : '(New)';
      const questionsCount = quiz.querySelectorAll('.question-item').length;
      const quizDiv = document.createElement('div');
      quizDiv.className = 'mb-2';
      quizDiv.innerHTML = `<strong>Quiz ${idx + 1}:</strong> ${judul} - ${questionsCount} pertanyaan ${status}`;
      quizzesContainer.appendChild(quizDiv);
    });
  }
}

document.addEventListener('DOMContentLoaded', function() {
  loadExistingContents();
  loadExistingQuizzes();
  
  // Ensure submit button is enabled
  const submitBtn = document.getElementById('submitBtn');
  if (submitBtn) {
    submitBtn.disabled = false;
    submitBtn.style.pointerEvents = 'auto';
    submitBtn.style.cursor = 'pointer';
  }
});
</script>

<style>
.wizard-step {
  animation: fadeIn 0.3s;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.content-item {
  border-left: 4px solid #ffc107;
}

.quiz-item {
  border-left: 4px solid #17a2b8;
}

.question-item {
  border-left: 4px solid #6c757d;
}

button[type="submit"] {
  cursor: pointer !important;
  pointer-events: auto !important;
  z-index: 10;
}
</style>
@endsection
