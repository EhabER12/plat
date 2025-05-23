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
        Schema::create('banned_words', function (Blueprint $table) {
            $table->id();
            $table->string('word', 100); // الكلمة أو العبارة المحظورة
            $table->string('type', 50)->default('general'); // نوع الحظر: شتائم، معلومات شخصية، إلخ
            $table->text('replacement')->nullable(); // البديل الاختياري للكلمة (مثل النجوم ***)
            $table->tinyInteger('severity')->default(1); // خطورة المخالفة (1-5)
            $table->boolean('active')->default(true); // هل الكلمة مفعلة في التصفية
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->timestamps();
            
            // إنشاء فهرس للكلمة للبحث السريع
            $table->index('word');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banned_words');
    }
};
