@extends('layouts.admin')

@section('title', 'Laporan Sertifikat')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.dashboard') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Sertifikat</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.reports.certificates.export', request()->query()) }}" class="btn btn-outline-info"><i class="fas fa-file-export mr-1"></i> Ekspor</a>
@endsection

@section('content')
    <form method="GET" action="{{ route('admin.reports.certificates') }}" class="mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4">
                        <label>Pencarian</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nomor/Nama/Kursus">
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
                    <th>Nomor</th>
                    <th>Pengguna</th>
                    <th>Kursus</th>
                    <th>Tanggal</th>
                </tr>
                </thead>
                <tbody>
                @forelse($certificates as $c)
                    <tr>
                        <td>{{ $c->nomor_sertifikat }}</td>
                        <td>{{ $c->user->nama ?? '-' }}</td>
                        <td>{{ $c->course->judul ?? '-' }}</td>
                        <td>{{ optional($c->issue_date)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">
            @include('partials._pagination', ['collection' => $certificates])
        </div>
    </div>
@endsection


