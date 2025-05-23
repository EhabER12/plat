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
        Schema::table('messages', function (Blueprint $table) {
            // Add content filtering fields if they don't exist
            if (!Schema::hasColumn('messages', 'contains_flagged_content')) {
                $table->boolean('contains_flagged_content')->default(false)->after('content');
            }
            
            if (!Schema::hasColumn('messages', 'flagged_severity')) {
                $table->tinyInteger('flagged_severity')->default(0)->after('contains_flagged_content');
            }
            
            if (!Schema::hasColumn('messages', 'is_filtered')) {
                $table->boolean('is_filtered')->default(false)->after('flagged_severity');
            }
            
            if (!Schema::hasColumn('messages', 'original_content')) {
                $table->text('original_content')->nullable()->after('is_filtered');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // We don't want to remove these columns in down() as it could cause data loss
        });
    }
};
