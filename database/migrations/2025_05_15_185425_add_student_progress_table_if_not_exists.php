<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::info('Starting student_progress table migration check');
        
        // التحقق من وجود جدول student_progress
        if (!Schema::hasTable('student_progress')) {
            Log::info('Creating student_progress table from scratch');
            
            Schema::create('student_progress', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('course_id');
                $table->string('content_type'); // يمكن أن يكون 'video', 'quiz', 'assignment' إلخ
                $table->unsignedBigInteger('content_id');
                $table->unsignedBigInteger('user_id')->nullable(); // لتخزين user_id كمرجع إضافي
                $table->integer('progress_percentage')->default(0);
                $table->float('last_position', 8, 2)->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                
                // إضافة فهارس للبحث السريع
                $table->index(['student_id', 'course_id', 'content_type', 'content_id']);
                
                // إضافة الـ foreign keys إذا كانت الجداول المرجعية موجودة
                if (Schema::hasTable('users') && Schema::hasColumn('users', 'user_id')) {
                    $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
                    $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                }
                
                if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'course_id')) {
                    $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
                }
            });
            
            DB::statement('ALTER TABLE student_progress COMMENT = "Stores student progress for course content"');
            
            echo "Created student_progress table successfully.\n";
            Log::info('Created student_progress table successfully');
        } else {
            echo "Table student_progress already exists, examining structure...\n";
            Log::info('Table student_progress already exists, examining structure');
            
            // الحصول على أسماء الأعمدة الموجودة
            $existingColumns = Schema::getColumnListing('student_progress');
            Log::info('Existing columns in student_progress:', $existingColumns);
            
            // التحقق من وجود الأعمدة الضرورية وإضافتها إذا كانت مفقودة
            // نضيف الأعمدة واحد تلو الآخر بإجراءات منفصلة لتجنب الأخطاء
            
            if (!in_array('user_id', $existingColumns)) {
                Schema::table('student_progress', function (Blueprint $table) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('content_id');
                });
                echo "Added missing column 'user_id' to student_progress table.\n";
                Log::info("Added missing column 'user_id' to student_progress table");
            }
            
            if (!in_array('progress_percentage', $existingColumns)) {
                Schema::table('student_progress', function (Blueprint $table) {
                    $table->integer('progress_percentage')->default(0)->after('user_id');
                });
                echo "Added missing column 'progress_percentage' to student_progress table.\n";
                Log::info("Added missing column 'progress_percentage' to student_progress table");
            }
            
            if (!in_array('last_position', $existingColumns)) {
                Schema::table('student_progress', function (Blueprint $table) {
                    $table->float('last_position', 8, 2)->nullable();
                });
                echo "Added missing column 'last_position' to student_progress table.\n";
                Log::info("Added missing column 'last_position' to student_progress table");
            }
            
            if (!in_array('completed_at', $existingColumns)) {
                Schema::table('student_progress', function (Blueprint $table) {
                    $table->timestamp('completed_at')->nullable();
                });
                echo "Added missing column 'completed_at' to student_progress table.\n";
                Log::info("Added missing column 'completed_at' to student_progress table");
            }
        }
        
        Log::info('Completed student_progress table migration check');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لن نقوم بحذف الجدول في الـ down لتجنب فقدان البيانات عن طريق الخطأ
        Log::info('Skipping down migration for student_progress table to prevent data loss');
    }
};
