@extends('layouts.admin')

@section('title', 'Semua Kuis')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Semua Kuis</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Semua Kuis</h5>
            <small>Daftar semua quiz dalam sistem</small>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th>Judul</th>
                        <th>Level</th>
                        <th>Course/Module/Sub-Module</th>
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
                            <td>
                                @if($q->sub_module_id)
                                    <span class="badge bg-info">Sub-Module</span>
                                @elseif($q->module_id)
                                    <span class="badge bg-warning">Module</span>
                                @elseif($q->course_id)
                                    <span class="badge bg-success">Course</span>
                                @endif
                            </td>
                            <td>
                                @if($q->sub_module_id)
                                    <small>{{ $q->subModule->judul ?? 'N/A' }}</small><br>
                                    <small class="text-muted">Module: {{ $q->subModule->module->judul ?? 'N/A' }}</small><br>
                                    <small class="text-muted">Course: {{ $q->subModule->module->course->judul ?? 'N/A' }}</small>
                                @elseif($q->module_id)
                                    <small>{{ $q->module->judul ?? 'N/A' }}</small><br>
                                    <small class="text-muted">Course: {{ $q->module->course->judul ?? 'N/A' }}</small>
                                @elseif($q->course_id)
                                    <small>{{ $q->course->judul ?? 'N/A' }}</small>
                                @endif
                            </td>
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
                        <tr><td colspan="7" class="text-center text-muted">Tidak ada data</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($quizzes->hasPages())
                <div class="mt-3">
                    {{ $quizzes->links() }}
                </div>
            @endif
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

