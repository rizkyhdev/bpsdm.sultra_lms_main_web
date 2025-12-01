@extends('layouts.studentapp')

@section('title', __('Lihat Sertifikat'))

@section('content')
<div class="container-fluid my-3">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('student.dashboard') }}">{{ __('Dasbor') }}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('student.certificates.index') }}">{{ __('Daftar Sertifikat') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $course->judul ?? $course->title }}
            </li>
        </ol>
    </nav>

    <div class="row g-3">
        <div class="col-12 col-xl-9">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">
                            {{ __('Sertifikat Pelatihan') }}
                        </h5>
                        <small class="text-muted">
                            {{ $course->judul ?? $course->title }}
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ $downloadUrl }}" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                            <i class="bi bi-download me-1"></i>{{ __('Unduh') }}
                        </a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @include('partials.pdf-viewer', [
                        'pdfUrl' => $pdfUrl,
                        'downloadUrl' => $downloadUrl,
                        'title' => $course->judul ?? $course->title,
                    ])
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-3">
            <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
                <div class="card-header bg-white border-0">
                    <h6 class="fw-bold mb-0">{{ __('Informasi Sertifikat') }}</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">{{ __('Pelatihan') }}</dt>
                        <dd class="col-7">{{ $course->judul ?? $course->title }}</dd>

                        @if(!empty($course->bidang_kompetensi))
                        <dt class="col-5 text-muted">{{ __('Bidang') }}</dt>
                        <dd class="col-7">{{ $course->bidang_kompetensi }}</dd>
                        @endif

                        @if(!empty($course->jp_value))
                        <dt class="col-5 text-muted">{{ __('JP') }}</dt>
                        <dd class="col-7">{{ $course->jp_value }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('student.certificates.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>{{ __('Kembali ke daftar sertifikat') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


