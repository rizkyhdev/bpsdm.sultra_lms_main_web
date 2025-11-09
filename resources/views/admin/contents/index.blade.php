@extends('layouts.admin')

@section('title', 'Konten Sub Modul')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sub_modules.show', $subModule->id) }}">Sub Modul</a></li>
    <li class="breadcrumb-item active">Konten</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.contents.create', ['sub_module' => $subModule->id]) }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Tambah Konten</a>
@endsection

@section('content')
    {{-- Header konteks sub modul --}}
    <div class="alert alert-light border">
        <strong>Sub Modul:</strong> {{ $subModule->judul }}
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.contents.index', ['sub_module' => $subModule->id]) }}" class="mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4">
                        <label for="tipe">Tipe</label>
                        <select id="tipe" name="tipe" class="form-control">
                            <option value="">Semua</option>
                            @foreach(['text','pdf','video','audio','image'] as $t)
                                <option value="{{ $t }}" @if(request('tipe')===$t) selected @endif>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="per_page">Per Halaman</label>
                        <select id="per_page" name="per_page" class="form-control">
                            @foreach([10,25,50,100] as $size)
                                <option value="{{ $size }}" @if(request('per_page', 10)==$size) selected @endif>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <button type="submit" class="btn btn-outline-secondary btn-block"><i class="fas fa-filter"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                <tr>
                    <th>Urutan</th>
                    <th>Judul</th>
                    <th>Tipe</th>
                    <th>File</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($contents as $c)
                    <tr>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <form action="{{ route('admin.contents.reorder', $c) }}" method="POST" class="d-inline">@csrf<input type="hidden" name="direction" value="up"><button class="btn btn-outline-secondary" title="Naik"><i class="fas fa-arrow-up"></i></button></form>
                                <form action="{{ route('admin.contents.reorder', $c) }}" method="POST" class="d-inline">@csrf<input type="hidden" name="direction" value="down"><button class="btn btn-outline-secondary" title="Turun"><i class="fas fa-arrow-down"></i></button></form>
                            </div>
                            <span class="ml-2">{{ $c->urutan }}</span>
                        </td>
                        <td><a href="{{ route('admin.contents.show', $c) }}">{{ $c->judul }}</a></td>
                        <td>{{ $c->tipe }}</td>
                        <td>
                            @if($c->file_path)
                                <a href="{{ Storage::url($c->file_path) }}" target="_blank">Unduh</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.contents.show', $c) }}" class="btn btn-outline-secondary"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.contents.create', ['sub_module' => $subModule->id]) }}" class="btn btn-outline-success" title="Tambah"><i class="fas fa-plus"></i></a>
                                <a href="{{ route('admin.contents.edit', $c) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-outline-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-action="{{ route('admin.contents.destroy', $c) }}"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">
            @include('partials._pagination', ['collection' => $contents])
        </div>
    </div>

    {{-- Modal konfirmasi hapus --}}
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Apakah Anda yakin ingin menghapus data ini?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="deleteFormContentIndex" method="POST" action="#">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$('#confirmDeleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var action = button.data('action');
    $('#deleteFormContentIndex').attr('action', action);
});
</script>
@endsection


