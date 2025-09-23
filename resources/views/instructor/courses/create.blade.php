@extends('layouts.instructor')

@section('title','Buat Course')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}">Courses</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
  </ol>
  {{-- Binding: none --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <form action="{{ route('instructor.courses.store') }}" method="post">
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
          <label>JP</label>
          <input type="number" name="jp_value" value="{{ old('jp_value') }}" class="form-control" min="0">
          @error('jp_value')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Bidang Kompetensi</label>
          <input type="text" name="bidang_kompetensi" value="{{ old('bidang_kompetensi') }}" class="form-control">
          @error('bidang_kompetensi')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="d-flex justify-content-between">
          <a href="{{ route('instructor.courses.index') }}" class="btn btn-light">Batal</a>
          <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


