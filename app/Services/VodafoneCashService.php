<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class VodafoneCashService
{
    /**
     * Vodafone Cash merchant code
     *
     * @var string
     */
    protected $merchantCode;

    /**
     * Vodafone Cash API key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * HTTP request timeout in seconds
     *
     * @var int
     */
    protected $timeout = 30;

    /**
     * Number of retry attempts
     *
     * @var int
     */
    protected $retries = 3;

    /**
     * Mock mode flag
     *
     * @var bool
     */
    protected $mockMode = true;

    /**
     * Create a new VodafoneCashService instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->merchantCode = config('services.vodafone_cash.merchant_code');
        $this->apiKey = config('services.vodafone_cash.api_key');

        // Validate configuration
        $this->validateConfig();

        // If API key is provided, disable mock mode
        if (!empty($this->apiKey)) {
            $this->mockMode = false;
        }
    }

    /**
     * Validate that all required configuration is present
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateConfig()
    {
        if (empty($this->merchantCode)) {
            throw new \InvalidArgumentException('Vodafone Cash merchant code is not configured');
        }
    }

    /**
     * Process a payment with Vodafone Cash
     *
     * @param array $paymentData
     * @return array
     */
    public function processPayment($paymentData)
    {
        // Log the payment data for debugging
        Log::info('Vodafone Cash payment processing started', [
            'payment_data' => $paymentData,
            'mock_mode' => $this->mockMode
        ]);

        // Validate required fields
        if (!isset($paymentData['phone_number']) || !isset($paymentData['amount'])) {
            Log::error('Missing required payment data', [
                'payment_data' => $paymentData
            ]);
            return [
                'success' => false,
                'message' => 'Missing required payment data (phone number or amount)'
            ];
        }

        // Generate a transaction reference
        $transactionRef = $this->generateTransactionReference();

        // If in mock mode, simulate API response
        if ($this->mockMode) {
            return $this->mockProcessPayment($paymentData, $transactionRef);
        }

        // Real API implementation would go here
        try {
            // This is a placeholder for real API implementation
            // In a real implementation, you would make API calls to Vodafone Cash
            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 100)
                ->post('https://api.vodafonecash.com.eg/payments', [
                    'merchant_code' => $this->merchantCode,
                    'api_key' => $this->apiKey,
                    'phone_number' => $paymentData['phone_number'],
                    'amount' => $paymentData['amount'],
                    'transaction_ref' => $transactionRef,
                    'description' => $paymentData['description'] ?? 'Payment',
                    'callback_url' => $paymentData['callback_url'] ?? null,
                ]);

            if ($response->failed()) {
                Log::error('Vodafone Cash payment failed', [
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body(),
                    'payment_data' => $paymentData
                ]);
                return [
                    'success' => false,
                    'message' => 'Payment processing failed'
                ];
            }

            $data = $response->json();
            return [
                'success' => true,
                'transaction_ref' => $transactionRef,
                'status' => 'pending', // Most mobile money payments are asynchronous
                'message' => 'Payment initiated, check your phone to confirm',
                'details' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Vodafone Cash payment error: ' . $e->getMessage(), [
                'payment_data' => $paymentData
            ]);
            return [
                'success' => false,
                'message' => 'Payment processing error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate a unique transaction reference
     *
     * @return string
     */
    private function generateTransactionReference()
    {
        return 'VF_' . strtoupper(Str::random(16)) . '_' . time();
    }

    /**
     * Mock the payment processing for testing/development
     *
     * @param array $paymentData
     * @param string $transactionRef
     * @return array
     */
    private function mockProcessPayment($paymentData, $transactionRef)
    {
        Log::info('Using mock Vodafone Cash payment processing', [
            'transaction_ref' => $transactionRef
        ]);

        // Simulate network delay
        sleep(1);

        // Randomize success (90% success rate)
        $isSuccess = mt_rand(1, 100) <= 90;

        if ($isSuccess) {
            return [
                'success' => true,
                'transaction_ref' => $transactionRef,
                'status' => 'pending',
                'message' => 'Payment initiated, please check your phone to confirm',
                'mock' => true,
                'instructions' => 'This is a simulated transaction. In a real environment, the user would receive a prompt on their phone.',
                'verification_link' => route('payment.verify.vodafone', ['reference' => $transactionRef])
            ];
        } else {
            return [
                'success' => false,
                'transaction_ref' => $transactionRef,
                'status' => 'failed',
                'message' => 'Payment failed. Please try again.',
                'mock' => true,
                'error_code' => 'MOCK_ERROR_' . mt_rand(1000, 9999)
            ];
        }
    }

    /**
     * Verify payment status
     *
     * @param string $transactionRef
     * @return array
     */
    public function verifyPayment($transactionRef)
    {
        Log::info('Verifying Vodafone Cash payment', [
            'transaction_ref' => $transactionRef,
            'mock_mode' => $this->mockMode
        ]);

        if ($this->mockMode) {
            return $this->mockVerifyPayment($transactionRef);
        }

        // Real API implementation would go here
        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 100)
                ->get('https://api.vodafonecash.com.eg/transactions/' . $transactionRef, [
                    'merchant_code' => $this->merchantCode,
                    'api_key' => $this->apiKey
                ]);

            if ($response->failed()) {
                Log::error('Vodafone Cash verification failed', [
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body(),
                    'transaction_ref' => $transactionRef
                ]);
                return [
                    'success' => false,
                    'verified' => false,
                    'message' => 'Verification failed'
                ];
            }

            $data = $response->json();
            return [
                'success' => true,
                'verified' => $data['status'] === 'completed',
                'status' => $data['status'],
                'message' => $data['message'] ?? 'Payment verification processed',
                'details' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Vodafone Cash verification error: ' . $e->getMessage(), [
                'transaction_ref' => $transactionRef
            ]);
            return [
                'success' => false,
                'verified' => false,
                'message' => 'Verification error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mock the payment verification for testing/development
     *
     * @param string $transactionRef
     * @return array
     */
    private function mockVerifyPayment($transactionRef)
    {
        Log::info('Using mock Vodafone Cash payment verification', [
            'transaction_ref' => $transactionRef
        ]);

        // Simulate network delay
        sleep(1);

        // Check if the verification was already done
        $cacheKey = 'vodafone_payment_' . $transactionRef;
        $previousStatus = Cache::get($cacheKey);

        // If no previous status, create a new one (80% completed, 20% pending)
        if (!$previousStatus) {
            $status = mt_rand(1, 100) <= 80 ? 'completed' : 'pending';
            
            // Cache the status for 30 minutes
            Cache::put($cacheKey, $status, 30 * 60);
            
            $previousStatus = $status;
        }

        return [
            'success' => true,
            'verified' => $previousStatus === 'completed',
            'status' => $previousStatus,
            'message' => $previousStatus === 'completed' 
                ? 'Payment completed successfully' 
                : 'Payment is still pending',
            'transaction_ref' => $transactionRef,
            'mock' => true,
            'timestamp' => now()->toIso8601String(),
            'details' => [
                'payment_id' => substr($transactionRef, 3, 8),
                'phone_number' => '****' . mt_rand(1000, 9999), // Masked phone number for privacy
                'amount' => mt_rand(100, 5000) / 100, // Random amount between 1.00 and 50.00
                'fee' => 0,
                'total' => mt_rand(100, 5000) / 100
            ]
        ];
    }
} 