@extends('layouts.instructor')

@section('title','Detail Attempt')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.quizzes.show', $attempt->quiz) }}">{{ $attempt->quiz->judul }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.quizzes.attempts.index', $attempt->quiz) }}">Attempts</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
  </ol>
  {{-- Binding: $attempt, $items --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card mb-3">
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">User</dt>
        <dd class="col-sm-9">{{ $attempt->user->name ?? '-' }}</dd>
        <dt class="col-sm-3">Nilai</dt>
        <dd class="col-sm-9">{{ $attempt->nilai ?? 0 }}</dd>
        <dt class="col-sm-3">Status</dt>
        <dd class="col-sm-9">{{ ($attempt->is_passed ?? false) ? 'Passed' : 'Not Passed' }}</dd>
      </dl>
    </div>
  </div>

  <form action="{{ route('instructor.attempts.gradeEssay', $attempt) }}" method="post">
    @csrf
    <div class="card">
      <div class="card-header">Jawaban Peserta</div>
      <div class="card-body">
        @forelse($items as $i => $item)
          <div class="mb-3">
            <div><strong>{{ $i+1 }}. {{ $item['pertanyaan'] }}</strong> <span class="badge badge-light">{{ $item['tipe'] }}</span></div>
            <div class="mt-1">
              @if($item['tipe'] === 'essay')
                <div class="p-2 border bg-light">{!! nl2br(e($item['jawaban'])) !!}</div>
                @if(empty($item['graded']))
                  <div class="form-inline mt-2">
                    <label class="mr-2">Nilai Essay</label>
                    <input type="number" name="grades[{{ $item['question_id'] }}]" class="form-control form-control-sm" min="0" max="{{ $item['bobot'] }}">
                  </div>
                @else
                  <div class="small text-success">Sudah dinilai: {{ $item['score'] }}</div>
                @endif
              @else
                <div>Jawaban: {!! nl2br(e($item['jawaban'])) !!}</div>
                <div>Benar? <span class="badge badge-{{ $item['is_correct'] ? 'success' : 'danger' }}">{{ $item['is_correct'] ? 'Ya' : 'Tidak' }}</span></div>
              @endif
            </div>
          </div>
        @empty
          <div class="text-muted">Tidak ada item</div>
        @endforelse
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary">Simpan Penilaian Essay</button>
      </div>
    </div>
  </form>
</div>
@endsection


