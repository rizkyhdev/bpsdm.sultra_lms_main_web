@extends('layouts.studentapp')

@section('title', 'Sunting Profil Peserta')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            @include('student._breadcrumbs', ['crumbs' => [
                ['label' => 'Dashboard', 'route' => 'student.dashboard'],
                ['label' => 'Profil Saya', 'route' => 'student.profile.show'],
                ['label' => 'Sunting Profil'],
            ]])

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mt-4">
                <div class="card-header bg-info py-3 px-4 border-0">
                    <h5 class="mb-0 text-white fw-bold">Pengaturan Profil & Verifikasi</h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    @if(session('status'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row g-4 mb-5">
                            <!-- Avatar Section -->
                            <div class="col-md-4 text-center border-md-end">
                                <label class="form-label d-block fw-bold small text-uppercase text-muted mb-3">Foto Profil</label>
                                <div class="position-relative d-inline-block shadow-sm rounded-circle p-1 bg-white border">
                                    <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                    <label for="avatar-input" class="position-absolute bottom-0 end-0 bg-info text-white p-2 rounded-circle shadow-sm border border-2 border-white cursor-pointer" title="Ubah Foto">
                                        <i class="bi bi-camera-fill"></i>
                                    </label>
                                    <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*" onchange="previewImage(this)">
                                </div>
                                <div class="mt-3 text-start px-3">
                                    <ul class="small text-muted mb-0 ps-3">
                                        <li>Maksimal ukuran file: 2MB</li>
                                        <li>Format: JPG, JPEG, atau PNG</li>
                                    </ul>
                                    @error('avatar') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <h6 class="fw-bold mb-3 border-bottom pb-2">Data Personal</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                        @error('name') <div class="invalid-feedback text-xs">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Alamat Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                        @error('email') <div class="invalid-feedback text-xs">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">NIP <span class="text-muted font-normal">(Opsional)</span></label>
                                        <input type="text" name="nip" class="form-control rounded-3" value="{{ old('nip', $user->nip) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Nomor Telepon</label>
                                        <input type="text" name="phone" id="phone" class="form-control rounded-3 @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                                        @error('phone') <div class="invalid-feedback text-xs">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Jabatan</label>
                                        <input type="text" name="jabatan" class="form-control rounded-3" value="{{ old('jabatan', $user->jabatan) }}" placeholder="Contoh: Analis Kebijakan">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Unit Kerja</label>
                                        <input type="text" name="unit_kerja" class="form-control rounded-3" value="{{ old('unit_kerja', $user->unit_kerja) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Verification Section -->
                        <div class="bg-light rounded-4 p-4 mb-5 shadow-sm border border-info-subtle" id="verification">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <i class="bi bi-shield-lock me-2 text-info"></i>Verifikasi Identitas ASN
                            </h6>
                            <p class="text-muted small mb-4">Silakan lengkapi Surat Tugas atau Surat Bukti ASN Anda untuk proses verifikasi oleh Admin.</p>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Tautan Google Drive (Publik)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0 rounded-start-3"><i class="bi bi-link-45deg"></i></span>
                                        <input type="url" name="surat_tugas_url" class="form-control border-start-0 rounded-end-3 @error('surat_tugas_url') is-invalid @enderror" 
                                               value="{{ old('surat_tugas_url', $user->surat_tugas_url) }}" placeholder="https://drive.google.com/...">
                                    </div>
                                    @error('surat_tugas_url') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Unggah File (PDF/Gambar, Max 5MB)</label>
                                    <input type="file" name="surat_tugas_file" class="form-control rounded-3 @error('surat_tugas_file') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                    @error('surat_tugas_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    @if($user->surat_tugas_file_path)
                                        <div class="mt-2 text-success small">
                                            <i class="bi bi-check-circle-fill me-1"></i> File berhasil diunggah sebelumnya.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Preferences & Bio -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Zona Waktu</label>
                                <select name="timezone" class="form-select rounded-3">
                                    @foreach(timezone_identifiers_list() as $tz)
                                        <option value="{{ $tz }}" {{ old('timezone', $user->timezone ?? 'Asia/Makassar') == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Bahasa Tampilan</label>
                                <select name="locale" class="form-select rounded-3">
                                    <option value="id" {{ old('locale', $user->locale ?? 'id') == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                    <option value="en" {{ old('locale', $user->locale) == 'en' ? 'selected' : '' }}>English</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Biografi Singkat</label>
                                <textarea name="bio" class="form-control rounded-3" rows="3" placeholder="Ceritakan sedikit tentang bidang pekerjaan atau ketertarikan Anda...">{{ old('bio', $user->bio) }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
                            <a href="{{ route('student.profile.show') }}" class="btn btn-link text-muted text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i>Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1rem !important; }
    .rounded-3 { border-radius: 0.75rem !important; }
    .cursor-pointer { cursor: pointer; }
    .form-control:focus, .form-select:focus {
        border-color: #21b3ca;
        box-shadow: 0 0 0 0.25rem rgba(33, 179, 202, 0.1);
    }
    .input-group-text { color: #6c757d; }
    @media (min-width: 768px) {
        .border-md-end { border-right: 1px solid #dee2e6 !important; }
    }
</style>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
