@extends('layouts.admin')

@section('title', __('Admin Dashboard'))

@section('breadcrumb')
    <li class="text-gray-600 dark:text-gray-400" aria-current="page">{{ __('Dashboard') }}</li>
@endsection

@section('admin_content')

    @php /** @var array{users_total?:int,courses_total?:int,enrollments_total?:int,completion_rate?:int|float} $metrics */ @endphp
    @php /** @var \Illuminate\Support\Collection|array $recentEnrollments */ @endphp
    @php /** @var \Illuminate\Support\Collection|array $recentCertificates */ @endphp

    <style>
        .dashboard-card {
            border: none;
            border-radius: 1.25rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            color: white;
            overflow: hidden;
            position: relative;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.175) !important;
        }
        .card-icon-bg {
            position: absolute;
            right: -10px;
            bottom: -10px;
            font-size: 5rem;
            opacity: 0.15;
            transform: rotate(-15deg);
        }
        .bg-gradient-indigo { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
        .bg-gradient-emerald { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .bg-gradient-amber { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .bg-gradient-purple { background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%); }
        
        .premium-table thead th {
            background-color: #f8fafc;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #64748b;
            border-top: none;
        }
        .avatar-sm {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            object-fit: cover;
        }
        .status-badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            border-radius: 50rem;
        }
        .cert-item {
            transition: all 0.2s ease;
            border-radius: 0.75rem;
        }
        .cert-item:hover {
            background-color: #f1f5f9;
        }
    </style>

    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card shadow-sm p-4 bg-gradient-indigo">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase mb-0 opacity-75 font-weight-bold" style="font-size: 0.8rem; letter-spacing: 1px;">{{ __('Total Users') }}</h6>
                    <div class="bg-white bg-opacity-25 rounded-3 p-2">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
                <h2 class="display-6 fw-bold mb-0 text-white">{{ number_format($metrics['users_total']) }}</h2>
                <i class="fas fa-users card-icon-bg text-white"></i>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card shadow-sm p-4 bg-gradient-emerald">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase mb-0 opacity-75 font-weight-bold" style="font-size: 0.8rem; letter-spacing: 1px;">{{ __('Total Pelatihan') }}</h6>
                    <div class="bg-white bg-opacity-25 rounded-3 p-2">
                        <i class="fas fa-book-open fa-lg"></i>
                    </div>
                </div>
                <h2 class="display-6 fw-bold mb-0 text-white">{{ number_format($metrics['courses_total']) }}</h2>
                <i class="fas fa-book-open card-icon-bg text-white"></i>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card shadow-sm p-4 bg-gradient-amber">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase mb-0 opacity-75 font-weight-bold" style="font-size: 0.8rem; letter-spacing: 1px;">{{ __('Enrollments') }}</h6>
                    <div class="bg-white bg-opacity-25 rounded-3 p-2">
                        <i class="fas fa-user-graduate fa-lg"></i>
                    </div>
                </div>
                <h2 class="display-6 fw-bold mb-0 text-white">{{ number_format($metrics['enrollments_total']) }}</h2>
                <i class="fas fa-user-graduate card-icon-bg text-white"></i>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-card shadow-sm p-4 bg-gradient-purple">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-uppercase mb-0 opacity-75 font-weight-bold" style="font-size: 0.8rem; letter-spacing: 1px;">{{ __('Completion Rate') }}</h6>
                    <div class="bg-white bg-opacity-25 rounded-3 p-2">
                        <i class="fas fa-chart-pie fa-lg"></i>
                    </div>
                </div>
                <h2 class="display-6 fw-bold mb-0 text-white">{{ $metrics['completion_rate'] }}%</h2>
                <i class="fas fa-chart-pie card-icon-bg text-white"></i>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="mb-0 fw-bold">{{ __('Recent Enrollments') }}</h5>
                    <a href="{{ route('admin.enrollments.index') }}" class="btn btn-sm btn-link text-decoration-none fw-bold">{{ __('View All') }} &rarr;</a>
                </div>
                <div class="table-responsive">
                    <table class="table premium-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">{{ __('User') }}</th>
                                <th>{{ __('Course') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th class="pe-4 text-center">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEnrollments as $en)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $en->user->avatar_url }}" class="avatar-sm" alt="Avatar">
                                            <span class="fw-semibold text-dark">{{ $en->user->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $en->course->judul ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ optional($en->created_at)->format('d M Y') }}</td>
                                    <td class="pe-4 text-center">
                                        @if($en->status === 'completed')
                                            <span class="status-badge bg-success bg-opacity-10 text-success">{{ __('Completed') }}</span>
                                        @else
                                            <span class="status-badge bg-primary bg-opacity-10 text-primary uppercase">{{ $en->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-2">
                                            <i class="fas fa-inbox fa-3x"></i>
                                        </div>
                                        <p class="mb-0">{{ __('No recent enrollments.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                    <h5 class="mb-0 fw-bold">{{ __('Recent Certificates') }}</h5>
                    <a href="{{ route('admin.certificates.index') }}" class="btn btn-sm btn-link text-decoration-none fw-bold">{{ __('View All') }}</a>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-2">
                        @forelse($recentCertificates as $c)
                            <div class="cert-item p-3 d-flex justify-content-between align-items-center border border-light">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <div class="bg-indigo bg-opacity-10 text-indigo rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; color: #4f46e5;">
                                        <i class="fas fa-award"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark truncate">{{ $c->user->name }}</div>
                                        <div class="text-muted small truncate">{{ $c->nomor_sertifikat }}</div>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($c->file_path) }}" target="_blank" class="btn btn-sm btn-light rounded-3 text-indigo">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="text-muted opacity-50 mb-2">
                                    <i class="fas fa-award fa-3x"></i>
                                </div>
                                <p class="mb-0 small">{{ __('No recent certificates.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 1rem;">
                <h6 class="fw-bold text-dark mb-4">{{ __('Enrollment Trend') }}</h6>
                <div id="chartEnrollments" style="height: 300px;" data-labels='@json($charts["enrollments"]["labels"])' data-series='@json($charts["enrollments"]["series"])'></div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 1rem;">
                <h6 class="fw-bold text-dark mb-4">{{ __('Completion Trend') }}</h6>
                <div id="chartCompletions" style="height: 300px;" data-labels='@json($charts["completions"]["labels"])' data-series='@json($charts["completions"]["series"])'></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Enrollment Trend Chart
            const enrollmentEl = document.getElementById('chartEnrollments');
            if (enrollmentEl) {
                const labels = JSON.parse(enrollmentEl.getAttribute('data-labels'));
                const series = JSON.parse(enrollmentEl.getAttribute('data-series'));

                new ApexCharts(enrollmentEl, {
                    chart: {
                        type: 'area',
                        height: '100%',
                        toolbar: { show: false },
                        fontFamily: 'inherit',
                        sparkline: { enabled: false },
                    },
                    series: [{
                        name: 'Enrollments',
                        data: series
                    }],
                    xaxis: {
                        categories: labels,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                    },
                    colors: ['#6366f1'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [50, 100]
                        }
                    },
                    stroke: { curve: 'smooth', width: 3 },
                    dataLabels: { enabled: false },
                    grid: { borderColor: '#f1f5f9' }
                }).render();
            }

            // Completion Trend Chart
            const completionEl = document.getElementById('chartCompletions');
            if (completionEl) {
                const labels = JSON.parse(completionEl.getAttribute('data-labels'));
                const series = JSON.parse(completionEl.getAttribute('data-series'));

                new ApexCharts(completionEl, {
                    chart: {
                        type: 'bar',
                        height: '100%',
                        toolbar: { show: false },
                        fontFamily: 'inherit',
                    },
                    series: [{
                        name: 'Completions',
                        data: series
                    }],
                    xaxis: {
                        categories: labels,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                    },
                    colors: ['#10b981'],
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '45%',
                        }
                    },
                    dataLabels: { enabled: false },
                    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }
                }).render();
            }
        });
    </script>
@endpush


