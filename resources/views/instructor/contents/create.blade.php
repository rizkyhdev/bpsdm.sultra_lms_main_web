@extends('layouts.instructor')

@section('title','Tambah Content')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.modules.show', $subModule->module) }}">{{ $subModule->module->judul }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.sub_modules.show', $subModule) }}">{{ $subModule->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Content Create</li>
  </ol>
  {{-- Binding: $subModule --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Tambah Content Baru</h5>
    </div>
    <div class="card-body">
      <form action="{{ route('instructor.contents.store', $subModule->id) }}" method="post" enctype="multipart/form-data" id="contentForm">
        @csrf
        <div class="mb-3">
          <label class="form-label">Judul Content <span class="text-danger">*</span></label>
          <input type="text" name="judul" value="{{ old('judul') }}" class="form-control" required>
          @error('judul')<small class="text-danger d-block">{{ $message }}</small>@enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Tipe Content <span class="text-danger">*</span></label>
          <select name="tipe" id="contentType" class="form-control" required>
            <option value="">Pilih Tipe</option>
            <option value="text" {{ old('tipe')=='text' ? 'selected' : '' }}>Text (Plain Text)</option>
            <option value="html" {{ old('tipe')=='html' ? 'selected' : '' }}>HTML (Rich Text)</option>
            <option value="video" {{ old('tipe')=='video' ? 'selected' : '' }}>Video</option>
            <option value="youtube" {{ old('tipe')=='youtube' ? 'selected' : '' }}>YouTube Video</option>
            <option value="audio" {{ old('tipe')=='audio' ? 'selected' : '' }}>Audio</option>
            <option value="pdf" {{ old('tipe')=='pdf' ? 'selected' : '' }}>PDF File</option>
            <option value="image" {{ old('tipe')=='image' ? 'selected' : '' }}>Image</option>
            <option value="link" {{ old('tipe')=='link' ? 'selected' : '' }}>External Link (PDF/File)</option>
          </select>
          @error('tipe')<small class="text-danger d-block">{{ $message }}</small>@enderror
        </div>

        <!-- HTML Content Field (for text and html types) -->
        <div class="mb-3" id="htmlContentField" style="display: none;">
          <label class="form-label">Konten HTML/Text <span class="text-danger">*</span></label>
          <textarea name="html_content" id="htmlContent" class="form-control" rows="10">{{ old('html_content') }}</textarea>
          <small class="text-muted">Gunakan HTML untuk format yang lebih kaya. Untuk plain text, cukup ketik teks biasa.</small>
          @error('html_content')<small class="text-danger d-block">{{ $message }}</small>@enderror
        </div>

        <!-- File Upload Field (for video, audio, pdf, image) -->
        <div class="mb-3" id="fileUploadField" style="display: none;">
          <label class="form-label">Upload File <span class="text-danger">*</span></label>
          <input type="file" name="file_path" id="fileInput" class="form-control" accept="">
          <small class="text-muted">Maksimal ukuran file: 100MB</small>
          @error('file_path')<small class="text-danger d-block">{{ $message }}</small>@enderror
        </div>

        <!-- External URL Field (for link type) -->
        <div class="mb-3" id="externalUrlField" style="display: none;">
          <label class="form-label">External URL <span class="text-danger">*</span></label>
          <input type="url" name="external_url" value="{{ old('external_url') }}" class="form-control" placeholder="https://example.com/file.pdf">
          <small class="text-muted">Masukkan URL lengkap ke file PDF atau resource eksternal lainnya</small>
          @error('external_url')<small class="text-danger d-block">{{ $message }}</small>@enderror
        </div>

        <!-- YouTube URL Field (for youtube type) -->
        <div class="mb-3" id="youtubeUrlField" style="display: none;">
          <label class="form-label">YouTube URL <span class="text-danger">*</span></label>
          <input type="url" name="youtube_url" value="{{ old('youtube_url') }}" class="form-control" placeholder="https://www.youtube.com/watch?v=VIDEO_ID atau https://youtu.be/VIDEO_ID">
          <small class="text-muted">Masukkan URL lengkap video YouTube (format: youtube.com/watch?v=... atau youtu.be/...)</small>
          @error('youtube_url')<small class="text-danger d-block">{{ $message }}</small>@enderror
        </div>

        <!-- Required Duration Field (for youtube type) -->
        <div class="mb-3" id="requiredDurationField" style="display: none;">
          <label class="form-label">Durasi Video yang Diperlukan (detik) <span class="text-danger">*</span></label>
          <input type="number" name="required_duration" value="{{ old('required_duration') }}" class="form-control" min="1" placeholder="Contoh: 300 (untuk 5 menit)">
          <small class="text-muted">Masukkan durasi video dalam detik. Siswa harus menonton video selama durasi ini sebelum dapat melanjutkan ke konten berikutnya.</small>
          @error('required_duration')<small class="text-danger d-block">{{ $message }}</small>@enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Urutan <span class="text-danger">*</span></label>
          <input type="number" name="urutan" value="{{ old('urutan', 1) }}" class="form-control" min="1" required>
          <small class="text-muted">Urutan tampil content dalam sub-module</small>
          @error('urutan')<small class="text-danger d-block">{{ $message }}</small>@enderror
        </div>

        <div class="d-flex justify-content-between">
          <a href="{{ route('instructor.contents.index', $subModule->id) }}" class="btn btn-light">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Content</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTypeSelect = document.getElementById('contentType');
    const htmlContentField = document.getElementById('htmlContentField');
    const fileUploadField = document.getElementById('fileUploadField');
    const externalUrlField = document.getElementById('externalUrlField');
    const youtubeUrlField = document.getElementById('youtubeUrlField');
    const requiredDurationField = document.getElementById('requiredDurationField');
    const fileInput = document.getElementById('fileInput');
    const htmlContent = document.getElementById('htmlContent');

    function toggleFields() {
        const selectedType = contentTypeSelect.value;
        
        // Hide all fields first
        htmlContentField.style.display = 'none';
        fileUploadField.style.display = 'none';
        externalUrlField.style.display = 'none';
        youtubeUrlField.style.display = 'none';
        requiredDurationField.style.display = 'none';
        
        // Remove required attributes
        htmlContent.removeAttribute('required');
        fileInput.removeAttribute('required');
        const externalUrlInput = document.querySelector('[name="external_url"]');
        const youtubeUrlInput = document.querySelector('[name="youtube_url"]');
        const requiredDurationInput = document.querySelector('[name="required_duration"]');
        if (externalUrlInput) externalUrlInput.removeAttribute('required');
        if (youtubeUrlInput) youtubeUrlInput.removeAttribute('required');
        if (requiredDurationInput) requiredDurationInput.removeAttribute('required');
        
        // Show and configure fields based on type
        if (selectedType === 'text' || selectedType === 'html') {
            htmlContentField.style.display = 'block';
            htmlContent.setAttribute('required', 'required');
        } else if (selectedType === 'video') {
            fileUploadField.style.display = 'block';
            fileInput.setAttribute('accept', 'video/*');
            fileInput.setAttribute('required', 'required');
        } else if (selectedType === 'audio') {
            fileUploadField.style.display = 'block';
            fileInput.setAttribute('accept', 'audio/*');
            fileInput.setAttribute('required', 'required');
        } else if (selectedType === 'pdf') {
            fileUploadField.style.display = 'block';
            fileInput.setAttribute('accept', '.pdf');
            fileInput.setAttribute('required', 'required');
        } else if (selectedType === 'image') {
            fileUploadField.style.display = 'block';
            fileInput.setAttribute('accept', 'image/*');
            fileInput.setAttribute('required', 'required');
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

    // Initial toggle
    toggleFields();
    
    // Toggle on change
    contentTypeSelect.addEventListener('change', toggleFields);
    
    // Set initial value if old input exists
    @if(old('tipe'))
        toggleFields();
    @endif
});
</script>
@endsection


