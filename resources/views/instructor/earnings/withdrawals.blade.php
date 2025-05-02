@extends('layouts.instructor')

@section('title', 'Withdrawal History')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Withdrawal History</h1>
        <div>
            <a href="{{ route('instructor.earnings.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
            </a>
            @if($availableEarnings >= $minWithdrawalAmount)
                <a href="{{ route('instructor.earnings.create-withdrawal') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-money-bill-wave fa-sm text-white-50"></i> Request Withdrawal
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Withdrawal History -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Withdrawal Requests</h6>
        </div>
        <div class="card-body">
            @if($withdrawals->isEmpty())
                <div class="alert alert-info">
                    <p class="mb-0">You haven't made any withdrawal requests yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Request Date</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Processed Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawals as $withdrawal)
                                <tr>
                                    <td>{{ $withdrawal->requested_at->format('M d, Y') }}</td>
                                    <td>${{ number_format($withdrawal->amount, 2) }}</td>
                                    <td>{{ ucfirst($withdrawal->payment_method) }}</td>
                                    <td>
                                        @if($withdrawal->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($withdrawal->status == 'completed')
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($withdrawal->status == 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @elseif($withdrawal->status == 'cancelled')
                                            <span class="badge badge-secondary">Cancelled</span>
                                        @else
                                            <span class="badge badge-info">{{ ucfirst($withdrawal->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $withdrawal->processed_at ? $withdrawal->processed_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('instructor.earnings.show-withdrawal', $withdrawal->withdrawal_id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        
                                        @if($withdrawal->status == 'pending')
                                            <form action="{{ route('instructor.earnings.cancel-withdrawal', $withdrawal->withdrawal_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this withdrawal request?')">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $withdrawals->links() }}
            @endif
        </div>
    </div>

    <!-- Withdrawal Information -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Withdrawal Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Withdrawal Requirements</h5>
                    <ul>
                        <li>Minimum withdrawal amount: ${{ number_format($minWithdrawalAmount, 2) }}</li>
                        <li>You must have a verified payment account</li>
                        <li>Earnings are available for withdrawal after a 14-day holding period</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Withdrawal Process</h5>
                    <ol>
                        <li>Request a withdrawal from your available balance</li>
                        <li>Our team will process your request within 3 business days</li>
                        <li>Funds will be transferred to your payment account</li>
                        <li>You can cancel a pending withdrawal request at any time</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
