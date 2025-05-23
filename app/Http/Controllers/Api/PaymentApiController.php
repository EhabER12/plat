<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentApiController extends Controller
{
    protected $paymobService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\PaymobService  $paymobService
     * @return void
     */
    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }

    /**
     * Get payment history for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentHistory()
    {
        $payments = Payment::where('student_id', Auth::id())
            ->with(['course', 'transaction'])
            ->orderBy('payment_date', 'desc')
            ->get();

        return response()->json([
            'payments' => $payments,
            'message' => 'Payment history retrieved successfully'
        ]);
    }

    /**
     * Initiate a payment for a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiatePayment(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = Auth::user();

        // Check if user is already enrolled
        $existingEnrollment = Enrollment::where('student_id', $user->user_id)
            ->where('course_id', $courseId)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'message' => 'You are already enrolled in this course',
                'enrollment' => $existingEnrollment
            ], 400);
        }

        // Validate payment information
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string|in:paymob,credit_card,vodafone_cash',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|string|max:20',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Create a transaction record
            $transactionId = 'TRX' . time() . rand(1000, 9999);
            
            $transaction = Transaction::create([
                'user_id' => $user->user_id,
                'amount' => $course->price,
                'currency' => 'USD',
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'transaction_type' => 'enrollment',
                'reference_id' => $course->course_id,
                'reference_type' => 'course',
                'gateway_transaction_id' => $transactionId,
                'description' => 'Enrollment in course: ' . $course->title,
            ]);

            // Create a payment record
            $payment = Payment::create([
                'student_id' => $user->user_id,
                'course_id' => $course->course_id,
                'amount' => $course->price,
                'payment_method' => $request->payment_method,
                'payment_date' => now(),
                'status' => 'pending',
                'transaction_id' => $transaction->transaction_id,
                'notes' => 'Payment initiated',
            ]);

            // Prepare payment data for Paymob
            $paymentData = [
                'amount_cents' => $course->price * 100, // Convert to cents
                'currency' => 'EGP',
                'merchant_order_id' => 'course_' . $course->course_id . '_user_' . $user->user_id . '_' . time(),
                'items' => [
                    [
                        'name' => $course->title,
                        'amount_cents' => $course->price * 100,
                        'description' => substr($course->description, 0, 100) . '...',
                        'quantity' => 1
                    ]
                ],
                'billing_data' => [
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'street' => $request->street,
                    'city' => $request->city,
                    'country' => $request->country,
                    'state' => $request->state,
                    'postal_code' => $request->postal_code,
                ]
            ];

            // Process payment with Paymob
            $result = $this->paymobService->processPayment($paymentData);

            if (!$result['success']) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Payment processing failed',
                    'error' => $result['message']
                ], 500);
            }

            DB::commit();

            return response()->json([
                'message' => 'Payment initiated successfully',
                'payment_id' => $payment->payment_id,
                'iframe_url' => $result['iframe_url'],
                'transaction_id' => $transaction->transaction_id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment initiation error: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Payment initiation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status.
     *
     * @param  int  $paymentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPaymentStatus($paymentId)
    {
        $payment = Payment::where('payment_id', $paymentId)
            ->where('student_id', Auth::id())
            ->with(['transaction', 'course'])
            ->firstOrFail();

        return response()->json([
            'payment' => $payment,
            'status' => $payment->status,
            'message' => 'Payment status retrieved successfully'
        ]);
    }

    /**
     * Process payment callback from Paymob.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymobCallback(Request $request)
    {
        Log::info('Paymob callback received', $request->all());

        try {
            $data = $request->all();
            $hmac = $request->header('HMAC');

            // Verify the webhook signature if HMAC is provided
            if ($hmac && !$this->paymobService->verifyWebhook($data, $hmac)) {
                Log::error('Paymob webhook verification failed', [
                    'hmac' => $hmac,
                    'data' => $data
                ]);
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
            }

            // Get transaction details
            $transactionId = $data['order']['id'] ?? null;
            $success = $data['success'] ?? false;
            $isVoided = $data['is_voided'] ?? false;
            $isRefunded = $data['is_refunded'] ?? false;
            $errorOccurred = $data['error_occured'] ?? false;

            if (!$transactionId) {
                Log::error('Missing transaction ID in Paymob callback', $data);
                return response()->json(['status' => 'error', 'message' => 'Missing transaction ID'], 400);
            }

            // Find the transaction in our database
            $transaction = Transaction::where('gateway_transaction_id', $transactionId)->first();

            if (!$transaction) {
                Log::error('Paymob transaction not found', [
                    'transaction_id' => $transactionId,
                    'data' => $data
                ]);
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            // Find the payment
            $payment = Payment::where('transaction_id', $transaction->transaction_id)->first();

            if (!$payment) {
                Log::error('Payment not found for transaction', [
                    'transaction_id' => $transaction->transaction_id,
                    'data' => $data
                ]);
                return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
            }

            DB::beginTransaction();

            // Determine the transaction status
            if ($isVoided || $isRefunded || $errorOccurred || !$success) {
                $transaction->status = 'failed';
                $payment->status = 'failed';
            } else if ($success) {
                $transaction->status = 'completed';
                $payment->status = 'completed';

                // Create enrollment if not exists
                Enrollment::firstOrCreate(
                    [
                        'student_id' => $payment->student_id,
                        'course_id' => $payment->course_id,
                    ],
                    [
                        'user_id' => $payment->student_id,
                        'enrolled_at' => now(),
                        'status' => 'active',
                    ]
                );
            }

            // Save updated records
            $transaction->gateway_response = array_merge($transaction->gateway_response ?? [], $data);
            $transaction->save();
            $payment->save();

            DB::commit();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Paymob callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }
}
