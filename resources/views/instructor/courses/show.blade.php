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
        <a href="{{ route('instructor.courses.edit', $course) }}" class="btn btn-secondary btn-sm">Edit</a>
      @endcan
      @can('delete', $course)
        <form action="{{ route('instructor.courses.destroy', $course) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus course ini?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm">Delete</button>
        </form>
      @endcan
      @can('duplicate', $course)
        <form action="{{ route('instructor.courses.duplicate', $course) }}" method="post" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-info btn-sm">Duplicate</button>
        </form>
      @endcan
    </div>
  </div>

  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#modules" role="tab">Modules</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#enrollments" role="tab">Enrollments</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reports" role="tab">Reports</a></li>
  </ul>
  <div class="tab-content p-3 border border-top-0">
    <div class="tab-pane fade show active" id="modules" role="tabpanel">
      <div class="list-group">
        @forelse($course->modules as $m)
          <a class="list-group-item list-group-item-action d-flex justify-content-between" href="{{ route('instructor.modules.show', $m) }}">
            <span>{{ $m->urutan }}. {{ $m->judul }}</span>
            <span class="text-muted small">Kelola</span>
          </a>
        @empty
          <div class="text-muted">Belum ada module.</div>
        @endforelse
      </div>
    </div>
    <div class="tab-pane fade" id="enrollments" role="tabpanel">
      <p>Snapshot singkat. <a href="{{ route('instructor.enrollments.index', ['course' => $course->id]) }}">Lihat detail</a></p>
    </div>
    <div class="tab-pane fade" id="reports" role="tabpanel">
      <a class="btn btn-outline-primary" href="{{ route('instructor.reports.course', ['courseId' => $course->id]) }}">Course Report</a>
    </div>
  </div>
</div>
@endsection


