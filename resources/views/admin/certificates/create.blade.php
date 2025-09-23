@extends('layouts.admin')

@section('title', 'Tambah Sertifikat')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Sertifikat</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.certificates.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="user_id">Pengguna</label>
                        <select id="user_id" name="user_id" class="form-control @error('user_id') is-invalid @enderror">
                            <option value="">-- Pilih Pengguna --</option>
                            @foreach(($usersList ?? []) as $u)
                                <option value="{{ $u->id }}" @if(old('user_id')==$u->id) selected @endif>{{ $u->nama }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="course_id">Kursus</label>
                        <select id="course_id" name="course_id" class="form-control @error('course_id') is-invalid @enderror">
                            <option value="">-- Pilih Kursus --</option>
                            @foreach(($coursesList ?? []) as $c)
                                <option value="{{ $c->id }}" @if(old('course_id')==$c->id) selected @endif>{{ $c->judul }}</option>
                            @endforeach
                        </select>
                        @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="nomor_sertifikat">Nomor Sertifikat</label>
                        <input type="text" id="nomor_sertifikat" name="nomor_sertifikat" value="{{ old('nomor_sertifikat') }}" class="form-control @error('nomor_sertifikat') is-invalid @enderror">
                        @error('nomor_sertifikat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="issue_date">Tanggal Terbit</label>
                        <input type="date" id="issue_date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}" class="form-control @error('issue_date') is-invalid @enderror">
                        @error('issue_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="file">File Sertifikat (PDF)</label>
                        <input type="file" id="file" name="file" class="form-control-file @error('file') is-invalid @enderror">
                        @error('file')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection


