@extends('layouts.admin')

@section('title', 'Sertifikat')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Sertifikat</li>
@endsection

@section('header-actions')
    <div class="btn-group">
        <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Tambah</a>
        <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#bulkGenerateModal"><i class="fas fa-cogs mr-1"></i> Generate Massal</button>
        <a href="{{ route('admin.certificates.export') }}" class="btn btn-outline-info"><i class="fas fa-file-export mr-1"></i> Ekspor</a>
    </div>
@endsection

@section('content')
    {{-- Pencarian --}}
    <form method="GET" action="{{ route('admin.certificates.index') }}" class="mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4">
                        <label for="q">Nomor/Nama/Email/Kursus</label>
                        <input type="text" id="q" name="q" class="form-control" value="{{ request('q') }}" placeholder="Cari...">
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
                        <button class="btn btn-outline-secondary btn-block"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="form-group col-md-4 text-right">
                        {{-- Form verifikasi nomor sertifikat --}}
                        <form action="{{ route('admin.certificates.verify') }}" method="GET" class="form-inline justify-content-end">
                            <input type="text" name="nomor_sertifikat" class="form-control mr-2" placeholder="Verifikasi nomor" required>
                            <button class="btn btn-outline-success"><i class="fas fa-check"></i></button>
                        </form>
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
                    <th>Nomor</th>
                    <th>Pengguna</th>
                    <th>Kursus</th>
                    <th>Tanggal</th>
                    <th>File</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($certificates as $c)
                    <tr>
                        <td><a href="{{ route('admin.certificates.show', $c) }}">{{ $c->nomor_sertifikat }}</a></td>
                        <td>{{ $c->user->nama ?? '-' }}</td>
                        <td>{{ $c->course->judul ?? '-' }}</td>
                        <td>{{ optional($c->issue_date)->format('d/m/Y') }}</td>
                        <td>
                            @if($c->file_path)
                                <a href="{{ Storage::url($c->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i></a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.certificates.show', $c) }}" class="btn btn-outline-secondary"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.certificates.edit', $c) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-outline-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-action="{{ route('admin.certificates.destroy', $c) }}"><i class="fas fa-trash"></i></button>
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
            @include('partials._pagination', ['collection' => $certificates])
        </div>
    </div>

    {{-- Modal Generate Massal --}}
    <div class="modal fade" id="bulkGenerateModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Sertifikat Massal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.certificates.bulk_generate') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="course_id_bulk">Kursus</label>
                            <select id="course_id_bulk" name="course_id" class="form-control" required>
                                @foreach(($coursesFilter ?? []) as $c)
                                    <option value="{{ $c->id }}">{{ $c->judul }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="issue_date_bulk">Tanggal Terbit</label>
                            <input type="date" id="issue_date_bulk" name="issue_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Proses</button>
                    </div>
                </form>
            </div>
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
                    <form id="deleteFormCertificate" method="POST" action="#">
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
    $('#deleteFormCertificate').attr('action', action);
});
</script>
@endsection


