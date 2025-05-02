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
        if (!Schema::hasColumn('chats', 'last_message_at')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->timestamp('last_message_at')->nullable()->after('title');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('chats', 'last_message_at')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->dropColumn('last_message_at');
            });
        }
    }
}; 