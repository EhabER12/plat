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
        // إضافة عمود verified_by إلى جدول parent_student_relations
        if (Schema::hasTable('parent_student_relations')) {
            if (!Schema::hasColumn('parent_student_relations', 'verified_by')) {
                Schema::table('parent_student_relations', function (Blueprint $table) {
                    $table->unsignedBigInteger('verified_by')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('parent_student_relations') && Schema::hasColumn('parent_student_relations', 'verified_by')) {
            Schema::table('parent_student_relations', function (Blueprint $table) {
                $table->dropColumn('verified_by');
            });
        }
    }
}; 