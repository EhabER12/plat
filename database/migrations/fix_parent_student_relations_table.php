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
        // Check if table exists and add missing columns
        if (Schema::hasTable('parent_student_relations')) {
            Schema::table('parent_student_relations', function (Blueprint $table) {
                // Add verification_status column if it doesn't exist
                if (!Schema::hasColumn('parent_student_relations', 'verification_status')) {
                    $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending')->after('student_name');
                }

                // Add other missing columns if they don't exist
                if (!Schema::hasColumn('parent_student_relations', 'verification_notes')) {
                    $table->text('verification_notes')->nullable()->after('verification_status');
                }

                if (!Schema::hasColumn('parent_student_relations', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable()->after('verification_notes');
                }

                if (!Schema::hasColumn('parent_student_relations', 'verified_by')) {
                    $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
                }

                if (!Schema::hasColumn('parent_student_relations', 'token')) {
                    $table->string('token', 100)->nullable()->after('verified_by');
                    $table->index('token');
                }

                if (!Schema::hasColumn('parent_student_relations', 'relation_type')) {
                    $table->string('relation_type')->default('parent')->after('student_name');
                }

                if (!Schema::hasColumn('parent_student_relations', 'notes')) {
                    $table->text('notes')->nullable()->after('additional_document');
                }
            });
        } else {
            // Create the table if it doesn't exist
            Schema::create('parent_student_relations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id'); // معرف ولي الأمر
                $table->unsignedBigInteger('student_id')->nullable(); // معرف الطالب (قد يكون null إذا لم يتم التحقق بعد)
                $table->string('student_name'); // اسم الطالب كما أدخله ولي الأمر
                $table->string('relation_type')->default('parent'); // نوع العلاقة
                $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending'); // حالة التحقق
                $table->text('verification_notes')->nullable(); // ملاحظات المشرف عند التحقق
                $table->string('birth_certificate')->nullable(); // مسار شهادة الميلاد
                $table->string('parent_id_card')->nullable(); // مسار بطاقة ولي الأمر
                $table->string('additional_document')->nullable(); // مسار لوثيقة إضافية
                $table->text('notes')->nullable(); // ملاحظات إضافية
                $table->timestamp('verified_at')->nullable(); // تاريخ التحقق
                $table->unsignedBigInteger('verified_by')->nullable(); // من قام بالتحقق
                $table->string('token', 100)->nullable(); // رمز التحقق
                $table->timestamps();

                // إضافة المفاتيح الأجنبية
                $table->foreign('parent_id')->references('user_id')->on('users')->onDelete('cascade');
                $table->foreign('student_id')->references('user_id')->on('users')->onDelete('set null');
                $table->foreign('verified_by')->references('user_id')->on('users')->onDelete('set null');

                // إضافة الفهارس
                $table->index('token');
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