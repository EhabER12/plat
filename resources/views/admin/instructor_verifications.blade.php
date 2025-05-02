@extends('admin.layout')

@section('title', 'Instructor Verification Requests')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Instructor Verification Requests</h4>
        <p class="text-muted">Review and approve instructor applications</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if(count($pendingVerifications) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Instructor</th>
                                <th>Education</th>
                                <th>Expertise</th>
                                <th>Experience</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingVerifications as $verification)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-3">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $verification->user ? $verification->user->name : 'User Deleted' }}</h6>
                                                <small>{{ $verification->user ? $verification->user->email : 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $verification->education }}</td>
                                    <td>{{ $verification->expertise }}</td>
                                    <td>{{ $verification->years_of_experience }}</td>
                                    <td>{{ $verification->submitted_at ? $verification->submitted_at->format('M d, Y') : 'Not available' }}</td>
                                    <td>
                                        <a href="{{ route('admin.instructor.verification.show', $verification->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $pendingVerifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-clipboard-check fa-4x text-muted"></i>
                    </div>
                    <h4>No Pending Verification Requests</h4>
                    <p class="text-muted">All instructor verification requests have been processed.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection