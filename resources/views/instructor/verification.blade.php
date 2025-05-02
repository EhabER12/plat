@extends('layouts.instructor')

@section('title', 'Instructor Verification')
@section('page-title', 'Instructor Verification')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Instructor Verification</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Verification Required</h5>
                                <p class="mb-0">
                                    To ensure the quality of our learning platform, all instructors must go through a verification process.
                                    Please provide your educational background, professional expertise, and relevant certificates.
                                    Our team will review your information and approve your instructor account within 1-3 business days.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('instructor.verification.submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="education" class="form-label">Educational Background <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('education') is-invalid @enderror"
                                           id="education" name="education"
                                           value="{{ old('education', $verification->education ?? '') }}" required>
                                    <small class="text-muted">Example: Master's Degree in Computer Science, Harvard University</small>
                                    @error('education')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expertise" class="form-label">Area of Expertise <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('expertise') is-invalid @enderror"
                                           id="expertise" name="expertise"
                                           value="{{ old('expertise', $verification->expertise ?? '') }}" required>
                                    <small class="text-muted">Example: Web Development, Data Science, Digital Marketing</small>
                                    @error('expertise')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="years_of_experience" class="form-label">Years of Experience <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('years_of_experience') is-invalid @enderror"
                                           id="years_of_experience" name="years_of_experience"
                                           value="{{ old('years_of_experience', $verification->years_of_experience ?? '') }}" required>
                                    <small class="text-muted">Example: 5+ years in Software Development</small>
                                    @error('years_of_experience')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="linkedin_profile" class="form-label">LinkedIn Profile</label>
                                    <input type="url" class="form-control @error('linkedin_profile') is-invalid @enderror"
                                           id="linkedin_profile" name="linkedin_profile"
                                           value="{{ old('linkedin_profile', $verification->linkedin_profile ?? '') }}">
                                    <small class="text-muted">Example: https://www.linkedin.com/in/yourprofile</small>
                                    @error('linkedin_profile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="certificate_file" class="form-label">Relevant Certificates (PDF, JPG, PNG)</label>
                                    @if($verification && $verification->certificate_file)
                                        <div class="mb-2">
                                            <a href="{{ asset('storage/' . $verification->certificate_file) }}"
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-alt me-2"></i> View Current Certificate
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('certificate_file') is-invalid @enderror"
                                           id="certificate_file" name="certificate_file">
                                    <small class="text-muted">Upload any teaching certifications or relevant credentials (max 5MB)</small>
                                    @error('certificate_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cv_file" class="form-label">Resume/CV (PDF, DOC, DOCX)</label>
                                    @if($verification && $verification->cv_file)
                                        <div class="mb-2">
                                            <a href="{{ asset('storage/' . $verification->cv_file) }}"
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-alt me-2"></i> View Current CV
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('cv_file') is-invalid @enderror"
                                           id="cv_file" name="cv_file">
                                    <small class="text-muted">Upload your latest resume or CV (max 5MB)</small>
                                    @error('cv_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Payment Account Information -->
                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Payment Account Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Please provide your Paymob account details to receive payments from your courses. This information is required to process your earnings.
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="payment_email" class="form-label">Paymob Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('payment_email') is-invalid @enderror"
                                               id="payment_email" name="payment_email"
                                               value="{{ old('payment_email', $verification->payment_details['email'] ?? '') }}" required>
                                        <small class="text-muted">Email associated with your Paymob account</small>
                                        @error('payment_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="payment_phone" class="form-label">Paymob Phone <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('payment_phone') is-invalid @enderror"
                                               id="payment_phone" name="payment_phone"
                                               value="{{ old('payment_phone', $verification->payment_details['phone'] ?? '') }}" required>
                                        <small class="text-muted">Phone number associated with your Paymob account</small>
                                        @error('payment_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="payment_bank_name" class="form-label">Bank Name (Optional)</label>
                                        <input type="text" class="form-control @error('payment_bank_name') is-invalid @enderror"
                                               id="payment_bank_name" name="payment_bank_name"
                                               value="{{ old('payment_bank_name', $verification->payment_details['bank_name'] ?? '') }}">
                                        <small class="text-muted">Your bank name if applicable</small>
                                        @error('payment_bank_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="payment_account_number" class="form-label">Bank Account Number (Optional)</label>
                                        <input type="text" class="form-control @error('payment_account_number') is-invalid @enderror"
                                               id="payment_account_number" name="payment_account_number"
                                               value="{{ old('payment_account_number', $verification->payment_details['account_number'] ?? '') }}">
                                        <small class="text-muted">Your bank account number if applicable</small>
                                        @error('payment_account_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="payment_terms" name="payment_terms" required>
                                    <label class="form-check-label" for="payment_terms">
                                        I confirm that the payment information provided is correct and I authorize the platform to process payments to this account.
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="additional_info" class="form-label">Additional Information</label>
                            <textarea class="form-control @error('additional_info') is-invalid @enderror"
                                      id="additional_info" name="additional_info" rows="4">{{ old('additional_info', $verification->additional_info ?? '') }}</textarea>
                            <small class="text-muted">Share any additional information that might help us evaluate your application.</small>
                            @error('additional_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($verification && $verification->status === 'rejected')
                            <div class="alert alert-danger mb-4">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="fas fa-exclamation-circle fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="alert-heading">Previous Application Rejected</h5>
                                        <p class="mb-0">
                                            <strong>Reason:</strong> {{ $verification->rejection_reason }}
                                        </p>
                                        <p class="mt-2 mb-0">
                                            Please update your information addressing the feedback above and resubmit your application.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i> Submit for Verification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection