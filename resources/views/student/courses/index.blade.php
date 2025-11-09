{{--
    @phpdoc
    Variabel:
    - $courses = Collection<object{id,title,short_description,cover_url,progress_percent,instructor_name,updated_at}>
--}}
@extends('layouts.studentapp')

@section('title', __('Courses'))

@section('content')
    @include('student._breadcrumbs', ['crumbs' => [
        ['label' => __('Dashboard'), 'route' => 'student.dashboard'],
        ['label' => __('Courses')],
    ]])

    @if(($courses ?? collect())->isEmpty())
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                <h5 class="fw-bold">{{ __('You are not enrolled in any course') }}</h5>
                <p class="text-muted">{{ __('Enroll to start learning.') }}</p>
                <a href="{{ route('courses.index') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-search me-2"></i>{{ __('Explore Catalog') }}
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($courses as $course)
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('student.courses.show', $course->id) }}" class="text-decoration-none">
                        <div class="card shadow border-0 h-100 hover-card" style="border-radius: 22px; overflow: hidden; cursor: pointer; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            {{-- Header/Thumbnail --}}
                            <div class="text-white d-flex align-items-center position-relative" 
                                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 95px; border-top-left-radius: 22px; border-top-right-radius: 22px;">
                                @if ($course->cover_url && $course->cover_url !== asset('image/course-placeholder.png'))
                                    <img src="{{ $course->cover_url }}" alt="{{ $course->title }}" class="w-100 h-100" style="object-fit: cover; border-top-left-radius: 22px; border-top-right-radius: 22px;">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-book fs-1 text-white opacity-75"></i>
                                    </div>
                                @endif
                                
                                {{-- Course Title Overlay --}}
                                <h5 class="position-absolute bottom-0 start-0 ms-3 mb-2 fw-bold text-white" 
                                    style="font-size: 1.1rem; line-height: 1.3; text-shadow: 0 1px 3px rgba(0,0,0,0.3); max-width: 85%;">
                                    {{ Str::limit($course->title, 40) }}
                                </h5>
                            </div>

                            {{-- Content --}}
                            <div class="card-body p-3">
                                {{-- Course Description --}}
                                <p class="mb-2 text-muted small" style="font-size: 0.85rem; line-height: 1.4;">
                                    {{ Str::limit($course->short_description, 80) }}
                                </p>

                                {{-- Progress Bar --}}
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-semibold text-dark">Progress</span>
                                        <span class="small text-muted">{{ number_format($course->progress_percent, 0) }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px; border-radius: 10px;">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: {{ $course->progress_percent }}%; border-radius: 10px; transition: width 0.3s ease;" 
                                             aria-valuenow="{{ $course->progress_percent }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Separator --}}
                            <div style="border-top: 3px solid #ffc107; margin: 0 1rem;"></div>

                            {{-- Footer with Instructor and Continue Button --}}
                            <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center pt-2 pb-3"
                                 style="border-bottom-left-radius: 22px; border-bottom-right-radius: 22px;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-warning rounded-circle d-flex justify-content-center align-items-center" 
                                         style="width: 28px; height: 28px;">
                                        <i class="bi bi-person-fill text-white small"></i>
                                    </div>
                                    <small class="fw-semibold text-warning">{{ Str::limit($course->instructor_name, 15) }}</small>
                                </div>
                                
                                {{-- Continue Button --}}
                                <span class="btn btn-primary btn-sm">
                                    <i class="bi bi-play-circle me-1"></i>Lanjutkan
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        @if(method_exists($courses, 'links'))
            <div class="mt-4">
                {{ $courses->links() }}
            </div>
        @endif
    @endif

    <style>
        .hover-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@endsection


