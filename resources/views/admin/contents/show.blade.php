@extends('layouts.admin')

@section('title', 'Detail Konten')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sub_modules.show', $content->sub_module_id) }}">Sub Modul</a></li>
    <li class="breadcrumb-item active">Detail Konten</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.contents.edit', $content) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit mr-1"></i> Ubah</a>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-1">{{ $content->judul }}</h5>
            <p class="mb-0 text-muted">Tipe: {{ $content->tipe }} | Urutan: {{ $content->urutan }}</p>
            <hr>
            @if($content->file_path)
                @if(in_array($content->tipe, ['image']))
                    <img src="{{ Storage::url($content->file_path) }}" alt="{{ $content->judul }}" class="img-fluid">
                @elseif(in_array($content->tipe, ['pdf']))
                    {{-- PDF Viewer (pdf.js) --}}
                    @php
                        $pdfUrl = Storage::url($content->file_path);
                    @endphp
                    @include('partials.pdf-viewer', [
                        'pdfUrl' => $pdfUrl,
                        'downloadUrl' => $pdfUrl,
                        'title' => $content->judul,
                    ])
                @elseif(in_array($content->tipe, ['video']))
                    <video controls class="w-100" src="{{ Storage::url($content->file_path) }}"></video>
                @elseif(in_array($content->tipe, ['audio']))
                    <audio controls class="w-100" src="{{ Storage::url($content->file_path) }}"></audio>
                @else
                    <a href="{{ Storage::url($content->file_path) }}" target="_blank" class="btn btn-outline-primary"><i class="fas fa-download mr-1"></i> Unduh</a>
                @endif
            @else
                <p class="text-muted mb-0">Tidak ada file untuk konten ini.</p>
            @endif
        </div>
    </div>
@endsection


