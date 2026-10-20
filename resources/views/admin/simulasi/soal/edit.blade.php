@extends('layouts.admin')

@section('title', 'Edit Soal')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.simulasi.index') }}">Simulasi Uji Kompetensi</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.simulasi.soal.index') }}">Bank Soal</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Soal</li>
@endsection

@section('admin_content')
    @include('admin.simulasi._nav')

    @if ($question->answers_count > 0)
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
            Soal ini sudah dijawab {{ $question->answers_count }} kali. Perubahan kunci jawaban hanya berlaku untuk jawaban berikutnya;
            skor simulasi yang sudah tercatat tidak berubah.
        </div>
    @endif

    <form action="{{ route('admin.simulasi.soal.update', $question) }}" method="POST">
        @method('PUT')
        @include('admin.simulasi.soal._form', ['submitLabel' => 'Simpan Perubahan'])
    </form>
@endsection
