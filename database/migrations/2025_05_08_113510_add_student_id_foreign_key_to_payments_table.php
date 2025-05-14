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
            // إضافة مفتاح أجنبي إذا كان عمود student_id موجود
            if (Schema::hasColumn('payments', 'student_id')) {
                // لا يوجد طريقة مباشرة للتحقق من وجود مفتاح أجنبي، سنحاول إضافته داخل try-catch
                try {
                    $table->foreign('student_id', 'payments_student_id_foreign')
                          ->references('user_id')
                          ->on('users')
                          ->onDelete('set null')
                          ->onUpdate('cascade');
                } catch (\Exception $e) {
                    // المفتاح الأجنبي موجود بالفعل
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // حذف المفتاح الأجنبي عند التراجع عن الهجرة
            if (Schema::hasColumn('payments', 'student_id')) {
                try {
                    $table->dropForeign('payments_student_id_foreign');
                } catch (\Exception $e) {
                    // المفتاح الأجنبي قد لا يكون موجوداً
                }
            }
        });
    }
};
