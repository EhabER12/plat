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
            if (!Schema::hasColumn('messages', 'contains_flagged_content')) {
                $table->boolean('contains_flagged_content')->default(false)->after('content');
            }
            
            if (!Schema::hasColumn('messages', 'flagged_severity')) {
                $table->tinyInteger('flagged_severity')->default(0)->after('contains_flagged_content');
            }
            
            if (!Schema::hasColumn('messages', 'is_filtered')) {
                $table->boolean('is_filtered')->default(false)->after('flagged_severity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'contains_flagged_content')) {
                $table->dropColumn('contains_flagged_content');
            }
            
            if (Schema::hasColumn('messages', 'flagged_severity')) {
                $table->dropColumn('flagged_severity');
            }
            
            if (Schema::hasColumn('messages', 'is_filtered')) {
                $table->dropColumn('is_filtered');
            }
        });
    }
}; 