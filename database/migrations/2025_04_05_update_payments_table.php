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
        Schema::table('payments', function (Blueprint $table) {
            // Drop the existing enum constraint
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method VARCHAR(50) NOT NULL");
            
            // Add additional columns for payment details
            $table->json('payment_details')->nullable()->after('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revert to the original enum constraint
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('credit_card', 'paypal', 'bank_transfer', 'wallet') NOT NULL");
            
            // Drop the additional columns
            $table->dropColumn('payment_details');
        });
    }
};
