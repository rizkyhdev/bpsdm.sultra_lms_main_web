<header class="d-flex justify-content-between align-items-center px-4 py-3 bg-white shadow">
  <div class="d-flex align-items-center gap-2">
    <img src="{{ asset('image/LOGO AURA.png') }}" alt="Logo" height="50" class="ms-3">
    <img src="{{ asset('image/LOGO AURA 1.png') }}" alt="Logo Font" height="50">
    <button id="menu-toggle" class="btn btn-link ms-5">
      <i class="fas fa-bars fs-4" style="color:#21b3ca;"></i>
    </button>
  </div>
  <nav class="d-none d-lg-flex gap-4 fw-semibold">
      <a href="{{ route('landing') }}" class="text-decoration-none text-dark">Home</a>
      <a href="{{ route('course') }}" class="text-decoration-none text-dark">Course</a>
      <a href="{{ route('article') }}" class="text-decoration-none text-dark">Article</a>
      <a href="{{ route('contact') }}" class="text-decoration-none text-dark">Contact</a>
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
        <div class="border-bottom p-3 d-flex align-items-center gap-2">
          <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #21b3ca; flex-shrink:0;">
            <i class="far fa-user text-black" style="line-height: 1; font-size:18px;"></i>
          </div>
          <div>
            @auth
              <p class="mb-0 fw-bold" style="font-size: 14px; color: #000;">{{ auth()->user()->name }}</p>
              <small class="text-muted" style="font-size: 12px;">{{ auth()->user()->email }}</small>
            @endauth
            @guest
              <p class="mb-0 fw-bold" style="font-size: 14px;">Nama Pengguna</p>
              <small class="text-primary" style="font-size: 12px;">NamaPengguna</small>
            @endguest
          </div>
        </div>
        <a class="dropdown-item dropdown-link" href="{{ route('profile') }}">
          <div class="hover-bg">
            <i class="far fa-user me-2"></i> Profil
          </div>
        </a>
        <a class="dropdown-item dropdown-link" href="{{ route('review') }}">
          <div class="hover-bg">
            <i class="far fa-star me-2"></i> Ulasan
          </div>
        </a>
        <a class="dropdown-item dropdown-link" href="{{ route('settings') }}">
          <div class="hover-bg">
            <i class="fas fa-cog me-2"></i> Pengaturan
          </div>
        </a>
        <div style="border-top: 1px solid #e0e0e0;"></div>
        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
          @csrf
          <button type="submit" class="dropdown-item dropdown-link">
            <div class="hover-bg">
              <i class="fas fa-power-off me-2"></i> Keluar
            </div>
          </button>
        </form>
      </div>
    </div>
  </div>
</header>


