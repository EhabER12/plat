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
        Schema::table('instructor_verifications', function (Blueprint $table) {
            $table->json('payment_details')->nullable()->after('additional_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructor_verifications', function (Blueprint $table) {
            $table->dropColumn('payment_details');
        });
    }
};
