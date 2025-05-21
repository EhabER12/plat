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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id('quiz_id');
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('time_limit')->nullable(); // in minutes
            $table->integer('passing_score')->default(70); // percentage
            $table->boolean('is_published')->default(false);
            $table->integer('position')->default(0);
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_to')->nullable();
            $table->boolean('show_answers_after_completion')->default(false);
            $table->integer('attempts_allowed')->default(1);
            $table->timestamps();

            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id('question_id');
            $table->unsignedBigInteger('quiz_id');
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'text'])->default('multiple_choice');
            $table->integer('points')->default(1);
            $table->boolean('is_required')->default(true);
            $table->integer('position')->default(0);
            $table->timestamps();

            $table->foreign('quiz_id')->references('quiz_id')->on('quizzes')->onDelete('cascade');
        });

        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id('answer_id');
            $table->unsignedBigInteger('question_id');
            $table->text('answer_text');
            $table->boolean('is_correct')->default(false);
            $table->text('explanation')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();

            $table->foreign('question_id')->references('question_id')->on('quiz_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
}; 