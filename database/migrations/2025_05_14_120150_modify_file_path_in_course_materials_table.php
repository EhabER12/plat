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
        // Use DB::statement to modify the column to be nullable
        DB::statement('ALTER TABLE course_materials MODIFY file_path VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This would revert back to non-nullable, but be careful as this can cause issues
        // if any existing rows have NULL for file_path
        // Only uncomment if you're sure it's safe to do so
        // DB::statement('ALTER TABLE course_materials MODIFY file_path VARCHAR(255) NOT NULL');
    }
};
