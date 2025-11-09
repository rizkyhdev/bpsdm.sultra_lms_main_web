@extends('layouts.studentapp')

@section('title', $quiz->judul)

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dasbor</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.courses.index') }}">Pelatihan Saya</a></li>
            @if($quiz->subModule && $quiz->subModule->module && $quiz->subModule->module->course)
                <li class="breadcrumb-item">
                    <a href="{{ route('student.courses.show', $quiz->subModule->module->course_id) }}">
                        {{ $quiz->subModule->module->course->judul }}
                    </a>
                </li>
            @endif
            @if($quiz->subModule && $quiz->subModule->module)
                <li class="breadcrumb-item">
                    <a href="{{ route('student.modules.show', $quiz->subModule->module_id) }}">
                        {{ $quiz->subModule->module->judul }}
                    </a>
                </li>
            @endif
            @if($quiz->subModule)
                <li class="breadcrumb-item">
                    <a href="{{ route('student.sub_modules.show', $quiz->subModule->id) }}">
                        {{ $quiz->subModule->judul }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active">{{ $quiz->judul }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-12 col-lg-8">
            {{-- Quiz Header --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body">
                    <h1 class="h3 fw-bold mb-3">{{ $quiz->judul }}</h1>
                    @if($quiz->deskripsi)
                    <p class="text-muted mb-3">{{ $quiz->deskripsi }}</p>
                    @endif
                    
                    <div class="d-flex flex-wrap gap-3 mb-3">
                        <span class="badge bg-primary">
                            <i class="bi bi-check-circle me-1"></i>Nilai Minimum: {{ $quiz->nilai_minimum }}%
                        </span>
                        @if($quiz->max_attempts)
                        <span class="badge bg-info">
                            <i class="bi bi-arrow-repeat me-1"></i>Maks Attempts: {{ $quiz->max_attempts }}
                        </span>
                        @endif
                        @if($quiz->questions()->count() > 0)
                        <span class="badge bg-secondary">
                            <i class="bi bi-question-circle me-1"></i>{{ $quiz->questions()->count() }} Pertanyaan
                        </span>
                        @endif
                    </div>

                    {{-- Active Attempt Alert --}}
                    @if($activeAttempt)
                    <div class="alert alert-warning">
                        <i class="bi bi-clock me-2"></i>
                        <strong>Attempt Berlangsung:</strong> Anda memiliki attempt yang sedang berlangsung. 
                        <a href="#" id="continueAttemptBtn" class="alert-link">Lanjutkan Attempt</a>
                    </div>
                    @endif

                    {{-- Previous Attempts --}}
                    @if($previousAttempts->isNotEmpty())
                    <div class="mt-3">
                        <h6 class="fw-semibold mb-2">Attempt Sebelumnya:</h6>
                        <div class="list-group">
                            @foreach($previousAttempts as $attempt)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-semibold">Attempt #{{ $loop->iteration }}</span>
                                        <span class="text-muted small ms-2">
                                            {{ $attempt->created_at->format('d M Y H:i') }}
                                        </span>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <span class="badge {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }}">
                                            {{ $attempt->is_passed ? 'Lulus' : 'Tidak Lulus' }}
                                        </span>
                                        <span class="fw-bold">{{ number_format($attempt->nilai, 1) }}%</span>
                                        <a href="{{ route('student.quizzes.review', $attempt->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>Review
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Quiz Actions --}}
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body">
                    @if($canTakeQuiz)
                        @if($activeAttempt)
                            <button id="startQuizBtn" class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-play-circle me-1"></i>Lanjutkan Attempt
                            </button>
                        @else
                            <button id="startQuizBtn" class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-play-circle me-1"></i>Mulai Quiz
                            </button>
                        @endif
                    @else
                            @php
                                $passedAttempt = $previousAttempts->where('is_passed', true)->first();
                            @endphp
                            @if($passedAttempt)
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <strong>Selamat!</strong> Anda telah lulus quiz ini dengan nilai {{ number_format($passedAttempt->nilai, 1) }}%.
                                </div>
                            <a href="{{ route('student.quizzes.review', $passedAttempt->id) }}" 
                               class="btn btn-outline-success w-100">
                                <i class="bi bi-eye me-1"></i>Lihat Hasil
                            </a>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Perhatian:</strong> Anda telah mencapai batas maksimum attempts untuk quiz ini.
                            </div>
                        @endif
                    @endif

                    @if($quiz->subModule)
                    <a href="{{ route('student.sub_modules.show', $quiz->subModule->id) }}" 
                       class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Sub-Modul
                    </a>
                    @elseif($quiz->module)
                    <a href="{{ route('student.modules.show', $quiz->module->id) }}" 
                       class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Modul
                    </a>
                    @elseif($quiz->course)
                    <a href="{{ route('student.courses.show', $quiz->course->id) }}" 
                       class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Kursus
                    </a>
                    @endif
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
                        <dt class="col-sm-5 text-muted small">Nilai Minimum</dt>
                        <dd class="col-sm-7 small">{{ $quiz->nilai_minimum }}%</dd>
                        
                        @if($quiz->max_attempts)
                        <dt class="col-sm-5 text-muted small">Maks Attempts</dt>
                        <dd class="col-sm-7 small">{{ $quiz->max_attempts }}</dd>
                        @endif
                        
                        <dt class="col-sm-5 text-muted small">Total Pertanyaan</dt>
                        <dd class="col-sm-7 small">{{ $quiz->questions()->count() }} Pertanyaan</dd>
                        
                        @php
                            $user = auth()->user();
                            $attemptCount = $user->quizAttempts()
                                ->where('quiz_id', $quiz->id)
                                ->whereNotNull('completed_at')
                                ->count();
                        @endphp
                        <dt class="col-sm-5 text-muted small">Attempts Digunakan</dt>
                        <dd class="col-sm-7 small">{{ $attemptCount }}{{ $quiz->max_attempts ? ' / ' . $quiz->max_attempts : '' }}</dd>
                        
                        @if($quiz->subModule)
                        <dt class="col-sm-5 text-muted small">Sub-Modul</dt>
                        <dd class="col-sm-7 small">
                            <a href="{{ route('student.sub_modules.show', $quiz->subModule->id) }}" 
                               class="text-decoration-none">
                                {{ $quiz->subModule->judul }}
                            </a>
                        </dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quiz Modal --}}
