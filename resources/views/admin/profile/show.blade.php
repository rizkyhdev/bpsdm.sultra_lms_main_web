@extends('layouts.admin')

@section('title', 'Profil Admin')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Profile Header -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header border-0 p-0" style="height: 160px; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);"></div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="text-center" style="margin-top: -80px;">
                        <div class="position-relative d-inline-block">
                            <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle border border-4 border-white shadow" style="width: 150px; height: 150px; object-fit: cover; background-color: #f8f9fa;">
                            @if($user->role === 'admin')
                                <span class="position-absolute bottom-0 end-0 bg-primary text-white p-2 rounded-circle border border-3 border-white shadow-sm" title="Administrator">
                                    <i class="bi bi-shield-check"></i>
                                </span>
                            @endif
                        </div>
                        <h3 class="fw-bold mt-3 mb-1">{{ $user->name }}</h3>
                        <p class="text-muted mb-3"><i class="bi bi-envelope me-1"></i> {{ $user->email }}</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-pencil-square me-2"></i>Edit Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Detailed Info -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-transparent border-0 py-3 px-4">
                            <h5 class="fw-bold mb-0">Informasi Pribadi</h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">NIP</label>
                                    <p class="mb-0 fw-semibold text-dark">{{ $user->nip ?? 'Tidak ada data' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">Nomor Telepon</label>
                                    <p class="mb-0 fw-semibold text-dark">{{ $user->phone ?? 'Tidak ada data' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">Jabatan</label>
                                    <p class="mb-0 fw-semibold text-dark">{{ $user->jabatan ?? 'Administrator' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">Unit Kerja</label>
                                    <p class="mb-0 fw-semibold text-dark">{{ $user->unit_kerja ?? 'Pusat Administrasi' }}</p>
                                </div>
                                <div class="col-12">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">Tentang Saya</label>
                                    <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $user->bio ?? 'Belum ada deskripsi profil.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Settings Info -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-transparent border-0 py-3 px-4">
                            <h5 class="fw-bold mb-0">Pengaturan Akun</h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex align-items-center">
                                    <div class="icon-shape bg-light text-primary rounded-3 me-3">
                                        <i class="bi bi-clock"></i>
                                    </div>
                                    <div>
                                        <label class="small text-muted d-block mb-0">Zona Waktu</label>
                                        <span class="fw-semibold">{{ $user->timezone ?? 'Asia/Makassar' }}</span>
                                    </div>
                                </div>
                                <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex align-items-center">
                                    <div class="icon-shape bg-light text-primary rounded-3 me-3">
                                        <i class="bi bi-translate"></i>
                                    </div>
                                    <div>
                                        <label class="small text-muted d-block mb-0">Bahasa</label>
                                        <span class="fw-semibold">{{ $user->locale === 'id' ? 'Bahasa Indonesia' : ($user->locale === 'en' ? 'English' : 'Default') }}</span>
                                    </div>
                                </div>
                                <div class="list-group-item bg-transparent border-0 px-0 py-3 d-flex align-items-center">
                                    <div class="icon-shape bg-light text-primary rounded-3 me-3">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div>
                                        <label class="small text-muted d-block mb-0">Bergabung Sejak</label>
                                        <span class="fw-semibold">{{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .tracking-wider { letter-spacing: 0.05em; }
    .icon-shape {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
</style>
@endsection
