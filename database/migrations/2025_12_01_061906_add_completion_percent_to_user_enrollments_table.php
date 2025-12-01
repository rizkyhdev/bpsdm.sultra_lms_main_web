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
        Schema::table('user_enrollments', function (Blueprint $table) {
            $table->unsignedTinyInteger('completion_percent')->default(0)->after('status');
            $table->index('completion_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_enrollments', function (Blueprint $table) {
            $table->dropIndex(['completion_percent']);
            $table->dropColumn('completion_percent');
        });
    }
};
