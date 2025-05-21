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
            if (!Schema::hasColumn('course_videos', 'is_encrypted')) {
                $table->boolean('is_encrypted')->default(false);
            }
            
            if (!Schema::hasColumn('course_videos', 'video_path')) {
                $table->string('video_path')->nullable();
            }
            
            if (!Schema::hasColumn('course_videos', 'storage_disk')) {
                $table->string('storage_disk')->default('public');
            }
            
            if (!Schema::hasColumn('course_videos', 'video_url')) {
                $table->string('video_url')->nullable();
            }
            
            if (!Schema::hasColumn('course_videos', 'thumbnail_url')) {
                $table->string('thumbnail_url')->nullable();
            }
            
            if (!Schema::hasColumn('course_videos', 'position')) {
                $table->integer('position')->default(0);
            }
            
            if (!Schema::hasColumn('course_videos', 'hls_path')) {
                $table->string('hls_path')->nullable();
            }
            
            if (!Schema::hasColumn('course_videos', 'hls_url')) {
                $table->string('hls_url')->nullable();
            }
            
            if (!Schema::hasColumn('course_videos', 'is_hls_enabled')) {
                $table->boolean('is_hls_enabled')->default(false);
            }
            
            if (!Schema::hasColumn('course_videos', 'encryption_key')) {
                $table->string('encryption_key')->nullable();
            }
            
            if (!Schema::hasColumn('course_videos', 'hls_segments_path')) {
                $table->string('hls_segments_path')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_videos', function (Blueprint $table) {
            // Only attempt to drop columns that exist
            $columns = [
                'is_encrypted', 
                'video_path', 
                'storage_disk', 
                'video_url', 
                'thumbnail_url',
                'position',
                'hls_path',
                'hls_url',
                'is_hls_enabled',
                'encryption_key',
                'hls_segments_path'
            ];
            
            $existingColumns = [];
            foreach ($columns as $column) {
                if (Schema::hasColumn('course_videos', $column)) {
                    $existingColumns[] = $column;
                }
            }
            
            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
