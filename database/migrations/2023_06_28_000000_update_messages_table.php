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
        // First check if the table exists to avoid errors
        if (Schema::hasTable('messages')) {
            
            // Rename sender_id to user_id if needed
            if (Schema::hasColumn('messages', 'sender_id') && !Schema::hasColumn('messages', 'user_id')) {
                Schema::table('messages', function (Blueprint $table) {
                    $table->renameColumn('sender_id', 'user_id');
                });
            }
            
            // Rename message to content if needed
            if (Schema::hasColumn('messages', 'message') && !Schema::hasColumn('messages', 'content')) {
                Schema::table('messages', function (Blueprint $table) {
                    $table->renameColumn('message', 'content');
                });
            }
            
            // Add other columns used by the Message model
            Schema::table('messages', function (Blueprint $table) {
                if (!Schema::hasColumn('messages', 'attachment_url')) {
                    $table->string('attachment_url')->nullable();
                }
                
                if (!Schema::hasColumn('messages', 'attachment_type')) {
                    $table->string('attachment_type')->nullable();
                }
                
                if (!Schema::hasColumn('messages', 'is_edited')) {
                    $table->boolean('is_edited')->default(false);
                }
                
                if (!Schema::hasColumn('messages', 'read_by')) {
                    $table->json('read_by')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not doing anything in down() as it's risky to revert column renames
        // and remove data columns
    }
}; 