@extends('layouts.instructor')

@section('title','Edit Course')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}">Courses</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
  {{-- Binding: $course --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Course Edit Wizard</h5>
      <small>Edit course lengkap dengan modules, sub-modules, dan contents dalam satu alur</small>
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
      
      <form action="{{ route('instructor.courses.update', $course->id) }}" method="post" enctype="multipart/form-data" id="courseWizardForm">
        @csrf
        @method('PUT')
        
        <!-- Step 1: Course Information -->
        <div class="wizard-step" id="step1">
          <h4 class="mb-4">Step 1: Course Information</h4>
          
          <div class="mb-3">
            <label class="form-label">Judul Course <span class="text-danger">*</span></label>
            <input type="text" name="judul" value="{{ old('judul', $course->judul) }}" class="form-control" required>
            @error('judul')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
            <textarea name="deskripsi" class="form-control" rows="4" required>{{ old('deskripsi', $course->deskripsi) }}</textarea>
            @error('deskripsi')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">JP Value <span class="text-danger">*</span></label>
              <input type="number" name="jp_value" value="{{ old('jp_value', $course->jp_value) }}" class="form-control" min="1" required>
              @error('jp_value')<small class="text-danger d-block">{{ $message }}</small>@enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Bidang Kompetensi <span class="text-danger">*</span></label>
              <input type="text" name="bidang_kompetensi" value="{{ old('bidang_kompetensi', $course->bidang_kompetensi) }}" class="form-control" required>
              @error('bidang_kompetensi')<small class="text-danger d-block">{{ $message }}</small>@enderror
            </div>
          </div>

          <div class="d-flex justify-content-end mt-4">
            <button type="button" class="btn btn-primary" onclick="nextStep(2)">Next: Manage Modules →</button>
          </div>
        </div>

        <!-- Step 2: Modules -->
        <div class="wizard-step" id="step2" style="display: none;">
          <h4 class="mb-4">Step 2: Modules</h4>
          <p class="text-muted mb-4">Kelola modules untuk course ini.</p>
          
          <div id="modulesContainer">
            <!-- Existing and new modules will be added here dynamically -->
          </div>

          <button type="button" class="btn btn-success mb-4" onclick="addModule()">
            <i class="bi bi-plus-circle"></i> Tambah Module Baru
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
              <i class="bi bi-check-circle"></i> Update Course
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Module Template (Hidden) -->
<template id="moduleTemplate">
  <div class="card mb-3 module-item" data-module-index="" data-module-id="">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>Module <span class="module-number"></span> <span class="module-status-badge"></span></span>
      <button type="button" class="btn btn-sm btn-danger" onclick="removeModule(this)">
        <i class="bi bi-trash"></i> Hapus
      </button>
    </div>
    <div class="card-body">
      <input type="hidden" name="modules[][id]" class="module-id" value="">
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
  <div class="card mb-3 sub-module-item" data-sub-module-index="" data-sub-module-id="">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <span>Sub-Module <span class="sub-module-number"></span> <span class="sub-module-status-badge"></span></span>
      <button type="button" class="btn btn-sm btn-danger" onclick="removeSubModule(this)">
        <i class="bi bi-trash"></i> Hapus
      </button>
    </div>
    <div class="card-body">
      <input type="hidden" name="modules[][sub_modules][][id]" class="sub-module-id" value="">
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
    </div>
  </div>
</template>

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
      <input type="hidden" name="modules[][sub_modules][][contents][][id]" class="content-id" value="">
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
        <div class="current-file-info" style="display: none;">
          <label class="form-label">File Saat Ini</label>
          <div class="mb-2">
            <a href="" class="current-file-link" target="_blank"></a>
          </div>
        </div>
        <label class="form-label">Upload File Baru (opsional)</label>
        <input type="file" name="modules[][sub_modules][][contents][][file_path]" class="form-control content-file">
        <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
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
let moduleIndex = {{ $course->modules->count() }};
let subModuleIndex = {};
let contentIndex = {};
const existingModules = @json($course->modules);

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

function loadExistingModules() {
  existingModules.forEach((moduleData, idx) => {
    addModuleFromData(moduleData, idx);
  });
}

function addModuleFromData(moduleData, index) {
  const template = document.getElementById('moduleTemplate');
  const clone = template.content.cloneNode(true);
  const moduleItem = clone.querySelector('.module-item');
  moduleItem.setAttribute('data-module-index', index);
  moduleItem.setAttribute('data-module-id', moduleData.id);
  moduleItem.querySelector('.module-number').textContent = index + 1;
  moduleItem.querySelector('.module-status-badge').innerHTML = '<span class="badge bg-info">Existing</span>';
  
  const idInput = moduleItem.querySelector('.module-id');
  const judulInput = moduleItem.querySelector('.module-judul');
  const deskripsiInput = moduleItem.querySelector('.module-deskripsi');
  const urutanInput = moduleItem.querySelector('.module-urutan');
  
  idInput.name = `modules[${index}][id]`;
  idInput.value = moduleData.id;
  judulInput.name = `modules[${index}][judul]`;
  judulInput.value = moduleData.judul;
  deskripsiInput.name = `modules[${index}][deskripsi]`;
  deskripsiInput.value = moduleData.deskripsi || '';
  urutanInput.name = `modules[${index}][urutan]`;
  urutanInput.value = moduleData.urutan;
  
  // Load existing sub-modules
  if (moduleData.sub_modules && moduleData.sub_modules.length > 0) {
    if (!subModuleIndex[index]) {
      subModuleIndex[index] = 0;
    }
    moduleData.sub_modules.forEach((subModuleData, subIdx) => {
      addSubModuleFromData(subModuleData, index, subIdx);
      subModuleIndex[index]++;
    });
  } else {
    subModuleIndex[index] = 0;
  }
  
  if (!contentIndex[index]) {
    contentIndex[index] = {};
  }
  
  document.getElementById('modulesContainer').appendChild(clone);
}

function addSubModuleFromData(subModuleData, moduleIdx, subIdx) {
  const moduleItem = document.querySelector(`[data-module-index="${moduleIdx}"]`);
  const subModuleList = moduleItem.querySelector('.sub-modules-list');
  
  const template = document.getElementById('subModuleTemplate');
  const clone = template.content.cloneNode(true);
  const subModuleItem = clone.querySelector('.sub-module-item');
  subModuleItem.setAttribute('data-sub-module-index', subIdx);
  subModuleItem.setAttribute('data-module-index', moduleIdx);
  subModuleItem.setAttribute('data-sub-module-id', subModuleData.id);
  subModuleItem.querySelector('.sub-module-number').textContent = subIdx + 1;
  subModuleItem.querySelector('.sub-module-status-badge').innerHTML = '<span class="badge bg-info">Existing</span>';
  
  const idInput = subModuleItem.querySelector('.sub-module-id');
  const judulInput = subModuleItem.querySelector('.sub-module-judul');
  const deskripsiInput = subModuleItem.querySelector('.sub-module-deskripsi');
  const urutanInput = subModuleItem.querySelector('.sub-module-urutan');
  
  idInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][id]`;
  idInput.value = subModuleData.id;
  judulInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][judul]`;
  judulInput.value = subModuleData.judul;
  deskripsiInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][deskripsi]`;
  deskripsiInput.value = subModuleData.deskripsi || '';
  urutanInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][urutan]`;
  urutanInput.value = subModuleData.urutan;
  
  // Load existing contents
  if (subModuleData.contents && subModuleData.contents.length > 0) {
    if (!contentIndex[moduleIdx]) {
      contentIndex[moduleIdx] = {};
    }
    if (!contentIndex[moduleIdx][subIdx]) {
      contentIndex[moduleIdx][subIdx] = 0;
    }
    subModuleData.contents.forEach((contentData, contentIdx) => {
      addContentFromData(contentData, moduleIdx, subIdx, contentIdx);
      contentIndex[moduleIdx][subIdx]++;
    });
  } else {
    if (!contentIndex[moduleIdx]) {
      contentIndex[moduleIdx] = {};
    }
    contentIndex[moduleIdx][subIdx] = 0;
  }
  
  subModuleList.appendChild(clone);
}

