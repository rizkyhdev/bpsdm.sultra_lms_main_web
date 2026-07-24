<!doctype html>
<html lang="en">
 <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Primary Meta Tags -->
    <title>Sobat ASR - Portal Pengembangan Kompetensi ASN BPSDM Sultra</title>
    <meta name="title" content="Sobat ASR - Portal Pengembangan Kompetensi ASN BPSDM Sultra">
    <meta name="description" content="Portal resmi Sobat ASR (Sistem Informasi Pengembangan Kompetensi Aparatur Sigap teRintegrasi) BPSDM Sulawesi Tenggara. Platform pembelajaran mandiri terintegrasi untuk meningkatkan profesionalisme ASN secara digital.">
    <meta name="keywords" content="BPSDM Sultra, Sobat ASR, Pelatihan ASN, Sultra, LMS ASN, Kompetensi ASN, Sulawesi Tenggara, Pengembangan Kompetensi">
    <meta name="author" content="BPSDM Provinsi Sulawesi Tenggara">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Sobat ASR - Portal Pengembangan Kompetensi ASN Sultra">
    <meta property="og:description" content="Tingkatkan kompetensi ASN Sultra melalui platform pembelajaran mandiri terintegrasi Sobat ASR. Akses pelatihan kapan saja dan di mana saja.">
    <meta property="og:image" content="{{ asset('image/LOGO_AURA.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="Sobat ASR - Portal Pengembangan Kompetensi ASN Sultra">
    <meta property="twitter:description" content="Tingkatkan kompetensi ASN Sultra melalui platform pembelajaran mandiri terintegrasi Sobat ASR. Akses pelatihan kapan saja dan di mana saja.">
    <meta property="twitter:image" content="{{ asset('image/LOGO_AURA.png') }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url('/') }}">

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
     <link rel="icon" href="{{ asset('image/LOGO_AURA_1.png') }}" type="image/png">
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
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
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
        <a href="{{ route('login') }}" class="btn btn-outline-info me-2">Login</a>
         <a href="{{ route('register') }}" class="btn btn-outline-info">Register</a>
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
            <h1 class="hero-title" data-splitting="chars">Selamat Datang di Sobat ASR</h1>
            <h5 class="hero-sub" data-splitting="chars">Sistem Informasi Pengembangan Kompetensi Aparatur Sigap teRintegrasi</h5>
            <p class="mt-3 text-shadow">Platform Pengembangan Kompetensi Mandiri Terintegrasi untuk Aparatur Sipil Negara di Sulawesi Tenggara.</p>
            <a href="#calendar" class="btn btn-gradient px-4 py-2 mt-3">Lihat Pelatihan</a>
          </div>
        </div>
      </div>

      {{-- Slide 2 --}}
      <div class="carousel-item position-relative" style="height: 100%;">
        <div class="hero-overlay"></div>
        <div class="d-flex align-items-center justify-content-center text-white position-relative"
             style="background-image: url('{{ asset('image/slide2.jpg') }}'); background-size: cover; background-position: center; height: 100%;">
          <div class="container text-center">
            <h1 class="hero-title" data-splitting="chars">Pelatihan Kompetensi Teknis</h1>
            <h5 class="hero-sub" data-splitting="chars">Kurikulum yang dirancang khusus untuk menghadapi tantangan birokrasi masa kini secara profesional.</h5>
            <a href="#calendar" class="btn btn-gradient px-4 py-2 mt-3">Lihat Pelatihan</a>
          </div>
        </div>
      </div>

      {{-- Slide 3 --}}
      <div class="carousel-item position-relative" style="height: 100%;">
        <div class="hero-overlay"></div>
        <div class="d-flex align-items-center justify-content-center text-white position-relative"
             style="background-image: url('{{ asset('image/slide3.jpg') }}'); background-size: cover; background-position: center; height: 100%;">
          <div class="container text-center">
            <h1 class="hero-title" data-splitting="chars">Sertifikasi Digital</h1>
            <h5 class="hero-sub" data-splitting="chars">Dapatkan pengakuan formal atas kompetensi Anda secara instan setelah menyelesaikan pelatihan.</h5>
            <a href="#calendar" class="btn btn-gradient px-4 py-2 mt-3">Lihat Pelatihan</a>
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
          @php
            $steps = [
              ['title' => 'Registrasi Mandiri', 'desc' => 'Pendaftaran akun menggunakan NIP dan data identitas ASN yang valid di portal Sobat ASR.'],
              ['title' => 'Verifikasi Profil', 'desc' => 'Unggah Surat Tugas atau bukti status ASN untuk memvalidasi kelayakan pendaftaran pelatihan.'],
              ['title' => 'Eksplorasi Katalog', 'desc' => 'Pilih jenis pelatihan teknis yang sesuai dengan kebutuhan pengembangan kompetensi Anda.'],
              ['title' => 'Pembelajaran Digital', 'desc' => 'Pelajari materi melalui modul video dan dokumen secara mandiri sesuai jadwal yang tersedia.'],
              ['title' => 'Evaluasi Belajar', 'desc' => 'Selesaikan kuis penilaian pada setiap modul untuk memastikan penguasaan substansi materi pelatihan.'],
              ['title' => 'Sertifikat Digital', 'desc' => 'Unduh sertifikat kelulusan secara mandiri setelah seluruh progres pembelajaran mencapai 100%.'],
            ];
          @endphp
          @foreach ($steps as $index => $step)
          <div class="col-md-4 step-wrapper" data-aos="fade-up" data-aos-delay="{{ $index * 150 }}" data-aos-duration="800">
            <div class="d-flex align-items-start gap-3 step-item">
              <div class="step-number">{{ $index + 1 }}</div>
              <div>
                <h6 class="mb-1 fw-bold">{{ $step['title'] }}</h6>
                <p class="small mb-0 text-muted">
                  {{ $step['desc'] }}
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

document.addEventListener("click", function (e) {
    if (e.target.classList.contains("calendar-day") && e.target.dataset.date) {
        renderPelatihan(e.target.dataset.date);
    }
});

function renderPelatihan(date) {
    const container = document.getElementById("pelatihan-list");
    container.innerHTML = "";

    const filtered = (window.pelatihanData || []).filter(item => item.date === date);

    if (filtered.length === 0) {
        container.innerHTML = `<div class="col-12"><p class="text-muted">Tidak ada pelatihan di tanggal ini.</p></div>`;
        return;
    }

    const levelColors = {
        "Beginner": "#1E90FF",
        "Intermediate": "#FFC107",
        "Advanced": "#2E8B57",
        "Umum": "#6f42c1"
    };

    let cardsHTML = "";
    filtered.forEach(item => {
        const headerColor = levelColors[item.level] || "#6c757d";
        const endTimeStr = item.end_time ? ` - ${item.end_time}` : '';
        const endDateStr = item.end_date && item.end_date !== item.date ? `<br><small class="text-white">s/d ${item.end_date}</small>` : '';
        cardsHTML += `
            <div class="col-md-4">
                <div class="cardCalendar shadow-sm border-0 rounded-4 h-100">
                    <div class="cardCalendar-header text-white fw-bold p-3 d-flex justify-content-between align-items-center" 
                        style="background-color: ${headerColor}; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                        <div>
                            ${item.title}
                            ${endDateStr}
                        </div>
                        <span class="badge bg-white text-dark small">${item.level}</span>
                    </div>
                    <div class="cardCalendar-body p-3">
                        <p class="text-muted small mb-3">${item.description}</p>
                        <div class="d-flex align-items-center mb-2 small">
                            <i class="fa fa-clock me-2 text-primary"></i> 
                            <strong>${item.start_time}${endTimeStr}</strong>
                        </div>
                        <div class="d-flex align-items-center mb-2 small">
                            <i class="fa fa-hourglass-half me-2 text-info"></i> ${item.duration}
                        </div>
                        <div class="text-warning mb-3">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <small class="text-muted ms-1">(5.0)</small>
                        </div>
                        <a href="${item.url}" class="btn btn-outline-primary btn-sm w-100 rounded-pill" 
                           title="{{ Auth::guest() ? 'Silakan login untuk melihat detail' : '' }}">
                           Lihat Detail
                        </a>
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


    <section class="meet-our-team py-5 py-lg-11 py-xl-12" id="pimpinan">
      <div class="container">
        <div class="d-flex flex-column gap-4 gap-xl-5">
          <div class="row">
            <div class="col-12">
              <div class="text-center" data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                <h2 class="mb-3 fw-bold text-gradient">Struktur Pimpinan & Organisasi</h2>
                <p class="text-muted fs-5">Mengenal lebih dekat jajaran pimpinan dan struktur organisasi BPSDM Provinsi Sulawesi Tenggara.</p>
              </div>
            </div>
          </div>
          
          <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="zoom-in" data-aos-delay="200" data-aos-duration="800">
              <div class="card border-0 shadow-lg rounded-4 overflow-hidden position-relative pimpinan-cta-card bg-white">
                <!-- Background decoration -->
                <div class="position-absolute top-0 end-0 p-5 opacity-10 pointer-events-none" style="transform: translate(20%, -20%);">
                  <i class="fas fa-network-wired text-primary" style="font-size: 15rem;"></i>
                </div>
                
                <div class="card-body p-5 text-center position-relative z-index-1">
                  <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle shadow-sm" style="width: 80px; height: 80px; background: linear-gradient(135deg, #0d6efd, #0a58ca);">
                      <i class="fas fa-sitemap fs-2"></i>
                    </div>
                  </div>
                  <h3 class="fw-bold mb-3 text-dark">Struktur Organisasi BPSDM Sultra</h3>
                  <p class="text-secondary mb-4 fs-5 mx-auto" style="max-width: 600px; line-height: 1.6;">
                    Untuk informasi terkini mengenai bagan susunan organisasi dan profil lengkap jajaran pimpinan BPSDM Provinsi Sulawesi Tenggara, silakan kunjungi portal resmi kami.
                  </p>
                  <a href="https://bpsdmsultra.my.id/struktur-organisasi" target="_blank" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow-sm btn-hover-effect fw-semibold d-inline-flex align-items-center gap-2">
                    <span>Lihat Struktur Lengkap</span>
                    <i class="fas fa-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <style>
      .pimpinan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
      }
      .text-gradient {
        background: linear-gradient(45deg, #0d6efd, #0dcaf0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
      }
    </style>
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
              <h5 class="card-title">Rapat Perdana Team Sobat ASR</h5>
              <p class="card-text">Tim Pengembang Sobat ASR Menggelar Rapat Perdana bersama Kabid Teknis Umum dan Fungsional</p>
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
              <h5 class="card-title">Hasil Rapat Perdana Team Sobat ASR</h5>
              <p class="card-text">Hasil Rapat Perdana Team Sobat ASR tentang fitur yang digunakan dalam sistem dan Deadline Pengerjaan Sistem</p>
            </div>
            <div class="card-footer bg-white border-top-0">
              <a href="#" class="btn btn-primary btn-sm">Baca Selengkapnya</a>
            </div>
          </div>
        </div>

        {{-- Card 3: Majalah Insight ASN --}}
        <div class="swiper-slide">
          <div class="card h-100 shadow-sm" style="transition: transform 0.3s ease-in-out;"
               onmouseover="this.style.transform='scale(1.05)'"
               onmouseout="this.style.transform='scale(1)'">
            <img src="{{ asset('image/banner_insight_asn.png') }}" class="card-img-top" alt="Majalah Insight ASN">
            <div class="card-body">
              <h5 class="card-title">Edisi Perdana: Majalah Insight ASN</h5>
              <p class="card-text">Temukan inspirasi, panduan teknologi informasi, dan ragam inovasi layanan digital pemerintahan di Sulawesi Tenggara melalui edisi perdana Majalah Insight ASN.</p>
            </div>
            <div class="card-footer bg-white border-top-0">
              <a href="https://bpsdmsultra.my.id/e-journal" class="btn btn-primary btn-sm">Baca Selengkapnya</a>
            </div>
          </div>
        </div>
      
        {{-- Card 4 --}}
        <!-- <div class="swiper-slide">
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
        </div> -->

        {{-- Card 5 --}}
        <!-- <div class="swiper-slide">
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
        </div> -->


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
            <!-- <a href="https://www.facebook.com/bpsdm.sultra.3" class="social-icon"><i class="fab fa-facebook-f"></i></a> -->
            <a href="https://www.instagram.com/bpsdmsultra" class="social-icon"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
      </div>
    </section>

    <hr class="my-3 border-light">

    <!-- Copyright -->
    <section class="text-center py-2">
      © SOBAT ASR 2025 <a href="https://bpsdmsultra.my.id/" class="fw-bold">BPSDM PEMPROV SULTRA</a>
    </section>
  </div>
</footer>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-/xUj+QAT/7rjG5Qbqseb3CidRub9pzQZAlPfMwVz6I6+w4n1vCtbmZh9rqx8uxFZ" crossorigin="anonymous"></script>
  <script>
    // Global pelatihan data for the calendar script
    window.pelatihanData = @json($pelatihan ?? []);
  </script>
  </body>
</html>