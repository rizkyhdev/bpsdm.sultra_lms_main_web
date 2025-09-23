@extends('layouts.instructor')

@section('title','Course Report')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $course) }}">{{ $course->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Course Report</li>
  </ol>
  {{-- Binding: $course, $report --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card mb-3">
    <div class="card-body">
      <form class="form-row" method="get" action="{{ route('instructor.reports.course', $course) }}">
        <div class="form-group col-md-4">
          <label class="small mb-1">Dari</label>
          <input type="date" name="from" value="{{ request('from') }}" class="form-control">
        </div>
        <div class="form-group col-md-4">
          <label class="small mb-1">Sampai</label>
          <input type="date" name="to" value="{{ request('to') }}" class="form-control">
        </div>
        <div class="form-group col-md-4 d-flex align-items-end">
          <button class="btn btn-primary" type="submit">Filter</button>
          <a href="{{ route('instructor.reports.course', $course) }}" class="btn btn-link">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="card"><div class="card-body">
        <div class="text-muted small">Completion Rate</div>
        <h4>{{ $report['completion_rate'] ?? 0 }}%</h4>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card"><div class="card-body">
        <div class="text-muted small">Rata-rata Skor</div>
        <h4>{{ $report['avg_score'] ?? 0 }}%</h4>
      </div></div>
    </div>
    <div class="col-md-4">
      <div class="card"><div class="card-body">
        <div class="text-muted small">Waktu Rata-rata Selesai</div>
        <h4>{{ $report['avg_time_to_complete'] ?? '-' }}</h4>
      </div></div>
    </div>
  </div>

  <div class="mt-3">
    <a href="{{ route('instructor.reports.course', array_merge(['course'=>$course->id], request()->all(), ['export'=>'csv'])) }}" class="btn btn-outline-secondary btn-sm">Export CSV</a>
    <a href="{{ route('instructor.reports.course', array_merge(['course'=>$course->id], request()->all(), ['export'=>'pdf'])) }}" class="btn btn-outline-secondary btn-sm">Export PDF</a>
  </div>
</div>
@endsection


