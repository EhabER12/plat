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
        
        // Create users table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id('user_id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password_hash');
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('profile_image')->nullable();
                $table->text('bio')->nullable();
                $table->date('dob')->nullable();
                $table->timestamp('last_login')->nullable();
                $table->string('timezone')->nullable();
                $table->string('language')->default('en');
                $table->boolean('status')->default(true);
                $table->rememberToken();
                $table->timestamp('email_verified_at')->nullable();
                $table->timestamps();
            });
        }
        
        // Create user_roles table
        if (!Schema::hasTable('user_roles')) {
            Schema::create('user_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id');
                $table->enum('role', ['admin', 'instructor', 'student', 'parent']);
                
                $table->primary(['user_id', 'role']);
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->timestamps();
            });
        }
        
        // Create categories table
        Schema::dropIfExists('categories');
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
            
            // Parent category relationship
            $table->foreign('parent_id')
                  ->references('category_id')
                  ->on('categories')
                  ->onDelete('set null');
        });
        
        // Create courses table
        Schema::dropIfExists('courses');
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
            $table->foreign('category_id')->references('category_id')->on('categories');
        });
        
        // Create course_materials table
        Schema::dropIfExists('course_materials');
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id('material_id');
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_url');
            $table->string('file_type', 50)->nullable();
            $table->unsignedBigInteger('file_size')->default(0); // In bytes
            $table->unsignedInteger('sequence_order')->default(0);
            $table->boolean('is_downloadable')->default(true);
            $table->timestamps();
            
            $table->foreign('course_id')
                  ->references('course_id')
                  ->on('courses')
                  ->onDelete('cascade');
        });
        
        // Create instructor_verifications table
        Schema::dropIfExists('instructor_verifications');
        Schema::create('instructor_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('education')->nullable();
            $table->string('expertise')->nullable();
            $table->string('years_of_experience')->nullable();
            $table->string('certificate_file')->nullable();
            $table->string('cv_file')->nullable();
            $table->string('linkedin_profile')->nullable();
            $table->text('additional_info')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
        
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
        
        Schema::dropIfExists('instructor_verifications');
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('users');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
