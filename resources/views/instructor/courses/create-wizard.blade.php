@extends('layouts.instructor')

@section('title','Buat Course Lengkap (Wizard)')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}">Courses</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create Wizard</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Course Creation Wizard</h5>
      <small>Buat course lengkap dengan modules, sub-modules, dan contents dalam satu alur</small>
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
      
      <form action="{{ route('instructor.courses.store-wizard') }}" method="post" enctype="multipart/form-data" id="courseWizardForm">
        @csrf
        
        <!-- Step 1: Course Information -->
        <div class="wizard-step" id="step1">
          <h4 class="mb-4">Step 1: Course Information</h4>
          
          <div class="mb-3">
            <label class="form-label">Judul Course <span class="text-danger">*</span></label>
            <input type="text" name="judul" value="{{ old('judul') }}" class="form-control" required>
            @error('judul')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
            <textarea name="deskripsi" class="form-control" rows="4" required>{{ old('deskripsi') }}</textarea>
            @error('deskripsi')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">JP Value <span class="text-danger">*</span></label>
              <input type="number" name="jp_value" value="{{ old('jp_value') }}" class="form-control" min="1" required>
              @error('jp_value')<small class="text-danger d-block">{{ $message }}</small>@enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Bidang Kompetensi <span class="text-danger">*</span></label>
              <input type="text" name="bidang_kompetensi" value="{{ old('bidang_kompetensi') }}" class="form-control" required>
              @error('bidang_kompetensi')<small class="text-danger d-block">{{ $message }}</small>@enderror
            </div>
          </div>

          <div class="d-flex justify-content-end mt-4">
            <button type="button" class="btn btn-primary" onclick="nextStep(2)">Next: Add Modules →</button>
          </div>
        </div>

        <!-- Step 2: Modules -->
        <div class="wizard-step" id="step2" style="display: none;">
          <h4 class="mb-4">Step 2: Modules</h4>
          <p class="text-muted mb-4">Tambahkan modules untuk course ini. Minimal 1 module diperlukan.</p>
          
          <div id="modulesContainer">
            <!-- Modules will be added here dynamically -->
          </div>

          <button type="button" class="btn btn-success mb-4" onclick="addModule()">
            <i class="bi bi-plus-circle"></i> Tambah Module
          </button>

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="prevStep(1)">← Back</button>
            <button type="button" class="btn btn-primary" onclick="nextStep(3)">Next: Review →</button>
          </div>
        </div>

        <!-- Step 3: Review and Submit -->
        <div class="wizard-step" id="step3" style="display: none;">
          <h4 class="mb-4">Step 3: Review and Submit</h4>
          <p class="text-muted mb-4">Tinjau informasi course Anda sebelum menyimpan.</p>
          
          <div class="card mb-3">
            <div class="card-header">Course Information</div>
            <div class="card-body">
              <p><strong>Judul:</strong> <span id="reviewJudul"></span></p>
              <p><strong>Deskripsi:</strong> <span id="reviewDeskripsi"></span></p>
              <p><strong>JP Value:</strong> <span id="reviewJpValue"></span></p>
              <p><strong>Bidang Kompetensi:</strong> <span id="reviewBidangKompetensi"></span></p>
            </div>
          </div>

          <div class="card mb-3">
            <div class="card-header">Modules Summary</div>
            <div class="card-body" id="reviewModules">
              <!-- Modules summary will be shown here -->
            </div>
          </div>

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="prevStep(2)">← Back</button>
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check-circle"></i> Create Course
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Module Template (Hidden) -->
<template id="moduleTemplate">
  <div class="card mb-3 module-item" data-module-index="">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Module <span class="module-number"></span></span>
      <button type="button" class="btn btn-sm btn-danger" onclick="removeModule(this)">
        <i class="bi bi-trash"></i> Hapus
      </button>
    </div>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Judul Module <span class="text-danger">*</span></label>
        <input type="text" name="modules[][judul]" class="form-control module-judul" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="modules[][deskripsi]" class="form-control module-deskripsi" rows="2"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Urutan <span class="text-danger">*</span></label>
        <input type="number" name="modules[][urutan]" class="form-control module-urutan" min="1" required>
      </div>
      
      <div class="mb-3">
        <button type="button" class="btn btn-sm btn-info" onclick="toggleSubModules(this)">
          <i class="bi bi-chevron-down"></i> Sub-Modules
        </button>
      </div>
      
      <div class="sub-modules-container" style="display: none;">
        <div class="sub-modules-list"></div>
        <button type="button" class="btn btn-sm btn-success" onclick="addSubModule(this)">
          <i class="bi bi-plus-circle"></i> Tambah Sub-Module
        </button>
      </div>
    </div>
  </div>
