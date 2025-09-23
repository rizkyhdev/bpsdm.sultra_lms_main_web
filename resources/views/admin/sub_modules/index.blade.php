@extends('layouts.admin')

@section('title', 'Sub Modul')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.modules.show', $module->id) }}">Modul</a></li>
    <li class="breadcrumb-item active">Sub Modul</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.sub_modules.create', ['module' => $module->id]) }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Tambah Sub Modul</a>
@endsection

@section('content')
    {{-- Header konteks modul --}}
    <div class="alert alert-light border">
        <strong>Modul:</strong> {{ $module->judul }}
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                <tr>
                    <th>Urutan</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($subModules as $s)
                    <tr>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <form action="{{ route('admin.sub_modules.reorder', $s) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="direction" value="up">
                                    <button class="btn btn-outline-secondary" title="Naik"><i class="fas fa-arrow-up"></i></button>
                                </form>
                                <form action="{{ route('admin.sub_modules.reorder', $s) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="direction" value="down">
                                    <button class="btn btn-outline-secondary" title="Turun"><i class="fas fa-arrow-down"></i></button>
                                </form>
                            </div>
                            <span class="ml-2">{{ $s->urutan }}</span>
                        </td>
                        <td><a href="{{ route('admin.sub_modules.show', $s) }}">{{ $s->judul }}</a></td>
                        <td>{{ \Illuminate\Support\Str::limit($s->deskripsi, 100) }}</td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.sub_modules.show', $s) }}" class="btn btn-outline-secondary"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.sub_modules.edit', $s) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-outline-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-action="{{ route('admin.sub_modules.destroy', $s) }}"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">
            @include('partials._pagination', ['collection' => $subModules])
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
                    <form id="deleteFormSubModule" method="POST" action="#">
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
    $('#deleteFormSubModule').attr('action', action);
});
</script>
@endsection


