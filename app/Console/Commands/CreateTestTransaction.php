<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Enrollment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateTestTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test transaction for payment testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Create a transaction record
            $transaction = Transaction::create([
                'user_id' => 1,
                'amount' => 399.99,
                'currency' => 'EGP',
                'status' => 'completed',
                'payment_method' => 'paymob',
                'transaction_type' => 'payment',
                'reference_id' => 3,
                'reference_type' => 'course',
                'gateway_transaction_id' => 'paymob_test_QRKyaoYPF2WFEavT5oEu',
                'gateway_response' => ['success' => true, 'message' => 'Simulated payment successful'],
                'description' => 'Enrollment in course: React UI Development (Simulated)',
                'ip_address' => '127.0.0.1'
            ]);

            $this->info('Transaction created successfully with ID: ' . $transaction->transaction_id);

            // Create a payment record using DB facade to avoid model issues
            $paymentId = DB::table('payments')->insertGetId([
                'user_id' => 1,
                'course_id' => 3,
                'amount' => 399.99,
                'payment_method' => 'paymob',
                'paid_at' => now(),
                'status' => 'completed',
                'transaction_id' => $transaction->transaction_id,
                'payment_details' => json_encode(['notes' => 'Simulated payment via Paymob']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info('Payment created successfully with ID: ' . $paymentId);

            // Create an enrollment record
            $enrollment = Enrollment::create([
                'student_id' => 1,
                'course_id' => 3,
                'enrolled_at' => now(),
                'status' => 'active',
            ]);

            $this->info('Enrollment created successfully with ID: ' . $enrollment->enrollment_id);

            return 0;
        } catch (\Exception $e) {
            $this->error('Error creating test transaction: ' . $e->getMessage());
            return 1;
        }
    }
}
