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
        // Check if the table exists
        if (Schema::hasTable('course_videos')) {
            // Check if 'id' column exists but 'video_id' does not
            if (Schema::hasColumn('course_videos', 'id') && !Schema::hasColumn('course_videos', 'video_id')) {
                // Rename the primary key from 'id' to 'video_id'
                Schema::table('course_videos', function (Blueprint $table) {
                    $table->dropPrimary();
                });
                
                DB::statement('ALTER TABLE course_videos CHANGE id video_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
                
                Schema::table('course_videos', function (Blueprint $table) {
                    $table->primary('video_id');
                });
            } 
            // If video_id doesn't exist at all, add it
            else if (!Schema::hasColumn('course_videos', 'video_id')) {
                Schema::table('course_videos', function (Blueprint $table) {
                    $table->id('video_id')->first();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to reverse this migration as it would break the database
    }
};
