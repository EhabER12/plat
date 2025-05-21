<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // الحصول على معرف المستخدم المسؤول الموجود
        $admin = DB::table('users')->where('email', 'admin@example.com')->first();
        $adminId = $admin ? $admin->user_id : null;

        if (!$adminId) {
            // إضافة المستخدم المسؤول (admin) إذا لم يكن موجوداً
            $adminId = DB::table('users')->insertGetId([
                'name' => 'مسؤول النظام',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'phone' => '01000000000',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // إضافة دور المسؤول
            DB::table('user_roles')->insert([
                'user_id' => $adminId,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // التحقق من وجود مستخدم طالب
        $student = DB::table('users')->where('email', 'student@example.com')->first();
        $studentId = null;

        if (!$student) {
            // إضافة المستخدم الطالب (student)
            $studentId = DB::table('users')->insertGetId([
                'name' => 'محمد الطالب',
                'email' => 'student@example.com',
                'password' => Hash::make('student123'),
                'phone' => '01200000000',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // إضافة دور الطالب
            DB::table('user_roles')->insert([
                'user_id' => $studentId,
                'role' => 'student',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $studentId = $student->user_id;
        }

        // التحقق من وجود مستخدم ولي أمر
        $parent = DB::table('users')->where('email', 'parent@example.com')->first();
        $parentId = null;

        if (!$parent) {
            // إضافة مستخدم ولي أمر (parent)
            $parentId = DB::table('users')->insertGetId([
                'name' => 'سمير ولي الأمر',
                'email' => 'parent@example.com',
                'password' => Hash::make('parent123'),
                'phone' => '01300000000',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // إضافة دور ولي الأمر
            DB::table('user_roles')->insert([
                'user_id' => $parentId,
                'role' => 'parent',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $parentId = $parent->user_id;
        }

        // التحقق من وجود علاقة بين ولي الأمر والطالب
        if ($parentId && $studentId) {
            $relationExists = DB::table('parent_student_relations')
                ->where('parent_id', $parentId)
                ->where('student_id', $studentId)
                ->exists();

            if (!$relationExists) {
                // إضافة علاقة بين ولي الأمر والطالب
                DB::table('parent_student_relations')->insert([
                    'parent_id' => $parentId,
                    'student_id' => $studentId,
                    'student_name' => 'محمد الطالب',
                    'verification_status' => 'approved',
                    'verification_notes' => 'تم التحقق من العلاقة',
                    'verified_by' => $adminId,
                    'verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // العثور على معرفات المستخدمين
        $student = DB::table('users')->where('email', 'student@example.com')->first();
        $parent = DB::table('users')->where('email', 'parent@example.com')->first();

        if ($parent && $student) {
            // حذف علاقة ولي الأمر بالطالب
            DB::table('parent_student_relations')
                ->where('parent_id', $parent->user_id)
                ->where('student_id', $student->user_id)
                ->delete();
        }

        // حذف أدوار المستخدمين ثم حذف المستخدمين
        if ($student) {
            DB::table('user_roles')->where('user_id', $student->user_id)->delete();
            DB::table('users')->where('user_id', $student->user_id)->delete();
        }

        if ($parent) {
            DB::table('user_roles')->where('user_id', $parent->user_id)->delete();
            DB::table('users')->where('user_id', $parent->user_id)->delete();
        }
    }
}; 