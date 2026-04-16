@extends('layouts.instructor')

@section('title', 'Edit Informasi Pelatihan')

@section('breadcrumb')
<nav aria-label="breadcrumb">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
    <li class="breadcrumb-item"><a href="{{ route('instructor.courses.index') }}">Pelatihan</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit - {{ $course->judul }}</li>
  </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1 text-dark">Edit Informasi Pelatihan</h2>
                    <p class="text-muted mb-0">Perbarui data detail untuk pelatihan "{{ $course->judul }}"</p>
                </div>
                <a href="{{ route('instructor.courses.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-journal-text me-2"></i>Formulir Data Pelatihan
                    </h5>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    @if(session('error'))
                        <div class="alert alert-danger bg-danger text-white border-0 alert-dismissible fade show rounded-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger bg-danger-subtle text-danger border-0 alert-dismissible fade show rounded-3" role="alert">
                            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-circle-fill me-2"></i>Mohon periksa kembali form Anda:</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('instructor.courses.update', $course->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Judul Pelatihan <span class="text-danger">*</span></label>
                            <input type="text" name="judul" value="{{ old('judul', $course->judul) }}" class="form-control form-control-lg @error('judul') is-invalid @enderror" placeholder="Contoh: Dasar Dasar Kepemimpinan 101" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Usahakan judul menarik dan merepresentasikan isi materi secara akurat.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Deskripsi Lengkap <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="5" placeholder="Tuliskan deskripsi lengkap mengenai tujuan dan materi pelatihan ini..." required>{{ old('deskripsi', $course->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label fw-bold text-dark">Total Jam Pelajaran (JP) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" name="jp_value" value="{{ old('jp_value', $course->jp_value) }}" class="form-control hover-border @error('jp_value') is-invalid @enderror" min="1" placeholder="Misal: 10" required>
                                    <span class="input-group-text bg-light text-muted fw-semibold">JP</span>
                                </div>
                                @error('jp_value')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Bidang Kompetensi <span class="text-danger">*</span></label>
                                <input type="text" name="bidang_kompetensi" value="{{ old('bidang_kompetensi', $course->bidang_kompetensi) }}" class="form-control @error('bidang_kompetensi') is-invalid @enderror" placeholder="Contoh: Manajemen Waktu" required>
                                @error('bidang_kompetensi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card bg-light border-0 rounded-4 mb-4 mt-5">
                            <div class="card-body p-4">
                                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-calendar-event me-2 text-primary"></i>Pengaturan Waktu Akses <span class="badge bg-secondary ms-1 fw-normal">Opsional</span></h6>
                                <p class="small text-muted mb-4">Tentukan batas tanggal siswa dapat mengakses pelatihan ini. Biarkan kosong jika dapat diakses kapan saja sepanjang waktu.</p>
                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label fw-semibold text-secondary small">Tanggal Mulai Akses</label>
                                        <input type="datetime-local" name="start_date_time" value="{{ old('start_date_time', $course->start_date_time ? \Carbon\Carbon::parse($course->start_date_time)->format('Y-m-d\TH:i') : '') }}" class="form-control @error('start_date_time') is-invalid @enderror">
                                        @error('start_date_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary small">Tanggal Berakhir Akses</label>
                                        <input type="datetime-local" name="end_date_time" value="{{ old('end_date_time', $course->end_date_time ? \Carbon\Carbon::parse($course->end_date_time)->format('Y-m-d\TH:i') : '') }}" class="form-control @error('end_date_time') is-invalid @enderror">
                                        @error('end_date_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3 mt-4">
                            <a href="{{ route('instructor.courses.index') }}" class="btn btn-light border px-4 py-2 fw-semibold rounded-pill me-md-2" style="color: #6c757d">Batal Simpan</a>
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm rounded-pill d-flex align-items-center premium-hover">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="alert alert-info border-0 shadow-sm rounded-4 mt-4 d-flex align-items-center p-4 bg-white" style="border-left: 5px solid #0dcaf0 !important;" role="alert">
                <i class="bi bi-info-circle-fill fs-3 me-3 text-info"></i>
                <div class="small text-muted">
                    <strong class="text-dark d-block mb-1">Catatan Pengelolaan Konten:</strong> 
                    Halaman ini khusus untuk mengubah data dasar profil pelatihan. Jika Anda ingin mengelola modul, menyusun konten materi, atau membuat kuis interaktif, silakan kunjungi menu <a href="{{ route('instructor.courses.show', $course->id) }}" class="alert-link text-decoration-none fw-bold"><i class="bi bi-box-arrow-up-right ms-1 me-1"></i>Detail Pelatihan</a> untuk manajemen pembelajaran yang lebih lengkap.
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<style>
    /* Premium UI styling for inputs */
    .form-control {
        border-radius: 0.6rem;
        padding: 0.75rem 1rem;
        border: 1px solid #ced4da;
        background-color: #fcfcfc;
        transition: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
    .form-control:focus {
        border-color: #21b3ca;
        background-color: #fff;
        box-shadow: 0 0 0 0.25rem rgba(33, 179, 202, 0.15);
    }
    .hover-border:hover {
        border-color: #adb5bd;
    }
    .form-control-lg {
        padding: 1rem 1.25rem;
        font-size: 1.15rem;
    }
    
    /* Input Group adjustments */
    .input-group > .form-control {
        border-radius: 0.6rem 0 0 0.6rem;
    }
    .input-group-text {
        border-radius: 0 0.6rem 0.6rem 0;
        border: 1px solid #ced4da;
        border-left: 0;
    }
    .input-group:focus-within .input-group-text {
        border-color: #21b3ca;
    }

    /* Button styles */
    .btn-primary {
        background: linear-gradient(135deg, #21b3ca 0%, #003f7d 100%);
        border: none;
    }
    .premium-hover {
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
    }
    .premium-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(33, 179, 202, 0.25)!important;
        background: linear-gradient(135deg, #29c5de 0%, #004d99 100%);
    }
    .premium-hover:active {
        transform: translateY(0);
    }
    
    /* Typography polish */
    .form-label {
        font-size: 0.95rem;
        margin-bottom: 0.4rem;
    }
</style>
@endpush
@endsection
