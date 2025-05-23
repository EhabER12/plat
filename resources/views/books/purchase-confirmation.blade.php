@extends('layouts.app')

@section('title', 'Payment Confirmation')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4 text-center">
                    @if($status === 'completed')
                        <div class="mb-4">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-check-circle fa-3x"></i>
                            </div>
                            <h2 class="mt-3 mb-2">Payment Successful!</h2>
                            <p class="text-muted mb-4">Your payment has been processed successfully.</p>
                        </div>
                        
                        <div class="alert alert-success mb-4">
                            <h5 class="alert-heading">You now have access to this book!</h5>
                            <p>You can now read and download the book from your account.</p>
                        </div>
                    @elseif($status === 'pending')
                        <div class="mb-4">
                            <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-clock fa-3x"></i>
                            </div>
                            <h2 class="mt-3 mb-2">Payment Processing</h2>
                            <p class="text-muted mb-4">Your payment is being processed. This may take a few moments.</p>
                        </div>
                        
                        <div class="alert alert-warning mb-4">
                            <h5 class="alert-heading">Please wait while we process your payment</h5>
                            <p>We're currently processing your payment. This usually takes a few seconds, but may take longer in some cases.</p>
                            <p class="mb-0">You'll receive access to the book as soon as the payment is confirmed.</p>
                        </div>
                        
                        <div class="text-center mb-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Checking payment status...</p>
                        </div>
                        
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary" id="refresh-status">
                                <i class="fas fa-sync-alt me-2"></i> Refresh Status
                            </button>
                        </div>
                    @else
                        <div class="mb-4">
                            <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-times-circle fa-3x"></i>
                            </div>
                            <h2 class="mt-3 mb-2">Payment Failed</h2>
                            <p class="text-muted mb-4">We couldn't process your payment at this time.</p>
                        </div>
                        
                        <div class="alert alert-danger mb-4">
                            <h5 class="alert-heading">There was a problem with your payment</h5>
                            <p>Your payment could not be processed. This could be due to:</p>
                            <ul class="mb-0 text-start">
                                <li>Insufficient funds in your account</li>
                                <li>Card declined by your bank</li>
                                <li>Technical issues with the payment gateway</li>
                            </ul>
                        </div>
                    @endif
                    
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 text-md-end fw-bold">Book:</div>
                                <div class="col-md-8 text-md-start">{{ $book->title }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 text-md-end fw-bold">Price:</div>
                                <div class="col-md-8 text-md-start">${{ number_format($book->price, 2) }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 text-md-end fw-bold">Status:</div>
                                <div class="col-md-8 text-md-start">
                                    @if($status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </div>
                            </div>
                            @if($transaction)
                                <div class="row mb-3">
                                    <div class="col-md-4 text-md-end fw-bold">Transaction ID:</div>
                                    <div class="col-md-8 text-md-start">{{ $transaction->transaction_id }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 text-md-end fw-bold">Date:</div>
                                    <div class="col-md-8 text-md-start">{{ $transaction->created_at->format('F j, Y, g:i a') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        @if($status === 'completed')
                            <a href="{{ route('books.show', $book) }}" class="btn btn-primary">
                                <i class="fas fa-book me-2"></i> Go to Book
                            </a>
                        @elseif($status === 'pending')
                            <a href="{{ route('books.show', $book) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Book
                            </a>
                        @else
                            <a href="{{ route('books.checkout', $book) }}" class="btn btn-primary">
                                <i class="fas fa-redo me-2"></i> Try Again
                            </a>
                            <a href="{{ route('books.show', $book) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Book
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <p class="text-muted">
                    If you have any questions or concerns, please <a href="{{ route('contact') }}">contact our support team</a>.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($status === 'pending')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-refresh the page every 5 seconds to check payment status
        const refreshInterval = setInterval(function() {
            location.reload();
        }, 5000);
        
        // Clear interval when user leaves the page
        window.addEventListener('beforeunload', function() {
            clearInterval(refreshInterval);
        });
        
        // Manual refresh button
        document.getElementById('refresh-status').addEventListener('click', function() {
            location.reload();
        });
    });
</script>
@endif
@endpush
