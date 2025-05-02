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
        Schema::table('student_progress', function (Blueprint $table) {
            // Add student_id column if it doesn't exist
            if (!Schema::hasColumn('student_progress', 'student_id')) {
                $table->unsignedBigInteger('student_id')->after('progress_id')->nullable();
            }
        });
        
        // Update student_id to match user_id for existing records
        DB::statement('UPDATE student_progress SET student_id = user_id WHERE student_id IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_progress', function (Blueprint $table) {
            $table->dropColumn('student_id');
        });
    }
};
