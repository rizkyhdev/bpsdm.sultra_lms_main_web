@extends('layouts.admin')

@section('title', 'Laporan Kuis')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.reports.dashboard') }}">Laporan</a></li>
    <li class="breadcrumb-item active">Kuis</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.reports.quizzes.export', request()->query()) }}" class="btn btn-outline-info"><i class="fas fa-file-export mr-1"></i> Ekspor</a>
@endsection

@section('content')
    <form method="GET" action="{{ route('admin.reports.quizzes') }}" class="mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4">
                        <label>Sub Modul</label>
                        <input type="text" name="sub_module" value="{{ request('sub_module') }}" class="form-control">
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
                    <th>Kuis</th>
                    <th>Sub Modul</th>
                    <th>Rata-rata Nilai</th>
                    <th>Kelulusan</th>
                    <th>Rata-rata Percobaan</th>
                </tr>
                </thead>
                <tbody>
                @forelse($quizStats as $s)
                    <tr>
                        <td>{{ $s->quiz_title }}</td>
                        <td>{{ $s->sub_module_title }}</td>
                        <td>{{ number_format($s->avg_score ?? 0, 2) }}</td>
                        <td>{{ $s->pass_rate ?? 0 }}%</td>
                        <td>{{ number_format($s->avg_attempts ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Tidak ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">
            @include('partials._pagination', ['collection' => $quizStats])
        </div>
    </div>
@endsection


