@extends('layouts.admin')

@section('title', 'Detail Kuis')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sub_modules.show', $quiz->sub_module_id) }}">Sub Modul</a></li>
    <li class="breadcrumb-item active">Detail Kuis</li>
@endsection

@section('header-actions')
    <div class="btn-group">
        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit mr-1"></i> Ubah</a>
        <a href="{{ route('admin.questions.index', $quiz) }}" class="btn btn-info btn-sm"><i class="fas fa-question mr-1"></i> Kelola Pertanyaan</a>
        <a href="{{ route('admin.quizzes.results', $quiz) }}" class="btn btn-secondary btn-sm"><i class="fas fa-list mr-1"></i> Hasil</a>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h5 class="mb-1">{{ $quiz->judul }}</h5>
            <p class="mb-0 text-muted">Nilai Minimum: {{ $quiz->nilai_minimum }} | Maks. Percobaan: {{ $quiz->max_attempts }}</p>
            <p class="mt-2 mb-0">{!! nl2br(e($quiz->deskripsi)) !!}</p>
        </div>
    </div>
@endsection


