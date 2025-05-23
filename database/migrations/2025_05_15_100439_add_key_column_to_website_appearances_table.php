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
        Schema::table('website_appearances', function (Blueprint $table) {
            // Add key column if it doesn't exist
            if (!Schema::hasColumn('website_appearances', 'key')) {
                $table->string('key')->after('id')->index();
            }
            
            // Add value column if it doesn't exist
            if (!Schema::hasColumn('website_appearances', 'value')) {
                $table->json('value')->nullable()->after('key');
            }
            
            // Add section column if it doesn't exist
            if (!Schema::hasColumn('website_appearances', 'section')) {
                $table->string('section')->after('value')->index();
            }
            
            // Add type column if it doesn't exist
            if (!Schema::hasColumn('website_appearances', 'type')) {
                $table->string('type')->default('text')->after('section');
            }
            
            // Add unique constraint
            $table->unique(['key', 'section'], 'website_appearances_key_section_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_appearances', function (Blueprint $table) {
            $table->dropUnique('website_appearances_key_section_unique');
            $table->dropColumn(['key', 'value', 'section', 'type']);
        });
    }
};
