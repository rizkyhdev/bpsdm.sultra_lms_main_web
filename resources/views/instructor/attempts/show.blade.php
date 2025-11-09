@extends('layouts.instructor')

@section('title','Detail Attempt')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.quizzes.show', $attempt->quiz) }}">{{ $attempt->quiz->judul }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.attempts.index', $attempt->quiz->id) }}">Attempts</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
  </ol>
  {{-- Binding: $attempt, $items --}}
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
      <h4 class="mb-0">Quiz Attempt Details</h4>
      <small class="text-muted">Quiz: {{ $attempt->quiz->judul }}</small>
    </div>
    <div>
      <a href="{{ route('instructor.attempts.index', $attempt->quiz->id) }}" class="btn btn-light btn-sm me-2">
        <i class="bi bi-arrow-left"></i> Back to Attempts
      </a>
      <a href="{{ route('instructor.quizzes.show', $attempt->quiz->id) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-eye"></i> View Quiz
      </a>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h6 class="text-muted mb-2">Student</h6>
          <h5 class="mb-0">{{ $attempt->user->name ?? 'Unknown User' }}</h5>
          @if($attempt->user->email ?? null)
            <small class="text-muted">{{ $attempt->user->email }}</small>
          @endif
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h6 class="text-muted mb-2">Score</h6>
          <h5 class="mb-0 {{ ($attempt->is_passed ?? false) ? 'text-success' : 'text-danger' }}">
            {{ number_format($attempt->nilai ?? 0, 1) }}%
          </h5>
          <small class="text-muted">Minimum: {{ $attempt->quiz->nilai_minimum }}%</small>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h6 class="text-muted mb-2">Status</h6>
          <h5 class="mb-0">
            @if($attempt->is_passed ?? false)
              <span class="badge bg-success">Passed</span>
            @else
              <span class="badge bg-danger">Failed</span>
            @endif
          </h5>
          <small class="text-muted">Attempt #{{ $attempt->attempt_number ?? 1 }}</small>
        </div>
      </div>
    </div>
  </div>

  <form action="{{ route('instructor.attempts.grade_essay', $attempt->id) }}" method="post">
    @csrf
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Student Answers</h5>
      </div>
      <div class="card-body">
        @forelse($items as $i => $item)
          <div class="card mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0">
                  <strong>{{ $i+1 }}. {{ $item['pertanyaan'] }}</strong>
                </h6>
                <div>
                  <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $item['tipe'])) }}</span>
                  <span class="badge bg-info">Weight: {{ $item['bobot'] }}</span>
                </div>
              </div>
              
              @if($item['tipe'] === 'essay')
                <div class="p-3 border rounded bg-light mb-3">
                  {!! nl2br(e($item['jawaban'])) !!}
                </div>
                @if(empty($item['graded']))
                  <div class="d-flex align-items-center gap-2">
                    <label class="mb-0">Grade Essay (0 - {{ $item['bobot'] }}):</label>
                    <input type="number" 
                           name="grades[{{ $item['question_id'] }}]" 
                           class="form-control form-control-sm" 
                           style="width: 100px;"
                           min="0" 
                           max="{{ $item['bobot'] }}"
                           required>
                    <small class="text-muted">/ {{ $item['bobot'] }} points</small>
                  </div>
                @else
                  <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Already Graded:</strong> {{ $item['score'] }} / {{ $item['bobot'] }} points
                  </div>
                @endif
              @else
                <div class="mb-2">
                  <strong>Answer:</strong> 
                  <span class="ms-2">{!! nl2br(e($item['jawaban'])) !!}</span>
                </div>
                <div>
                  <strong>Correct?</strong> 
                  <span class="badge bg-{{ $item['is_correct'] ? 'success' : 'danger' }} ms-2">
                    {{ $item['is_correct'] ? 'Yes' : 'No' }}
                  </span>
                </div>
              @endif
            </div>
          </div>
        @empty
          <div class="text-center py-4 text-muted">
            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
            <p class="mt-2">No answers found for this attempt.</p>
          </div>
        @endforelse
      </div>
      @if(count($items) > 0)
        <div class="card-footer d-flex justify-content-end">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i>Save Essay Grades
          </button>
        </div>
      @endif
    </div>
  </form>
</div>
@endsection


