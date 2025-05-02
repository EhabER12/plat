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
        Schema::create('video_views', function (Blueprint $table) {
            $table->id('view_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('video_id');
            $table->unsignedBigInteger('course_id');
            $table->timestamp('view_date');
            $table->float('view_progress')->default(0); // Percentage of video watched (0-100)
            $table->boolean('completed')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('video_id')->references('video_id')->on('course_videos')->onDelete('cascade');
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
            
            $table->index(['user_id', 'video_id', 'view_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_views');
    }
};
