@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    
                    <h2 class="mb-3">Payment Successful!</h2>
                    
                    <p class="lead mb-4">
                        Thank you for your payment. You are now enrolled in <strong>{{ $payment->course->title }}</strong>.
                    </p>
                    
                    <div class="alert alert-success mb-4">
                        <h5 class="alert-heading">Payment Details</h5>
                        <p class="mb-0">
                            <strong>Amount:</strong> ${{ number_format($payment->amount, 2) }}<br>
                            <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}<br>
                            <strong>Date:</strong> {{ $payment->payment_date->format('M d, Y H:i') }}<br>
                            <strong>Transaction ID:</strong> {{ $payment->transaction_id }}<br>
                            <strong>Status:</strong> <span class="badge bg-success">Completed</span>
                        </p>
                    </div>
                    
                    <p class="mb-4">
                        A receipt has been sent to your email address. You can now start learning and access all course materials.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('student.course-content', $payment->course->course_id) }}" class="btn btn-primary">
                            <i class="fas fa-play-circle me-2"></i> Start Learning
                        </a>
                        <a href="{{ route('student.my-courses') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-book-reader me-2"></i> Go to My Courses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
