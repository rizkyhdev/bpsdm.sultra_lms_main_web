@extends('layouts.studentapp')

@section('title', 'Review Quiz')

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
            <li class="breadcrumb-item active">Review</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-12">
            {{-- Review Header --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body">
                    <h1 class="h3 fw-bold mb-3">{{ $attempt->quiz->judul }}</h1>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Nilai</h6>
                                    <h3 class="fw-bold mb-0 {{ $isPassed ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($score, 1) }}%
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Status</h6>
                                    <h5 class="mb-0">
                                        @if($isPassed)
                                            <span class="badge bg-success">Lulus</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Lulus</span>
                                        @endif
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Jawaban Benar</h6>
                                    <h3 class="fw-bold text-success mb-0">{{ $correctAnswers }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">Total Pertanyaan</h6>
                                    <h3 class="fw-bold mb-0">{{ $totalQuestions }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detailed Review --}}
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="fw-bold mb-0">Review Detail Jawaban</h5>
                </div>
                <div class="card-body">
                    @foreach($userAnswers as $index => $userAnswer)
                        @php
                            $question = $userAnswer->question;
                            $selectedOption = $userAnswer->selectedAnswerOption;
                            $correctOption = $question->answerOptions->where('is_correct', true)->first();
                            $allOptions = $question->answerOptions;
                        @endphp
                        <div class="card mb-4 {{ $userAnswer->is_correct ? 'border-success' : 'border-danger' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="fw-semibold mb-0">
                                        <span class="badge {{ $userAnswer->is_correct ? 'bg-success' : 'bg-danger' }} me-2">
                                            {{ $index + 1 }}
                                        </span>
                                        {{ $question->pertanyaan }}
                                    </h6>
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
                                
                                <div class="mb-3">
                                    <strong class="text-muted d-block mb-2">Pilihan Jawaban:</strong>
                                    <div class="list-group">
                                        @foreach($allOptions as $option)
                                            <div class="list-group-item {{ $option->is_correct ? 'list-group-item-success' : ($selectedOption && $selectedOption->id === $option->id && !$option->is_correct ? 'list-group-item-danger' : '') }}">
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="radio" 
                                                           {{ $selectedOption && $selectedOption->id === $option->id ? 'checked' : '' }} 
                                                           disabled>
                                                    <label class="form-check-label w-100">
                                                        {{ $option->teks }}
                                                        @if($option->is_correct)
                                                            <span class="badge bg-success ms-2">
                                                                <i class="bi bi-check-circle me-1"></i>Jawaban Benar
                                                            </span>
                                                        @endif
                                                        @if($selectedOption && $selectedOption->id === $option->id && !$option->is_correct)
                                                            <span class="badge bg-danger ms-2">
                                                                <i class="bi bi-x-circle me-1"></i>Jawaban Anda
                                                            </span>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                @if($selectedOption && !$userAnswer->is_correct)
                                <div class="alert alert-danger mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Jawaban Anda:</strong> {{ $selectedOption->teks }}
                                </div>
                                @elseif(!$selectedOption)
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <strong>Anda tidak menjawab pertanyaan ini.</strong>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="card shadow-sm border-0 mt-4" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('student.quizzes.result', $attempt->id) }}" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i>Kembali ke Hasil
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

