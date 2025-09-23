@extends('layouts.admin')

@section('title', 'Laporan - Ringkasan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Laporan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.reports.users') }}" class="card text-decoration-none text-body h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-1">Laporan Pengguna</h6>
                    <p class="text-muted mb-0 small">Statistik registrasi, validasi, peran.</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.reports.courses') }}" class="card text-decoration-none text-body h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-1">Laporan Kursus</h6>
                    <p class="text-muted mb-0 small">Pendaftaran, penyelesaian, tren.</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.reports.jp') }}" class="card text-decoration-none text-body h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-1">Laporan JP</h6>
                    <p class="text-muted mb-0 small">Akumulasi JP per tahun.</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.reports.quizzes') }}" class="card text-decoration-none text-body h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-1">Laporan Kuis</h6>
                    <p class="text-muted mb-0 small">Nilai, kelulusan, percobaan.</p>
                </div>
            </a>
        </div>
        <div class="col-md-3 mb-3">
            <a href="{{ route('admin.reports.certificates') }}" class="card text-decoration-none text-body h-100 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-1">Laporan Sertifikat</h6>
                    <p class="text-muted mb-0 small">Penerbitan dan verifikasi.</p>
                </div>
            </a>
        </div>
    </div>
@endsection


