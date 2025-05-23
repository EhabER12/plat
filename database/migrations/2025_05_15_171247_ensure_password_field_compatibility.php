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
            // تحقق مما إذا كان حقل password_hash موجودًا
            $passwordHashExists = Schema::hasColumn('users', 'password_hash');
            $passwordExists = Schema::hasColumn('users', 'password');
            
            // إذا وجدنا كلاهما، ننسخ القيم من password_hash إلى password ثم نحذف password_hash
            if ($passwordHashExists && $passwordExists) {
                DB::statement('UPDATE users SET password = password_hash WHERE password IS NULL OR password = ""');
                
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('password_hash');
                });
                
                DB::statement("INSERT INTO migrations (migration, batch) VALUES ('copied_password_hash_to_password_and_dropped_column', 9999)");
            }
            // إذا وجدنا فقط password_hash، نقوم بإعادة تسميته إلى password
            elseif ($passwordHashExists && !$passwordExists) {
                Schema::table('users', function (Blueprint $table) {
                    $table->renameColumn('password_hash', 'password');
                });
                
                DB::statement("INSERT INTO migrations (migration, batch) VALUES ('renamed_password_hash_to_password', 9999)");
            }
            // إذا لم نجد أيًا منهما، نضيف حقل password
            elseif (!$passwordExists && !$passwordHashExists) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('password')->nullable();
                });
                
                DB::statement("INSERT INTO migrations (migration, batch) VALUES ('added_password_column', 9999)");
            }
            
            // تأكد من نسخ قيمة id إلى user_id مرة أخرى
            if (Schema::hasColumn('users', 'id') && Schema::hasColumn('users', 'user_id')) {
                DB::statement('UPDATE users SET user_id = id WHERE user_id IS NULL OR user_id = 0');
                DB::statement("INSERT INTO migrations (migration, batch) VALUES ('ensured_user_id_values', 9999)");
            }
            
        } catch (\Exception $e) {
            DB::statement("INSERT INTO migrations (migration, batch) VALUES ('error_ensuring_password_field_" . $e->getCode() . "', 9999)");
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
