@props(['course', 'view' => 'grid', 'actions' => true])

@php
    $hasRoute = Route::has('courses.show');
    $instructorEmail = 'rizkyhatsa.dev@gmail.com';
    if (isset($course->owner) && $course->owner) {
        $instructorEmail = $course->owner->email;
    } elseif (isset($course->userEnrollments) && $course->userEnrollments->isNotEmpty()) {
        $firstEnrollment = $course->userEnrollments->first();
        if ($firstEnrollment && isset($firstEnrollment->user) && $firstEnrollment->user) {
            $instructorEmail = $firstEnrollment->user->email;
        }
    }

    // Schedule & enrollment window (best-practice LMS behavior)
    $nowUtc = \Carbon\CarbonImmutable::now('UTC');
    $scheduleStatus = $course instanceof \App\Models\Course
        ? $course->scheduleStatus($nowUtc)
        : null;
    $canEnroll = $course instanceof \App\Models\Course
        ? $course->canEnroll($nowUtc)
        : true;
    $hideCtaOutsideWindow = config('lms.hide_enroll_cta_outside_window', false);

    $startLocal = $course->start_date_time
        ? $course->start_date_time->setTimezone(config('app.timezone'))
        : null;
    $endLocal = $course->end_date_time
        ? $course->end_date_time->setTimezone(config('app.timezone'))
        : null;
@endphp

@if ($view === 'grid')
    <div class="card h-100 border-0 shadow-sm hover-card bg-white" style="border-radius: 20px; overflow: hidden;">
        <a href="{{ route('courses.public_show', $course) }}" class="text-decoration-none group">
            {{-- Image Container --}}
            <div class="position-relative" style="height: 180px; overflow: hidden;">
                @if (isset($course->cover_url) && $course->cover_url)
                    <img src="{{ $course->cover_url }}" alt="{{ $course->judul }}" class="w-100 h-100 transition-all duration-500 group-hover:scale-110" style="object-fit: cover;">
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-gradient-to-br from-indigo-500 to-purple-600">
                        <i class="bi bi-journal-bookmark-fill text-white opacity-50" style="font-size: 3rem;"></i>
                    </div>
                @endif

                {{-- Glassmorphism Badge --}}
                @if ($course->bidang_kompetensi)
                    <div class="position-absolute top-0 start-0 m-3">
                        <span class="badge backdrop-blur-md bg-white/20 text-white border border-white/30 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                            {{ $course->bidang_kompetensi }}
                        </span>
                    </div>
                @endif

                {{-- Status Overlay (Bottom Left) --}}
                <div class="position-absolute bottom-0 start-0 m-3">
                    @if($canEnroll)
                        <span class="badge bg-success text-white px-2 py-1 rounded-sm shadow-sm" style="font-size: 0.6rem;">Pendaftaran Dibuka</span>
                    @endif
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body p-4">
                {{-- Category & Rating --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-primary fw-bold" style="font-size: 0.75rem;">{{ $course->difficulty ?? 'Umum' }}</span>
                    <div class="d-flex align-items-center gap-1 text-warning">
                        <i class="fas fa-star text-xs"></i>
                        <span class="text-dark fw-bold" style="font-size: 0.8rem;">{{ number_format(4.8, 1) }}</span>
                    </div>
                </div>

                <h5 class="card-title fw-bold text-dark mb-3 leading-tight group-hover:text-primary transition-colors" style="font-size: 1.05rem; height: 2.6rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                    {{ $course->judul }}
                </h5>

                {{-- Meta Info --}}
                <div class="d-flex flex-wrap gap-3 mb-0">
                    <div class="d-flex align-items-center text-muted gap-1" style="font-size: 0.75rem;">
                        <i class="bi bi-clock"></i>
                        <span>{{ $course->jp_value ?? 0 }} JP</span>
                    </div>
                    <div class="d-flex align-items-center text-muted gap-1" style="font-size: 0.75rem;">
                        <i class="bi bi-people"></i>
                        <span>{{ $course->enrollments_count ?? rand(20, 100) }} Peserta</span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Footer --}}
        <div class="card-footer bg-white border-top-0 px-4 pb-4 pt-0">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-sm rounded-circle shadow-sm overflow-hidden d-flex align-items-center justify-content-center bg-light" style="width: 32px; height: 32px; border: 2px solid white;">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($instructorEmail) }}&background=random&color=fff" alt="Instructor" class="w-100 h-100 object-fit-cover">
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-dark fw-bold" style="font-size: 0.7rem;">Pengajar</span>
                        <span class="text-muted" style="font-size: 0.65rem;">{{ Str::limit(explode('@', $instructorEmail)[0], 12) }}</span>
                    </div>
                </div>
                <a href="{{ route('courses.public_show', $course) }}" class="btn btn-outline-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                    Lihat Detail
                </a>
            </div>
        </div>
    </div>
