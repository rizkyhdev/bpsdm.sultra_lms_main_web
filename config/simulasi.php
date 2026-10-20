<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Simulasi Uji Kompetensi (CAT)
    |--------------------------------------------------------------------------
    |
    | Setiap sesi simulasi mengambil soal secara acak dari bank soal.
    | Nilai ini disalin ke setiap percobaan saat dimulai, sehingga perubahan
    | konfigurasi tidak memengaruhi simulasi yang sedang berlangsung.
    |
    */

    'jumlah_soal' => (int) env('SIMULASI_JUMLAH_SOAL', 70),

    'durasi_menit' => (int) env('SIMULASI_DURASI_MENIT', 90),

];
