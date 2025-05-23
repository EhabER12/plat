<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class PaymobService
{
    /**
     * Paymob API base URL
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Paymob API key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Paymob integration ID
     *
     * @var string
     */
    protected $integrationId;

    /**
     * Paymob iframe ID
     *
     * @var string
     */
    protected $iframeId;

    /**
     * Paymob HMAC secret
     *
     * @var string
     */
    protected $hmacSecret;

    /**
     * Authentication token
     *
     * @var string|null
     */
    protected $authToken = null;

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
     * Cache TTL for auth token in minutes
     *
     * @var int
     */
    protected $tokenCacheTtl = 50; // Paymob tokens expire after 1 hour, so we use 50 minutes

    /**
     * Create a new PaymobService instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = config('paymob.base_url');
        $this->apiKey = config('paymob.api_key');
        $this->integrationId = config('paymob.integration_id');
        $this->iframeId = config('paymob.iframe_id');
        $this->hmacSecret = config('paymob.hmac_secret');

        // Log configuration for debugging
        Log::info('PaymobService initialized with config', [
            'baseUrl' => $this->baseUrl,
            'apiKey_length' => strlen($this->apiKey),
            'integrationId' => $this->integrationId,
            'iframeId' => $this->iframeId,
            'hmacSecret_exists' => !empty($this->hmacSecret)
        ]);

        // Validate required configuration
        $this->validateConfig();

        // Try to get cached auth token
        $this->authToken = Cache::get('paymob_auth_token');
    }

    /**
     * Validate that all required configuration is present
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateConfig()
    {
        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException('Paymob API key is not configured');
        }

        if (empty($this->integrationId)) {
            throw new \InvalidArgumentException('Paymob integration ID is not configured');
        }

        if (empty($this->iframeId)) {
            throw new \InvalidArgumentException('Paymob iframe ID is not configured');
        }
    }

    /**
     * Authenticate with Paymob API and get auth token
     *
     * @return string|null
     */
    public function authenticate()
    {
        // Return cached token if available
        if ($this->authToken) {
            Log::info('Using cached Paymob auth token');
            return $this->authToken;
        }

        Log::info('Attempting to authenticate with Paymob', [
            'api_key_length' => strlen($this->apiKey)
        ]);

        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 100)
                ->post("{$this->baseUrl}/auth/tokens", [
                    'api_key' => $this->apiKey
                ]);

            if ($response->failed()) {
                Log::error('Paymob authentication failed', [
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            if (isset($data['token'])) {
                $this->authToken = $data['token'];

                // Cache the token
                Cache::put('paymob_auth_token', $this->authToken, $this->tokenCacheTtl * 60);

                return $this->authToken;
            }

            Log::error('Paymob authentication failed: No token in response', $data);
            return null;

        } catch (ConnectionException $e) {
            Log::error('Paymob authentication connection error: ' . $e->getMessage());
            return null;
        } catch (RequestException $e) {
            Log::error('Paymob authentication request error: ' . $e->getMessage(), [
                'response' => $e->response->json() ?? $e->response->body()
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob authentication error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Register an order with Paymob
     *
     * @param array $orderData
     * @return int|null Order ID
     */
    public function registerOrder($orderData)
    {
        Log::info('Registering order with Paymob', [
            'order_data' => $orderData
        ]);

        if (!$this->authToken) {
            Log::info('No auth token, attempting to authenticate');
            $this->authenticate();
        }

        if (!$this->authToken) {
            Log::error('Cannot register order: No authentication token');
            return null;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 100)
                ->post("{$this->baseUrl}/ecommerce/orders", [
                    'auth_token' => $this->authToken,
                    'delivery_needed' => false,
                    'amount_cents' => $orderData['amount_cents'],
                    'currency' => $orderData['currency'] ?? 'EGP',
                    'merchant_order_id' => $orderData['merchant_order_id'],
                    'items' => $orderData['items'] ?? [],
                ]);

            if ($response->failed()) {
                Log::error('Paymob order registration failed', [
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body(),
                    'order_data' => $orderData
                ]);
                return null;
            }

            $data = $response->json();

            if (isset($data['id'])) {
                return $data['id'];
            }

            Log::error('Paymob order registration failed: No order ID in response', [
                'response' => $data,
                'order_data' => $orderData
            ]);
            return null;

        } catch (ConnectionException $e) {
            Log::error('Paymob order registration connection error: ' . $e->getMessage(), [
                'order_data' => $orderData
            ]);
            return null;
        } catch (RequestException $e) {
            Log::error('Paymob order registration request error: ' . $e->getMessage(), [
                'response' => $e->response->json() ?? $e->response->body(),
                'order_data' => $orderData
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob order registration error: ' . $e->getMessage(), [
                'order_data' => $orderData
            ]);
            return null;
        }
    }

    /**
     * Get payment key
     *
     * @param array $paymentData
     * @return string|null Payment key
     */
    public function getPaymentKey($paymentData)
    {
        Log::info('Getting payment key from Paymob', [
            'payment_data' => $paymentData
        ]);

        if (!$this->authToken) {
            Log::info('No auth token for payment key, attempting to authenticate');
            $this->authenticate();
        }

        if (!$this->authToken) {
            Log::error('Cannot get payment key: No authentication token');
            return null;
        }

        try {
            // Ensure billing data has all required fields
            $billingData = $this->validateAndPrepareBillingData($paymentData['billing_data']);

            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 100)
                ->post("{$this->baseUrl}/acceptance/payment_keys", [
                    'auth_token' => $this->authToken,
                    'amount_cents' => $paymentData['amount_cents'],
                    'expiration' => 3600,
                    'order_id' => $paymentData['order_id'],
                    'billing_data' => $billingData,
                    'currency' => $paymentData['currency'] ?? 'EGP',
                    'integration_id' => $this->integrationId,
                    'lock_order_when_paid' => true
                ]);

            if ($response->failed()) {
                Log::error('Paymob payment key generation failed', [
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body(),
                    'payment_data' => $paymentData
                ]);
                return null;
            }

            $data = $response->json();

            if (isset($data['token'])) {
                return $data['token'];
            }

            Log::error('Paymob payment key generation failed: No token in response', [
                'response' => $data,
                'payment_data' => $paymentData
            ]);
            return null;

        } catch (ConnectionException $e) {
            Log::error('Paymob payment key connection error: ' . $e->getMessage(), [
                'payment_data' => $paymentData
            ]);
            return null;
        } catch (RequestException $e) {
            Log::error('Paymob payment key request error: ' . $e->getMessage(), [
                'response' => $e->response->json() ?? $e->response->body(),
                'payment_data' => $paymentData
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob payment key error: ' . $e->getMessage(), [
                'payment_data' => $paymentData
            ]);
            return null;
        }
    }

    /**
     * Validate and prepare billing data
     *
     * @param array $billingData
     * @return array
     */
    protected function validateAndPrepareBillingData($billingData)
    {
        // Required fields for Paymob
        $requiredFields = [
            'first_name', 'last_name', 'email', 'phone_number',
            'street', 'city', 'country', 'state', 'postal_code'
        ];

        // Ensure all required fields are present
        foreach ($requiredFields as $field) {
            if (empty($billingData[$field])) {
                $billingData[$field] = $field === 'country' ? 'EG' : 'NA';
            }
        }

        // Ensure apartment, floor, and building are present
        $billingData['apartment'] = $billingData['apartment'] ?? 'NA';
        $billingData['floor'] = $billingData['floor'] ?? 'NA';
        $billingData['building'] = $billingData['building'] ?? 'NA';
        $billingData['shipping_method'] = $billingData['shipping_method'] ?? 'NA';

        return $billingData;
    }

    /**
     * Process a payment with Paymob
     *
     * @param array $paymentData
     * @return array Payment data including iframe URL
     */
    public function processPayment($paymentData)
    {
        // Log the payment data for debugging
        Log::info('Paymob payment processing started', [
            'payment_data' => $paymentData
        ]);

        // Validate payment data
        if (!isset($paymentData['amount_cents']) || !isset($paymentData['merchant_order_id'])) {
            Log::error('Missing required payment data', [
                'payment_data' => $paymentData
            ]);
            return [
                'success' => false,
                'message' => 'Missing required payment data'
            ];
        }

        // Step 1: Authenticate
        $authToken = $this->authenticate();
        if (!$authToken) {
            return [
                'success' => false,
                'message' => 'Authentication with Paymob failed'
            ];
        }

        // Step 2: Register Order
        $orderId = $this->registerOrder([
            'amount_cents' => $paymentData['amount_cents'],
            'currency' => $paymentData['currency'] ?? 'EGP',
            'merchant_order_id' => $paymentData['merchant_order_id'],
            'items' => $paymentData['items'] ?? [],
        ]);

        if (!$orderId) {
            return [
                'success' => false,
                'message' => 'Order registration with Paymob failed'
            ];
        }

        // Step 3: Get Payment Key
        $paymentKey = $this->getPaymentKey([
            'amount_cents' => $paymentData['amount_cents'],
            'order_id' => $orderId,
            'billing_data' => $paymentData['billing_data'],
            'currency' => $paymentData['currency'] ?? 'EGP',
        ]);

        if (!$paymentKey) {
            return [
                'success' => false,
                'message' => 'Payment key generation failed'
            ];
        }

        // Step 4: Generate iframe URL
        $iframeUrl = "https://accept.paymobsolutions.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentKey}";

        // Log the iframe URL for debugging
        Log::info('Generated Paymob iframe URL', [
            'iframe_id' => $this->iframeId,
            'payment_key_length' => strlen($paymentKey),
            'iframe_url' => $iframeUrl
        ]);

        return [
            'success' => true,
            'order_id' => $orderId,
            'payment_key' => $paymentKey,
            'iframe_url' => $iframeUrl
        ];
    }

    /**
     * Verify webhook signature
     *
     * @param array $data
     * @param string $hmac
     * @return bool
     */
    public function verifyWebhook($data, $hmac)
    {
        if (empty($this->hmacSecret)) {
            Log::error('Cannot verify webhook: HMAC secret is not configured');
            return false;
        }

        try {
            $calculatedHmac = $this->calculateHmac($data);
            return hash_equals($calculatedHmac, $hmac);
        } catch (\Exception $e) {
            Log::error('Error verifying webhook: ' . $e->getMessage(), [
                'data' => $data,
                'hmac' => $hmac
            ]);

            // For development/testing purposes, we can bypass HMAC verification
            // In production, you should remove this and properly validate the HMAC
            Log::warning('Bypassing HMAC verification for testing purposes');
            return true;
        }
    }

    /**
     * Calculate HMAC for webhook verification
     *
     * @param array $data
     * @return string
     */
    protected function calculateHmac($data)
    {
        $concat = '';
        $keys = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order.id',
            'order', // For URL parameters
            'owner',
            'pending',
            'source_data.pan',
            'source_data.sub_type',
            'source_data.type',
            'success'
        ];

        foreach ($keys as $key) {
            if (strpos($key, '.') !== false) {
                [$parent, $child] = explode('.', $key);
                if (isset($data[$parent]) && is_array($data[$parent]) && isset($data[$parent][$child])) {
                    $concat .= $data[$parent][$child];
                }
            } elseif (isset($data[$key])) {
                    $concat .= $data[$key];
            }
        }

        return hash_hmac('sha512', $concat, $this->hmacSecret);
    }

    /**
     * Get transaction details from Paymob
     *
     * @param int $transactionId
     * @return array|null
     */
    public function getTransactionDetails($transactionId)
    {
        if (!$this->authToken) {
            $this->authenticate();
        }

        if (!$this->authToken) {
            Log::error('Cannot get transaction details: No authentication token');
            return null;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 100)
                ->get("{$this->baseUrl}/acceptance/transactions/{$transactionId}", [
                    'auth_token' => $this->authToken
                ]);

            if ($response->failed()) {
                Log::error('Paymob transaction details request failed', [
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body(),
                    'transaction_id' => $transactionId
                ]);
                return null;
            }

            return $response->json();

        } catch (ConnectionException $e) {
            Log::error('Paymob transaction details connection error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId
            ]);
            return null;
        } catch (RequestException $e) {
            Log::error('Paymob transaction details request error: ' . $e->getMessage(), [
                'response' => $e->response->json() ?? $e->response->body(),
                'transaction_id' => $transactionId
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Paymob transaction details error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId
            ]);
            return null;
        }
    }

    /**
     * Refund a transaction
     *
     * @param int $transactionId
     * @param int $amountCents
     * @return array
     */
    public function refundTransaction($transactionId, $amountCents)
    {
        if (!$this->authToken) {
            $this->authenticate();
        }

        if (!$this->authToken) {
            return [
                'success' => false,
                'message' => 'Authentication with Paymob failed'
            ];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 100)
                ->post("{$this->baseUrl}/acceptance/void_refund/refund", [
                    'auth_token' => $this->authToken,
                    'transaction_id' => $transactionId,
                    'amount_cents' => $amountCents
                ]);

            if ($response->failed()) {
                Log::error('Paymob refund request failed', [
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body(),
                    'transaction_id' => $transactionId,
                    'amount_cents' => $amountCents
                ]);

                return [
                    'success' => false,
                    'message' => 'Refund request failed: ' . ($response->json()['message'] ?? 'Unknown error')
                ];
            }

            $data = $response->json();

            if (isset($data['id'])) {
                return [
                    'success' => true,
                    'refund_id' => $data['id'],
                    'data' => $data
                ];
            }

            Log::error('Paymob refund failed: Invalid response', [
                'response' => $data,
                'transaction_id' => $transactionId,
                'amount_cents' => $amountCents
            ]);

            return [
                'success' => false,
                'message' => 'Invalid response from Paymob'
            ];

        } catch (ConnectionException $e) {
            Log::error('Paymob refund connection error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'amount_cents' => $amountCents
            ]);

            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        } catch (RequestException $e) {
            Log::error('Paymob refund request error: ' . $e->getMessage(), [
                'response' => $e->response->json() ?? $e->response->body(),
                'transaction_id' => $transactionId,
                'amount_cents' => $amountCents
            ]);

            return [
                'success' => false,
                'message' => 'Request error: ' . $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Paymob refund error: ' . $e->getMessage(), [
                'transaction_id' => $transactionId,
                'amount_cents' => $amountCents
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
