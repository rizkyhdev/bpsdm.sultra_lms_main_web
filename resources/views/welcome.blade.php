<!doctype html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" 
          crossorigin="anonymous">

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Calendar Styling CSS  -->
    <link rel="stylesheet" href="{{ asset('css/calendarstyling.css') }}">

    {{-- Preload Styling CSS --}}
     <link rel="stylesheet" href="{{ asset('css/preload.css') }}"> 

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" 
          integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" 
          integrity="sha512-pvVf9YqTQm1dNN1Ff6aM5AjlF6XK8u5GqZ1l3u1Z6r0QeN6cZsPZ4u8D1hZxS6H3c7hI3NkR6vH+G3BfQ4gBg==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" 
          integrity="sha512-IE3fgyN2F9X+G2V8k7eI5uLf7t0zH+1MS7FvjSYZJ2L4G7+5JQ9G1nU6X4uK0zA6Zf9hZ3u8xFh+QKpYz6xOg==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
</head>

  <body style="padding-top:80px;">
  {{-- Preload --}}
     <div id="preloader">
        <div class="preloader-logo">
            <img src="{{ asset('image/LOGO_AURA_1.png') }}" alt="Logo" />
        </div>
    </div>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg fixed-top" style="background-color:#FFFFFF;  box-shadow: 0px 5px 5px rgba(0.2, 0.2, 0.2, 0.2);">
  <div class="container">
    <div class="navbar-start" href="#" style="" alt="logo">
      <img src="{{ asset('image/LOGO_AURA.png') }}" alt="Logo" width="80" height="80" class="me-3 img-fluid" data-animate="animate__animated animate__zoomInDown">
       <img src="{{ asset('image/LOGO_AURA_1.png') }}" alt="Logo" width="80" height="80" class="me-3 img-fluid" data-animate="animate__animated animate__zoomInDown"> 
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#about">About</a>
        </li>
      </ul>
      <div>
        <a href="{{ url('/login') }}" class="btn btn-outline-primary me-2">Login</a>
         <a href="{{ url('/register') }}" class="btn btn-primary">Register</a>
      </div>
    </div>
  </div>
</nav>
{{-- Akhir Navbar --}}

{{-- Hero Carousel --}}
<div class="container-fluid px-5">
<div id="heroCarousel" class="carousel slide mx-auto my-5 shadow" data-bs-ride="carousel" style="max-width: 1500px; border-radius: 20px; overflow: hidden;">
  <div class="carousel-inner" style="height: 75vh;">

    {{-- Slide 1 --}}
    <div class="carousel-item active"  style="height: 100%; position: relative;">
      <div class="position-absolute top-0 start-0 w-100 h-100" style="background-color: rgba(0,0,0,0.5); z-index: 1;"></div>
      <div class="d-flex align-items-center justify-content-center text-white position-relative"
           style="background-image: url('{{ asset('image/slide1.jpeg') }}'); background-size: cover; background-position: center; height: 100%; z-index: 2;">
        <div class="container text-center">
          <h1 class="fw-bold mb-3 text-shadow" data-animate="animate__animated animate__fadeInDownBig">Selamat Datang</h1>
          <h5 class="text-shadow">Sistem Informasi Bangkom Teknis<br>Aparatur Unggul Responsif Adaptif</h5>
          <p class="mt-3 text-shadow">"Lorem ipsum dolor sit amet, consectetur adipiscing elit..."</p>
          <a href="#" class="btn btn-gradient px-4 py-2 mt-3" data-animate="animate__animated animate__fadeInUp">Register</a>
        </div>
      </div>
    </div>

    {{-- Slide 2 --}}
    <div class="carousel-item"  style="height: 100%;">
      <div class="d-flex align-items-center justify-content-center text-black"
           style="background-image: url('{{ asset('image/slide2.jpg') }}'); background-size: cover; background-position: center; height: 100%;">
        <div class="container text-center">
          <h1 class="fw-bold mb-3 animate__animated animate__fadeInDownBig">Pelatihan Terbaik</h1>
          <p class="mt-3">"Pelatihan kami dirancang untuk ASN dengan pendekatan aplikatif."</p>
          <a href="#" class="btn btn-info text-white mt-3">Lihat Kursus</a>
        </div>
      </div>
    </div>

    {{-- Slide 3 --}}
    <div class="carousel-item"  style="height: 100%;">
      <div class="d-flex align-items-center justify-content-center text-black"
           style="background-image: url('{{ asset('image/slide3.jpg') }}'); background-size: cover; background-position: center; height: 100%;">
        <div class="container text-center">
          <h1 class="fw-bold mb-3 animate__animated animate__fadeInDownBig">Gabung Sekarang</h1>
          <p class="mt-3">"Transformasi dimulai dari langkah pertama."</p>
          <a href="#" class="btn btn-info text-white mt-3">Gabung</a>
        </div>
      </div>
    </div>

  </div>

  {{-- Controls --}}
  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>

  {{-- Indicators --}}
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
  </div>
