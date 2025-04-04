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
        Schema::create('courses', function (Blueprint $table) {
            $table->id('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->decimal('price', 8, 2)->default(0.00);
            $table->string('thumbnail')->nullable();
            $table->string('approval_status')->default('pending');
            $table->text('approval_feedback')->nullable();
            $table->timestamps();
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
