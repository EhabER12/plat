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
        Schema::create('payment_coupons', function (Blueprint $table) {
            $table->id('payment_coupon_id');
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('coupon_id');
            $table->decimal('discount_percentage', 5, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();
            
            $table->foreign('payment_id')->references('payment_id')->on('payments')->onDelete('cascade');
            $table->foreign('coupon_id')->references('coupon_id')->on('coupons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_coupons');
    }
};
