@extends('layouts.instructor')

@section('title','Edit Content')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.modules.show', $content->subModule->module) }}">{{ $content->subModule->module->judul }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.sub_modules.show', $content->subModule) }}">{{ $content->subModule->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Content Edit</li>
  </ol>
  {{-- Binding: $content --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Content Edit Wizard</h5>
      <small>Edit content dengan langkah-langkah yang terstruktur</small>
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
      
      <form action="{{ route('instructor.contents.update', $content->id) }}" method="post" enctype="multipart/form-data" id="contentWizardForm">
        @csrf
        @method('PUT')
        
        <!-- Step 1: Content Information -->
        <div class="wizard-step" id="step1">
          <h4 class="mb-4">Step 1: Content Information</h4>
          
          <div class="mb-3">
            <label class="form-label">Judul Content <span class="text-danger">*</span></label>
            <input type="text" name="judul" value="{{ old('judul', $content->judul) }}" class="form-control" required>
            @error('judul')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Tipe Content <span class="text-danger">*</span></label>
            <select name="tipe" id="contentType" class="form-control" required onchange="toggleFields()">
              <option value="">Pilih Tipe</option>
              <option value="text" {{ old('tipe', $content->tipe)=='text' ? 'selected' : '' }}>Text (Plain Text)</option>
              <option value="html" {{ old('tipe', $content->tipe)=='html' ? 'selected' : '' }}>HTML (Rich Text)</option>
              <option value="video" {{ old('tipe', $content->tipe)=='video' ? 'selected' : '' }}>Video</option>
              <option value="youtube" {{ old('tipe', $content->tipe)=='youtube' ? 'selected' : '' }}>YouTube Video</option>
              <option value="audio" {{ old('tipe', $content->tipe)=='audio' ? 'selected' : '' }}>Audio</option>
              <option value="pdf" {{ old('tipe', $content->tipe)=='pdf' ? 'selected' : '' }}>PDF File</option>
              <option value="image" {{ old('tipe', $content->tipe)=='image' ? 'selected' : '' }}>Image</option>
              <option value="link" {{ old('tipe', $content->tipe)=='link' ? 'selected' : '' }}>External Link (PDF/File)</option>
            </select>
            @error('tipe')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Urutan <span class="text-danger">*</span></label>
            <input type="number" name="urutan" value="{{ old('urutan', $content->urutan) }}" class="form-control" min="1" required>
            <small class="text-muted">Urutan tampil content dalam sub-module</small>
            @error('urutan')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="d-flex justify-content-end mt-4">
            <button type="button" class="btn btn-primary" onclick="nextStep(2)">Next: Content Details →</button>
          </div>
        </div>

        <!-- Step 2: Content Details -->
        <div class="wizard-step" id="step2" style="display: none;">
          <h4 class="mb-4">Step 2: Content Details</h4>
          <p class="text-muted mb-4">Lengkapi detail content berdasarkan tipe yang dipilih.</p>
          
          <!-- HTML Content Field (for text and html types) -->
          <div class="mb-3" id="htmlContentField" style="display: none;">
            <label class="form-label">Konten HTML/Text <span class="text-danger">*</span></label>
            <textarea name="html_content" id="htmlContent" class="form-control" rows="10">{{ old('html_content', $content->html_content ?? '') }}</textarea>
            <small class="text-muted">Gunakan HTML untuk format yang lebih kaya. Untuk plain text, cukup ketik teks biasa.</small>
            @error('html_content')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <!-- File Upload Field (for video, audio, pdf, image) -->
          <div class="mb-3" id="fileUploadField" style="display: none;">
            @if($content->file_path)
              <div class="mb-2">
                <label class="form-label">File Saat Ini</label>
                <div>
                  <a href="{{ route('instructor.contents.download', $content->id) }}" target="_blank">{{ basename($content->file_path) }}</a>
                </div>
              </div>
            @endif
            <label class="form-label">{{ $content->file_path ? 'Ganti File' : 'Upload File' }} <span class="text-danger">*</span></label>
            <input type="file" name="file_path" id="fileInput" class="form-control" accept="">
            <small class="text-muted">Maksimal ukuran file: 100MB. {{ $content->file_path ? 'Kosongkan jika tidak ingin mengganti file.' : '' }}</small>
            @error('file_path')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <!-- External URL Field (for link type) -->
          <div class="mb-3" id="externalUrlField" style="display: none;">
            <label class="form-label">External URL <span class="text-danger">*</span></label>
            <input type="url" name="external_url" value="{{ old('external_url', $content->external_url ?? '') }}" class="form-control" placeholder="https://example.com/file.pdf">
            <small class="text-muted">Masukkan URL lengkap ke file PDF atau resource eksternal lainnya</small>
            @error('external_url')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <!-- YouTube URL Field (for youtube type) -->
          <div class="mb-3" id="youtubeUrlField" style="display: none;">
            <label class="form-label">YouTube URL <span class="text-danger">*</span></label>
            <input type="url" name="youtube_url" value="{{ old('youtube_url', $content->youtube_url ?? '') }}" class="form-control" placeholder="https://www.youtube.com/watch?v=VIDEO_ID atau https://youtu.be/VIDEO_ID">
            <small class="text-muted">Masukkan URL lengkap video YouTube (format: youtube.com/watch?v=... atau youtu.be/...)</small>
            @error('youtube_url')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <!-- Required Duration Field (for youtube type) -->
          <div class="mb-3" id="requiredDurationField" style="display: none;">
            <label class="form-label">Durasi Video yang Diperlukan (detik) <span class="text-danger">*</span></label>
            <input type="number" name="required_duration" value="{{ old('required_duration', $content->required_duration ?? '') }}" class="form-control" min="1" placeholder="Contoh: 300 (untuk 5 menit)">
            <small class="text-muted">Masukkan durasi video dalam detik. Siswa harus menonton video selama durasi ini sebelum dapat melanjutkan ke konten berikutnya.</small>
            @error('required_duration')<small class="text-danger d-block">{{ $message }}</small>@enderror
          </div>

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="prevStep(1)">← Back</button>
            <button type="button" class="btn btn-primary" onclick="nextStep(3)">Next: Review →</button>
          </div>
        </div>

        <!-- Step 3: Review and Submit -->
        <div class="wizard-step" id="step3" style="display: none;">
          <h4 class="mb-4">Step 3: Review and Submit</h4>
          <p class="text-muted mb-4">Tinjau informasi content Anda sebelum menyimpan.</p>
          
          <div class="card mb-3">
            <div class="card-header">Content Information</div>
            <div class="card-body">
              <p><strong>Judul:</strong> <span id="reviewJudul"></span></p>
              <p><strong>Tipe:</strong> <span id="reviewTipe"></span></p>
              <p><strong>Urutan:</strong> <span id="reviewUrutan"></span></p>
              <div id="reviewDetails"></div>
            </div>
          </div>

          <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" onclick="prevStep(2)">← Back</button>
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check-circle"></i> Update Content
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function nextStep(step) {
  if (step === 2) {
    if (!validateStep1()) {
      return;
    }
    toggleFields();
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
  const tipe = document.querySelector('select[name="tipe"]').value;
  const urutan = document.querySelector('input[name="urutan"]').value;
  
  if (!judul || !tipe || !urutan) {
    alert('Please fill in all required fields in Step 1.');
    return false;
  }
  return true;
}

function validateStep2() {
  const tipe = document.querySelector('select[name="tipe"]').value;
  let isValid = true;
  
  if (tipe === 'text' || tipe === 'html') {
    const htmlContent = document.querySelector('textarea[name="html_content"]').value;
    if (!htmlContent) {
      isValid = false;
    }
  } else if (tipe === 'video' || tipe === 'audio' || tipe === 'pdf' || tipe === 'image') {
    // File is optional on edit if existing file exists
    const fileInput = document.querySelector('input[name="file_path"]');
    const hasExistingFile = {{ $content->file_path ? 'true' : 'false' }};
    if (!fileInput.files || fileInput.files.length === 0) {
      if (!hasExistingFile) {
        isValid = false;
      }
    }
  } else if (tipe === 'link') {
    const externalUrl = document.querySelector('input[name="external_url"]').value;
    if (!externalUrl) {
      isValid = false;
    }
  } else if (tipe === 'youtube') {
    const youtubeUrl = document.querySelector('input[name="youtube_url"]').value;
    const requiredDuration = document.querySelector('input[name="required_duration"]').value;
    if (!youtubeUrl || !requiredDuration) {
      isValid = false;
    }
  }
  
  if (!isValid) {
    alert('Please fill in all required fields for the selected content type.');
    return false;
  }
  return true;
}

function toggleFields() {
  const contentTypeSelect = document.getElementById('contentType');
  const htmlContentField = document.getElementById('htmlContentField');
  const fileUploadField = document.getElementById('fileUploadField');
  const externalUrlField = document.getElementById('externalUrlField');
  const youtubeUrlField = document.getElementById('youtubeUrlField');
  const requiredDurationField = document.getElementById('requiredDurationField');
  const fileInput = document.getElementById('fileInput');
  const htmlContent = document.getElementById('htmlContent');
  
  const selectedType = contentTypeSelect.value;
  
  // Hide all fields first
  htmlContentField.style.display = 'none';
  fileUploadField.style.display = 'none';
  externalUrlField.style.display = 'none';
  youtubeUrlField.style.display = 'none';
  requiredDurationField.style.display = 'none';
  
  // Remove required attributes
  if (htmlContent) htmlContent.removeAttribute('required');
  if (fileInput) fileInput.removeAttribute('required');
  const externalUrlInput = document.querySelector('[name="external_url"]');
  const youtubeUrlInput = document.querySelector('[name="youtube_url"]');
  const requiredDurationInput = document.querySelector('[name="required_duration"]');
  if (externalUrlInput) externalUrlInput.removeAttribute('required');
  if (youtubeUrlInput) youtubeUrlInput.removeAttribute('required');
  if (requiredDurationInput) requiredDurationInput.removeAttribute('required');
  
  // Show and configure fields based on type
  if (selectedType === 'text' || selectedType === 'html') {
    htmlContentField.style.display = 'block';
    if (htmlContent) htmlContent.setAttribute('required', 'required');
  } else if (selectedType === 'video') {
    fileUploadField.style.display = 'block';
    if (fileInput) {
      fileInput.setAttribute('accept', 'video/*');
      // File is optional on edit if existing file exists
      const hasExistingFile = {{ $content->file_path ? 'true' : 'false' }};
      if (!hasExistingFile) {
        fileInput.setAttribute('required', 'required');
      }
    }
  } else if (selectedType === 'audio') {
    fileUploadField.style.display = 'block';
    if (fileInput) {
      fileInput.setAttribute('accept', 'audio/*');
      const hasExistingFile = {{ $content->file_path ? 'true' : 'false' }};
      if (!hasExistingFile) {
        fileInput.setAttribute('required', 'required');
      }
    }
  } else if (selectedType === 'pdf') {
    fileUploadField.style.display = 'block';
    if (fileInput) {
      fileInput.setAttribute('accept', '.pdf');
      const hasExistingFile = {{ $content->file_path ? 'true' : 'false' }};
      if (!hasExistingFile) {
        fileInput.setAttribute('required', 'required');
      }
    }
  } else if (selectedType === 'image') {
    fileUploadField.style.display = 'block';
    if (fileInput) {
      fileInput.setAttribute('accept', 'image/*');
      const hasExistingFile = {{ $content->file_path ? 'true' : 'false' }};
      if (!hasExistingFile) {
        fileInput.setAttribute('required', 'required');
      }
    }
  } else if (selectedType === 'link') {
    externalUrlField.style.display = 'block';
    if (externalUrlInput) externalUrlInput.setAttribute('required', 'required');
  } else if (selectedType === 'youtube') {
    youtubeUrlField.style.display = 'block';
    requiredDurationField.style.display = 'block';
    if (youtubeUrlInput) youtubeUrlInput.setAttribute('required', 'required');
    if (requiredDurationInput) requiredDurationInput.setAttribute('required', 'required');
  }
}

function updateReview() {
  document.getElementById('reviewJudul').textContent = document.querySelector('input[name="judul"]').value;
  document.getElementById('reviewTipe').textContent = document.querySelector('select[name="tipe"]').options[document.querySelector('select[name="tipe"]').selectedIndex].text;
  document.getElementById('reviewUrutan').textContent = document.querySelector('input[name="urutan"]').value;
  
  const tipe = document.querySelector('select[name="tipe"]').value;
  const detailsDiv = document.getElementById('reviewDetails');
  detailsDiv.innerHTML = '';
  
  if (tipe === 'text' || tipe === 'html') {
    const htmlContent = document.querySelector('textarea[name="html_content"]').value;
    detailsDiv.innerHTML = `<p><strong>Content:</strong> ${htmlContent.substring(0, 100)}${htmlContent.length > 100 ? '...' : ''}</p>`;
  } else if (tipe === 'video' || tipe === 'audio' || tipe === 'pdf' || tipe === 'image') {
    const fileInput = document.querySelector('input[name="file_path"]');
    const hasExistingFile = {{ $content->file_path ? 'true' : 'false' }};
    if (fileInput.files && fileInput.files.length > 0) {
      detailsDiv.innerHTML = `<p><strong>New File:</strong> ${fileInput.files[0].name}</p>`;
    } else if (hasExistingFile) {
      detailsDiv.innerHTML = `<p><strong>Current File:</strong> {{ basename($content->file_path) }} (will be kept if no new file uploaded)</p>`;
    }
  } else if (tipe === 'link') {
    const externalUrl = document.querySelector('input[name="external_url"]').value;
    detailsDiv.innerHTML = `<p><strong>External URL:</strong> ${externalUrl}</p>`;
  } else if (tipe === 'youtube') {
    const youtubeUrl = document.querySelector('input[name="youtube_url"]').value;
    const requiredDuration = document.querySelector('input[name="required_duration"]').value;
    detailsDiv.innerHTML = `<p><strong>YouTube URL:</strong> ${youtubeUrl}</p><p><strong>Required Duration:</strong> ${requiredDuration} seconds</p>`;
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  toggleFields();
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
</style>
@endsection
