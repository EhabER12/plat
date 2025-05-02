@extends('layouts.app')

@section('title', "Checkout - {$course->title}")

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Checkout</h4>
                </div>
                <div class="card-body">
                    <h5 class="card-title mb-4">Payment Details</h5>

                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i> Secure Payment with Paymob</h5>
                        <p class="mb-0">Paymob offers multiple payment methods including credit cards, mobile wallets, and more. Please fill in your billing information below to proceed to the secure payment page.</p>
                    </div>

                    <form action="{{ route('payment.process.paymob', $course->course_id) }}" method="POST" id="paymob-payment-form">
                        @csrf

                        <div class="text-center mb-4">
                            <img src="https://accept.paymobsolutions.com/images/logo.png" alt="Paymob" class="img-fluid" style="max-height: 60px;">
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name ?? '') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name ?? '') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone ?? '') }}" required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="street" class="form-label">Street Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('street') is-invalid @enderror" id="street" name="street" value="{{ old('street', $user->address ?? '') }}" required>
                            @error('street')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $user->city ?? '') }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="state" class="form-label">State/Province <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" id="state" name="state" value="{{ old('state', $user->state ?? '') }}" required>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code ?? '') }}" required>
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                <select class="form-select @error('country') is-invalid @enderror" id="country" name="country" required>
                                    <option value="EG" {{ old('country', $user->country ?? 'EG') == 'EG' ? 'selected' : '' }}>Egypt</option>
                                    <option value="SA" {{ old('country', $user->country ?? '') == 'SA' ? 'selected' : '' }}>Saudi Arabia</option>
                                    <option value="AE" {{ old('country', $user->country ?? '') == 'AE' ? 'selected' : '' }}>United Arab Emirates</option>
                                    <option value="KW" {{ old('country', $user->country ?? '') == 'KW' ? 'selected' : '' }}>Kuwait</option>
                                    <option value="QA" {{ old('country', $user->country ?? '') == 'QA' ? 'selected' : '' }}>Qatar</option>
                                    <option value="BH" {{ old('country', $user->country ?? '') == 'BH' ? 'selected' : '' }}>Bahrain</option>
                                    <option value="OM" {{ old('country', $user->country ?? '') == 'OM' ? 'selected' : '' }}>Oman</option>
                                    <option value="JO" {{ old('country', $user->country ?? '') == 'JO' ? 'selected' : '' }}>Jordan</option>
                                    <option value="LB" {{ old('country', $user->country ?? '') == 'LB' ? 'selected' : '' }}>Lebanon</option>
                                </select>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <input type="hidden" name="payment_method" value="paymob">

                        <div class="mb-4">
                            <h6>Available Payment Methods:</h6>
                            <div class="row mt-3">
                                <div class="col-4 text-center">
                                    <div class="payment-method-icon">
                                        <i class="fas fa-credit-card fa-2x text-primary"></i>
                                        <p class="mt-2">Credit Card</p>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="payment-method-icon">
                                        <i class="fas fa-mobile-alt fa-2x text-success"></i>
                                        <p class="mt-2">Mobile Wallet</p>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="payment-method-icon">
                                        <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                                        <p class="mt-2">Other Methods</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock me-2"></i> Pay ${{ number_format($course->price, 2) }} Securely
                            </button>
                            <p class="small text-muted text-center mt-2">You will be redirected to Paymob's secure payment page where you can choose your preferred payment method.</p>
                        </div>
                    </form>
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

<style>
    .payment-method-icon {
        padding: 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .payment-method-icon:hover {
        background-color: #e9ecef;
        transform: translateY(-5px);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Payment form page loaded');
        const form = document.getElementById('paymob-payment-form');

        if (!form) {
            console.error('Payment form not found!');
            return;
        }

        console.log('Payment form found:', form);

        // Debug CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const csrfInput = form.querySelector('input[name="_token"]');
        console.log('CSRF meta tag:', csrfToken);
        console.log('CSRF form input:', csrfInput?.value);

        // Debug form action
        console.log('Form action:', form.action);
        console.log('Form method:', form.method);

        form.addEventListener('submit', function(e) {
            // Prevent default form submission for debugging
            e.preventDefault();

            console.log('Form submission started');

            // Add a loading indicator
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing Payment...';

            // Log the form data
            const formData = new FormData(form);
            const formDataObj = {};
            formData.forEach((value, key) => {
                formDataObj[key] = value;
            });
            console.log('Form data:', formDataObj);

            // Manual form submission with fetch
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfInput.value,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(data => {
                console.log('Response data:', data);
                try {
                    // Try to parse as JSON
                    const jsonData = JSON.parse(data);
                    console.log('Parsed JSON:', jsonData);

                    // If we have an iframe URL, redirect to it
                    if (jsonData.iframe_url) {
                        window.location.href = jsonData.iframe_url;
                    } else if (jsonData.redirect) {
                        window.location.href = jsonData.redirect;
                    } else if (jsonData.success === false) {
                        // Handle error response
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                        alert(jsonData.message || 'Payment processing failed. Please try again.');

                        // If it's a simulation, we can redirect to a simulated payment page
                        if (jsonData.simulation) {
                            // Submit the form normally to use the simulation mode
                            form.submit();
                        }
                    }
                } catch (e) {
                    console.log('Not JSON data, might be HTML or redirect');
                    // If it's HTML with a redirect, the browser will handle it
                    document.open();
                    document.write(data);
                    document.close();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                alert('Payment processing failed. Please try again.');
            });
        });
    });
</script>
@endsection


