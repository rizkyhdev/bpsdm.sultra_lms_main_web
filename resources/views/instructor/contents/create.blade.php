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
    <div class="card-body">
      <form action="{{ route('instructor.contents.store', $subModule->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label>Judul</label>
          <input type="text" name="judul" value="{{ old('judul') }}" class="form-control" required>
          @error('judul')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Tipe</label>
          <select name="tipe" class="form-control" required>
            @foreach(['text','pdf','video','audio','image'] as $t)
              <option value="{{ $t }}" {{ old('tipe')==$t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
            @endforeach
          </select>
          @error('tipe')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>File (opsional untuk tipe non-text)</label>
          <input type="file" name="file" class="form-control-file">
          @error('file')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Urutan</label>
          <input type="number" name="urutan" value="{{ old('urutan') }}" class="form-control" min="1">
          @error('urutan')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Konten Teks (untuk tipe text)</label>
          <textarea name="teks" class="form-control" rows="4">{{ old('teks') }}</textarea>
          @error('teks')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="d-flex justify-content-between">
          <a href="{{ route('instructor.sub_modules.show', $subModule) }}" class="btn btn-light">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


