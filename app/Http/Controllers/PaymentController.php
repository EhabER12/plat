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
use App\Services\VodafoneCashService;

class PaymentController extends Controller
{
    /**
     * The Paymob service instance.
     *
     * @var \App\Services\PaymobService
     */
    protected $paymobService;

    /**
     * The Vodafone Cash service instance.
     *
     * @var \App\Services\VodafoneCashService
     */
    protected $vodafoneCashService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\PaymobService $paymobService
     * @param \App\Services\VodafoneCashService $vodafoneCashService
     * @return void
     */
    public function __construct(PaymobService $paymobService, \App\Services\VodafoneCashService $vodafoneCashService)
    {
        $this->paymobService = $paymobService;
        $this->vodafoneCashService = $vodafoneCashService;
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
                    'user_id' => $user->user_id,
                    'course_id' => $course->course_id,
                    'status' => 'active',
                    'enrolled_at' => now()
                ]);

                return redirect()->route('student.course-content', $course->course_id)
                    ->with('success', 'Successfully enrolled in the free course');
            }

            // Get enabled payment methods from settings
            $paymentMethodsSetting = \App\Models\Setting::where('key', 'payment_methods')->first();
            $enabledPaymentMethods = $paymentMethodsSetting ? explode(',', $paymentMethodsSetting->value) : ['paymob'];

            // Get default payment method from settings
            $defaultPaymentMethod = \App\Models\Setting::where('key', 'default_payment_method')->first();
            $defaultMethod = $defaultPaymentMethod ? $defaultPaymentMethod->value : 'paymob';

            // If default method is not enabled, use the first enabled method
            if (!in_array($defaultMethod, $enabledPaymentMethods) && !empty($enabledPaymentMethods)) {
                $defaultMethod = $enabledPaymentMethods[0];
            }

            // Check for applied coupon in session
            $appliedCoupon = null;
            $finalPrice = $course->price;
            
            if (session()->has('applied_coupon_' . $courseId)) {
                $couponId = session('applied_coupon_' . $courseId);
                $appliedCoupon = \App\Models\Coupon::find($couponId);
                
                if ($appliedCoupon && $appliedCoupon->isValid()) {
                    $discount = $appliedCoupon->calculateDiscount($course->price);
                    $finalPrice = max(0, $course->price - $discount);
                } else {
                    // If coupon is no longer valid, remove it from session
                    session()->forget('applied_coupon_' . $courseId);
                    $appliedCoupon = null;
                }
            }

            // Initialize the payment iframe URL (empty by default until payment method is selected)
            $iframeUrl = null;
            
            // If Paymob is set as default, prepare the iframe URL
            if ($defaultMethod === 'paymob' && !empty(config('services.paymob.iframe_id'))) {
                try {
                    // Generate payment data for iframe
                    $billingData = [
                        'first_name' => $user->name,
                        'last_name' => '',
                        'email' => $user->email,
                        'phone_number' => $user->phone ?? '',
                        'street' => 'N/A',
                        'city' => 'N/A',
                        'country' => 'EG',
                        'state' => 'N/A',
                        'postal_code' => 'N/A',
                        'shipping_method' => 'NA',
                        'apartment' => 'NA',
                        'floor' => 'NA',
                        'building' => 'NA',
                    ];
                    
                    // Use final price after any discounts
                    $amountCents = (int)($finalPrice * 100);
                    $merchantOrderId = 'course_' . $course->course_id . '_user_' . $user->user_id . '_' . time();
                    
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
                    if ($result['success']) {
                        $iframeUrl = $result['iframe_url'];
                    } else {
                        Log::warning('Failed to generate Paymob iframe URL', ['error' => $result['message']]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error generating Paymob iframe: ' . $e->getMessage());
                    // Continue without an iframe URL, it will be handled client-side
                }
            }

            // For paid courses, proceed to payment
            return view('payments.checkout', compact(
                'course', 
                'user', 
                'enabledPaymentMethods', 
                'defaultMethod', 
                'iframeUrl',
                'appliedCoupon',
                'finalPrice'
            ));
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
                'user_id' => $user->user_id,
                'course_id' => $course->course_id,
                'status' => 'active',
                'enrolled_at' => now(),
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
        try {
        $payment = Payment::with(['course', 'student'])->findOrFail($paymentId);

        // Ensure the payment belongs to the current user
        if ($payment->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

            // Load the course relationship if not already loaded
            if (!$payment->relationLoaded('course')) {
                $payment->load('course');
            }

            // Make sure enrollment exists
            if ($payment->status === 'completed') {
                $enrollment = Enrollment::firstOrCreate(
                    [
                        'student_id' => $payment->student_id,
                        'course_id' => $payment->course_id,
                    ],
                    [
                        'user_id' => $payment->student_id,
                        'enrolled_at' => now(),
                        'status' => 'active',
                        'payment_id' => $payment->payment_id,
                    ]
                );
        }

        return view('payments.success', compact('payment'));
        } catch (\Exception $e) {
            Log::error('Error showing success payment page: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'حدث خطأ أثناء عرض صفحة نجاح الدفع.');
        }
    }

    /**
     * Show the payment failure page.
     *
     * @param  int  $paymentId
     * @return \Illuminate\View\View
     */
    public function showFailedPayment($paymentId)
    {
        try {
        $payment = Payment::with(['course', 'student'])->findOrFail($paymentId);

        // Ensure the payment belongs to the current user
        if ($payment->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

            // Load the course relationship if not already loaded
            if (!$payment->relationLoaded('course')) {
                $payment->load('course');
        }

        return view('payments.failed', compact('payment'));
        } catch (\Exception $e) {
            Log::error('Error showing failed payment page: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'حدث خطأ أثناء عرض صفحة فشل الدفع.');
        }
    }

    /**
     * Process the payment using Paymob.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPaymobPayment(Request $request, $courseId)
    {
        try {
                $course = Course::findOrFail($courseId);
                $user = Auth::user();

            // Validate the course is available for purchase
            if ($course->status !== 'published' || $course->approval_status !== 'approved') {
                return redirect()->back()->with('error', 'This course is not available for purchase at this time.');
        }

            // Check if already enrolled
            $existingEnrollment = Enrollment::where('student_id', $user->user_id)
                ->where('course_id', $courseId)
                ->first();

            if ($existingEnrollment) {
                return redirect()->route('student.course-content', $courseId)
                    ->with('info', 'You are already enrolled in this course');
            }

            // Create a unique merchant order ID
            $merchantOrderId = 'course_' . $course->course_id . '_user_' . $user->user_id . '_' . time();

            // Prepare billing data
            $billingData = [
                'first_name' => $user->name,
                'last_name' => '',
                'email' => $user->email,
                'phone_number' => $user->phone ?? '',
                'street' => 'N/A',
                'city' => 'N/A',
                'country' => 'EG',
                'state' => 'N/A',
                'postal_code' => 'N/A',
                'shipping_method' => 'NA',
                'apartment' => 'NA',
                'floor' => 'NA',
                'building' => 'NA',
            ];

            // Prepare payment data
            $amountCents = (int)($course->price * 100);
            $paymentData = [
                'amount_cents' => $amountCents,
                'currency' => 'EGP',
                'merchant_order_id' => $merchantOrderId,
                'integration_id' => 5066833, // Use the Integration ID from Paymob Dashboard
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

            // Process payment through Paymob service
            $result = $this->paymobService->processPayment($paymentData);

            if (!$result['success']) {
                Log::error('Paymob payment processing failed', [
                    'error' => $result['message'],
                    'course_id' => $courseId,
                    'user_id' => $user->user_id
                ]);
                return redirect()->back()->with('error', 'Payment processing failed: ' . $result['message']);
            }

            // Store order_id as the gateway_transaction_id 
            // This is the ID that Paymob will send back in the callback
            $orderIdFromPaymob = $result['order_id'] ?? null;

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->user_id,
                'amount' => $course->price,
                'currency' => 'EGP',
                'status' => Transaction::STATUS_PENDING,
                'payment_method' => 'paymob',
                'transaction_type' => Transaction::TYPE_PAYMENT,
                'reference_id' => $course->course_id,
                'reference_type' => 'course',
                'gateway_transaction_id' => $orderIdFromPaymob,
                'gateway_response' => $result,
                'description' => 'Enrollment in course: ' . $course->title,
                'ip_address' => $request->ip(),
            ]);

            Log::info('Transaction created with Paymob order_id', [
                'transaction_id' => $transaction->transaction_id,
                'gateway_transaction_id' => $orderIdFromPaymob,
                'paymob_order_id' => $orderIdFromPaymob
            ]);

            // Create payment record
            $payment = Payment::create([
                'student_id' => $user->user_id,
                'user_id' => $user->user_id,
                'course_id' => $course->course_id,
                'amount' => $course->price,
                'payment_method' => 'paymob',
                'payment_date' => now(),
                'status' => 'pending',
                'transaction_id' => $transaction->transaction_id,
                'notes' => 'Payment being processed via Paymob',
                'payment_details' => json_encode([
                    'order_id' => $orderIdFromPaymob,
                    'iframe_url' => $result['iframe_url'] ?? null,
                ])
            ]);

            // Redirect to iframe URL
            if (!empty($result['iframe_url'])) {
                return redirect($result['iframe_url']);
            }

            // If no iframe URL, redirect to pending payment page
            return redirect()->route('payment.pending', $payment->payment_id);

        } catch (\Exception $e) {
            Log::error('Paymob payment error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment processing failed: ' . $e->getMessage());
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
                
                if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
                } else {
                    return redirect()->route('payment.generic-failed')->with('error', 'Invalid payment signature');
                }
            }

            // Get transaction details - first try webhook format, then URL format
            $transactionId = null;
            $orderId = null;
            $merchantOrderId = null;
            
            // Try to extract transaction ID from different possible sources
            if (!empty($data['order']['id'])) {
                // Standard webhook format
                $transactionId = $data['order']['id'];
                $orderId = $transactionId;
            } elseif (!empty($data['order'])) {
                // Direct order parameter
                $transactionId = $data['order'];
                $orderId = $transactionId;
            } elseif ($request->has('order')) {
                // URL query parameter
                $transactionId = $request->input('order');
                $orderId = $transactionId;
            } elseif ($request->has('id')) {
                // Payment ID in URL
                $transactionId = $request->input('id');
            }

            // Get merchant order ID if present
            if (!empty($data['merchant_order_id'])) {
                $merchantOrderId = $data['merchant_order_id'];
            } elseif ($request->has('merchant_order_id')) {
                $merchantOrderId = $request->input('merchant_order_id');
            }

            if (!$transactionId && !$orderId && !$merchantOrderId) {
                Log::error('Missing transaction/order ID in Paymob callback', $data);
                
                if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                    return response()->json(['status' => 'error', 'message' => 'Missing transaction/order ID'], 400);
                } else {
                    return redirect()->route('payment.generic-failed')->with('error', 'Missing transaction details');
                }
            }

            Log::info('Processing transaction', [
                'transaction_id' => $transactionId,
                'order_id' => $orderId,
                'merchant_order_id' => $merchantOrderId
            ]);

            // Find the transaction in our database - try different possible IDs
            $transaction = null;
            
            // First try gateway_transaction_id (should match order_id from Paymob)
            if ($orderId) {
                $transaction = Transaction::where('gateway_transaction_id', $orderId)->first();
            }
            
            // If not found, try by ID
            if (!$transaction && $transactionId) {
            $transaction = Transaction::where('gateway_transaction_id', $transactionId)->first();
            }
            
            // If still not found, try by merchant order ID in gateway_response
            if (!$transaction && $merchantOrderId) {
                $transactions = Transaction::where('payment_method', 'paymob')
                    ->where(function($query) use ($merchantOrderId) {
                        $query->where('gateway_response->merchant_order_id', $merchantOrderId)
                            ->orWhere('description', 'like', "%{$merchantOrderId}%");
                    })->get();
                
                if ($transactions->count() == 1) {
                    $transaction = $transactions->first();
                }
            }

            if (!$transaction) {
                // Additional debugging for transaction lookup
                $possibleTransactions = Transaction::where('payment_method', 'paymob')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['transaction_id', 'gateway_transaction_id', 'created_at', 'gateway_response']);
                
                Log::error('Paymob transaction not found', [
                    'transaction_id' => $transactionId,
                    'order_id' => $orderId,
                    'merchant_order_id' => $merchantOrderId,
                    'recent_transactions' => $possibleTransactions,
                    'data' => $data
                ]);
                
                // Check if this is an API call or a browser redirect
                if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Transaction not found',
                        'debug' => [
                            'transaction_id' => $transactionId,
                            'order_id' => $orderId,
                            'merchant_order_id' => $merchantOrderId,
                            'recent_transactions' => $possibleTransactions->toArray()
                        ]
                    ], 404);
                } else {
                    // This is likely a browser redirect from Paymob - handle it for the user
                    $success = $data['success'] ?? $request->input('success') ?? false;
                    
                    // Initialize variables
                    $courseId = null;
                    $userId = null;
                    
                    // Try to extract course ID from merchant order ID if available
                    if ($merchantOrderId && strpos($merchantOrderId, 'course_') === 0) {
                        $parts = explode('_', $merchantOrderId);
                        if (count($parts) >= 4 && $parts[0] === 'course' && $parts[2] === 'user') {
                            $courseId = $parts[1];
                            $userId = $parts[3];
                        }
                    }
                    
                    return redirect()->route('payment.unknown-transaction', [
                        'courseId' => $courseId,
                        'userId' => $userId,
                        'orderId' => $orderId,
                        'merchantOrderId' => $merchantOrderId,
                        'status' => $success ? 'success' : 'failed'
                    ]);
                }
            }

            // Find the payment
            $payment = Payment::where('transaction_id', $transaction->transaction_id)->first();

            if (!$payment) {
                Log::error('Payment not found for transaction', [
                    'transaction_id' => $transaction->transaction_id,
                    'data' => $data
                ]);
                
                if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
                } else {
                    return redirect()->route('payment.generic-failed')->with('error', 'Payment details not found');
                }
            }

            DB::beginTransaction();

            // Determine the transaction status
            $success = $data['success'] ?? $request->input('success') ?? false;
            $isVoided = $data['is_voided'] ?? $request->input('is_voided') ?? false;
            $isRefunded = $data['is_refunded'] ?? $request->input('is_refunded') ?? false;
            $errorOccurred = $data['error_occured'] ?? $request->input('error_occured') ?? false;

            if ($isVoided || $isRefunded || $errorOccurred || !$success) {
                $transaction->status = Transaction::STATUS_FAILED;
                $payment->status = 'failed';
                Log::info('Payment marked as failed', [
                    'transaction_id' => $transaction->transaction_id,
                    'order_id' => $orderId
                ]);
            } else if ($success) {
                $transaction->status = Transaction::STATUS_COMPLETED;
                $payment->status = 'completed';

                // Create enrollment if not exists
                $enrollment = Enrollment::firstOrCreate(
                    [
                        'student_id' => $payment->student_id,
                        'course_id' => $payment->course_id,
                    ],
                    [
                        'user_id' => $payment->student_id,
                        'enrolled_at' => now(),
                        'status' => 'active',
                        'payment_id' => $payment->payment_id,
                    ]
                );

                // Record instructor earnings
                $this->recordInstructorEarnings($payment);
                
                Log::info('Payment completed successfully and enrollment created', [
                    'transaction_id' => $transaction->transaction_id,
                    'payment_id' => $payment->payment_id,
                    'enrollment_id' => $enrollment->enrollment_id ?? null,
                    'student_id' => $payment->student_id,
                    'course_id' => $payment->course_id
                ]);
            }

            // Save updated records
            $transaction->gateway_response = array_merge($transaction->gateway_response ?? [], $data);
            $transaction->save();
            $payment->save();

            // Commit all DB changes
            DB::commit();

            // If this is an API call, return JSON response
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
            return response()->json(['status' => 'success']);
            } else {
                // If it's a browser request, redirect to the appropriate page
                if ($transaction->status === Transaction::STATUS_COMPLETED || $payment->status === 'completed') {
                    return redirect()->route('payment.success', ['payment' => $payment->payment_id])
                        ->with('success', 'تم إتمام الدفع بنجاح!');
                } else {
                    return redirect()->route('payment.generic-failed', ['payment' => $payment->payment_id])
                        ->with('error', 'فشلت عملية الدفع.');
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Paymob callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json(['status' => 'error', 'message' => 'Internal server error: ' . $e->getMessage()], 500);
            } else {
                return redirect()->route('payment.generic-failed')
                    ->with('error', 'حدث خطأ أثناء معالجة الدفع. الرجاء المحاولة مرة أخرى.');
            }
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
            // Log the response for debugging
            Log::info('Paymob response received', [
                'status' => $status,
                'data' => $request->all()
            ]);
            
            $transactionId = $request->input('order');
            $merchantOrderId = $request->input('merchant_order_id');

            if (!$transactionId && !$merchantOrderId) {
                Log::error('Invalid Paymob response - missing order ID', [
                    'status' => $status,
                    'data' => $request->all()
                ]);
                return redirect()->route('home')->with('error', 'عملية دفع غير صالحة، لم يتم العثور على معرف الطلب');
            }

            // Find the transaction with multiple fallback options
            $transaction = null;
            
            // Try by gateway_transaction_id first
            if ($transactionId) {
            $transaction = Transaction::where('gateway_transaction_id', $transactionId)->first();
            }
            
            // If not found, try by merchant_order_id in gateway_response
            if (!$transaction && $merchantOrderId) {
                $transactions = Transaction::where('payment_method', 'paymob')
                    ->where(function($query) use ($merchantOrderId) {
                        $query->where('gateway_response->merchant_order_id', $merchantOrderId)
                            ->orWhere('description', 'like', "%{$merchantOrderId}%");
                    })->get();
                
                if ($transactions->count() == 1) {
                    $transaction = $transactions->first();
                }
            }

            if (!$transaction) {
                Log::error('Paymob transaction not found in response handler', [
                    'transaction_id' => $transactionId,
                    'merchant_order_id' => $merchantOrderId,
                    'status' => $status
                ]);
                
                // If transaction not found but status is success, try to create it
                if ($status === 'success' && $merchantOrderId) {
                    // Extract course_id and user_id from merchant_order_id pattern: "course_{course_id}_user_{user_id}_timestamp"
                    $parts = explode('_', $merchantOrderId);
                    if (count($parts) >= 4 && $parts[0] === 'course' && $parts[2] === 'user') {
                        $courseId = $parts[1];
                        $userId = $parts[3];
                        
                        return redirect()->route('payment.unknown-transaction', [
                            'courseId' => $courseId,
                            'userId' => $userId,
                            'orderId' => $transactionId,
                            'merchantOrderId' => $merchantOrderId,
                            'status' => $status
                        ])->with('warning', 'نحن نتحقق من عملية الدفع الخاصة بك. سيتم تفعيل الوصول للدورة قريباً إذا تم تأكيد الدفع.');
                    }
                }
                
                return redirect()->route('home')->with('error', 'لم يتم العثور على معاملة الدفع في النظام');
            }

            // Find the payment
            $payment = Payment::where('transaction_id', $transaction->transaction_id)->first();

            if (!$payment) {
                Log::error('Payment not found for transaction in response handler', [
                    'transaction_id' => $transaction->transaction_id,
                    'status' => $status
                ]);
                return redirect()->route('home')->with('error', 'لم يتم العثور على معلومات الدفع');
            }

            // Get course information
            $course = Course::find($payment->course_id);
            $courseTitle = $course ? $course->title : 'الدورة التدريبية';

            if ($status === 'success') {
                // If the transaction is still pending, update it
                if ($transaction->status === Transaction::STATUS_PENDING) {
                    DB::beginTransaction();
                    
                    // Update transaction and payment status
                    $transaction->status = Transaction::STATUS_COMPLETED;
                    $transaction->save();
                    
                    $payment->status = 'completed';
                    $payment->save();
                    
                    // Create enrollment if not exists
                    $enrollment = Enrollment::firstOrCreate(
                        [
                            'student_id' => $payment->student_id,
                            'course_id' => $payment->course_id,
                        ],
                        [
                            'user_id' => $payment->student_id,
                            'enrolled_at' => now(),
                            'status' => 'active',
                            'payment_id' => $payment->payment_id,
                        ]
                    );
                    
                    // Record instructor earnings
                    $this->recordInstructorEarnings($payment);
                    
                    DB::commit();
                }
                
                // Payment was successful - redirect to success page
                return redirect()->route('payment.success', $payment->payment_id)
                    ->with('success', 'تم إتمام عملية الدفع بنجاح! تم تسجيلك في ' . $courseTitle);
            } else {
                // If the transaction is still pending, update it to failed
                if ($transaction->status === Transaction::STATUS_PENDING) {
                    $transaction->status = Transaction::STATUS_FAILED;
                    $transaction->save();
                    
                    $payment->status = 'failed';
                    $payment->save();
                }
                
                // Payment failed - redirect to failure page
                return redirect()->route('payment.generic-failed', $payment->payment_id)
                    ->with('error', 'فشلت عملية الدفع. يرجى المحاولة مرة أخرى أو الاتصال بالدعم.');
            }

        } catch (\Exception $e) {
            Log::error('Paymob response error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'status' => $status,
                'data' => $request->all()
            ]);
            return redirect()->route('home')
                ->with('error', 'حدث خطأ أثناء معالجة عملية الدفع الخاصة بك: ' . $e->getMessage());
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
                'status' => 'available',
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

    /**
     * Verify the payment status using Vodafone Cash.
     *
     * @param  string  $reference
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyVodafonePayment($reference)
    {
        Log::info('Verifying Vodafone Cash payment', ['reference' => $reference]);
        
        try {
            // Verify payment with VodafoneCashService
            $verificationResult = $this->vodafoneCashService->verifyPayment($reference);
            
            Log::info('Vodafone Cash verification result', $verificationResult);
            
            if (!$verificationResult['success']) {
                return redirect()->route('courses.index')
                    ->with('error', 'Failed to verify payment: ' . ($verificationResult['message'] ?? 'Unknown error'));
            }
            
            // Find the transaction by gateway transaction ID
            $transaction = Transaction::where('gateway_transaction_id', $reference)->first();
            
            if (!$transaction) {
                return redirect()->route('courses.index')
                    ->with('error', 'Transaction not found');
            }
            
            // Find the payment
            $payment = Payment::where('transaction_id', $transaction->transaction_id)->first();
            
            if (!$payment) {
                return redirect()->route('courses.index')
                    ->with('error', 'Payment not found');
            }

            if ($verificationResult['verified']) {
                // Verify the payment is already processed
                $existingEnrollment = Enrollment::where('student_id', $payment->student_id)
                    ->where('course_id', $payment->course_id)
                    ->first();
                
                if (!$existingEnrollment) {
                    DB::beginTransaction();
                    
                    try {
                        // Update transaction and payment status
                        $transaction->update([
                            'status' => Transaction::STATUS_COMPLETED,
                            'gateway_response' => array_merge($transaction->gateway_response ?? [], [
                                'verification' => $verificationResult
                            ])
                        ]);
                        
                        $payment->update([
                            'status' => 'completed',
                            'payment_date' => now()
                        ]);
                        
                        // Create an enrollment record
                        $enrollment = Enrollment::create([
                            'student_id' => $payment->student_id,
                            'user_id' => $payment->user_id,
                            'course_id' => $payment->course_id,
                            'enrolled_at' => now(),
                            'status' => 'active',
                            'payment_id' => $payment->payment_id,
                        ]);
                        
                        // Record instructor earnings
                        $this->recordInstructorEarnings($payment);
                        
                        DB::commit();
                        
                        return redirect()->route('payment.success', $payment->payment_id)
                            ->with('success', 'Payment verified successfully! You are now enrolled in the course.');
                        
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Error completing Vodafone Cash payment: ' . $e->getMessage());
                        
                        return redirect()->route('payment.generic-failed', $payment->payment_id)
                            ->with('error', 'Failed to complete enrollment: ' . $e->getMessage());
                    }
                } else {
                    return redirect()->route('payment.success', $payment->payment_id)
                        ->with('success', 'Payment was already verified. You are enrolled in the course.');
                }
            } else {
                // Payment is still pending
                return redirect()->route('payment.pending', $payment->payment_id)
                    ->with('info', 'Your payment is still being processed. Please check back later.');
            }
            
        } catch (\Exception $e) {
            Log::error('Vodafone Cash verification error: ' . $e->getMessage());
            
            return redirect()->route('courses.index')
                ->with('error', 'An error occurred while verifying your payment: ' . $e->getMessage());
        }
    }

    /**
     * Simulate a payment for testing purposes.
     *
     * @param  int  $courseId
     * @param  string  $method
     * @return \Illuminate\Http\RedirectResponse
     */
    public function simulatePayment($courseId, $method = 'paymob')
    {
        try {
            $course = Course::findOrFail($courseId);
            $user = Auth::user();
            
            // Check for applied coupon
            $couponId = null;
            $discountAmount = 0;
            $finalAmount = $course->price;
            
            if (request()->has('coupon_id')) {
                $couponId = request()->input('coupon_id');
                $coupon = \App\Models\Coupon::find($couponId);

                if ($coupon && $coupon->isValid()) {
                    $discountAmount = request()->input('discount_amount', 0);
                    $finalAmount = max(0, $course->price - $discountAmount);
                    
                    // Increment coupon usage
                    $coupon->incrementUsed();
                }
            }

            // Create transaction record
                $transaction = Transaction::create([
                    'user_id' => $user->user_id,
                'amount' => $finalAmount,
                    'currency' => 'EGP',
                'status' => 'completed',
                'payment_method' => $method,
                'transaction_id' => $method . '_' . Str::random(20),
                'transaction_data' => json_encode([
                    'course_id' => $course->course_id,
                    'course_title' => $course->title,
                    'original_price' => $course->price,
                    'discount_amount' => $discountAmount,
                    'coupon_id' => $couponId,
                    'payment_method' => $method,
                    'simulated' => true
                ]),
                'transaction_type' => 'payment',
                'metadata' => json_encode([
                    'course_id' => $course->course_id,
                    'user_id' => $user->user_id,
                ])
                ]);

            // Create payment record
                $payment = Payment::create([
                    'student_id' => $user->user_id,
                    'course_id' => $course->course_id,
                'transaction_id' => $transaction->transaction_id,
                'amount' => $finalAmount,
                'payment_method' => $method,
                'status' => 'completed',
                    'payment_date' => now(),
                'coupon_id' => $couponId,
                'discount_amount' => $discountAmount,
                'metadata' => json_encode([
                    'original_amount' => $course->price,
                    'final_amount' => $finalAmount,
                    'discount_amount' => $discountAmount,
                    'coupon_id' => $couponId,
                    'transaction_id' => $transaction->transaction_id,
                    ])
                ]);

            // Create enrollment
                Enrollment::create([
                    'student_id' => $user->user_id,
                    'user_id' => $user->user_id,
                    'course_id' => $course->course_id,
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'payment_id' => $payment->payment_id
                ]);

            // Record instructor earnings
            $this->recordInstructorEarnings($payment);

            // Clear any applied coupon from session after successful payment
            session()->forget('applied_coupon_' . $courseId);

            return redirect()->route('payment.success', $payment->payment_id)
                ->with('success', 'Payment successfully simulated. You are now enrolled in the course!');
        } catch (\Exception $e) {
            Log::error('Simulate payment error: ' . $e->getMessage());
            return redirect()->route('courses.show', $courseId)
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Debug method to manually complete a payment.
     * Only for development use - should be disabled in production.
     *
     * @param  string  $transactionId
     * @return \Illuminate\Http\Response
     */
    public function debugCompletePayment($transactionId)
    {
        // Only allow this in development environment
        if (app()->environment('production')) {
            abort(404);
        }

        try {
            DB::beginTransaction();

            // Find transaction by ID or gateway_transaction_id
            $transaction = Transaction::where('transaction_id', $transactionId)
                ->orWhere('gateway_transaction_id', $transactionId)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found'
                ], 404);
            }

            $payment = Payment::where('transaction_id', $transaction->transaction_id)->first();

            if (!$payment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment not found for transaction'
                ], 404);
            }

            // Update transaction and payment status
            $transaction->status = Transaction::STATUS_COMPLETED;
            $transaction->save();

            $payment->status = 'completed';
            $payment->save();

            // Create enrollment
            $enrollment = Enrollment::firstOrCreate(
                [
                    'student_id' => $payment->student_id,
                    'course_id' => $payment->course_id,
                ],
                [
                    'user_id' => $payment->student_id,
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'payment_id' => $payment->payment_id,
                ]
            );

                // Record instructor earnings
                $this->recordInstructorEarnings($payment);

                DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment manually completed',
                'transaction' => [
                    'id' => $transaction->transaction_id,
                    'status' => $transaction->status
                ],
                'payment' => [
                    'id' => $payment->payment_id,
                    'status' => $payment->status
                ],
                'enrollment' => [
                    'id' => $enrollment->enrollment_id,
                    'status' => $enrollment->status
                ],
                'redirect_url' => route('student.course-content', $payment->course_id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error completing payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display page for unknown transactions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showUnknownTransaction(Request $request)
    {
        $data = [
            'courseId' => $request->input('courseId'),
            'userId' => $request->input('userId'),
            'orderId' => $request->input('orderId'),
            'merchantOrderId' => $request->input('merchantOrderId'),
            'status' => $request->input('status')
        ];
        
        try {
            // Try to get course information
            if ($data['courseId']) {
                $course = Course::find($data['courseId']);
                if ($course) {
                    $data['course'] = $course;
                }
            }
            
            // Check if this is a success status and we have all needed information
            if ($data['status'] === 'success' && $data['courseId'] && $data['userId']) {
                $user = \App\Models\User::find($data['userId']);
                
                if ($user && $course) {
                    // Check if user is already enrolled
                    $existingEnrollment = Enrollment::where('student_id', $user->user_id)
                        ->where('course_id', $course->course_id)
                        ->first();
                    
                    if ($existingEnrollment) {
                        $data['alreadyEnrolled'] = true;
                        $data['enrollmentId'] = $existingEnrollment->enrollment_id;
                    } else {
                        $data['canCreateEnrollment'] = true;
                        $data['user'] = $user;
                    }
                }
            }
            
            // Log this event for investigation
            Log::warning('Unknown transaction payment response', $data);
            
        } catch (\Exception $e) {
            Log::error('Error processing unknown transaction: ' . $e->getMessage());
        }
        
        return view('payments.unknown-transaction', $data);
    }
    
    /**
     * Process unknown transaction and create enrollment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processUnknownTransaction(Request $request)
    {
        $courseId = $request->input('courseId');
        $userId = $request->input('userId');
        $orderId = $request->input('orderId');
        $merchantOrderId = $request->input('merchantOrderId');
        
        try {
                DB::beginTransaction();
            
            // Get course and user
            $course = Course::findOrFail($courseId);
            $user = \App\Models\User::findOrFail($userId);
            
            // Check if user is already enrolled
            $existingEnrollment = Enrollment::where('student_id', $user->user_id)
                ->where('course_id', $courseId)
                ->first();
                
            if ($existingEnrollment) {
                return redirect()->route('student.course-content', $courseId)
                    ->with('info', 'أنت مسجل بالفعل في هذه الدورة');
            }
                
                // Create a transaction record
                $transaction = Transaction::create([
                'user_id' => $userId,
                    'amount' => $course->price,
                'currency' => 'EGP',
                    'status' => Transaction::STATUS_COMPLETED,
                'payment_method' => 'paymob',
                    'transaction_type' => Transaction::TYPE_PAYMENT,
                'reference_id' => $courseId,
                    'reference_type' => 'course',
                'gateway_transaction_id' => $orderId ?? ('unknown_' . Str::random(10)),
                'gateway_response' => [
                    'merchant_order_id' => $merchantOrderId,
                    'success' => true,
                    'message' => 'Manual verification for unknown transaction'
                ],
                'description' => "Enrollment in course: {$course->title} (Manual verification)",
                'ip_address' => $request->ip(),
                ]);

                // Create a payment record
                $payment = Payment::create([
                'student_id' => $userId,
                'user_id' => $userId,
                'course_id' => $courseId,
                    'amount' => $course->price,
                'payment_method' => 'paymob',
                    'payment_date' => now(),
                    'status' => 'completed',
                    'transaction_id' => $transaction->transaction_id,
                'notes' => 'Payment verified manually for unknown transaction',
                    'payment_details' => json_encode([
                    'order_id' => $orderId,
                    'merchant_order_id' => $merchantOrderId,
                    ])
                ]);

            // Create enrollment
            $enrollment = Enrollment::create([
                'student_id' => $userId,
                'user_id' => $userId,
                'course_id' => $courseId,
                    'enrolled_at' => now(),
                    'status' => 'active',
                    'payment_id' => $payment->payment_id,
                ]);

                // Record instructor earnings
                $this->recordInstructorEarnings($payment);

                DB::commit();

            Log::info('Successfully processed unknown transaction', [
                'course_id' => $courseId,
                'user_id' => $userId,
                'transaction_id' => $transaction->transaction_id,
                'payment_id' => $payment->payment_id,
                'enrollment_id' => $enrollment->enrollment_id
            ]);
            
            return redirect()->route('student.course-content', $courseId)
                ->with('success', 'تم تأكيد عملية الدفع بنجاح وتم تسجيلك في الدورة!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing unknown transaction manually: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء معالجة عملية الدفع: ' . $e->getMessage());
        }
    }

    /**
     * Apply a coupon code to a course checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function applyCoupon(Request $request, $courseId)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $course = Course::findOrFail($courseId);
        $couponCode = $request->coupon_code;
        
        // Find the coupon by code
        $coupon = \App\Models\Coupon::where('code', $couponCode)->first();
        
        if (!$coupon) {
            return back()->with('coupon_error', 'Invalid coupon code. Please try again.');
        }
        
        // Check if coupon is valid
        if (!$coupon->isValid()) {
            return back()->with('coupon_error', 'This coupon has expired or reached its usage limit.');
        }
        
        // Check if coupon is applicable to this course
        if (!empty($coupon->courses_applicable) && !in_array($courseId, $coupon->courses_applicable)) {
            return back()->with('coupon_error', 'This coupon is not applicable to this course.');
        }
        
        // Check minimum order amount
        if ($course->price < $coupon->minimum_order_amount) {
            return back()->with('coupon_error', 'This coupon requires a minimum purchase of $' . $coupon->minimum_order_amount);
        }
        
        // Store coupon in session
        session(['applied_coupon_' . $courseId => $coupon->coupon_id]);
        
        // Calculate discount for display
        $discount = $coupon->calculateDiscount($course->price);
        $finalPrice = max(0, $course->price - $discount);
        
        return redirect()->route('payment.checkout', $courseId)
            ->with('success', 'Coupon applied successfully! You saved $' . number_format($discount, 2));
    }
    
    /**
     * Remove a coupon from a course checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeCoupon(Request $request, $courseId)
    {
        // Remove coupon from session
        session()->forget('applied_coupon_' . $courseId);
        
        return redirect()->route('payment.checkout', $courseId)
            ->with('info', 'Coupon has been removed.');
    }

    /**
     * Show the generic payment failure page.
     *
     * @return \Illuminate\View\View
     */
    public function showGenericFailure()
    {
        return view('payments.generic-failed');
    }
}
