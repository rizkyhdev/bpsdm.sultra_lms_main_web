@extends('layouts.instructor')

@section('title', $quiz->judul)

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.sub_modules.show', $quiz->subModule) }}">{{ $quiz->subModule->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $quiz->judul }}</li>
  </ol>
  {{-- Binding: $quiz, $stats --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small">Attempts: {{ $stats['attempts'] ?? 0 }} | Rata-rata Nilai: {{ $stats['avg_score'] ?? 0 }}%</div>
    <div>
      <a href="{{ route('instructor.questions.index', $quiz) }}" class="btn btn-outline-primary btn-sm">Kelola Pertanyaan</a>
      <a href="{{ route('instructor.quizzes.results', $quiz) }}" class="btn btn-outline-info btn-sm">Lihat Hasil</a>
      @can('update', $quiz)
        <a href="{{ route('instructor.quizzes.edit', $quiz) }}" class="btn btn-secondary btn-sm">Edit</a>
      @endcan
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Judul</dt>
        <dd class="col-sm-9">{{ $quiz->judul }}</dd>
        <dt class="col-sm-3">Deskripsi</dt>
        <dd class="col-sm-9">{{ $quiz->deskripsi }}</dd>
        <dt class="col-sm-3">Nilai Minimum</dt>
        <dd class="col-sm-9">{{ $quiz->nilai_minimum }}</dd>
        <dt class="col-sm-3">Maks Attempts</dt>
        <dd class="col-sm-9">{{ $quiz->max_attempts }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection


