<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds the view_progress column to the video_views table
     * to fix the "Unknown column 'view_progress' in 'field list'" error.
     */
    public function up(): void
    {
        Schema::table('video_views', function (Blueprint $table) {
            // Add view_progress column if it doesn't exist
            if (!Schema::hasColumn('video_views', 'view_progress')) {
                $table->decimal('view_progress', 5, 2)->default(0)->comment('Video progress percentage (0-100)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_views', function (Blueprint $table) {
            if (Schema::hasColumn('video_views', 'view_progress')) {
                $table->dropColumn('view_progress');
            }
        });
    }
};
