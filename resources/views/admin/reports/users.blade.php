@extends('layouts.admin')

@section('title', 'Laporan Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.dashboard') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Pengguna</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.reports.users.export', request()->query()) }}" class="btn btn-outline-info"><i class="fas fa-file-export mr-1"></i> Ekspor</a>
@endsection

@section('content')
    <form method="GET" action="{{ route('admin.reports.users') }}" class="mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-3">
                        <label>Peran</label>
                        <select name="role" class="form-control">
                            <option value="">Semua</option>
                            @foreach(['admin','instructor','student'] as $r)
                                <option value="{{ $r }}" @if(request('role')===$r) selected @endif>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Validasi</label>
                        <select name="validated" class="form-control">
                            <option value="">Semua</option>
                            <option value="1" @if(request('validated')==='1') selected @endif>Tervalidasi</option>
                            <option value="0" @if(request('validated')==='0') selected @endif>Belum</option>
                        </select>
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
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Validasi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $u)
                    <tr>
                        <td>{{ $u->nip }}</td>
                        <td>{{ $u->nama }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->role }}</td>
                        <td>{{ $u->is_validated ? 'Ya' : 'Tidak' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">
            @include('partials._pagination', ['collection' => $users])
        </div>
    </div>
@endsection


