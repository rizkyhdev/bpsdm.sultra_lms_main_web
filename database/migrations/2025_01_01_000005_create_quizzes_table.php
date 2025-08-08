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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_module_id')->constrained()->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi');
            $table->float('nilai_minimum');
            $table->integer('max_attempts')->default(3);
            $table->timestamps();
            
            // Add indexes for frequently searched columns
            $table->index('sub_module_id');
            $table->index('nilai_minimum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
}; 