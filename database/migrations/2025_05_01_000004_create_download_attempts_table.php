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
        Schema::create('download_attempts', function (Blueprint $table) {
            $table->id('attempt_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('video_id');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('fingerprint')->nullable();
            $table->text('request_details')->nullable();
            $table->integer('attempt_count')->default(1);
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('blocked_until')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('video_id')->references('video_id')->on('course_videos')->onDelete('cascade');
            
            $table->index(['user_id', 'video_id', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download_attempts');
    }
};
