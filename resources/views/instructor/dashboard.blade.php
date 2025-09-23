@extends('layouts.instructor')

@section('title','Dashboard')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
  </ol>
  {{-- Binding controller: $metrics (total_courses,total_enrollments,avg_completion,avg_quiz_score) --}}
  {{-- Binding controller: $recentEnrollments, $recentAttempts --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-md-3">
      <div class="card text-white bg-primary mb-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="card-title mb-0">Total Courses</div>
              <h4 class="mb-0">{{ $metrics['total_courses'] ?? 0 }}</h4>
            </div>
            <span class="badge badge-light">All</span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-info mb-3">
        <div class="card-body">
          <div class="card-title mb-0">Total Enrollments</div>
          <h4 class="mb-0">{{ $metrics['total_enrollments'] ?? 0 }}</h4>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-success mb-3">
        <div class="card-body">
          <div class="card-title mb-0">Avg Completion</div>
          <h4 class="mb-0">{{ $metrics['avg_completion'] ?? 0 }}%</h4>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning mb-3">
        <div class="card-body">
          <div class="card-title mb-0">Avg Quiz Score</div>
          <h4 class="mb-0">{{ $metrics['avg_quiz_score'] ?? 0 }}%</h4>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header">Recent Enrollments</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Course</th>
                  <th>Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentEnrollments as $en)
                  <tr>
                    <td>{{ $en->user->name ?? '-' }}</td>
                    <td>{{ $en->course->judul ?? '-' }}</td>
                    <td>{{ optional($en->created_at)->format('d M Y') }}</td>
                    <td><span class="badge badge-{{ $en->status === 'completed' ? 'success' : 'secondary' }}">{{ $en->status }}</span></td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center">Belum ada data</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header">Recent Quiz Attempts</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Quiz</th>
                  <th>Score</th>
                  <th>Passed</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentAttempts as $at)
                  <tr>
                    <td>{{ $at->user->name ?? '-' }}</td>
                    <td>{{ $at->quiz->judul ?? '-' }}</td>
                    <td>{{ $at->nilai ?? 0 }}</td>
                    <td>
                      <span class="badge badge-{{ ($at->is_passed ?? false) ? 'success' : 'secondary' }}">
                        {{ ($at->is_passed ?? false) ? 'Yes' : 'No' }}
                      </span>
                    </td>
                    <td>{{ optional($at->created_at)->format('d M Y') }}</td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-center">Belum ada data</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


