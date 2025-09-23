<aside id="sidebar" style="background-color: #88d4e1;" class="text-black p-4">
  <div class="d-flex align-items-center gap-2 mb-4">
    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
      <i class="far fa-user text-black fs-3"></i>
    </div>
    <div>
      @auth
          <p class="mb-0 fw-bold">{{ auth()->user()->name }}</p>
          <small class="text-info">{{ auth()->user()->email }}</small>
      @else
          <p class="mb-0 fw-bold">NamaPengguna</p>
          <small class="text-white">NamaPengguna</small>
      @endauth
    </div>
  </div>
  <nav>
    <h6 class="fw-bold text-uppercase text-white">Dasbor</h6>
    <ul class="nav flex-column mb-4">
      <li class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
          <a class="nav-link text-black" href="{{ route('dashboard') }}">
              <i class="fas fa-home me-2"></i>Dasbor
          </a>
      </li>
      <li class="nav-item {{ Route::is('profile') ? 'active' : '' }}">
          <a class="nav-link text-black" href="{{ route('profile') }}">
              <i class="far fa-user me-2"></i>Profil Saya
          </a>
      </li>
      @php
          $isEnrolledActive = Route::is('enrolled') || Route::is('active') || Route::is('complete');
      @endphp
      <li class="nav-item {{ $isEnrolledActive ? 'active' : '' }}">
          <a class="nav-link {{ $isEnrolledActive ? 'text-white' : 'text-black' }}" 
            style="{{ $isEnrolledActive ? 'background-color: #21b3ca;' : '' }}" 
            href="{{ route('enrolled') }}">
              <i class="fas fa-book me-2"></i>Daftar Pelatihan
          </a>
      </li>
      <li class="nav-item {{ Route::is('wishlist') ? 'active' : '' }}">
          <a class="nav-link text-black fw-semibold" href="{{ route('wishlist') }}">
              <i class="fas fa-bookmark me-2"></i> Daftar Keinginan
          </a>
      </li>
      <li class="nav-item {{ Route::is('reviews') ? 'active' : '' }}">
          <a class="nav-link text-black fw-semibold" href="{{ route('reviews') }}">
              <i class="fas fa-star me-2"></i>Ulasan
          </a>
      </li>
      <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-pen me-2"></i>Kuis Saya</a></li>
      <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-shopping-bag me-2"></i>Riwayat Pesanan</a></li>
      <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-question-circle me-2"></i>Tanya Jawab</a></li>
      <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-calendar me-2"></i>Kalender</a></li>
    </ul>
    <h6 class="fw-bold text-uppercase text-white">Pengaturan Akun</h6>
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-gear me-2"></i>Ubah Profil</a></li>
      <li class="nav-item"><a class="nav-link text-black" href="#"><i class="far fa-user me-2"></i>Keamanan</a></li>
      <li class="nav-item"><a class="nav-link text-black" href="#"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
    </ul>
  </nav>
</aside>


