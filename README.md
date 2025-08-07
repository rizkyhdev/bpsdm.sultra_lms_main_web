# Sistem Learning Management System (LMS) BPSDM Provinsi Sulawesi Tenggara

## Latar Belakang

Sistem Learning Management System (LMS) untuk Badan Pengembangan Sumber Daya Manusia (BPSDM) Provinsi Sulawesi Tenggara dikembangkan sebagai platform pembelajaran daring terstruktur untuk mendukung pengembangan kompetensi Aparatur Sipil Negara (ASN).

Berdasarkan Peraturan Pemerintah Nomor 11 Tahun 2017 yang telah diubah dengan Peraturan Pemerintah Nomor 17 Tahun 2020 tentang Manajemen Pegawai Negeri Sipil, setiap ASN diwajibkan mengikuti pengembangan kompetensi minimal 20 Jam Pelajaran (JP) per tahun.

Sistem LMS ini akan menjadi platform utama dalam mendukung pemenuhan kewajiban tersebut serta mewujudkan konsep Smart ASN yang memiliki integritas, profesionalisme, wawasan global, dan kemampuan memanfaatkan teknologi informasi.

## Tujuan Pengembangan

-   Memfasilitasi pengembangan kompetensi teknis ASN Pemprov Sultra
-   Menyediakan akses belajar yang fleksibel tanpa batasan waktu dan tempat
-   Memenuhi kewajiban minimal 20 JP pengembangan kompetensi per tahun
-   Meningkatkan efisiensi dan efektivitas program pengembangan SDM
-   Mendukung terwujudnya Smart ASN di lingkungan Pemprov Sultra

## Spesifikasi Teknis

-   PHP versi 8.3
-   Composer versi 2.8
-   Laravel versi 5.16
-   Node versi v20.19
-   NPM versi 10.8

## Setting sistem pengembangan (Windows)

Untuk mengatur sistem komputer anda pada pengembangan LMS BPSDM Sultra, silakan ikuti langkah-langkah berikut:

1. Install PHP versi 8.3 disini. 
[https://windows.php.net/download#php-8.3](https://windows.php.net/download#php-8.3) . 
Lalu di bagian VS16 x64 Thread Safe (2025-Jul-29 16:52:42), klik Zip file untuk mendownload. Ekstrak Zip file tadi. Lalu di Setting Environment Variable Windows, di variable PATH, tambahkan direktori tempat anda mengekstrak Zip file PHP 8.3. Buka CMD baru untuk mencoba php yang baru saja install dengan cara `php -v`.

2. Install Composer versi 2.8 disini. 
https://getcomposer.org/Composer-Setup.exe . 
Lalu ikuti arahan instalasi. Pastikan php yang digunakan mengacu pada direktori PHP yang baru saja di-install. Buka CMD baru untuk mencoba composer yang baru saja install dengan cara `composer --version`.

3. Unggah kode dari github. Lalu masuk ke direktori tsb.
`git clone [https://github.com/rizkyhdev/bpsdm.sultra_lms_main_web.git](https://github.com/rizkyhdev/bpsdm.sultra_lms_main_web.git)`
4. Install komponen composer yang dibutuhkan `composer install`
5. Install komponen frontend yang dibutuhkan `npm install`
6. Inisiasi database dengan `php artisan migrate`
6. Jalankan development mode dengan `composer run dev`

## Spesifikasi Utama Sistem

### 1. Sistem Akses dan Keamanan

-   Akses Terbatas: Hanya pegawai Pemprov SULTRA yang telah divalidasi yang dapat mengakses sistem LMS
-   Autentikasi: Sistem login yang terintegrasi dengan database kepegawaian Pemprov Sultra
-   Verifikasi: Mekanisme validasi identitas pegawai untuk memastikan keamanan akses

### 2. Konten Pembelajaran

-   Fokus Konten: Materi kompetensi teknis untuk ASN Pemprov
-   Format Materi: File teks, file PDF, dan video pembelajaran
-   Struktur: Modul dan sub-modul pembelajaran yang terstruktur

### 3. Sistem Evaluasi

-   Kuis Evaluasi: Setiap sub-modul pembelajaran dilengkapi dengan kuis
-   Nilai Minimum: Penetapan nilai minimum kelulusan untuk setiap kuis
-   Remedial: Kesempatan mengulang kuis bagi yang belum memenuhi nilai minimum

### 4. Monitoring JP (Jam Pelajaran)

-   Tracking JP: Sistem pencatatan akumulasi JP yang telah ditempuh oleh setiap ASN
-   Target Minimal: 20 JP per tahun sesuai dengan ketentuan peraturan
-   Laporan: Sistem pelaporan capaian JP individu dan kolektif

## Fitur Utama

| **Fitur**              | **Deskripsi**                                                                            |
| ---------------------- | ---------------------------------------------------------------------------------------- |
| Dashboard Pengguna     | Tampilan informasi progress pembelajaran, JP yang telah ditempuh, dan rekomendasi kursus |
| Katalog Kursus         | Daftar lengkap kursus yang tersedia dengan kategorisasi berdasarkan bidang kompetensi    |
| Manajemen Pembelajaran | Fitur untuk mengakses, menyimpan, dan melanjutkan pembelajaran                           |
| Sistem Evaluasi        | Kuis dan penilaian untuk mengukur tingkat pemahaman materi                               |
| Sertifikasi            | Penerbitan sertifikat elektronik setelah menyelesaikan modul pembelajaran                |
| Laporan dan Analitik   | Data statistik dan laporan mengenai aktivitas pembelajaran dan capaian JP                |
| Integrasi Data         | Koneksi dengan database kepegawaian Pemprov Sultra                                       |

## Implementasi Platform

Sistem ini dikembangkan menggunakan platform MOODLE LMS, sebuah platform pembelajaran open source yang telah banyak digunakan oleh institusi pendidikan dan pelatihan di seluruh dunia dengan keunggulan:

-   Fleksibilitas dan kemudahan kustomisasi sesuai kebutuhan spesifik BPSDM Sultra
-   Dukungan berbagai format konten pembelajaran (teks, PDF, video, dll)
-   Sistem evaluasi dan penilaian yang komprehensif
-   Fitur pelacakan kemajuan belajar yang dapat disesuaikan dengan perhitungan JP
-   Komunitas pengguna yang luas dan dukungan dokumentasi yang lengkap

## Target Pengguna

Sistem LMS ini ditujukan untuk seluruh Aparatur Sipil Negara (ASN) di lingkungan Pemerintah Provinsi Sulawesi Tenggara yang berjumlah 79 orang PNS dan 8 orang non-PNS/honorer (berdasarkan data tahun 2017).

## Cara Penggunaan

### Untuk Administrator:

1. Kelola pengguna dan validasi akses
2. Unggah dan atur materi pembelajaran
3. Buat dan konfigurasi kuis evaluasi
4. Monitor progres pembelajaran dan capaian JP
5. Buat laporan aktivitas pembelajaran

### Untuk Pengguna (ASN):

1. Login menggunakan kredensial yang telah divalidasi
2. Pilih kursus dari katalog yang tersedia
3. Akses materi pembelajaran (teks, PDF, video)
4. Selesaikan kuis evaluasi di setiap sub-modul
5. Pantau progres pembelajaran dan akumulasi JP

## Kontribusi

Untuk berkontribusi pada pengembangan LMS BPSDM Sultra, silakan ikuti langkah-langkah berikut:

1. Fork atau git pull repositori ini (`git pull origin master`)
2. Buat branch fitur baru (`git checkout -b fitur-baru`)
3. Commit perubahan Anda (`git commit -m 'Menambahkan fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## Kontak

Untuk informasi lebih lanjut, silakan hubungi tim pengembangan LMS BPSDM Provinsi Sulawesi Tenggara.
