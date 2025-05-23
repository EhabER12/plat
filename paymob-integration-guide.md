# Paymob Integration Guide

## Step 1: Configure Environment Variables

Add the following variables to your `.env` file:

```
# Paymob Configuration
PAYMOB_BASE_URL=https://accept.paymob.com/api
PAYMOB_API_KEY=your_api_key_here
PAYMOB_INTEGRATION_ID=your_integration_id_here
PAYMOB_IFRAME_ID=your_iframe_id_here
PAYMOB_HMAC_SECRET=your_hmac_secret_here
```

## Step 2: Create Paymob Service

Create a new service provider for Paymob integration:

```bash
php artisan make:provider PaymobServiceProvider
```

## Step 3: Create Paymob Config File

Create a new config file at `config/paymob.php`:

```php
<?php

return [
    'base_url' => env('PAYMOB_BASE_URL', 'https://accept.paymob.com/api'),
    'api_key' => env('PAYMOB_API_KEY'),
    'integration_id' => env('PAYMOB_INTEGRATION_ID'),
    'iframe_id' => env('PAYMOB_IFRAME_ID'),
    'hmac_secret' => env('PAYMOB_HMAC_SECRET'),
];
```

## Step 4: Register the Config in ServiceProvider

Update the `PaymobServiceProvider.php`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PaymobServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/paymob.php', 'paymob'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/paymob.php' => config_path('paymob.php'),
        ], 'config');
    }
}
```

## Step 5: Create Paymob Service Class

Create a new class at `app/Services/PaymobService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PaymobService
{
    protected $baseUrl;
    protected $apiKey;
    protected $integrationId;
    protected $iframeId;

    public function __construct()
    {
        $this->baseUrl = config('paymob.base_url');
        $this->apiKey = config('paymob.api_key');
        $this->integrationId = config('paymob.integration_id');
        $this->iframeId = config('paymob.iframe_id');
    }

    /**
     * Step 1: Authentication Request
     */
    public function authenticate()
    {
        $response = Http::post("{$this->baseUrl}/auth/tokens", [
            'api_key' => $this->apiKey
        ]);
        
        return $response->json();
    }

    /**
     * Step 2: Order Registration API
     */
    public function registerOrder($authToken, $amount, $items = [], $merchantOrderId = null)
    {
        $response = Http::post("{$this->baseUrl}/ecommerce/orders", [
            'auth_token' => $authToken,
            'delivery_needed' => false,
            'amount_cents' => $amount * 100, // Convert to cents
            'currency' => 'EGP',
            'merchant_order_id' => $merchantOrderId ?? time(),
            'items' => $items,
        ]);
        
        return $response->json();
    }

    /**
     * Step 3: Payment Key Request
     */
    public function getPaymentKey($authToken, $orderId, $amount, $user)
    {
        $response = Http::post("{$this->baseUrl}/acceptance/payment_keys", [
            'auth_token' => $authToken,
            'amount_cents' => $amount * 100, // Convert to cents
            'expiration' => 3600,
            'order_id' => $orderId,
            'billing_data' => [
                'apartment' => 'NA',
                'email' => $user->email,
                'floor' => 'NA',
                'first_name' => $user->first_name ?? $user->name,
                'street' => 'NA',
                'building' => 'NA',
                'phone_number' => $user->phone ?? 'NA',
                'shipping_method' => 'NA',
                'postal_code' => 'NA',
                'city' => 'NA',
                'country' => 'NA',
                'last_name' => $user->last_name ?? '',
                'state' => 'NA',
            ],
            'currency' => 'EGP',
            'integration_id' => $this->integrationId,
            'lock_order_when_paid' => true
        ]);
        
        return $response->json();
    }

    /**
     * Generate Paymob iframe URL
     */
    public function getIframeUrl($paymentToken)
    {
        return "https://accept.paymobsolutions.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentToken}";
    }
    
    /**
     * Process callback from Paymob
     */
    public function validateTransaction($request)
    {
        $hmacSecret = config('paymob.hmac_secret');
        
        // Build the string that will be hashed
        $concatenatedString = $request->get('amount_cents') . 
            $request->get('created_at') . 
            $request->get('currency') . 
            $request->get('error_occured') . 
            $request->get('has_parent_transaction') . 
            $request->get('id') . 
            $request->get('integration_id') . 
            $request->get('is_3d_secure') . 
            $request->get('is_auth') . 
            $request->get('is_capture') . 
            $request->get('is_refunded') . 
            $request->get('is_standalone_payment') . 
            $request->get('is_voided') . 
            $request->get('order') . 
            $request->get('owner') . 
            $request->get('pending') . 
            $request->get('source_data_pan') . 
            $request->get('source_data_sub_type') . 
            $request->get('source_data_type') . 
            $request->get('success');
            
        $calculatedHmac = hash_hmac('sha512', $concatenatedString, $hmacSecret);
        
        return $calculatedHmac === $request->get('hmac');
    }
}
```

## Step 6: Create Controller for Payment

Create a controller to handle payments:

```bash
php artisan make:controller PaymentController
```

Then implement the controller:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymobService;
use Auth;

class PaymentController extends Controller
{
    protected $paymobService;
    
    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }
    
    public function checkout(Request $request)
    {
        // Validate amount and other data
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);
        
        $amount = $request->amount;
        $user = Auth::user();
        
        // Step 1: Authenticate with Paymob
        $authResponse = $this->paymobService->authenticate();
        $authToken = $authResponse['token'];
        
        // Step 2: Register Order
        $orderResponse = $this->paymobService->registerOrder(
            $authToken, 
            $amount
        );
        $orderId = $orderResponse['id'];
        
        // Step 3: Get Payment Key
        $paymentKeyResponse = $this->paymobService->getPaymentKey(
            $authToken,
            $orderId,
            $amount,
            $user
        );
        $paymentToken = $paymentKeyResponse['token'];
        
        // Step 4: Generate iframe URL
        $iframeUrl = $this->paymobService->getIframeUrl($paymentToken);
        
        // Save payment details to your database here
        
        return view('payments.checkout', compact('iframeUrl'));
    }
    
    public function callback(Request $request)
    {
        // Validate HMAC
        $isValid = $this->paymobService->validateTransaction($request);
        
        if (!$isValid) {
            return response()->json(['error' => 'Invalid transaction'], 400);
        }
        
        // Process the payment result
        $success = $request->get('success') === 'true';
        $transactionId = $request->get('id');
        $orderId = $request->get('order');
        $amount = $request->get('amount_cents') / 100; // Convert from cents
        
        // Update your database with payment status
        
        if ($success) {
            // Payment successful
            return response()->json(['status' => 'success', 'transaction_id' => $transactionId]);
        } else {
            // Payment failed
            return response()->json(['status' => 'failed', 'transaction_id' => $transactionId]);
        }
    }
    
    public function processResponse(Request $request)
    {
        // This is the page user will be redirected to after payment
        $success = $request->get('success') === 'true';
        
        if ($success) {
            return view('payments.success');
        } else {
            return view('payments.failed');
        }
    }
}
```

## Step 7: Create Routes

Add the following routes to your `routes/web.php` file:

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
    Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
    Route::get('/payment/response', [PaymentController::class, 'processResponse'])->name('payment.response');
});
```

## Step 8: Create View for Checkout

Create a view at `resources/views/payments/checkout.blade.php`:

```html
@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Checkout</div>
                <div class="card-body">
                    <h3 class="mb-4">Payment</h3>
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="{{ $iframeUrl }}" width="100%" height="600px" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

## Step 9: Register the Service Provider

Add the Paymob service provider to your `config/app.php` file:

```php
'providers' => [
    // Other providers...
    App\Providers\PaymobServiceProvider::class,
],
``` 