</template>

<!-- Sub-Module Template (Hidden) -->
<template id="subModuleTemplate">
  <div class="card mb-3 sub-module-item" data-sub-module-index="">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <span>Sub-Module <span class="sub-module-number"></span></span>
      <button type="button" class="btn btn-sm btn-danger" onclick="removeSubModule(this)">
        <i class="bi bi-trash"></i> Hapus
      </button>
    </div>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Judul Sub-Module <span class="text-danger">*</span></label>
        <input type="text" name="modules[][sub_modules][][judul]" class="form-control sub-module-judul" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="modules[][sub_modules][][deskripsi]" class="form-control sub-module-deskripsi" rows="2"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Urutan <span class="text-danger">*</span></label>
        <input type="number" name="modules[][sub_modules][][urutan]" class="form-control sub-module-urutan" min="1" required>
      </div>
      
      <div class="mb-3">
        <button type="button" class="btn btn-sm btn-warning" onclick="toggleContents(this)">
          <i class="bi bi-chevron-down"></i> Contents
        </button>
      </div>
      
      <div class="contents-container" style="display: none;">
        <div class="contents-list"></div>
        <button type="button" class="btn btn-sm btn-primary" onclick="addContent(this)">
          <i class="bi bi-plus-circle"></i> Tambah Content
        </button>
      </div>
      
      <div class="mb-3">
        <button type="button" class="btn btn-sm btn-info" onclick="toggleQuizzes(this)">
          <i class="bi bi-chevron-down"></i> Quizzes
        </button>
      </div>
      
      <div class="quizzes-container" style="display: none;">
        <div class="quizzes-list"></div>
        <button type="button" class="btn btn-sm btn-success" onclick="addQuiz(this)">
          <i class="bi bi-plus-circle"></i> Tambah Quiz
        </button>
      </div>
    </div>
  </div>
</template>

<!-- Quiz Template (Hidden) -->
<template id="quizTemplate">
  <div class="card mb-3 quiz-item" data-quiz-index="">
    <div class="card-header bg-info d-flex justify-content-between align-items-center">
      <span>Quiz <span class="quiz-number"></span></span>
      <button type="button" class="btn btn-sm btn-danger" onclick="removeQuiz(this)">
        <i class="bi bi-trash"></i> Hapus
      </button>
    </div>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Judul Quiz <span class="text-danger">*</span></label>
        <input type="text" name="modules[][sub_modules][][quizzes][][judul]" class="form-control quiz-judul" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="modules[][sub_modules][][quizzes][][deskripsi]" class="form-control quiz-deskripsi" rows="2"></textarea>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Nilai Minimum <span class="text-danger">*</span></label>
          <input type="number" name="modules[][sub_modules][][quizzes][][nilai_minimum]" class="form-control quiz-nilai-minimum" min="0" max="100" step="0.01" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Maks Attempts <span class="text-danger">*</span></label>
          <input type="number" name="modules[][sub_modules][][quizzes][][max_attempts]" class="form-control quiz-max-attempts" min="1" value="3" required>
        </div>
      </div>
      
      <!-- Questions Section -->
      <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="mb-0">Pertanyaan (Pilihan Ganda)</h6>
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
  <div class="card mb-3 question-item" data-question-index="">
    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
      <span>Pertanyaan <span class="question-number"></span></span>
      <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion(this)">
        <i class="bi bi-trash"></i> Hapus
      </button>
    </div>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Pertanyaan <span class="text-danger">*</span></label>
        <textarea name="modules[][sub_modules][][quizzes][][questions][][pertanyaan]" class="form-control question-pertanyaan" rows="2" required></textarea>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Bobot <span class="text-danger">*</span></label>
          <input type="number" name="modules[][sub_modules][][quizzes][][questions][][bobot]" class="form-control question-bobot" min="1" value="1" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Urutan <span class="text-danger">*</span></label>
          <input type="number" name="modules[][sub_modules][][quizzes][][questions][][urutan]" class="form-control question-urutan" min="1" required>
        </div>
      </div>
      <input type="hidden" name="modules[][sub_modules][][quizzes][][questions][][tipe]" class="question-tipe" value="multiple_choice">
      
      <!-- Answer Options -->
      <div class="mt-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <label class="form-label mb-0">Opsi Jawaban (Pilih salah satu yang benar) <span class="text-danger">*</span></label>
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAnswerOption(this)">
            <i class="bi bi-plus"></i> Tambah Opsi
          </button>
        </div>
        <div class="answer-options-container"></div>
        <small class="text-muted">Minimal 2 opsi, maksimal 5 opsi. Centang salah satu sebagai jawaban benar.</small>
      </div>
    </div>
  </div>
