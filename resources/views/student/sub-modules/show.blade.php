@extends('layouts.studentapp')

@section('title', $subModule->judul)

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dasbor</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.courses.index') }}">Pelatihan Saya</a></li>
            @if($course)
                <li class="breadcrumb-item">
                    <a href="{{ route('student.courses.show', $course->id) }}">{{ $course->judul }}</a>
                </li>
            @endif
            @if($module)
                <li class="breadcrumb-item">
                    <a href="{{ route('student.modules.show', $module->id) }}">{{ $module->judul }}</a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ $subModule->judul }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-12 col-lg-8">
            {{-- Sub-Module Header Card --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <h1 class="h3 fw-bold mb-2">{{ $subModule->judul }}</h1>
                            <p class="text-muted mb-2">
                                <i class="bi bi-list-ol me-2"></i>Urutan: {{ $subModule->urutan ?? '-' }}
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
                            @if($previousSubModule)
                                <a href="{{ route('student.sub_modules.show', $previousSubModule->id) }}" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-chevron-left me-1"></i>Sebelumnya
                                </a>
                            @else
                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                    <i class="bi bi-chevron-left me-1"></i>Sebelumnya
                                </button>
                            @endif
                            @if($nextSubModule)
                                <a href="{{ route('student.sub_modules.show', $nextSubModule->id) }}" 
                                   class="btn btn-primary btn-sm">
                                    Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                                </a>
                            @else
                                {{-- No next sub-module - check if we can go to next module --}}
                                @if(isset($nextModule) && $nextModule)
                                    <a href="{{ route('student.modules.show', $nextModule->id) }}" 
                                       class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle me-1"></i>Modul Selanjutnya
                                    </a>
                                @else
                                    <button class="btn btn-primary btn-sm" disabled>
                                        Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Sub-Module Progress --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold text-dark">Progress Sub-Modul</span>
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
                            {{ $completedContents ?? 0 }} dari {{ $totalContents ?? 0 }} konten selesai
                        </small>
                    </div>
                </div>
            </div>

            {{-- Sub-Module Description --}}
            @if($subModule->deskripsi)
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Deskripsi Sub-Modul</h5>
                </div>
                <div class="card-body">
                    <div class="text-muted">
                        {!! nl2br(e($subModule->deskripsi)) !!}
                    </div>
                </div>
            </div>
            @endif

            {{-- Contents List --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Konten</h5>
                </div>
                <div class="card-body">
                    @if(($contents ?? collect())->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                            <p class="text-muted mb-0">Belum ada konten</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($contents as $content)
                                @php
                                    $contentProgress = $content->userProgress->first();
                                    $isContentCompleted = $contentProgress && $contentProgress->is_completed;
                                    $contentProgressPercent = $contentProgress ? $contentProgress->progress_percentage : 0;
                                @endphp
                                <div class="list-group-item border-0 px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-2 fw-semibold">
                                                <a href="{{ route('student.contents.show', $content->id) }}" 
                                                   class="text-decoration-none text-dark">
                                                    {{ $content->judul }}
                                                </a>
                                            </h6>
                                            <p class="small text-muted mb-2">
                                                <i class="bi bi-list-ol me-1"></i>Urutan: {{ $content->urutan ?? '-' }}
                                                <span class="ms-2">
                                                    @if($content->tipe === 'youtube')
                                                        <i class="bi bi-youtube me-1"></i>YouTube Video
                                                    @elseif($content->tipe === 'video')
                                                        <i class="bi bi-play-circle me-1"></i>Video
                                                    @elseif($content->tipe === 'html' || $content->tipe === 'text')
                                                        <i class="bi bi-file-text me-1"></i>Text
                                                    @elseif($content->tipe === 'pdf')
                                                        <i class="bi bi-file-pdf me-1"></i>PDF
                                                    @elseif($content->tipe === 'audio')
                                                        <i class="bi bi-music-note me-1"></i>Audio
                                                    @elseif($content->tipe === 'image')
                                                        <i class="bi bi-image me-1"></i>Image
                                                    @elseif($content->tipe === 'link')
                                                        <i class="bi bi-link-45deg me-1"></i>Link
                                                    @endif
                                                </span>
                                            </p>
                                            
                                            {{-- Content Progress --}}
                                            @if($contentProgress)
                                            <div class="mt-2">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="small text-muted">Progress</span>
                                                    <span class="small text-muted">{{ number_format($contentProgressPercent, 0) }}%</span>
                                                </div>
                                                <div class="progress" style="height: 6px; border-radius: 10px;">
                                                    <div class="progress-bar {{ $isContentCompleted ? 'bg-success' : 'bg-info' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $contentProgressPercent }}%; border-radius: 10px;" 
                                                         aria-valuenow="{{ $contentProgressPercent }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="ms-3 d-flex flex-column align-items-end">
                                            @if($isContentCompleted)
                                                <span class="badge bg-success mb-2">
                                                    <i class="bi bi-check-circle me-1"></i>Selesai
                                                </span>
                                            @elseif($contentProgress)
                                                <span class="badge bg-warning mb-2">
                                                    <i class="bi bi-clock me-1"></i>Berlangsung
                                                </span>
                                            @else
                                                <span class="badge bg-secondary mb-2">
                                                    <i class="bi bi-circle me-1"></i>Belum Dimulai
                                                </span>
                                            @endif
                                            <a href="{{ route('student.contents.show', $content->id) }}" 
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

            {{-- Quizzes List --}}
            @if(($subModuleQuizzes ?? collect())->isNotEmpty())
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Quiz</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($subModuleQuizzes as $quiz)
                            @php
                                $user = auth()->user();
                                $quizAttempts = $user->quizAttempts()
                                    ->where('quiz_id', $quiz->id)
                                    ->get();
                                $passedAttempt = $quizAttempts->where('status', 'passed')->first();
                                $activeAttempt = $quizAttempts->where('status', 'in_progress')->first();
                                $isPassed = $passedAttempt !== null;
                                $canTakeQuiz = !$isPassed && (!$quiz->max_attempts || $quizAttempts->where('status', '!=', 'in_progress')->count() < $quiz->max_attempts);
                            @endphp
                            <div class="list-group-item border-0 px-0 py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-semibold">
                                            <a href="{{ route('student.quizzes.show', $quiz->id) }}" 
                                               class="text-decoration-none text-dark">
                                                {{ $quiz->judul }}
                                            </a>
                                        </h6>
                                        @if($quiz->deskripsi)
                                        <p class="small text-muted mb-2">
                                            {{ Str::limit($quiz->deskripsi, 100) }}
                                        </p>
                                        @endif
                                        <div class="d-flex gap-3 small text-muted">
                                            <span><i class="bi bi-check-circle me-1"></i>Nilai Minimum: {{ $quiz->nilai_minimum }}%</span>
                                            @if($quiz->max_attempts)
                                            <span><i class="bi bi-arrow-repeat me-1"></i>Maks Attempts: {{ $quiz->max_attempts }}</span>
                                            @endif
                                            @if($passedAttempt)
                                            <span><i class="bi bi-trophy me-1"></i>Nilai: {{ number_format($passedAttempt->score, 1) }}%</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ms-3 d-flex flex-column align-items-end">
                                        @if($isPassed)
                                            <span class="badge bg-success mb-2">
                                                <i class="bi bi-check-circle me-1"></i>Lulus
                                            </span>
                                        @elseif($activeAttempt)
                                            <span class="badge bg-warning mb-2">
                                                <i class="bi bi-clock me-1"></i>Berlangsung
                                            </span>
                                        @else
                                            <span class="badge bg-secondary mb-2">
                                                <i class="bi bi-circle me-1"></i>Belum Dimulai
                                            </span>
                                        @endif
                                        <a href="{{ route('student.quizzes.show', $quiz->id) }}" 
                                           class="btn btn-sm {{ $isPassed ? 'btn-outline-success' : 'btn-primary' }} mt-2">
                                            <i class="bi bi-arrow-right me-1"></i>{{ $isPassed ? 'Lihat Hasil' : ($activeAttempt ? 'Lanjutkan' : 'Mulai') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr class="my-0">
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-lg-4">
            {{-- Sub-Module Info Card --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Informasi Sub-Modul</h5>
                </div>
                <div class="card-body">
                    <dl class="row g-3 mb-0">
                        <dt class="col-sm-5 text-muted small">Urutan</dt>
                        <dd class="col-sm-7 small">{{ $subModule->urutan ?? '-' }}</dd>
                        
                        <dt class="col-sm-5 text-muted small">Progress</dt>
                        <dd class="col-sm-7 small">{{ number_format($completionPercentage ?? 0, 1) }}%</dd>
                        
                        <dt class="col-sm-5 text-muted small">Konten</dt>
                        <dd class="col-sm-7 small">{{ $totalContents ?? 0 }} Konten</dd>
                        
                        <dt class="col-sm-5 text-muted small">Selesai</dt>
                        <dd class="col-sm-7 small">{{ $completedContents ?? 0 }} Konten</dd>
                        
                        @if($module)
                        <dt class="col-sm-5 text-muted small">Modul</dt>
                        <dd class="col-sm-7 small">
                            <a href="{{ route('student.modules.show', $module->id) }}" 
                               class="text-decoration-none">
                                {{ $module->judul }}
                            </a>
                        </dd>
                        @endif
                        
                        @if($course)
                        <dt class="col-sm-5 text-muted small">Pelatihan</dt>
                        <dd class="col-sm-7 small">
                            <a href="{{ route('student.courses.show', $course->id) }}" 
                               class="text-decoration-none">
                                {{ $course->judul }}
                            </a>
                        </dd>
                        @endif
                        
                        @if($subModule->updated_at)
                        <dt class="col-sm-5 text-muted small">Diperbarui</dt>
                        <dd class="col-sm-7 small">{{ $subModule->updated_at->diffForHumans() }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Navigation Card --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Navigasi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($previousSubModule)
                            <a href="{{ route('student.sub_modules.show', $previousSubModule->id) }}" 
                               class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-chevron-left me-1"></i>Sub-Modul Sebelumnya
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="bi bi-chevron-left me-1"></i>Sub-Modul Sebelumnya
                            </button>
                        @endif
                        
                        @if($nextSubModule)
                            <a href="{{ route('student.sub_modules.show', $nextSubModule->id) }}" 
                               class="btn btn-primary btn-sm">
                                Sub-Modul Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        @else
                            {{-- No next sub-module - check if we can go to next module --}}
                            @if(isset($nextModule) && $nextModule)
                                <a href="{{ route('student.modules.show', $nextModule->id) }}" 
                                   class="btn btn-success btn-sm">
                                    <i class="bi bi-check-circle me-1"></i>Modul Selanjutnya
                                </a>
                            @else
                                <button class="btn btn-primary btn-sm" disabled>
                                    Sub-Modul Selanjutnya<i class="bi bi-chevron-right ms-1"></i>
                                </button>
                            @endif
                        @endif
                        
                        @if($module)
                            <a href="{{ route('student.modules.show', $module->id) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke Modul
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Mark Complete Button --}}
            @if($canMarkComplete && !$isCompleted)
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body">
                    <button id="markCompleteBtn" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-1"></i>Tandai sebagai Selesai
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($canMarkComplete && !$isCompleted)
<script>
document.getElementById('markCompleteBtn').addEventListener('click', function() {
    if (confirm('Apakah Anda yakin ingin menandai sub-modul ini sebagai selesai?')) {
        fetch('{{ route("student.sub_modules.mark-complete", $subModule->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan saat menandai sub-modul sebagai selesai.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menandai sub-modul sebagai selesai.');
        });
    }
});
</script>
@endif
@endsection