@else
    {{-- List View --}}
    <div class="card border-0 shadow-sm hover-card bg-white overflow-hidden mb-4" style="border-radius: 20px;">
        <div class="row g-0">
            {{-- Image --}}
            <div class="col-md-3 position-relative">
                @if (isset($course->cover_url) && $course->cover_url)
                    <img src="{{ $course->cover_url }}" alt="{{ $course->judul }}" class="w-100 h-100" style="object-fit: cover; min-height: 160px;">
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-gradient-to-br from-indigo-500 to-purple-600" style="min-height: 160px;">
                        <i class="bi bi-journal-bookmark-fill text-white opacity-50" style="font-size: 2.5rem;"></i>
                    </div>
                @endif
                
                @if ($course->bidang_kompetensi)
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge backdrop-blur-md bg-white/20 text-white border border-white/30 px-2 py-1 rounded-pill" style="font-size: 0.6rem; font-weight: 600; text-transform: uppercase;">
                            {{ $course->bidang_kompetensi }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="col-md-9 p-4">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex flex-column">
                        <span class="text-primary fw-bold mb-1" style="font-size: 0.75rem;">{{ $course->difficulty ?? 'Umum' }}</span>
                        <h5 class="card-title fw-bold text-dark mb-2 leading-tight">
                            <a href="{{ route('courses.public_show', $course) }}" class="text-decoration-none text-dark hover:text-primary transition-colors">
                                {{ $course->judul }}
                            </a>
                        </h5>
                    </div>
                    <div class="d-flex align-items-center gap-1 text-warning">
                        <i class="fas fa-star text-sm"></i>
                        <span class="text-dark fw-bold" style="font-size: 0.9rem;">{{ number_format(4.8, 1) }}</span>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-4 mb-4">
                    <div class="d-flex align-items-center text-muted gap-2" style="font-size: 0.8rem;">
                        <i class="bi bi-clock text-primary"></i>
                        <span>{{ $course->jp_value ?? 0 }} JP</span>
                    </div>
                    <div class="d-flex align-items-center text-muted gap-2" style="font-size: 0.8rem;">
                        <i class="bi bi-people text-primary"></i>
                        <span>{{ $course->enrollments_count ?? rand(20, 100) }} Peserta</span>
                    </div>
                    <div class="d-flex align-items-center text-muted gap-2" style="font-size: 0.8rem;">
                        <i class="bi bi-calendar3 text-primary"></i>
                        <span>{{ $canEnroll ? 'Pendaftaran Dibuka' : 'Ditutup' }}</span>
                    </div>
                </div>

                <div class="d-flex items-center justify-content-between border-top pt-3 mt-auto">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-sm rounded-circle shadow-sm overflow-hidden d-flex align-items-center justify-content-center bg-light" style="width: 36px; height: 36px; border: 2px solid white;">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($instructorEmail) }}&background=random&color=fff" alt="Instructor" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-dark fw-bold" style="font-size: 0.75rem;">Pengajar Pelatihan</span>
                            <span class="text-muted" style="font-size: 0.7rem;">{{ $instructorEmail }}</span>
                        </div>
                    </div>
                    <a href="{{ route('courses.public_show', $course) }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold" style="font-size: 0.85rem;">
                        Lihat Detail Pelatihan
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

