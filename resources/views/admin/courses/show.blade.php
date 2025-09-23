@extends('layouts.admin')

@section('title', 'Detail Kursus')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Kursus</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('header-actions')
    <div class="btn-group">
        @can('update', $course)
            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit mr-1"></i> Ubah</a>
        @endcan
        @can('duplicate', $course)
            <form action="{{ route('admin.courses.duplicate', $course) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-outline-secondary btn-sm"><i class="fas fa-copy mr-1"></i> Duplikasi</button>
            </form>
        @endcan
        @can('report', $course)
            <a href="{{ route('admin.courses.report', $course) }}" class="btn btn-outline-info btn-sm"><i class="fas fa-file-alt mr-1"></i> Laporan</a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Informasi Kursus</div>
                <div class="card-body">
                    <h5 class="mb-2">{{ $course->judul }}</h5>
                    <p class="mb-1 text-muted">Bidang: {{ $course->bidang_kompetensi }} | JP: {{ $course->jp_value }}</p>
                    <p class="mb-0">{!! nl2br(e($course->deskripsi)) !!}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Statistik</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-7">Total Pendaftar</dt><dd class="col-5 text-right">{{ $stats['enrollments'] ?? 0 }}</dd>
                        <dt class="col-7">Selesai</dt><dd class="col-5 text-right">{{ $stats['completed'] ?? 0 }}</dd>
                        <dt class="col-7">Completion Rate</dt><dd class="col-5 text-right">{{ $stats['completion_rate'] ?? 0 }}%</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span>Modul</span>
            @can('create', App\Models\Module::class)
                <a href="{{ route('admin.modules.create', ['course' => $course->id]) }}" class="btn btn-sm btn-primary"><i class="fas fa-plus mr-1"></i> Tambah Modul</a>
            @endcan
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th>Urutan</th>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse(($modules ?? []) as $m)
                        <tr>
                            <td>{{ $m->urutan }}</td>
                            <td><a href="{{ route('admin.modules.show', $m) }}">{{ $m->judul }}</a></td>
                            <td>{{ \Illuminate\Support\Str::limit($m->deskripsi, 80) }}</td>
                            <td class="text-right">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.modules.show', $m) }}" class="btn btn-outline-secondary"><i class="fas fa-eye"></i></a>
                                    @can('update', $m)
                                        <a href="{{ route('admin.modules.edit', $m) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">Belum ada modul</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection


