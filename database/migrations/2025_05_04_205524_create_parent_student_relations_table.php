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
        if (!Schema::hasTable('parent_student_relations')) {
            Schema::create('parent_student_relations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id'); // معرف ولي الأمر
                $table->unsignedBigInteger('student_id')->nullable(); // معرف الطالب (قد يكون null إذا لم يتم التحقق بعد)
                $table->string('student_name'); // اسم الطالب كما أدخله ولي الأمر
                $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending'); // حالة التحقق
                $table->text('verification_notes')->nullable(); // ملاحظات المشرف عند التحقق
                $table->string('birth_certificate')->nullable(); // مسار شهادة الميلاد
                $table->string('parent_id_card')->nullable(); // مسار بطاقة ولي الأمر
                $table->string('additional_document')->nullable(); // مسار لوثيقة إضافية
                $table->timestamp('verified_at')->nullable(); // تاريخ التحقق
                $table->timestamps();
                
                // إضافة المفاتيح الأجنبية
                $table->foreign('parent_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('student_id')->references('user_id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_student_relations');
    }
};
