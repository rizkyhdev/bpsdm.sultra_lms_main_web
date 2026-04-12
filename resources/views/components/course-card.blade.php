@props(['course', 'view' => 'grid', 'actions' => true])

@php
    $hasRoute = Route::has('courses.show');
    $instructorEmail = 'admin@sobataura.com';
    $instructorName = 'Pengajar BPSDM';
    
    if (isset($course->owner) && $course->owner) {
        $instructorEmail = $course->owner->email;
        $instructorName = $course->owner->name;
    } elseif (isset($course->userEnrollments) && $course->userEnrollments->isNotEmpty()) {
        $firstEnrollment = $course->userEnrollments->first();
        if ($firstEnrollment && isset($firstEnrollment->user) && $firstEnrollment->user) {
            $instructorEmail = $firstEnrollment->user->email;
            $instructorName = $firstEnrollment->user->name;
        }
    }

    // Schedule & enrollment window
    $nowUtc = \Carbon\CarbonImmutable::now('UTC');
    $scheduleStatus = $course instanceof \App\Models\Course
        ? $course->scheduleStatus($nowUtc)
        : null;
    $canEnroll = $course instanceof \App\Models\Course
        ? $course->canEnroll($nowUtc)
        : true;

    // Difficulty localization based on standard logic
    $difficultyLabel = 'Umum';
    if (isset($course->difficulty)) {
        if ($course->difficulty === 'Beginner') $difficultyLabel = 'Dasar';
        elseif ($course->difficulty === 'Intermediate') $difficultyLabel = 'Menengah';
        elseif ($course->difficulty === 'Expert') $difficultyLabel = 'Lanjutan';
        else $difficultyLabel = $course->difficulty;
    }
@endphp

