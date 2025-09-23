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
    <div class="card-body">
      <form action="{{ route('instructor.courses.modules.store', $course) }}" method="post">
        @csrf
        <div class="form-group">
          <label>Judul</label>
          <input type="text" name="judul" value="{{ old('judul') }}" class="form-control" required>
          @error('judul')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi') }}</textarea>
          @error('deskripsi')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Urutan</label>
          <input type="number" name="urutan" value="{{ old('urutan') }}" class="form-control" min="1">
          @error('urutan')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="d-flex justify-content-between">
          <a href="{{ route('instructor.courses.show', $course) }}" class="btn btn-light">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


