@extends('layouts.admin')

@section('title', 'Detail Pendaftaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.enrollments.index') }}">Pendaftaran</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit mr-1"></i> Ubah</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Informasi Peserta</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Nama</dt><dd class="col-7">{{ $enrollment->user->nama ?? '-' }}</dd>
                        <dt class="col-5">Email</dt><dd class="col-7">{{ $enrollment->user->email ?? '-' }}</dd>
                        <dt class="col-5">NIP</dt><dd class="col-7">{{ $enrollment->user->nip ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Informasi Kursus</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Judul</dt><dd class="col-7"><a href="{{ route('admin.courses.show', $enrollment->course) }}">{{ $enrollment->course->judul ?? '-' }}</a></dd>
                        <dt class="col-5">Bidang</dt><dd class="col-7">{{ $enrollment->course->bidang_kompetensi ?? '-' }}</dd>
                        <dt class="col-5">JP</dt><dd class="col-7">{{ $enrollment->course->jp_value ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">Ringkasan Progres</div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-3">Tgl Daftar</dt><dd class="col-9">{{ optional($enrollment->enrollment_date)->format('d/m/Y') }}</dd>
                <dt class="col-3">Status</dt><dd class="col-9">{{ $enrollment->status }}</dd>
                <dt class="col-3">Selesai</dt><dd class="col-9">{{ optional($enrollment->completed_at)->format('d/m/Y H:i') ?? '-' }}</dd>
            </dl>
        </div>
    </div>
@endsection


