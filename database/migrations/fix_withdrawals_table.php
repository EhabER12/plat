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
        if (!Schema::hasTable('withdrawals')) {
            Schema::create('withdrawals', function (Blueprint $table) {
                $table->id('withdrawal_id');
                $table->unsignedBigInteger('instructor_id');
                $table->decimal('amount', 10, 2);
                $table->enum('status', ['pending', 'completed', 'rejected', 'failed'])->default('pending');
                $table->string('payment_method')->nullable();
                $table->string('bank_account')->nullable();
                $table->string('paypal_email')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('requested_at')->default(now());
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
                
                // Adding foreign key if users table exists
                if (Schema::hasTable('users')) {
                    $table->foreign('instructor_id')->references('user_id')->on('users')->onDelete('cascade');
                }
            });
        } else {
            // Ensure the status enum includes 'rejected'
            Schema::table('withdrawals', function (Blueprint $table) {
                // We can't easily modify enums in migrations, so we'll check in php code
                // Add any missing columns here if needed
                if (!Schema::hasColumn('withdrawals', 'processed_at')) {
                    $table->timestamp('processed_at')->nullable()->after('requested_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop the table in down() to prevent data loss
    }
}; 