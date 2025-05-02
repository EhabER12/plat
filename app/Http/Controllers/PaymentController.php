<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\FinancialLedger;
use App\Models\InstructorEarning;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Transaction;
use App\Services\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    /**
     * The Paymob service instance.
     *
     * @var \App\Services\PaymobService
     */
    protected $paymobService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\PaymobService $paymobService
     * @return void
     */
    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }
    /**
     * Show the checkout page for a course.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function checkout($courseId)
    {
        try {
            $course = Course::where('approval_status', 'approved')->findOrFail($courseId);
            $user = Auth::user();

            // Check if already enrolled
            $existingEnrollment = Enrollment::where('student_id', $user->user_id)
                ->where('course_id', $courseId)
                ->first();

            if ($existingEnrollment) {
                return redirect()->route('student.course-content', $courseId)
                    ->with('info', 'You are already enrolled in this course');
            }

            // For free courses, enroll directly
            if ($course->price <= 0) {
                // Create enrollment
                Enrollment::create([
                    'student_id' => $user->user_id,
                    'course_id' => $course->course_id,
                    'status' => 'active',
                    'enrolled_at' => now()
                ]);

                return redirect()->route('student.course-content', $course->course_id)
                    ->with('success', 'Successfully enrolled in the free course');
            }

            // For paid courses, proceed to payment
            return view('payments.checkout', compact('course', 'user'));
        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->route('courses.show', $courseId)
                ->with('error', 'An error occurred while processing your checkout. Please try again.');
        }
    }

    /**
     * Process the payment using Stripe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processStripePayment(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = Auth::user();

        // Validate the request
        $request->validate([
            'stripeToken' => 'required',
        ]);

        try {
            // This would be the actual Stripe integration
            // For now, we'll simulate a successful payment

            // In a real application, you would use the Stripe PHP SDK:
            // \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            // $charge = \Stripe\Charge::create([
            //     'amount' => $course->price * 100, // Amount in cents
            //     'currency' => 'usd',
            //     'description' => 'Enrollment in course: ' . $course->title,
            //     'source' => $request->stripeToken,
            //     'metadata' => [
            //         'course_id' => $course->course_id,
            //         'user_id' => $user->user_id,
            //     ],
            // ]);

            // Simulate a successful payment
            $transactionId = 'stripe_' . Str::random(20);

            // Create a transaction record
            $transaction = Transaction::create([
                'user_id' => $user->user_id,
                'amount' => $course->price,
                'currency' => 'USD',
                'status' => Transaction::STATUS_COMPLETED,
                'payment_method' => 'credit_card',
                'transaction_type' => Transaction::TYPE_PAYMENT,
                'reference_id' => $course->course_id,
                'reference_type' => 'course',
                'gateway_transaction_id' => $transactionId,
                'gateway_response' => ['success' => true, 'message' => 'Payment successful'],
                'description' => 'Enrollment in course: ' . $course->title,
                'ip_address' => $request->ip(),
            ]);

            // Create a payment record
            $payment = Payment::create([
                'student_id' => $user->user_id,
                'course_id' => $course->course_id,
                'amount' => $course->price,
                'payment_method' => 'credit_card',
                'payment_date' => now(),
                'status' => 'completed',
                'transaction_id' => $transaction->transaction_id,
                'notes' => 'Payment processed via Stripe',
                'payment_details' => json_encode([
                    'transaction_id' => $transactionId,
                ])
            ]);

            // Create an enrollment record
            $enrollment = Enrollment::create([
                'student_id' => $user->user_id,
                'course_id' => $course->course_id,
                'enrolled_at' => now(),
                'status' => 'active',
                'payment_id' => $payment->payment_id,
            ]);

            return redirect()->route('student.course-content', $course->course_id)
                ->with('success', 'Payment successful! You are now enrolled in the course.');

        } catch (\Exception $e) {
            Log::error('Stripe payment error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Process the payment using Vodafone Cash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processVodafonePayment(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = Auth::user();

        // Validate the request
        $request->validate([
            'phone_number' => 'required|regex:/^01[0-9]{9}$/',
            'transaction_reference' => 'required|min:6',
        ]);

        try {
            // In a real application, you would verify the Vodafone Cash payment
            // with their API or a manual verification process

            // For now, we'll simulate a pending payment that requires manual verification
            $transactionId = 'vodafone_' . Str::random(20);

            // Create a transaction record
            $transaction = Transaction::create([
                'user_id' => $user->user_id,
                'amount' => $course->price,
                'currency' => 'EGP',
                'status' => Transaction::STATUS_PENDING,
                'payment_method' => 'vodafone_cash',
                'transaction_type' => Transaction::TYPE_PAYMENT,
                'reference_id' => $course->course_id,
                'reference_type' => 'course',
                'gateway_transaction_id' => $transactionId,
                'gateway_response' => [
                    'phone_number' => $request->phone_number,
                    'transaction_reference' => $request->transaction_reference,
                ],
                'description' => 'Enrollment in course: ' . $course->title,
                'ip_address' => $request->ip(),
            ]);

            // Create a payment record
            $payment = Payment::create([
                'student_id' => $user->user_id,
                'course_id' => $course->course_id,
                'amount' => $course->price,
                'payment_method' => 'vodafone_cash',
                'payment_date' => now(),
                'status' => 'pending',
                'transaction_id' => $transaction->transaction_id,
                'notes' => 'Payment via Vodafone Cash. Reference: ' . $request->transaction_reference,
                'payment_details' => json_encode([
                    'phone_number' => $request->phone_number,
                    'transaction_reference' => $request->transaction_reference,
                ])
            ]);

            return redirect()->route('payment.pending', $payment->payment_id)
                ->with('info', 'Your payment is being processed. You will be enrolled in the course once the payment is verified.');

        } catch (\Exception $e) {
            Log::error('Vodafone Cash payment error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Show the pending payment page.
     *
     * @param  int  $paymentId
     * @return \Illuminate\View\View
     */
    public function showPendingPayment($paymentId)
    {
        $payment = Payment::with(['course', 'student'])->findOrFail($paymentId);

        // Ensure the payment belongs to the current user
        if ($payment->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('payments.pending', compact('payment'));
    }

    /**
     * Show the payment success page.
     *
     * @param  int  $paymentId
     * @return \Illuminate\View\View
     */
    public function showSuccessPayment($paymentId)
    {
        $payment = Payment::with(['course', 'student'])->findOrFail($paymentId);

        // Ensure the payment belongs to the current user
        if ($payment->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('payments.success', compact('payment'));
    }

    /**
     * Show the payment failure page.
     *
     * @param  int  $paymentId
     * @return \Illuminate\View\View
     */
    public function showFailedPayment($paymentId)
    {
        $payment = Payment::with(['course', 'student'])->findOrFail($paymentId);

        // Ensure the payment belongs to the current user
        if ($payment->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('payments.failed', compact('payment'));
    }

    /**
     * Process the payment using Paymob.
     *
     * @param  \App\Http\Requests\PaymentRequest  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPaymobPayment(Request $request, $courseId)
    {
        // Log the request for debugging
        Log::info('Paymob payment request received', [
            'course_id' => $courseId,
            'user_id' => Auth::id(),
            'request_data' => $request->all(),
            'headers' => $request->header(),
            'method' => $request->method(),
            'url' => $request->url(),
            'is_ajax' => $request->ajax()
        ]);

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            try {
                DB::beginTransaction();

                $course = Course::findOrFail($courseId);
                $user = Auth::user();

                if (!$user) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'User not authenticated'
                    ], 401);
                }

            // Check if already enrolled
            $existingEnrollment = Enrollment::where('student_id', $user->user_id)
                ->where('course_id', $courseId)
                ->first();

            if ($existingEnrollment) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'You are already enrolled in this course',
                    'redirect' => route('student.course-content', $courseId)
                ]);
            }

            // Check if Paymob is configured
            if (empty(config('services.paymob.api_key')) ||
                empty(config('services.paymob.integration_id')) ||
                empty(config('services.paymob.iframe_id'))) {
                Log::warning('Paymob not properly configured, using simulation mode');
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway not properly configured. Please contact the administrator.',
                    'simulation' => true
                ]);
            }

            // Prepare billing data from validated request
            $billingData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'street' => $request->street,
                'city' => $request->city,
                'country' => $request->country,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                // Additional required fields for Paymob
                'shipping_method' => 'NA',
                'apartment' => 'NA',
                'floor' => 'NA',
                'building' => 'NA',
            ];

            // Convert price to cents (Paymob requires amount in cents)
            $amountCents = (int)($course->price * 100);

            // Generate a unique merchant order ID
            $merchantOrderId = 'course_' . $course->course_id . '_user_' . $user->user_id . '_' . time();

            // Process payment with Paymob
            $paymentData = [
                'amount_cents' => $amountCents,
                'currency' => 'EGP',
                'merchant_order_id' => $merchantOrderId,
                'items' => [
                    [
                        'name' => $course->title,
                        'amount_cents' => $amountCents,
                        'description' => substr($course->description ?? '', 0, 100),
                        'quantity' => 1
                    ]
                ],
                'billing_data' => $billingData
            ];

            $result = $this->paymobService->processPayment($paymentData);

            if (!$result['success']) {
                DB::rollBack();
                Log::error('Paymob payment processing failed', [
                    'error' => $result['message'],
                    'course_id' => $course->course_id,
                    'user_id' => $user->user_id
                ]);

                // For AJAX requests, return JSON with option to use simulation mode
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway error: ' . $result['message'] . '. Would you like to try simulation mode instead?',
                    'simulation' => true
                ]);
            }

            // Create a transaction record
            $transaction = Transaction::create([
                'user_id' => $user->user_id,
                'amount' => $course->price,
                'currency' => 'EGP',
                'status' => Transaction::STATUS_PENDING,
                'payment_method' => 'paymob',
                'transaction_type' => Transaction::TYPE_PAYMENT,
                'reference_id' => $course->course_id,
                'reference_type' => 'course',
                'gateway_transaction_id' => $result['order_id'],
                'gateway_response' => $result,
                'description' => "Enrollment in course: {$course->title}",
                'ip_address' => $request->ip(),
            ]);

            // Create a payment record
            $payment = Payment::create([
                'student_id' => $user->user_id,
                'course_id' => $course->course_id,
                'amount' => $course->price,
                'payment_method' => 'paymob',
                'payment_date' => now(),
                'status' => 'pending',
                'transaction_id' => $transaction->transaction_id,
                'notes' => 'Payment via Paymob',
                'payment_details' => json_encode([
                    'order_id' => $result['order_id'],
                    'payment_key' => $result['payment_key'],
                    'merchant_order_id' => $merchantOrderId,
                ])
            ]);

            DB::commit();

            // Return iframe URL for AJAX request
            return response()->json([
                'success' => true,
                'message' => 'Payment processing initiated',
                'iframe_url' => $result['iframe_url'],
                'order_id' => $result['order_id'],
                'payment_key' => $result['payment_key']
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Paymob payment error: ' . $e->getMessage(), [
                'course_id' => $courseId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your payment: ' . $e->getMessage()
            ], 500);
        }
        }

        // For non-AJAX requests, use the original flow
        DB::beginTransaction();

        try {
            $course = Course::findOrFail($courseId);
            $user = Auth::user();

            // Check if already enrolled
            $existingEnrollment = Enrollment::where('student_id', $user->user_id)
                ->where('course_id', $courseId)
                ->first();

            if ($existingEnrollment) {
                return redirect()->route('student.course-content', $courseId)
                    ->with('info', 'You are already enrolled in this course');
            }

            // Check if Paymob is configured
            if (empty(config('services.paymob.api_key')) ||
                empty(config('services.paymob.integration_id')) ||
                empty(config('services.paymob.iframe_id'))) {
                Log::warning('Paymob not properly configured, using simulation mode');
                return $this->simulatePaymobPayment($request, $course, $user);
            }

            // Prepare billing data from validated request
            $billingData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'street' => $request->street,
                'city' => $request->city,
                'country' => $request->country,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                // Additional required fields for Paymob
                'shipping_method' => 'NA',
                'apartment' => 'NA',
                'floor' => 'NA',
                'building' => 'NA',
            ];

            // Convert price to cents (Paymob requires amount in cents)
            $amountCents = (int)($course->price * 100);

            // Generate a unique merchant order ID
            $merchantOrderId = 'course_' . $course->course_id . '_user_' . $user->user_id . '_' . time();

            // Process payment with Paymob
            $paymentData = [
                'amount_cents' => $amountCents,
                'currency' => 'EGP',
                'merchant_order_id' => $merchantOrderId,
                'items' => [
                    [
                        'name' => $course->title,
                        'amount_cents' => $amountCents,
                        'description' => substr($course->description ?? '', 0, 100),
                        'quantity' => 1
                    ]
                ],
                'billing_data' => $billingData
            ];

            $result = $this->paymobService->processPayment($paymentData);

            if (!$result['success']) {
                DB::rollBack();
                Log::error('Paymob payment processing failed', [
                    'error' => $result['message'],
                    'course_id' => $course->course_id,
                    'user_id' => $user->user_id
                ]);
                return redirect()->back()->with('error', $result['message']);
            }

            // Create a transaction record
            $transaction = Transaction::create([
                'user_id' => $user->user_id,
                'amount' => $course->price,
                'currency' => 'EGP',
                'status' => Transaction::STATUS_PENDING,
                'payment_method' => 'paymob',
                'transaction_type' => Transaction::TYPE_PAYMENT,
                'reference_id' => $course->course_id,
                'reference_type' => 'course',
                'gateway_transaction_id' => $result['order_id'],
                'gateway_response' => $result,
                'description' => "Enrollment in course: {$course->title}",
                'ip_address' => $request->ip(),
            ]);

            // Create a payment record
            $payment = Payment::create([
                'student_id' => $user->user_id,
                'course_id' => $course->course_id,
                'amount' => $course->price,
                'payment_method' => 'paymob',
                'payment_date' => now(),
                'status' => 'pending',
                'transaction_id' => $transaction->transaction_id,
                'notes' => 'Payment via Paymob',
                'payment_details' => json_encode([
                    'order_id' => $result['order_id'],
                    'payment_key' => $result['payment_key'],
                    'merchant_order_id' => $merchantOrderId,
                ])
            ]);

            DB::commit();

            // Redirect to Paymob iframe
            return redirect()->away($result['iframe_url']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Paymob payment error: ' . $e->getMessage(), [
                'course_id' => $courseId,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback to simulation mode in case of errors
            return $this->simulatePaymobPayment($request, $course ?? null, $user ?? null);
        }
    }

    /**
     * Simulate a successful payment for testing purposes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    private function simulatePaymobPayment(Request $request, $course, $user)
    {
        if (!$course || !$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid course or user information'
                ]);
            }

            return redirect()->route('home')
                ->with('error', 'Invalid course or user information');
        }

        DB::beginTransaction();

        try {
            // Generate a fake transaction ID
            $transactionId = 'paymob_test_' . Str::random(20);

            // Create a transaction record
            $transaction = Transaction::create([
                'user_id' => $user->user_id,
                'amount' => $course->price,
                'currency' => 'EGP',
                'status' => Transaction::STATUS_COMPLETED, // Mark as completed for testing
                'payment_method' => 'paymob',
                'transaction_type' => Transaction::TYPE_PAYMENT,
                'reference_id' => $course->course_id,
                'reference_type' => 'course',
                'gateway_transaction_id' => $transactionId,
                'gateway_response' => ['success' => true, 'message' => 'Simulated payment successful'],
                'description' => "Enrollment in course: {$course->title} (Simulated)",
                'ip_address' => $request->ip(),
            ]);

            // Create a payment record
            $payment = Payment::create([
                'student_id' => $user->user_id,
                'course_id' => $course->course_id,
                'amount' => $course->price,
                'payment_method' => 'paymob',
                'payment_date' => now(),
                'status' => 'completed', // Mark as completed for testing
                'transaction_id' => $transaction->transaction_id,
                'notes' => 'Simulated payment via Paymob',
                'payment_details' => json_encode([
                    'order_id' => $transactionId,
                    'payment_key' => 'simulated_payment_key_' . Str::random(10),
                ])
            ]);

            // Create an enrollment record
            Enrollment::create([
                'student_id' => $user->user_id,
                'course_id' => $course->course_id,
                'enrolled_at' => now(),
                'status' => 'active',
                'payment_id' => $payment->payment_id,
            ]);

            // Record instructor earnings
            $this->recordInstructorEarnings($payment);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! You are now enrolled in the course. (This is a simulated payment for testing purposes)',
                    'redirect' => route('student.course-content', $course->course_id)
                ]);
            }

            return redirect()->route('student.course-content', $course->course_id)
                ->with('success', 'Payment successful! You are now enrolled in the course. (This is a simulated payment for testing purposes)');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Simulated payment error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment simulation failed: ' . $e->getMessage()
                ]);
            }

            return redirect()->back()
                ->with('error', 'Payment simulation failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle Paymob callback.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
                $transaction->status = Transaction::STATUS_FAILED;
                $payment->status = 'failed';
            } else if ($success) {
                $transaction->status = Transaction::STATUS_COMPLETED;
                $payment->status = 'completed';

                // Create enrollment if not exists
                Enrollment::firstOrCreate(
                    [
                        'student_id' => $payment->student_id,
                        'course_id' => $payment->course_id,
                    ],
                    [
                        'enrolled_at' => now(),
                        'status' => 'active',
                        'payment_id' => $payment->payment_id,
                    ]
                );

                // Record instructor earnings
                $this->recordInstructorEarnings($payment);
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

    /**
     * Handle Paymob response (success, failure).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paymobResponse(Request $request, $status)
    {
        try {
            $transactionId = $request->input('order');

            if (!$transactionId) {
                Log::error('Invalid Paymob response - missing order ID', [
                    'status' => $status,
                    'data' => $request->all()
                ]);
                return redirect()->route('home')->with('error', 'Invalid payment response');
            }

            // Find the transaction
            $transaction = Transaction::where('gateway_transaction_id', $transactionId)->first();

            if (!$transaction) {
                Log::error('Paymob transaction not found in response handler', [
                    'transaction_id' => $transactionId,
                    'status' => $status
                ]);
                return redirect()->route('home')->with('error', 'Transaction not found');
            }

            // Find the payment
            $payment = Payment::where('transaction_id', $transaction->transaction_id)->first();

            if (!$payment) {
                Log::error('Payment not found for transaction in response handler', [
                    'transaction_id' => $transaction->transaction_id,
                    'status' => $status
                ]);
                return redirect()->route('home')->with('error', 'Payment not found');
            }

            if ($status === 'success') {
                // Payment was successful - redirect to success page
                return redirect()->route('payment.success', $payment->payment_id);
            } else {
                // Payment failed - redirect to failure page
                return redirect()->route('payment.failed', $payment->payment_id);
            }

        } catch (\Exception $e) {
            Log::error('Paymob response error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'status' => $status,
                'data' => $request->all()
            ]);
            return redirect()->route('home')->with('error', 'An error occurred while processing your payment');
        }
    }

    /**
     * Record instructor earnings for a successful payment.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    private function recordInstructorEarnings($payment)
    {
        try {
            // Get the course and instructor
            $course = Course::find($payment->course_id);

            // Make sure the course has an instructor
            if (!$course || !$course->instructor_id) {
                Log::error('Cannot record instructor earnings: Course or instructor not found', [
                    'payment_id' => $payment->payment_id,
                    'course_id' => $payment->course_id
                ]);
                return;
            }

            // Get commission rates from settings
            $instructorRate = Setting::where('key', 'instructor_commission_rate')->first()->value ?? 70;
            $platformRate = Setting::where('key', 'platform_commission_rate')->first()->value ?? 30;

            // Calculate amounts
            $totalAmount = $payment->amount;
            $platformAmount = ($totalAmount * $platformRate) / 100;
            $instructorAmount = ($totalAmount * $instructorRate) / 100;

            // Create instructor earning record
            InstructorEarning::create([
                'instructor_id' => $course->instructor_id,
                'course_id' => $course->course_id,
                'payment_id' => $payment->payment_id,
                'amount' => $instructorAmount,
                'platform_fee' => $platformAmount,
                'status' => 'pending', // Will be changed to 'available' after a certain period
                'notes' => 'Earnings from course enrollment'
            ]);

            // Create financial ledger entry
            FinancialLedger::create([
                'user_id' => $course->instructor_id,
                'payment_id' => $payment->payment_id,
                'amount' => $instructorAmount,
                'type' => 'credit',
                'description' => "Earnings from course: {$course->title}",
                'transaction_date' => now()
            ]);

            Log::info('Instructor earnings recorded successfully', [
                'payment_id' => $payment->payment_id,
                'instructor_id' => $course->instructor_id,
                'amount' => $instructorAmount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to record instructor earnings: ' . $e->getMessage(), [
                'payment_id' => $payment->payment_id,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