</div>
</div>
{{-- Akhir Hero Carousel --}}



{{-- About --}}
<div class="about mb-4" id="about">
  <div class="container">
    <div class="row">
      <div class="col-12 col-md-10 mx-auto text-center mb-5 mt-5" data-aos="fade-down" data-aos-duration="1000">
        <h2>Tentang Kami</h2>
      </div>
        <div class="col-12 col-md-10 mx-auto">
          <div class="ratio ratio-16x9">
              <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/UxlcBiRu9p8" allowfullscreen></iframe>
          </div>
        </div>
    </div>
  </div>
</div>
{{-- Akhir About --}}
<br><br>
{{-- Panduan Alur Sistem --}}
<div class="alur mb-8" id="alur">
  <div class="container">
    <!-- Judul -->
    <div class="row mb-4">
      <div class="col">
        <h2 class="fw-bold text-center mb-5">Panduan Alur Sistem</h2>
      </div>
    </div>

    <div class="row gx-5 align-items-center">
      <!-- Ilustrasi (kiri) -->
      <div class="col-lg-4 mb-4 mb-lg-0 d-flex justify-content-center">
        <div class="position-relative" style="max-width:300px;">
          <div style="background: linear-gradient(135deg,#6ec1e4,#f0f9ff); border-radius:16px; padding:1rem;">
            <img 
              src="{{ asset('image/asn.png') }}" 
              alt="Ilustrasi panduan" 
              class="img-fluid" 
              style="position: relative; z-index:1; transform: translateY(-10px);">
          </div>
        </div>
      </div>

      <!-- Langkah-langkah (kanan) -->
      <div class="col-lg-8">
        <div class="row gy-4">
          <!-- baris 1 -->
          <div class="col-md-4">
            <div class="d-flex align-items-start gap-3">
              <div class="step-number flex-shrink-0 d-flex justify-content-center align-items-center rounded" data-aos="fade-down-left" data-aos-duration="1000"
                   style="background:#0f4d1a; color:#fff; width:48px; height:48px; font-weight:600; font-size:1rem;">
                1
              </div>
              <div>
                <h6 class="mb-1">Langkah Pertama</h6>
                <p class="small mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="d-flex align-items-start gap-3">
              <div class="step-number flex-shrink-0 d-flex justify-content-center align-items-center rounded" data-aos="fade-down-left" data-aos-duration="1000"
                   style="background:#0f4d1a; color:#fff; width:48px; height:48px; font-weight:600; font-size:1rem;">
                2
              </div>
              <div>
                <h6 class="mb-1">Langkah Kedua</h6>
                <p class="small mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="d-flex align-items-start gap-3">
              <div class="step-number flex-shrink-0 d-flex justify-content-center align-items-center rounded" data-aos="fade-down-left" data-aos-duration="1000"
                   style="background:#0f4d1a; color:#fff; width:48px; height:48px; font-weight:600; font-size:1rem;">
                3
              </div>
              <div>
                <h6 class="mb-1">Langkah Ketiga</h6>
                <p class="small mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
              </div>
            </div>
          </div>

          <!-- baris 2 -->
          <div class="col-md-4">
            <div class="d-flex align-items-start gap-3">
              <div class="step-number flex-shrink-0 d-flex justify-content-center align-items-center rounded" data-aos="fade-down-left" data-aos-duration="1000"
                   style="background:#0f4d1a; color:#fff; width:48px; height:48px; font-weight:600; font-size:1rem;">
                4
              </div>
              <div>
                <h6 class="mb-1">Langkah Keempat</h6>
                <p class="small mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="d-flex align-items-start gap-3">
              <div class="step-number flex-shrink-0 d-flex justify-content-center align-items-center rounded" data-aos="fade-down-left" data-aos-duration="1000"
                   style="background:#0f4d1a; color:#fff; width:48px; height:48px; font-weight:600; font-size:1rem;">
                5
              </div>
              <div>
                <h6 class="mb-1">Langkah Kelima</h6>
                <p class="small mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="d-flex align-items-start gap-3">
              <div class="step-number flex-shrink-0 d-flex justify-content-center align-items-center rounded" data-aos="fade-down-left" data-aos-duration="1000"
                   style="background:#0f4d1a; color:#fff; width:48px; height:48px; font-weight:600; font-size:1rem;">
                6
              </div>
              <div>
                <h6 class="mb-1">Langkah Keenam</h6>
                <p class="small mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
{{-- Akhir Panduan Alur Sistem--}}
{{-- Article --}}
<div class="article py-5" id="article">
  <div class="container">
    <h1 class="text-center mb-5">Artikel</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4">

      {{-- Card 1 --}}
      <div class="col">
        <div class="card h-100 shadow-sm" style="transition: transform 0.3s ease-in-out;"
  onmouseover="this.style.transform='scale(1.05)'"
  onmouseout="this.style.transform='scale(1)'">
          <img src="{{ asset('image/image1.jpg') }}" class="card-img-top" alt="Artikel 1">
          <div class="card-body">
            <h5 class="card-title">Rapat Perdana Team Sobat Aura</h5>
            <p class="card-text">Tim Pengembang Sobat Aura Menggelar Rapat Perdana bersama Kabid Teknis Umum dan Fungsional</p>
          </div>
          <div class="card-footer bg-white border-top-0">
            <a href="#" class="btn btn-primary btn-sm">Baca Selengkapnya</a>
          </div>
        </div>
      </div>

      {{-- Card 2 --}}
      <div class="col">
        <div class="card h-100 shadow-sm" style="transition: transform 0.3s ease-in-out;"
  onmouseover="this.style.transform='scale(1.05)'"
  onmouseout="this.style.transform='scale(1)'">
          <img src="{{ asset('image/image2.jpg') }}" class="card-img-top" alt="Artikel 2">
          <div class="card-body">
            <h5 class="card-title">Hasil Rapat Perdana Team Sobat Aura</h5>
            <p class="card-text">Hasil Rapat Perdana Team Sobat Aura tentang fitur yang digunakan dalam sistem dan Deadline Pengerjaan Sistem</p>
          </div>
          <div class="card-footer bg-white border-top-0">
            <a href="#" class="btn btn-primary btn-sm">Baca Selengkapnya</a>
          </div>
        </div>
      </div>

      {{-- Card 3 --}}
      <div class="col">
        <div class="card h-100 shadow-sm" style="transition: transform 0.3s ease-in-out;"
  onmouseover="this.style.transform='scale(1.05)'"
  onmouseout="this.style.transform='scale(1)'">
          <img src="{{ asset('image/image3.jpg') }}" class="card-img-top" alt="Artikel 3">
          <div class="card-body">
            <h5 class="card-title">Rizky Leader Falling in Love ♡</h5>
            <p class="card-text">Rizky Leader Jatuh Cinta pada Rekrutan Anggota Terbaru</p>
          </div>
          <div class="card-footer bg-white border-top-0">
            <a href="#" class="btn btn-primary btn-sm">Baca Selengkapnya</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
{{-- Akhir Article --}}


