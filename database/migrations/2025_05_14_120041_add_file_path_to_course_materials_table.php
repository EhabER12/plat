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
        Schema::table('course_materials', function (Blueprint $table) {
            $table->string('file_path')->nullable();
            
            // Also check and add other possibly missing columns
            if (!Schema::hasColumn('course_materials', 'file_type')) {
                $table->string('file_type')->nullable();
            }
            
            if (!Schema::hasColumn('course_materials', 'file_size')) {
                $table->integer('file_size')->nullable();
            }
            
            if (!Schema::hasColumn('course_materials', 'sequence_order')) {
                $table->integer('sequence_order')->default(0);
            }
            
            if (!Schema::hasColumn('course_materials', 'is_downloadable')) {
                $table->boolean('is_downloadable')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            // Only drop columns that exist
            $columns = [
                'file_path',
                'file_type',
                'file_size',
                'sequence_order',
                'is_downloadable'
            ];
            
            $existingColumns = [];
            foreach ($columns as $column) {
                if (Schema::hasColumn('course_materials', $column)) {
                    $existingColumns[] = $column;
                }
            }
            
            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
