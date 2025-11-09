@extends('layouts.studentapp')

@section('title', $module->judul)

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dasbor</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.courses.index') }}">Pelatihan Saya</a></li>
            @if($module->course)
                <li class="breadcrumb-item">
                    <a href="{{ route('student.courses.show', $module->course_id) }}">{{ $module->course->judul }}</a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ $module->judul }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-12 col-lg-8">
            {{-- Module Header Card --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <h1 class="h3 fw-bold mb-2">{{ $module->judul }}</h1>
                            <p class="text-muted mb-2">
                                <i class="bi bi-list-ol me-2"></i>Urutan: {{ $module->urutan ?? '-' }}
                            </p>
                            <div class="mb-3">
                                @php
                                    $isCompleted = $completionPercentage >= 100;
                                @endphp
                                @if($isCompleted)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Selesai
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="bi bi-clock me-1"></i>Berlangsung
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @if($previousModule)
                                <a href="{{ route('student.modules.show', $previousModule->id) }}" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-chevron-left me-1"></i>Sebelumnya
                                </a>
                            @else
                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                    <i class="bi bi-chevron-left me-1"></i>Sebelumnya
                                </button>
                            @endif
                            @if($nextModule)
                                @php
                                    $canProceedToNext = $isModuleCompleted ?? false;
                                @endphp
                                @if($canProceedToNext)
                                    <a href="{{ route('student.modules.show', $nextModule->id) }}" 
                                       class="btn btn-primary btn-sm">
                                        Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                                    </a>
                                @else
                                    <button class="btn btn-primary btn-sm" disabled>
                                        Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                                    </button>
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-info-circle me-1"></i>Selesaikan semua sub-modul untuk melanjutkan
                                    </small>
                                @endif
                            @else
                                {{-- No next module - show button to go back to course --}}
                                @if($module->course)
                                    <a href="{{ route('student.courses.show', $module->course_id) }}" 
                                       class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle me-1"></i>Kembali ke Pelatihan
                                    </a>
                                @else
                                    <button class="btn btn-primary btn-sm" disabled>
                                        Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Module Progress --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold text-dark">Progress Modul</span>
                            <span class="small text-muted">{{ number_format($completionPercentage ?? 0, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar {{ $isCompleted ? 'bg-success' : 'bg-warning' }}" 
                                 role="progressbar" 
                                 style="width: {{ $completionPercentage ?? 0 }}%; border-radius: 10px;" 
                                 aria-valuenow="{{ $completionPercentage ?? 0 }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ $completedSubModules ?? 0 }} dari {{ $totalSubModules ?? 0 }} sub-modul selesai
                        </small>
                    </div>
                </div>
            </div>

            {{-- Module Description --}}
            @if($module->deskripsi)
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Deskripsi Modul</h5>
                </div>
                <div class="card-body">
                    <div class="text-muted">
                        {!! nl2br(e($module->deskripsi)) !!}
                    </div>
                </div>
            </div>
            @endif

            {{-- Sub-Modules List --}}
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Sub-Modul</h5>
                </div>
                <div class="card-body">
                    @if(($module->subModules ?? collect())->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                            <p class="text-muted mb-0">Belum ada sub-modul</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($module->subModules as $subModule)
                                @php
                                    $progress = $subModule->userProgress->first();
                                    $isSubCompleted = $progress && $progress->is_completed;
                                    $progressPercent = $progress ? $progress->progress_percentage : 0;
                                @endphp
                                <div class="list-group-item border-0 px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-2 fw-semibold">
                                                <a href="{{ route('student.sub_modules.show', $subModule->id) }}" 
                                                   class="text-decoration-none text-dark">
                                                    {{ $subModule->judul }}
                                                </a>
                                            </h6>
                                            <p class="small text-muted mb-2">
                                                <i class="bi bi-list-ol me-1"></i>Urutan: {{ $subModule->urutan ?? '-' }}
                                            </p>
                                            
                                            {{-- Sub-Module Progress --}}
                                            @if($progress)
                                            <div class="mt-2">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="small text-muted">Progress</span>
                                                    <span class="small text-muted">{{ number_format($progressPercent, 0) }}%</span>
                                                </div>
                                                <div class="progress" style="height: 6px; border-radius: 10px;">
                                                    <div class="progress-bar {{ $isSubCompleted ? 'bg-success' : 'bg-info' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $progressPercent }}%; border-radius: 10px;" 
                                                         aria-valuenow="{{ $progressPercent }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="ms-3 d-flex flex-column align-items-end">
                                            @if($isSubCompleted)
                                                <span class="badge bg-success mb-2">
                                                    <i class="bi bi-check-circle me-1"></i>Selesai
                                                </span>
                                            @elseif($progress)
                                                <span class="badge bg-warning mb-2">
                                                    <i class="bi bi-clock me-1"></i>Berlangsung
                                                </span>
                                            @else
                                                <span class="badge bg-secondary mb-2">
                                                    <i class="bi bi-circle me-1"></i>Belum Dimulai
                                                </span>
                                            @endif
                                            <a href="{{ route('student.sub_modules.show', $subModule->id) }}" 
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
            {{-- Module Info Card --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Informasi Modul</h5>
                </div>
                <div class="card-body">
                    <dl class="row g-3 mb-0">
                        <dt class="col-sm-5 text-muted small">Urutan</dt>
                        <dd class="col-sm-7 small">{{ $module->urutan ?? '-' }}</dd>
                        
                        <dt class="col-sm-5 text-muted small">Progress</dt>
                        <dd class="col-sm-7 small">{{ number_format($completionPercentage ?? 0, 1) }}%</dd>
                        
                        <dt class="col-sm-5 text-muted small">Sub-Modul</dt>
                        <dd class="col-sm-7 small">{{ $totalSubModules ?? 0 }} Sub-modul</dd>
                        
                        <dt class="col-sm-5 text-muted small">Selesai</dt>
                        <dd class="col-sm-7 small">{{ $completedSubModules ?? 0 }} Sub-modul</dd>
                        
                        @if($module->course)
                        <dt class="col-sm-5 text-muted small">Pelatihan</dt>
                        <dd class="col-sm-7 small">
                            <a href="{{ route('student.courses.show', $module->course_id) }}" 
                               class="text-decoration-none">
                                {{ $module->course->judul }}
                            </a>
                        </dd>
                        @endif
                        
                        @if($module->updated_at)
                        <dt class="col-sm-5 text-muted small">Diperbarui</dt>
                        <dd class="col-sm-7 small">{{ $module->updated_at->diffForHumans() }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Navigation Card --}}
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Navigasi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($previousModule)
                            <a href="{{ route('student.modules.show', $previousModule->id) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-chevron-left me-1"></i>Modul Sebelumnya
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="bi bi-chevron-left me-1"></i>Modul Sebelumnya
                            </button>
                        @endif
                        
                        @if($nextModule)
                            @php
                                $canProceedToNext = $isModuleCompleted ?? false;
                            @endphp
                            @if($canProceedToNext)
                                <a href="{{ route('student.modules.show', $nextModule->id) }}" 
                                   class="btn btn-primary btn-sm">
                                    Modul Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                                </a>
                            @else
                                <button class="btn btn-primary btn-sm" disabled>
                                    Modul Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                                </button>
                                <small class="text-muted d-block mt-2">
                                    <i class="bi bi-info-circle me-1"></i>Selesaikan semua sub-modul untuk melanjutkan
                                </small>
                            @endif
                        @else
                            {{-- No next module - show button to go back to course --}}
                            @if($module->course)
                                <a href="{{ route('student.courses.show', $module->course_id) }}" 
                                   class="btn btn-success btn-sm">
                                    <i class="bi bi-check-circle me-1"></i>Kembali ke Pelatihan
                                </a>
                            @else
                                <button class="btn btn-primary btn-sm" disabled>
                                    Modul Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                                </button>
                            @endif
                        @endif
                        
                        @if($module->course)
                            <a href="{{ route('student.courses.show', $module->course_id) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke Pelatihan
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