<div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quizModalLabel">{{ $quiz->judul }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="quizContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Memuat quiz...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitQuizBtn" style="display: none;">
                    <i class="bi bi-check-circle me-1"></i>Submit Quiz
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startQuizBtn = document.getElementById('startQuizBtn');
    const continueAttemptBtn = document.getElementById('continueAttemptBtn');
    const quizModal = new bootstrap.Modal(document.getElementById('quizModal'));
    const submitQuizBtn = document.getElementById('submitQuizBtn');
    
    @if($activeAttempt)
    const activeAttemptId = {{ $activeAttempt->id }};
    @else
    const activeAttemptId = null;
    @endif

    let currentAttemptId = activeAttemptId;

    function startQuiz() {
        // If there's already an active attempt, use it
        if (activeAttemptId) {
            currentAttemptId = activeAttemptId;
            loadQuizQuestions(activeAttemptId);
            quizModal.show();
            return;
        }

        // Otherwise, start a new attempt
        fetch('{{ route("student.quizzes.start", $quiz->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store the attempt_id
                currentAttemptId = data.attempt_id;
                loadQuizQuestions(data.attempt_id);
                quizModal.show();
            } else {
                alert(data.message || 'Gagal memulai quiz.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memulai quiz.');
        });
    }

    function loadQuizQuestions(attemptId) {
        fetch('{{ route("student.quizzes.questions", $quiz->id) }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store the attempt_id from the response
                currentAttemptId = data.data.attempt_id || attemptId;
                renderQuiz(data.data);
            } else {
                alert(data.message || 'Gagal memuat pertanyaan quiz.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat pertanyaan quiz.');
        });
    }

    function renderQuiz(quizData) {
        const quizContent = document.getElementById('quizContent');
        let html = '<form id="quizForm">';
        
        quizData.questions.forEach((question, index) => {
            html += `
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">${index + 1}. ${question.pertanyaan}</h6>
                        <div class="form-check-group">
            `;
            
            question.answer_options.forEach(option => {
                html += `
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" 
                               name="answers[${index}][question_id]" 
                               id="option_${option.id}" 
                               value="${option.id}"
                               data-question-id="${question.id}">
                        <label class="form-check-label" for="option_${option.id}">
                            ${option.teks}
                        </label>
                    </div>
                `;
            });
            
            html += `
                        </div>
                        <input type="hidden" name="answers[${index}][selected_answer_option_id]" 
                               id="selected_${question.id}" value="">
                    </div>
                </div>
            `;
        });
        
        html += '</form>';
        quizContent.innerHTML = html;
        submitQuizBtn.style.display = 'block';

        // Update hidden input when radio is selected
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionId = this.dataset.questionId;
                document.getElementById('selected_' + questionId).value = this.value;
            });
        });
    }

    function submitQuiz() {
        const form = document.getElementById('quizForm');
        const formData = new FormData(form);
        const answers = [];
        
        document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
            const questionId = radio.dataset.questionId;
            answers.push({
                question_id: parseInt(questionId),
                selected_answer_option_id: parseInt(radio.value)
            });
        });

        // Get attempt ID from multiple sources
        const attemptId = currentAttemptId || activeAttemptId || document.querySelector('[data-attempt-id]')?.dataset.attemptId;
        
        if (!attemptId) {
            alert('Attempt ID tidak ditemukan. Silakan mulai quiz terlebih dahulu.');
            return;
        }

        fetch('{{ route("student.quizzes.submit", $quiz->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                attempt_id: parseInt(attemptId),
                answers: answers
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                quizModal.hide();
                window.location.href = '{{ route("student.quizzes.result", ":attempt") }}'.replace(':attempt', data.data.attempt_id);
            } else {
                alert(data.message || 'Gagal mengirim quiz.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim quiz.');
        });
    }

    if (startQuizBtn) {
        startQuizBtn.addEventListener('click', startQuiz);
    }

    if (continueAttemptBtn) {
        continueAttemptBtn.addEventListener('click', function(e) {
            e.preventDefault();
            startQuiz();
        });
    }

    if (submitQuizBtn) {
        submitQuizBtn.addEventListener('click', submitQuiz);
    }
});
</script>
@endsection