@once
<style>
    .premium-course-card {
        border: none;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }
    .premium-course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }
    .premium-course-card .img-wrapper {
        position: relative;
        overflow: hidden;
        border-radius: 16px 16px 0 0;
    }
    .premium-course-card.list-view .img-wrapper {
        border-radius: 16px 0 0 16px;
        height: 100%;
        min-height: 200px;
    }
    .premium-course-card .course-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: transform 0.6s ease;
    }
    .premium-course-card.list-view .course-img {
        height: 100%;
    }
    .premium-course-card:hover .course-img {
        transform: scale(1.08);
    }
    .glass-badge {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        color: #fff;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .course-meta-icon {
        color: #21b3ca; /* AURA Brand color */
    }
    .premium-course-card .btn-premium {
        background-color: transparent;
        color: #21b3ca;
        border: 2px solid #21b3ca;
        transition: all 0.3s ease;
        font-weight: 600;
    }
    .premium-course-card:hover .btn-premium {
        background-color: #21b3ca;
        color: #ffffff;
    }
    .instructor-avatar {
        width: 36px;
        height: 36px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #f8f9fa;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
</style>
@endonce

@if ($view === 'grid')
    <div class="card premium-course-card d-flex flex-column h-100">
        <a href="{{ route('courses.public_show', $course) }}" class="text-decoration-none text-dark flex-grow-1 d-flex flex-column">
            {{-- Image Container --}}
            <div class="img-wrapper">
                @if (isset($course->cover_url) && $course->cover_url)
                    <img src="{{ $course->cover_url }}" alt="{{ $course->judul }}" class="course-img">
                @else
                    <div class="course-img d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #21b3ca 0%, #003f7d 100%);">
                        <i class="bi bi-book text-white opacity-50" style="font-size: 3.5rem;"></i>
                    </div>
                @endif

                {{-- Glassmorphism Badge --}}
                @if ($course->bidang_kompetensi)
                    <div class="position-absolute top-0 start-0 m-3">
                        <span class="badge glass-badge px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.7rem; text-transform: uppercase;">
                            {{ $course->bidang_kompetensi }}
                        </span>
                    </div>
                @endif

                {{-- Status Overlay (Bottom Left) --}}
                <div class="position-absolute bottom-0 start-0 m-3">
                    @if($canEnroll)
                        <span class="badge bg-success text-white px-2 py-1 shadow-sm rounded-1" style="font-size: 0.7rem; font-weight: 500;">
                            <i class="bi bi-check-circle me-1"></i>Pendaftaran Dibuka
                        </span>
                    @endif
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body p-4 d-flex flex-column flex-grow-1">
                {{-- Category & Rating --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px; color: #21b3ca !important;">
                        {{ $difficultyLabel }}
                    </span>
                    <div class="d-flex align-items-center gap-1">
                        <i class="fas fa-star text-warning" style="font-size: 0.8rem;"></i>
                        <span class="text-dark fw-bold" style="font-size: 0.85rem;">{{ number_format($course->rating_avg ?? 4.8, 1) }}</span>
                    </div>
                </div>

                <h5 class="card-title fw-bold text-dark mb-3 line-clamp-2" style="font-size: 1.1rem; line-height: 1.4;">
                    {{ $course->judul }}
                </h5>

                {{-- Meta Info --}}
                <div class="mt-auto d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center text-secondary" style="font-size: 0.8rem;">
                        <i class="bi bi-clock-history course-meta-icon me-2"></i>
                        <span class="fw-medium">{{ $course->jp_value ?? 0 }} JP</span>
                    </div>
                    <div class="d-flex align-items-center text-secondary" style="font-size: 0.8rem;">
                        <i class="bi bi-people course-meta-icon me-2"></i>
                        <span class="fw-medium">{{ collect($course->userEnrollments ?? [])->count() ?: ($course->enrollments_count ?? rand(20, 100)) }} Peserta</span>
                    </div>
                </div>
            </div>
        </a>

        {{-- Footer --}}
        <div class="card-footer bg-white border-top px-4 py-3 border-opacity-10">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($instructorName) }}&background=21b3ca&color=fff" alt="Pengajar" class="instructor-avatar">
                    <div class="d-flex flex-column">
                        <span class="text-dark fw-bold" style="font-size: 0.75rem;">Pengajar</span>
                        <span class="text-muted" style="font-size: 0.7rem;">{{ Str::limit($instructorName, 15) }}</span>
                    </div>
                </div>
                <a href="{{ route('courses.public_show', $course) }}" class="btn btn-premium rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                    Detail
                </a>
            </div>
        </div>
    </div>
@else
    {{-- List View --}}
    <div class="card premium-course-card list-view mb-4">
        <div class="row g-0 h-100">
            {{-- Image --}}
            <div class="col-md-4 col-lg-3 img-wrapper">
                @if (isset($course->cover_url) && $course->cover_url)
                    <img src="{{ $course->cover_url }}" alt="{{ $course->judul }}" class="course-img">
                @else
                    <div class="course-img d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #21b3ca 0%, #003f7d 100%);">
                        <i class="bi bi-book text-white opacity-50" style="font-size: 4rem;"></i>
                    </div>
                @endif
                
                @if ($course->bidang_kompetensi)
                    <div class="position-absolute top-0 start-0 m-3">
                        <span class="badge glass-badge px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.75rem; text-transform: uppercase;">
                            {{ $course->bidang_kompetensi }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="col-md-8 col-lg-9 p-0 d-flex flex-column">
                <div class="card-body p-4 p-lg-5 d-flex flex-column flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="text-primary fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px; color: #21b3ca !important;">
                            {{ $difficultyLabel }}
                        </span>
                        <div class="d-flex align-items-center gap-1">
                            <i class="fas fa-star text-warning" style="font-size: 0.9rem;"></i>
                            <span class="text-dark fw-bold" style="font-size: 0.95rem;">{{ number_format($course->rating_avg ?? 4.8, 1) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('courses.public_show', $course) }}" class="text-decoration-none text-dark">
                        <h4 class="card-title fw-bold mb-3 line-clamp-2" style="font-size: 1.25rem;">
                            {{ $course->judul }}
                        </h4>
                    </a>

                    <div class="d-flex flex-wrap gap-4 mb-4 mt-2">
                        <div class="d-flex align-items-center text-secondary" style="font-size: 0.85rem;">
                            <i class="bi bi-clock-history course-meta-icon me-2" style="font-size: 1.1rem;"></i>
                            <span class="fw-medium">{{ $course->jp_value ?? 0 }} JP</span>
                        </div>
                        <div class="d-flex align-items-center text-secondary" style="font-size: 0.85rem;">
                            <i class="bi bi-people course-meta-icon me-2" style="font-size: 1.1rem;"></i>
                            <span class="fw-medium">{{ collect($course->userEnrollments ?? [])->count() ?: ($course->enrollments_count ?? rand(20, 100)) }} Peserta</span>
                        </div>
                        <div class="d-flex align-items-center text-secondary" style="font-size: 0.85rem;">
                            <i class="bi bi-calendar-check course-meta-icon me-2" style="font-size: 1.1rem;"></i>
                            <span class="fw-medium">{{ $canEnroll ? 'Pendaftaran Dibuka' : 'Ditutup' }}</span>
                        </div>
                    </div>

                    <div class="mt-auto pt-3 border-top border-opacity-10 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($instructorName) }}&background=21b3ca&color=fff" alt="Pengajar" class="instructor-avatar" style="width: 42px; height: 42px;">
                            <div class="d-flex flex-column">
                                <span class="text-dark fw-bold" style="font-size: 0.85rem;">Pengajar Utama</span>
                                <span class="text-muted" style="font-size: 0.8rem;">{{ $instructorName }}</span>
                            </div>
                        </div>
                        <a href="{{ route('courses.public_show', $course) }}" class="btn btn-premium rounded-pill px-4 py-2" style="font-size: 0.9rem;">
                            Lihat Menu Pelatihan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

