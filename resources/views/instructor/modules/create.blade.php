@extends('layouts.instructor')

@section('title','Tambah Module')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $course) }}">{{ $course->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Module Create</li>
  </ol>
  {{-- Binding: $course --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Module Creation Wizard</h5>
      <small>Buat module lengkap dengan sub-modules dan contents dalam satu alur</small>
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
      
      <form action="{{ route('instructor.modules.store', $course->id) }}" method="post" enctype="multipart/form-data" id="moduleWizardForm">
        @csrf
        
        <!-- Step 1: Module Information -->
        <div class="wizard-step" id="step1">
          <h4 class="mb-4">Step 1: Module Information</h4>
          
          <div class="mb-3">
            <label class="form-label">Judul Module <span class="text-danger">*</span></label>
            <input type="text" name="judul" value="{{ old('judul') }}" class="form-control" required>
            @error('judul')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Urutan <span class="text-danger">*</span></label>
            <input type="number" name="urutan" value="{{ old('urutan') }}" class="form-control" min="1" required>
            @error('urutan')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="d-flex justify-content-end mt-4">
            <button type="button" class="btn btn-primary" onclick="nextStep(2)">Next: Add Sub-Modules →</button>
          </div>
        </div>

        <!-- Step 2: Sub-Modules -->
        <div class="wizard-step" id="step2" style="display: none;">
          <h4 class="mb-4">Step 2: Sub-Modules</h4>
          <p class="text-muted mb-4">Tambahkan sub-modules untuk module ini (opsional).</p>
          
          <div id="subModulesContainer">
            <!-- Sub-modules will be added here dynamically -->
          </div>

          <button type="button" class="btn btn-success mb-4" onclick="addSubModule()">
            <i class="bi bi-plus-circle"></i> Tambah Sub-Module
          </button>

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="prevStep(1)">← Back</button>
            <button type="button" class="btn btn-primary" onclick="nextStep(3)">Next: Review →</button>
          </div>
        </div>

        <!-- Step 3: Review and Submit -->
        <div class="wizard-step" id="step3" style="display: none;">
          <h4 class="mb-4">Step 3: Review and Submit</h4>
          <p class="text-muted mb-4">Tinjau informasi module Anda sebelum menyimpan.</p>
          
          <div class="card mb-3">
            <div class="card-header">Module Information</div>
            <div class="card-body">
              <p><strong>Judul:</strong> <span id="reviewJudul"></span></p>
              <p><strong>Deskripsi:</strong> <span id="reviewDeskripsi"></span></p>
              <p><strong>Urutan:</strong> <span id="reviewUrutan"></span></p>
            </div>
          </div>

          <div class="card mb-3">
            <div class="card-header">Sub-Modules Summary</div>
            <div class="card-body" id="reviewSubModules">
              <!-- Sub-modules summary will be shown here -->
            </div>
          </div>

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="prevStep(2)">← Back</button>
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check-circle"></i> Create Module
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

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
        <input type="text" name="sub_modules[][judul]" class="form-control sub-module-judul" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Deskripsi</label>
        <textarea name="sub_modules[][deskripsi]" class="form-control sub-module-deskripsi" rows="2"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Urutan <span class="text-danger">*</span></label>
        <input type="number" name="sub_modules[][urutan]" class="form-control sub-module-urutan" min="1" required>
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
    </div>
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
        <input type="text" name="sub_modules[][contents][][judul]" class="form-control content-judul" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Tipe Content <span class="text-danger">*</span></label>
        <select name="sub_modules[][contents][][tipe]" class="form-control content-tipe" required onchange="toggleContentFields(this)">
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
        <input type="number" name="sub_modules[][contents][][urutan]" class="form-control content-urutan" min="1" required>
      </div>
      
      <!-- HTML Content Field -->
      <div class="mb-3 content-html-field" style="display: none;">
        <label class="form-label">HTML Content <span class="text-danger">*</span></label>
        <textarea name="sub_modules[][contents][][html_content]" class="form-control" rows="5"></textarea>
      </div>
      
      <!-- File Upload Field -->
      <div class="mb-3 content-file-field" style="display: none;">
        <label class="form-label">Upload File <span class="text-danger">*</span></label>
        <input type="file" name="sub_modules[][contents][][file_path]" class="form-control content-file">
      </div>
      
      <!-- External URL Field -->
      <div class="mb-3 content-url-field" style="display: none;">
        <label class="form-label">External URL <span class="text-danger">*</span></label>
        <input type="url" name="sub_modules[][contents][][external_url]" class="form-control" placeholder="https://example.com/file.pdf">
      </div>
      
      <!-- YouTube URL Field -->
      <div class="mb-3 content-youtube-field" style="display: none;">
        <label class="form-label">YouTube URL <span class="text-danger">*</span></label>
        <input type="url" name="sub_modules[][contents][][youtube_url]" class="form-control" placeholder="https://www.youtube.com/watch?v=VIDEO_ID atau https://youtu.be/VIDEO_ID">
      </div>
      
      <!-- Required Duration Field (for YouTube videos) -->
      <div class="mb-3 content-required-duration-field" style="display: none;">
        <label class="form-label">Durasi Video yang Diperlukan (detik) <span class="text-danger">*</span></label>
        <input type="number" name="sub_modules[][contents][][required_duration]" class="form-control" min="1" placeholder="Contoh: 300 (untuk 5 menit)">
        <small class="text-muted">Masukkan durasi video dalam detik. Siswa harus menonton video selama durasi ini sebelum dapat melanjutkan ke konten berikutnya.</small>
      </div>
    </div>
  </div>
</template>

<script>
let subModuleIndex = 0;
let contentIndex = {};

function nextStep(step) {
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
  // Sub-modules are optional, so validation passes if at least module info is filled
  return true;
}

function addSubModule() {
  const template = document.getElementById('subModuleTemplate');
  const clone = template.content.cloneNode(true);
  const subModuleItem = clone.querySelector('.sub-module-item');
  subModuleItem.setAttribute('data-sub-module-index', subModuleIndex);
  subModuleItem.querySelector('.sub-module-number').textContent = subModuleIndex + 1;
  
  const judulInput = subModuleItem.querySelector('.sub-module-judul');
  const deskripsiInput = subModuleItem.querySelector('.sub-module-deskripsi');
  const urutanInput = subModuleItem.querySelector('.sub-module-urutan');
  
  judulInput.name = `sub_modules[${subModuleIndex}][judul]`;
  deskripsiInput.name = `sub_modules[${subModuleIndex}][deskripsi]`;
  urutanInput.name = `sub_modules[${subModuleIndex}][urutan]`;
  urutanInput.value = subModuleIndex + 1;
  
  contentIndex[subModuleIndex] = 0;
  
  document.getElementById('subModulesContainer').appendChild(clone);
  subModuleIndex++;
}

function removeSubModule(btn) {
  const subModuleItem = btn.closest('.sub-module-item');
  const subIdx = parseInt(subModuleItem.getAttribute('data-sub-module-index'));
  delete contentIndex[subIdx];
  subModuleItem.remove();
  renumberSubModules();
}

function renumberSubModules() {
  const subModules = document.querySelectorAll('.sub-module-item');
  subModules.forEach((subModule, index) => {
    subModule.setAttribute('data-sub-module-index', index);
    subModule.querySelector('.sub-module-number').textContent = index + 1;
    const judulInput = subModule.querySelector('.sub-module-judul');
    const deskripsiInput = subModule.querySelector('.sub-module-deskripsi');
    const urutanInput = subModule.querySelector('.sub-module-urutan');
    const subIdx = index;
    judulInput.name = `sub_modules[${subIdx}][judul]`;
    deskripsiInput.name = `sub_modules[${subIdx}][deskripsi]`;
    urutanInput.name = `sub_modules[${subIdx}][urutan]`;
    urutanInput.value = subIdx + 1;
    
    const contents = subModule.querySelectorAll('.content-item');
    contents.forEach((content, contentIdx) => {
      updateContentNames(content, subIdx, contentIdx);
    });
  });
  subModuleIndex = subModules.length;
}

function toggleContents(btn) {
  const container = btn.closest('.card-body').querySelector('.contents-container');
  container.style.display = container.style.display === 'none' ? 'block' : 'none';
  const icon = btn.querySelector('i');
  icon.classList.toggle('bi-chevron-down');
  icon.classList.toggle('bi-chevron-up');
}

function addContent(btn) {
  const subModuleItem = btn.closest('.sub-module-item');
  const subIdx = parseInt(subModuleItem.getAttribute('data-sub-module-index'));
  const contentList = subModuleItem.querySelector('.contents-list');
  
  if (!contentIndex[subIdx]) {
    contentIndex[subIdx] = 0;
  }
  const contentIdx = contentIndex[subIdx];
  
  const template = document.getElementById('contentTemplate');
  const clone = template.content.cloneNode(true);
  const contentItem = clone.querySelector('.content-item');
  contentItem.setAttribute('data-content-index', contentIdx);
  contentItem.setAttribute('data-sub-module-index', subIdx);
  contentItem.querySelector('.content-number').textContent = contentIdx + 1;
  
  updateContentNames(contentItem, subIdx, contentIdx);
  
  contentList.appendChild(clone);
  contentIndex[subIdx]++;
}

function updateContentNames(contentItem, subIdx, contentIdx) {
  const judulInput = contentItem.querySelector('.content-judul');
  const tipeInput = contentItem.querySelector('.content-tipe');
  const urutanInput = contentItem.querySelector('.content-urutan');
  const htmlContentInput = contentItem.querySelector('textarea[name*="html_content"]');
  const fileInput = contentItem.querySelector('.content-file');
  const urlInput = contentItem.querySelector('input[name*="external_url"]');
  const youtubeUrlInput = contentItem.querySelector('input[name*="youtube_url"]');
  
  judulInput.name = `sub_modules[${subIdx}][contents][${contentIdx}][judul]`;
  tipeInput.name = `sub_modules[${subIdx}][contents][${contentIdx}][tipe]`;
  urutanInput.name = `sub_modules[${subIdx}][contents][${contentIdx}][urutan]`;
  urutanInput.value = contentIdx + 1;
  
  if (htmlContentInput) {
    htmlContentInput.name = `sub_modules[${subIdx}][contents][${contentIdx}][html_content]`;
  }
  if (fileInput) {
    fileInput.name = `sub_modules[${subIdx}][contents][${contentIdx}][file_path]`;
  }
  if (urlInput) {
    urlInput.name = `sub_modules[${subIdx}][contents][${contentIdx}][external_url]`;
  }
  if (youtubeUrlInput) {
    youtubeUrlInput.name = `sub_modules[${subIdx}][contents][${contentIdx}][youtube_url]`;
  }
}

function removeContent(btn) {
  const contentItem = btn.closest('.content-item');
  contentItem.remove();
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

function updateReview() {
  document.getElementById('reviewJudul').textContent = document.querySelector('input[name="judul"]').value;
  document.getElementById('reviewDeskripsi').textContent = document.querySelector('textarea[name="deskripsi"]').value || '(Tidak ada deskripsi)';
  document.getElementById('reviewUrutan').textContent = document.querySelector('input[name="urutan"]').value;
  
  const subModulesContainer = document.getElementById('reviewSubModules');
  subModulesContainer.innerHTML = '';
  const subModules = document.querySelectorAll('.sub-module-item');
  if (subModules.length === 0) {
    subModulesContainer.innerHTML = '<p class="text-muted">Tidak ada sub-module</p>';
  } else {
    subModules.forEach((subModule, idx) => {
      const judul = subModule.querySelector('.sub-module-judul').value;
      const contents = subModule.querySelectorAll('.content-item');
      const subModuleDiv = document.createElement('div');
      subModuleDiv.className = 'mb-2';
      subModuleDiv.innerHTML = `<strong>Sub-Module ${idx + 1}:</strong> ${judul} (${contents.length} contents)`;
      subModulesContainer.appendChild(subModuleDiv);
    });
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('moduleWizardForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      document.querySelectorAll('.wizard-step').forEach(step => {
        step.style.display = 'block';
      });
      
      document.querySelectorAll('.sub-modules-container, .contents-container').forEach(container => {
        container.style.display = 'block';
      });
      
      document.querySelectorAll('.content-html-field, .content-file-field, .content-url-field, .content-youtube-field, .content-required-duration-field').forEach(field => {
        field.style.display = 'block';
      });
      
      if (!validateStep1()) {
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

.sub-module-item, .content-item {
  border-left: 4px solid #17a2b8;
}

.content-item {
  border-left-color: #ffc107;
}
</style>
@endsection
