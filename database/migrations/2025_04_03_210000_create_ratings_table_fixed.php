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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id('rating_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('student_id');
            $table->decimal('rating_value', 3, 2);
            $table->text('review_text')->nullable();
            $table->boolean('is_published')->default(true);
            $table->enum('admin_review_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_review_notes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            
            // Unique constraint to allow only one rating per student per course
            $table->unique(['student_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
