@extends('layouts.studentapp')

@section('title', 'Hasil Simulasi')

@section('content')
@php
    $benar = $attempt->skor;
    $salah = $terjawab - $benar;
    $kosong = $attempt->jumlah_soal - $terjawab;
    $lamaDetik = (int) $attempt->started_at->diffInSeconds($attempt->completed_at);
@endphp
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dasbor</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.simulasi.index') }}">Simulasi Ujian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Hasil</li>
        </ol>
    </nav>

    @include('partials._flash')

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6">
            <div class="card shadow-sm border-0 text-center" style="border-radius: 12px;">
                <div class="card-body p-4 p-md-5">
                    <i class="bi bi-patch-check-fill text-success" style="font-size: 3rem;" aria-hidden="true"></i>
                    <h1 class="h3 fw-bold mt-2 mb-1">Hasil Simulasi</h1>
                    <p class="text-muted mb-4">{{ $attempt->token?->sesi }}</p>

                    <div class="display-1 fw-bold text-primary lh-1">{{ $benar }}</div>
                    <p class="text-muted">Skor &mdash; {{ $benar }} jawaban benar dari {{ $attempt->jumlah_soal }} soal</p>

                    <div class="row g-3 my-4">
                        <div class="col-4">
                            <div class="border rounded-3 py-3">
                                <div class="h4 fw-bold text-success mb-0">{{ $benar }}</div>
                                <small class="text-muted">Benar</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded-3 py-3">
                                <div class="h4 fw-bold text-danger mb-0">{{ $salah }}</div>
                                <small class="text-muted">Salah</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded-3 py-3">
                                <div class="h4 fw-bold text-secondary mb-0">{{ $kosong }}</div>
                                <small class="text-muted">Tidak Dijawab</small>
                            </div>
                        </div>
                    </div>

                    <dl class="row text-start small mx-auto mb-4" style="max-width: 28rem;">
                        <dt class="col-5 text-muted">Nama</dt>
                        <dd class="col-7">{{ $attempt->user->name }}</dd>
                        <dt class="col-5 text-muted">NIP</dt>
                        <dd class="col-7">{{ $attempt->user->nip ?: '-' }}</dd>
                        <dt class="col-5 text-muted">Unit Kerja</dt>
                        <dd class="col-7">{{ $attempt->user->unit_kerja ?: '-' }}</dd>
                        <dt class="col-5 text-muted">Waktu Mulai</dt>
                        <dd class="col-7">{{ $attempt->started_at->format('d M Y H:i') }}</dd>
                        <dt class="col-5 text-muted">Waktu Selesai</dt>
                        <dd class="col-7">{{ $attempt->completed_at->format('d M Y H:i') }}</dd>
                        <dt class="col-5 text-muted">Lama Pengerjaan</dt>
                        <dd class="col-7 mb-0">{{ intdiv($lamaDetik, 60) }} menit {{ $lamaDetik % 60 }} detik</dd>
                    </dl>

                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="{{ route('student.simulasi.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-repeat me-1"></i>Kembali ke Simulasi
                        </a>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-1"></i>Dasbor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
