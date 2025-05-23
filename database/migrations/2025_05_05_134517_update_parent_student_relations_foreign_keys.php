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
        // إضافة المفاتيح الأجنبية فقط إذا لم تكن موجودة بالفعل
        try {
            Schema::table('parent_student_relations', function (Blueprint $table) {
                // محاولة إضافة المفاتيح الأجنبية
                try {
                    $table->foreign('parent_id')->references('user_id')->on('users')->onDelete('cascade');
                } catch (\Exception $e) {
                    // المفتاح الأجنبي ربما موجود بالفعل، نتجاهل الخطأ
                }
                
                try {
                    $table->foreign('student_id')->references('user_id')->on('users')->onDelete('set null');
                } catch (\Exception $e) {
                    // المفتاح الأجنبي ربما موجود بالفعل، نتجاهل الخطأ
                }
            });
        } catch (\Exception $e) {
            // تجاهل أي أخطاء أخرى
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نقوم بإزالة المفاتيح الأجنبية في هذه الهجرة
    }
};
