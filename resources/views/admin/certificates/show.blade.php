@extends('layouts.admin')

@section('title', 'Detail Sertifikat')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Sertifikat</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.certificates.edit', $certificate) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit mr-1"></i> Ubah</a>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-4">Nomor Sertifikat</dt><dd class="col-8">{{ $certificate->nomor_sertifikat }}</dd>
                <dt class="col-4">Pengguna</dt><dd class="col-8">{{ $certificate->user->nama ?? '-' }}</dd>
                <dt class="col-4">Kursus</dt><dd class="col-8">{{ $certificate->course->judul ?? '-' }}</dd>
                <dt class="col-4">Tanggal Terbit</dt><dd class="col-8">{{ optional($certificate->issue_date)->format('d/m/Y') }}</dd>
                <dt class="col-4">File</dt>
                <dd class="col-8">
                    @if($certificate->file_path)
                        <a href="{{ Storage::url($certificate->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download mr-1"></i> Unduh</a>
                    @else
                        <span class="text-muted">Tidak ada</span>
                    @endif
                </dd>
            </dl>
        </div>
    </div>
@endsection


