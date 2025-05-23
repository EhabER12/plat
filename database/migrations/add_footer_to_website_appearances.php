<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة سجل للـ footer إذا لم يكن موجودًا
        if (DB::table('website_appearances')->where('section', 'footer')->count() == 0) {
            DB::table('website_appearances')->insert([
                'section' => 'footer',
                'primary_color' => '#4A6CF7',
                'secondary_color' => '#F9C254',
                'footer_text' => 'منصة تعليمية متكاملة',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('website_appearances')->where('section', 'footer')->delete();
    }
}; 