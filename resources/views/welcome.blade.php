<!doctype html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sobat AURA</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" 
          crossorigin="anonymous">
 
    <!-- Vite  -->
    @vite(['resources/js/app.js', 'resources/css/app.css'])
  
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" 
          integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
     <link rel="icon" href="{{ asset('image/LOGO AURA 1.png') }}" type="image/png">
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
      <img src="{{ asset('image/LOGO_AURA.png') }}" alt="Logo" width="80" height="80" class="logo-navbar me-3 img-fluid">
       <img src="{{ asset('image/LOGO_AURA_1.png') }}" alt="Logo" width="80" height="80" class="logo-navbar img-fluid"> 
    </div>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent" data-aos="fade-down" data-aos-delay="200" data-aos-duration="1000">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="/">Beranda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#alur">Panduan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('courses.index') }}">Pelatihan</a>
        </li>
         <li class="nav-item">
          <a class="nav-link" href="#article">Artikel</a>
        </li>
      </ul>
      <div>
        <a href="{{ url('/login') }}" class="btn btn-outline-info me-2">Login</a>
         <a href="{{ url('/register') }}" class="btn btn-outline-info">Register</a>
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
      <div class="carousel-item active position-relative" style="height: 100%;">
        <div class="hero-overlay"></div>
        <div class="d-flex align-items-center justify-content-center text-white position-relative"
             style="background-image: url('{{ asset('image/slide1.jpeg') }}'); background-size: cover; background-position: center; height: 100%;">
          <div class="container text-center">
            <h1 class="hero-title" data-splitting="chars">Selamat Datang</h1>
            <h5 class="hero-sub"  data-splitting="chars">Sistem Informasi Bangkom Teknis<br>Aparatur Unggul Responsif Adaptif</h5>
            <p class="mt-3 text-shadow">"Lorem ipsum dolor sit amet, consectetur adipiscing elit..."</p>
            <a href="#" class="btn btn-gradient px-4 py-2 mt-3">Lihat Kursus</a>
          </div>
        </div>
      </div>

      {{-- Slide 2 --}}
      <div class="carousel-item position-relative" style="height: 100%;">
        <div class="hero-overlay"></div>
        <div class="d-flex align-items-center justify-content-center text-white position-relative"
             style="background-image: url('{{ asset('image/slide2.jpg') }}'); background-size: cover; background-position: center; height: 100%;">
          <div class="container text-center">
            <h1 class="hero-title" data-splitting="chars">Pelatihan Terbaik</h1>
            <h5 class="hero-sub"  data-splitting="chars">Pelatihan kami dirancang untuk ASN dengan pendekatan aplikatif.</h5>
            <a href="#" class="btn btn-gradient px-4 py-2 mt-3">Lihat Kursus</a>
          </div>
        </div>
      </div>

      {{-- Slide 3 --}}
      <div class="carousel-item position-relative" style="height: 100%;">
        <div class="hero-overlay"></div>
        <div class="d-flex align-items-center justify-content-center text-white position-relative"
             style="background-image: url('{{ asset('image/slide3.jpg') }}'); background-size: cover; background-position: center; height: 100%;">
          <div class="container text-center">
            <h1 class="hero-title" data-splitting="chars">Gabung Sekarang</h1>
            <h5 class="hero-sub"  data-splitting="chars">"Transformasi dimulai dari langkah pertama."</h5>
            <a href="#" class="btn btn-gradient px-4 py-2 mt-3">Lihat Kursus</a>
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
<div class="about-section py-5" id="about">
  <div class="container">
    <div class="row">
      <!-- Judul -->
      <div class="col-12 col-md-10 mx-auto text-center mb-4" 
           data-aos="fade-down" 
           data-aos-delay="200" 
           data-aos-duration="1000">
        <h2 class="about-title">Tentang Kami</h2>
      </div>

      <!-- Video -->
      <div class="col-12 col-md-10 mx-auto">
        <div class="video-wrapper ratio ratio-16x9" data-aos="zoom-in" data-aos-delay="300">
          <iframe class="embed-responsive-item" 
                  src="https://www.youtube.com/embed/UxlcBiRu9p8" 
                  allowfullscreen></iframe>
        </div>
      </div>
    </div>
  </div>
</div>
{{-- Akhir About --}}

<br><br>


{{-- Panduan Alur Sistem --}}
<div class="alur mb-5" id="alur">
  <div class="container">
    <!-- Judul -->
    <div class="row mb-5">
      <div class="col">
        <h2 class="fw-bold text-center mb-5" data-aos="fade-down" data-aos-delay="200" data-aos-duration="1000">
          Panduan Alur Sistem
        </h2>
      </div>
    </div>

    <div class="row gx-5 align-items-center">
      <!-- Ilustrasi -->
      <div class="col-lg-4 mb-4 mb-lg-0 d-flex justify-content-center">
        <div class="position-relative" style="max-width:300px;">
          <div class="illustration-box">
            <img src="{{ asset('image/asn.png') }}" alt="Ilustrasi panduan" class="img-fluid">
          </div>
        </div>
      </div>

      <!-- Langkah-langkah -->
      <div class="col-lg-8">
        <div class="row gy-5 position-relative steps-container">
          @foreach ([
            'Langkah Pertama', 'Langkah Kedua', 'Langkah Ketiga',
            'Langkah Keempat', 'Langkah Kelima', 'Langkah Keenam'
          ] as $index => $title)
          <div class="col-md-4 step-wrapper" data-aos="fade-up" data-aos-delay="{{ $index * 150 }}" data-aos-duration="800">
            <div class="d-flex align-items-start gap-3 step-item">
              <div class="step-number">{{ $index + 1 }}</div>
              <div>
                <h6 class="mb-1">{{ $title }}</h6>
                <p class="small mb-0">
                  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                </p>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
{{-- Akhir Panduan Alur Sistem --}}


{{-- Kalender Pembelajaran --}}
<section class="ftco-section mt-5 py-5">
  <h1 class="text-center mb-5 mt-5" data-aos="fade-down" data-aos-delay="200" data-aos-duration="1000">
    Kalender dan Daftar Pelatihan
  </h1>

  <div class="container">
    <div class="calendar-section-wrapper p-4 p-md-5">
      <div class="row">
        <!-- Kolom Kiri: Kalender -->
        <div class="col-md-12 col-lg-4 mt-4" data-aos="fade-right" data-aos-delay="200" data-aos-duration="1000">
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
        <div class="col-md-8 mt-4">
          <h4 class="mb-3">Daftar Pelatihan</h4>
          <div id="pelatihan-list" class="row g-3">
            <p class="text-muted">Klik tanggal di kalender untuk melihat pelatihan.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
const pelatihanData = @json($pelatihan);

document.addEventListener("click", function (e) {
    if (e.target.classList.contains("calendar-day") && e.target.dataset.date) {
        renderPelatihan(e.target.dataset.date);
    }
});

