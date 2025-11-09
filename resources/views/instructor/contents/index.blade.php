@extends('layouts.instructor')

@section('title','Contents')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.modules.show', $subModule->module) }}">{{ $subModule->module->judul }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.sub_modules.show', $subModule) }}">{{ $subModule->judul }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Contents</li>
  </ol>
  {{-- Binding: $subModule, $contents, $filters --}}
</nav>
@endsection

@section('content')
<div class="container-fluid">
  <div class="card mb-3">
    <div class="card-body">
      <form class="form-inline" method="get" action="{{ route('instructor.contents.index', $subModule->id) }}">
        <div class="form-group mr-2 mb-2">
          <select name="tipe" class="form-control" onchange="this.form.submit()">
            <option value="">- Semua Tipe -</option>
            @foreach(['text','pdf','video','audio','image'] as $t)
              <option value="{{ $t }}" {{ ($filters['tipe'] ?? '')==$t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group mr-2 mb-2">
          <select name="per_page" class="form-control" onchange="this.form.submit()">
            @foreach([10,25,50] as $pp)
              <option value="{{ $pp }}" {{ (request('per_page', 10)==$pp) ? 'selected' : '' }}>{{ $pp }}/hal</option>
            @endforeach
          </select>
        </div>
        @can('create', [App\Models\Content::class, $subModule])
          <a href="{{ route('instructor.contents.create', $subModule->id) }}" class="btn btn-success mb-2 ml-auto">Tambah Content</a>
        @endcan
      </form>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header">Reorder</div>
    <div class="card-body">
      <form action="{{ route('instructor.contents.reorder') }}" method="post">
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
        <thead><tr><th>Urutan</th><th>Judul</th><th>Tipe</th><th>File</th><th class="text-right">Aksi</th></tr></thead>
        <tbody>
          @forelse($contents as $c)
            <tr>
              <td>{{ $c->urutan }}</td>
              <td>{{ $c->judul }}</td>
              <td>{{ $c->tipe }}</td>
              <td>
                @if($c->file_path)
                  <a href="{{ route('instructor.contents.download', $c) }}">Download</a>
                @endif
              </td>
              <td class="text-right">
                  <a href="{{ route('instructor.contents.show', $c->id) }}" class="btn btn-sm btn-outline-primary">Show</a>
                @can('create', [App\Models\Content::class, $subModule])
                  <a href="{{ route('instructor.contents.create', $subModule->id) }}" class="btn btn-sm btn-outline-success">Tambah</a>
                @endcan
                @can('update', $c)
                  <a href="{{ route('instructor.contents.edit', $c->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                @endcan
                @can('delete', $c)
                  <form action="{{ route('instructor.contents.destroy', $c->id) }}" method="post" class="d-inline" onsubmit="return confirm('Hapus konten ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                  </form>
                @endcan
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center">Belum ada konten</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection


