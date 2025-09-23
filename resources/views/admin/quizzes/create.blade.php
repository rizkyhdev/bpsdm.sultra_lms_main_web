@extends('layouts.admin')

@section('title', 'Tambah Kuis')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sub_modules.show', request('sub_module')) }}">Sub Modul</a></li>
    <li class="breadcrumb-item active">Tambah Kuis</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.quizzes.store') }}" method="POST">
                @csrf
                <input type="hidden" name="sub_module_id" value="{{ request('sub_module') ?? ($subModule->id ?? '') }}">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="judul">Judul</label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul') }}" class="form-control @error('judul') is-invalid @enderror">
                        @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label for="nilai_minimum">Nilai Minimum</label>
                        <input type="number" id="nilai_minimum" name="nilai_minimum" value="{{ old('nilai_minimum') }}" class="form-control @error('nilai_minimum') is-invalid @enderror">
                        @error('nilai_minimum')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label for="max_attempts">Maks. Percobaan</label>
                        <input type="number" id="max_attempts" name="max_attempts" value="{{ old('max_attempts', 1) }}" class="form-control @error('max_attempts') is-invalid @enderror">
                        @error('max_attempts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.sub_modules.show', request('sub_module') ?? ($subModule->id ?? 0)) }}" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection


