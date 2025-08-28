# Controller Mahasiswa untuk Sistem LMS BPSDM Sultra

Dokumen ini menyediakan gambaran komprehensif dari semua controller peran mahasiswa yang dibuat untuk sistem LMS BPSDM Sultra.

## Gambaran Umum

Sistem mencakup 8 controller utama yang dirancang untuk menangani semua fungsionalitas mahasiswa dalam sistem manajemen pembelajaran. Setiap controller mengikuti praktik terbaik Laravel dan mencakup autentikasi, otorisasi, dan penanganan error yang tepat.

## Controller yang Dibuat

### 1. StudentDashboardController
**Lokasi:** `app/Http/Controllers/Student/StudentDashboardController.php`

**Tujuan:** Dashboard utama untuk mahasiswa yang menampilkan gambaran pembelajaran dan progress.

**Metode Utama:**
- `index()` - Menampilkan dashboard mahasiswa dengan kursus yang diikuti, progress, catatan JP, dan kuis yang akan datang
- `getDashboardData()` - Endpoint AJAX untuk data dashboard dengan filter tahun
- `getLearningStats()` - Mendapatkan statistik pembelajaran dan metrik

**Fitur:**
- Kursus yang diikuti dengan pelacakan progress
- Akumulasi JP (Jam Pelajaran) untuk tahun berjalan
- Tampilan kuis yang akan datang
- Aktivitas pembelajaran terbaru
- Visualisasi progress kursus
- Statistik pembelajaran

### 2. StudentCourseController
**Lokasi:** `app/Http/Controllers/Student/StudentCourseController.php`

**Tujuan:** Menangani penelusuran kursus, pendaftaran, dan progress pembelajaran.

**Metode Utama:**
- `index()` - Daftar semua kursus yang tersedia dengan filter dan pencarian
- `show()` - Menampilkan detail kursus tertentu dan progress
- `enroll()` - Mendaftarkan mahasiswa ke kursus
- `myLearning()` - Menampilkan kursus yang diikuti dengan progress
- `trackProgress()` - Melacak dan menampilkan progress pembelajaran
- `unenroll()` - Menghapus pendaftaran dari kursus
- `getRecommendations()` - Mendapatkan rekomendasi kursus berdasarkan riwayat pembelajaran

**Fitur:**
- Penelusuran kursus dengan filter (bidang kompetensi, pencarian, pengurutan)
- Manajemen pendaftaran kursus
- Pelacakan dan visualisasi progress
- Rekomendasi kursus
- Manajemen status pendaftaran

### 3. StudentModuleController
**Lokasi:** `app/Http/Controllers/Student/StudentModuleController.php`

**Tujuan:** Menangani tampilan modul dan pelacakan penyelesaian.

**Metode Utama:**
- `show()` - Menampilkan modul dengan sub-modul dan progress
- `markComplete()` - Menandai modul sebagai selesai
- `getProgress()` - Mendapatkan progress modul yang detail
- `getNavigation()` - Mendapatkan navigasi antar modul

**Fitur:**
- Kontrol akses modul (progresi berurutan)
- Pelacakan dan visualisasi progress
- Validasi penyelesaian modul
- Navigasi antar modul
- Pemeriksaan penyelesaian kursus otomatis

### 4. StudentSubModuleController
**Lokasi:** `app/Http/Controllers/Student/StudentSubModuleController.php`

**Tujuan:** Menangani tampilan sub-modul dan progresi konten.

**Metode Utama:**
- `show()` - Menampilkan sub-modul dengan konten dan progress
- `markComplete()` - Menandai sub-modul sebagai selesai
- `getProgress()` - Mendapatkan progress sub-modul yang detail
- `getNavigation()` - Mendapatkan navigasi antar sub-modul
- `updateProgress()` - Memperbarui progress pembelajaran

**Fitur:**
- Kontrol akses sub-modul
- Pelacakan progress konten
- Penegakan pembelajaran berurutan
- Pembaruan persentase progress
- Pemeriksaan penyelesaian modul otomatis

### 5. StudentContentController
**Lokasi:** `app/Http/Controllers/Student/StudentContentController.php`

**Tujuan:** Menangani tampilan konten pembelajaran dan pelacakan progress.

