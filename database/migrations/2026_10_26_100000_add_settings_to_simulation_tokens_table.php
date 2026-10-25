<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Jumlah soal dan batas waktu ditentukan admin untuk setiap token (sesi).
        Schema::table('simulation_tokens', function (Blueprint $table) {
            $table->unsignedSmallInteger('jumlah_soal')->nullable();
            $table->unsignedSmallInteger('durasi_menit')->nullable();
        });

        // Token yang sudah ada dibuat dengan nilai dari config/simulasi.php.
        DB::table('simulation_tokens')->update([
            'jumlah_soal' => config('simulasi.jumlah_soal'),
            'durasi_menit' => config('simulasi.durasi_menit'),
        ]);

        Schema::table('simulation_tokens', function (Blueprint $table) {
            $table->unsignedSmallInteger('jumlah_soal')->nullable(false)->change();
            $table->unsignedSmallInteger('durasi_menit')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulation_tokens', function (Blueprint $table) {
            $table->dropColumn(['jumlah_soal', 'durasi_menit']);
        });
    }
};