{{-- Kalender Pembelajaran --}}
<section class="ftco-section"><div class="container my-4">
    <div class="row">
        <!-- Kolom Kiri: Kalender -->
        <div class="col-md-12 col-lg-4">
            <div class="elegant-calencar">
                <div class="wrap-header d-flex align-items-center">
                    <p id="reset" class="me-2 btn btn-sm btn-outline-secondary">Date</p>
                    <div id="header" class="p-0 w-100 d-flex justify-content-between">
                        <div class="pre-button"><i class="fa fa-chevron-left"></i></div>
                        <div class="head-info text-center">
                            <div class="head-day"></div>
                            <div class="head-month"></div>
                        </div>
                        <div class="next-button"><i class="fa fa-chevron-right"></i></div>
                    </div>
                </div>
                <div class="calendar-wrap">
                    <table id="calendar" class="text-center">
                        <thead>
                            <tr>
                                <th>Sun</th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < 6; $i++)
                                <tr>
                                    @for ($j = 0; $j < 7; $j++)
                                        <td class="calendar-day" data-date=""></td>
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Card Pelatihan -->
        <div class="col-md-8">
            <h4 class="mb-3">Daftar Pelatihan</h4>
            <div id="pelatihan-list" class="row g-3">
                <p class="text-muted">Klik tanggal di kalender untuk melihat pelatihan.</p>
            </div>
        </div>
    </div>