**Metode Utama:**
- `show()` - Menampilkan konten pembelajaran (teks, PDF, video)
- `trackProgress()` - Melacak progress melihat konten
- `markComplete()` - Menandai konten sebagai selesai
- `download()` - Mengunduh file konten
- `streamVideo()` - Streaming konten video
- `getProgress()` - Mendapatkan detail progress konten
- `getNavigation()` - Mendapatkan navigasi antar item konten

**Fitur:**
- Dukungan berbagai tipe konten (teks, PDF, video)
- Pelacakan progress dengan pembaruan persentase
- Fungsionalitas unduhan file
- Dukungan streaming video
- Kontrol akses konten
- Persistensi progress

### 6. StudentQuizController
**Lokasi:** `app/Http/Controllers/Student/StudentQuizController.php`

**Tujuan:** Menangani fungsionalitas kuis termasuk mengerjakan, penilaian, dan review.

**Metode Utama:**
- `index()` - Daftar kuis untuk sub-modul
- `show()` - Menampilkan detail kuis dan percobaan
- `start()` - Memulai percobaan kuis baru
- `submit()` - Mengirimkan jawaban kuis dan menghitung skor
- `result()` - Menampilkan hasil kuis
- `reviewAttempt()` - Review percobaan kuis sebelumnya
- `getQuestions()` - Mendapatkan pertanyaan kuis untuk frontend
- `saveProgress()` - Auto-save progress kuis

**Fitur:**
- Manajemen percobaan kuis
- Penilaian dan grading otomatis
- Auto-save progress
- Penegakan batas percobaan
- Review dan analisis hasil
- Randomisasi pertanyaan
- Validasi jawaban

### 7. StudentCertificateController
**Lokasi:** `app/Http/Controllers/Student/StudentCertificateController.php`

**Tujuan:** Menangani tampilan, unduhan, dan validasi sertifikat.

**Metode Utama:**
- `index()` - Daftar semua sertifikat yang diperoleh
- `show()` - Menampilkan detail sertifikat tertentu
- `download()` - Mengunduh sertifikat sebagai PDF
- `view()` - Melihat sertifikat di browser
- `getCertificateData()` - Mendapatkan data sertifikat untuk AJAX
- `getStatistics()` - Mendapatkan statistik sertifikat
- `search()` - Mencari sertifikat
- `export()` - Mengekspor sertifikat ke CSV
- `validateCertificate()` - Memvalidasi keaslian sertifikat

**Fitur:**
- Generasi sertifikat PDF
- Sistem validasi sertifikat
- Statistik dan pelaporan
- Pencarian dan filter
- Fungsionalitas ekspor CSV
- Verifikasi keaslian sertifikat

### 8. StudentJPRecordController
**Lokasi:** `app/Http/Controllers/Student/StudentJPRecordController.php`

**Tujuan:** Menangani manajemen catatan JP (Jam Pelajaran) dan pelaporan.

**Metode Utama:**
- `index()` - Daftar semua catatan JP dengan filter
- `yearSummary()` - Menampilkan ringkasan JP untuk tahun tertentu
- `getJpData()` - Mendapatkan data JP untuk permintaan AJAX
- `getStatistics()` - Mendapatkan statistik dan tren JP
- `search()` - Mencari catatan JP
- `export()` - Mengekspor catatan JP ke CSV
- `getTargetProgress()` - Mendapatkan progress menuju target JP
- `getYearComparison()` - Membandingkan JP antar tahun

**Fitur:**
- Pelacakan dan visualisasi catatan JP
- Ringkasan tahunan dan bulanan
- Pelacakan progress target
- Perbandingan tahun ke tahun
- Analisis statistik
- Fungsionalitas ekspor CSV
- Filter dan pencarian

## File Pendukung

### Middleware
**Lokasi:** `app/Http/Middleware/CheckRole.php`

**Tujuan:** Kontrol akses berbasis peran untuk fungsionalitas mahasiswa.

**Fitur:**
- Verifikasi autentikasi
- Validasi peran
- Penegakan kontrol akses

### Trait
**Lokasi:** `app/Http/Controllers/Student/Traits/StudentControllerTrait.php`

**Tujuan:** Fungsionalitas umum yang dibagikan di semua controller mahasiswa.

