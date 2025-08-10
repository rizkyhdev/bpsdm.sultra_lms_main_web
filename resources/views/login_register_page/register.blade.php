<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/preload.css') }}">
  <style>
    body, html {
      height: 100%;
      margin: 0;
    }
    .left-section {
      background-color: #007bff; /* Biru */
      color: white;
      padding: 40px;
      min-height: 100vh;
    }
    .left-section h2 {
      font-weight: bold;
    }
    .logo-img {
    width: 150px; /* Perbesar ukuran logo */
    height: auto;
    }
    .me-5 {
    margin-right: 3rem !important; /* jarak antar logo */
    }
    @media (max-width: 768px) {
        .logo-img {
            width: 100px;
        }
    }
    .right-section {
      background-color: white;
      padding: 40px;
    }
    .login-container {
      height: 100vh;
    }
    .form-control {
      border-radius: 8px;
    }
    .btn-primary {
      border-radius: 25px;
      padding: 10px;
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

  <div class="container-fluid">
    <div class="row login-container">
      <!-- Kiri -->
      <div class="col-md-6 left-section d-flex flex-column justify-content-center align-items-center">
        <div class="d-flex align-items-center justify-content-center mb-4">
            <img src="{{ asset('image/LOGO_AURA.png') }}" alt="Logo 1" class="logo-img me-5" width="100">
            <img src="{{ asset('image/LOGO_AURA_1.png') }}" alt="Logo 2" class="logo-img" width="100">
        </div>
        <h2>Selamat Datang di Sistem</h2>
        <p>Deskripsi singkat platform atau tagline bisa diletakkan di sini.</p>
      </div>

      <!-- Kanan -->
      <div class="col-md-6 right-section d-flex flex-column justify-content-center">
        <h3 class="mb-4 text-center">Register Akun</h3>
        <form>
          <div class="mb-3">
            <label for="email" class="form-label">NIP</label>
            <input type="email" class="form-control" id="email" placeholder="Masukkan NIP anda">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Username</label>
            <input type="email" class="form-control" id="email" placeholder="Masukkan Username Anda">
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Masukkan password">
          </div>
          <button type="submit" class="btn btn-primary w-100">REGISTER</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/preload.js') }}"></script>
</body>
</html>
