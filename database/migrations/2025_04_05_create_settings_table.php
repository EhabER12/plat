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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
        
        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'site_name',
                'value' => 'TOTO Learning',
                'description' => 'The name of the website',
                'updated_at' => now()
            ],
            [
                'key' => 'site_description',
                'value' => 'Online Learning Platform',
                'description' => 'A short description of the website',
                'updated_at' => now()
            ],
            [
                'key' => 'currency',
                'value' => 'USD',
                'description' => 'The default currency for payments',
                'updated_at' => now()
            ],
            [
                'key' => 'instructor_commission_rate',
                'value' => '70',
                'description' => 'The percentage of course revenue that goes to instructors',
                'updated_at' => now()
            ],
            [
                'key' => 'payment_methods',
                'value' => 'credit_card,vodafone_cash',
                'description' => 'Comma-separated list of enabled payment methods',
                'updated_at' => now()
            ],
            [
                'key' => 'stripe_public_key',
                'value' => 'pk_test_51NxyzTLkjhGfdSaQWertYUioPAsdfGhjkL',
                'description' => 'Stripe public API key',
                'updated_at' => now()
            ],
            [
                'key' => 'stripe_secret_key',
                'value' => 'sk_test_51NxyzTLkjhGfdSaQWertYUioPAsdfGhjkL',
                'description' => 'Stripe secret API key',
                'updated_at' => now()
            ],
            [
                'key' => 'vodafone_cash_merchant_code',
                'value' => '123456',
                'description' => 'Vodafone Cash merchant code',
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
