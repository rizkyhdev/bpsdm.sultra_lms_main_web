@extends('layouts.admin')

@section('title', 'Pendaftaran')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Pendaftaran</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.enrollments.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Tambah</a>
@endsection

@section('content')
    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.enrollments.index') }}" class="mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-3">
                        <label for="search">Cari Pengguna / Kursus</label>
                        <input type="text" id="search" name="search" class="form-control"
                               value="{{ request('search') }}" placeholder="Ketik nama, NIP, atau judul kursus">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="course_id">Kursus</label>
                        <select id="course_id" name="course_id" class="form-control">
                            <option value="all">Semua</option>
                            @foreach(($courses ?? []) as $c)
                                <option value="{{ $c->id }}" @if(request('course_id','all')==$c->id) selected @endif>{{ $c->judul }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="all">Semua</option>
                            @php
                                $statusLabels = [
                                    'not_started' => 'Belum Mulai',
                                    'in_progress' => 'Sedang Berjalan',
                                    'completed' => 'Selesai',
                                    'dropped' => 'Dibatalkan / Drop',
                                ];
                            @endphp
                            @foreach(($statuses ?? []) as $val)
                                <option value="{{ $val }}" @if(request('status','all')===$val) selected @endif>
                                    {{ $statusLabels[$val] ?? ucfirst(str_replace('_',' ',$val)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="date_from">Dari</label>
                        <input type="date" id="date_from" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="date_to">Sampai</label>
                        <input type="date" id="date_to" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="per_page">Per Halaman</label>
                        <select id="per_page" name="per_page" class="form-control">
                            @foreach([10,25,50,100] as $size)
                                <option value="{{ $size }}" @if(request('per_page', 10)==$size) selected @endif>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="text-right">
                    <button class="btn btn-outline-secondary"><i class="fas fa-filter mr-1"></i> Terapkan</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                <tr>
                    <th>Pengguna</th>
                    <th>Kursus</th>
                    <th>Tgl Daftar</th>
                    <th>Status</th>
                    <th>Selesai</th>
                    <th class="text-right">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($enrollments as $en)
                    <tr>
                        <td>
                            @if($en->user)
                                {{ $en->user->nip ?? '-' }} - {{ $en->user->name ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($en->course_id)
                                <a href="{{ route('admin.courses.show', $en->course_id) }}">{{ $en->course->judul ?? '-' }}</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ optional($en->enrollment_date)->format('d/m/Y') }}</td>
                        <td>{{ $en->status }}</td>
                        <td>{{ optional($en->completed_at)->format('d/m/Y') ?? '-' }}</td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.enrollments.show', $en) }}" class="btn btn-outline-secondary"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.enrollments.edit', $en) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-outline-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-action="{{ route('admin.enrollments.destroy', $en) }}"><i class="fas fa-trash"></i></button>
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
            @include('partials._pagination', ['collection' => $enrollments])
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
                    <form id="deleteFormEnrollment" method="POST" action="#">
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
    $('#deleteFormEnrollment').attr('action', action);
});
</script>
@endsection


