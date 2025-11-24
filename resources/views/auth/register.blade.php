<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link rel="stylesheet" href="{{ asset('css/preload.css') }}">
  <style>
      body, html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
    }
    .register-container {
      height: 100vh;
      display: flex;
    }
    /* Left Section */
    .left-section {
      background: linear-gradient(-45deg, #007bff, #00c6ff, #0056b3, #00ffcc);
      background-size: 400% 400%;
      animation: gradientShift 8s ease infinite;
      color: white;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    .logo-group {
      display: flex;
      gap: 2rem;
      margin-bottom: 20px;
    }
    .logo-img {
      width: 130px;
      height: auto;
    }
    .left-section h2 {
      font-weight: bold;
      font-size: 1.4rem;
      animation: fadeInUp 1s ease;
    }
    /* Right Section */
    .right-section {
      background-color: white;
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      animation: fadeIn 1s ease;
    }
    .form-control {
      border-radius: 8px;
      padding-left: 40px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .form-control:focus {
      border-color: #007bff;
      box-shadow: 0 0 6px rgba(0,123,255,0.3);
    }
    .input-icon {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: gray;
    }
    .input-group-custom {
      position: relative;
    }
    .show-password {
      position: absolute;
      top: 50%;
      right: 12px;
      transform: translateY(-50%);
      cursor: pointer;
      color: gray;
    }
    /* Button */
    .btn-info {
      border-radius: 25px;
      padding: 10px;
      transition: all 0.3s ease;
    }
    .btn-info:hover {
      transform: translateY(-2px);
      background-color: #0056b3;
    }
    /* Animations */
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    @keyframes fadeInUp {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
    }
    /* Responsive */
    @media (max-width: 768px) {
      .register-container {
        flex-direction: column;
      }
      .left-section, .right-section {
        width: 100%;
        min-height: auto;
        padding: 20px;
      }
      .logo-img {
        width: 90px;
      }
    }
  </style>
</head>
<body>
  <!-- Preloader -->
  <div id="preloader">
    <div class="preloader-logo">
      <img src="{{ asset('image/LOGO_AURA_1.png') }}" alt="Logo" />
    </div>
  </div>

  <div class="container-fluid p-0">
    <div class="register-container">
      <!-- Kiri -->
      <div class="col-md-6 left-section">
        <div class="logo-group">
            <img src="{{ asset('image/LOGO_AURA.png') }}" alt="Logo 1" class="logo-img">
            <img src="{{ asset('image/LOGO_AURA_1.png') }}" alt="Logo 2" class="logo-img">
        </div>
        <h2 id="typed-header"></h2>
      </div>

      <!-- Kanan -->
      <div class="col-md-6 right-section">
        <h3 class="mb-4">Register Akun</h3>
       <form action="{{ route('register') }}" method="POST">
        @csrf
  <!-- Field NIP -->
  <!-- <div class="mb-3 input-group-custom">
    <label for="nip" class="form-label fw-semibold">Masukkan NIP Anda</label>
    <i class="fa fa-user input-icon"></i>
    <input type="text" id="nip" class="form-control" placeholder="Contoh: 197801012005011001">
  </div> -->

    <!-- Field NIP -->
  <!-- <div class="mb-3 input-group-custom">
    <label for="nip" class="form-label fw-semibold">Masukkan Username Anda</label>
    <i class="fa fa-user input-icon"></i>
    <input type="text" id="username" class="form-control" placeholder="Contoh: IsnanSaleh">
  </div> -->

  <!-- Field Password -->
  <!-- <div class="mb-3 input-group-custom">
    <label for="password" class="form-label fw-semibold">Masukkan Password Anda</label>
    <i class="fa fa-lock input-icon"></i>
    <input type="password" id="password" class="form-control" placeholder="Masukkan password">
    <i class="fa fa-eye show-password" id="togglePassword"></i>
  </div> -->

   <div class="row mb-3 ">
      <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Nama') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                         <div class="row mb-3">
                            <label for="nip" class="col-md-4 col-form-label text-md-end">{{ __('NIP') }}</label>

                            <div class="col-md-6">
                                <input id="nip" type="text" class="form-control @error('nip') is-invalid @enderror" name="nip" value="{{ old('nip') }}" required autocomplete="nip" autofocus>

                                @error('nip')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Alamat Email') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                          <div class="row mb-3">
                            <label for="jabatan" class="col-md-4 col-form-label text-md-end">{{ __('Jabatan') }}</label>

                            <div class="col-md-6">
                                <input id="jabatan" type="jabatan" class="form-control @error('jabatan') is-invalid @enderror" name="jabatan" value="{{ old('jabatan') }}" required autocomplete="jabatan">

                                @error('jabatan')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                          <div class="row mb-3">
                            <label for="unit_kerja" class="col-md-4 col-form-label text-md-end">{{ __('Unit Kerja') }}</label>

                            <div class="col-md-6">
                                <input id="unit_kerja" type="unit_kerja" class="form-control @error('unit_kerja') is-invalid @enderror" name="unit_kerja" value="{{ old('unit_kerja') }}" required autocomplete="unit_kerja">

                                @error('unit_kerja')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="role" class="col-md-4 col-form-label text-md-end">{{ __('Role') }}</label>

                            <div class="col-md-6">
                                <select id="role" class="form-control @error('role') is-invalid @enderror" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instructor</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>-</option>
                                </select>

                                @error('role')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Konfirmasi Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

  <!-- Tombol -->
  <button type="submit" class="btn btn-info w-100">REGISTER</button>
</form>
      </div>
    </div>
  </div>

 <script>
   window.registerSuccessMessage = "{{ session('success') }}";
  </script>
  <script src="https://unpkg.com/typed.js@2.1.0/dist/typed.umd.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('js/preload.js') }}"></script>
  <script src="{{ asset('js/text_animation.js') }}"></script>
  <script src="{{ asset('js/toggle_password.js') }}"></script>
  <script src="{{ asset('js/register_alert.js') }}"></script>
</body>
</html>