</template>

<!-- Answer Option Template (Hidden) -->
<template id="answerOptionTemplate">
  <div class="input-group mb-2 answer-option-item" data-option-index="">
    <input type="text" name="modules[][sub_modules][][quizzes][][questions][][answer_options][][teks_jawaban]" class="form-control answer-option-text" placeholder="Teks jawaban" required>
    <div class="input-group-text">
      <div class="form-check">
        <input class="form-check-input answer-option-correct" type="checkbox" name="modules[][sub_modules][][quizzes][][questions][][answer_options][][is_correct]" value="1" onchange="validateCorrectAnswer(this)">
        <label class="form-check-label">Benar</label>
      </div>
    </div>
    <button type="button" class="btn btn-outline-danger" onclick="removeAnswerOption(this)">
      <i class="bi bi-trash"></i>
    </button>
  </div>
</template>

<!-- Content Template (Hidden) -->
<template id="contentTemplate">
  <div class="card mb-3 content-item" data-content-index="">
    <div class="card-header bg-warning d-flex justify-content-between align-items-center">
      <span>Content <span class="content-number"></span></span>
      <button type="button" class="btn btn-sm btn-danger" onclick="removeContent(this)">
        <i class="bi bi-trash"></i> Hapus
      </button>
    </div>
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Judul Content <span class="text-danger">*</span></label>
        <input type="text" name="modules[][sub_modules][][contents][][judul]" class="form-control content-judul" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Tipe Content <span class="text-danger">*</span></label>
        <select name="modules[][sub_modules][][contents][][tipe]" class="form-control content-tipe" required onchange="toggleContentFields(this)">
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
        <input type="number" name="modules[][sub_modules][][contents][][urutan]" class="form-control content-urutan" min="1" required>
      </div>
      
      <!-- HTML Content Field -->
      <div class="mb-3 content-html-field" style="display: none;">
        <label class="form-label">HTML Content <span class="text-danger">*</span></label>
        <textarea name="modules[][sub_modules][][contents][][html_content]" class="form-control" rows="5"></textarea>
      </div>
      
      <!-- File Upload Field -->
      <div class="mb-3 content-file-field" style="display: none;">
        <label class="form-label">Upload File <span class="text-danger">*</span></label>
        <input type="file" name="modules[][sub_modules][][contents][][file_path]" class="form-control content-file">
      </div>
      
      <!-- External URL Field -->
      <div class="mb-3 content-url-field" style="display: none;">
        <label class="form-label">External URL <span class="text-danger">*</span></label>
        <input type="url" name="modules[][sub_modules][][contents][][external_url]" class="form-control" placeholder="https://example.com/file.pdf">
      </div>
      
      <!-- YouTube URL Field -->
      <div class="mb-3 content-youtube-field" style="display: none;">
        <label class="form-label">YouTube URL <span class="text-danger">*</span></label>
        <input type="url" name="modules[][sub_modules][][contents][][youtube_url]" class="form-control" placeholder="https://www.youtube.com/watch?v=VIDEO_ID atau https://youtu.be/VIDEO_ID">
      </div>
      
      <!-- Required Duration Field (for YouTube videos) -->
      <div class="mb-3 content-required-duration-field" style="display: none;">
        <label class="form-label">Durasi Video yang Diperlukan (detik) <span class="text-danger">*</span></label>
        <input type="number" name="modules[][sub_modules][][contents][][required_duration]" class="form-control" min="1" placeholder="Contoh: 300 (untuk 5 menit)">
        <small class="text-muted">Masukkan durasi video dalam detik. Siswa harus menonton video selama durasi ini sebelum dapat melanjutkan ke konten berikutnya.</small>
      </div>
    </div>
  </div>
</template>

<script>
let moduleIndex = 0;
let subModuleIndex = {};
let contentIndex = {};
let quizIndex = {};
let questionIndex = {};
let answerOptionIndex = {};

function nextStep(step) {
  // Validate current step
  if (step === 2) {
    if (!validateStep1()) {
      return;
    }
  } else if (step === 3) {
    if (!validateStep2()) {
      return;
    }
    updateReview();
  }
  
  // Hide all steps
  document.querySelectorAll('.wizard-step').forEach(s => s.style.display = 'none');
  // Show target step
  document.getElementById('step' + step).style.display = 'block';
}

function prevStep(step) {
  document.querySelectorAll('.wizard-step').forEach(s => s.style.display = 'none');
  document.getElementById('step' + step).style.display = 'block';
}

function validateStep1() {
  const judul = document.querySelector('input[name="judul"]').value;
  const deskripsi = document.querySelector('textarea[name="deskripsi"]').value;
  const jpValue = document.querySelector('input[name="jp_value"]').value;
  const bidangKompetensi = document.querySelector('input[name="bidang_kompetensi"]').value;
  
  if (!judul || !deskripsi || !jpValue || !bidangKompetensi) {
    alert('Please fill in all required fields in Step 1.');
    return false;
  }
  return true;
}

function validateStep2() {
  const modules = document.querySelectorAll('.module-item');
  if (modules.length === 0) {
    alert('Please add at least one module.');
    return false;
  }
  
  let isValid = true;
  modules.forEach((module, index) => {
    const judul = module.querySelector('.module-judul').value;
    const urutan = module.querySelector('.module-urutan').value;
    if (!judul || !urutan) {
      isValid = false;
    }
  });
  
  if (!isValid) {
    alert('Please fill in all required fields for modules.');
    return false;
  }
  return true;
}

function addModule() {
  const template = document.getElementById('moduleTemplate');
  const clone = template.content.cloneNode(true);
  const moduleItem = clone.querySelector('.module-item');
  moduleItem.setAttribute('data-module-index', moduleIndex);
  moduleItem.querySelector('.module-number').textContent = moduleIndex + 1;
  
  // Update input names with proper indices
  const judulInput = moduleItem.querySelector('.module-judul');
  const deskripsiInput = moduleItem.querySelector('.module-deskripsi');
  const urutanInput = moduleItem.querySelector('.module-urutan');
  
  judulInput.name = `modules[${moduleIndex}][judul]`;
  deskripsiInput.name = `modules[${moduleIndex}][deskripsi]`;
  urutanInput.name = `modules[${moduleIndex}][urutan]`;
  urutanInput.value = moduleIndex + 1;
  
  subModuleIndex[moduleIndex] = 0;
  contentIndex[moduleIndex] = {};
  
  document.getElementById('modulesContainer').appendChild(clone);
  moduleIndex++;
}

function removeModule(btn) {
  const moduleItem = btn.closest('.module-item');
  const moduleIdx = parseInt(moduleItem.getAttribute('data-module-index'));
  delete subModuleIndex[moduleIdx];
  delete contentIndex[moduleIdx];
  moduleItem.remove();
  renumberModules();
}

function renumberModules() {
  const modules = document.querySelectorAll('.module-item');
  modules.forEach((module, index) => {
    module.setAttribute('data-module-index', index);
    module.querySelector('.module-number').textContent = index + 1;
    const judulInput = module.querySelector('.module-judul');
    const deskripsiInput = module.querySelector('.module-deskripsi');
    const urutanInput = module.querySelector('.module-urutan');
    const moduleIdx = index;
    judulInput.name = `modules[${moduleIdx}][judul]`;
    deskripsiInput.name = `modules[${moduleIdx}][deskripsi]`;
    urutanInput.name = `modules[${moduleIdx}][urutan]`;
    urutanInput.value = moduleIdx + 1;
    
    // Update sub-modules
    const subModules = module.querySelectorAll('.sub-module-item');
    subModules.forEach((subModule, subIdx) => {
      updateSubModuleNames(subModule, moduleIdx, subIdx);
    });
  });
  moduleIndex = modules.length;
}

function toggleSubModules(btn) {
  const container = btn.closest('.card-body').querySelector('.sub-modules-container');
  container.style.display = container.style.display === 'none' ? 'block' : 'none';
  const icon = btn.querySelector('i');
  icon.classList.toggle('bi-chevron-down');
  icon.classList.toggle('bi-chevron-up');
}

function addSubModule(btn) {
  const moduleItem = btn.closest('.module-item');
  const moduleIdx = parseInt(moduleItem.getAttribute('data-module-index'));
  const subModuleList = moduleItem.querySelector('.sub-modules-list');
  
  if (!subModuleIndex[moduleIdx]) {
    subModuleIndex[moduleIdx] = 0;
  }
  const subIdx = subModuleIndex[moduleIdx];
  
  const template = document.getElementById('subModuleTemplate');
  const clone = template.content.cloneNode(true);
  const subModuleItem = clone.querySelector('.sub-module-item');
  subModuleItem.setAttribute('data-sub-module-index', subIdx);
  subModuleItem.setAttribute('data-module-index', moduleIdx);
  subModuleItem.querySelector('.sub-module-number').textContent = subIdx + 1;
  
  updateSubModuleNames(subModuleItem, moduleIdx, subIdx);
  
  subModuleList.appendChild(clone);
  subModuleIndex[moduleIdx]++;
  
  if (!contentIndex[moduleIdx]) {
    contentIndex[moduleIdx] = {};
  }
  contentIndex[moduleIdx][subIdx] = 0;
  
  if (!quizIndex[moduleIdx]) {
    quizIndex[moduleIdx] = {};
  }
  quizIndex[moduleIdx][subIdx] = 0;
}

