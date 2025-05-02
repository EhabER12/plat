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
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id('attempt_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('exam_id');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('score')->nullable();
            $table->boolean('passed')->nullable();
            $table->json('answers')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
