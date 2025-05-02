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
        Schema::create('instructor_payment_accounts', function (Blueprint $table) {
            $table->id('account_id');
            $table->unsignedBigInteger('instructor_id');
            $table->string('payment_provider')->default('paymob');
            $table->string('provider_account_id')->nullable();
            $table->string('account_name')->nullable();
            $table->json('account_details')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('instructor_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_payment_accounts');
    }
};