function renderPelatihan(date) {
    const container = document.getElementById("pelatihan-list");
    container.innerHTML = "";

    const filtered = pelatihanData.filter(item => item.date === date);

    if (filtered.length === 0) {
        container.innerHTML = `<div class="col-12"><p class="text-muted">Tidak ada pelatihan di tanggal ini.</p></div>`;
        return;
    }

    const levelColors = {
        "Beginner": "#1E90FF",
        "Intermediate": "#FFC107",
        "Advanced": "#2E8B57"
    };

    let cardsHTML = "";
    filtered.forEach(item => {
        const headerColor = levelColors[item.level] || "#6c757d";
        cardsHTML += `
            <div class="col-md-4">
                <div class="cardCalendar shadow-sm border-0 rounded-4 h-100">
                    <div class="cardCalendar-header text-white fw-bold p-3" 
                        style="background-color: ${headerColor}; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                        ${item.title}
                    </div>
                    <div class="cardCalendar-body p-3">
                        <p class="text-muted">${item.description}</p>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fa fa-clock me-2 text-danger"></i> ${item.duration}
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fa fa-signal me-2 text-warning"></i> ${item.level}
                        </div>
                        <div class="text-warning mb-2">
                            ★★★★★ <small class="text-muted">(5.0)</small>
                        </div>
                        <a href="${item.url}" class="btn btn-outline-warning w-100 rounded-pill">Start Learning</a>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = cardsHTML;

   if (typeof animatePelatihanCards === "function") {
    animatePelatihanCards();
}
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
            <div class="col-md-6 col-xl-3 mb-7 mb-xl-0" data-aos="fade-up" data-aos-delay="100">
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
            <div class="col-md-6 col-xl-3 mb-7 mb-xl-0" data-aos="fade-up" data-aos-delay="200">
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
            <div class="col-md-6 col-xl-3 mb-7 mb-xl-0" data-aos="fade-up" data-aos-delay="300">
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
            <div class="col-md-6 col-xl-3 mb-7 mb-xl-0" data-aos="fade-up" data-aos-delay="400">
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

{{-- Article --}}
<div class="article py-5" id="article">
  <div class="container">
    <h1 class="text-center mb-5">Artikel</h1>

    <!-- Swiper -->
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">

        {{-- Card 1 --}}
        <div class="swiper-slide">
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
        <div class="swiper-slide">
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
        <div class="swiper-slide">
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
      
        {{-- Card 4 --}}
        <div class="swiper-slide">
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

        {{-- Card 5 --}}
        <div class="swiper-slide">
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

      <!-- Navigasi & Pagination -->
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>
    <div class="swiper-pagination"></div>
  </div>
</div>
{{-- Akhir Article --}}
<footer class="footer-custom">
  <div class="container p-4 pb-0">
    <section>
      <div class="row">
        <!-- Lokasi -->
        <div class="col-md-4 mb-4">
          <h6 class="text-uppercase fw-bold mb-4">Lokasi Kantor</h6>
          <div class="footer-map ratio ratio-4x3">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1990.124375520206!2d122.46603083858034!3d-3.9691188990011566!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d98f3b6f79f5443%3A0x1abd21e5a25d0c41!2sBPSDM%20Propinsi%20Sulawesi%20Tenggara!5e0!3m2!1sid!2sid!4v1755157089327!5m2!1sid!2sid" allowfullscreen="" loading="lazy"></iframe>
          </div>
        </div>

        <!-- Kontak -->
        <div class="col-md-4 mb-4">
          <h6 class="text-uppercase fw-bold mb-4">Kontak dan Alamat</h6>
          <p><i class="fas fa-home me-2"></i> Jalan Chairil Anwar No. 8 A Puuwatu</p>
          <p><i class="fas fa-envelope me-2"></i> 
            <a href="mailto:BPSDMPROVSULTRA@gmail.com">BPSDMPROVSULTRA@gmail.com</a>
          </p>
          <p><i class="fas fa-phone me-2"></i> Telp: 3124061</p>
          <p><i class="fas fa-print me-2"></i> Fax: 312595</p>
        </div>

        <!-- Sosial Media -->
        <div class="col-md-4 mb-4">
          <h6 class="text-uppercase fw-bold mb-4">Ikuti Kami</h6>
          <div class="footer-social">
            <a href="https://www.facebook.com/bpsdm.sultra.3" class="social-icon"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
      </div>
    </section>

    <hr class="my-3 border-light">

    <!-- Copyright -->
    <section class="text-center py-2">
      © SOBAT AURA 2025 <a href="http://bpsdmprovsultra.home.blog/" class="fw-bold">BPSDM PEMPROV SULTRA</a>
    </section>
  </div>
</footer>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-/xUj+QAT/7rjG5Qbqseb3CidRub9pzQZAlPfMwVz6I6+w4n1vCtbmZh9rqx8uxFZ" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <script>
    window.pelatihanData = @json($pelatihan);
</script>
  </body>
</html>