function updateSubModuleNames(subModuleItem, moduleIdx, subIdx) {
  const judulInput = subModuleItem.querySelector('.sub-module-judul');
  const deskripsiInput = subModuleItem.querySelector('.sub-module-deskripsi');
  const urutanInput = subModuleItem.querySelector('.sub-module-urutan');
  
  judulInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][judul]`;
  deskripsiInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][deskripsi]`;
  urutanInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][urutan]`;
  urutanInput.value = subIdx + 1;
  
  // Update contents
  const contents = subModuleItem.querySelectorAll('.content-item');
  contents.forEach((content, contentIdx) => {
    updateContentNames(content, moduleIdx, subIdx, contentIdx);
  });
  
  // Update quizzes
  const quizzes = subModuleItem.querySelectorAll('.quiz-item');
  quizzes.forEach((quiz, quizIdx) => {
    updateQuizNames(quiz, moduleIdx, subIdx, quizIdx);
  });
}

function removeSubModule(btn) {
  const subModuleItem = btn.closest('.sub-module-item');
  const moduleIdx = parseInt(subModuleItem.getAttribute('data-module-index'));
  const subIdx = parseInt(subModuleItem.getAttribute('data-sub-module-index'));
  if (contentIndex[moduleIdx]) {
    delete contentIndex[moduleIdx][subIdx];
  }
  if (quizIndex[moduleIdx]) {
    delete quizIndex[moduleIdx][subIdx];
  }
  subModuleItem.remove();
}

function toggleContents(btn) {
  const container = btn.closest('.card-body').querySelector('.contents-container');
  container.style.display = container.style.display === 'none' ? 'block' : 'none';
  const icon = btn.querySelector('i');
  icon.classList.toggle('bi-chevron-down');
  icon.classList.toggle('bi-chevron-up');
}

function toggleQuizzes(btn) {
  const container = btn.closest('.card-body').querySelector('.quizzes-container');
  container.style.display = container.style.display === 'none' ? 'block' : 'none';
  const icon = btn.querySelector('i');
  icon.classList.toggle('bi-chevron-down');
  icon.classList.toggle('bi-chevron-up');
}

function addContent(btn) {
  const subModuleItem = btn.closest('.sub-module-item');
  const moduleIdx = parseInt(subModuleItem.getAttribute('data-module-index'));
  const subIdx = parseInt(subModuleItem.getAttribute('data-sub-module-index'));
  const contentList = subModuleItem.querySelector('.contents-list');
  
  if (!contentIndex[moduleIdx]) {
    contentIndex[moduleIdx] = {};
  }
  if (!contentIndex[moduleIdx][subIdx]) {
    contentIndex[moduleIdx][subIdx] = 0;
  }
  const contentIdx = contentIndex[moduleIdx][subIdx];
  
  const template = document.getElementById('contentTemplate');
  const clone = template.content.cloneNode(true);
  const contentItem = clone.querySelector('.content-item');
  contentItem.setAttribute('data-content-index', contentIdx);
  contentItem.setAttribute('data-module-index', moduleIdx);
  contentItem.setAttribute('data-sub-module-index', subIdx);
  contentItem.querySelector('.content-number').textContent = contentIdx + 1;
  
  updateContentNames(contentItem, moduleIdx, subIdx, contentIdx);
  
  contentList.appendChild(clone);
  contentIndex[moduleIdx][subIdx]++;
}

function updateContentNames(contentItem, moduleIdx, subIdx, contentIdx) {
  const judulInput = contentItem.querySelector('.content-judul');
  const tipeInput = contentItem.querySelector('.content-tipe');
  const urutanInput = contentItem.querySelector('.content-urutan');
  const htmlContentInput = contentItem.querySelector('textarea[name*="html_content"]');
  const fileInput = contentItem.querySelector('.content-file');
  const urlInput = contentItem.querySelector('input[name*="external_url"]');
  const youtubeUrlInput = contentItem.querySelector('input[name*="youtube_url"]');
  
  judulInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][judul]`;
  tipeInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][tipe]`;
  urutanInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][urutan]`;
  urutanInput.value = contentIdx + 1;
  
  if (htmlContentInput) {
    htmlContentInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][html_content]`;
  }
  if (fileInput) {
    fileInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][file_path]`;
  }
  if (urlInput) {
    urlInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][external_url]`;
  }
  if (youtubeUrlInput) {
    youtubeUrlInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][youtube_url]`;
  }
}

