@extends('layouts.instructor')

@section('title','Detail Pertanyaan')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.quizzes.show', $question->quiz) }}">{{ $question->quiz->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Pertanyaan</li>
  </ol>
  {{-- Binding: $question --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Pertanyaan</dt>
        <dd class="col-sm-9">{{ $question->pertanyaan }}</dd>
        <dt class="col-sm-3">Tipe</dt>
        <dd class="col-sm-9">{{ $question->tipe }}</dd>
        <dt class="col-sm-3">Bobot</dt>
        <dd class="col-sm-9">{{ $question->bobot }}</dd>
      </dl>
      @if(in_array($question->tipe, ['multiple_choice','true_false']))
        <h6>Opsi Jawaban</h6>
        <ul>
          @foreach($question->answerOptions as $opt)
            <li>
              {{ $opt->teks_jawaban }}
              @if($opt->is_correct)
                <span class="badge badge-success">Benar</span>
              @endif
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
</div>
@endsection


