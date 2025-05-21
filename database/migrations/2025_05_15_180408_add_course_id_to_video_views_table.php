<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds the course_id and completed columns to the video_views table
     * to fix the "Unknown column" errors.
     */
    public function up(): void
    {
        Schema::table('video_views', function (Blueprint $table) {
            // Add course_id column if it doesn't exist
            if (!Schema::hasColumn('video_views', 'course_id')) {
                $table->string('course_id')->after('video_id')->nullable();
            }
            
            // Add completed column if it doesn't exist
            if (!Schema::hasColumn('video_views', 'completed')) {
                $table->boolean('completed')->default(false);
            }
            
            // Add index for faster queries
            $table->index(['user_id', 'course_id']);
            $table->index(['user_id', 'completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_views', function (Blueprint $table) {
            // Drop the indexes first
            $table->dropIndex(['user_id', 'course_id']);
            $table->dropIndex(['user_id', 'completed']);
            
            // Then drop the columns
            if (Schema::hasColumn('video_views', 'course_id')) {
                $table->dropColumn('course_id');
            }
            
            if (Schema::hasColumn('video_views', 'completed')) {
                $table->dropColumn('completed');
            }
        });
    }
};
