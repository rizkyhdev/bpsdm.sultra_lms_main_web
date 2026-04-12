@extends('layouts.instructor')

@section('title', 'Profil Instruktur')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
      </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <!-- Profile Header -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header border-0 p-0" style="height: 180px; background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);"></div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="row align-items-end" style="margin-top: -60px;">
                        <div class="col-auto">
                            <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle border border-4 border-white shadow" style="width: 160px; height: 160px; object-fit: cover; background-color: #f8f9fa;">
                        </div>
                        <div class="col mb-2">
                            <h2 class="fw-bold mb-1">{{ $user->name }}</h2>
                            <p class="text-muted mb-0"><i class="bi bi-envelope me-1"></i> {{ $user->email }}</p>
                            <span class="badge bg-success-soft text-success rounded-pill px-3 py-2 mt-2">
                                <i class="bi bi-person-check me-1"></i> Instruktur Terverifikasi
                            </span>
                        </div>
                        <div class="col-auto mb-2">
                            <a href="{{ route('instructor.profile.edit') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-pencil-square me-2"></i>Edit Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Detailed Info -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-0 py-3 px-4">
                            <h5 class="fw-bold mb-0">Informasi Pengajar</h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">NIP</label>
                                    <p class="mb-0 fw-semibold text-dark fs-5">{{ $user->nip ?? '-' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">Nomor Telepon</label>
                                    <p class="mb-0 fw-semibold text-dark fs-5">{{ $user->phone ?? '-' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">Jabatan</label>
                                    <p class="mb-0 fw-semibold text-dark fs-5">{{ $user->jabatan ?? 'Widyaiswara' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">Unit Kerja</label>
                                    <p class="mb-0 fw-semibold text-dark fs-5">{{ $user->unit_kerja ?? '-' }}</p>
                                </div>
                                <div class="col-12 border-top pt-4">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">Biografi & Keahlian</label>
                                    <p class="mb-0 text-dark fs-6" style="white-space: pre-line; line-height: 1.6;">{{ $user->bio ?? 'Belum ada deskripsi profil untuk instruktur ini.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4 text-center py-4 px-3 bg-primary text-white">
                        <div class="card-body">
                            <i class="bi bi-star-fill fs-1 mb-3"></i>
                            <h4 class="fw-bold mb-1">Status Instruktur</h4>
                            <p class="opacity-75 mb-0 small">Aktif mengajar sejak {{ $user->created_at ? $user->created_at->translatedFormat('F Y') : '-' }}</p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-transparent border-0 py-3 px-4">
                            <h5 class="fw-bold mb-0">Preferensi & Lokasi</h5>
                        </div>
                        <div class="card-body px-4 pb-4 pt-0">
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item bg-transparent border-0 px-0 py-3 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="bi bi-globe me-2"></i>Zona Waktu</span>
                                    <span class="fw-bold">{{ $user->timezone ?? 'Asia/Makassar' }}</span>
                                </li>
                                <li class="list-group-item bg-transparent border-0 px-0 py-3 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="bi bi-translate me-2"></i>Bahasa</span>
                                    <span class="fw-bold">{{ $user->locale === 'id' ? 'Bahasa Indonesia' : 'English' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .bg-success-soft { background-color: rgba(28, 200, 138, 0.1); }
    .tracking-wider { letter-spacing: 0.05em; }
    .card-header { position: relative; }
</style>
@endsection
