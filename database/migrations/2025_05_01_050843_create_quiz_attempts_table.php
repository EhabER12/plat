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
            $table->foreignId('quiz_id')->constrained('quizzes', 'quiz_id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->float('score')->default(0);
            $table->float('score_percentage')->default(0);
            $table->json('answers_json')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'timed_out'])->default('in_progress');
            $table->integer('time_spent_seconds')->nullable();
            $table->boolean('is_passed')->default(false);
            $table->integer('correct_answers_count')->default(0);
            $table->text('instructor_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
