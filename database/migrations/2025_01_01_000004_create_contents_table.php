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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_module_id')->constrained()->onDelete('cascade');
            $table->string('judul');
            $table->enum('tipe', ['text', 'pdf', 'video', 'image', 'audio'])->default('text');
            $table->string('file_path')->nullable();
            $table->integer('urutan');
            $table->timestamps();
            
            // Add indexes for frequently searched columns
            $table->index('sub_module_id');
            $table->index('tipe');
            $table->index('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
}; 