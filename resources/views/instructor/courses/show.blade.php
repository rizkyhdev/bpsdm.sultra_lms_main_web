@extends('layouts.instructor')

@section('title', $course->judul)

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}">Courses</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $course->judul }}</li>
  </ol>
  {{-- Binding: $course (loadCount modules,userEnrollments), $stats['completion_rate'] --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h5 class="mb-1">Ringkasan</h5>
      <div class="text-muted small">Modules: {{ $course->modules_count }} | Enrollments: {{ $course->user_enrollments_count ?? $course->enrollments_count }} | Completion: {{ $stats['completion_rate'] ?? 0 }}%</div>
    </div>
    <div>
      @can('update', $course)
        <a href="{{ route('instructor.courses.edit', $course->id) }}" class="btn btn-secondary btn-sm">Edit</a>
      @endcan
      @can('delete', $course)
        <form action="{{ route('instructor.courses.destroy', $course->id) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus course ini?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
        </form>
      @endcan
      @can('duplicate', $course)
        <form action="{{ route('instructor.courses.duplicate', $course->id) }}" method="post" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-info btn-sm">Duplicate</button>
        </form>
      @endcan
    </div>
  </div>

  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#modules" role="tab">Modules</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#schedule" role="tab">Schedule</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#enrollments" role="tab">Enrollments</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reports" role="tab">Reports</a></li>
  </ul>
  <div class="tab-content p-3 border border-top-0">
    <div class="tab-pane fade show active" id="modules" role="tabpanel">
      <div class="mb-3">
        @can('update', $course)
          <button type="button" class="btn btn-primary btn-sm" onclick="openModuleModal({{ $course->id }})">
            <i class="bi bi-plus-circle"></i> Add Module
          </button>
        @endcan
      </div>
      <div class="list-group">
        @forelse($course->modules as $m)
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <a href="{{ route('instructor.modules.show', $m) }}" class="flex-grow-1 text-decoration-none">
              <span>{{ $m->urutan }}. {{ $m->judul }}</span>
            </a>
            <div class="btn-group btn-group-sm" role="group">
              @can('update', $m)
                <button type="button" class="btn btn-outline-secondary" onclick="openModuleModal({{ $course->id }}, {{ $m->id }})" title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
              @endcan
              @can('delete', $m)
                <button type="button" class="btn btn-outline-danger" onclick="openModuleDeleteModal({{ $m->id }})" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              @endcan
            </div>
          </div>
        @empty
          <div class="text-muted">Belum ada module.</div>
        @endforelse
      </div>
    </div>
    <div class="tab-pane fade" id="schedule" role="tabpanel">
      @can('updateSchedule', $course)
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0">Course Schedule</h6>
          <small class="text-muted">Stored in UTC. Shown in your local time: {{ config('app.timezone') }}</small>
        </div>
        <div class="card-body">
          <form id="scheduleForm" action="{{ route('instructor.courses.schedule.update', $course->id) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="start_date_time" class="form-label">Start Date & Time</label>
                <input 
                  type="datetime-local" 
                  id="start_date_time" 
                  name="start_date_time" 
                  class="form-control @error('start_date_time') is-invalid @enderror"
                  value="{{ $course->start_date_time ? $course->start_date_time->setTimezone(config('app.timezone'))->format('Y-m-d\TH:i') : '' }}"
                >
                @error('start_date_time')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="document.getElementById('start_date_time').value = ''">
                  <i class="bi bi-x-circle"></i> Clear
                </button>
              </div>
              <div class="col-md-6">
                <label for="end_date_time" class="form-label">End Date & Time</label>
                <input 
                  type="datetime-local" 
                  id="end_date_time" 
                  name="end_date_time" 
                  class="form-control @error('end_date_time') is-invalid @enderror"
                  value="{{ $course->end_date_time ? $course->end_date_time->setTimezone(config('app.timezone'))->format('Y-m-d\TH:i') : '' }}"
                >
                @error('end_date_time')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="document.getElementById('end_date_time').value = ''">
                  <i class="bi bi-x-circle"></i> Clear
                </button>
              </div>
            </div>
            
            @if($errors->has('start_date_time') || $errors->has('end_date_time'))
              <div class="alert alert-danger">
                @if($errors->has('start_date_time'))
                  <div>{{ $errors->first('start_date_time') }}</div>
                @endif
                @if($errors->has('end_date_time'))
                  <div>{{ $errors->first('end_date_time') }}</div>
                @endif
              </div>
            @endif
            
            <div class="mb-3">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Update Schedule
              </button>
            </div>
          </form>
          
          {{-- Preview Countdown --}}
          @if($course->start_date_time || $course->end_date_time)
          <div class="mt-4">
            <h6>Preview</h6>
            <div class="alert alert-info">
              <div x-data="courseCountdown({
                startDateTimeUtc: @js($course->start_date_time?->toIso8601String()),
                endDateTimeUtc: @js($course->end_date_time?->toIso8601String()),
                serverNowUtc: @js(now('UTC')->toIso8601String()),
                locale: @js(app()->getLocale()),
                scheduleStatus: @js($course->scheduleStatus())
              })" x-init="init()">
                <strong x-text="statusText"></strong>
                <div x-show="showCountdown" class="mt-2">
                  <span class="badge bg-primary fs-6" x-text="countdownText"></span>
                </div>
                <div x-show="showEnded" class="mt-2">
                  <span class="text-muted">{{ __('schedule.ended') }}</span>
                </div>
              </div>
            </div>
          </div>
          @endif
          
          @if($course->updated_by)
          <div class="mt-3 small text-muted">
            Last updated by: {{ $course->scheduleUpdater->name ?? 'Unknown' }} 
            at {{ $course->updated_at->format('Y-m-d H:i:s') }}
          </div>
          @endif
        </div>
      </div>
      @else
      <div class="alert alert-warning">
        You don't have permission to update the schedule.
      </div>
      @endcan
    </div>
    <div class="tab-pane fade" id="enrollments" role="tabpanel">
      <p>Snapshot singkat. <a href="{{ route('instructor.enrollments.index', ['course' => $course->id]) }}">Lihat detail</a></p>
    </div>
    <div class="tab-pane fade" id="reports" role="tabpanel">
      <a class="btn btn-outline-primary" href="{{ route('instructor.reports.course', ['courseId' => $course->id]) }}">Course Report</a>
    </div>
  </div>
</div>

{{-- Schedule Form Handler --}}
@can('updateSchedule', $course)
@include('partials.modals.module-modal')
<script src="{{ asset('js/modal-operations.js') }}"></script>
<script>
document.getElementById('scheduleForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    
    const formData = new FormData(form);
    
    try {
        const response = await fetch(form.action, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            alert('Schedule updated successfully!');
            if (data.meta?.warning) {
                alert('Warning: ' + data.meta.warning);
            }
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update schedule'));
        }
    } catch (error) {
        console.error(error);
        alert('Error: Failed to update schedule');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
</script>

{{-- Course Countdown Component (for preview) --}}
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
            showCountdown: false,
            showEnded: false,
            intervalId: null,
            
            init() {
                this.computeDrift();
                this.update();
                this.startInterval();
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
                    const diff = start - now;
                    this.statusText = '{{ __("schedule.starts_in", ["countdown" => ""]) }}';
                    this.countdownText = this.formatCountdown(diff);
                    this.showCountdown = true;
                    this.showEnded = false;
                } else if (end && now >= end) {
                    this.statusText = '{{ __("schedule.ended") }}';
                    this.showCountdown = false;
                    this.showEnded = true;
                } else {
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
            
            destroy() {
                if (this.intervalId) {
                    clearInterval(this.intervalId);
                }
            }
        };
    }
</script>
@endcan
@endsection


