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
        try {
            // تحقق من وجود حقل user_id
            if (!Schema::hasColumn('users', 'user_id')) {
                // إذا لم يكن موجودًا، أضفه
                Schema::table('users', function (Blueprint $table) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                });
                
                // نسخ قيم id إلى user_id
                DB::statement('UPDATE users SET user_id = id');
                
                // إضافة فهرس
                Schema::table('users', function (Blueprint $table) {
                    $table->index('user_id');
                });
            } else {
                // إذا كان موجودًا، تأكد من أن جميع القيم صحيحة
                DB::statement('UPDATE users SET user_id = id WHERE user_id IS NULL OR user_id = 0');
            }
            
            // تأكد من أن جميع السجلات في جدول user_roles تحتوي على user_id صالح
            if (Schema::hasTable('user_roles')) {
                // تحديث user_roles، كن حذرًا! نحن نستخدم الدالة raw SQL
                DB::statement('
                    UPDATE user_roles ur 
                    JOIN users u ON ur.user_id = u.id 
                    SET ur.user_id = u.user_id 
                    WHERE ur.user_id != u.user_id
                ');
            }
            
            // تسجيل تنفيذ هذا التصحيح في جدول migrations
            DB::statement("INSERT INTO migrations (migration, batch) VALUES ('fix_user_registration_manual_update', 9999)");
            
        } catch (\Exception $e) {
            // تسجيل الخطأ
            DB::statement("INSERT INTO migrations (migration, batch) VALUES ('error_fixing_users_" . $e->getCode() . "', 9999)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نريد عكس هذه التغييرات
    }
};
