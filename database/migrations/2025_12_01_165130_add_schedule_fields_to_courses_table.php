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
        Schema::table('courses', function (Blueprint $table) {
            $table->timestampTz('start_date_time')->nullable()->after('bidang_kompetensi');
            $table->timestampTz('end_date_time')->nullable()->after('start_date_time');
            $table->unsignedBigInteger('updated_by')->nullable()->after('end_date_time');
            
            $table->index('start_date_time');
            $table->index('end_date_time');
            
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropIndex(['start_date_time']);
            $table->dropIndex(['end_date_time']);
            $table->dropColumn(['start_date_time', 'end_date_time', 'updated_by']);
        });
    }
};
