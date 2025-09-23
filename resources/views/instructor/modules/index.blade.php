@extends('layouts.instructor')

@section('title','Modules')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.show', $course) }}">{{ $course->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Modules</li>
  </ol>
  {{-- Binding: $course, $modules --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <a href="{{ route('instructor.courses.index') }}" class="btn btn-light btn-sm">&larr; Kembali ke Courses</a>
    </div>
    @can('create', [App\Models\Module::class, $course])
      <a href="{{ route('instructor.courses.modules.create', $course) }}" class="btn btn-primary btn-sm">Tambah Module</a>
    @endcan
  </div>

  <div class="card mb-3">
    <div class="card-header">Reorder</div>
    <div class="card-body">
      {{-- Komentar: UI drag&drop bisa ditambahkan. Di sini form sederhana untuk kirim JSON urutan. --}}
      <form action="{{ route('instructor.modules.reorder', $course) }}" method="post">
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
          @forelse($modules as $m)
            <tr>
              <td>{{ $m->urutan }}</td>
              <td>{{ $m->judul }}</td>
              <td class="text-truncate" style="max-width: 420px;">{{ $m->deskripsi }}</td>
              <td class="text-right">
                <a href="{{ route('instructor.modules.show', $m) }}" class="btn btn-sm btn-outline-primary">Show</a>
                @can('update', $m)
                  <a href="{{ route('instructor.modules.edit', $m) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endcan
                @can('delete', $m)
                  <form action="{{ route('instructor.modules.destroy', $m) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus module ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center">Belum ada module</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection


