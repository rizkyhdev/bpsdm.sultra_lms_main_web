<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Selamat Datang Peserta</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet" />
  @vite('resources/css/app.css')
  <script src="https://unpkg.com/lucide@latest"></script>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
  <style>
    body {
      font-family: "Montserrat", sans-serif;
    }
    .scrollbar-hide::-webkit-scrollbar {
      display: none;
    }
    .scrollbar-hide {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
  </style>
</head>

<body class="font-sans">
  <!-- Header -->
  <header class="relative bg-cover bg-center pb-0" style="background-image: url('image/jemmbatan.png')">
    <div class="absolute inset-0 bg-green-900 bg-opacity-60"></div>

   <div class="relative z-10 flex flex-col min-h-screen justify-start">
      <!-- Logo & Nav -->
      <nav class="flex justify-between items-center px-6 py-4 text-white">
        <a href="index.html" class="flex gap-2 items-center">
          <div class="flex gap-2 items-center">
            <img src="image/LOGOGRAM SOBAT ASR.png" alt="Logo 1" class="h-20" />
            <img src="image/LOGOFONT SOBAT ASR.png" alt="Logo 2" class="h-20" />
          </div>
        </a>
        <ul class="flex gap-4 text-sm">
          <li>
            <a
              href="index.html"
              class="text-green-400 font-bold border-b-2 border-green-400"
            >
              Beranda
            </a>
          </li>
          <li>
            <a
              href="/nurul/about.html"
              class="hover:text-green-400 active:text-green-400 transition duration-300"
            >
              About
            </a>
          </li>
          <li class="border-l border-white pl-4">
            <a
              href="#"
              class="hover:text-green-400 active:text-green-400 transition duration-300"
            >
              Log In
            </a>
          </li>
          <li>
            <a
              href="#"
              class="hover:text-green-400 active:text-green-400 transition duration-300"
            >
              Sign Up
            </a>
          </li>
        </ul>
      </nav>

      <!-- Hero Section -->
      <div class="flex flex-col justify-center items-center text-white pt-4 pb-4 px-4 grow">
        <div class="text-left max-w-4xl">
          <h1 class="text-7xl font-bold mb-4 tracking-tight leading-tight">Selamat Datang</h1>
          <p class="text-2xl font-medium leading-snug">Sistem Informasi Bangkom Teknis<br />Aparatur Sigap Terintegrasi</p>
          <div class="flex flex-col sm:flex-row justify-center gap-4">
  <button class="relative group inline-block px-6 py-3 rounded-full bg-white text-black font-semibold overflow-hidden shadow-md transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-xl">
    <span class="absolute inset-0 bg-gradient-to-r from-green-400 to-green-600 opacity-0 group-hover:opacity-100 transition-all duration-300 ease-in-out"></span>
    <span class="relative z-10 group-hover:text-white transition duration-300">SELAMAT DATANG DI WEBSITE</span>
  </button>
</div>
        </div>
      </div>

      <!-- Quotes -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-white px-10 pt-10 pb-20 text-sm">
        <div>
          <p>"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua..."</p>
          <p>"Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."</p>
          <p>"Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur."</p>
        </div>
        <div>
          <p>"Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."</p>
          <p>"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium."</p>
          <p>"Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo."</p>
        </div>
      </div>
    </div>
  </header>

  <!-- Sambutan Section -->
  <section class="relative flex flex-col lg:flex-row items-stretch justify-center pt-0 pb-0">
    <div class="w-full lg:w-1/2 bg-white flex flex-col items-center justify-center px-10 relative">
      <img src="image/logo bpsdm.jpeg" alt="Logo BPSDM" class="w-[13rem] mb-0 mt-8" />
      <img src="image/kepala-badan.jpg" alt="Kepala BPSDM" class="w-[18rem] rounded-b-full" />
      <div class="mt-4">
        <p class="font-bold text-green-700 text-lg text-center">Syahruddin Nurdin, SE</p>
        <p class="text-sm text-green-700 text-center mb-12">Kepala BPSDM Sulawesi Tenggara</p>
      </div>
    </div>
    <div class="w-full lg:w-1/2 bg-green-600 text-white p-10 pt-12 flex flex-col justify-start">
      <h2 class="text-5xl font-bold mb-8">Sambutan<br />Kepala Badan</h2>
      <p class="mb-8">
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua..."
      </p>
      <p>
        "Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."
      </p>
    </div>
  </section>

  <!-- Panduan Alur Sistem section -->
  <section class="relative flex flex-col lg:flex-row items-stretch justify-center pt-0 pb-0">
    <div class="w-full lg:w-2/2 bg-green-100 p-10 pt-12 flex flex-col justify-center">
      <h2 class="text-3xl font-bold text-black mb-12">Panduan Alur Sistem</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Alur Cards -->
        <div class="flex items-start gap-4"><div class="bg-green-700 text-white text-4xl font-bold px-5 py-2 rounded">1</div><p class="text-gray-800">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.  Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium. Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p></div>
        <div class="flex items-start gap-4"><div class="bg-green-700 text-white text-4xl font-bold px-5 py-2 rounded">2</div><p class="text-gray-800">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.  Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium. Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p></div>
        <div class="flex items-start gap-4"><div class="bg-green-700 text-white text-4xl font-bold px-5 py-2 rounded">3</div><p class="text-gray-800">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.  Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium. Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p></div>
        <div class="flex items-start gap-4"><div class="bg-green-700 text-white text-4xl font-bold px-5 py-2 rounded">4</div><p class="text-gray-800">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.  Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium. Totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p></div>
      </div>
    </div>
    <div class="w-full lg:w-1/2 bg-white flex flex-col items-center justify-center px-10">
      <img src="image/asn.png" alt="Karakter Penjelas" class="w-64 lg:w-72" />
    </div>
  </section>
  </head>

  <!-- Kalender Pembelajaran dan Jadwal Pelatihan Section -->
  <section class="flex w-full min-h-screen bg-cover bg-center" style="background-image: url('image/jemmbatan.png');">
    <!-- Kiri: Kalender -->
    <div class="w-full md:w-1/2 bg-green-300 bg-opacity-40 flex justify-center items-center p-10">
      <div class="text-center">
        <h2 class="text-2xl font-bold text-green-800 mb-4">Kalender Pembelajaran</h2>
        <div id="calendar" class="flatpickr-calendar shadow-lg"></div>
      </div>
    </div>

    <!-- Kanan: Jadwal -->
    <div class="w-full md:w-2/3 bg-green-900 bg-opacity-60 text-white p-10 pt-24">
      <div id="jadwalContainer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <p class="text-sm opacity-70">Silakan klik tanggal pada kalender untuk melihat jadwal pelatihan.</p>
      </div>
    </div>
  </section>

  <body class="bg-green-200 bg-opacity-40 font-sans">
  <!-- Struktur Organisasi -->
  <section class="bg-green-100 py-20 px-6">
      <h2 class="text-3xl font-bold text-center text-green-900 mb-12">Struktur Organisasi</h2>
      <div class="swiper w-full px-6">
        <div class="swiper-wrapper">
          <!-- Slide 1 -->
          <div class="swiper-slide bg-white rounded-2xl shadow-lg p-10 w-72 text-center">
            <img src="image/kepala-badan.jpg" alt="Pejabat2" class="rounded-full w-40 h-41 mx-auto mb-4" />
            <p class="font-bold text-green-700">Nama Pejabat 2</p>
            <p class="text-sm text-gray-600">Jabatan</p>
          </div>
          <!-- Slide 2 -->
          <div class="swiper-slide bg-white rounded-2xl shadow-lg p-10 w-72 text-center">
            <img src="image/kepala-badan.jpg" alt="Kepala BPSDM" class="rounded-full w-40 h-41 mx-auto mb-4" />
            <p class="font-bold text-green-700">Syahruddin Nurdin, SE</p>
            <p class="text-sm text-gray-600">Kepala BPSDM Provinsi Sulawesi Tenggara</p>
          </div>
          <!-- Slide 3 -->
          <div class="swiper-slide bg-white rounded-2xl shadow-lg p-10 w-72 text-center">
            <img src="image/kepala-badan.jpg" alt="Pejabat3" class="rounded-full w-40 h-41 mx-auto mb-4" />
            <p class="font-bold text-green-700">Nama Pejabat 3</p>
            <p class="text-sm text-gray-600">Jabatan</p>
          </div>
        </div>
      </div>
  </section>

  <!-- Footer -->
  <footer class="bg-green-900 text-white py-10">
    <div class="container mx-auto px-8 grid grid-cols-1 md:grid-cols-3 gap-8 text-sm">

      <!-- Kontak -->
      <div class="flex flex-col gap-2">
        <p class="font-bold text-base mb-2">📍 Alamat</p>
        <div class="flex items-start gap-2">
          <i data-lucide="map-pin" class="w-5 h-5"></i>
          <span>Jalan Chairil Anwar No. 8 A Puwatu</span>
        </div>
        <div class="flex items-start gap-2">
          <i data-lucide="phone" class="w-5 h-5"></i>
          <span>Tlp. 3124061 Fax. 312595</span>
        </div>
        <div class="flex items-start gap-2">
          <i data-lucide="mail" class="w-5 h-5"></i>
          <span>bpsdmprovsultra@gmail.com</span>
        </div>
      </div>

      <!-- Quotes -->
      <div class="flex flex-col gap-4">
        <p class="font-bold text-base mb-2">📖 Quotes</p>
        <p>“Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut enim ad minim veniam...”</p>
        <p>“Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut enim ad minim veniam...”</p>
      </div>

      <!-- Sosial Media -->
      <div class="flex flex-col gap-2">
        <p class="font-bold text-base mb-2">🌐 Sosial Media</p>
        <a href="https://facebook.com/" target="_blank" class="flex items-center gap-2 hover:text-green-400">
          <i data-lucide="facebook" class="w-5 h-5"></i> Facebook
        </a>
        <a href="https://instagram.com/" target="_blank" class="flex items-center gap-2 hover:text-green-400">
          <i data-lucide="instagram" class="w-5 h-5"></i> Instagram
        </a>
        <a href="https://tiktok.com/" target="_blank" class="flex items-center gap-2 hover:text-green-400">
          <i data-lucide="music" class="w-5 h-5"></i> TikTok
        </a>
        <a href="https://youtube.com/" target="_blank" class="flex items-center gap-2 hover:text-green-400">
          <i data-lucide="youtube" class="w-5 h-5"></i> YouTube
        </a>
        <a href="https://wa.me/628123456789" target="_blank" class="flex items-center gap-2 hover:text-green-400">
          <i data-lucide="phone" class="w-5 h-5"></i> 0812 3456 789
        </a>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
      document.addEventListener("DOMContentLoaded", function () {
        let direction = 'next';

        const swiper = new Swiper(".swiper", {
          effect: "coverflow",
          grabCursor: true,
          centeredSlides: true,
          slidesPerView: "auto",
          coverflowEffect: {
            rotate: 30,
            stretch: 0,
            depth: 200,
            modifier: 1,
            slideShadows: true,
          },
          loop: false, // loop dinonaktifkan supaya bisa bolak-balik manual
        });

        // Fungsi autoplay manual bolak-balik
        function autoPlaySwipe() {
          if (direction === 'next') {
            if (swiper.isEnd) {
              direction = 'prev';
              swiper.slidePrev();
            } else {
              swiper.slideNext();
            }
          } else {
            if (swiper.isBeginning) {
              direction = 'next';
              swiper.slideNext();
            } else {
              swiper.slidePrev();
            }
          }
        }

        // Jalankan autoplay manual setiap 3 detik
        setInterval(autoPlaySwipe, 3000);
      });
  </script>
  <!-- Tambahkan (Kalender) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
      document.addEventListener("DOMContentLoaded", function () {
        const jadwal = {
          "2025-07-07": [
            {
              judul: "Pelatihan Sertifikasi Pengadaan Barang dan Jasa",
              deskripsi: "Pelatihan tentang proses pengadaan barang dan jasa secara profesional."
            },
            {
              judul: "Pelatihan Sertifikasi Pengadaan Barang dan Jasa",
              deskripsi: "Pelatihan tentang proses pengadaan barang dan jasa secara profesional."
            },
            {
              judul: "Pelatihan Sertifikasi Pengadaan Barang dan Jasa",
              deskripsi: "Pelatihan tentang proses pengadaan barang dan jasa secara profesional."
            },
            {
              judul: "Pelatihan Sertifikasi Pengadaan Barang dan Jasa",
              deskripsi: "Pelatihan tentang proses pengadaan barang dan jasa secara profesional."
            }
          ],
          "2025-07-08": [
            {
              judul: "Pelatihan Dasar ASN",
              deskripsi: "Pembekalan nilai-nilai dasar ASN dan peran dalam birokrasi."
            },
            {
              judul: "Pelatihan Manajemen ASN",
              deskripsi: "Meningkatkan kemampuan kepemimpinan dan tata kelola pegawai negeri."
            },
            {
              judul: "Pelatihan Dasar ASN",
              deskripsi: "Pembekalan nilai-nilai dasar ASN dan peran dalam birokrasi."
            },
            {
              judul: "Pelatihan Dasar ASN",
              deskripsi: "Pembekalan nilai-nilai dasar ASN dan peran dalam birokrasi."
            }
          ]
        };

        flatpickr("#calendar", {
          inline: true,
          defaultDate: "today",
          onChange: function (selectedDates, dateStr, instance) {
            tampilkanJadwal(dateStr);
          }
        });

        function tampilkanJadwal(tanggal) {
          const container = document.getElementById("jadwalContainer");
          container.innerHTML = "";

          if (jadwal[tanggal]) {
            jadwal[tanggal].forEach(item => {
              const card = document.createElement("div");
              card.className = "bg-green-600 text-white p-6 rounded-lg shadow-md";
              card.innerHTML = `
                <h3 class="text-xl font-bold mb-2">${item.judul}</h3>
                <p class="text-sm">${item.deskripsi}</p>
              `;
              container.appendChild(card);
            });
          } else {
            const kosong = document.createElement("p");
            kosong.className = "text-gray-600 text-sm col-span-2 text-center";
            kosong.innerText = "Tidak ada pelatihan terjadwal untuk tanggal ini.";
            container.appendChild(kosong);
          }
        }

        const today = new Date().toISOString().split('T')[0];
        tampilkanJadwal(today);
      });
  </script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
      lucide.createIcons();
  </script>

</body>
</html>
