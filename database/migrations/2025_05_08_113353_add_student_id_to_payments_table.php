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
        Schema::table('payments', function (Blueprint $table) {
            // إضافة عمود student_id إذا لم يكن موجوداً بالفعل
            if (!Schema::hasColumn('payments', 'student_id')) {
                $table->unsignedBigInteger('student_id')->nullable()->after('payment_id');
            }
        });
        
        // نسخ قيم user_id إلى student_id إذا كان موجوداً
        if (Schema::hasColumn('payments', 'user_id') && Schema::hasColumn('payments', 'student_id')) {
            DB::statement('UPDATE payments SET student_id = user_id WHERE student_id IS NULL AND user_id IS NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // حذف العمود عند التراجع عن الهجرة
            if (Schema::hasColumn('payments', 'student_id')) {
                $table->dropColumn('student_id');
            }
        });
    }
};
