@extends('layouts.admin')

@section('title', __('Edit User'))

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-info text-decoration-none">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-info text-decoration-none">{{ __('Daftar Pengguna') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Ubah Data') }}</li>
        </ol>
    </nav>
@endsection

@section('admin_content')

    @php /** @var App\Models\User $user */ @endphp
    <style>
        .edit-user-container {
            max-width: 1100px;
            margin: 0 auto;
        }
        .premium-card {
            border: none;
            border-radius: 1.25rem;
            background: #fff;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .profile-header-bg {
            background: linear-gradient(135deg, #88d4e1 0%, #21b3ca 100%);
            height: 100px;
        }
        .avatar-wrapper {
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
        .avatar-edit {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
            object-fit: cover;
        }
        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: #475569;
            margin-bottom: 0.5rem;
        }
        .form-control-premium {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }
        .form-control-premium:focus {
            box-shadow: 0 0 0 3px rgba(33, 179, 202, 0.15);
            border-color: #21b3ca;
            background-color: #fff;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-title::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        .btn-premium-save {
            background-color: #21b3ca;
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-premium-save:hover {
            background-color: #1a8f9d;
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(33, 179, 202, 0.2);
        }
    </style>

    <div class="edit-user-container mt-4">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" novalidate enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Left Panel: Profile Summary -->
                <div class="col-12 col-lg-4">
                    <div class="premium-card text-center pb-4">
                        <div class="profile-header-bg"></div>
                        <div class="avatar-wrapper">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar-edit bg-white">
                        </div>
                        <div class="px-4 mt-3">
                            <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                            <p class="text-muted small mb-3">{{ $user->email }}</p>
                            
                            <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                                <span class="badge rounded-pill bg-info text-dark">
                                    <i class="fas fa-tag me-1"></i> {{ ucfirst($user->role) }}
                                </span>
                                @if($user->is_validated)
                                    <span class="badge rounded-pill bg-success">
                                        <i class="fas fa-check-circle me-1"></i> Validated
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-warning text-dark">
                                        <i class="fas fa-clock me-1"></i> Pending
                                    </span>
                                @endif
                            </div>

                            <hr class="my-4 opacity-50">

                            <div class="text-start">
                                <label class="form-label">{{ __('Account Status') }}</label>
                                <div class="mb-3">
                                    <select name="is_validated" class="form-select form-control-premium @error('is_validated') is-invalid @enderror">
                                        <option value="0" {{ old('is_validated', $user->is_validated) == 0 ? 'selected' : '' }}>{{ __('Pending / Not Validated') }}</option>
                                        <option value="1" {{ old('is_validated', $user->is_validated) == 1 ? 'selected' : '' }}>{{ __('Verified') }}</option>
                                    </select>
                                    @error('is_validated') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <label class="form-label">{{ __('Account Role') }}</label>
                                <div class="mb-3">
                                    <select name="role" class="form-select form-control-premium @error('role') is-invalid @enderror">
                                        <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>{{ __('Student') }}</option>
                                        <option value="instructor" {{ old('role', $user->role) == 'instructor' ? 'selected' : '' }}>{{ __('Instructor') }}</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                    </select>
                                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel: Main Information -->
                <div class="col-12 col-lg-8">
                    <div class="premium-card p-4 p-md-5">
                        <h5 class="section-title"><i class="fas fa-info-circle text-info"></i> {{ __('Informasi Dasar') }}</h5>
                        
                        <div class="row g-4">
                            <div class="col-12">
                                <label for="name" class="form-label">{{ __('Nama Lengkap') }} <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control form-control-premium @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nip" class="form-label">{{ __('NIP (Opsional)') }}</label>
                                <input type="text" id="nip" name="nip" class="form-control form-control-premium @error('nip') is-invalid @enderror" value="{{ old('nip', $user->nip) }}">
                                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">{{ __('Alamat Email') }} <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control form-control-premium @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="jabatan" class="form-label">{{ __('Jabatan') }} <span class="text-danger">*</span></label>
                                <input type="text" id="jabatan" name="jabatan" class="form-control form-control-premium @error('jabatan') is-invalid @enderror" value="{{ old('jabatan', $user->jabatan) }}" required>
                                @error('jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="unit_kerja" class="form-label">{{ __('Unit Kerja') }} <span class="text-danger">*</span></label>
                                <input type="text" id="unit_kerja" name="unit_kerja" class="form-control form-control-premium @error('unit_kerja') is-invalid @enderror" value="{{ old('unit_kerja', $user->unit_kerja) }}" required>
                                @error('unit_kerja') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h5 class="section-title mt-5"><i class="fas fa-key text-info"></i> {{ __('Keamanan (Opsional)') }}</h5>
                        <p class="text-muted small mb-4">{{ __('Isi jika ingin mengubah kata sandi pengguna.') }}</p>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">{{ __('Kata Sandi Baru') }}</label>
                                <input type="password" id="password" name="password" class="form-control form-control-premium @error('password') is-invalid @enderror">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">{{ __('Konfirmasi Kata Sandi') }}</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-premium">
                            </div>
                        </div>

                        <div class="mt-5 d-flex flex-sm-row flex-column justify-content-end gap-3 pt-3 border-top border-light">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4 py-2 rounded-3 text-secondary border">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" class="btn btn-premium-save shadow-sm">
                                <i class="fas fa-save me-2"></i> {{ __('Simpan Perubahan') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection



