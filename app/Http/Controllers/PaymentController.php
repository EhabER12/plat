<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Show the checkout page for a course.
     *
     * @param  int  $courseId
     * @return \Illuminate\View\View
     */
    public function checkout($courseId)
    {
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
        
        // Get available payment methods from settings
        $paymentMethods = explode(',', DB::table('settings')->where('key', 'payment_methods')->value('value') ?? 'credit_card');
        
        return view('payments.checkout', compact('course', 'user', 'paymentMethods'));
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
}