function removeContent(btn) {
  const contentItem = btn.closest('.content-item');
  const moduleIdx = parseInt(contentItem.getAttribute('data-module-index'));
  const subIdx = parseInt(contentItem.getAttribute('data-sub-module-index'));
  const contentIdx = parseInt(contentItem.getAttribute('data-content-index'));
  contentItem.remove();
}

function addQuiz(btn) {
  const subModuleItem = btn.closest('.sub-module-item');
  const moduleIdx = parseInt(subModuleItem.getAttribute('data-module-index'));
  const subIdx = parseInt(subModuleItem.getAttribute('data-sub-module-index'));
  const quizList = subModuleItem.querySelector('.quizzes-list');
  
  if (!quizIndex[moduleIdx]) {
    quizIndex[moduleIdx] = {};
  }
  if (!quizIndex[moduleIdx][subIdx]) {
    quizIndex[moduleIdx][subIdx] = 0;
  }
  const quizIdx = quizIndex[moduleIdx][subIdx];
  
  const template = document.getElementById('quizTemplate');
  const clone = template.content.cloneNode(true);
  const quizItem = clone.querySelector('.quiz-item');
  quizItem.setAttribute('data-quiz-index', quizIdx);
  quizItem.setAttribute('data-module-index', moduleIdx);
  quizItem.setAttribute('data-sub-module-index', subIdx);
  quizItem.querySelector('.quiz-number').textContent = quizIdx + 1;
  
  updateQuizNames(quizItem, moduleIdx, subIdx, quizIdx);
  
  quizList.appendChild(clone);
  quizIndex[moduleIdx][subIdx]++;
}

function updateQuizNames(quizItem, moduleIdx, subIdx, quizIdx) {
  const judulInput = quizItem.querySelector('.quiz-judul');
  const deskripsiInput = quizItem.querySelector('.quiz-deskripsi');
  const nilaiMinimumInput = quizItem.querySelector('.quiz-nilai-minimum');
  const maxAttemptsInput = quizItem.querySelector('.quiz-max-attempts');
  
  judulInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][quizzes][${quizIdx}][judul]`;
  deskripsiInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][quizzes][${quizIdx}][deskripsi]`;
  nilaiMinimumInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][quizzes][${quizIdx}][nilai_minimum]`;
  maxAttemptsInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][quizzes][${quizIdx}][max_attempts]`;
  
  // Update question names
  const questions = quizItem.querySelectorAll('.question-item');
  questions.forEach((question, qIdx) => {
    updateQuestionNames(question, moduleIdx, subIdx, quizIdx, qIdx);
  });
}

function addQuestion(btn) {
  const quizItem = btn.closest('.quiz-item');
  const moduleIdx = parseInt(quizItem.getAttribute('data-module-index'));
  const subIdx = parseInt(quizItem.getAttribute('data-sub-module-index'));
  const quizIdx = parseInt(quizItem.getAttribute('data-quiz-index'));
  const questionsContainer = quizItem.querySelector('.questions-container');
  
  const key = `${moduleIdx}_${subIdx}_${quizIdx}`;
  if (!questionIndex[key]) {
    questionIndex[key] = 0;
  }
  const qIdx = questionIndex[key];
  
  const template = document.getElementById('questionTemplate');
  const clone = template.content.cloneNode(true);
  const questionItem = clone.querySelector('.question-item');
  questionItem.setAttribute('data-question-index', qIdx);
  questionItem.setAttribute('data-module-index', moduleIdx);
  questionItem.setAttribute('data-sub-module-index', subIdx);
  questionItem.setAttribute('data-quiz-index', quizIdx);
  questionItem.querySelector('.question-number').textContent = qIdx + 1;
  
  updateQuestionNames(questionItem, moduleIdx, subIdx, quizIdx, qIdx);
  
  questionsContainer.appendChild(clone);
  questionIndex[key]++;
  
  // Initialize answer options index for this question
  const questionKey = `${moduleIdx}_${subIdx}_${quizIdx}_${qIdx}`;
  if (!answerOptionIndex[questionKey]) {
    answerOptionIndex[questionKey] = 0;
  }
  
  // Add at least 2 answer options by default
  const answerOptionsContainer = questionItem.querySelector('.answer-options-container');
  const addOptionBtn = questionItem.querySelector('button[onclick*="addAnswerOption"]');
  for (let i = 0; i < 2; i++) {
    if (addOptionBtn) {
      addAnswerOption(addOptionBtn);
    }
  }
}

