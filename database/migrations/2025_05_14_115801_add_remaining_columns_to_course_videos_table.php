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
            $table->boolean('is_encrypted')->default(false);
            $table->string('video_path')->nullable();
            $table->string('storage_disk')->default('public');
            $table->string('video_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->integer('position')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            $table->dropColumn([
                'is_encrypted', 
                'video_path', 
                'storage_disk', 
                'video_url', 
                'thumbnail_url',
                'position'
            ]);
        });
    }
};
