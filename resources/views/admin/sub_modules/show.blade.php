@extends('layouts.admin')

@section('title', 'Detail Sub Modul')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.modules.show', $subModule->module_id) }}">Modul</a></li>
    <li class="breadcrumb-item active">Detail Sub Modul</li>
@endsection

@section('header-actions')
    <div class="btn-group">
        <a href="{{ route('admin.sub_modules.edit', $subModule) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit mr-1"></i> Ubah</a>
        <a href="{{ route('admin.contents.create', ['sub_module' => $subModule->id]) }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> Konten</a>
        <a href="{{ route('admin.quizzes.create', ['sub_module' => $subModule->id]) }}" class="btn btn-info btn-sm"><i class="fas fa-plus mr-1"></i> Kuis</a>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h5 class="mb-1">{{ $subModule->judul }}</h5>
            <p class="mb-0 text-muted">Urutan: {{ $subModule->urutan }}</p>
            <p class="mt-2 mb-0">{!! nl2br(e($subModule->deskripsi)) !!}</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" id="subModuleTabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" id="contents-tab" data-toggle="tab" href="#contents" role="tab">Konten</a></li>
                <li class="nav-item"><a class="nav-link" id="quizzes-tab" data-toggle="tab" href="#quizzes" role="tab">Kuis</a></li>
                <li class="nav-item"><a class="nav-link" id="progress-tab" data-toggle="tab" href="#progress" role="tab">Progres</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="subModuleTabsContent">
                <div class="tab-pane fade show active" id="contents" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
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
                            @forelse(($contents ?? []) as $c)
                                <tr>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <form action="{{ route('admin.contents.reorder', $c) }}" method="POST" class="d-inline">@csrf<input type="hidden" name="direction" value="up"><button class="btn btn-outline-secondary"><i class="fas fa-arrow-up"></i></button></form>
                                            <form action="{{ route('admin.contents.reorder', $c) }}" method="POST" class="d-inline">@csrf<input type="hidden" name="direction" value="down"><button class="btn btn-outline-secondary"><i class="fas fa-arrow-down"></i></button></form>
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
                                            <a href="{{ route('admin.contents.edit', $c) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                            <button class="btn btn-outline-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-action="{{ route('admin.contents.destroy', $c) }}"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Belum ada konten</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="quizzes" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
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
                            @forelse(($quizzes ?? []) as $q)
                                <tr>
                                    <td><a href="{{ route('admin.quizzes.show', $q) }}">{{ $q->judul }}</a></td>
                                    <td>{{ $q->nilai_minimum }}</td>
                                    <td>{{ $q->max_attempts }}</td>
                                    <td>{{ $q->questions_count ?? 0 }}</td>
                                    <td class="text-right">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.quizzes.show', $q) }}" class="btn btn-outline-secondary"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('admin.quizzes.edit', $q) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Belum ada kuis</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="progress" role="tabpanel">
                    {{-- Snapshot progres sederhana --}}
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between"><span>Total Peserta</span><span>{{ $progressSnapshot['participants'] ?? 0 }}</span></li>
                        <li class="d-flex justify-content-between"><span>Sudah Selesai</span><span>{{ $progressSnapshot['completed'] ?? 0 }}</span></li>
                        <li class="d-flex justify-content-between"><span>Completion Rate</span><span>{{ $progressSnapshot['completion_rate'] ?? 0 }}%</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal konfirmasi hapus konten --}}
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
                    <form id="deleteFormContent" method="POST" action="#">
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
    $('#deleteFormContent').attr('action', action);
});
</script>
@endsection


