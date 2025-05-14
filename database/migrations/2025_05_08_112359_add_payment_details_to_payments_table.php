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
            // إضافة عمود payment_details إذا لم يكن موجوداً بالفعل
            if (!Schema::hasColumn('payments', 'payment_details')) {
                $table->json('payment_details')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // حذف العمود عند التراجع عن الهجرة
            if (Schema::hasColumn('payments', 'payment_details')) {
                $table->dropColumn('payment_details');
            }
        });
    }
};
