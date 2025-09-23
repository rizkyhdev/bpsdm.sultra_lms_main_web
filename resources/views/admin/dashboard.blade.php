@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('breadcrumb')
    {{-- Breadcrumb sederhana --}}
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
@endsection

@section('content')
    {{-- Kartu metrik utama --}}
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Total Pengguna</div>
                            <div class="h4 mb-0">{{ $metrics['users_total'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-users fa-2x text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Total Kursus</div>
                            <div class="h4 mb-0">{{ $metrics['courses_total'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-book-open fa-2x text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Total Pendaftaran</div>
                            <div class="h4 mb-0">{{ $metrics['enrollments_total'] ?? 0 }}</div>
                        </div>
                        <i class="fas fa-user-plus fa-2x text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">Completion Rate</div>
                            <div class="h4 mb-0">{{ $metrics['completion_rate'] ?? 0 }}%</div>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <span>Aktivitas Pendaftaran Terbaru</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>Pengguna</th>
                                <th>Kursus</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse(($recentEnrollments ?? []) as $en)
                                <tr>
                                    <td>{{ $en->user->nama ?? '-' }}</td>
                                    <td>{{ $en->course->judul ?? '-' }}</td>
                                    <td>{{ optional($en->enrollment_date)->format('d/m/Y') }}</td>
                                    <td><span class="badge badge-{{ $en->status == 'completed' ? 'success' : 'secondary' }}">{{ $en->status }}</span></td>
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
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">Sertifikat Terbaru</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                            <tr>
                                <th>Nomor</th>
                                <th>Pengguna</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse(($recentCertificates ?? []) as $c)
                                <tr>
                                    <td>{{ $c->nomor_sertifikat }}</td>
                                    <td>{{ $c->user->nama ?? '-' }}</td>
                                    <td class="text-right">
                                        @if(!empty($c->file_path))
                                            <a class="btn btn-sm btn-outline-primary" href="{{ Storage::url($c->file_path) }}" target="_blank"><i class="fas fa-download"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Placeholder chart (controller perlu menyediakan data berikut) --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white">Tren Pendaftaran</div>
                <div class="card-body">
                    <div id="chartEnrollments" style="height:240px"
                         data-labels='@json($charts['enrollments']['labels'] ?? [])'
                         data-series='@json($charts['enrollments']['series'] ?? [])'>
                        {{-- Tempatkan chart JS di app.js menggunakan dataset di atas --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white">Tren Kelulusan</div>
                <div class="card-body">
                    <div id="chartCompletions" style="height:240px"
                         data-labels='@json($charts['completions']['labels'] ?? [])'
                         data-series='@json($charts['completions']['series'] ?? [])'>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


