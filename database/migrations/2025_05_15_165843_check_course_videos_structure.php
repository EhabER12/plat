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
        // First check the current structure of the table
        try {
            $columns = DB::select("SHOW COLUMNS FROM course_videos");
            $hasVideoId = false;
            $hasId = false;
            
            foreach ($columns as $column) {
                if ($column->Field === 'video_id') {
                    $hasVideoId = true;
                }
                if ($column->Field === 'id') {
                    $hasId = true;
                }
            }
            
            // If we don't have video_id but have id, we need to add video_id
            if (!$hasVideoId) {
                Schema::table('course_videos', function (Blueprint $table) {
                    $table->id('video_id')->first();
                });
                
                // Log the change for debugging
                DB::statement("INSERT INTO migrations (migration, batch) VALUES ('added_video_id_manually', 9999)");
            }
            
        } catch (\Exception $e) {
            // Log the error for debugging
            DB::statement("INSERT INTO migrations (migration, batch) VALUES ('error_checking_course_videos_". $e->getCode() ."', 9999)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to do anything in down() as we don't want to remove the column
    }
};
