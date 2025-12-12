<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->string('youtube_url')->nullable()->after('external_url');
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            // PostgreSQL cannot alter enum+check in one statement; manage constraint manually.
            DB::statement("ALTER TABLE contents DROP CONSTRAINT IF EXISTS contents_tipe_check");
            DB::statement("ALTER TABLE contents ALTER COLUMN tipe TYPE varchar(255)");
            DB::statement("ALTER TABLE contents ALTER COLUMN tipe SET DEFAULT 'text'");
            DB::statement("ALTER TABLE contents ALTER COLUMN tipe SET NOT NULL");
            DB::statement("ALTER TABLE contents ADD CONSTRAINT contents_tipe_check CHECK (tipe IN ('text', 'html', 'pdf', 'video', 'image', 'audio', 'link', 'youtube'))");
        } else {
            Schema::table('contents', function (Blueprint $table) {
                $table->enum('tipe', ['text', 'html', 'pdf', 'video', 'image', 'audio', 'link', 'youtube'])->default('text')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('youtube_url');
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE contents DROP CONSTRAINT IF EXISTS contents_tipe_check");
            DB::statement("ALTER TABLE contents ALTER COLUMN tipe TYPE varchar(255)");
            DB::statement("ALTER TABLE contents ALTER COLUMN tipe SET DEFAULT 'text'");
            DB::statement("ALTER TABLE contents ALTER COLUMN tipe SET NOT NULL");
            DB::statement("ALTER TABLE contents ADD CONSTRAINT contents_tipe_check CHECK (tipe IN ('text', 'html', 'pdf', 'video', 'image', 'audio', 'link'))");
        } else {
            Schema::table('contents', function (Blueprint $table) {
                $table->enum('tipe', ['text', 'html', 'pdf', 'video', 'image', 'audio', 'link'])->default('text')->change();
            });
        }
    }
};
