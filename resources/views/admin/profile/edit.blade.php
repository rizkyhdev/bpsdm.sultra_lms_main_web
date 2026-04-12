@extends('layouts.admin')

@section('title', 'Edit Profil Admin')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.profile.show') }}">Profil Saya</a></li>
    <li class="breadcrumb-item active" aria-current="page">Sunting</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-primary py-3 px-4 border-0">
                    <h5 class="mb-0 text-white fw-bold">Sunting Profil Anda</h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    @if(session('status'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row g-4">
                            <!-- Avatar Section -->
                            <div class="col-md-4 text-center border-end-md">
                                <div class="mb-4">
                                    <label class="form-label d-block fw-bold small text-uppercase text-muted mb-3">Foto Profil</label>
                                    <div class="position-relative d-inline-block">
                                        <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle border border-4 border-light shadow-sm" style="width: 160px; height: 160px; object-fit: cover;">
                                        <label for="avatar-input" class="position-absolute bottom-0 end-0 bg-white text-primary p-2 rounded-circle shadow-sm border cursor-pointer" title="Ubah Foto">
                                            <i class="bi bi-camera-fill"></i>
                                        </label>
                                        <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*" onchange="previewImage(this)">
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted d-block">Ukuran maksimal 2MB (JPG, PNG)</small>
                                        @error('avatar')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Info Section -->
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Alamat Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">NIP</label>
                                        <input type="text" name="nip" class="form-control rounded-3" value="{{ old('nip', $user->nip) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nomor Telepon</label>
                                        <input type="text" name="phone" id="phone" class="form-control rounded-3 @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Jabatan</label>
                                        <input type="text" name="jabatan" class="form-control rounded-3" value="{{ old('jabatan', $user->jabatan) }}" placeholder="Misal: Administrator Utama">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Unit Kerja</label>
                                        <input type="text" name="unit_kerja" class="form-control rounded-3" value="{{ old('unit_kerja', $user->unit_kerja) }}" placeholder="Misal: BPSDM Provinsi">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Prefrences Section -->
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Zona Waktu</label>
                                        <select name="timezone" class="form-select rounded-3">
                                            @foreach(timezone_identifiers_list() as $tz)
                                                <option value="{{ $tz }}" {{ old('timezone', $user->timezone ?? 'Asia/Makassar') == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Bahasa</label>
                                        <select name="locale" class="form-select rounded-3">
                                            <option value="id" {{ old('locale', $user->locale ?? 'id') == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                            <option value="en" {{ old('locale', $user->locale) == 'en' ? 'selected' : '' }}>English</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Bio / Tentang Saya</label>
                                        <textarea name="bio" class="form-control rounded-3" rows="4" placeholder="Tuliskan sedikit tentang diri Anda...">{{ old('bio', $user->bio) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                            <a href="{{ route('admin.profile.show') }}" class="btn btn-link text-muted text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Profil
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
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
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.1);
    }
    @media (min-width: 768px) {
        .border-end-md { border-right: 1px solid #dee2e6 !important; }
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
