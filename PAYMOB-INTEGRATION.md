# Paymob Payment Integration Guide

This guide explains how to configure and use the Paymob payment gateway in your Laravel application.

## Configuration

### 1. Paymob Dashboard Setup

1. Log in to your [Paymob Dashboard](https://accept.paymobsolutions.com/login)
2. Navigate to "Developers" > "Payment Integrations" 
3. Note down the following information:
   - Integration ID: `5066833` (as shown in your screenshot)
   - API Key: Find this in your Paymob account settings
   - HMAC Secret: Required for securing webhook callbacks

### 2. Environment Variables

Add the following environment variables to your `.env` file:

```
# Paymob Configuration
PAYMOB_BASE_URL=https://accept.paymob.com/api
PAYMOB_API_KEY=your-api-key-here
PAYMOB_INTEGRATION_ID=5066833
PAYMOB_IFRAME_ID=your-iframe-id-here
PAYMOB_HMAC_SECRET=your-hmac-secret-here
```

### 3. Callback URL Configuration

In your Paymob Dashboard, update the callback URLs to point to your application:

1. Go to "Developers" > "Payment Integrations" > Click your integration
2. Update both Transaction Processed Callback and Transaction Response Callback URLs:
   ```
   https://your-domain.com/payment/paymob/callback
   ```

## How It Works

The payment flow works as follows:

1. Customer selects a course to purchase
2. The `PaymentController@processPaymobPayment` method is called
3. The payment is processed using the `PaymobService` with Integration ID
4. Customer is redirected to Paymob's iframe for payment
5. After payment, Paymob sends a webhook to `/payment/paymob/callback`
6. The `PaymentController@paymobCallback` method handles the webhook
7. If successful, the customer is enrolled in the course

## Testing

You can test the integration in two ways:

1. **Paymob Test Mode**: In your Paymob dashboard, enable test mode
2. **Simulation**: Use the built-in simulation route:
   ```
   /payment/test/simulate/{courseId}/paymob
   ```

## Troubleshooting

If you encounter issues:

1. Check the Laravel logs for detailed error messages
2. Verify your API Key and Integration ID are correct
3. Ensure the callback URLs are properly configured
4. Test with the simulation route first before live transactions

## Important Code Locations

- `app/Services/PaymobService.php`: The service that handles API calls to Paymob
- `app/Http/Controllers/PaymentController.php`: Contains methods for processing payments
- `config/paymob.php`: Configuration file for Paymob settings
- `routes/web.php`: Contains the routes for payment processing and callbacks

## Security Notes

- The HMAC Secret is used to verify that webhook calls come from Paymob
- Always use HTTPS for production environments
- Never expose your API Key or HMAC Secret in client-side code 