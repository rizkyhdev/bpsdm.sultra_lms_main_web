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
        Schema::table('contents', function (Blueprint $table) {
            // Add YouTube URL field for YouTube video links
            $table->string('youtube_url')->nullable()->after('external_url');
            
            // Update enum to include 'youtube' type
            $table->enum('tipe', ['text', 'html', 'pdf', 'video', 'image', 'audio', 'link', 'youtube'])->default('text')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('youtube_url');
            $table->enum('tipe', ['text', 'html', 'pdf', 'video', 'image', 'audio', 'link'])->default('text')->change();
        });
    }
};
