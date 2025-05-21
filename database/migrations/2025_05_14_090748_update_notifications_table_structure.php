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
        // تحقق من وجود جدول الإشعارات
        if (Schema::hasTable('notifications')) {
            // إضافة أو تعديل الأعمدة الضرورية
            Schema::table('notifications', function (Blueprint $table) {
                // إزالة المفاتيح الأجنبية إذا وجدت للتمكن من إجراء التعديلات
                try {
                    if (Schema::hasColumn('notifications', 'user_id')) {
                        // محاولة حذف المفتاح الأجنبي إذا كان موجودًا
                        DB::statement('ALTER TABLE notifications DROP FOREIGN KEY IF EXISTS notifications_user_id_foreign');
                    }
                } catch (\Exception $e) {
                    // تجاهل الخطأ إذا لم يكن هناك مفتاح أجنبي
                }

                // التحقق من وجود العمود وإضافته إذا لم يكن موجودًا
                if (!Schema::hasColumn('notifications', 'title')) {
                    $table->string('title')->nullable();
                }
                if (!Schema::hasColumn('notifications', 'message')) {
                    $table->text('message')->nullable();
                }
                if (!Schema::hasColumn('notifications', 'type')) {
                    $table->string('type')->nullable();
                }
                if (!Schema::hasColumn('notifications', 'related_entity')) {
                    $table->string('related_entity')->nullable();
                }
                if (!Schema::hasColumn('notifications', 'entity_id')) {
                    $table->unsignedBigInteger('entity_id')->nullable();
                }
                if (!Schema::hasColumn('notifications', 'sent_at')) {
                    $table->timestamp('sent_at')->nullable();
                }
                if (!Schema::hasColumn('notifications', 'read_at')) {
                    $table->timestamp('read_at')->nullable();
                }
            });

            // إعادة إنشاء المفتاح الأجنبي بعد التعديل
            try {
                Schema::table('notifications', function (Blueprint $table) {
                    if (Schema::hasColumn('notifications', 'user_id')) {
                        $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
                    }
                });
            } catch (\Exception $e) {
                // تجاهل الخطأ إذا كان هناك مشكلة في إنشاء المفتاح الأجنبي
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نقوم بأي شيء في حالة التراجع لأن حذف الأعمدة قد يسبب مشاكل
    }
};
