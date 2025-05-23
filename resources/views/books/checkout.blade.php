@extends('layouts.app')

@section('title', 'Checkout - ' . $book->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('books.index') }}">Books</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                </ol>
            </nav>

            <div class="card shadow-sm border-0 mb-5">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0">Complete Your Purchase</h4>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="window.location.reload()">
                                <i class="fas fa-sync-alt me-1"></i> Refresh Page
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Error message that might be displayed by JavaScript -->
                    <div class="alert alert-danger mb-4" id="payment-error-alert" style="display: none;">
                        <i class="fas fa-exclamation-circle me-2"></i> <span id="payment-error-message">An error occurred while processing your payment. Please try again later.</span>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="window.location.reload()">
                                <i class="fas fa-sync-alt me-1"></i> Refresh Page
                            </button>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <img src="{{ $book->getCoverImageUrlAttribute() }}" alt="{{ $book->title }}" class="img-fluid rounded shadow-sm">
                        </div>
                        <div class="col-md-9">
                            <h5 class="mb-2">{{ $book->title }}</h5>
                            <p class="text-muted mb-1">
                                <i class="fas fa-user me-1"></i> {{ $book->author ?? 'Unknown Author' }}
                            </p>
                            <p class="text-muted mb-3">
                                <i class="fas fa-language me-1"></i> {{ $book->language }}
                                @if($book->pages)
                                    <span class="mx-1">•</span>
                                    <i class="fas fa-file-alt me-1"></i> {{ $book->pages }} pages
                                @endif
                            </p>
                            <div class="d-flex align-items-center">
                                <span class="h4 text-primary mb-0">${{ number_format($book->price, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="alert alert-info">
                        <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i> Payment Instructions</h5>
                        <p>When you click "Complete Purchase", you'll be redirected to our secure payment page where you can complete your transaction safely.</p>
                        <p class="mb-0">After successful payment, you'll gain immediate access to download and read the book.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="mb-3">Payment Method</h5>

                            <div class="payment-methods">
                                <div class="card mb-3 payment-method-card active" id="paymob-card">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" id="paymob" value="paymob" checked>
                                            <label class="form-check-label d-flex align-items-center" for="paymob">
                                                <span class="me-2">Credit/Debit Card</span>
                                                <img src="{{ asset('images/payment/visa.png') }}" alt="Visa" height="25" class="me-1">
                                                <img src="{{ asset('images/payment/mastercard.png') }}" alt="Mastercard" height="25">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Price:</span>
                                    <span>${{ number_format($book->price, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="fw-bold">Total:</span>
                                    <span class="fw-bold">${{ number_format($book->price, 2) }}</span>
                                </div>

                                <form action="{{ route('books.purchase.paymob', $book) }}" method="POST" id="payment-form">
                                    @csrf
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-lock me-2"></i> Complete Purchase
                                        </button>

                                        @if(config('app.debug'))
                                        <button type="button" class="btn btn-info btn-lg" id="direct-iframe-btn">
                                            <i class="fas fa-external-link-alt me-2"></i> Open Payment Page Directly
                                        </button>
                                        @endif

                                        <button type="button" class="btn btn-outline-primary mb-2" onclick="window.location.reload()">
                                            <i class="fas fa-sync-alt me-2"></i> Refresh Payment Page
                                        </button>

                                        <a href="{{ route('books.show', $book) }}" class="btn btn-outline-secondary mb-2">
                                            <i class="fas fa-arrow-left me-2"></i> Back to Book Details
                                        </a>
                                    </div>
                                </form>

                                @if(config('app.debug'))
                                <div class="mt-3 p-3 bg-light rounded">
                                    <h6 class="text-muted">Debug Information</h6>
                                    <p class="small mb-1">Integration ID: {{ config('paymob.integration_id') }}</p>
                                    <p class="small mb-1">Iframe ID: {{ config('paymob.iframe_id') }}</p>
                                    <p class="small mb-0">API Key Set: {{ !empty(config('paymob.api_key')) ? 'Yes' : 'No' }}</p>

                                    <div class="mt-3">
                                        <button type="button" class="btn btn-sm btn-info" id="test-iframe-btn">
                                            Test Iframe URL
                                        </button>
                                    </div>

                                    <div id="iframe-test-result" class="mt-2" style="display: none;">
                                        <div class="alert alert-info">
                                            <p class="mb-1"><strong>Test Result:</strong> <span id="iframe-test-message"></span></p>
                                            <p class="mb-0 small"><span id="iframe-test-url"></span></p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="mb-3">What You'll Get</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Immediate access to the full book in PDF format</span>
                        </li>
                        <li class="list-group-item px-0 d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Ability to read online or download for offline reading</span>
                        </li>
                        <li class="list-group-item px-0 d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-3"></i>
                            <span>Lifetime access to the book content</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .payment-method-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid #dee2e6;
    }

    .payment-method-card:hover {
        border-color: #adb5bd;
    }

    .payment-method-card.active {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentCards = document.querySelectorAll('.payment-method-card');
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');

        paymentCards.forEach(card => {
            card.addEventListener('click', function() {
                // Find the radio input inside this card
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;

                    // Remove active class from all cards
                    paymentCards.forEach(c => c.classList.remove('active'));

                    // Add active class to the clicked card
                    this.classList.add('active');
                }
            });
        });

        // Direct iframe button functionality
        const directIframeBtn = document.getElementById('direct-iframe-btn');
        if (directIframeBtn) {
            directIframeBtn.addEventListener('click', function() {
                // Show loading state
                directIframeBtn.disabled = true;
                directIframeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Opening...';

                // Submit the form with a test flag
                const form = document.getElementById('payment-form');
                const testInput = document.createElement('input');
                testInput.type = 'hidden';
                testInput.name = 'test_mode';
                testInput.value = '1';
                form.appendChild(testInput);

                // Use fetch to submit the form
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.iframe_url) {
                        // Open the iframe URL in a new tab
                        window.open(data.iframe_url, '_blank');
                    } else {
                        // Show error in the alert div instead of using alert()
                        const errorAlert = document.getElementById('payment-error-alert');
                        const errorMessage = document.getElementById('payment-error-message');
                        errorMessage.textContent = 'Error: ' + (data.message || 'Failed to generate payment page');
                        errorAlert.style.display = 'block';

                        // Scroll to the error message
                        errorAlert.scrollIntoView({ behavior: 'smooth' });
                    }
                })
                .catch(error => {
                    // Show error in the alert div instead of using alert()
                    const errorAlert = document.getElementById('payment-error-alert');
                    const errorMessage = document.getElementById('payment-error-message');
                    errorMessage.textContent = 'Error: ' + error.message;
                    errorAlert.style.display = 'block';

                    // Scroll to the error message
                    errorAlert.scrollIntoView({ behavior: 'smooth' });
                })
                .finally(() => {
                    // Reset button state
                    directIframeBtn.disabled = false;
                    directIframeBtn.innerHTML = '<i class="fas fa-external-link-alt me-2"></i> Open Payment Page Directly';

                    // Remove the test input
                    form.removeChild(testInput);
                });
            });
        }

        // Test iframe URL functionality
        const testIframeBtn = document.getElementById('test-iframe-btn');
        if (testIframeBtn) {
            testIframeBtn.addEventListener('click', function() {
                const resultDiv = document.getElementById('iframe-test-result');
                const messageSpan = document.getElementById('iframe-test-message');
                const urlSpan = document.getElementById('iframe-test-url');

                // Show loading state
                testIframeBtn.disabled = true;
                testIframeBtn.innerHTML = 'Testing...';
                resultDiv.style.display = 'block';
                messageSpan.textContent = 'Testing connection to Paymob...';

                // Submit the form with a test flag
                const form = document.getElementById('payment-form');
                const testInput = document.createElement('input');
                testInput.type = 'hidden';
                testInput.name = 'test_mode';
                testInput.value = '1';
                form.appendChild(testInput);

                // Use fetch to submit the form
                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageSpan.textContent = 'Successfully generated iframe URL!';
                        urlSpan.textContent = data.iframe_url;

                        // Add a direct link to test
                        const linkElem = document.createElement('a');
                        linkElem.href = data.iframe_url;
                        linkElem.target = '_blank';
                        linkElem.className = 'btn btn-sm btn-success mt-2';
                        linkElem.textContent = 'Open Iframe in New Tab';
                        resultDiv.querySelector('.alert').appendChild(linkElem);
                    } else {
                        messageSpan.textContent = 'Error: ' + (data.message || 'Unknown error');

                        // Also show in the main error alert
                        const errorAlert = document.getElementById('payment-error-alert');
                        const errorMessage = document.getElementById('payment-error-message');
                        errorMessage.textContent = 'Error: ' + (data.message || 'Unknown error');
                        errorAlert.style.display = 'block';
                    }
                })
                .catch(error => {
                    messageSpan.textContent = 'Error: ' + error.message;

                    // Also show in the main error alert
                    const errorAlert = document.getElementById('payment-error-alert');
                    const errorMessage = document.getElementById('payment-error-message');
                    errorMessage.textContent = 'Error: ' + error.message;
                    errorAlert.style.display = 'block';
                })
                .finally(() => {
                    // Reset button state
                    testIframeBtn.disabled = false;
                    testIframeBtn.innerHTML = 'Test Iframe URL';

                    // Remove the test input
                    form.removeChild(testInput);
                });
            });
        }
    });
</script>
@endpush
