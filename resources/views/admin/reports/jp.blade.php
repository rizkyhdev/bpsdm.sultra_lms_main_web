@extends('layouts.admin')

@section('title', 'Laporan JP')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.dashboard') }}">Laporan</a></li>
    <li class="breadcrumb-item active">JP</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.reports.jp.export', request()->query()) }}" class="btn btn-outline-info"><i class="fas fa-file-export mr-1"></i> Ekspor</a>
@endsection

@section('content')
    <form method="GET" action="{{ route('admin.reports.jp') }}" class="mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-3">
                        <label>Tahun</label>
                        <input type="number" name="tahun" value="{{ request('tahun', now()->year) }}" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label>User</label>
                        <input type="text" name="user" value="{{ request('user') }}" class="form-control" placeholder="Nama/Email/NIP">
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
                    <th>User</th>
                    <th>Kursus</th>
                    <th>JP</th>
                    <th>Tahun</th>
                    <th>Tgl Catat</th>
                </tr>
                </thead>
                <tbody>
                @forelse($jpRecords as $r)
                    <tr>
                        <td>{{ $r->user->nama ?? '-' }}</td>
                        <td>{{ $r->course->judul ?? '-' }}</td>
                        <td>{{ $r->jp_earned }}</td>
                        <td>{{ $r->tahun }}</td>
                        <td>{{ optional($r->recorded_at)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">
            @include('partials._pagination', ['collection' => $jpRecords])
        </div>
    </div>
@endsection


