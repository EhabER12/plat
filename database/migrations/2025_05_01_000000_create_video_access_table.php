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
        Schema::create('video_access', function (Blueprint $table) {
            $table->id('access_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('video_id');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('last_accessed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('video_id')->references('video_id')->on('course_videos')->onDelete('cascade');
            
            $table->unique(['user_id', 'video_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_access');
    }
};
