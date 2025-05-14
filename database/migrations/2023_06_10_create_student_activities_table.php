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
        if (!Schema::hasTable('student_activities')) {
            Schema::create('student_activities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->string('activity_type'); // login, course, exam, quiz, material, video_watch, etc.
                $table->string('title');
                $table->text('description');
                $table->string('related_entity')->nullable(); // courses, exams, etc.
                $table->unsignedBigInteger('related_id')->nullable(); // course_id, exam_id, etc.
                $table->timestamps();
                
                $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_activities');
    }
}; 