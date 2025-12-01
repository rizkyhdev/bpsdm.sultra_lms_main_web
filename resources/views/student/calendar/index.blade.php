@extends('layouts.studentapp')

@section('title', __('calendar.calendar'))

@section('content')
<div class="container-fluid my-1">
    {{-- Page Header --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-2">{{ __('calendar.calendar') }}</h2>
        <p class="text-muted mb-0">{{ __('calendar.view_schedule') }}</p>
    </div>

    {{-- Calendar Container --}}
    <div id="student-calendar-app" class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-4">
            {{-- View Toggle and Navigation --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm calendar-view-btn active" data-view="month" aria-pressed="true">
                        <i class="fas fa-calendar me-1"></i>{{ __('calendar.month') }}
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm calendar-view-btn" data-view="week" aria-pressed="false">
                        <i class="fas fa-calendar-week me-1"></i>{{ __('calendar.week') }}
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm calendar-view-btn" data-view="agenda" aria-pressed="false">
                        <i class="fas fa-list me-1"></i>{{ __('calendar.agenda') }}
                    </button>
                </div>
                
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="calendar-prev" aria-label="{{ __('calendar.previous') }}">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="calendar-today" aria-label="{{ __('calendar.today') }}">
                        {{ __('calendar.today') }}
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="calendar-next" aria-label="{{ __('calendar.next') }}">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            {{-- Filters and Search --}}
            <div class="d-flex flex-wrap gap-2 mb-4">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="calendar-search" placeholder="{{ __('calendar.search_courses') }}" aria-label="{{ __('calendar.search_courses') }}">
                </div>
                <div class="btn-group" role="group" aria-label="{{ __('calendar.search_courses') }}">
                    <input type="radio" class="btn-check" name="calendar-filter" id="filter-all" value="all" checked>
                    <label class="btn btn-outline-secondary btn-sm" for="filter-all">{{ __('calendar.all') }}</label>
                    
                    <input type="radio" class="btn-check" name="calendar-filter" id="filter-enrolled" value="enrolled">
                    <label class="btn btn-outline-secondary btn-sm" for="filter-enrolled">{{ __('calendar.enrolled') }}</label>
                    
                    <input type="radio" class="btn-check" name="calendar-filter" id="filter-favorites" value="favorites">
                    <label class="btn btn-outline-secondary btn-sm" for="filter-favorites">{{ __('calendar.favorites') }}</label>
                </div>
                <div class="form-check form-switch d-flex align-items-center">
                    <input class="form-check-input" type="checkbox" id="hide-past" role="switch">
                    <label class="form-check-label ms-2" for="hide-past">{{ __('calendar.hide_past') }}</label>
                </div>
            </div>

            {{-- Calendar Views --}}
            <div id="calendar-month-view" class="calendar-view" role="grid" aria-label="{{ __('calendar.month') }}">
                <div class="calendar-month-header mb-3">
                    <h3 class="text-center fw-bold" id="calendar-month-title"></h3>
                </div>
                <div class="calendar-month-grid" id="calendar-month-grid">
                    {{-- Month grid will be rendered here --}}
                </div>
            </div>

            <div id="calendar-week-view" class="calendar-view d-none" role="grid" aria-label="{{ __('calendar.week') }}">
                <div class="calendar-week-header mb-3">
                    <h3 class="text-center fw-bold" id="calendar-week-title"></h3>
                </div>
                <div class="calendar-week-grid" id="calendar-week-grid">
                    {{-- Week grid will be rendered here --}}
                </div>
            </div>

            <div id="calendar-agenda-view" class="calendar-view d-none" role="list" aria-label="{{ __('calendar.agenda') }}">
                <div class="calendar-agenda-header mb-3">
                    <h3 class="text-center fw-bold" id="calendar-agenda-title"></h3>
                </div>
                <div class="calendar-agenda-list" id="calendar-agenda-list">
                    {{-- Agenda list will be rendered here --}}
                </div>
            </div>

            {{-- Loading State --}}
            <div id="calendar-loading" class="text-center py-5 d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">{{ __('calendar.loading') }}</span>
                </div>
                <p class="mt-2 text-muted">{{ __('calendar.loading') }}</p>
            </div>

            {{-- Empty State --}}
            <div id="calendar-empty" class="text-center py-5 d-none">
                <i class="bi bi-calendar-x fs-1 text-muted mb-3"></i>
                <h5 class="fw-bold">{{ __('calendar.no_upcoming_course_windows') }}</h5>
                <p class="text-muted">{{ __('calendar.no_events_period') }}</p>
                <a href="{{ route('courses.index') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-search me-2"></i>{{ __('calendar.browse_courses') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Event Details Modal --}}
    <div class="modal fade" id="event-details-modal" tabindex="-1" aria-labelledby="event-details-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="event-details-modal-label">{{ __('calendar.course_event') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('calendar.close') }}"></button>
                </div>
                <div class="modal-body" id="event-details-content">
                    {{-- Event details will be rendered here --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('calendar.close') }}</button>
                    <a href="#" id="event-go-to-course" class="btn btn-primary">{{ __('calendar.go_to_course') }}</a>
                </div>
            </div>
        </div>
    </div>

    {{-- ARIA Live Region for Updates --}}
    <div id="calendar-aria-live" class="visually-hidden" aria-live="polite" aria-atomic="true"></div>
</div>

@push('scripts')
<script>
    // Calendar configuration
    window.CalendarConfig = {
        apiUrl: @json(route('api.student.calendar')),
        courseUrlTemplate: @json(url('/courses/:id')),
        locale: @json(app()->getLocale()),
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        userId: @json(auth()->id()),
        translations: {
            course_starts: @json(__('calendar.course_starts')),
            course_ends: @json(__('calendar.course_ends')),
            in: @json(__('calendar.in')),
            starts_at: @json(__('calendar.starts_at')),
            ends_at: @json(__('calendar.ends_at')),
            go_to_course: @json(__('calendar.go_to_course')),
            events_updated: @json(__('calendar.events_updated')),
        }
    };
</script>
@vite(['resources/js/student-calendar.js'])
@endpush
@endsection