function updateQuestionNames(questionItem, moduleIdx, subIdx, quizIdx, qIdx) {
  const pertanyaanInput = questionItem.querySelector('.question-pertanyaan');
  const bobotInput = questionItem.querySelector('.question-bobot');
  const urutanInput = questionItem.querySelector('.question-urutan');
  const tipeInput = questionItem.querySelector('.question-tipe');
  
  pertanyaanInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][quizzes][${quizIdx}][questions][${qIdx}][pertanyaan]`;
  bobotInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][quizzes][${quizIdx}][questions][${qIdx}][bobot]`;
  urutanInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][quizzes][${quizIdx}][questions][${qIdx}][urutan]`;
  urutanInput.value = qIdx + 1;
  tipeInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][quizzes][${quizIdx}][questions][${qIdx}][tipe]`;
  
  // Update answer option names
  const answerOptions = questionItem.querySelectorAll('.answer-option-item');
  answerOptions.forEach((option, optIdx) => {
    updateAnswerOptionNames(option, moduleIdx, subIdx, quizIdx, qIdx, optIdx);
  });
}

function removeQuestion(btn) {
  const questionItem = btn.closest('.question-item');
  const moduleIdx = parseInt(questionItem.getAttribute('data-module-index'));
  const subIdx = parseInt(questionItem.getAttribute('data-sub-module-index'));
  const quizIdx = parseInt(questionItem.getAttribute('data-quiz-index'));
  const qIdx = parseInt(questionItem.getAttribute('data-question-index'));
  
  const key = `${moduleIdx}_${subIdx}_${quizIdx}`;
  const questionKey = `${moduleIdx}_${subIdx}_${quizIdx}_${qIdx}`;
  delete answerOptionIndex[questionKey];
  
  questionItem.remove();
  
  // Renumber remaining questions
  const quizItem = questionItem.closest('.quiz-item');
  const questions = quizItem.querySelectorAll('.question-item');
  questions.forEach((q, index) => {
    const newQIdx = index;
    q.setAttribute('data-question-index', newQIdx);
    q.querySelector('.question-number').textContent = newQIdx + 1;
    updateQuestionNames(q, moduleIdx, subIdx, quizIdx, newQIdx);
  });
}

function addAnswerOption(btn) {
  const questionItem = btn.closest('.question-item');
  const answerOptionsContainer = questionItem.querySelector('.answer-options-container');
  
  const moduleIdx = parseInt(questionItem.getAttribute('data-module-index'));
  const subIdx = parseInt(questionItem.getAttribute('data-sub-module-index'));
  const quizIdx = parseInt(questionItem.getAttribute('data-quiz-index'));
  const qIdx = parseInt(questionItem.getAttribute('data-question-index'));
  
  const questionKey = `${moduleIdx}_${subIdx}_${quizIdx}_${qIdx}`;
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
  
  updateAnswerOptionNames(optionItem, moduleIdx, subIdx, quizIdx, qIdx, optIdx);
  
  answerOptionsContainer.appendChild(clone);
  answerOptionIndex[questionKey]++;
}

function updateAnswerOptionNames(optionItem, moduleIdx, subIdx, quizIdx, qIdx, optIdx) {
  const textInput = optionItem.querySelector('.answer-option-text');
  const correctInput = optionItem.querySelector('.answer-option-correct');
  
  textInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][quizzes][${quizIdx}][questions][${qIdx}][answer_options][${optIdx}][teks_jawaban]`;
  correctInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][quizzes][${quizIdx}][questions][${qIdx}][answer_options][${optIdx}][is_correct]`;
}

