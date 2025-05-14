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
        Schema::table('withdrawals', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawals', 'payment_provider')) {
                $table->string('payment_provider')->nullable()->after('status')->comment('طريقة الدفع: vodafone_cash, instapay');
            }
            
            if (!Schema::hasColumn('withdrawals', 'provider_account_id')) {
                $table->string('provider_account_id')->nullable()->after('payment_provider')->comment('رقم الحساب أو رقم الهاتف');
            }
            
            if (!Schema::hasColumn('withdrawals', 'transfer_receipt')) {
                $table->string('transfer_receipt')->nullable()->after('provider_account_id')->comment('صورة إثبات التحويل');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $columns = ['payment_provider', 'provider_account_id', 'transfer_receipt'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('withdrawals', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
