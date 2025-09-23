@extends('layouts.admin')

@section('title', 'Laporan Kursus')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.dashboard') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Kursus</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.reports.courses.export', request()->query()) }}" class="btn btn-outline-info"><i class="fas fa-file-export mr-1"></i> Ekspor</a>
@endsection

@section('content')
    <form method="GET" action="{{ route('admin.reports.courses') }}" class="mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4">
                        <label>Bidang Kompetensi</label>
                        <input type="text" name="bidang_kompetensi" value="{{ request('bidang_kompetensi') }}" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Per Halaman</label>
                        <select name="per_page" class="form-control">
                            @foreach([10,25,50,100] as $size)
                                <option value="{{ $size }}" @if(request('per_page', 10)==$size) selected @endif>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <button class="btn btn-outline-secondary btn-block"><i class="fas fa-filter"></i></button>
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
                    <th>Judul</th>
                    <th>Bidang</th>
                    <th>JP</th>
                    <th>Pendaftar</th>
                    <th>Selesai</th>
                    <th>Completion Rate</th>
                </tr>
                </thead>
                <tbody>
                @forelse($courses as $c)
                    <tr>
                        <td>{{ $c->judul }}</td>
                        <td>{{ $c->bidang_kompetensi }}</td>
                        <td>{{ $c->jp_value }}</td>
                        <td>{{ $c->enrollments_count ?? 0 }}</td>
                        <td>{{ $c->completed_count ?? 0 }}</td>
                        <td>{{ $c->completion_rate ?? 0 }}%</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">
            @include('partials._pagination', ['collection' => $courses])
        </div>
    </div>
@endsection


