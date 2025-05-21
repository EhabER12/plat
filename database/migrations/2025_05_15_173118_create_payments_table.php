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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->string('student_id');
            $table->string('course_id');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method');
            $table->timestamp('payment_date')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->json('payment_details')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Add indexes for faster lookups
            $table->index('student_id');
            $table->index('course_id');
            $table->index('transaction_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
