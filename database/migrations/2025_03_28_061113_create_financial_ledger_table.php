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
        Schema::create('financial_ledger', function (Blueprint $table) {
            $table->id('ledger_id');
            $table->enum('transaction_type', ['revenue', 'expense', 'profit', 'instructor_payment']);
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->unsignedBigInteger('withdrawal_id')->nullable();
            $table->string('description');
            $table->date('transaction_date');
            $table->timestamps();
            
            $table->foreign('user_id')->references('user_id')->on('users')->nullOnDelete();
            $table->foreign('payment_id')->references('payment_id')->on('payments')->nullOnDelete();
            $table->foreign('withdrawal_id')->references('withdrawal_id')->on('withdrawals')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_ledger');
    }
};
