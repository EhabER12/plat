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
        Schema::create('course_videos', function (Blueprint $table) {
            $table->id('video_id');
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url')->nullable();
            $table->string('storage_disk')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->integer('position')->default(0);
            $table->boolean('is_free')->default(false);
            $table->boolean('is_encrypted')->default(false);
            $table->string('hls_path')->nullable();
            $table->string('hls_playlist')->nullable();
            $table->boolean('is_processed')->default(false);
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