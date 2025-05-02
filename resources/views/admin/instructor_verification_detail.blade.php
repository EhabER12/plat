@extends('admin.layout')

@section('title', 'Review Instructor Verification')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="mb-3">Review Instructor Verification</h4>
        <a href="{{ route('admin.instructor.verifications') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Verification Requests
        </a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4 mb-md-0">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Instructor Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($verification->user && $verification->user->profile_image)
                            <img src="{{ asset($verification->user->profile_image) }}"
                                 alt="{{ $verification->user->name }}"
                                 class="rounded-circle img-thumbnail" style="width: 120px; height: 120px;">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto"
                                 style="width: 120px; height: 120px;">
                                <i class="fas fa-user fa-4x text-secondary"></i>
                            </div>
                        @endif
                    </div>

                    <h5 class="text-center mb-3">{{ $verification->user ? $verification->user->name : 'User Deleted' }}</h5>

                    <div class="mb-3">
                        <p class="mb-1 small text-muted">Email</p>
                        <p class="mb-0">{{ $verification->user ? $verification->user->email : 'N/A' }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1 small text-muted">Phone</p>
                        <p class="mb-0">{{ $verification->user && $verification->user->phone ? $verification->user->phone : 'Not provided' }}</p>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1 small text-muted">Joined</p>
                        <p class="mb-0">{{ $verification->user && $verification->user->created_at ? $verification->user->created_at->format('M d, Y') : 'Not available' }}</p>
                    </div>

                    @if($verification->user && $verification->user->bio)
                        <div class="mb-0">
                            <p class="mb-1 small text-muted">Bio</p>
                            <p class="mb-0">{{ $verification->user->bio }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Verification Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-1 small text-muted">Status</p>
                        <p class="mb-0">
                            <span class="badge bg-warning">Pending Review</span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <p class="mb-1 small text-muted">Submitted</p>
                        <p class="mb-0">{{ $verification->submitted_at ? $verification->submitted_at->format('M d, Y g:i A') : 'Not available' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Verification Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1 small text-muted">Educational Background</p>
                            <p class="mb-3">{{ $verification->education }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 small text-muted">Area of Expertise</p>
                            <p class="mb-3">{{ $verification->expertise }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1 small text-muted">Years of Experience</p>
                            <p class="mb-3">{{ $verification->years_of_experience }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 small text-muted">LinkedIn Profile</p>
                            <p class="mb-3">
                                @if($verification->linkedin_profile)
                                    <a href="{{ $verification->linkedin_profile }}" target="_blank">
                                        {{ $verification->linkedin_profile }}
                                        <i class="fas fa-external-link-alt ms-1 small"></i>
                                    </a>
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="mb-1 small text-muted">Additional Information</p>
                        <p class="mb-0">{{ $verification->additional_info ?? 'No additional information provided.' }}</p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2 small text-muted">Certificate</p>
                            @if($verification->certificate_file)
                                <a href="{{ asset('storage/' . $verification->certificate_file) }}"
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-alt me-2"></i> View Certificate
                                </a>
                            @else
                                <p>No certificate uploaded</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2 small text-muted">Resume/CV</p>
                            @if($verification->cv_file)
                                <a href="{{ asset('storage/' . $verification->cv_file) }}"
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-file-alt me-2"></i> View CV
                                </a>
                            @else
                                <p>No CV uploaded</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Review Decision</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.instructor.verification.process', $verification->id) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">Verify this instructor?</label>
                            <div class="d-flex">
                                <div class="form-check me-4">
                                    <input class="form-check-input" type="radio" name="status" id="actionApprove" value="approved" checked>
                                    <label class="form-check-label" for="actionApprove">
                                        Approve
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="actionReject" value="rejected">
                                    <label class="form-check-label" for="actionReject">
                                        Reject
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 rejection-reason" style="display: none;">
                            <label for="admin_notes" class="form-label">Rejection Reason</label>
                            <textarea class="form-control @error('admin_notes') is-invalid @enderror"
                                      id="admin_notes" name="admin_notes" rows="4">{{ old('admin_notes') }}</textarea>
                            <small class="text-muted">Provide clear feedback to the instructor about why their verification was rejected and what they can do to improve.</small>
                            @error('admin_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.instructor.verifications') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Submit Decision
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const actionReject = document.getElementById('actionReject');
        const actionApprove = document.getElementById('actionApprove');
        const rejectionReasonDiv = document.querySelector('.rejection-reason');
        const rejectionReasonField = document.getElementById('admin_notes');

        // Toggle rejection reason textarea based on selected action
        actionReject.addEventListener('change', function() {
            if (this.checked) {
                rejectionReasonDiv.style.display = 'block';
                rejectionReasonField.setAttribute('required', 'required');
            }
        });

        actionApprove.addEventListener('change', function() {
            if (this.checked) {
                rejectionReasonDiv.style.display = 'none';
                rejectionReasonField.removeAttribute('required');
            }
        });
    });
</script>
@endsection