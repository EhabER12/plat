<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('instructor_payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->string('account_type')->default('bank'); // bank, paypal, etc.
            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('paypal_email')->nullable();
            $table->text('payment_details')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->foreign('instructor_id')->references('user_id')->on('users')->onDelete('cascade');
        });

        // Add a default payment account for instructor_id = 4
        DB::table('instructor_payment_accounts')->insert([
            'instructor_id' => 4,
            'account_type' => 'bank',
            'bank_name' => 'Sample Bank',
            'account_name' => 'Instructor Account',
            'account_number' => '1234567890',
            'is_active' => true,
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_payment_accounts');
    }
};
