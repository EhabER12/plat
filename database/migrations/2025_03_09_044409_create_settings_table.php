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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        $this->seedDefaultSettings();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }

    /**
     * Seed default settings
     */
    private function seedDefaultSettings(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Laravel App',
            ],
            [
                'key' => 'site_description',
                'value' => 'Your Online Learning Platform',
            ],
            [
                'key' => 'contact_email',
                'value' => 'contact@example.com',
            ],
            [
                'key' => 'instructor_commission_rate',
                'value' => '70',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
            ],
            [
                'key' => 'currency',
                'value' => 'USD',
            ],
            [
                'key' => 'payment_methods',
                'value' => 'credit_card',
            ],
            [
                'key' => 'mail_driver',
                'value' => 'smtp',
            ],
            [
                'key' => 'mail_host',
                'value' => 'smtp.mailtrap.io',
            ],
            [
                'key' => 'mail_port',
                'value' => '2525',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
