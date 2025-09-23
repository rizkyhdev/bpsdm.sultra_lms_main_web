@extends('layouts.instructor')

@section('title', $subModule->judul)

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $subModule->module->course) }}">{{ $subModule->module->course->judul }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.modules.show', $subModule->module) }}">{{ $subModule->module->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $subModule->judul }}</li>
  </ol>
  {{-- Binding: $subModule, $contents, $quizzes, $progressSummary --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small">Progress rata-rata: {{ $progressSummary['avg_completion'] ?? 0 }}%</div>
  </div>

  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#contents" role="tab">Contents</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#quizzes" role="tab">Quizzes</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#progress" role="tab">Progress</a></li>
  </ul>
  <div class="tab-content p-3 border border-top-0">
    <div class="tab-pane fade show active" id="contents" role="tabpanel">
      <div class="d-flex mb-2">
        @can('create', [App\Models\Content::class, $subModule])
          <a href="{{ route('instructor.sub_modules.contents.create', $subModule) }}" class="btn btn-primary btn-sm">Tambah Content</a>
        @endcan
      </div>
      <div class="list-group">
        @forelse($contents as $c)
          <a class="list-group-item list-group-item-action d-flex justify-content-between" href="{{ route('instructor.contents.show', $c) }}">
            <span>{{ $c->urutan }}. {{ $c->judul }} ({{ $c->tipe }})</span>
          </a>
        @empty
          <div class="text-muted">Belum ada konten.</div>
        @endforelse
      </div>
    </div>
    <div class="tab-pane fade" id="quizzes" role="tabpanel">
      <div class="d-flex mb-2">
        @can('create', [App\Models\Quiz::class, $subModule])
          <a href="{{ route('instructor.sub_modules.quizzes.create', $subModule) }}" class="btn btn-primary btn-sm">Tambah Quiz</a>
        @endcan
      </div>
      <div class="list-group">
        @forelse($quizzes as $q)
          <a class="list-group-item list-group-item-action d-flex justify-content-between" href="{{ route('instructor.quizzes.show', $q) }}">
            <span>{{ $q->judul }}</span>
          </a>
        @empty
          <div class="text-muted">Belum ada kuis.</div>
        @endforelse
      </div>
    </div>
    <div class="tab-pane fade" id="progress" role="tabpanel">
      <div class="row">
        <div class="col-md-4">
          <div class="card"><div class="card-body">
            <div class="text-muted small">Completion Rata-rata</div>
            <h4>{{ $progressSummary['avg_completion'] ?? 0 }}%</h4>
          </div></div>
        </div>
        <div class="col-md-4">
          <div class="card"><div class="card-body">
            <div class="text-muted small">Jumlah Peserta</div>
            <h4>{{ $progressSummary['participants'] ?? 0 }}</h4>
          </div></div>
        </div>
        <div class="col-md-4">
          <div class="card"><div class="card-body">
            <div class="text-muted small">Selesai</div>
            <h4>{{ $progressSummary['completed'] ?? 0 }}</h4>
          </div></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


