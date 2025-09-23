@extends('layouts.admin')

@section('title', 'Detail Pertanyaan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.quizzes.show', $question->quiz_id) }}">Kuis</a></li>
    <li class="breadcrumb-item active">Detail Pertanyaan</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit mr-1"></i> Ubah</a>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-1">Pertanyaan</h5>
            <p class="mb-0">{!! nl2br(e($question->pertanyaan)) !!}</p>
            <p class="text-muted mt-2 mb-0">Tipe: {{ $question->tipe }} | Bobot: {{ $question->bobot }}</p>
            @if(isset($answerOptions) && count($answerOptions))
                <hr>
                <h6 class="mb-2">Opsi Jawaban</h6>
                <ul class="mb-0">
                    @foreach($answerOptions as $opt)
                        <li>{!! e($opt->teks_jawaban) !!} @if($opt->is_correct) <span class="badge badge-success">Benar</span> @endif</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection


