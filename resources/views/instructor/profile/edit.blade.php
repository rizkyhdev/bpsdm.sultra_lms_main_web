@extends('layouts.instructor')

@section('title', 'Sunting Profil Instruktur')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.profile.show') }}">Profil Saya</a></li>
        <li class="breadcrumb-item active" aria-current="page">Sunting</li>
      </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-success py-3 px-4 border-0">
                    <h5 class="mb-0 text-white fw-bold">Perbarui Profil Instruktur</h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    @if(session('status'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('instructor.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row g-4">
                            <!-- Avatar Section -->
                            <div class="col-md-4 text-center border-end-md">
                                <div class="mb-4">
                                    <label class="form-label d-block fw-bold small text-uppercase text-muted mb-3">Foto Profil</label>
                                    <div class="position-relative d-inline-block">
                                        <img id="avatar-preview" src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle border border-4 border-light shadow-sm" style="width: 160px; height: 160px; object-fit: cover;">
                                        <label for="avatar-input" class="position-absolute bottom-0 end-0 bg-white text-success p-2 rounded-circle shadow-sm border cursor-pointer" title="Ubah Foto">
                                            <i class="bi bi-camera-fill"></i>
                                        </label>
                                        <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*" onchange="previewImage(this)">
                                    </div>
                                    <div class="mt-3 text-start small text-muted">
                                        <p class="mb-1"><i class="bi bi-info-circle me-1"></i> Gunakan foto formal yang jelas.</p>
                                        <p class="mb-0"><i class="bi bi-info-circle me-1"></i> Maksimal 2MB (JPG atau PNG).</p>
                                        @error('avatar') <span class="text-danger d-block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Basic Info Section -->
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control rounded-3 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Alamat Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">NIP</label>
                                        <input type="text" name="nip" class="form-control rounded-3" value="{{ old('nip', $user->nip) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Nomor Telepon</label>
                                        <input type="text" name="phone" id="phone" class="form-control rounded-3 @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Jabatan</label>
                                        <input type="text" name="jabatan" class="form-control rounded-3" value="{{ old('jabatan', $user->jabatan) }}" placeholder="Misal: Widyaiswara Ahli Madya">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Unit Kerja</label>
                                        <input type="text" name="unit_kerja" class="form-control rounded-3" value="{{ old('unit_kerja', $user->unit_kerja) }}">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Details & Bio -->
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Zona Waktu</label>
                                        <select name="timezone" class="form-select rounded-3">
                                            @foreach(timezone_identifiers_list() as $tz)
                                                <option value="{{ $tz }}" {{ old('timezone', $user->timezone ?? 'Asia/Makassar') == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Bahasa Pengantar</label>
                                        <select name="locale" class="form-select rounded-3">
                                            <option value="id" {{ old('locale', $user->locale ?? 'id') == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                            <option value="en" {{ old('locale', $user->locale) == 'en' ? 'selected' : '' }}>English</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-muted text-uppercase">Biografi & Keahlian</label>
                                        <textarea name="bio" class="form-control rounded-3" rows="5" placeholder="Tuliskan pengalaman mengajar atau bidang keahlian Anda...">{{ old('bio', $user->bio) }}</textarea>
                                        <small class="text-muted">Biografi ini akan ditampilkan pada halaman kursus Anda.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top">
                            <a href="{{ route('instructor.profile.show') }}" class="btn btn-link text-muted text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Profil
                            </a>
                            <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm">
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
        border-color: #1cc88a;
        box-shadow: 0 0 0 0.25rem rgba(28, 200, 138, 0.1);
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
