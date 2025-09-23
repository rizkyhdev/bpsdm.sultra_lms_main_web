@extends('layouts.admin')

@section('title', 'Ubah Sertifikat')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Sertifikat</a></li>
    <li class="breadcrumb-item active">Ubah</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.certificates.update', $certificate) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="nomor_sertifikat">Nomor Sertifikat</label>
                        <input type="text" id="nomor_sertifikat" name="nomor_sertifikat" value="{{ old('nomor_sertifikat', $certificate->nomor_sertifikat) }}" class="form-control @error('nomor_sertifikat') is-invalid @enderror">
                        @error('nomor_sertifikat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="issue_date">Tanggal Terbit</label>
                        <input type="date" id="issue_date" name="issue_date" value="{{ old('issue_date', optional($certificate->issue_date)->toDateString()) }}" class="form-control @error('issue_date') is-invalid @enderror">
                        @error('issue_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label>File Saat Ini</label>
                        <div>
                            @if($certificate->file_path)
                                <a href="{{ Storage::url($certificate->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download mr-1"></i> Unduh</a>
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="file">Ganti File (PDF)</label>
                    <input type="file" id="file" name="file" class="form-control-file @error('file') is-invalid @enderror">
                    @error('file')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.certificates.show', $certificate) }}" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save mr-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection


