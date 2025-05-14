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
            $table->unsignedBigInteger('coupon_id')->nullable()->after('payment_date');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('coupon_id');
            $table->json('metadata')->nullable()->after('payment_details');
            
            // Add foreign key to coupons table
            $table->foreign('coupon_id')->references('coupon_id')->on('coupons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'discount_amount', 'metadata']);
        });
    }
};
