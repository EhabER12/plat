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
        Schema::table('course_videos', function (Blueprint $table) {
            if (!Schema::hasColumn('course_videos', 'video_url')) {
                $table->string('video_url')->nullable()->after('storage_disk');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            if (Schema::hasColumn('course_videos', 'video_url')) {
                $table->dropColumn('video_url');
            }
        });
    }
};
