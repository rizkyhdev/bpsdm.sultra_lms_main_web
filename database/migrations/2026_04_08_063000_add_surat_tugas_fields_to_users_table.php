<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'surat_tugas_url')) {
                $table->string('surat_tugas_url')->nullable()->after('is_validated');
            }
            if (!Schema::hasColumn('users', 'surat_tugas_file_path')) {
                $table->string('surat_tugas_file_path')->nullable()->after('surat_tugas_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'surat_tugas_url')) {
                $table->dropColumn('surat_tugas_url');
            }
            if (Schema::hasColumn('users', 'surat_tugas_file_path')) {
                $table->dropColumn('surat_tugas_file_path');
            }
        });
    }
};
