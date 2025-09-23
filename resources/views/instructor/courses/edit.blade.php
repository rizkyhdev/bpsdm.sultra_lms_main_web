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
    <div class="card-body">
      <form action="{{ route('instructor.courses.update', $course) }}" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Judul</label>
          <input type="text" name="judul" value="{{ old('judul', $course->judul) }}" class="form-control" required>
          @error('judul')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $course->deskripsi) }}</textarea>
          @error('deskripsi')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>JP</label>
          <input type="number" name="jp_value" value="{{ old('jp_value', $course->jp_value) }}" class="form-control" min="0">
          @error('jp_value')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Bidang Kompetensi</label>
          <input type="text" name="bidang_kompetensi" value="{{ old('bidang_kompetensi', $course->bidang_kompetensi) }}" class="form-control">
          @error('bidang_kompetensi')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="d-flex justify-content-between">
          <a href="{{ route('instructor.courses.index') }}" class="btn btn-light">Batal</a>
          <button class="btn btn-primary" type="submit">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection


