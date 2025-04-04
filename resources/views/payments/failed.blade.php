@extends('layouts.app')

@section('title', 'Payment Failed')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-times-circle fa-5x text-danger"></i>
                    </div>
                    
                    <h2 class="mb-3">Payment Failed</h2>
                    
                    <p class="lead mb-4">
                        We're sorry, but your payment for <strong>{{ $payment->course->title }}</strong> could not be processed.
                    </p>
                    
                    <div class="alert alert-danger mb-4">
                        <h5 class="alert-heading">Payment Details</h5>
                        <p class="mb-0">
                            <strong>Amount:</strong> ${{ number_format($payment->amount, 2) }}<br>
                            <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}<br>
                            <strong>Date:</strong> {{ $payment->payment_date->format('M d, Y H:i') }}<br>
                            <strong>Status:</strong> <span class="badge bg-danger">Failed</span>
                        </p>
                    </div>
                    
                    <p class="mb-4">
                        The payment was not successful. This could be due to insufficient funds, incorrect card details, or other issues with your payment method.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('payment.checkout', $payment->course->course_id) }}" class="btn btn-primary">
                            <i class="fas fa-redo me-2"></i> Try Again
                        </a>
                        <a href="{{ route('course.detail', $payment->course->course_id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back to Course
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
