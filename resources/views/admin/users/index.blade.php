@extends('layouts.admin')

@section('title', 'Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pengguna</li>
@endsection

@section('header-actions')
    @can('create', App\Models\User::class)
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Tambah</a>
    @endcan
@endsection

@section('content')
    {{-- Pencarian & Filter --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-3">
                        <label for="q">Cari (NIP/Nama/Email)</label>
                        <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Ketik kata kunci">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="role">Peran</label>
                        <select id="role" name="role" class="form-control">
                            <option value="">Semua</option>
                            <option value="admin" @if(request('role')=='admin') selected @endif>Admin</option>
                            <option value="instructor" @if(request('role')=='instructor') selected @endif>Instruktur</option>
                            <option value="student" @if(request('role')=='student') selected @endif>Siswa</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="validated">Validasi</label>
                        <select id="validated" name="validated" class="form-control">
                            <option value="">Semua</option>
                            <option value="1" @if(request('validated')==='1') selected @endif>Tervalidasi</option>
                            <option value="0" @if(request('validated')==='0') selected @endif>Belum</option>
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
                    <div class="form-group col-md-1">
                        <button type="submit" class="btn btn-outline-secondary btn-block"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Tabel data --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th><a href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'nip'])) }}">NIP</a></th>
                        <th><a href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'nama'])) }}">Nama</a></th>
                        <th><a href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'email'])) }}">Email</a></th>
                        <th>Peran</th>
                        <th>Validasi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->nip }}</td>
                        <td>{{ $user->nama }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge badge-info text-uppercase">{{ $user->role }}</span></td>
                        <td>
                            @if($user->is_validated)
                                <span class="badge badge-success">Ya</span>
                            @else
                                <span class="badge badge-secondary">Tidak</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm" role="group">
                                @can('view', $user)
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary" title="Detail"><i class="fas fa-eye"></i></a>
                                @endcan
                                @can('update', $user)
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary" title="Ubah"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('delete', $user)
                                    <button class="btn btn-outline-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-action="{{ route('admin.users.destroy', $user) }}" title="Hapus"><i class="fas fa-trash"></i></button>
                                @endcan
                                @can('validateUser', $user)
                                    @if(!$user->is_validated)
                                        <form action="{{ route('admin.users.validate', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-outline-success" title="Validasi"><i class="fas fa-check"></i></button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body">
            @include('partials._pagination', ['collection' => $users])
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
                    <form id="deleteForm" method="POST" action="#">
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
// Isi action form hapus dari tombol yang ditekan
$('#confirmDeleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var action = button.data('action');
    $('#deleteForm').attr('action', action);
});
</script>
@endsection


