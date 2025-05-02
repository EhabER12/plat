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
        Schema::table('chats', function (Blueprint $table) {
            if (!Schema::hasColumn('chats', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('title');
                $table->foreign('created_by')->references('user_id')->on('users')->nullOnDelete();
            }
            
            if (!Schema::hasColumn('chats', 'is_group_chat')) {
                $table->boolean('is_group_chat')->default(false)->after('title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            if (Schema::hasColumn('chats', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            
            if (Schema::hasColumn('chats', 'is_group_chat')) {
                $table->dropColumn('is_group_chat');
            }
        });
    }
}; 