<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-container {
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
    .form-label {
  display: block;
  margin-bottom: 5px;
  color: #333;
  font-size: 0.95rem;
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
      .login-container {
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
   <!-- Vite  -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body>
  <!-- Preloader -->
  <div id="preloader">
    <div class="preloader-logo">
      <img src="{{ asset('image/LOGO_AURA_1.png') }}" alt="Logo" />
    </div>
  </div>
 <div class="container-fluid p-0">
    <div class="login-container">
      <!-- Left -->
      <div class="col-md-6 left-section">
        <div class="logo-group">
          <img src="{{ asset('image/LOGO_AURA.png') }}" alt="Logo 1" class="logo-img">
          <img src="{{ asset('image/LOGO_AURA_1.png') }}" alt="Logo 2" class="logo-img">
        </div>
        <h2 id="typed-header"></h2>
      </div>

      <!-- Right -->
      <div class="col-md-6 right-section">
        <h3 class="mb-4 text-center">Log In Akun</h3>
        <form method="POST" action="{{ route('login') }}">
  <!-- Field NIP -->
  <div class="mb-3 input-group-custom">
     @csrf
    <label for="email" class="form-label fw-semibold">{{ __('Alamat Email') }}</label>
    <i class="fa fa-user input-icon"></i>
    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
  </div>

  <!-- Field Password -->
  <div class="mb-3 input-group-custom">
    <label for="password" class="form-label fw-semibold">{{ __('Masukkan Password Anda') }}</label>
    <i class="fa fa-lock input-icon"></i>
    <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password" name="password" required autocomplete="current-password">
    <i class="fa fa-eye show-password" id="togglePassword"></i>
     @error('password')
      <span class="invalid-feedback" role="alert">
      <strong>{{ $message }}</strong>
        </span>
      @enderror
  </div>

  <!-- Tombol -->
  <button type="submit" class="btn btn-info w-100"> {{ __('Login') }}</button>
    @if (Route::has('password.request'))
    <a class="btn btn-link" href="{{ route('password.request') }}">
     {{ __('Forgot Your Password?') }}
    </a>
 @endif
</form>
        <p class="mt-3 text-center">Belum punya akun? <a href="{{ url('/register') }}">Daftar</a></p>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
