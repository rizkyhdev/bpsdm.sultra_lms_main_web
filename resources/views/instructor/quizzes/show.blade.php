@extends('layouts.instructor')

@section('title', $quiz->judul)

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.sub_modules.show', $quiz->subModule->id) }}">{{ $quiz->subModule->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $quiz->judul }}</li>
  </ol>
  {{-- Binding: $quiz, $stats --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error!</strong> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0">{{ $quiz->judul }}</h4>
      <small class="text-muted">{{ $quiz->deskripsi }}</small>
    </div>
    <div>
      <a href="{{ route('instructor.quizzes.index', $quiz->subModule->id) }}" class="btn btn-light btn-sm me-2">
        <i class="bi bi-arrow-left"></i> Back to Quizzes
      </a>
      @can('update', $quiz)
        <a href="{{ route('instructor.quizzes.edit', $quiz->id) }}" class="btn btn-outline-secondary btn-sm me-2">
          <i class="bi bi-pencil"></i> Edit
        </a>
      @endcan
      @can('delete', $quiz)
        <form action="{{ route('instructor.quizzes.destroy', $quiz->id) }}" method="post" class="d-inline" 
              onsubmit="return confirm('Are you sure you want to delete this quiz?\n\nThis will also delete:\n- All questions\n- All answer options\n\nThis action cannot be undone!');">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-sm me-2">
            <i class="bi bi-trash"></i> Delete
          </button>
        </form>
      @endcan
      <a href="{{ route('instructor.questions.index', $quiz->id) }}" class="btn btn-primary btn-sm me-2">
        <i class="bi bi-list-ul"></i> Manage Questions
      </a>
      <a href="{{ route('instructor.quizzes.results', $quiz->id) }}" class="btn btn-info btn-sm">
        <i class="bi bi-bar-chart"></i> View Results
      </a>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title text-muted mb-0">Questions</h5>
          <h2 class="mb-0">{{ $quiz->questions->count() ?? 0 }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title text-muted mb-0">Attempts</h5>
          <h2 class="mb-0">{{ $attemptsCount ?? 0 }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title text-muted mb-0">Avg Score</h5>
          <h2 class="mb-0">{{ number_format($avgScore ?? 0, 1) }}%</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title text-muted mb-0">Min Score</h5>
          <h2 class="mb-0">{{ $quiz->nilai_minimum }}%</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Quiz Details</h5>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Title</dt>
        <dd class="col-sm-9">{{ $quiz->judul }}</dd>
        <dt class="col-sm-3">Description</dt>
        <dd class="col-sm-9">{{ $quiz->deskripsi ?? 'No description' }}</dd>
        <dt class="col-sm-3">Minimum Score</dt>
        <dd class="col-sm-9">{{ $quiz->nilai_minimum }}%</dd>
        <dt class="col-sm-3">Max Attempts</dt>
        <dd class="col-sm-9">{{ $quiz->max_attempts }}</dd>
        <dt class="col-sm-3">Questions Count</dt>
        <dd class="col-sm-9">{{ $quiz->questions->count() ?? 0 }}</dd>
      </dl>
    </div>
  </div>
</div>
@endsection


