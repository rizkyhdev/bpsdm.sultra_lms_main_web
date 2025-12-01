@extends('layouts.admin')

@section('title', 'Kursus')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Kursus</li>
@endsection

@section('header-actions')
    @can('create', App\Models\Course::class)
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Tambah</a>
    @endcan
@endsection

@section('content')
    {{-- Pencarian & Filter --}}
    <form method="GET" action="{{ route('admin.courses.index') }}" class="mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4">
                        <label for="q">Cari Judul</label>
                        <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Ketik judul">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="bidang_kompetensi">Bidang Kompetensi</label>
                        <input type="text" id="bidang_kompetensi" name="bidang_kompetensi" value="{{ request('bidang_kompetensi') }}" class="form-control" placeholder="Contoh: Manajerial">
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
                        <th><a href="?{{ http_build_query(array_merge(request()->all(), ['sort' => 'judul'])) }}">Judul</a></th>
                        <th>JP</th>
                        <th>Bidang</th>
                        <th>Pendaftar</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($courses as $course)
                    <tr>
                        <td><a href="{{ route('admin.courses.show', $course->id) }}">{{ $course->judul }}</a></td>
                        <td>{{ $course->jp_value }}</td>
                        <td>{{ $course->bidang_kompetensi }}</td>
                        <td>{{ $course->user_enrollments_count ?? 0 }}</td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.courses.show', $course->id) }}" class="btn btn-outline-secondary"><i class="fas fa-eye"></i></a>
                                @can('update', $course)
                                <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('delete', $course)
                                <button class="btn btn-outline-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-action="{{ route('admin.courses.destroy', $course->id) }}"><i class="fas fa-trash"></i></button>
                                @endcan
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
            @include('partials._pagination', ['collection' => $courses])
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
                    <form id="deleteFormCourse" method="POST" action="#">
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
    $('#deleteFormCourse').attr('action', action);
});
</script>
@endsection


