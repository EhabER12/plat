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
        // Ensure courses table exists first
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Check if course_contents table exists
        if (!Schema::hasTable('course_contents')) {
            Schema::create('course_contents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id');
                $table->string('title');
                $table->string('content_type'); // video, document, quiz, etc.
                $table->longText('content')->nullable();
                $table->integer('order_index')->default(0);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
                
                // Add foreign key constraint
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_contents');
    }
};
