@extends('layouts.studentapp')

@section('title', 'Profil Peserta')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            
            @include('student._breadcrumbs', ['crumbs' => [
                ['label' => 'Dashboard', 'route' => 'student.dashboard'],
                ['label' => 'Profil Saya'],
            ]])

            <!-- Profile Header -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 mt-4">
                <div class="card-header border-0 p-0" style="height: 180px; background: linear-gradient(135deg, #21b3ca 0%, #178ea1 100%);"></div>
                <div class="card-body px-4 pb-4 pt-0">
                    <div class="row align-items-end" style="margin-top: -60px;">
                        <div class="col-auto">
                            <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle border border-4 border-white shadow" style="width: 160px; height: 160px; object-fit: cover; background-color: #f8f9fa;">
                        </div>
                        <div class="col mb-2">
                            <h2 class="fw-bold mb-1">{{ $user->name }}</h2>
                            <p class="text-muted mb-0"><i class="bi bi-envelope me-1"></i> {{ $user->email }}</p>
                            
                            @if($user->is_validated)
                                <span class="badge bg-info-soft text-info rounded-pill px-3 py-2 mt-2">
                                    <i class="bi bi-patch-check-fill me-1"></i> Akun Terverifikasi ASN
                                </span>
                            @else
                                <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2 mt-2">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Menunggu Verifikasi
                                </span>
                            @endif
                        </div>
                        <div class="col-auto mb-2">
                            <a href="{{ route('student.profile.edit') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-pencil-square me-2"></i>Edit Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- User Information -->
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-0 py-3 px-4">
                            <h5 class="fw-bold mb-0">Detail Informasi Peserta</h5>
                        </div>
                        <div class="card-body px-4 pb-4">
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">NIP</label>
                                    <p class="mb-0 fw-semibold text-dark fs-5">{{ $user->nip ?? 'Belum diatur' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">Nomor Telepon</label>
                                    <p class="mb-0 fw-semibold text-dark fs-5">{{ $user->phone ?? 'Belum diatur' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">Jabatan</label>
                                    <p class="mb-0 fw-semibold text-dark fs-5">{{ $user->jabatan ?? '-' }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">Unit Kerja</label>
                                    <p class="mb-0 fw-semibold text-dark fs-5">{{ $user->unit_kerja ?? '-' }}</p>
                                </div>
                                <div class="col-12 border-top pt-4">
                                    <label class="small text-muted text-uppercase fw-bold tracking-wider mb-1">Tentang Saya</label>
                                    <p class="mb-0 text-dark fs-6" style="white-space: pre-line; line-height: 1.6;">{{ $user->bio ?? 'Tuliskan biografi singkat Anda di menu edit profil.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ASN Verification Status Card -->
                    @if(!$user->is_validated)
                    <div class="card border-0 shadow-sm rounded-4 bg-light">
                        <div class="card-body p-4 text-center">
                            <i class="bi bi-shield-lock fs-1 text-warning mb-3"></i>
                            <h5 class="fw-bold text-dark">Lengkapi Verifikasi Akun</h5>
                            <p class="text-muted mb-4 small">Akun Anda belum diverifikasi oleh Admin. Silakan unggah Surat Tugas atau Bukti ASN untuk mendapatkan akses penuh ke sertifikat dan fitur lainnya.</p>
                            <a href="{{ route('student.profile.edit') }}#verification" class="btn btn-outline-primary rounded-pill">Unggah Dokumen Verifikasi</a>
                        </div>
                    </div>
                    @else
                    <div class="card border-0 shadow-sm rounded-4 bg-success text-white">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <i class="bi bi-shield-check fs-1"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">Verifikasi Berhasil</h5>
                                <p class="mb-0 opacity-75 small">Identitas ASN Anda telah divalidasi oleh sistem BPSDM. Selamat belajar!</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Personal Information & Stats Sidebar -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                        <div class="card-header bg-transparent border-0 py-3 px-4">
                            <h5 class="fw-bold mb-0">Informasi Tambahan</h5>
                        </div>
                        <div class="card-body px-4 pb-4 pt-0">
                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item bg-transparent border-0 px-0 py-3 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="bi bi-clock me-2"></i>Zona Waktu</span>
                                    <span class="fw-bold text-dark">{{ $user->timezone ?? 'Asia/Makassar' }}</span>
                                </li>
                                <li class="list-group-item bg-transparent border-0 px-0 py-3 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="bi bi-translate me-2"></i>Bahasa Konten</span>
                                    <span class="fw-bold text-dark">{{ $user->locale === 'en' ? 'English' : 'Bahasa Indonesia' }}</span>
                                </li>
                                <li class="list-group-item bg-transparent border-0 px-0 py-3 d-flex justify-content-between align-items-center">
                                    <span class="text-muted"><i class="bi bi-calendar-event me-2"></i>Tanggal Daftar</span>
                                    <span class="fw-bold text-dark">{{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Quick Stats (Static for now, but premium placeholder) -->
                    <div class="card border-0 shadow-sm rounded-4 bg-dark text-white p-4">
                        <h6 class="text-uppercase small opacity-50 fw-bold mb-3">Statistik Belajar</h6>
                        <div class="row g-3 text-center">
                            <div class="col-6 border-end border-secondary">
                                <h3 class="fw-bold mb-0">{{ $user->enrollments_count ?? 0 }}</h3>
                                <small class="opacity-75">Pelatihan</small>
                            </div>
                            <div class="col-6">
                                <h3 class="fw-bold mb-0 text-info">{{ $user->certificates_count ?? 0 }}</h3>
                                <small class="opacity-75">Sertifikat</small>
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
    .bg-info-soft { background-color: rgba(33, 179, 202, 0.1); }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .tracking-wider { letter-spacing: 0.05em; }
</style>
@endsection
