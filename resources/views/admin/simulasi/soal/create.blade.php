@extends('layouts.admin')

@section('title', 'Tambah Soal')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.simulasi.index') }}">Simulasi Uji Kompetensi</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.simulasi.soal.index') }}">Bank Soal</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah Soal</li>
@endsection

@section('admin_content')
    @include('admin.simulasi._nav')

    <form action="{{ route('admin.simulasi.soal.store') }}" method="POST">
        @include('admin.simulasi.soal._form', ['submitLabel' => 'Simpan Soal'])
    </form>
@endsection
