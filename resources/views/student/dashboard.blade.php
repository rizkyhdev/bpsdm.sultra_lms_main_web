@extends('layouts.studentapp')

@section('content')
<div class="container-fluid my-1">
    {{-- Page Header --}}
    <div class="mb-4">
        <h2 class="fw-bold mb-2">Dashboard</h2>
        <p class="text-muted mb-0">Selamat datang kembali, {{ auth()->user()->name }}</p>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2 small fw-semibold">Terdaftar</h6>
                            <h3 class="mb-0 fw-bold">{{ $enrolledCount ?? 0 }}</h3>
                        </div>
                        <div class="p-3 rounded" style="background-color: #e0e7ff;">
                            <i class="bi bi-book text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2 small fw-semibold">Sedang Berlangsung</h6>
                            <h3 class="mb-0 fw-bold">{{ $inProgressCount ?? 0 }}</h3>
                        </div>
                        <div class="p-3 rounded" style="background-color: #dbeafe;">
                            <i class="bi bi-lightning-charge text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2 small fw-semibold">Selesai</h6>
                            <h3 class="mb-0 fw-bold">{{ $completedCount ?? 0 }}</h3>
                        </div>
                        <div class="p-3 rounded" style="background-color: #d1fae5;">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2 small fw-semibold">Total JP</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalJp ?? 0 }}</h3>
                        </div>
                        <div class="p-3 rounded" style="background-color: #f3e8ff;">
                            <i class="bi bi-clock text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pelatihan Tersedia Section --}}
    @if(isset($availableCourses) && $availableCourses->count() > 0)
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold mb-1">Pelatihan Tersedia</h4>
                    <p class="text-muted small mb-0">Pelatihan yang dapat Anda ikuti</p>
                </div>
                <a href="{{ route('courses.index') }}" class="btn btn-outline-primary btn-sm">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="row g-4">
                @foreach($availableCourses as $course)
                    <div class="col-12 col-md-6 col-lg-4">
                        <x-course-card :course="$course" :actions="true" />
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0 mt-4" style="border-radius: 12px;">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                <h5 class="fw-bold">Tidak ada pelatihan tersedia</h5>
                <p class="text-muted">Semua pelatihan sudah terdaftar atau belum ada pelatihan yang tersedia.</p>
                <a href="{{ route('courses.index') }}" class="btn btn-primary">
                    Jelajahi Pelatihan
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

