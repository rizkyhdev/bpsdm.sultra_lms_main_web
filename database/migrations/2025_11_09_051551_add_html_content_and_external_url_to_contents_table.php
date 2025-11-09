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
            // Add HTML content field for rich text content
            $table->longText('html_content')->nullable()->after('file_path');
            
            // Add external URL field for links to PDFs or other external resources
            $table->string('external_url')->nullable()->after('html_content');
            
            // Update enum to include 'link' type for external links
            $table->enum('tipe', ['text', 'html', 'pdf', 'video', 'image', 'audio', 'link'])->default('text')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn(['html_content', 'external_url']);
            $table->enum('tipe', ['text', 'pdf', 'video', 'image', 'audio'])->default('text')->change();
        });
    }
};
