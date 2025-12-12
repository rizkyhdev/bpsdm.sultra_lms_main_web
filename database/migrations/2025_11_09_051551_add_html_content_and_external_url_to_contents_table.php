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
            $table->longText('html_content')->nullable()->after('file_path');
            $table->string('external_url')->nullable()->after('html_content');
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            // PostgreSQL cannot alter enum columns with a check clause in a single statement,
            // so we drop and re-create the check constraint manually.
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn(['html_content', 'external_url']);
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE contents DROP CONSTRAINT IF EXISTS contents_tipe_check");
            DB::statement("ALTER TABLE contents ALTER COLUMN tipe TYPE varchar(255)");
            DB::statement("ALTER TABLE contents ALTER COLUMN tipe SET DEFAULT 'text'");
            DB::statement("ALTER TABLE contents ALTER COLUMN tipe SET NOT NULL");
            DB::statement("ALTER TABLE contents ADD CONSTRAINT contents_tipe_check CHECK (tipe IN ('text', 'pdf', 'video', 'image', 'audio'))");
        } else {
            Schema::table('contents', function (Blueprint $table) {
                $table->enum('tipe', ['text', 'pdf', 'video', 'image', 'audio'])->default('text')->change();
            });
        }
    }
};
