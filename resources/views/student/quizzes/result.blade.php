@extends('layouts.studentapp')

@section('title', 'Hasil Quiz')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dasbor</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.courses.index') }}">Pelatihan Saya</a></li>
            @if($attempt->quiz->subModule && $attempt->quiz->subModule->module && $attempt->quiz->subModule->module->course)
                <li class="breadcrumb-item">
                    <a href="{{ route('student.courses.show', $attempt->quiz->subModule->module->course_id) }}">
                        {{ $attempt->quiz->subModule->module->course->judul }}
                    </a>
                </li>
            @endif
            @if($attempt->quiz->subModule && $attempt->quiz->subModule->module)
                <li class="breadcrumb-item">
                    <a href="{{ route('student.modules.show', $attempt->quiz->subModule->module_id) }}">
                        {{ $attempt->quiz->subModule->module->judul }}
                    </a>
                </li>
            @endif
            @if($attempt->quiz->subModule)
                <li class="breadcrumb-item">
                    <a href="{{ route('student.sub_modules.show', $attempt->quiz->subModule->id) }}">
                        {{ $attempt->quiz->subModule->judul }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item">
                <a href="{{ route('student.quizzes.show', $attempt->quiz->id) }}">
                    {{ $attempt->quiz->judul }}
                </a>
            </li>
            <li class="breadcrumb-item active">Hasil Quiz</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-12 col-lg-8">
            {{-- Result Header --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body text-center">
                    <h1 class="h3 fw-bold mb-3">{{ $attempt->quiz->judul }}</h1>
                    
                    @if($isPassed)
                        <div class="alert alert-success mb-4">
                            <i class="bi bi-check-circle fs-1 mb-2"></i>
                            <h4 class="fw-bold">Selamat! Anda Lulus</h4>
                            <p class="mb-0">Nilai Anda: <strong>{{ number_format($score, 1) }}%</strong></p>
                            <p class="mb-0">Nilai Minimum: {{ $attempt->quiz->nilai_minimum }}%</p>
                        </div>
                    @else
                        <div class="alert alert-danger mb-4">
                            <i class="bi bi-x-circle fs-1 mb-2"></i>
                            <h4 class="fw-bold">Maaf, Anda Belum Lulus</h4>
                            <p class="mb-0">Nilai Anda: <strong>{{ number_format($score, 1) }}%</strong></p>
                            <p class="mb-0">Nilai Minimum: {{ $attempt->quiz->nilai_minimum }}%</p>
                            <p class="mb-0 mt-2">Anda membutuhkan minimal {{ $attempt->quiz->nilai_minimum }}% untuk lulus.</p>
                        </div>
                    @endif

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Total Pertanyaan</h6>
                                    <h3 class="fw-bold mb-0">{{ $totalQuestions }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Jawaban Benar</h6>
                                    <h3 class="fw-bold text-success mb-0">{{ $correctAnswers }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">Jawaban Salah</h6>
                                    <h3 class="fw-bold text-danger mb-0">{{ $totalQuestions - $correctAnswers }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="progress mb-4" style="height: 30px;">
                        <div class="progress-bar {{ $isPassed ? 'bg-success' : 'bg-danger' }}" 
                             role="progressbar" 
                             style="width: {{ $score }}%;" 
                             aria-valuenow="{{ $score }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ number_format($score, 1) }}%
                        </div>
                    </div>
                </div>
            </div>

            {{-- Answer Review --}}
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Review Jawaban</h5>
                </div>
                <div class="card-body">
                    @foreach($userAnswers as $index => $userAnswer)
                        @php
                            $question = $userAnswer->question;
                            $selectedOption = $userAnswer->answerOption;
                            $correctOption = $question->answerOptions->where('is_correct', true)->first();
                        @endphp
                        <div class="card mb-3 {{ $userAnswer->is_correct ? 'border-success' : 'border-danger' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="fw-semibold mb-0">{{ $index + 1 }}. {{ $question->pertanyaan }}</h6>
                                    @if($userAnswer->is_correct)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Benar
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>Salah
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="mb-2">
                                    <strong class="text-muted">Jawaban Anda:</strong>
                                    <div class="mt-1">
                                        @if($selectedOption)
                                            <span class="badge {{ $userAnswer->is_correct ? 'bg-success' : 'bg-danger' }}">
                                                {{ $selectedOption->teks }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Tidak dijawab</span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if(!$userAnswer->is_correct && $correctOption)
                                <div class="mb-2">
                                    <strong class="text-muted">Jawaban Benar:</strong>
                                    <div class="mt-1">
                                        <span class="badge bg-success">
                                            {{ $correctOption->teks }}
                                        </span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-lg-4">
            {{-- Quiz Info --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Informasi Quiz</h5>
                </div>
                <div class="card-body">
                    <dl class="row g-3 mb-0">
                        <dt class="col-sm-5 text-muted small">Nilai</dt>
                        <dd class="col-sm-7 small fw-bold">{{ number_format($score, 1) }}%</dd>
                        
                        <dt class="col-sm-5 text-muted small">Status</dt>
                        <dd class="col-sm-7 small">
                            @if($isPassed)
                                <span class="badge bg-success">Lulus</span>
                            @else
                                <span class="badge bg-danger">Tidak Lulus</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-5 text-muted small">Nilai Minimum</dt>
                        <dd class="col-sm-7 small">{{ $attempt->quiz->nilai_minimum }}%</dd>
                        
                        <dt class="col-sm-5 text-muted small">Total Pertanyaan</dt>
                        <dd class="col-sm-7 small">{{ $totalQuestions }}</dd>
                        
                        <dt class="col-sm-5 text-muted small">Jawaban Benar</dt>
                        <dd class="col-sm-7 small text-success fw-bold">{{ $correctAnswers }}</dd>
                        
                        <dt class="col-sm-5 text-muted small">Jawaban Salah</dt>
                        <dd class="col-sm-7 small text-danger fw-bold">{{ $totalQuestions - $correctAnswers }}</dd>
                        
                        @if($attempt->started_at)
                        <dt class="col-sm-5 text-muted small">Dimulai</dt>
                        <dd class="col-sm-7 small">{{ $attempt->started_at->format('d M Y H:i') }}</dd>
                        @endif
                        
                        @if($attempt->completed_at)
                        <dt class="col-sm-5 text-muted small">Selesai</dt>
                        <dd class="col-sm-7 small">{{ $attempt->completed_at->format('d M Y H:i') }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.quizzes.review', $attempt->id) }}" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>Review Detail
                        </a>
                        @if($attempt->quiz->subModule)
                        <a href="{{ route('student.sub_modules.show', $attempt->quiz->subModule->id) }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali ke Sub-Modul
                        </a>
                        @endif
                        <a href="{{ route('student.quizzes.show', $attempt->quiz->id) }}" 
                           class="btn btn-outline-info">
                            <i class="bi bi-arrow-clockwise me-1"></i>Kembali ke Quiz
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

