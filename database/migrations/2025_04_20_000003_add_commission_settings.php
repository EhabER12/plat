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
            // Check if the instructor_commission_rate setting exists
            $instructorRate = DB::table('settings')->where('key', 'instructor_commission_rate')->first();

            if (!$instructorRate) {
                // Insert instructor commission rate setting
                DB::table('settings')->insert([
                    'key' => 'instructor_commission_rate',
                    'value' => '70',
                    'description' => 'The percentage of course revenue that goes to instructors',
                    'updated_at' => now()
                ]);
            }

            // Check if the platform_commission_rate setting exists
            $platformRate = DB::table('settings')->where('key', 'platform_commission_rate')->first();

            if (!$platformRate) {
                // Insert platform commission rate setting
                DB::table('settings')->insert([
                    'key' => 'platform_commission_rate',
                    'value' => '30',
                    'description' => 'The percentage of course revenue that goes to the platform',
                    'updated_at' => now()
                ]);
            }

            // Check if the minimum_withdrawal_amount setting exists
            $minWithdrawal = DB::table('settings')->where('key', 'minimum_withdrawal_amount')->first();

            if (!$minWithdrawal) {
                // Insert minimum withdrawal amount setting
                DB::table('settings')->insert([
                    'key' => 'minimum_withdrawal_amount',
                    'value' => '100',
                    'description' => 'The minimum amount that instructors can withdraw',
                    'updated_at' => now()
                ]);
            }

            // Check if the withdrawal_processing_days setting exists
            $processingDays = DB::table('settings')->where('key', 'withdrawal_processing_days')->first();

            if (!$processingDays) {
                // Insert withdrawal processing days setting
                DB::table('settings')->insert([
                    'key' => 'withdrawal_processing_days',
                    'value' => '3',
                    'description' => 'The number of days it takes to process a withdrawal request',
                    'updated_at' => now()
                ]);
            }

            // Check if the earnings_holding_period setting exists
            $holdingPeriod = DB::table('settings')->where('key', 'earnings_holding_period')->first();

            if (!$holdingPeriod) {
                // Insert earnings holding period setting
                DB::table('settings')->insert([
                    'key' => 'earnings_holding_period',
                    'value' => '14',
                    'description' => 'The number of days to hold instructor earnings before making them available for withdrawal',
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
            // Remove the settings we added
            DB::table('settings')->whereIn('key', [
                'platform_commission_rate',
                'minimum_withdrawal_amount',
                'withdrawal_processing_days'
            ])->delete();

            // We don't remove instructor_commission_rate as it might have existed before
        }
    }
};
