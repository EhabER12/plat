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
        if (Schema::hasTable('messages')) {
            // Check if the essential columns for direct messaging exist
            Schema::table('messages', function (Blueprint $table) {
                if (!Schema::hasColumn('messages', 'is_read')) {
                    $table->boolean('is_read')->default(false);
                }
                
                if (!Schema::hasColumn('messages', 'read_at')) {
                    $table->timestamp('read_at')->nullable();
                }
                
                if (!Schema::hasColumn('messages', 'course_id')) {
                    $table->unsignedBigInteger('course_id')->nullable();
                    $table->foreign('course_id')->references('course_id')->on('courses')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration needed, as we're only adding columns if they don't exist
    }
}; 