function addContentFromData(contentData, moduleIdx, subIdx, contentIdx) {
  const moduleItem = document.querySelector(`[data-module-index="${moduleIdx}"]`);
  if (!moduleItem) return;
  const subModuleItem = moduleItem.querySelector(`[data-sub-module-index="${subIdx}"]`);
  if (!subModuleItem) return;
  const contentList = subModuleItem.querySelector('.contents-list');
  
  const template = document.getElementById('contentTemplate');
  const clone = template.content.cloneNode(true);
  const contentItem = clone.querySelector('.content-item');
  contentItem.setAttribute('data-content-index', contentIdx);
  contentItem.setAttribute('data-module-index', moduleIdx);
  contentItem.setAttribute('data-sub-module-index', subIdx);
  contentItem.setAttribute('data-content-id', contentData.id);
  contentItem.querySelector('.content-number').textContent = contentIdx + 1;
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
  
  idInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][id]`;
  idInput.value = contentData.id;
  judulInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][judul]`;
  judulInput.value = contentData.judul;
  tipeInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][tipe]`;
  tipeInput.value = contentData.tipe;
  urutanInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][urutan]`;
  urutanInput.value = contentData.urutan;
  
  if (htmlContentInput) {
    htmlContentInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][html_content]`;
    htmlContentInput.value = contentData.html_content || '';
  }
  if (fileInput) {
    fileInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][file_path]`;
    if (contentData.file_path) {
      currentFileInfo.style.display = 'block';
      currentFileLink.href = `/instructor/contents/${contentData.id}/download`;
      currentFileLink.textContent = contentData.file_path.split('/').pop();
    }
  }
  if (urlInput) {
    urlInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][external_url]`;
    urlInput.value = contentData.external_url || '';
  }
  if (youtubeUrlInput) {
    youtubeUrlInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][youtube_url]`;
    youtubeUrlInput.value = contentData.youtube_url || '';
  }
  if (requiredDurationInput) {
    requiredDurationInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][required_duration]`;
    requiredDurationInput.value = contentData.required_duration || '';
  }
  
  toggleContentFields(tipeInput);
  contentList.appendChild(clone);
}

function addModule() {
  const template = document.getElementById('moduleTemplate');
  const clone = template.content.cloneNode(true);
  const moduleItem = clone.querySelector('.module-item');
  moduleItem.setAttribute('data-module-index', moduleIndex);
  moduleItem.setAttribute('data-module-id', '');
  moduleItem.querySelector('.module-number').textContent = moduleIndex + 1;
  moduleItem.querySelector('.module-status-badge').innerHTML = '<span class="badge bg-success">New</span>';
  
  const idInput = moduleItem.querySelector('.module-id');
  const judulInput = moduleItem.querySelector('.module-judul');
  const deskripsiInput = moduleItem.querySelector('.module-deskripsi');
  const urutanInput = moduleItem.querySelector('.module-urutan');
  
  idInput.name = `modules[${moduleIndex}][id]`;
  idInput.value = '';
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
    const idInput = module.querySelector('.module-id');
    const judulInput = module.querySelector('.module-judul');
    const deskripsiInput = module.querySelector('.module-deskripsi');
    const urutanInput = module.querySelector('.module-urutan');
    const moduleIdx = index;
    if (idInput) {
      idInput.name = `modules[${moduleIdx}][id]`;
    }
    judulInput.name = `modules[${moduleIdx}][judul]`;
    deskripsiInput.name = `modules[${moduleIdx}][deskripsi]`;
    urutanInput.name = `modules[${moduleIdx}][urutan]`;
    urutanInput.value = moduleIdx + 1;
    
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
  subModuleItem.setAttribute('data-sub-module-id', '');
  subModuleItem.querySelector('.sub-module-number').textContent = subIdx + 1;
  subModuleItem.querySelector('.sub-module-status-badge').innerHTML = '<span class="badge bg-success">New</span>';
  
  updateSubModuleNames(subModuleItem, moduleIdx, subIdx);
  
  subModuleList.appendChild(clone);
  subModuleIndex[moduleIdx]++;
  
  if (!contentIndex[moduleIdx]) {
    contentIndex[moduleIdx] = {};
  }
  contentIndex[moduleIdx][subIdx] = 0;
}

function updateSubModuleNames(subModuleItem, moduleIdx, subIdx) {
  const idInput = subModuleItem.querySelector('.sub-module-id');
  const judulInput = subModuleItem.querySelector('.sub-module-judul');
  const deskripsiInput = subModuleItem.querySelector('.sub-module-deskripsi');
  const urutanInput = subModuleItem.querySelector('.sub-module-urutan');
  
  if (idInput) {
    idInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][id]`;
  }
  judulInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][judul]`;
  deskripsiInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][deskripsi]`;
  urutanInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][urutan]`;
  urutanInput.value = subIdx + 1;
  
  const contents = subModuleItem.querySelectorAll('.content-item');
  contents.forEach((content, contentIdx) => {
    updateContentNames(content, moduleIdx, subIdx, contentIdx);
  });
}

function removeSubModule(btn) {
  const subModuleItem = btn.closest('.sub-module-item');
  const moduleIdx = parseInt(subModuleItem.getAttribute('data-module-index'));
  const subIdx = parseInt(subModuleItem.getAttribute('data-sub-module-index'));
  delete contentIndex[moduleIdx][subIdx];
  subModuleItem.remove();
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
  contentItem.setAttribute('data-content-id', '');
  contentItem.querySelector('.content-number').textContent = contentIdx + 1;
  contentItem.querySelector('.content-status-badge').innerHTML = '<span class="badge bg-success">New</span>';
  
  updateContentNames(contentItem, moduleIdx, subIdx, contentIdx);
  
  contentList.appendChild(clone);
  contentIndex[moduleIdx][subIdx]++;
}

function updateContentNames(contentItem, moduleIdx, subIdx, contentIdx) {
  const idInput = contentItem.querySelector('.content-id');
  const judulInput = contentItem.querySelector('.content-judul');
  const tipeInput = contentItem.querySelector('.content-tipe');
  const urutanInput = contentItem.querySelector('.content-urutan');
  const htmlContentInput = contentItem.querySelector('textarea[name*="html_content"]');
  const fileInput = contentItem.querySelector('.content-file');
  const urlInput = contentItem.querySelector('input[name*="external_url"]');
  const youtubeUrlInput = contentItem.querySelector('input[name*="youtube_url"]');
  const requiredDurationInput = contentItem.querySelector('input[name*="required_duration"]');
  
  if (idInput) {
    idInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][id]`;
  }
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
  if (requiredDurationInput) {
    requiredDurationInput.name = `modules[${moduleIdx}][sub_modules][${subIdx}][contents][${contentIdx}][required_duration]`;
  }
}

