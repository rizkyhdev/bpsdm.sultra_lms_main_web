<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <label for="email" class="form-label fw-semibold">{{ __('Email Address') }}Masukkan NIP Anda</label>
    <i class="fa fa-user input-icon"></i>
    <input type="text" id="email" class="form-control  @error('email') is-invalid @enderror" placeholder="Contoh: misnan021@gmail.com" value="{{ old('email') }}" required autofocus>
     @error('email')
          <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
          </span>
      @enderror
  </div>

  <!-- Field Password -->
  <div class="mb-3 input-group-custom">
    <label for="password" class="form-label fw-semibold">{{ __('Password') }}Masukkan Password Anda</label>
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
