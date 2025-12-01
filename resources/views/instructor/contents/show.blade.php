@extends('layouts.instructor')

@section('title', $content->judul)

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.modules.show', $content->subModule->module) }}">{{ $content->subModule->module->judul }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.sub_modules.show', $content->subModule) }}">{{ $content->subModule->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $content->judul }}</li>
  </ol>
  {{-- Binding: $content --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error!</strong> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
    <h4 class="mb-0">{{ $content->judul }}</h4>
      <small class="text-muted">{{ ucfirst($content->tipe) }} Content</small>
    </div>
    <div>
      <a href="{{ route('instructor.contents.index', $content->subModule->id) }}" class="btn btn-light btn-sm me-2">
        <i class="bi bi-arrow-left"></i> Back to Contents
      </a>
      @can('update', $content)
        <a href="{{ route('instructor.contents.edit', $content->id) }}" class="btn btn-outline-secondary btn-sm me-2">
          <i class="bi bi-pencil"></i> Edit
        </a>
      @endcan
      @can('delete', $content)
        <form action="{{ route('instructor.contents.destroy', $content->id) }}" method="POST" class="d-inline" 
              onsubmit="return confirm('Are you sure you want to delete this content?\n\nThis action cannot be undone!');">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-trash"></i> Delete
          </button>
        </form>
      @endcan
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Detail Konten</h5>
    </div>
    <div class="card-body">
      <dl class="row mb-4">
        <dt class="col-sm-3">Judul</dt>
        <dd class="col-sm-9">{{ $content->judul }}</dd>
        <dt class="col-sm-3">Tipe</dt>
        <dd class="col-sm-9">
          <span class="badge badge-secondary">{{ ucfirst($content->tipe) }}</span>
        </dd>
        <dt class="col-sm-3">Urutan</dt>
        <dd class="col-sm-9">{{ $content->urutan }}</dd>
        @if($content->file_path)
          <dt class="col-sm-3">File</dt>
          <dd class="col-sm-9">
            <a href="{{ route('instructor.contents.download', $content->id) }}" class="btn btn-sm btn-outline-primary">Download File</a>
          </dd>
        @endif
      </dl>

      <hr>

      {{-- Display content based on type --}}
      @if($content->youtube_url)
        {{-- YouTube Video --}}
        <div class="mb-4">
          <h5 class="mb-3">Video YouTube</h5>
          <div class="embed-responsive embed-responsive-16by9" style="max-width: 800px;">
            <iframe 
              class="embed-responsive-item" 
              src="{{ $content->youtube_embed_url }}" 
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
              allowfullscreen
              style="width: 100%; height: 450px; border: 0;">
            </iframe>
          </div>
          <div class="mt-2">
            <a href="{{ $content->youtube_url }}" target="_blank" class="text-muted small">Buka di YouTube</a>
          </div>
        </div>
      @elseif(in_array($content->tipe, ['text', 'html']) && $content->html_content)
        {{-- Text/HTML Content with Editor --}}
        <div class="mb-4">
          <h5 class="mb-3">Konten</h5>
          <div id="htmlContentEditor" class="border rounded p-4" style="min-height: 400px; background-color: #f8f9fa; font-family: Arial, sans-serif;">
            {!! $content->html_content !!}
          </div>
        </div>
      @elseif($content->external_url)
        {{-- External URL/Link (with special handling for Google Drive PDF) --}}
        <div class="mb-4">
          <h5 class="mb-3">Link Eksternal</h5>
          @php
            $externalUrl = $content->external_url;
          @endphp

          @if($externalUrl && \Illuminate\Support\Str::contains($externalUrl, 'drive.google.com'))
            @include('partials.google-drive-pdf-viewer', [
                'driveUrl' => $externalUrl,
                'title' => $content->judul,
            ])
          @else
            <div class="alert alert-info">
              <p><strong>URL:</strong> <a href="{{ $externalUrl }}" target="_blank">{{ $externalUrl }}</a></p>
              <a href="{{ $externalUrl }}" target="_blank" class="btn btn-primary">Buka Link</a>
            </div>
          @endif
        </div>
      @elseif($content->file_path)
        {{-- File Content --}}
        <div class="mb-4">
          <h5 class="mb-3">File Konten</h5>
          @if($content->tipe === 'image')
            <div>
              <img src="{{ Storage::url($content->file_path) }}" alt="{{ $content->judul }}" class="img-fluid" style="max-width: 100%; height: auto;">
            </div>
          @elseif($content->tipe === 'video')
            <div>
              <video controls style="max-width: 100%; height: auto;">
                <source src="{{ Storage::url($content->file_path) }}" type="video/mp4">
                Browser Anda tidak mendukung tag video.
              </video>
            </div>
          @elseif($content->tipe === 'audio')
            <div>
              <audio controls style="width: 100%;">
                <source src="{{ Storage::url($content->file_path) }}" type="audio/mpeg">
                Browser Anda tidak mendukung tag audio.
              </audio>
            </div>
          @elseif($content->tipe === 'pdf')
            <div>
              <iframe src="{{ Storage::url($content->file_path) }}" style="width: 100%; height: 600px; border: 1px solid #ddd;"></iframe>
            </div>
          @else
            <div class="alert alert-info">
              <p>File tersedia untuk diunduh.</p>
              <a href="{{ route('instructor.contents.download', $content->id) }}" class="btn btn-primary">Download File</a>
            </div>
          @endif
        </div>
      @else
        {{-- No content available --}}
        <div class="alert alert-warning">
          <p>Tidak ada konten yang tersedia untuk ditampilkan.</p>
        </div>
      @endif
    </div>
  </div>
</div>

@if(in_array($content->tipe, ['text', 'html']) && $content->html_content)
  {{-- Simple HTML content display with editor-like styling --}}
  <style>
    #htmlContentEditor {
      line-height: 1.6;
      color: #333;
    }
    #htmlContentEditor h1, #htmlContentEditor h2, #htmlContentEditor h3 {
      margin-top: 1em;
      margin-bottom: 0.5em;
    }
    #htmlContentEditor p {
      margin-bottom: 1em;
    }
    #htmlContentEditor ul, #htmlContentEditor ol {
      margin-left: 2em;
      margin-bottom: 1em;
    }
  </style>
@endif
@endsection


