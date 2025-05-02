@extends('layouts.instructor')

@section('title', 'Earnings Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Earnings Dashboard</h1>
        
        @if($availableEarnings >= $minWithdrawalAmount && $hasPaymentAccount)
            <a href="{{ route('instructor.earnings.create-withdrawal') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-money-bill-wave fa-sm text-white-50"></i> Request Withdrawal
            </a>
        @endif
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

    @if(session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    @if(!$hasPaymentAccount)
        <div class="alert alert-warning">
            <h5><i class="fas fa-exclamation-triangle"></i> Payment Account Required</h5>
            <p>You need to add a payment account before you can withdraw your earnings.</p>
            <a href="{{ route('instructor.payment-accounts.create') }}" class="btn btn-warning btn-sm">Add Payment Account</a>
        </div>
    @endif

    <!-- Earnings Overview -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Earnings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalEarnings, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Available for Withdrawal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($availableEarnings, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pending Earnings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($pendingEarnings, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Withdrawn</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($withdrawnEarnings, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Earnings -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Recent Earnings</h6>
            <a href="{{ route('instructor.earnings.history') }}" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body">
            @if($recentEarnings->isEmpty())
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
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentEarnings as $earning)
                                <tr>
                                    <td>{{ $earning->created_at->format('M d, Y') }}</td>
                                    <td>{{ $earning->course->title }}</td>
                                    <td>${{ number_format($earning->amount, 2) }}</td>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $recentEarnings->links() }}
            @endif
        </div>
    </div>

    <!-- Withdrawal Information -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Withdrawal Information</h6>
            <a href="{{ route('instructor.earnings.withdrawals') }}" class="btn btn-sm btn-primary">View Withdrawals</a>
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
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
