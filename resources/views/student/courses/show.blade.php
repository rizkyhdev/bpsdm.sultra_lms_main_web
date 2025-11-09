@extends('layouts.studentapp')

@section('title', $courseData->title ?? $course->judul)

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dasbor</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.courses.index') }}">Pelatihan Saya</a></li>
            <li class="breadcrumb-item active">{{ $courseData->title ?? $course->judul }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-12 col-lg-8">
            {{-- Course Header Card --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12 col-md-4">
                            <img src="{{ $courseData->cover_url ?? asset('image/course-placeholder.png') }}" 
                                 alt="{{ $courseData->title ?? $course->judul }}" 
                                 class="w-100 rounded" 
                                 style="height: 200px; object-fit: cover;">
                        </div>
                        <div class="col-12 col-md-8">
                            <h1 class="h3 fw-bold mb-2">{{ $courseData->title ?? $course->judul }}</h1>
                            <p class="text-muted mb-3">
                                <i class="fas fa-user me-2"></i>{{ __('By') }} {{ $courseData->instructor_name ?? ($course->owner ? $course->owner->name : __('Instructor')) }}
                            </p>
                            
                            {{-- Overall Progress --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-semibold text-dark">Progress Keseluruhan</span>
                                    <span class="small text-muted">{{ number_format($overallProgress ?? $courseData->progress_percent ?? 0, 0) }}%</span>
                                </div>
                                <div class="progress" style="height: 10px; border-radius: 10px;">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: {{ $overallProgress ?? $courseData->progress_percent ?? 0 }}%; border-radius: 10px;" 
                                         aria-valuenow="{{ $overallProgress ?? $courseData->progress_percent ?? 0 }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>

                            {{-- Course Meta Info --}}
                            <div class="d-flex flex-wrap gap-3 small text-muted">
                                @if($courseData->jp_value ?? $course->jp_value)
                                    <span><i class="bi bi-clock me-1"></i>{{ $courseData->jp_value ?? $course->jp_value }} JP</span>
                                @endif
                                @if($courseData->bidang_kompetensi ?? $course->bidang_kompetensi)
                                    <span><i class="bi bi-tag me-1"></i>{{ $courseData->bidang_kompetensi ?? $course->bidang_kompetensi }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Course Description --}}
            @if($courseData->description ?? $course->deskripsi)
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Deskripsi Pelatihan</h5>
                </div>
                <div class="card-body">
                    <div class="text-muted">
                        {!! nl2br(e($courseData->description ?? $course->deskripsi)) !!}
                    </div>
                </div>
            </div>
            @endif

            {{-- Modules List --}}
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Modul Pelatihan</h5>
                </div>
                <div class="card-body">
                    @if(($modules ?? collect())->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                            <p class="text-muted mb-0">{{ __('No modules yet') }}</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($modules as $module)
                                @php
                                    $progress = $moduleProgress[$module->id] ?? ['total' => 0, 'completed' => 0, 'percentage' => 0];
                                    $isCompleted = $progress['percentage'] >= 100;
                                @endphp
                                <div class="list-group-item border-0 px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-2 fw-semibold">
                                                <a href="{{ route('student.modules.show', $module->id) }}" 
                                                   class="text-decoration-none text-dark">
                                                    {{ $module->judul }}
                                                </a>
                                            </h6>
                                            <p class="small text-muted mb-2">
                                                <i class="bi bi-list-ol me-1"></i>Urutan: {{ $module->urutan ?? '-' }}
                                                <span class="mx-2">•</span>
                                                <i class="bi bi-file-earmark-text me-1"></i>{{ $progress['total'] }} Sub-modul
                                            </p>
                                            
                                            {{-- Module Progress --}}
                                            <div class="mt-2">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="small text-muted">Progress Modul</span>
                                                    <span class="small text-muted">{{ number_format($progress['percentage'], 0) }}%</span>
                                                </div>
                                                <div class="progress" style="height: 6px; border-radius: 10px;">
                                                    <div class="progress-bar {{ $isCompleted ? 'bg-success' : 'bg-info' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $progress['percentage'] }}%; border-radius: 10px;" 
                                                         aria-valuenow="{{ $progress['percentage'] }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $progress['completed'] }} dari {{ $progress['total'] }} sub-modul selesai
                                                </small>
                                            </div>
                                        </div>
                                        <div class="ms-3 d-flex flex-column align-items-end">
                                            @if($isCompleted)
                                                <span class="badge bg-success mb-2">
                                                    <i class="bi bi-check-circle me-1"></i>Selesai
                                                </span>
                                            @else
                                                <span class="badge bg-warning mb-2">
                                                    <i class="bi bi-clock me-1"></i>Berlangsung
                                                </span>
                                            @endif
                                            <a href="{{ route('student.modules.show', $module->id) }}" 
                                               class="btn btn-sm btn-primary mt-2">
                                                <i class="bi bi-arrow-right me-1"></i>Lihat
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <hr class="my-0">
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-lg-4">
            {{-- Course Info Card --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Informasi Pelatihan</h5>
                </div>
                <div class="card-body">
                    <dl class="row g-3 mb-0">
                        <dt class="col-sm-5 text-muted small">Instruktur</dt>
                        <dd class="col-sm-7 small">{{ $courseData->instructor_name ?? ($course->owner ? $course->owner->name : __('Instructor')) }}</dd>
                        
                        <dt class="col-sm-5 text-muted small">Progress</dt>
                        <dd class="col-sm-7 small">{{ number_format($overallProgress ?? $courseData->progress_percent ?? 0, 0) }}%</dd>
                        
                        @if($courseData->jp_value ?? $course->jp_value)
                        <dt class="col-sm-5 text-muted small">Nilai JP</dt>
                        <dd class="col-sm-7 small">{{ $courseData->jp_value ?? $course->jp_value }} JP</dd>
                        @endif
                        
                        @if($courseData->bidang_kompetensi ?? $course->bidang_kompetensi)
                        <dt class="col-sm-5 text-muted small">Bidang Kompetensi</dt>
                        <dd class="col-sm-7 small">{{ $courseData->bidang_kompetensi ?? $course->bidang_kompetensi }}</dd>
                        @endif
                        
                        <dt class="col-sm-5 text-muted small">Total Modul</dt>
                        <dd class="col-sm-7 small">{{ $modules->count() ?? 0 }} Modul</dd>
                        
                        @if($courseData->updated_at ?? $course->updated_at)
                        <dt class="col-sm-5 text-muted small">Diperbarui</dt>
                        <dd class="col-sm-7 small">{{ ($courseData->updated_at ?? $course->updated_at)->diffForHumans() }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Statistics Card --}}
            @if(isset($totalStudents) || isset($completedStudents))
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Statistik</h5>
                </div>
                <div class="card-body">
                    <dl class="row g-3 mb-0">
                        @if(isset($totalStudents))
                        <dt class="col-sm-6 text-muted small">Total Peserta</dt>
                        <dd class="col-sm-6 small">{{ $totalStudents }}</dd>
                        @endif
                        
                        @if(isset($completedStudents))
                        <dt class="col-sm-6 text-muted small">Selesai</dt>
                        <dd class="col-sm-6 small">{{ $completedStudents }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
