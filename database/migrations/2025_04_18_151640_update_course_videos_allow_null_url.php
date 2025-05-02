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
            // Modify video_url column to allow NULL values
            $table->string('video_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            // Revert video_url column to NOT NULL
            $table->string('video_url')->nullable(false)->change();
        });
    }
};
