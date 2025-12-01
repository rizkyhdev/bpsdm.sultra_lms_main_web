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
        Schema::table('certificates', function (Blueprint $table) {
            $table->uuid('certificate_uid')->unique()->nullable()->after('course_id');
            $table->timestamp('generated_at')->nullable()->after('file_path');
            $table->index('certificate_uid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropIndex(['certificate_uid']);
            $table->dropColumn(['certificate_uid', 'generated_at']);
        });
    }
};
