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
        // Check if the settings table exists
        if (Schema::hasTable('settings')) {
            // Check if the payment_methods setting exists
            $paymentMethods = DB::table('settings')->where('key', 'payment_methods')->first();
            
            if ($paymentMethods) {
                // Update existing record
                DB::table('settings')->where('key', 'payment_methods')
                    ->update([
                        'value' => 'credit_card,vodafone_cash,paymob',
                        'updated_at' => now()
                    ]);
            } else {
                // Insert new record
                DB::table('settings')->insert([
                    'key' => 'payment_methods',
                    'value' => 'credit_card,vodafone_cash,paymob',
                    'description' => 'Comma-separated list of enabled payment methods',
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            DB::table('settings')->where('key', 'payment_methods')
                ->update([
                    'value' => 'credit_card,vodafone_cash',
                    'updated_at' => now()
                ]);
        }
    }
};
