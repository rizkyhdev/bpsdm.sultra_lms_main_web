@extends('layouts.instructor')

@section('title','Sub-Modules')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $module->course) }}">{{ $module->course->judul }}</a></li>
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
      <a href="{{ route('instructor.sub_modules.create', $module->id) }}" class="btn btn-primary btn-sm">Tambah Sub-Module</a>
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
                <a href="{{ route('instructor.sub_modules.show', $sm->id) }}" class="btn btn-sm btn-outline-primary">Show</a>
                @can('create', [App\Models\SubModule::class, $module])
                  <a href="{{ route('instructor.sub_modules.create', $module->id) }}" class="btn btn-sm btn-outline-success">Tambah</a>
                @endcan
                @can('update', $sm)
                  <a href="{{ route('instructor.sub_modules.edit', $sm->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endcan
                @can('delete', $sm)
                  <form action="{{ route('instructor.sub_modules.destroy', $sm->id) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus sub-module ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                @endcan
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
@endsection


