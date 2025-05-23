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
        Schema::table('courses', function (Blueprint $table) {
            // إذا لم يكن الحقل موجود بالفعل
            if (!Schema::hasColumn('courses', 'certificate_available')) {
                $table->boolean('certificate_available')->default(false)
                      ->comment('ما إذا كانت الدورة توفر شهادة إتمام');
            }
            
            $table->string('certificate_type')->default('default')
                  ->comment('نوع الشهادة: default, custom');
                  
            $table->string('custom_certificate_path')->nullable()
                  ->comment('مسار الشهادة المخصصة المرفوعة');
                  
            $table->text('certificate_text')->nullable()
                  ->comment('نص مخصص للشهادة');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // لا نحذف certificate_available إذا كان موجوداً من قبل
            
            $table->dropColumn('certificate_type');
            $table->dropColumn('custom_certificate_path');
            $table->dropColumn('certificate_text');
        });
    }
};
