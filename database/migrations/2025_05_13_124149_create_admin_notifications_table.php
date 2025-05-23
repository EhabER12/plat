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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // نوع الإشعار: flagged_message, system_alert, etc.
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->nullOnDelete(); // المستخدم المرتبط بالإشعار
            $table->unsignedBigInteger('related_id')->nullable(); // معرف العنصر المرتبط (رسالة، مستخدم، إلخ)
            $table->string('related_type', 50)->nullable(); // نوع العنصر المرتبط
            $table->text('content'); // محتوى الإشعار
            $table->json('data')->nullable(); // بيانات إضافية بتنسيق JSON
            $table->boolean('is_read')->default(false); // هل تم قراءة الإشعار
            $table->dateTime('read_at')->nullable(); // وقت قراءة الإشعار
            $table->tinyInteger('severity')->default(1); // مستوى أهمية الإشعار (1-5)
            $table->timestamps();
            
            // إنشاء فهارس للبحث السريع
            $table->index('type');
            $table->index('is_read');
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
