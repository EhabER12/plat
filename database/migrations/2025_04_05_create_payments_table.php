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
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50);
            $table->timestamp('payment_date');
            $table->string('status', 20);
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamps();
            
            $table->foreign('student_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('course_id')->on('courses')->onDelete('cascade');
            $table->foreign('transaction_id')->references('transaction_id')->on('transactions')->onDelete('set null');
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
