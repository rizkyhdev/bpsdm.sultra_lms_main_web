@extends('layouts.instructor')

@section('title','Edit Sub-Module')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $sub->module->course->id) }}">{{ $sub->module->course->judul }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.modules.show', $sub->module) }}">{{ $sub->module->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sub-Module Edit</li>
  </ol>
  {{-- Binding: $sub --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <form action="{{ route('instructor.sub_modules.update', $sub->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Judul</label>
          <input type="text" name="judul" value="{{ old('judul', $sub->judul) }}" class="form-control" required>
          @error('judul')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $sub->deskripsi) }}</textarea>
          @error('deskripsi')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>Urutan</label>
          <input type="number" name="urutan" value="{{ old('urutan', $sub->urutan) }}" class="form-control" min="1">
          @error('urutan')<small class="text-danger">{{ $message }}</small>@enderror
        </div>
        <div class="d-flex justify-content-between">
          <a href="{{ route('instructor.modules.show', $sub->module) }}" class="btn btn-light">Batal</a>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

