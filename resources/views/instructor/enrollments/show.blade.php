@extends('layouts.instructor')

@section('title','Detail Enrollment')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.enrollments.index') }}">Enrollments</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
  </ol>
  {{-- Binding: $enrollment, $progress --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card mb-3">
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">User</dt>
        <dd class="col-sm-9">{{ $enrollment->user->name ?? '-' }}</dd>
        <dt class="col-sm-3">Course</dt>
        <dd class="col-sm-9">{{ $enrollment->course->judul ?? '-' }}</dd>
        <dt class="col-sm-3">Tanggal</dt>
        <dd class="col-sm-9">{{ optional($enrollment->created_at)->format('d M Y') }}</dd>
        <dt class="col-sm-3">Status</dt>
        <dd class="col-sm-9">{{ $enrollment->status }}</dd>
      </dl>
    </div>
  </div>

  <div class="card">
    <div class="card-header">Progress</div>
    <div class="card-body">
      {{-- Komentar: $progress berisi ringkasan progress per module/sub-module --}}
      @forelse($progress as $module)
        <div class="mb-2">
          <strong>{{ $module['judul'] }}</strong>
          <div class="progress" style="height: 8px;">
            <div class="progress-bar" role="progressbar" style="width: {{ $module['completion'] ?? 0 }}%"></div>
          </div>
          <div class="small text-muted">{{ $module['completion'] ?? 0 }}% selesai</div>
        </div>
      @empty
        <div class="text-muted">Belum ada progress</div>
      @endforelse
    </div>
  </div>
</div>
@endsection


