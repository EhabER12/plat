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
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('status', ['draft', 'published', 'archived'])
                  ->default('draft')
                  ->after('approval_status');
        });
        
        // Update existing courses with approved status to published
        DB::statement("UPDATE courses SET status = 'published' WHERE approval_status = 'approved'");
        
        // Update any other courses to draft status
        DB::statement("UPDATE courses SET status = 'draft' WHERE approval_status != 'approved'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
