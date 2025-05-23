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
        // Make student_id nullable in parent_student_relations table
        if (Schema::hasTable('parent_student_relations')) {
            Schema::table('parent_student_relations', function (Blueprint $table) {
                // Check if student_id column exists and modify it to be nullable
                if (Schema::hasColumn('parent_student_relations', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->nullable()->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('parent_student_relations')) {
            Schema::table('parent_student_relations', function (Blueprint $table) {
                if (Schema::hasColumn('parent_student_relations', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->nullable(false)->change();
                }
            });
        }
    }
};
