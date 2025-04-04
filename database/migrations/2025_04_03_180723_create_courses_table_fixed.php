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
        // Drop the existing courses table if it exists
        Schema::dropIfExists('courses');

        // Create the courses table with the correct structure
        Schema::create('courses', function (Blueprint $table) {
            $table->id('course_id');
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('category_id');
            $table->decimal('price', 8, 2);
            $table->integer('duration')->nullable();  // in hours
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->string('language')->default('en');
            $table->boolean('featured')->default(0);
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('approval_feedback')->nullable();
            $table->string('thumbnail')->nullable();
            $table->timestamps();

            $table->foreign('instructor_id')->references('user_id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
