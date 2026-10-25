<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Simulasi Uji Kompetensi (CAT)
    |--------------------------------------------------------------------------
    |
    | Setiap sesi simulasi mengambil soal secara acak dari bank soal.
    | Admin menentukan jumlah soal dan batas waktu untuk setiap token; nilai di
    | bawah ini hanya nilai awal pada formulir "Buat Token". Pengaturan token
    | disalin ke setiap percobaan saat dimulai, sehingga token baru tidak
    | memengaruhi simulasi yang sedang berlangsung.
    |
    */

    'jumlah_soal' => (int) env('SIMULASI_JUMLAH_SOAL', 70),

    'durasi_menit' => (int) env('SIMULASI_DURASI_MENIT', 90),

];
