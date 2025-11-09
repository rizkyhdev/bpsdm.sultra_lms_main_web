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
        Schema::table('quizzes', function (Blueprint $table) {
            // Make sub_module_id nullable since quizzes can now be at course/module level
            $table->foreignId('sub_module_id')->nullable()->change();
            
            // Add course_id and module_id as nullable foreign keys
            $table->foreignId('course_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->nullable()->after('course_id')->constrained()->onDelete('cascade');
            
            // Add indexes
            $table->index('course_id');
            $table->index('module_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['course_id']);
            $table->dropIndex(['module_id']);
            
            // Drop foreign keys
            $table->dropForeign(['course_id']);
            $table->dropForeign(['module_id']);
            
            // Drop columns
            $table->dropColumn(['course_id', 'module_id']);
            
            // Make sub_module_id required again
            $table->foreignId('sub_module_id')->nullable(false)->change();
        });
    }
};