function removeContent(btn) {
  const contentItem = btn.closest('.content-item');
  const moduleIdx = parseInt(contentItem.getAttribute('data-module-index'));
  const subIdx = parseInt(contentItem.getAttribute('data-sub-module-index'));
  const contentIdx = parseInt(contentItem.getAttribute('data-content-index'));
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
  document.getElementById('reviewDeskripsi').textContent = document.querySelector('textarea[name="deskripsi"]').value;
  document.getElementById('reviewJpValue').textContent = document.querySelector('input[name="jp_value"]').value;
  document.getElementById('reviewBidangKompetensi').textContent = document.querySelector('input[name="bidang_kompetensi"]').value;
  
  const modulesContainer = document.getElementById('reviewModules');
  modulesContainer.innerHTML = '';
  const modules = document.querySelectorAll('.module-item');
  modules.forEach((module, idx) => {
    const judul = module.querySelector('.module-judul').value;
    const subModules = module.querySelectorAll('.sub-module-item');
    const isExisting = module.getAttribute('data-module-id') !== '';
    const status = isExisting ? '(Existing)' : '(New)';
    const moduleDiv = document.createElement('div');
    moduleDiv.className = 'mb-2';
    moduleDiv.innerHTML = `<strong>Module ${idx + 1}:</strong> ${judul} (${subModules.length} sub-modules) ${status}`;
    modulesContainer.appendChild(moduleDiv);
  });
}

document.addEventListener('DOMContentLoaded', function() {
  loadExistingModules();
  
  const form = document.getElementById('courseWizardForm');
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
</style>
@endsection
