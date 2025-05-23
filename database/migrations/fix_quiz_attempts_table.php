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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id('attempt_id');
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('started_at');
            $table->timestamp('submitted_at')->nullable();
            $table->integer('score')->nullable();
            $table->decimal('score_percentage', 5, 2)->nullable();
            $table->integer('correct_answers_count')->nullable();
            $table->integer('total_questions')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'passed', 'failed', 'timed_out'])->default('in_progress');
            $table->integer('time_spent')->nullable(); // in seconds
            $table->boolean('is_graded')->default(false);
            $table->timestamps();

            $table->foreign('quiz_id')->references('quiz_id')->on('quizzes')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });

        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attempt_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('answer_id')->nullable();
            $table->text('text_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->integer('points_awarded')->default(0);
            $table->timestamps();

            $table->foreign('attempt_id')->references('attempt_id')->on('quiz_attempts')->onDelete('cascade');
            $table->foreign('question_id')->references('question_id')->on('quiz_questions')->onDelete('cascade');
            $table->foreign('answer_id')->references('answer_id')->on('quiz_answers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
        Schema::dropIfExists('quiz_attempts');
    }
}; 