</div>

	</section>
  <script>
    // Data pelatihan dari Laravel
    const pelatihanData = @json($pelatihan); 

    // Event klik tanggal
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("calendar-day") && e.target.dataset.date) {
            const selectedDate = e.target.dataset.date;
            renderPelatihan(selectedDate);
        }
    });

    // Fungsi render card pelatihan
    function renderPelatihan(date) {
        const container = document.getElementById("pelatihan-list");
        container.innerHTML = ""; // Bersihkan

        const filtered = pelatihanData.filter(item => item.date === date);

        if (filtered.length === 0) {
            container.innerHTML = `<div class="col-12"><p class="text-muted">Tidak ada pelatihan di tanggal ini.</p></div>`;
            return;
        }

        filtered.forEach(item => {
            container.innerHTML += `
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">${item.title}</h5>
                            <p class="card-text">${item.description}</p>
                            <p class="mb-1"><i class="fa fa-clock"></i> ${item.duration}</p>
                            <p class="mb-2"><i class="fa fa-signal"></i> ${item.level}</p>
                            <a href="${item.url}" class="btn btn-warning btn-sm">Start Learning</a>
                        </div>
                    </div>
                </div>
            `;
        });
    }
</script>
{{-- Kalender Pembelajaran --}}


<!--  Struktur Organisasi -->
    <section class="meet-our-team py-5 py-lg-11 py-xl-12">
      <div class="container">
        <div class="d-flex flex-column gap-5 gap-xl-11">
          <div class="row">
        <div class="col-12">
          <div class="text-center" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
            <h2 class="mb-5">Meet our team</h2>
          </div>
        </div>
      </div>
          <div class="row">
            <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
              <div class="meet-team d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="100"
                data-aos-duration="1000">
                <div class="meet-team-img position-relative overflow-hidden">
                  <img src="{{ asset('image/kepala-badan.jpg') }}" alt="team-img" class="img-fluid w-100">
                  <div class="meet-team-overlay p-7 d-flex flex-column justify-content-end">
                    <ul class="social list-unstyled mb-0 hstack gap-2 justify-content-end">
                      <li><a href="#!" class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><i class="fa-brands fa-square-facebook"></i></a></li>
                      <li><a href="#!" class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><i class="fa-brands fa-instagram"></i></a></li>
                    </ul>
                  </div>
                </div>
                <div class="meet-team-details">
                  <h4 class="mb-0">kepala-badan</h4>
                  <p class="mb-0">kepala-badan</p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
              <div class="meet-team d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="200"
                data-aos-duration="1000">
                <div class="meet-team-img position-relative overflow-hidden">
                  <img src="{{ asset('image/kaban.JPG') }}" alt="team-img" class="img-fluid w-100">
                  <div class="meet-team-overlay p-7 d-flex flex-column justify-content-end">
                    <ul class="social list-unstyled mb-0 hstack gap-2 justify-content-end">
                      <li><a href="#!" class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><i class="fa-brands fa-square-facebook"></i></a></li>
                      <li><a href="#!" class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><i class="fa-brands fa-instagram"></i></a></li>
                    </ul>
                  </div>
                </div>
                <div class="meet-team-details">
                  <h4 class="mb-0">kaban</h4>
                  <p class="mb-0">kaban</p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
              <div class="meet-team d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="300"
                data-aos-duration="1000">
                <div class="meet-team-img position-relative overflow-hidden">
                  <img src="{{ asset('image/sekban.png') }}" alt="team-img" class="img-fluid w-100">
                  <div class="meet-team-overlay p-7 d-flex flex-column justify-content-end">
                    <ul class="social list-unstyled mb-0 hstack gap-2 justify-content-end">
                      <li><a href="#!" class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><i class="fa-brands fa-square-facebook"></i></a></li>
                      <li><a href="#!" class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><i class="fa-brands fa-instagram"></i></a></li>
                    </ul>
                  </div>
                </div>
                <div class="meet-team-details">
                  <h4 class="mb-0">sekban</h4>
                  <p class="mb-0">sekban</p>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-xl-3 mb-7 mb-xl-0">
              <div class="meet-team d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="400"
                data-aos-duration="1000">
                <div class="meet-team-img position-relative overflow-hidden">
                  <img src="{{ asset('image/kadis.png') }}" alt="team-img" class="img-fluid w-100">
                  <div class="meet-team-overlay p-7 d-flex flex-column justify-content-end">
                    <ul class="social list-unstyled mb-0 hstack gap-2 justify-content-end">
                      <li><a href="#!" class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><i class="fa-brands fa-square-facebook"></i></a></li>
                      <li><a href="#!" class="btn bg-white p-2 round-45 rounded-circle hstack justify-content-center"><i class="fa-brands fa-instagram"></i></a></li>
                    </ul>
                  </div>
                </div>
                <div class="meet-team-details">
                  <h4 class="mb-0">kadis</h4>
                  <p class="mb-0">kadis</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
{{-- Akhir Section Struktur Organisasi --}}

 <!-- Footer -->
  <footer
          class="text-center text-lg-start text-black"
          style="background-color: #88d3e1"
          >
    <div class="container p-4 pb-0">
  <!-- Section: Links -->
  <section class="">
    <!--Grid row-->
    <div class="row">
      <!-- Grid column: Maps -->
      <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
        <h6 class="text-uppercase mb-4 font-weight-bold">
          Lokasi Kantor
        </h6>
        <div class="ratio ratio-4x3">
         <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1990.124375520206!2d122.46603083858034!3d-3.9691188990011566!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d98f3b6f79f5443%3A0x1abd21e5a25d0c41!2sBPSDM%20Propinsi%20Sulawesi%20Tenggara!5e0!3m2!1sid!2sid!4v1754266822957!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
      <!-- Grid column -->

      <hr class="w-100 clearfix d-md-none" />

      <!-- Grid column: Contact -->
      <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
        <h6 class="text-uppercase mb-4 font-weight-bold">Kontak dan Alamat</h6>
        <p><i class="fas fa-home mr-3"></i>Jalan Chairil Anwar No. 8 A Puuwatu</p>
        <p><i class="fas fa-envelope mr-3"></i>email: BPSDMPROVSULTRA@gmail.com</p>
        <p><i class="fas fa-phone mr-3"></i>Telp: 3124061</p>
        <p><i class="fas fa-print mr-3"></i>Fax: 312595</p>
      </div>
      <!-- Grid column -->
    </div>
    <!--Grid row-->
  </section>
  <!-- Section: Links -->

  <hr class="my-3">

  <!-- Section: Copyright -->
  <section class="p-3 pt-0">
    <div class="row d-flex align-items-center">
      <div class="col-md-7 col-lg-8 text-center text-md-start">
        <div class="p-3">
          © SOBAT AURA 2025
          <a class="text-white" href="http://bpsdmprovsultra.home.blog/">BPSDM PEMPROV SULTRA</a>
        </div>
      </div>

      <div class="col-md-5 col-lg-4 text-center text-md-end">
        <a class="btn btn-outline-light btn-floating m-1" role="button"><i class="fab fa-facebook-f" href="https://www.facebook.com/bpsdm.sultra.3"></i></a>
        <a class="btn btn-outline-light btn-floating m-1" role="button"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </section>
  <!-- Section: Copyright -->
</div>
<!-- Grid container -->
  </footer>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-/xUj+QAT/7rjG5Qbqseb3CidRub9pzQZAlPfMwVz6I6+w4n1vCtbmZh9rqx8uxFZ" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-YlHf5yZ5QZ4VdHz6YQFQFQJwQeN5e8sZ/6IDdnh3oX1N1pAVccahJgN9zeps2sonMSKcwV5Y8ZndKyVwU9g0Fg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <script>
    window.pelatihanData = @json($pelatihan);
</script>
  <script src="{{asset('js/jquery.min.js')}}"></script>
  <script src="{{asset('js/bootstrap.min.js')}}"></script>
  <script src="{{asset('js/main.js')}}"></script>
  <script src="{{asset('js/popper.js')}}"></script>
   <script src="{{asset('js/preload.js')}}"></script>

  <script>
    AOS.init();
  </script>
  </body>
</html>