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
    <div class="card shadow border-0 h-100 hover-card" style="border-radius: 22px; overflow: hidden; cursor: pointer;">
        {{-- Header/Thumbnail --}}
        <div class="text-white d-flex align-items-center position-relative" 
             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 95px; border-top-left-radius: 22px; border-top-right-radius: 22px;">
            @if (isset($course->cover_url) && $course->cover_url)
                <img src="{{ $course->cover_url }}" alt="{{ $course->judul }}" class="w-100 h-100" style="object-fit: cover; border-top-left-radius: 22px; border-top-right-radius: 22px;">
            @else
                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                    <i class="bi bi-book fs-1 text-white opacity-75"></i>
                </div>
            @endif
            
            {{-- Category Tag --}}
            @if ($course->bidang_kompetensi)
                <div class="position-absolute top-0 start-0 m-2">
                    <span class="badge bg-success text-uppercase" style="font-size: 0.7rem;">
                        {{ $course->bidang_kompetensi }}
                    </span>
                </div>
            @endif
            
            {{-- Course Title Overlay --}}
            <h5 class="position-absolute bottom-0 start-0 ms-3 mb-2 fw-bold text-white" 
                style="font-size: 1.1rem; line-height: 1.3; text-shadow: 0 1px 3px rgba(0,0,0,0.3);">
                {{ Str::limit($course->judul, 40) }}
            </h5>
        </div>

        {{-- Content --}}
        <div class="card-body p-3">
            {{-- Course Title (if not shown in header) --}}
            <p class="mb-1 fw-semibold text-dark" style="font-size: 0.9rem;">
                {{ Str::limit($course->judul, 50) }}
            </p>

            {{-- Meta Info --}}
            <div class="mb-2">
                @if ($course->jp_value)
                    <span class="text-muted small me-2">
                        <i class="bi bi-clock me-1"></i>{{ $course->jp_value }} JP
                    </span>
                @endif
                @if (isset($course->difficulty))
                    <span class="text-muted small">
                        <i class="bi bi-bar-chart-fill me-1"></i>{{ $course->difficulty }}
                    </span>
                @endif
            </div>

            {{-- Enrollment window (schedule) --}}
            <div class="small text-muted">
                @if($startLocal || $endLocal)
                    <div>
                        <i class="bi bi-calendar-event me-1"></i>
                        <span>
                            @if($startLocal)
                                Mulai: {{ $startLocal->format('Y-m-d H:i') }}
                            @else
                                Mulai: tidak dibatasi
                            @endif
                            &mdash;
                            @if($endLocal)
                                Selesai: {{ $endLocal->format('Y-m-d H:i') }}
                            @else
                                Selesai: tidak dibatasi
                            @endif
                        </span>
                    </div>
                    @if($scheduleStatus)
                        <span class="badge mt-1
                            @if($scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_BEFORE_START) bg-secondary
                            @elseif($scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_IN_PROGRESS) bg-success
                            @elseif($scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_AFTER_END) bg-danger
                            @else bg-info
                            @endif
                        ">
                            {{ $scheduleStatus }}
                        </span>
                    @endif
                @else
                    <div>
                        <i class="bi bi-calendar-check me-1"></i>
                        <span>Pendaftaran selalu terbuka</span>
                    </div>
                @endif
            </div>

            {{-- Rating/Enrollments --}}
            @if ($course->enrollments_count ?? 0)
                <p class="text-warning mb-2 small">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star"></i>
                    <span class="text-muted ms-1">({{ $course->enrollments_count }})</span>
                </p>
            @endif
        </div>

        {{-- Separator --}}
        <div style="border-top: 3px solid #ffc107; margin: 0 1rem;"></div>

        {{-- Footer with Instructor and CTA --}}
        <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center pt-2 pb-3"
             style="border-bottom-left-radius: 22px; border-bottom-right-radius: 22px;">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-warning rounded-circle d-flex justify-content-center align-items-center" 
                     style="width: 28px; height: 28px;">
                    <i class="bi bi-person-fill text-white small"></i>
                </div>
                <small class="fw-semibold text-warning">{{ Str::limit($instructorEmail, 15) }}</small>
            </div>
            
            {{-- CTA Button --}}
            @if($actions)
                @if(Route::has('student.enroll'))
                    @php
                        $shouldShowCta = $canEnroll || ! $hideCtaOutsideWindow;
                    @endphp
                    @if($shouldShowCta)
                        <form action="{{ route('student.enroll', $course) }}" method="POST" class="d-inline">
                            @csrf
                            <button
                                type="submit"
                                class="btn btn-primary btn-sm"
                                @if(! $canEnroll)
                                    disabled
                                    title="@if($scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_BEFORE_START)
                                        {{ __('schedule.enrollment_opens_in', ['time' => $course->start_date_time?->diffForHumans() ?? '']) }}
                                    @elseif($scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_AFTER_END)
                                        {{ __('schedule.enrollment_closed_ago', ['time' => $course->end_date_time?->diffForHumans() ?? '']) }}
                                    @else
                                        {{ __('Enrollment is not available at this time.') }}
                                    @endif"
                                @endif
                            >
                                <i class="bi bi-plus-circle me-1"></i>Daftar
                            </button>
                        </form>
                    @endif
                @elseif ($hasRoute)
                    <a href="{{ route('courses.show', $course->id) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-play-circle me-1"></i>Mulai
                    </a>
                @else
                    <button disabled class="btn btn-secondary btn-sm" disabled>
                        Mulai
                    </button>
                @endif
            @endif
        </div>
    </div>
@else
    {{-- List View --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
        <div class="flex flex-col lg:flex-row">
            {{-- Thumbnail --}}
            <div class="relative w-full lg:w-48 h-40 lg:h-auto bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center flex-shrink-0">
                @if (isset($course->cover_url) && $course->cover_url)
                    <img src="{{ $course->cover_url }}" alt="{{ $course->judul }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-16 h-16 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                @endif
                
                @if ($course->bidang_kompetensi)
                    <div class="absolute top-2 left-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 uppercase">
                            {{ $course->bidang_kompetensi }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 p-4 lg:p-6">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between mb-3">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ $course->judul }}
                        </h3>
                        <div class="flex items-center text-sm text-gray-600 mb-3">
                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>{{ $instructorEmail }}</span>
                        </div>

                        {{-- Enrollment window (schedule) --}}
                        <div class="mt-1 text-sm text-gray-600">
                            @if($startLocal || $endLocal)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>
                                        @if($startLocal)
                                            Mulai: {{ $startLocal->format('Y-m-d H:i') }}
                                        @else
                                            Mulai: tidak dibatasi
                                        @endif
                                        &mdash;
                                        @if($endLocal)
                                            Selesai: {{ $endLocal->format('Y-m-d H:i') }}
                                        @else
                                            Selesai: tidak dibatasi
                                        @endif
                                    </span>
                                </div>
                                @if($scheduleStatus)
                                    <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded-full text-xs font-medium
                                        @if($scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_BEFORE_START) bg-gray-200 text-gray-800
                                        @elseif($scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_IN_PROGRESS) bg-green-100 text-green-800
                                        @elseif($scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_AFTER_END) bg-red-100 text-red-800
                                        @else bg-blue-100 text-blue-800
                                        @endif
                                    ">
                                        {{ $scheduleStatus }}
                                    </span>
                                @endif
                            @else
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Pendaftaran selalu terbuka</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($actions)
                        @if(Route::has('student.enroll'))
                            @php
                                $shouldShowCta = $canEnroll || ! $hideCtaOutsideWindow;
                            @endphp
                            @if($shouldShowCta)
                                <form action="{{ route('student.enroll', $course) }}" method="POST" class="mt-3 lg:mt-0 lg:ml-4">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="btn-primary whitespace-nowrap disabled:opacity-60 disabled:cursor-not-allowed"
                                        @if(! $canEnroll)
                                            disabled
                                            title="@if($scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_BEFORE_START)
                                                {{ __('schedule.enrollment_opens_in', ['time' => $course->start_date_time?->diffForHumans() ?? '']) }}
                                            @elseif($scheduleStatus === \App\Models\Course::SCHEDULE_STATUS_AFTER_END)
                                                {{ __('schedule.enrollment_closed_ago', ['time' => $course->end_date_time?->diffForHumans() ?? '']) }}
                                            @else
                                                {{ __('Enrollment is not available at this time.') }}
                                            @endif"
                                        @endif
                                    >
                                        Daftar
                                    </button>
                                </form>
                            @endif
                        @elseif ($hasRoute)
                            <a href="{{ route('courses.show', $course->id) }}" class="mt-3 lg:mt-0 lg:ml-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 whitespace-nowrap">
                                {{ __('Start Course') }}
                            </a>
                        @else
                            <button disabled class="mt-3 lg:mt-0 lg:ml-4 inline-block px-4 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed whitespace-nowrap">
                                {{ __('Start Course') }}
                            </button>
                        @endif
                    @endif
                </div>

                {{-- Features --}}
                <ul class="flex flex-wrap gap-4 mb-3 text-sm text-gray-600">
                    @if ($course->modules_count ?? 0)
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $course->modules_count ?? 0 }} {{ __('Modules') }}</span>
                        </li>
                    @endif
                    @if ($course->contents_count ?? 0)
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ $course->contents_count ?? 0 }} {{ __('Lessons') }}</span>
                        </li>
                    @endif
                    @if (isset($course->difficulty))
                        <li class="flex items-center">
                            <span>{{ $course->difficulty }}</span>
                        </li>
                    @endif
                </ul>

                {{-- Meta Info --}}
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    @if ($course->jp_value)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $course->jp_value }} JP</span>
                        </div>
                    @endif
                    @if ($course->enrollments_count ?? 0)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>{{ $course->enrollments_count ?? 0 }} {{ __('Students') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

