<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\Transaction;
use App\Services\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookPurchaseController extends Controller
{
    protected $paymobService;

    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
        $this->middleware('auth');
    }

    /**
     * Show the checkout page for a book.
     */
    public function checkout(Book $book)
    {
        // Check if the book is published
        if (!$book->is_published) {
            abort(404);
        }

        // Check if the user has already purchased this book
        $user = Auth::user();
        $existingPurchase = BookPurchase::where('user_id', $user->user_id)
            ->where('book_id', $book->id)
            ->where('status', 'completed')
            ->first();

        if ($existingPurchase) {
            return redirect()->route('books.show', $book)->with('info', 'You have already purchased this book.');
        }

        return view('books.checkout', compact('book'));
    }

    /**
     * Process the payment using Paymob.
     */
    public function processPaymobPayment(Request $request, Book $book)
    {
        try {
            $user = Auth::user();
            $isTestMode = $request->has('test_mode') && $request->input('test_mode') == '1';

            // Check if this is an AJAX request for testing
            $isAjaxRequest = $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest';

            // Check if the book is published
            if (!$book->is_published) {
                if ($isAjaxRequest) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This book is not available for purchase at this time.'
                    ]);
                }
                return redirect()->back()->with('error', 'This book is not available for purchase at this time.');
            }

            // Check if the user has already purchased this book (skip in test mode)
            if (!$isTestMode) {
                $existingCompletedPurchase = BookPurchase::where('user_id', $user->user_id)
                    ->where('book_id', $book->id)
                    ->where('status', 'completed')
                    ->first();

                if ($existingCompletedPurchase) {
                    if ($isAjaxRequest) {
                        return response()->json([
                            'success' => false,
                            'message' => 'You have already purchased this book.'
                        ]);
                    }
                    return redirect()->route('books.show', $book)->with('info', 'You have already purchased this book.');
                }

                // Check if there's a pending purchase with a recent transaction
                $existingPendingPurchase = BookPurchase::where('user_id', $user->user_id)
                    ->where('book_id', $book->id)
                    ->where('status', 'pending')
                    ->first();

                if ($existingPendingPurchase) {
                    // Get the transaction to redirect to payment page
                    $pendingTransaction = $existingPendingPurchase->transaction;

                    // Check if the transaction is recent (less than 5 minutes old)
                    $isRecentTransaction = $pendingTransaction &&
                        $pendingTransaction->created_at->diffInMinutes(now()) < 5 &&
                        !empty($pendingTransaction->gateway_response['iframe_url']);

                    if ($isRecentTransaction) {
                        $iframeUrl = $pendingTransaction->gateway_response['iframe_url'];

                        if ($isAjaxRequest) {
                            return response()->json([
                                'success' => true,
                                'iframe_url' => $iframeUrl,
                                'message' => 'Redirecting to existing payment page'
                            ]);
                        }

                        // Redirect to the existing payment page
                        return redirect()->away($iframeUrl);
                    } else {
                        // If transaction is older than 5 minutes or doesn't have iframe_url,
                        // delete the old purchase record so we can create a new one
                        Log::info('Deleting old pending purchase record', [
                            'purchase_id' => $existingPendingPurchase->purchase_id,
                            'transaction_id' => $existingPendingPurchase->transaction_id,
                            'created_at' => $existingPendingPurchase->created_at
                        ]);

                        // Delete the old transaction and purchase
                        if ($pendingTransaction) {
                            $pendingTransaction->delete();
                        }
                        $existingPendingPurchase->delete();
                    }
                }
            }

            // Generate a unique merchant order ID
            $merchantOrderId = 'book_' . $book->id . '_user_' . $user->user_id . '_' . time();

            // Prepare billing data
            $billingData = [
                'first_name' => $user->name,
                'last_name' => '',
                'email' => $user->email,
                'phone_number' => $user->phone ?? '01000000000',
                'street' => $user->address ?? 'NA',
                'city' => 'NA',
                'country' => 'EG',
                'state' => 'NA',
                'postal_code' => 'NA',
            ];

            // Prepare payment data
            $amountCents = (int)($book->price * 100);
            $paymentData = [
                'amount_cents' => $amountCents,
                'currency' => 'EGP',
                'merchant_order_id' => $merchantOrderId,
                'items' => [
                    [
                        'name' => $book->title,
                        'amount_cents' => $amountCents,
                        'description' => substr($book->description ?? '', 0, 100),
                        'quantity' => 1
                    ]
                ],
                'billing_data' => $billingData
            ];

            // Add integration_id directly to payment data
            $paymentData['integration_id'] = 5066833; // Use the same Integration ID as in PaymentController

            // Log payment data before processing
            Log::info('Processing book payment with Paymob', [
                'book_id' => $book->id,
                'user_id' => $user->user_id,
                'amount' => $book->price,
                'merchant_order_id' => $merchantOrderId,
                'integration_id' => 5066833
            ]);

            // Process payment with Paymob
            $result = $this->paymobService->processPayment($paymentData);

            // Log the result
            Log::info('Paymob payment processing result', [
                'success' => $result['success'] ?? false,
                'iframe_url' => $result['iframe_url'] ?? null,
                'order_id' => $result['order_id'] ?? null,
                'message' => $result['message'] ?? null
            ]);

            if (!$result['success']) {
                Log::error('Failed to process Paymob payment for book', [
                    'book_id' => $book->id,
                    'user_id' => $user->user_id,
                    'error' => $result['message']
                ]);
                return redirect()->back()->with('error', 'Payment processing failed. Please try again later.');
            }

            // Store order_id as the gateway_transaction_id
            $orderIdFromPaymob = $result['order_id'] ?? null;

            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => $user->user_id,
                'amount' => $book->price,
                'currency' => 'EGP',
                'status' => Transaction::STATUS_PENDING,
                'payment_method' => 'paymob',
                'transaction_type' => Transaction::TYPE_PAYMENT,
                'reference_id' => $book->id,
                'reference_type' => 'book',
                'gateway_transaction_id' => $orderIdFromPaymob,
                'gateway_response' => $result,
                'description' => 'Purchase of book: ' . $book->title,
                'ip_address' => $request->ip(),
            ]);

            Log::info('Transaction created for book purchase with Paymob order_id', [
                'transaction_id' => $transaction->transaction_id,
                'gateway_transaction_id' => $orderIdFromPaymob,
                'paymob_order_id' => $orderIdFromPaymob
            ]);

            // In test mode, we don't create a purchase record
            if (!$isTestMode) {
                try {
                    // Create a new book purchase record
                    BookPurchase::create([
                        'user_id' => $user->user_id,
                        'book_id' => $book->id,
                        'amount' => $book->price,
                        'payment_method' => 'paymob',
                        'status' => 'pending',
                        'transaction_id' => $transaction->transaction_id,
                    ]);

                    Log::info('Created new book purchase record', [
                        'user_id' => $user->user_id,
                        'book_id' => $book->id,
                        'transaction_id' => $transaction->transaction_id
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Check if this is a duplicate entry error
                    if ($e->errorInfo[1] == 1062) { // MySQL duplicate entry error code
                        Log::info('Duplicate entry detected, updating existing purchase record', [
                            'user_id' => $user->user_id,
                            'book_id' => $book->id
                        ]);

                        // Find the existing purchase record
                        $existingPurchase = BookPurchase::where('user_id', $user->user_id)
                            ->where('book_id', $book->id)
                            ->first();

                        if ($existingPurchase) {
                            // Update the existing record with the new transaction
                            $existingPurchase->transaction_id = $transaction->transaction_id;
                            $existingPurchase->status = 'pending'; // Reset status to pending
                            $existingPurchase->save();

                            Log::info('Updated existing book purchase record', [
                                'purchase_id' => $existingPurchase->purchase_id,
                                'transaction_id' => $transaction->transaction_id
                            ]);
                        } else {
                            // This shouldn't happen, but log it just in case
                            Log::warning('Could not find existing purchase record despite duplicate entry error', [
                                'user_id' => $user->user_id,
                                'book_id' => $book->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    } else {
                        // For other database errors, log and rethrow
                        Log::error('Database error creating book purchase record', [
                            'user_id' => $user->user_id,
                            'book_id' => $book->id,
                            'error' => $e->getMessage(),
                            'code' => $e->errorInfo[1] ?? 'unknown'
                        ]);
                        throw $e;
                    }
                }
            }

            // For AJAX requests (test mode), return JSON response
            if ($isAjaxRequest) {
                return response()->json([
                    'success' => true,
                    'iframe_url' => $result['iframe_url'],
                    'order_id' => $result['order_id'],
                    'message' => 'Payment iframe URL generated successfully'
                ]);
            }

            // Redirect to Paymob iframe for normal requests
            return redirect()->away($result['iframe_url']);
        } catch (\Exception $e) {
            Log::error('Error processing Paymob payment for book', [
                'book_id' => $book->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($isAjaxRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ]);
            }

            return redirect()->back()->with('error', 'An error occurred while processing your payment. Please try again later.');
        }
    }

    /**
     * Handle the Paymob callback for book purchases.
     */
    public function paymobCallback(Request $request)
    {
        Log::info('Paymob callback received for book purchase', [
            'request' => $request->all()
        ]);

        try {
            $data = $request->all();
            $hmac = $request->header('HMAC');

            // Extract transaction ID from the request
            $initialMerchantOrderId = $request->input('merchant_order_id');

            // Check if this is a book purchase transaction
            if ($initialMerchantOrderId && !Str::startsWith($initialMerchantOrderId, 'book_')) {
                // Not a book purchase, let the regular callback handle it
                return response()->json(['status' => 'not_book_purchase']);
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
                    return redirect()->route('books.index')->with('error', 'Missing transaction details');
                }
            }

            Log::info('Processing book transaction', [
                'transaction_id' => $transactionId,
                'order_id' => $orderId,
                'merchant_order_id' => $merchantOrderId
            ]);

            // Find the transaction in our database - try different possible IDs
            $transaction = null;

            // First try gateway_transaction_id (should match order_id from Paymob)
            if ($orderId) {
                $transaction = Transaction::where('gateway_transaction_id', $orderId)
                    ->where('reference_type', 'book')
                    ->first();
            }

            // If not found, try by ID
            if (!$transaction && $transactionId) {
                $transaction = Transaction::where('gateway_transaction_id', $transactionId)
                    ->where('reference_type', 'book')
                    ->first();
            }

            // If still not found, try by merchant order ID in gateway_response
            if (!$transaction && $merchantOrderId) {
                $transactions = Transaction::where('payment_method', 'paymob')
                    ->where('reference_type', 'book')
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
                    ->where('reference_type', 'book')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['transaction_id', 'gateway_transaction_id', 'created_at', 'gateway_response']);

                Log::error('Paymob transaction not found for book purchase', [
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
                            'merchant_order_id' => $merchantOrderId
                        ]
                    ], 404);
                } else {
                    // This is likely a browser redirect from Paymob - handle it for the user
                    return redirect()->route('books.index')
                        ->with('error', 'Payment information not found. Please contact support.');
                }
            }

            // Find the book purchase
            $bookPurchase = BookPurchase::where('transaction_id', $transaction->transaction_id)->first();

            if (!$bookPurchase) {
                Log::error('Book purchase not found for transaction', [
                    'transaction_id' => $transaction->transaction_id,
                    'data' => $data
                ]);

                if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                    return response()->json(['status' => 'error', 'message' => 'Book purchase not found'], 404);
                } else {
                    return redirect()->route('books.index')->with('error', 'Book purchase details not found');
                }
            }

            DB::beginTransaction();

            // Determine the transaction status
            $success = $data['success'] ?? $request->input('success') ?? false;
            if (is_string($success)) {
                $success = strtolower($success) === 'true';
            }

            $isVoided = $data['is_voided'] ?? $request->input('is_voided') ?? false;
            $isRefunded = $data['is_refunded'] ?? $request->input('is_refunded') ?? false;
            $errorOccurred = $data['error_occured'] ?? $request->input('error_occured') ?? false;

            // Convert string boolean values to actual booleans
            if (is_string($isVoided)) {
                $isVoided = strtolower($isVoided) === 'true';
            }
            if (is_string($isRefunded)) {
                $isRefunded = strtolower($isRefunded) === 'true';
            }
            if (is_string($errorOccurred)) {
                $errorOccurred = strtolower($errorOccurred) === 'true';
            }

            // Check for additional success indicators
            $hasSuccessIndicator = false;

            // Check for success in data.source_data.pan (nested format)
            if (isset($data['source_data']) && isset($data['source_data']['pan']) && !empty($data['source_data']['pan'])) {
                $hasSuccessIndicator = true;
                Log::info('Found PAN data which indicates successful payment', [
                    'pan_masked' => substr($data['source_data']['pan'], 0, 4) . '******'
                ]);
            }

            // Check for success in data.source_data_pan (flat format)
            if (isset($data['source_data_pan']) && !empty($data['source_data_pan'])) {
                $hasSuccessIndicator = true;
                Log::info('Found source_data_pan which indicates successful payment', [
                    'pan_masked' => $data['source_data_pan']
                ]);
            }

            // Check for approved transaction response code
            if (isset($data['txn_response_code']) && strtoupper($data['txn_response_code']) === 'APPROVED') {
                $hasSuccessIndicator = true;
                Log::info('Found APPROVED txn_response_code', [
                    'txn_response_code' => $data['txn_response_code']
                ]);
            }

            // Check for successful acquirer response code (00 means success)
            if (isset($data['acq_response_code']) && $data['acq_response_code'] === '00') {
                $hasSuccessIndicator = true;
                Log::info('Found successful acq_response_code', [
                    'acq_response_code' => $data['acq_response_code']
                ]);
            }

            // Check for payment_result.response_status success
            if (isset($data['payment_result']) && isset($data['payment_result']['response_status']) &&
                strtolower($data['payment_result']['response_status']) === 'success') {
                $hasSuccessIndicator = true;
                Log::info('Found success in payment_result.response_status', [
                    'payment_result' => $data['payment_result']
                ]);
            }

            // Determine final status based on all indicators
            $finalSuccess = $success || $hasSuccessIndicator;

            // Log all the decision factors
            Log::info('Payment decision factors', [
                'success' => $success,
                'success_type' => gettype($success),
                'hasSuccessIndicator' => $hasSuccessIndicator,
                'finalSuccess' => $finalSuccess,
                'isVoided' => $isVoided,
                'isVoided_type' => gettype($isVoided),
                'isRefunded' => $isRefunded,
                'isRefunded_type' => gettype($isRefunded),
                'errorOccurred' => $errorOccurred,
                'errorOccurred_type' => gettype($errorOccurred),
                'condition_result' => ($isVoided || $isRefunded || $errorOccurred || !$finalSuccess)
            ]);

            if ($isVoided || $isRefunded || $errorOccurred || !$finalSuccess) {
                $transaction->status = Transaction::STATUS_FAILED;
                $bookPurchase->status = 'failed';

                Log::info('Book payment marked as failed', [
                    'transaction_id' => $transaction->transaction_id,
                    'purchase_id' => $bookPurchase->purchase_id,
                    'order_id' => $orderId,
                    'success' => $success,
                    'has_success_indicator' => $hasSuccessIndicator
                ]);
            } else if ($finalSuccess) {
                $transaction->status = Transaction::STATUS_COMPLETED;
                $bookPurchase->status = 'completed';
                $bookPurchase->purchased_at = now();

                Log::info('Book payment marked as completed', [
                    'transaction_id' => $transaction->transaction_id,
                    'purchase_id' => $bookPurchase->purchase_id,
                    'order_id' => $orderId,
                    'success' => $success,
                    'has_success_indicator' => $hasSuccessIndicator
                ]);
            }

            // Save updated records
            $transaction->gateway_response = array_merge($transaction->gateway_response ?? [], $data);
            $transaction->save();
            $bookPurchase->save();

            // Commit all DB changes
            DB::commit();

            // If this is an API call, return JSON response
            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json(['status' => 'success']);
            } else {
                // If it's a browser request, redirect to the appropriate page
                $book = Book::find($bookPurchase->book_id);

                if (!$book) {
                    Log::error('Book not found for confirmation page', [
                        'book_id' => $bookPurchase->book_id
                    ]);
                    return redirect()->route('books.index')
                        ->with('error', 'Book not found. Please contact support.');
                }

                if ($transaction->status === Transaction::STATUS_COMPLETED || $bookPurchase->status === 'completed') {
                    return redirect()->route('books.purchase.confirmation', [
                        'book' => $book,
                        'status' => 'completed',
                        'transaction_id' => $transaction->transaction_id
                    ])->with('success', 'تم إتمام الدفع بنجاح!');
                } else {
                    return redirect()->route('books.purchase.confirmation', [
                        'book' => $book,
                        'status' => 'failed',
                        'transaction_id' => $transaction->transaction_id
                    ])->with('error', 'فشلت عملية الدفع.');
                }
            }
        } catch (\Exception $e) {
            // Rollback any DB changes if an error occurs
            DB::rollBack();

            Log::error('Error processing Paymob callback for book purchase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            if ($request->expectsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
                return response()->json(['status' => 'error', 'message' => 'Internal server error: ' . $e->getMessage()], 500);
            } else {
                return redirect()->route('books.index')
                    ->with('error', 'حدث خطأ أثناء معالجة الدفع. الرجاء المحاولة مرة أخرى.');
            }
        }
    }

    /**
     * Display the purchase confirmation page.
     *
     * @param Request $request
     * @param Book $book
     * @return \Illuminate\View\View
     */
    public function showConfirmation(Request $request, Book $book)
    {
        $urlStatus = $request->input('status', 'pending');
        $transactionId = $request->input('transaction_id');

        // Log the request for debugging
        Log::info('Showing purchase confirmation page', [
            'book_id' => $book->id,
            'url_status' => $urlStatus,
            'transaction_id' => $transactionId
        ]);

        // Get the transaction if ID is provided
        $transaction = null;
        if ($transactionId) {
            $transaction = Transaction::find($transactionId);
        }

        // Get the purchase record
        $purchase = null;
        if ($transaction) {
            $purchase = BookPurchase::where('transaction_id', $transaction->transaction_id)
                ->where('book_id', $book->id)
                ->first();
        }

        // Check if the user has already purchased this book (regardless of transaction)
        $user = auth()->user();
        $hasAccess = false;

        if ($user) {
            $hasAccess = BookPurchase::where('user_id', $user->user_id)
                ->where('book_id', $book->id)
                ->where('status', 'completed')
                ->exists();
        }

        // Determine the status to display
        $status = 'pending'; // Default status

        // If we have a purchase record with a completed status, show completed
        if ($purchase && $purchase->status === 'completed') {
            $status = 'completed';
        }
        // If the transaction is completed, show completed
        elseif ($transaction && $transaction->status === Transaction::STATUS_COMPLETED) {
            $status = 'completed';
        }
        // If the user has access to the book, show completed
        elseif ($hasAccess) {
            $status = 'completed';
        }
        // If the transaction or purchase is explicitly failed, show failed
        elseif (($transaction && $transaction->status === Transaction::STATUS_FAILED) ||
                ($purchase && $purchase->status === 'failed')) {
            $status = 'failed';
        }
        // If the URL explicitly says completed and we don't have contrary evidence, trust it
        elseif ($urlStatus === 'completed') {
            $status = 'completed';
        }
        // If the URL explicitly says failed and we don't have contrary evidence, trust it
        elseif ($urlStatus === 'failed') {
            $status = 'failed';
        }

        // Log the final determined status
        Log::info('Final status for confirmation page', [
            'url_status' => $urlStatus,
            'determined_status' => $status,
            'transaction_status' => $transaction?->status,
            'purchase_status' => $purchase?->status,
            'user_has_access' => $hasAccess
        ]);

        return view('books.purchase-confirmation', [
            'book' => $book,
            'status' => $status,
            'transaction' => $transaction,
            'purchase' => $purchase
        ]);
    }
}
