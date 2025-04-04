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
        Schema::table('categories', function (Blueprint $table) {
            // Rename parent_id to parent_category_id to match the SQL schema
            $table->dropForeign(['parent_id']);
            $table->renameColumn('parent_id', 'parent_category_id');
            
            // Add foreign key constraint
            $table->foreign('parent_category_id')
                  ->references('category_id')
                  ->on('categories')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_category_id']);
            $table->renameColumn('parent_category_id', 'parent_id');
            
            $table->foreign('parent_id')
                  ->references('category_id')
                  ->on('categories')
                  ->nullOnDelete();
        });
    }
};
