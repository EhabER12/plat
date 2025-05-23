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
        if (!Schema::hasTable('student_progress')) {
            Schema::create('student_progress', function (Blueprint $table) {
                $table->id('progress_id');
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('course_id');
                $table->string('content_type');
                $table->unsignedBigInteger('content_id');
                $table->integer('progress_percentage')->default(0);
                $table->integer('last_position')->default(0);
                $table->timestamp('completed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
            });
        } else {
            Schema::table('student_progress', function (Blueprint $table) {
                if (!Schema::hasColumn('student_progress', 'progress_id')) {
                    $table->id('progress_id')->first();
                }
                
                if (!Schema::hasColumn('student_progress', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->after('progress_id');
                }
                
                if (!Schema::hasColumn('student_progress', 'course_id')) {
                    $table->unsignedBigInteger('course_id')->after('student_id');
                }
                
                if (!Schema::hasColumn('student_progress', 'content_type')) {
                    $table->string('content_type')->after('course_id');
                }
                
                if (!Schema::hasColumn('student_progress', 'content_id')) {
                    $table->unsignedBigInteger('content_id')->after('content_type');
                }
                
                if (!Schema::hasColumn('student_progress', 'progress_percentage')) {
                    $table->integer('progress_percentage')->default(0)->after('content_id');
                }
                
                if (!Schema::hasColumn('student_progress', 'last_position')) {
                    $table->integer('last_position')->default(0)->after('progress_percentage');
                }
                
                if (!Schema::hasColumn('student_progress', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('last_position');
                }
                
                if (!Schema::hasColumn('student_progress', 'notes')) {
                    $table->text('notes')->nullable()->after('completed_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     * 
     * Since this migration only ensures structure, we don't need to reverse anything.
     */
    public function down(): void
    {
        // No need to revert anything
    }
}; 