@extends('layouts.admin')

@section('title', 'Tambah Modul')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Kursus</a></li>
    <li class="breadcrumb-item active">Tambah Modul</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.modules.store') }}" method="POST">
                @csrf
                <input type="hidden" name="course_id" value="{{ request('course') ?? ($course->id ?? '') }}">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="judul">Judul</label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul') }}" class="form-control @error('judul') is-invalid @enderror">
                        @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-2">
                        <label for="urutan">Urutan</label>
                        <input type="number" id="urutan" name="urutan" value="{{ old('urutan') }}" class="form-control @error('urutan') is-invalid @enderror">
                        @error('urutan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.courses.show', request('course') ?? ($course->id ?? 0)) }}" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection


