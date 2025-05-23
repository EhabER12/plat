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
        Schema::table('parent_student_relations', function (Blueprint $table) {
            if (!Schema::hasColumn('parent_student_relations', 'token')) {
                $table->string('token', 100)->nullable();
                $table->index('token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parent_student_relations', function (Blueprint $table) {
            if (Schema::hasColumn('parent_student_relations', 'token')) {
                $table->dropColumn('token');
            }
        });
    }
};
