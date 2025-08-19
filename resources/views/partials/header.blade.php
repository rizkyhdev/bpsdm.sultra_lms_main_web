<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'AURA Dashboard') }}</title>
  
  <!-- custom css -->
  <link href="{{ asset('navbarstyling.css') }}" rel="stylesheet">

  <!-- Font Awesome for Icons (via CDN) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="icon" href="{{ asset('image/LOGO AURA 1.png') }}" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body class="bg-light d-flex flex-column min-vh-100">
  <!-- Navbar -->
  <header class="d-flex justify-content-between align-items-center px-4 py-3 bg-white shadow">
    <div class="d-flex align-items-center gap-2">
      <img src="{{ asset('images/LOGO AURA.png') }}" alt="Logo" height="50" class="ms-3">
      <img src="{{ asset('images/LOGO AURA 1.png') }}" alt="Logo Font" height="50">
      <button id="menu-toggle" class="btn btn-link ms-5">
        <i class="fas fa-bars fs-4" style="color:#21b3ca;"></i>
      </button>
    </div>
    <nav class="d-none d-lg-flex gap-4 fw-semibold">
        <a href="" class="text-decoration-none text-dark">Home</a>
        <a href="" class="text-decoration-none text-dark">Course</a>
        <a href="" class="text-decoration-none text-dark">Article</a>
        <a href="" class="text-decoration-none text-dark">Contact</a>
    </nav>
    <div class="d-flex align-items-center gap-3">
      <div class="position-relative">
        <input type="text" class="form-control border-info ps-4" placeholder="Search.." style="color:#88d3e1;">
        <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-info"></i>
      </div>
      <button class="btn rounded-circle d-flex align-items-center justify-content-center shadow"
        style="background-color: #88d4e1; width: 40px; height: 40px; outline: none; box-shadow: 0 2px 6px rgba(0,0,0,0.1); border: none;"
        onfocus="this.blur();">
        <i class="far fa-bell" style="color: black;"></i>
      </button>
      <div class="dropdown">
        <div class="dropdown">
        <button class="btn rounded-circle d-flex align-items-center justify-content-center shadow"
          style="background-color: #88d4e1; width: 40px; height: 40px; outline: none; box-shadow: 0 2px 6px rgba(0,0,0,0.1); border: none;"
          data-bs-toggle="dropdown"
          onfocus="this.blur();">
          <i class="far fa-user" style="color: black;"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-0 rounded-4 overflow-hidden mt-2" style="min-width: 240px; border-radius: 16px; background-color: #fff;
          font-family: sans-serif; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2) !important; z-index: 1055;">
          <!-- Header -->
          <div class="border-bottom p-3 d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #21b3ca;">
              <i class="far fa-user text-black"></i>
            </div>
            <div>
              @auth
                <p class="mb-0 fw-bold" style="font-size: 14px; color: #000;">{{ auth()->user()->name }}</p>
                <small class="text-muted" style="font-size: 12px;">{{ auth()->user()->email }}</small>
              @endauth
              @guest
                <p class="mb-0 fw-bold" style="font-size: 14px;">Guest</p>
                <small class="text-primary" style="font-size: 12px;">Please log in</small>
              @endguest
            </div>
          </div>
          <a class="dropdown-item dropdown-link" href="">
            <div class="hover-bg">
              <i class="far fa-user me-2"></i> Profile
            </div>
          </a>
          <a class="dropdown-item dropdown-link" href="">
            <div class="hover-bg">
              <i class="far fa-star me-2"></i> Review
            </div>
          </a>
          <a class="dropdown-item dropdown-link" href="">
            <div class="hover-bg">
              <i class="fas fa-cog me-2"></i> Setting
            </div>
          </a>
          <!-- Divider -->
          <div style="border-top: 1px solid #e0e0e0;"></div>
          <!-- Sign Out -->
          <form action="" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="dropdown-item dropdown-link">
              <div class="hover-bg">
                <i class="fas fa-power-off me-2"></i> Sign Out
              </div>
            </button>
          </form>
        </div>
      </div>
    </div>
  </header>

  <div class="page-container d-flex flex-fill fw-semibold">
    <!-- Sidebar -->
    <aside id="sidebar" style="background-color: #88d4e1;" class="text-black p-4 d-none d-md-block">
      <div class="d-flex align-items-center gap-2 mb-4">
        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
          <i class="far fa-user text-black fs-3"></i>
        </div>
        <div>
          @auth
              <p class="mb-0 fw-bold">{{ auth()->user()->name }}</p>
              <small class="text-info">{{ auth()->user()->email }}</small>
          @else
              <p class="mb-0 fw-bold">Guest</p>
              <small class="text-white">Please log in</small>
          @endauth
        </div>
      </div>
      <nav>
        <h6 class="fw-bold text-uppercase text-white">Dashboard</h6>
        <ul class="nav flex-column mb-4">
          <li class="nav-item">
              <a class="nav-link text-black" href="">
                  <i class="fas fa-home me-2"></i>Dashboard
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link text-black" href="">
                  <i class="far fa-user me-2"></i>Profile
              </a>
          </li>
          @php
              $isEnrolledActive = Route::is('enrolled') || Route::is('active') || Route::is('complete');
          @endphp
          <li class="nav-item {{ $isEnrolledActive ? 'active' : '' }}">
              <a class="nav-link {{ $isEnrolledActive ? 'text-white' : 'text-black' }}" 
                style="{{ $isEnrolledActive ? 'background-color: #21b3ca;' : '' }}" 
                href="">
                  <i class="fas fa-book me-2"></i>Enrolled Courses
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link text-black fw-semibold" href="">
                  <i class="fas fa-bookmark me-2"></i> Wishlist
              </a>
          </li>
          <li class="nav-item">
              <a class="nav-link text-black fw-semibold" href="">
                  <i class="fas fa-star me-2"></i>Reviews
              </a>
          </li>
          <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-pen me-2"></i>My Quiz Attempts</a></li>
          <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-shopping-bag me-2"></i>Order History</a></li>
          <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-question-circle me-2"></i>Question & Answer</a></li>
          <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-calendar me-2"></i>Calendar</a></li>
        </ul>
        <h6 class="fw-bold text-uppercase text-white">Store</h6>
        <ul class="nav flex-column mb-4">
          <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-shopping-cart me-2"></i>Order</a></li>
          <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-download me-2"></i>Downloads</a></li>
          <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-map-marker-alt me-2"></i>Edit Address</a></li>
          <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-user me-2"></i>Edit Account</a></li>
          <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-wallet me-2"></i>Payment</a></li>
        </ul>
        <h6 class="fw-bold text-uppercase text-white">Account Setting</h6>
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-gear me-2"></i>Edit Profile</a></li>
          <li class="nav-item"><a class="nav-link text-black" href="#"><i class="far fa-user me-2"></i>Security</a></li>
          <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-sign-out-alt me-2"></i>Log Out</a></li>
        </ul>
      </nav>
    </aside>
    <!-- Main Content -->
    <main class="flex-fill p-4">
      @yield('content')
    </main>
  </div>

  <!-- JavaScript to Toggle Sidebar -->
  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('menu-toggle');

    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('d-none');
    });
  </script>
  <!--pindah halaman-->
  <script>
  // Saat halaman selesai dimuat, aktifkan fade-in
    document.addEventListener("DOMContentLoaded", function () {
      document.body.classList.add("page-loaded");
    });

    // Tangani transisi fade-out saat klik menu internal
    document.querySelectorAll('a[href]').forEach(link => {
      link.addEventListener('click', function (e) {
        const target = this.getAttribute('href');

        // Cek kalau link masih dalam domain & bukan blank
        const isSameOrigin = this.hostname === window.location.hostname;
        const isNotBlank = this.getAttribute('target') !== '_blank';
        const isNotAnchor = !target.startsWith('#');

        if (isSameOrigin && isNotBlank && isNotAnchor) {
          e.preventDefault();

          // Tambah efek fade-out
          document.body.classList.remove("page-loaded");
          document.body.classList.add("fade-out");

          // Setelah transisi selesai, redirect ke halaman
          setTimeout(() => {
            window.location.href = target;
          }, 100); // Waktu harus sesuai dengan CSS
        }
      });
    });
  </script>
</body>
</html>
