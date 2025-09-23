@extends('layouts.admin')

@section('title', 'Detail Modul')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $module->course_id) }}">Kursus</a></li>
    <li class="breadcrumb-item active">Detail Modul</li>
@endsection

@section('header-actions')
    <div class="btn-group">
        <a href="{{ route('admin.modules.edit', $module) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit mr-1"></i> Ubah</a>
        <a href="{{ route('admin.sub_modules.create', ['module' => $module->id]) }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> Sub Modul</a>
    </div>
@endsection

@section('content')
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h5 class="mb-1">{{ $module->judul }}</h5>
            <p class="mb-0 text-muted">Urutan: {{ $module->urutan }}</p>
            <p class="mt-2 mb-0">{!! nl2br(e($module->deskripsi)) !!}</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">Sub Modul</div>
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
                    @forelse(($subModules ?? []) as $s)
                        <tr>
                            <td>{{ $s->urutan }}</td>
                            <td><a href="{{ route('admin.sub_modules.show', $s) }}">{{ $s->judul }}</a></td>
                            <td>{{ \Illuminate\Support\Str::limit($s->deskripsi, 100) }}</td>
                            <td class="text-right">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.sub_modules.show', $s) }}" class="btn btn-outline-secondary"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('admin.sub_modules.edit', $s) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">Belum ada sub modul</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection


