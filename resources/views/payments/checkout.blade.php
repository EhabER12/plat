@extends('layouts.app')

@section('title', 'Checkout - ' . $course->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Checkout</h4>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Payment Method</h5>
                    
                    <ul class="nav nav-tabs mb-4" id="paymentTabs" role="tablist">
                        @if(in_array('credit_card', $paymentMethods))
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="credit-card-tab" data-bs-toggle="tab" data-bs-target="#credit-card" type="button" role="tab" aria-controls="credit-card" aria-selected="true">
                                <i class="fas fa-credit-card me-2"></i> Credit Card
                            </button>
                        </li>
                        @endif
                        
                        @if(in_array('vodafone_cash', $paymentMethods))
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ !in_array('credit_card', $paymentMethods) ? 'active' : '' }}" id="vodafone-cash-tab" data-bs-toggle="tab" data-bs-target="#vodafone-cash" type="button" role="tab" aria-controls="vodafone-cash" aria-selected="false">
                                <i class="fas fa-mobile-alt me-2"></i> Vodafone Cash
                            </button>
                        </li>
                        @endif
                    </ul>
                    
                    <div class="tab-content" id="paymentTabsContent">
                        @if(in_array('credit_card', $paymentMethods))
                        <div class="tab-pane fade show active" id="credit-card" role="tabpanel" aria-labelledby="credit-card-tab">
                            <form action="{{ route('payment.process.stripe', $course->course_id) }}" method="POST" id="payment-form">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="card-element" class="form-label">Credit or debit card</label>
                                    <div id="card-element" class="form-control" style="height: 40px; padding-top: 10px;">
                                        <!-- A Stripe Element will be inserted here. -->
                                    </div>
                                    <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="save-card" name="save_card">
                                    <label class="form-check-label" for="save-card">
                                        Save card for future payments
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg w-100" id="submit-button">
                                    <i class="fas fa-lock me-2"></i> Pay ${{ number_format($course->price, 2) }}
                                </button>
                            </form>
                        </div>
                        @endif
                        
                        @if(in_array('vodafone_cash', $paymentMethods))
                        <div class="tab-pane fade {{ !in_array('credit_card', $paymentMethods) ? 'show active' : '' }}" id="vodafone-cash" role="tabpanel" aria-labelledby="vodafone-cash-tab">
                            <div class="alert alert-info mb-4">
                                <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i> How to pay with Vodafone Cash</h5>
                                <ol class="mb-0">
                                    <li>Open Vodafone Cash on your phone</li>
                                    <li>Select "Pay" from the main menu</li>
                                    <li>Enter our merchant code: <strong>123456</strong></li>
                                    <li>Enter the amount: <strong>${{ number_format($course->price, 2) }}</strong></li>
                                    <li>Confirm the payment and note down the transaction reference</li>
                                    <li>Enter your phone number and transaction reference below</li>
                                </ol>
                            </div>
                            
                            <form action="{{ route('payment.process.vodafone', $course->course_id) }}" method="POST">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Vodafone Cash Phone Number</label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" placeholder="e.g. 01012345678" value="{{ old('phone_number') }}">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="transaction_reference" class="form-label">Transaction Reference</label>
                                    <input type="text" class="form-control @error('transaction_reference') is-invalid @enderror" id="transaction_reference" name="transaction_reference" placeholder="e.g. VC123456789" value="{{ old('transaction_reference') }}">
                                    @error('transaction_reference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-check-circle me-2"></i> Verify Payment
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="img-fluid rounded me-3" style="width: 80px; height: 60px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 60px;">
                                <i class="fas fa-book fa-2x text-muted"></i>
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-1">{{ $course->title }}</h6>
                            <p class="text-muted mb-0">{{ $course->instructor->name ?? 'Unknown Instructor' }}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Original Price:</span>
                        <span>${{ number_format($course->price, 2) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount:</span>
                        <span>$0.00</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-0">
                        <strong>Total:</strong>
                        <strong>${{ number_format($course->price, 2) }}</strong>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Secure Payment</h6>
                    <p class="card-text small text-muted">
                        Your payment information is processed securely. We do not store credit card details nor have access to your credit card information.
                    </p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <i class="fab fa-cc-visa fa-2x me-2 text-primary"></i>
                            <i class="fab fa-cc-mastercard fa-2x me-2 text-danger"></i>
                            <i class="fab fa-cc-amex fa-2x text-info"></i>
                        </div>
                        <div>
                            <i class="fas fa-lock fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create a Stripe client
        // Note: This is a public key, it's safe to include in client-side code
        const stripe = Stripe('pk_test_51NxyzTLkjhGfdSaQWertYUioPAsdfGhjkL');
        
        // Create an instance of Elements
        const elements = stripe.elements();
        
        // Create a Card Element and mount it to the card-element div
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            }
        });
        
        cardElement.mount('#card-element');
        
        // Handle real-time validation errors from the card Element
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        
        // Handle form submission
        const form = document.getElementById('payment-form');
        if (form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                
                const submitButton = document.getElementById('submit-button');
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
                
                stripe.createToken(cardElement).then(function(result) {
                    if (result.error) {
                        // Inform the user if there was an error
                        const errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                        
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-lock me-2"></i> Pay ${{ number_format($course->price, 2) }}';
                    } else {
                        // Send the token to your server
                        stripeTokenHandler(result.token);
                    }
                });
            });
        }
        
        // Submit the form with the token ID
        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            const form = document.getElementById('payment-form');
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);
            
            // Submit the form
            form.submit();
        }
    });
</script>
@endsection
