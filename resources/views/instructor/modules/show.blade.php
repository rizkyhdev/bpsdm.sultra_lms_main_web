@extends('layouts.instructor')

@section('title', $module->judul)

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $module->course) }}">{{ $module->course->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $module->judul }}</li>
  </ol>
  {{-- Binding: $module, $subModules --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <div class="text-muted small">Sub-Modules: {{ $subModules->count() }}</div>
    </div>
    @can('create', [App\Models\SubModule::class, $module])
      <a href="{{ route('instructor.sub_modules.create', $module->id) }}" class="btn btn-primary btn-sm">Tambah Sub-Module</a>
    @endcan
  </div>

  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#subs" role="tab">Sub-Modules</a></li>
  </ul>
  <div class="tab-content p-3 border border-top-0">
    <div class="tab-pane fade show active" id="subs" role="tabpanel">
      <div class="list-group">
        @forelse($subModules as $sm)
          <a class="list-group-item list-group-item-action d-flex justify-content-between" href="{{ route('instructor.sub_modules.show', $sm->id) }}">
            <span>{{ $sm->urutan }}. {{ $sm->judul }}</span>
            <span class="text-muted small">Kelola</span>
          </a>
        @empty
          <div class="text-muted">Belum ada sub-module.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection


