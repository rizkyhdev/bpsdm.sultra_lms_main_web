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
    <div class="card-body">
      <form action="{{ route('instructor.contents.update', $content->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Judul</label>
          <input type="text" name="judul" value="{{ old('judul', $content->judul) }}" class="form-control" required>
          @error('judul')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Tipe</label>
          <select name="tipe" class="form-control" required>
            @foreach(['text','pdf','video','audio','image'] as $t)
              <option value="{{ $t }}" {{ old('tipe', $content->tipe)==$t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
            @endforeach
          </select>
          @error('tipe')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>File Saat Ini</label>
          <div>
            @if($content->file_path)
              <a href="{{ route('instructor.contents.download', $content->id) }}">{{ basename($content->file_path) }}</a>
            @else
              <span class="text-muted">Tidak ada file</span>
            @endif
          </div>
        </div>
        <div class="form-group">
          <label>Ganti File (opsional)</label>
          <input type="file" name="file" class="form-control-file">
          @error('file')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Urutan</label>
          <input type="number" name="urutan" value="{{ old('urutan', $content->urutan) }}" class="form-control" min="1">
          @error('urutan')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Konten Teks (untuk tipe text)</label>
          <textarea name="teks" class="form-control" rows="4">{{ old('teks', $content->teks ?? '') }}</textarea>
          @error('teks')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="d-flex justify-content-between">
          <a href="{{ route('instructor.sub_modules.show', $content->subModule) }}" class="btn btn-light">Batal</a>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


