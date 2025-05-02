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
        // Drop the table if it exists and recreate it with a consistent structure
        Schema::dropIfExists('course_videos');
        
        Schema::create('course_videos', function (Blueprint $table) {
            $table->id('video_id');
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url'); // URL for external videos (YouTube, Vimeo, etc.)
            $table->string('video_path')->nullable(); // Path for locally uploaded videos
            $table->string('thumbnail_url')->nullable(); // Thumbnail image URL
            $table->integer('duration_seconds')->default(0); // Duration in seconds
            $table->integer('sequence_order')->default(0); // Order in the course
            $table->boolean('is_free_preview')->default(false); // Whether this video is available as a free preview
            $table->timestamps();
            
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_videos');
    }
};
