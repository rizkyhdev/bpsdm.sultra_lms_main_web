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
  <div class="card">
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Judul</dt>
        <dd class="col-sm-9">{{ $content->judul }}</dd>
        <dt class="col-sm-3">Tipe</dt>
        <dd class="col-sm-9">{{ $content->tipe }}</dd>
        <dt class="col-sm-3">Urutan</dt>
        <dd class="col-sm-9">{{ $content->urutan }}</dd>
        <dt class="col-sm-3">File</dt>
        <dd class="col-sm-9">
          @if($content->file_path)
            <a href="{{ route('instructor.contents.download', $content->id) }}">Download</a>
          @else
            <span class="text-muted">-</span>
          @endif
        </dd>
      </dl>
      @if($content->tipe === 'text')
        <hr>
        <div>
          {!! nl2br(e($content->teks)) !!}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection


