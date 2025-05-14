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
            // Check if the default_payment_method setting exists
            $defaultPaymentMethod = DB::table('settings')->where('key', 'default_payment_method')->first();
            
            if ($defaultPaymentMethod) {
                // Update existing record
                DB::table('settings')->where('key', 'default_payment_method')
                    ->update([
                        'value' => 'paymob', // Set Paymob as default
                        'updated_at' => now()
                    ]);
            } else {
                // Insert new record
                DB::table('settings')->insert([
                    'key' => 'default_payment_method',
                    'value' => 'paymob',
                    'description' => 'Default payment method for the checkout page',
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
            DB::table('settings')->where('key', 'default_payment_method')->delete();
        }
    }
};