function removeAnswerOption(btn) {
  const optionItem = btn.closest('.answer-option-item');
  const questionItem = optionItem.closest('.question-item');
  const moduleIdx = parseInt(questionItem.getAttribute('data-module-index'));
  const subIdx = parseInt(questionItem.getAttribute('data-sub-module-index'));
  const quizIdx = parseInt(questionItem.getAttribute('data-quiz-index'));
  const qIdx = parseInt(questionItem.getAttribute('data-question-index'));
  
  const answerOptionsContainer = questionItem.querySelector('.answer-options-container');
  const existingOptions = answerOptionsContainer.querySelectorAll('.answer-option-item');
  
  // Check min 2 options
  if (existingOptions.length <= 2) {
    alert('Minimal 2 opsi jawaban per pertanyaan.');
    return;
  }
  
  optionItem.remove();
  
  // Renumber remaining options
  const remainingOptions = answerOptionsContainer.querySelectorAll('.answer-option-item');
  remainingOptions.forEach((opt, index) => {
    const newOptIdx = index;
    opt.setAttribute('data-option-index', newOptIdx);
    updateAnswerOptionNames(opt, moduleIdx, subIdx, quizIdx, qIdx, newOptIdx);
  });
}

function validateCorrectAnswer(checkbox) {
  const questionItem = checkbox.closest('.question-item');
  const allCheckboxes = questionItem.querySelectorAll('.answer-option-correct');
  
  if (checkbox.checked) {
    // Uncheck all other checkboxes
    allCheckboxes.forEach(cb => {
      if (cb !== checkbox) {
        cb.checked = false;
      }
    });
  } else {
    // Ensure at least one is checked
    const hasChecked = Array.from(allCheckboxes).some(cb => cb.checked);
    if (!hasChecked) {
      alert('Setidaknya satu opsi harus dipilih sebagai jawaban benar.');
      checkbox.checked = true;
    }
  }
}

function removeQuiz(btn) {
  const quizItem = btn.closest('.quiz-item');
  quizItem.remove();
}

function toggleContentFields(select) {
  const contentItem = select.closest('.content-item');
  const htmlField = contentItem.querySelector('.content-html-field');
  const fileField = contentItem.querySelector('.content-file-field');
  const urlField = contentItem.querySelector('.content-url-field');
  const youtubeField = contentItem.querySelector('.content-youtube-field');
  const requiredDurationField = contentItem.querySelector('.content-required-duration-field');
  
  // Hide all fields
  htmlField.style.display = 'none';
  fileField.style.display = 'none';
  urlField.style.display = 'none';
  if (youtubeField) youtubeField.style.display = 'none';
  if (requiredDurationField) requiredDurationField.style.display = 'none';
  
  // Show relevant field based on type
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

function updateReview() {
  document.getElementById('reviewJudul').textContent = document.querySelector('input[name="judul"]').value;
  document.getElementById('reviewDeskripsi').textContent = document.querySelector('textarea[name="deskripsi"]').value;
  document.getElementById('reviewJpValue').textContent = document.querySelector('input[name="jp_value"]').value;
  document.getElementById('reviewBidangKompetensi').textContent = document.querySelector('input[name="bidang_kompetensi"]').value;
  
  const modulesContainer = document.getElementById('reviewModules');
  modulesContainer.innerHTML = '';
  const modules = document.querySelectorAll('.module-item');
  modules.forEach((module, idx) => {
    const judul = module.querySelector('.module-judul').value;
    const subModules = module.querySelectorAll('.sub-module-item');
    const moduleDiv = document.createElement('div');
    moduleDiv.className = 'mb-2';
    moduleDiv.innerHTML = `<strong>Module ${idx + 1}:</strong> ${judul} (${subModules.length} sub-modules)`;
    modulesContainer.appendChild(moduleDiv);
  });
}

// Initialize with one module
document.addEventListener('DOMContentLoaded', function() {
  addModule();
  
  // Ensure all form fields are submitted even if hidden
  const form = document.getElementById('courseWizardForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      // Show all wizard steps to ensure all fields are included
      document.querySelectorAll('.wizard-step').forEach(step => {
        step.style.display = 'block';
      });
      
      // Show all collapsed containers
      document.querySelectorAll('.sub-modules-container, .contents-container, .quizzes-container').forEach(container => {
        container.style.display = 'block';
      });
      
      // Show all content type fields
      document.querySelectorAll('.content-html-field, .content-file-field, .content-url-field, .content-youtube-field, .content-required-duration-field').forEach(field => {
        field.style.display = 'block';
      });
      
      // Validate before submit
      if (!validateStep1() || !validateStep2()) {
        e.preventDefault();
        alert('Please fill in all required fields before submitting.');
        return false;
      }
    });
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

.module-item, .sub-module-item, .content-item {
  border-left: 4px solid #007bff;
}

.sub-module-item {
  border-left-color: #17a2b8;
}

.content-item {
  border-left-color: #ffc107;
}

.quiz-item {
  border-left-color: #17a2b8;
}
</style>
@endsection

