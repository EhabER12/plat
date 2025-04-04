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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id('withdrawal_id');
            $table->unsignedBigInteger('instructor_id');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('paypal_email')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('requested_at')->default(now());
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('instructor_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
