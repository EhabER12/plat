@extends('layouts.instructor')

@section('title', 'Verification Pending')
@section('page-title', 'Verification Pending')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <div class="display-1 text-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    
                    <h2 class="mb-3">Your Instructor Verification is Pending</h2>
                    
                    <p class="mb-4 text-muted">
                        Thank you for submitting your verification information. Our team is currently reviewing your credentials.
                        This process typically takes 1-3 business days. You'll receive an email notification once your account is approved.
                    </p>
                    
                    <div class="card mb-4 bg-light">
                        <div class="card-body">
                            <h5 class="mb-3">Submission Details</h5>
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> <span class="badge bg-warning">Pending Review</span></p>
                                    <p><strong>Education:</strong> {{ $verification->education }}</p>
                                    <p><strong>Expertise:</strong> {{ $verification->expertise }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Submitted:</strong> {{ $verification->submitted_at->format('M d, Y') }}</p>
                                    <p><strong>Experience:</strong> {{ $verification->years_of_experience }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('instructor.verification.form') }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i> Update Information
                    </a>
                    
                    <div class="mt-5">
                        <h5 class="mb-3">What's Next?</h5>
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3 text-primary">
                                            <i class="fas fa-check-circle fa-3x"></i>
                                        </div>
                                        <h6>Get Approved</h6>
                                        <p class="small text-muted mb-0">Once approved, you'll get full access to the instructor dashboard</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3 text-success">
                                            <i class="fas fa-book fa-3x"></i>
                                        </div>
                                        <h6>Create Courses</h6>
                                        <p class="small text-muted mb-0">Start building your first course to share your knowledge</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="mb-3 text-info">
                                            <i class="fas fa-users fa-3x"></i>
                                        </div>
                                        <h6>Reach Students</h6>
                                        <p class="small text-muted mb-0">Teach and inspire students from around the world</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 