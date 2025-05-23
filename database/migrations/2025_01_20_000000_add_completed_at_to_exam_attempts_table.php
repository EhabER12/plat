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
        Schema::table('exam_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_attempts', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('start_time');
            }
            
            if (!Schema::hasColumn('exam_attempts', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('student_id');
            }
            
            if (!Schema::hasColumn('exam_attempts', 'passed')) {
                $table->boolean('passed')->nullable()->after('score');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('exam_attempts', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            
            if (Schema::hasColumn('exam_attempts', 'started_at')) {
                $table->dropColumn('started_at');
            }
            
            if (Schema::hasColumn('exam_attempts', 'passed')) {
                $table->dropColumn('passed');
            }
        });
    }
};
