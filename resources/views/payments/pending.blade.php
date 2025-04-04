@extends('layouts.app')

@section('title', 'Payment Pending')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-clock fa-5x text-warning"></i>
                    </div>
                    
                    <h2 class="mb-3">Payment Pending</h2>
                    
                    <p class="lead mb-4">
                        Your payment for <strong>{{ $payment->course->title }}</strong> is being processed.
                    </p>
                    
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading">Payment Details</h5>
                        <p class="mb-0">
                            <strong>Amount:</strong> ${{ number_format($payment->amount, 2) }}<br>
                            <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}<br>
                            <strong>Date:</strong> {{ $payment->payment_date->format('M d, Y H:i') }}<br>
                            <strong>Status:</strong> <span class="badge bg-warning">Pending</span>
                        </p>
                    </div>
                    
                    <p class="mb-4">
                        We are currently verifying your payment. This process may take up to 24 hours. 
                        You will be automatically enrolled in the course once your payment is confirmed.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.my-courses') }}" class="btn btn-primary">
                            <i class="fas fa-book-reader me-2"></i> Go to My Courses
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
