@extends('layouts.instructor')

@section('title','Quiz Report')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.quizzes.show', $quiz) }}">{{ $quiz->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Quiz Report</li>
  </ol>
  {{-- Binding: $quiz, $analysis --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead><tr><th>#</th><th>Pertanyaan</th><th>Correct Rate</th></tr></thead>
        <tbody>
          @forelse($analysis as $i => $row)
            <tr>
              <td>{{ $i+1 }}</td>
              <td class="text-truncate" style="max-width: 520px;">{{ $row['pertanyaan'] }}</td>
              <td>{{ $row['correct_rate'] ?? 0 }}%</td>
            </tr>
          @empty
            <tr><td colspan="3" class="text-center">Tidak ada data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-body">
      <a href="{{ route('instructor.reports.quiz', array_merge(['quiz'=>$quiz->id], request()->all(), ['export'=>'csv'])) }}" class="btn btn-outline-secondary btn-sm">Export CSV</a>
      <a href="{{ route('instructor.reports.quiz', array_merge(['quiz'=>$quiz->id], request()->all(), ['export'=>'pdf'])) }}" class="btn btn-outline-secondary btn-sm">Export PDF</a>
    </div>
  </div>
</div>
@endsection


