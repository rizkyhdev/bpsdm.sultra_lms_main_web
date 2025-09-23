@extends('layouts.admin')

@section('title', 'Detail Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Pengguna</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('header-actions')
    <div class="btn-group">
        @can('update', $user)
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit mr-1"></i> Ubah</a>
        @endcan
        @can('validateUser', $user)
            @if(!$user->is_validated)
                <form action="{{ route('admin.users.validate', $user) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-success btn-sm"><i class="fas fa-check mr-1"></i> Validasi</button>
                </form>
            @endif
        @endcan
        @can('export', App\Models\User::class)
            <a href="{{ route('admin.users.export') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-export mr-1"></i> Ekspor</a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Profil</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">NIP</dt><dd class="col-7">{{ $user->nip }}</dd>
                        <dt class="col-5">Nama</dt><dd class="col-7">{{ $user->nama }}</dd>
                        <dt class="col-5">Email</dt><dd class="col-7">{{ $user->email }}</dd>
                        <dt class="col-5">Jabatan</dt><dd class="col-7">{{ $user->jabatan }}</dd>
                        <dt class="col-5">Unit Kerja</dt><dd class="col-7">{{ $user->unit_kerja }}</dd>
                        <dt class="col-5">Peran</dt><dd class="col-7 text-uppercase"><span class="badge badge-info">{{ $user->role }}</span></dd>
                        <dt class="col-5">Validasi</dt><dd class="col-7">@if($user->is_validated)<span class="badge badge-success">Ya</span>@else<span class="badge badge-secondary">Tidak</span>@endif</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-3">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span>Pendaftaran Kursus</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>Kursus</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Selesai</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse(($enrollments ?? []) as $en)
                                <tr>
                                    <td><a href="{{ route('admin.courses.show', $en->course) }}">{{ $en->course->judul ?? '-' }}</a></td>
                                    <td>{{ optional($en->enrollment_date)->format('d/m/Y') }}</td>
                                    <td>{{ $en->status }}</td>
                                    <td>{{ optional($en->completed_at)->format('d/m/Y') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">Rekam JP</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>Tahun</th>
                                <th>Kursus</th>
                                <th>JP</th>
                                <th>Tercatat</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse(($jpRecords ?? []) as $jp)
                                <tr>
                                    <td>{{ $jp->tahun }}</td>
                                    <td><a href="{{ route('admin.courses.show', $jp->course) }}">{{ $jp->course->judul ?? '-' }}</a></td>
                                    <td>{{ $jp->jp_earned }}</td>
                                    <td>{{ optional($jp->recorded_at)->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


