@extends('layouts.instructor')

@section('title', 'Buat Course Baru')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}">Courses</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-2">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-primary-soft text-primary rounded-3 me-3">
                            <i class="bi bi-journal-plus fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">Informasi Dasar Course</h4>
                            <p class="text-muted small mb-0">Isi detail course Anda untuk mulai membangun kurikulum.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 pt-2">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                            <strong><i class="bi bi-x-circle-fill me-2"></i>Validation Errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('instructor.courses.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-1">Judul Course <span class="text-danger">*</span></label>
                            <input type="text" name="judul" value="{{ old('judul') }}" class="form-control form-control-lg rounded-3 @error('judul') is-invalid @enderror" placeholder="Contoh: Pemrograman Web Lanjutan" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <div class="form-text">Gunakan judul yang ringkas dan menggambarkan isi course.</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-1">Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control rounded-3 @error('deskripsi') is-invalid @enderror" rows="5" placeholder="Jelaskan apa yang akan dipelajari siswa dalam course ini..." required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold mb-1">JP Value (Jam Pelajaran) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light rounded-start-3 border-end-0"><i class="bi bi-clock"></i></span>
                                    <input type="number" name="jp_value" value="{{ old('jp_value') }}" class="form-control rounded-end-3 @error('jp_value') is-invalid @enderror" min="1" placeholder="Misal: 40" required>
                                    @error('jp_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-semibold mb-1">Bidang Kompetensi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light rounded-start-3 border-end-0"><i class="bi bi-star"></i></span>
                                    <input type="text" name="bidang_kompetensi" value="{{ old('bidang_kompetensi') }}" class="form-control rounded-end-3 @error('bidang_kompetensi') is-invalid @enderror" placeholder="Misal: IT / Soft Skill" required>
                                    @error('bidang_kompetensi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-3 py-3 fw-bold shadow-sm transition-all">
                                <i class="bi bi-plus-circle-fill me-2"></i> Buat Course & Lanjutkan
                            </button>
                            <a href="{{ route('instructor.courses.index') }}" class="btn btn-link text-muted mt-2 text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Course
                            </a>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light p-4 text-center border-top-0">
                    <p class="mb-0 text-muted small">Setelah membuat informasi dasar, Anda akan dapat menambahkan modul dan materi pada halaman selanjutnya.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .icon-shape {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .transition-all {
        transition: all 0.2s ease-in-out;
    }
    .transition-all:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
    }
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }
    .rounded-3 {
        border-radius: 0.75rem !important;
    }
</style>
@endsection
