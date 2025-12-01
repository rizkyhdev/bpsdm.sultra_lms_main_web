@extends('layouts.instructor')

@section('title','Sub-Modules')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $module->course->id) }}">{{ $module->course->judul }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.modules.show', $module) }}">{{ $module->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sub-Modules</li>
  </ol>
  {{-- Binding: $module, $subModules --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    @can('create', [App\Models\SubModule::class, $module])
      <button type="button" class="btn btn-primary btn-sm" onclick="openSubModuleModal({{ $module->id }})">
        <i class="bi bi-plus-circle"></i> Tambah Sub-Module
      </button>
    @endcan
  </div>

  <div class="card mb-3">
    <div class="card-header">Reorder</div>
    <div class="card-body">
      <form action="{{ route('instructor.sub_modules.reorder') }}" method="post">
        @csrf
        <div class="form-group">
          <label>JSON Payload</label>
          <textarea name="items" class="form-control" rows="4" placeholder='{"items":[{"id":1,"urutan":1}]}'></textarea>
        </div>
        <button type="submit" class="btn btn-outline-secondary">Simpan Urutan</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead><tr><th>Urutan</th><th>Judul</th><th>Deskripsi</th><th class="text-right">Aksi</th></tr></thead>
        <tbody>
          @forelse($subModules as $sm)
            <tr>
              <td>{{ $sm->urutan }}</td>
              <td>{{ $sm->judul }}</td>
              <td class="text-truncate" style="max-width: 420px;">{{ $sm->deskripsi }}</td>
              <td class="text-right">
                <div class="btn-group btn-group-sm" role="group">
                  <a href="{{ route('instructor.sub_modules.show', $sm->id) }}" class="btn btn-outline-primary" title="View">
                    <i class="bi bi-eye"></i>
                  </a>
                  @can('update', $sm)
                    <button type="button" class="btn btn-outline-secondary" onclick="openSubModuleModal({{ $module->id }}, {{ $sm->id }})" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </button>
                  @endcan
                  @can('delete', $sm)
                    <button type="button" class="btn btn-outline-danger" onclick="openSubModuleDeleteModal({{ $sm->id }})" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  @endcan
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center">Belum ada sub-module</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@include('partials.modals.submodule-modal')
<script src="{{ asset('js/modal-operations.js') }}"></script>
@endsection


