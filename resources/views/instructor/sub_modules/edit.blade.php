@extends('layouts.instructor')

@section('title','Edit Sub-Module')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $subModule->module->course) }}">{{ $subModule->module->course->judul }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.modules.show', $subModule->module) }}">{{ $subModule->module->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sub-Module Edit</li>
  </ol>
  {{-- Binding: $subModule --}}
</nav>
@endsection

@section('content')
@php
  $subModule->load('contents');
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
            <button type="button" class="btn btn-primary" onclick="nextStep(3)">Next: Review →</button>
          </div>
        </div>

        <!-- Step 3: Review and Submit -->
        <div class="wizard-step" id="step3" style="display: none;">
          <h4 class="mb-4">Step 3: Review and Submit</h4>
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

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="prevStep(2)">← Back</button>
            <button type="submit" class="btn btn-success">
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

<script>
let contentIndex = {{ $subModule->contents->count() }};
const existingContents = @json($subModule->contents);

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
  // Contents are optional, so validation passes
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
}

document.addEventListener('DOMContentLoaded', function() {
  loadExistingContents();
  
  const form = document.getElementById('subModuleWizardForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      document.querySelectorAll('.wizard-step').forEach(step => {
        step.style.display = 'block';
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

.content-item {
  border-left: 4px solid #ffc107;
}
</style>
@endsection
