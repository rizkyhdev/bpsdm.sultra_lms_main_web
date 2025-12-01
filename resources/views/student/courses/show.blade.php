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

                            {{-- Course Schedule Countdown --}}
                            @if(isset($course->start_date_time) || isset($course->end_date_time))
                            <div class="mt-3 mb-3" 
                                 x-data="courseCountdown({
                                     startDateTimeUtc: @js($course->start_date_time?->toIso8601String()),
                                     endDateTimeUtc: @js($course->end_date_time?->toIso8601String()),
                                     serverNowUtc: @js($serverNowUtc->toIso8601String()),
                                     locale: @js(app()->getLocale()),
                                     scheduleStatus: @js($scheduleStatus ?? 'ALWAYS_OPEN')
                                 })"
                                 x-init="init()">
                                <div class="alert alert-info mb-0" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-clock-history me-2"></i>
                                        <div class="flex-grow-1">
                                            <strong x-text="statusText"></strong>
                                            <div x-show="showCountdown" class="mt-1">
                                                <span class="badge bg-primary fs-6" x-text="countdownText"></span>
                                            </div>
                                            <div x-show="showEnded" class="mt-1">
                                                <span class="text-muted">{{ __('schedule.ended') }}</span>
                                            </div>
                                        </div>
                                        <span x-show="syncing" class="badge bg-secondary ms-2">{{ __('schedule.syncing') }}</span>
                                    </div>
                                </div>
                                <div aria-live="polite" aria-atomic="true" class="visually-hidden" x-text="ariaLiveText"></div>
                            </div>
                            @endif

                            {{-- Enrollment CTA --}}
                            @if(!isset($enrollment))
                            <div class="mt-3">
                                @php
                                    $hideCtaOutsideWindow = config('lms.hide_enroll_cta_outside_window', false);
                                    $shouldShowCta = ($canEnroll ?? true) || !$hideCtaOutsideWindow;
                                @endphp
                                @if($shouldShowCta)
                                <form action="{{ route('student.enroll', $course->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button 
                                        type="submit" 
                                        class="btn btn-primary"
                                        @if(!($canEnroll ?? true)) 
                                            disabled 
                                            title="{{ $scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_BEFORE_START ? __('schedule.enrollment_opens_in', ['time' => $course->start_date_time?->diffForHumans() ?? '']) : __('schedule.enrollment_closed_ago', ['time' => $course->end_date_time?->diffForHumans() ?? '']) }}"
                                        @endif
                                    >
                                        <i class="bi bi-person-plus me-1"></i>
                                        {{ __('Mendaftar ke Pelatihan') }}
                                    </button>
                                </form>
                                @if(!($canEnroll ?? true))
                                    <div class="small text-muted mt-1">
                                        @if($scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_BEFORE_START)
                                            {{ __('schedule.enrollment_opens_in', ['time' => $course->start_date_time?->diffForHumans() ?? '']) }}
                                        @elseif($scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_AFTER_END)
                                            {{ __('schedule.enrollment_closed_ago', ['time' => $course->end_date_time?->diffForHumans() ?? '']) }}
                                        @endif
                                    </div>
                                @endif
                                @endif
                            </div>
                            @endif

                            {{-- Certificate CTA --}}
                            @if(isset($enrollment))
                                <div class="mt-3">
                                    <button
                                        id="see-certificate-btn"
                                        type="button"
                                        class="btn btn-outline-success btn-sm"
                                        data-generate-url="{{ route('certificates.generate', ['course' => $course->slug]) }}"
                                        @if(!($canSeeCertificate ?? false)) disabled @endif
                                    >
                                        <i class="bi bi-award me-1"></i>
                                        {{ __('Lihat Sertifikat') }}
                                    </button>
                                    <div class="small mt-1">
                                        @if($canSeeCertificate ?? false)
                                            <span class="text-success">
                                                {{ __('Anda telah menyelesaikan kursus ini. Anda dapat mengunduh sertifikat Anda.') }}
                                            </span>
                                        @else
                                            <span class="text-muted" title="{{ __('Selesaikan kursus ini untuk membuka sertifikat Anda.') }}">
                                                {{ __('Selesaikan kursus ini untuk mengakses sertifikat Anda.') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('see-certificate-btn');
        if (!btn) return;

        const originalHtml = btn.innerHTML;
        const generateUrl = btn.getAttribute('data-generate-url');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function showToast(message, type = 'danger') {
            const containerId = 'global-toast-container';
            let container = document.getElementById(containerId);
            if (!container) {
                container = document.createElement('div');
                container.id = containerId;
                container.style.position = 'fixed';
                container.style.top = '1rem';
                container.style.right = '1rem';
                container.style.zIndex = '1080';
                document.body.appendChild(container);
            }

            const alert = document.createElement('div');
            alert.className = 'alert alert-' + type + ' alert-dismissible fade show shadow-sm mb-2';
            alert.role = 'alert';
            alert.innerHTML = `
                <span>${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;

            container.appendChild(alert);

            setTimeout(function () {
                alert.classList.remove('show');
                alert.addEventListener('transitionend', function () {
                    alert.remove();
                });
            }, 4000);
        }

        btn.addEventListener('click', function (event) {
            event.preventDefault();
            if (btn.disabled || !generateUrl || !csrfToken) {
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __('Processing...') }}';

            fetch(generateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
                .then(async function (response) {
                    const data = await response.json().catch(function () {
                        return null;
                    });

                    if (!response.ok || !data) {
                        throw new Error(data && data.message ? data.message : 'Failed to generate certificate.');
                    }

                    if (!data.success || !data.download_url) {
                        throw new Error(data.message || 'Failed to generate certificate.');
                    }

                    window.location.href = data.download_url;
                    showToast('{{ __('Certificate download started.') }}', 'success');
                })
                .catch(function (error) {
                    console.error(error);
                    showToast(error.message || 'Failed to generate certificate.', 'danger');
                })
                .finally(function () {
                    btn.disabled = !(@json($canSeeCertificate ?? false));
                    btn.innerHTML = originalHtml;
                });
        });
    });
</script>

{{-- Course Countdown Component --}}
<script>
    function courseCountdown(config) {
        return {
            startDateTimeUtc: config.startDateTimeUtc,
            endDateTimeUtc: config.endDateTimeUtc,
            serverNowUtc: config.serverNowUtc,
            locale: config.locale,
            scheduleStatus: config.scheduleStatus,
            
            drift: 0,
            countdownText: '',
            statusText: '',
            ariaLiveText: '',
            showCountdown: false,
            showEnded: false,
            syncing: false,
            intervalId: null,
            ariaUpdateIntervalId: null,
            lastAriaUpdate: 0,
            
            init() {
                this.computeDrift();
                this.update();
                this.startInterval();
                this.startAriaUpdates();
                this.setupResync();
                this.setupBroadcastListener();
            },
            
            computeDrift() {
                const serverTime = new Date(this.serverNowUtc).getTime();
                const clientTime = Date.now();
                this.drift = serverTime - clientTime;
            },
            
            getCurrentTime() {
                return new Date(Date.now() + this.drift);
            },
            
            update() {
                const now = this.getCurrentTime();
                const start = this.startDateTimeUtc ? new Date(this.startDateTimeUtc) : null;
                const end = this.endDateTimeUtc ? new Date(this.endDateTimeUtc) : null;
                
                if (!start && !end) {
                    this.statusText = '{{ __("schedule.always_open") }}';
                    this.showCountdown = false;
                    this.showEnded = false;
                    return;
                }
                
                if (start && now < start) {
                    // Before start
                    const diff = start - now;
                    this.statusText = '{{ __("schedule.starts_in", ["countdown" => ""]) }}';
                    this.countdownText = this.formatCountdown(diff);
                    this.showCountdown = true;
                    this.showEnded = false;
                } else if (end && now >= end) {
                    // After end
                    this.statusText = '{{ __("schedule.ended") }}';
                    this.showCountdown = false;
                    this.showEnded = true;
                } else {
                    // In progress
                    if (end) {
                        const diff = end - now;
                        this.statusText = '{{ __("schedule.ends_in", ["countdown" => ""]) }}';
                        this.countdownText = this.formatCountdown(diff);
                        this.showCountdown = true;
                        this.showEnded = false;
                    } else {
                        this.statusText = '{{ __("schedule.always_open") }}';
                        this.showCountdown = false;
                        this.showEnded = false;
                    }
                }
            },
            
            formatCountdown(ms) {
                const totalSeconds = Math.floor(ms / 1000);
                const days = Math.floor(totalSeconds / 86400);
                const hours = Math.floor((totalSeconds % 86400) / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;
                
                return `${String(days).padStart(2, '0')}:${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            },
            
            startInterval() {
                this.intervalId = setInterval(() => {
                    this.update();
                }, 1000);
            },
            
            startAriaUpdates() {
                this.ariaUpdateIntervalId = setInterval(() => {
                    const now = Date.now();
                    if (now - this.lastAriaUpdate >= 10000) {
                        this.ariaLiveText = this.statusText + ' ' + (this.countdownText || '');
                        this.lastAriaUpdate = now;
                    }
                }, 1000);
            },
            
            setupResync() {
                // Resync on focus
                window.addEventListener('focus', () => {
                    this.resync();
                });
                
                // Resync every 60 seconds
                setInterval(() => {
                    this.resync();
                }, 60000);
            },
            
            async resync() {
                this.syncing = true;
                try {
                    const response = await fetch(`/api/courses/{{ $course->id }}/server-time`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.server_now_utc) {
                            this.serverNowUtc = data.server_now_utc;
                            this.computeDrift();
                        }
                    }
                } catch (error) {
                    console.warn('Failed to resync server time, using client time with drift:', error);
                } finally {
                    this.syncing = false;
                }
            },
            
            setupBroadcastListener() {
                @if(config('lms.enable_websocket_countdown_sync', true))
                if (typeof Echo !== 'undefined') {
                    Echo.channel(`course.{{ $course->id }}`)
                        .listen('.CourseScheduleUpdated', (e) => {
                            if (e.start_date_time !== undefined) {
                                this.startDateTimeUtc = e.start_date_time;
                            }
                            if (e.end_date_time !== undefined) {
                                this.endDateTimeUtc = e.end_date_time;
                            }
                            if (e.server_now_utc) {
                                this.serverNowUtc = e.server_now_utc;
                                this.computeDrift();
                            }
                            this.update();
                        });
                } else {
                    // Fallback polling every 60s
                    setInterval(() => {
                        this.resync();
                    }, 60000);
                }
                @endif
            },
            
            destroy() {
                if (this.intervalId) {
                    clearInterval(this.intervalId);
                }
                if (this.ariaUpdateIntervalId) {
                    clearInterval(this.ariaUpdateIntervalId);
                }
            }
        };
    }
</script>
@endsection
