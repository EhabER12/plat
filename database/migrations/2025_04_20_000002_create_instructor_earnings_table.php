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
        Schema::create('instructor_earnings', function (Blueprint $table) {
            $table->id('earning_id');
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('payment_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('platform_fee', 10, 2);
            $table->enum('status', ['pending', 'available', 'withdrawn', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('withdrawal_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('instructor_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
            $table->foreign('payment_id')->references('payment_id')->on('payments')->onDelete('cascade');
            $table->foreign('withdrawal_id')->references('withdrawal_id')->on('withdrawals')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_earnings');
    }
};
