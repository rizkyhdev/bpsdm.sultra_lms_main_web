@extends('layouts.studentapp')

@section('title', 'My Quizzes')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dasbor</a></li>
            <li class="breadcrumb-item active">Kuis Saya</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Kuis Saya</h4>
            <small class="text-muted">Semua kuis dari pelatihan yang Anda ikuti</small>
        </div>
        <div>
            <a href="{{ route('student.courses.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Pelatihan
            </a>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('student.quizzes.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Cari Kuis</label>
                    <input type="text" name="q" class="form-control" placeholder="Cari berdasarkan judul..." value="{{ request('q') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Belum Dimulai</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Berlangsung</option>
                        <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}>Lulus</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Tidak Lulus</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pelatihan</label>
                    <select name="course_id" class="form-select">
                        <option value="">Semua Pelatihan</option>
                        @foreach($enrollments as $enrollment)
                            <option value="{{ $enrollment->course_id }}" {{ request('course_id') == $enrollment->course_id ? 'selected' : '' }}>
                                {{ $enrollment->course->judul }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Quizzes List --}}
    @if($quizzes->count() > 0)
        <div class="row g-4">
            @foreach($quizzes as $quiz)
                @php
                    $stats = $quizStats[$quiz->id] ?? [];
                    $course = $quiz->course ?? $quiz->module->course ?? $quiz->subModule->module->course ?? null;
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                        <div class="card-body d-flex flex-column">
                            {{-- Quiz Header --}}
                            <div class="mb-3">
                                <h5 class="card-title fw-bold mb-2">{{ $quiz->judul }}</h5>
                                @if($quiz->deskripsi)
                                    <p class="text-muted small mb-2">{{ Str::limit($quiz->deskripsi, 100) }}</p>
                                @endif
                                
                                {{-- Course/Module Info --}}
                                @if($course)
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-book me-1"></i>
                                            <a href="{{ route('student.courses.show', $course->id) }}" class="text-decoration-none">
                                                {{ $course->judul }}
                                            </a>
                                        </small>
                                    </div>
                                @endif
                                
                                @if($quiz->subModule)
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-layers me-1"></i>
                                            {{ $quiz->subModule->module->judul ?? '' }} > {{ $quiz->subModule->judul }}
                                        </small>
                                    </div>
                                @endif
                            </div>

                            {{-- Quiz Info Badges --}}
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-primary">
                                    <i class="bi bi-check-circle me-1"></i>Min: {{ $quiz->nilai_minimum }}%
                                </span>
                                @if($quiz->max_attempts)
                                    <span class="badge bg-info">
                                        <i class="bi bi-arrow-repeat me-1"></i>Max: {{ $quiz->max_attempts }}
                                    </span>
                                @endif
                                <span class="badge bg-secondary">
                                    <i class="bi bi-question-circle me-1"></i>{{ $quiz->questions()->count() }} Soal
                                </span>
                            </div>

                            {{-- Statistics --}}
                            @if(!empty($stats))
                                <div class="mb-3 p-2 bg-light rounded">
                                    <div class="row g-2 text-center">
                                        <div class="col-4">
                                            <div class="small text-muted">Attempts</div>
                                            <div class="fw-bold">{{ $stats['total_attempts'] ?? 0 }}</div>
                                        </div>
                                        <div class="col-4">
                                            <div class="small text-muted">Best Score</div>
                                            <div class="fw-bold {{ ($stats['best_score'] ?? 0) >= $quiz->nilai_minimum ? 'text-success' : 'text-danger' }}">
                                                {{ $stats['best_score'] ? number_format($stats['best_score'], 1) . '%' : '-' }}
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="small text-muted">Status</div>
                                            <div>
                                                @if($stats['has_passed'] ?? false)
                                                    <span class="badge bg-success">Lulus</span>
                                                @elseif($stats['completed_attempts'] > 0)
                                                    <span class="badge bg-danger">Tidak Lulus</span>
                                                @else
                                                    <span class="badge bg-secondary">Belum</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Action Button --}}
                            <div class="mt-auto">
                                <a href="{{ route('student.quizzes.show', $quiz->id) }}" class="btn btn-primary w-100">
                                    <i class="bi bi-play-circle me-1"></i>
                                    @if($stats['can_take'] ?? true)
                                        Mulai Kuis
                                    @else
                                        Lihat Detail
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($quizzes->hasPages())
            <div class="mt-4">
                {{ $quizzes->links() }}
            </div>
        @endif
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">Tidak Ada Kuis</h5>
                <p class="text-muted">Anda belum memiliki kuis yang tersedia. Mulai pelatihan untuk mengakses kuis.</p>
                <a href="{{ route('student.courses.index') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-book me-1"></i>Lihat Pelatihan
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

