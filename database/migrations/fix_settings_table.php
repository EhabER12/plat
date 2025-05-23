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
            $table->string('display_name');
            $table->string('type')->default('text');
            $table->text('description')->nullable();
            $table->string('group')->default('general');
            $table->integer('order')->default(0);
            $table->boolean('is_public')->default(true);
            $table->json('options')->nullable();
            $table->timestamps();
        });

        // إدراج الإعدادات الافتراضية بشكل منفصل
        DB::table('settings')->insert([
            'key' => 'site_name',
            'value' => 'منصة التعليم الإلكتروني',
            'display_name' => 'اسم الموقع',
            'type' => 'text',
            'description' => 'اسم الموقع الرئيسي المعروض في المتصفح وفي الإشعارات',
            'group' => 'general',
            'order' => 1,
            'is_public' => true,
            'options' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('settings')->insert([
            'key' => 'site_description',
            'value' => 'منصة تعليمية متكاملة للطلاب والمعلمين',
            'display_name' => 'وصف الموقع',
            'type' => 'textarea',
            'description' => 'وصف مختصر للموقع يظهر في محركات البحث',
            'group' => 'general',
            'order' => 2,
            'is_public' => true,
            'options' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('settings')->insert([
            'key' => 'contact_email',
            'value' => 'info@example.com',
            'display_name' => 'بريد التواصل',
            'type' => 'email',
            'description' => 'البريد الإلكتروني الرئيسي للتواصل',
            'group' => 'general',
            'order' => 3,
            'is_public' => true,
            'options' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('settings')->insert([
            'key' => 'default_payment_method',
            'value' => 'paymob',
            'display_name' => 'طريقة الدفع الافتراضية',
            'type' => 'select',
            'description' => 'طريقة الدفع الافتراضية للمنصة',
            'group' => 'payment',
            'order' => 1,
            'is_public' => true,
            'options' => json_encode(['paymob' => 'PayMob', 'fawry' => 'Fawry', 'cash' => 'نقداً']),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('settings')->insert([
            'key' => 'commission_percentage',
            'value' => '20',
            'display_name' => 'نسبة العمولة',
            'type' => 'number',
            'description' => 'نسبة العمولة التي يتم خصمها من ربح المعلم',
            'group' => 'payment',
            'order' => 2,
            'is_public' => true,
            'options' => null,
            'created_at' => now(),
            'updated_at' => now()
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