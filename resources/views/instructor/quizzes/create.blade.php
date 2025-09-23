@extends('layouts.instructor')

@section('title','Tambah Quiz')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.sub_modules.show', $subModule) }}">{{ $subModule->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Quiz Create</li>
  </ol>
  {{-- Binding: $subModule --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <form action="{{ route('instructor.sub_modules.quizzes.store', $subModule) }}" method="post">
        @csrf
        <div class="form-group">
          <label>Judul</label>
          <input type="text" name="judul" value="{{ old('judul') }}" class="form-control" required>
          @error('judul')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
          @error('deskripsi')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Nilai Minimum</label>
          <input type="number" name="nilai_minimum" value="{{ old('nilai_minimum') }}" class="form-control" min="0" max="100">
          @error('nilai_minimum')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Maks Attempts</label>
          <input type="number" name="max_attempts" value="{{ old('max_attempts') }}" class="form-control" min="1">
          @error('max_attempts')<small class="text-danger">{{ $message }}</small>@enderror
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


