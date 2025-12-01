@extends('layouts.instructor')

@section('title', 'Quiz Results - ' . $quiz->judul)

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    @if($quiz->subModule && $quiz->subModule->module && $quiz->subModule->module->course)
      <li class="breadcrumb-item">
        <a href="{{ route('instructor.courses.show', $quiz->subModule->module->course) }}">
          {{ $quiz->subModule->module->course->judul }}
        </a>
      </li>
    @endif
    @if($quiz->subModule && $quiz->subModule->module)
      <li class="breadcrumb-item">
        <a href="{{ route('instructor.modules.show', $quiz->subModule->module_id) }}">
          {{ $quiz->subModule->module->judul }}
        </a>
      </li>
    @endif
    @if($quiz->subModule)
      <li class="breadcrumb-item">
        <a href="{{ route('instructor.sub_modules.show', $quiz->subModule->id) }}">
          {{ $quiz->subModule->judul }}
        </a>
      </li>
    @endif
    <li class="breadcrumb-item">
      <a href="{{ route('instructor.quizzes.show', $quiz->id) }}">{{ $quiz->judul }}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Results</li>
  </ol>
  {{-- Binding: $quiz, $attempts, $totalAttempts, $passedAttempts, $failedAttempts, $avgScore, $highestScore, $lowestScore, $passRate, $uniqueUsers --}}
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
      <h4 class="mb-0">Quiz Results</h4>
      <small class="text-muted">{{ $quiz->judul }}</small>
    </div>
    <div>
      <a href="{{ route('instructor.quizzes.show', $quiz->id) }}" class="btn btn-light btn-sm me-2">
        <i class="bi bi-arrow-left"></i> Back to Quiz
      </a>
      <a href="{{ route('instructor.quizzes.index', $quiz->subModule->id ?? $quiz->id) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-list"></i> All Quizzes
      </a>
    </div>
  </div>

  {{-- Statistics Cards --}}
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title text-muted mb-0">Total Attempts</h5>
          <h2 class="mb-0">{{ $totalAttempts }}</h2>
          <small class="text-muted">{{ $uniqueUsers }} unique users</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title text-muted mb-0">Passed</h5>
          <h2 class="mb-0 text-success">{{ $passedAttempts }}</h2>
          <small class="text-muted">{{ $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 1) : 0 }}%</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title text-muted mb-0">Failed</h5>
          <h2 class="mb-0 text-danger">{{ $failedAttempts }}</h2>
          <small class="text-muted">{{ $totalAttempts > 0 ? round(($failedAttempts / $totalAttempts) * 100, 1) : 0 }}%</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title text-muted mb-0">Pass Rate</h5>
          <h2 class="mb-0">{{ number_format($passRate, 1) }}%</h2>
          <small class="text-muted">Average: {{ number_format($avgScore, 1) }}%</small>
        </div>
      </div>
    </div>
  </div>

  {{-- Score Range Cards --}}
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title text-muted mb-0">Average Score</h5>
          <h2 class="mb-0">{{ number_format($avgScore, 1) }}%</h2>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title text-muted mb-0">Highest Score</h5>
          <h2 class="mb-0 text-success">{{ number_format($highestScore, 1) }}%</h2>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body text-center">
          <h5 class="card-title text-muted mb-0">Lowest Score</h5>
          <h2 class="mb-0 text-danger">{{ number_format($lowestScore, 1) }}%</h2>
        </div>
      </div>
    </div>
  </div>

  {{-- Quiz Attempts Table --}}
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Quiz Attempts ({{ $attempts->total() }})</h5>
      <div>
        <small class="text-muted">Showing completed attempts only</small>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th width="80">#</th>
              <th>Student</th>
              <th width="100" class="text-center">Attempt</th>
              <th width="120" class="text-center">Score</th>
              <th width="100" class="text-center">Status</th>
              <th width="150" class="text-center">Completed At</th>
              <th width="120" class="text-center">Duration</th>
              <th width="100" class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($attempts as $attempt)
              <tr>
                <td>{{ $attempts->firstItem() + $loop->index }}</td>
                <td>
                  <div>
                    <strong>{{ $attempt->user->name ?? 'Unknown User' }}</strong>
                    @if($attempt->user->email ?? null)
                      <br><small class="text-muted">{{ $attempt->user->email }}</small>
                    @endif
                  </div>
                </td>
                <td class="text-center">
                  <span class="badge bg-secondary">#{{ $attempt->attempt_number ?? 1 }}</span>
                </td>
                <td class="text-center">
                  <strong class="{{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">
                    {{ number_format($attempt->nilai, 1) }}%
                  </strong>
                </td>
                <td class="text-center">
                  @if($attempt->is_passed)
                    <span class="badge bg-success">Passed</span>
                  @else
                    <span class="badge bg-danger">Failed</span>
                  @endif
                </td>
                <td class="text-center">
                  <small>{{ $attempt->completed_at ? $attempt->completed_at->format('d M Y H:i') : 'N/A' }}</small>
                </td>
                <td class="text-center">
                  @if($attempt->started_at && $attempt->completed_at)
                    @php
                      $duration = $attempt->started_at->diffInMinutes($attempt->completed_at);
                      $hours = floor($duration / 60);
                      $minutes = $duration % 60;
                    @endphp
                    <small>
                      @if($hours > 0)
                        {{ $hours }}h {{ $minutes }}m
                      @else
                        {{ $minutes }}m
                      @endif
                    </small>
                  @else
                    <small class="text-muted">N/A</small>
                  @endif
                </td>
                <td class="text-center">
                  <a href="{{ route('instructor.attempts.show', $attempt->id) }}" 
                     class="btn btn-sm btn-outline-primary" 
                     title="View Details">
                    <i class="bi bi-eye"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-4">
                  <div class="text-muted">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2">No quiz attempts found yet.</p>
                    <small>Students need to complete the quiz before results appear here.</small>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($attempts->hasPages())
      <div class="card-footer">
        {{ $attempts->links() }}
      </div>
    @endif
  </div>

  {{-- Additional Information --}}
  <div class="row mt-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0">Quiz Information</h6>
        </div>
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-sm-5">Title</dt>
            <dd class="col-sm-7">{{ $quiz->judul }}</dd>
            <dt class="col-sm-5">Minimum Score</dt>
            <dd class="col-sm-7">{{ $quiz->nilai_minimum }}%</dd>
            <dt class="col-sm-5">Max Attempts</dt>
            <dd class="col-sm-7">{{ $quiz->max_attempts ?? 'Unlimited' }}</dd>
            <dt class="col-sm-5">Questions</dt>
            <dd class="col-sm-7">{{ $quiz->questions()->count() }}</dd>
            @if($quiz->subModule)
              <dt class="col-sm-5">Sub-Module</dt>
              <dd class="col-sm-7">
                <a href="{{ route('instructor.sub_modules.show', $quiz->subModule->id) }}">
                  {{ $quiz->subModule->judul }}
                </a>
              </dd>
            @endif
          </dl>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0">Performance Summary</h6>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <small>Pass Rate</small>
              <small>{{ number_format($passRate, 1) }}%</small>
            </div>
            <div class="progress" style="height: 20px;">
              <div class="progress-bar bg-success" role="progressbar" 
                   style="width: {{ $passRate }}%" 
                   aria-valuenow="{{ $passRate }}" 
                   aria-valuemin="0" 
                   aria-valuemax="100">
                {{ number_format($passRate, 1) }}%
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between mb-1">
              <small>Average Score vs Minimum</small>
              <small>{{ number_format($avgScore, 1) }}% / {{ $quiz->nilai_minimum }}%</small>
            </div>
            <div class="progress" style="height: 20px;">
              <div class="progress-bar {{ $avgScore >= $quiz->nilai_minimum ? 'bg-success' : 'bg-warning' }}" 
                   role="progressbar" 
                   style="width: {{ min(100, ($avgScore / max($quiz->nilai_minimum, 1)) * 100) }}%" 
                   aria-valuenow="{{ $avgScore }}" 
                   aria-valuemin="0" 
                   aria-valuemax="100">
                {{ number_format($avgScore, 1) }}%
              </div>
            </div>
          </div>
          @if($totalAttempts > 0)
            <div class="mt-3">
              <small class="text-muted">
                <strong>Score Distribution:</strong><br>
                Range: {{ number_format($lowestScore, 1) }}% - {{ number_format($highestScore, 1) }}%<br>
                @if($totalAttempts > 1)
                  Standard Deviation: {{ number_format($stdDev ?? 0, 2) }}%
                @endif
              </small>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

