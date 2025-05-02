@extends('layouts.instructor')

@section('title', 'Earnings History')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Earnings History</h1>
        <a href="{{ route('instructor.earnings.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
        </a>
    </div>

    <!-- Earnings History -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Earnings</h6>
        </div>
        <div class="card-body">
            @if($earnings->isEmpty())
                <div class="alert alert-info">
                    <p class="mb-0">You don't have any earnings yet. Once students purchase your courses, your earnings will appear here.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Course</th>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Platform Fee</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($earnings as $earning)
                                <tr>
                                    <td>{{ $earning->created_at->format('M d, Y') }}</td>
                                    <td>{{ $earning->course->title }}</td>
                                    <td>{{ $earning->payment->student->name }}</td>
                                    <td>${{ number_format($earning->amount, 2) }}</td>
                                    <td>${{ number_format($earning->platform_fee, 2) }}</td>
                                    <td>
                                        @if($earning->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($earning->status == 'available')
                                            <span class="badge badge-success">Available</span>
                                        @elseif($earning->status == 'withdrawn')
                                            <span class="badge badge-info">Withdrawn</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($earning->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $earning->notes }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $earnings->links() }}
            @endif
        </div>
    </div>

    <!-- Earnings Explanation -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Understanding Your Earnings</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Earnings Status</h5>
                    <ul>
                        <li><span class="badge badge-warning">Pending</span> - Earnings are in the holding period and not yet available for withdrawal</li>
                        <li><span class="badge badge-success">Available</span> - Earnings are available for withdrawal</li>
                        <li><span class="badge badge-info">Withdrawn</span> - Earnings have been withdrawn</li>
                        <li><span class="badge badge-secondary">Cancelled</span> - Earnings were cancelled (e.g., due to refund)</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Revenue Sharing</h5>
                    <p>For each course sale:</p>
                    <ul>
                        <li><strong>Your earnings:</strong> 70% of the course price</li>
                        <li><strong>Platform fee:</strong> 30% of the course price</li>
                    </ul>
                    <p>The platform fee covers payment processing, hosting, marketing, and customer support.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