**Fitur:**
- Pemeriksaan pendaftaran pengguna
- Metode perhitungan progress
- Helper kontrol akses
- Utilitas perhitungan JP
- Helper formatting
- Statistik pembelajaran

## Fitur Utama yang Diimplementasikan

### Autentikasi & Otorisasi
- Semua controller menggunakan middleware `auth`
- Kontrol akses berbasis peran dengan middleware `role:student`
- Verifikasi pendaftaran yang tepat untuk akses kursus
- Penegakan pembelajaran berurutan

### Pelacakan Progress
- Pelacakan progress komprehensif di semua level (kursus, modul, sub-modul, konten)
- Deteksi penyelesaian otomatis
- Perhitungan persentase progress
- Penegakan jalur pembelajaran

### Manajemen JP (Jam Pelajaran)
- Pelacakan akumulasi JP
- Ringkasan tahunan dan bulanan
- Pemantauan progress target
- Analisis statistik dan pelaporan

### Sistem Kuis
- Dukungan multiple percobaan
- Penilaian otomatis
- Auto-save progress
- Review dan analisis hasil

### Sistem Sertifikat
- Generasi PDF
- Validasi keaslian
- Opsi unduhan dan melihat
- Fungsionalitas ekspor

### Ekspor Data
- Ekspor CSV untuk catatan JP dan sertifikat
- Opsi ekspor yang difilter
- Format dan header yang tepat

## Relasi Database yang Digunakan

Controller dengan tepat memanfaatkan relasi database yang didefinisikan:
- User → UserEnrollment → Course
- Course → Module → SubModule → Content
- Course → Quiz → Question → AnswerOption
- User → UserProgress (untuk progress konten/sub-modul)
- User → QuizAttempt → UserAnswer
- User → Certificate
- User → JpRecord

## Penanganan Error

Semua controller mencakup penanganan error yang komprehensif:
- Kode status HTTP yang tepat
- Pesan error yang ramah pengguna
- Manajemen transaksi database
- Penanganan error validasi
- Pelanggaran kontrol akses

## Format Response

Controller mengembalikan response yang sesuai berdasarkan tipe permintaan:
- **Views** untuk tampilan halaman
- **JSON responses** untuk permintaan AJAX
- **File downloads** untuk konten dan sertifikat
- **CSV exports** untuk ekspor data

## Fitur Keamanan

- Middleware autentikasi di semua controller
- Kontrol akses berbasis peran
- Verifikasi pendaftaran untuk akses kursus
- Penegakan pembelajaran berurutan
- Validasi dan sanitasi input
- Pencegahan SQL injection melalui Eloquent ORM

## Pertimbangan Performa

- Query database yang efisien dengan relasi yang tepat
- Paginasi untuk dataset besar
- Lazy loading yang sesuai
- Peluang caching untuk statistik
- Optimasi perhitungan progress

## Peningkatan Masa Depan

Controller dirancang untuk dapat diperluas untuk fitur masa depan:
- Analitik dan wawasan pembelajaran
- Fitur pembelajaran sosial
- Pelacakan progress lanjutan
- Dukungan aplikasi mobile
- Integrasi dengan sistem eksternal
- Pelaporan dan analitik lanjutan

## Contoh Penggunaan

### Penggunaan Controller Dasar
```php
// Di routes/web.php
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/student/courses', [StudentCourseController::class, 'index'])->name('student.courses.index');
    // ... rute lainnya
});
```

### Penggunaan Trait
```php
use App\Http\Controllers\Student\Traits\StudentControllerTrait;

class StudentCourseController extends Controller
{
    use StudentControllerTrait;
    
    public function show(Course $course)
    {
        if (!$this->isUserEnrolled($course->id)) {
            abort(403, 'Anda harus terdaftar untuk melihat kursus ini.');
        }
        
        $progress = $this->getUserCourseProgress($course->id);
        // ... sisa metode
    }
}
```

Set controller komprehensif ini menyediakan fondasi yang solid untuk pengalaman pembelajaran mahasiswa dalam sistem LMS BPSDM Sultra, dengan pemisahan keprihatinan yang tepat, keamanan, dan kemampuan diperluas untuk peningkatan masa depan. 