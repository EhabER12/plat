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

    <!-- Withdrawal Notifications -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-info mb-3">
                <div class="card-header bg-info text-white d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-bell me-2"></i> إشعارات السحب الأخيرة</span>
                    <a href="{{ route('instructor.earnings.withdrawals') }}" class="btn btn-sm btn-light">عرض كل السحوبات</a>
                </div>
                <div class="card-body p-2">
                    @php
                        $withdrawalNotifications = Auth::user()->withdrawals()->orderBy('requested_at', 'desc')->take(5)->get();
                    @endphp
                    @if($withdrawalNotifications->isEmpty())
                        <div class="text-muted">لا توجد طلبات سحب حديثة.</div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($withdrawalNotifications as $w)
                                <li class="list-group-item d-flex align-items-center justify-content-between {{ $w->status == 'pending' ? 'bg-warning bg-opacity-10' : '' }}">
                                    <div>
                                        <i class="fas {{ $w->status == 'completed' ? 'fa-check-circle text-success' : ($w->status == 'pending' ? 'fa-clock text-warning' : 'fa-times-circle text-danger') }} me-2"></i>
                                        <span>طلب سحب بقيمة</span>
                                        <strong>${{ number_format($w->amount,2) }}</strong>
                                        <span class="badge ms-2 {{
                                            $w->status == 'completed' ? 'badge-success' :
                                            ($w->status == 'pending' ? 'badge-warning' : 'badge-danger')
                                        }}">
                                            {{ $w->status == 'completed' ? 'تم التحويل' : ($w->status == 'pending' ? 'قيد المراجعة' : 'مرفوض') }}
                                        </span>
                                    </div>
                                    <a href="{{ route('instructor.earnings.show-withdrawal', $w->withdrawal_id) }}" class="btn btn-sm btn-outline-info">تفاصيل</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal Quick Action -->
    <div class="row mb-3">
        <div class="col-md-6 mb-2">
            <div class="card border-primary shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold text-primary mb-1">الرصيد المتاح للسحب</div>
                        <div class="h4 mb-0">${{ number_format($availableEarnings, 2) }}</div>
                        <small class="text-muted">الحد الأدنى للسحب: ${{ number_format($minWithdrawalAmount, 2) }}</small>
                    </div>
                    <form action="{{ route('instructor.earnings.create-withdrawal') }}" method="get">
                        <button type="submit" class="btn btn-lg btn-success ms-2" 
                            @if($availableEarnings < $minWithdrawalAmount || !$hasPaymentAccount) disabled @endif>
                            <i class="fas fa-money-bill-wave"></i> طلب سحب
                        </button>
                    </form>
                </div>
                @if($availableEarnings < $minWithdrawalAmount)
                    <div class="alert alert-warning mb-0 mt-2 py-2 px-3">
                        <i class="fas fa-info-circle"></i> يجب أن يكون رصيدك المتاح أكبر من أو يساوي الحد الأدنى للسحب.
                    </div>
                @elseif(!$hasPaymentAccount)
                    <div class="alert alert-warning mb-0 mt-2 py-2 px-3">
                        <i class="fas fa-info-circle"></i> يجب إضافة حساب دفع قبل طلب السحب.
                    </div>
                @endif
            </div>
        </div>
    </div>

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
