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
        Schema::create('exams', function (Blueprint $table) {
            $table->id('exam_id');
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration')->comment('Duration in minutes');
            $table->integer('passing_score')->default(60);
            $table->boolean('is_published')->default(false);
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_to')->nullable();
            $table->timestamps();
            
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
        });
        
        // Questions table
        Schema::create('questions', function (Blueprint $table) {
            $table->id('question_id');
            $table->unsignedBigInteger('exam_id');
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'short_answer']);
            $table->integer('points')->default(1);
            $table->timestamps();
            
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
        });
        
        // Answer options table
        Schema::create('answer_options', function (Blueprint $table) {
            $table->id('option_id');
            $table->unsignedBigInteger('question_id');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
            
            $table->foreign('question_id')->references('question_id')->on('questions')->onDelete('cascade');
        });
        
        // Student exam attempts table
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id('attempt_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('exam_id');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('score')->nullable();
            $table->boolean('passed')->nullable();
            $table->timestamps();
            
            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onDelete('cascade');
        });
        
        // Student answers table
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id('answer_id');
            $table->unsignedBigInteger('attempt_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('option_id')->nullable();
            $table->text('text_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->integer('points_awarded')->default(0);
            $table->timestamps();
            
            $table->foreign('attempt_id')->references('attempt_id')->on('exam_attempts')->onDelete('cascade');
            $table->foreign('question_id')->references('question_id')->on('questions')->onDelete('cascade');
            $table->foreign('option_id')->references('option_id')->on('answer_options')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('answer_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('exams');
    }
};
