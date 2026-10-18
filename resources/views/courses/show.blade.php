@extends('layouts.app')

@section('title', $course->judul . ' - Sobat ASR')

@section('content')
<div class="course-detail-wrapper bg-light min-vh-100">
    {{-- Course Hero --}}
    <div class="course-hero position-relative py-5 text-white" 
         style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('{{ $course->cover_url ?? asset('image/slide1.jpeg') }}'); background-size: cover; background-position: center;">
        <div class="container py-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}" class="text-white opacity-75">Katalog Pelatihan</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">{{ Str::limit($course->judul, 30) }}</li>
                </ol>
            </nav>

            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-success text-uppercase mb-3 px-3 py-2 rounded-pill shadow-sm">
                        {{ $course->bidang_kompetensi ?? 'Umum' }}
                    </span>
                    <h1 class="display-5 fw-bold mb-3">{{ $course->judul }}</h1>
                    
                    <div class="d-flex flex-wrap gap-4 align-items-center mb-4 opacity-90">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-fill me-2 fs-5 text-warning"></i>
                            <span class="fw-semibold">{{ $course->jp_value }} JP</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bar-chart-steps me-2 fs-5 text-warning"></i>
                            <span class="fw-semibold">{{ $course->difficulty }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-star-fill me-2 fs-5 text-warning"></i>
                            <span class="fw-semibold">5.0 ({{ $course->enrollments_count ?? 0 }} Peserta)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container py-5">
        <div class="row g-5">
            {{-- Left column --}}
            <div class="col-lg-8">
                {{-- Deskripsi --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-5">
                    <h4 class="fw-bold mb-4 border-start border-4 border-primary ps-3">Tentang Pelatihan</h4>
                    <div class="course-description text-secondary leading-relaxed mb-0">
                        {!! nl2br(e($course->deskripsi)) !!}
                    </div>
                </div>

                {{-- Kurikulum --}}
                <div class="kurikulum-section mb-5">
                    <h4 class="fw-bold mb-4 border-start border-4 border-primary ps-3">Kurikulum Pelatihan</h4>
                    <div class="accordion accordion-flush shadow-sm rounded-4 overflow-hidden border" id="curriculumAccordion">
                        @forelse($course->modules as $index => $module)
                            <div class="accordion-item border-bottom">
                                <h2 class="accordion-header" id="heading{{ $module->id }}">
                                    <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }} py-3" 
                                            type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse{{ $module->id }}" 
                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                            aria-controls="collapse{{ $module->id }}">
                                        <div class="d-flex align-items-center w-100">
                                            <span class="badge bg-light text-dark border me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                {{ $index + 1 }}
                                            </span>
                                            <span class="fw-bold text-dark">{{ $module->judul }}</span>
                                            <span class="ms-auto me-3 small text-muted">{{ $module->subModules->count() }} Pelajaran</span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse{{ $module->id }}" 
                                     class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                     aria-labelledby="heading{{ $module->id }}" 
                                     data-bs-parent="#curriculumAccordion">
                                    <div class="accordion-body bg-light p-0">
                                        <ul class="list-group list-group-flush">
                                            @foreach($module->subModules as $subModule)
                                                <li class="list-group-item bg-transparent d-flex align-items-center py-3 px-4 border-0">
                                                    <i class="bi bi-play-circle me-3 text-primary opacity-75"></i>
                                                    <span class="text-secondary small fw-medium">{{ $subModule->judul }}</span>
                                                    <i class="bi bi-lock-fill ms-auto text-muted opacity-50 small"></i>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">Materi belum tersedia.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Instructor --}}
                @if($course->owner)
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h4 class="fw-bold mb-4 border-start border-4 border-primary ps-3">Instruktur Pelatihan</h4>
                        <div class="d-flex align-items-start gap-4">
                            <div class="instructor-avatar flex-shrink-0">
                                @if($course->owner->profile_photo_url)
                                    <img src="{{ $course->owner->profile_photo_url }}" class="rounded-circle shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px; font-size: 2rem;">
                                        {{ strtoupper(substr($course->owner->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="instructor-info">
                                <h5 class="fw-bold mb-1 text-dark">{{ $course->owner->name }}</h5>
                                <p class="text-primary small fw-semibold mb-2">Instruktur BPSDM</p>
                                <p class="text-secondary small mb-0">
                                    Berpengalaman dalam pengembangan kompetensi aparatur negara dan fasilitator berbagai pelatihan teknis di BPSDM Provinsi Sulawesi Tenggara.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right column --}}
            <div class="col-lg-4">
                <div class="course-sidebar sticky-top" style="top: 100px;">
                    <div class="card border-0 shadow rounded-4 overflow-hidden mb-4">
                        <div class="p-4 bg-white">
                            <h3 class="fw-bold text-dark mb-4">Gratis</h3>
                            
                            {{-- CTA logic --}}
                            @auth
                                @if($isEnrolled)
                                    <a href="{{ route('student.courses.show', $course->id) }}" class="btn btn-primary btn-lg w-100 rounded-pill mb-3 py-3 fw-bold premium-btn">
                                        <i class="bi bi-play-circle-fill me-2"></i>Lanjut Belajar
                                    </a>
                                    <p class="text-center small text-success fw-semibold"><i class="bi bi-patch-check-fill me-1"></i>Anda sudah terdaftar</p>
                                @else
                                    <form action="{{ route('student.enroll', $course->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill mb-3 py-3 fw-bold shadow premium-btn">
                                            <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 rounded-pill mb-3 py-3 fw-bold shadow premium-btn">
                                    <i class="bi bi-pencil-square me-2"></i>Daftar Sekarang
                                </a>
                                <p class="text-center small text-muted mb-0">Silakan login untuk pendaftaran</p>
                            @endauth
                        </div>

<style>
    .premium-btn {
        background: linear-gradient(135deg, #21b3ca 0%, #003f7d 100%);
        border: none;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .premium-btn:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 10px 20px rgba(33, 179, 202, 0.3);
        background: linear-gradient(135deg, #26c7e1 0%, #004d99 100%);
    }
    .premium-btn:active {
        transform: translateY(0) scale(1);
    }
</style>
                        </div>

                        <div class="p-4 bg-light border-top">
                            <h6 class="fw-bold mb-3 text-dark">Informasi Pelatihan</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between mb-2 small text-secondary">
                                    <span><i class="bi bi-bar-chart me-2"></i>Level</span>
                                    <span class="fw-bold text-dark">{{ $course->difficulty }}</span>
                                </li>
                                <li class="d-flex justify-content-between mb-2 small text-secondary">
                                    <span><i class="bi bi-journal-text me-2"></i>Modul</span>
                                    <span class="fw-bold text-dark">{{ $course->modules->count() }} Modul</span>
                                </li>
                                <li class="d-flex justify-content-between mb-2 small text-secondary">
                                    <span><i class="bi bi-award me-2"></i>Sertifikat</span>
                                    <span class="fw-bold text-dark">Lulus & Kompeten</span>
                                </li>
                                <li class="d-flex justify-content-between small text-secondary">
                                    <span><i class="bi bi-globe me-2"></i>Akses</span>
                                    <span class="fw-bold text-dark">Seumur Hidup</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    {{-- Schedule Card --}}
                    @if($course->start_date_time)
                        <div class="card border-0 shadow-sm rounded-4 p-4" style="background-color: #e3f2fd;">
                            <h6 class="fw-bold text-primary mb-3">Jadwal Pelatihan</h6>
                            <div class="small">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar-check me-2 text-primary"></i>
                                    <span class="text-secondary">Mulai: {{ $course->start_date_time->setTimezone(config('app.timezone'))->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-x me-2 text-danger"></i>
                                    <span class="text-secondary">Selesai: {{ $course->end_date_time ? $course->end_date_time->setTimezone(config('app.timezone'))->format('d M Y, H:i') : 'Tidak dibatasi' }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .course-hero {
        min-height: 400px;
    }
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #0d6efd;
    }
    .text-gradient {
        background: linear-gradient(45deg, #0d6efd, #0dcaf0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .course-description {
        line-height: 1.8;
    }
</style>
@endsection
