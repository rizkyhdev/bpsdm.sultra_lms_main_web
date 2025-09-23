@extends('layouts.admin')

@section('title', 'Kuis')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.sub_modules.show', $subModule->id) }}">Sub Modul</a></li>
    <li class="breadcrumb-item active">Kuis</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.quizzes.create', ['sub_module' => $subModule->id]) }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Tambah Kuis</a>
@endsection

@section('content')
    <div class="alert alert-light border"><strong>Sub Modul:</strong> {{ $subModule->judul }}</div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                <tr>
                    <th>Judul</th>
                    <th>Nilai Minimum</th>
                    <th>Maks. Percobaan</th>
                    <th>Pertanyaan</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($quizzes as $q)
                    <tr>
                        <td><a href="{{ route('admin.quizzes.show', $q) }}">{{ $q->judul }}</a></td>
                        <td>{{ $q->nilai_minimum }}</td>
                        <td>{{ $q->max_attempts }}</td>
                        <td>{{ $q->questions_count ?? 0 }}</td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.quizzes.show', $q) }}" class="btn btn-outline-secondary"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.quizzes.edit', $q) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-outline-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-action="{{ route('admin.quizzes.destroy', $q) }}"><i class="fas fa-trash"></i></button>
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
            @include('partials._pagination', ['collection' => $quizzes])
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
                    <form id="deleteFormQuizIndex" method="POST" action="#">
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
    $('#deleteFormQuizIndex').attr('action', action);
});
</script>
@endsection


