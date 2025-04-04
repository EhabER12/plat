<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Create enrollments table
        if (!Schema::hasTable('enrollments')) {
            Schema::create('enrollments', function (Blueprint $table) {
                $table->id('enrollment_id');
                $table->unsignedBigInteger('course_id');
                $table->unsignedBigInteger('student_id');
                $table->timestamp('enrolled_at');
                $table->enum('status', ['active', 'completed', 'dropped'])->default('active');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                
                $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
                $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
                
                $table->unique(['course_id', 'student_id']);
            });
        }
        
        // Create ratings table
        if (!Schema::hasTable('ratings')) {
            Schema::create('ratings', function (Blueprint $table) {
                $table->id('rating_id');
                $table->unsignedBigInteger('course_id');
                $table->unsignedBigInteger('user_id');
                $table->integer('rating');
                $table->text('review')->nullable();
                $table->boolean('is_approved')->default(true);
                $table->timestamps();
                
                $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                
                $table->unique(['course_id', 'user_id']);
            });
        }
        
        // Create exams table
        if (!Schema::hasTable('exams')) {
            Schema::create('exams', function (Blueprint $table) {
                $table->id('exam_id');
                $table->unsignedBigInteger('course_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->integer('duration')->nullable(); // in minutes
                $table->integer('passing_score')->default(60);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
            });
        }
        
        // Create payments table
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id('payment_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('course_id');
                $table->decimal('amount', 10, 2);
                $table->string('payment_method');
                $table->string('transaction_id')->nullable();
                $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
            });
        }
        
        // Create student_progress table
        if (!Schema::hasTable('student_progress')) {
            Schema::create('student_progress', function (Blueprint $table) {
                $table->id('progress_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('course_id');
                $table->unsignedBigInteger('content_id');
                $table->string('content_type'); // 'video', 'material', 'exam', etc.
                $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
                $table->integer('progress_percentage')->default(0);
                $table->timestamp('last_accessed_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
                
                $table->unique(['user_id', 'content_id', 'content_type']);
            });
        }
        
        // Create chats table
        if (!Schema::hasTable('chats')) {
            Schema::create('chats', function (Blueprint $table) {
                $table->id('chat_id');
                $table->unsignedBigInteger('course_id');
                $table->string('title')->nullable();
                $table->boolean('is_group')->default(false);
                $table->timestamps();
                
                $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
            });
        }
        
        // Create chat_messages table
        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id('message_id');
                $table->unsignedBigInteger('chat_id');
                $table->unsignedBigInteger('sender_id');
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                
                $table->foreign('chat_id')->references('chat_id')->on('chats')->onDelete('cascade');
                $table->foreign('sender_id')->references('user_id')->on('users')->onDelete('cascade');
            });
        }
        
        // Create course_videos table
        if (!Schema::hasTable('course_videos')) {
            Schema::create('course_videos', function (Blueprint $table) {
                $table->id('video_id');
                $table->unsignedBigInteger('course_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('video_url');
                $table->integer('duration')->nullable(); // in seconds
                $table->integer('sequence_order')->default(0);
                $table->boolean('is_preview')->default(false);
                $table->timestamps();
                
                $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
            });
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chats');
        Schema::dropIfExists('student_progress');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('course_videos');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
