@extends('layouts.instructor')

@section('title','Attempts')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.quizzes.show', $quiz) }}">{{ $quiz->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Attempts</li>
  </ol>
  {{-- Binding: $quiz, $attempts --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead><tr><th>User</th><th>Attempt</th><th>Nilai</th><th>Passed</th><th>Mulai</th><th>Selesai</th><th class="text-right">Aksi</th></tr></thead>
        <tbody>
          @forelse($attempts as $a)
            <tr>
              <td>{{ $a->user->name ?? '-' }}</td>
              <td>{{ $a->attempt_number ?? '-' }}</td>
              <td>{{ $a->nilai ?? 0 }}</td>
              <td><span class="badge badge-{{ ($a->is_passed ?? false) ? 'success' : 'secondary' }}">{{ ($a->is_passed ?? false) ? 'Yes' : 'No' }}</span></td>
              <td>{{ optional($a->started_at)->format('d M Y H:i') }}</td>
              <td>{{ optional($a->completed_at)->format('d M Y H:i') }}</td>
              <td class="text-right">
                <a href="{{ route('instructor.attempts.show', $a) }}" class="btn btn-sm btn-outline-primary">Show</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center">Tidak ada data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection


