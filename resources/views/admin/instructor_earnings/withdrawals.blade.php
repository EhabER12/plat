@extends('layouts.admin')

@section('title', 'Manage Withdrawals')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Withdrawals</h1>
        <div>
            <a href="{{ route('admin.instructor-earnings.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Earnings
            </a>
            <a href="{{ route('admin.instructor-earnings.settings') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
                <i class="fas fa-cog fa-sm text-white-50"></i> Commission Settings
            </a>
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

    <!-- Withdrawal Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Withdrawals</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingWithdrawals }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pending Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($pendingAmount, 2) }}</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Withdrawals</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedWithdrawals }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Completed Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($completedAmount, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal Requests -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Withdrawal Requests</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-filter fa-sm fa-fw text-gray-400"></i> Filter
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Status:</div>
                    <a class="dropdown-item" href="#">All</a>
                    <a class="dropdown-item" href="#">Pending</a>
                    <a class="dropdown-item" href="#">Completed</a>
                    <a class="dropdown-item" href="#">Rejected</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($withdrawals->isEmpty())
                <div class="alert alert-info">
                    <p class="mb-0">No withdrawal requests available.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Instructor</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Requested</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawals as $withdrawal)
                                <tr class="{{ $withdrawal->status == 'pending' ? 'table-warning' : '' }}">
                                    <td>{{ $withdrawal->withdrawal_id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img class="img-profile rounded-circle mr-2" src="{{ $withdrawal->instructor->profile_image ? asset('storage/' . $withdrawal->instructor->profile_image) : asset('img/undraw_profile.svg') }}" width="40">
                                            <div>
                                                <div>{{ $withdrawal->instructor->name }}</div>
                                                <small class="text-muted">{{ $withdrawal->instructor->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${{ number_format($withdrawal->amount, 2) }}</td>
                                    <td>
                                        @if($withdrawal->payment_provider == 'vodafone_cash')
                                            <span class="badge badge-info">
                                                <i class="fas fa-mobile-alt"></i> فودافون كاش
                                            </span>
                                        @elseif($withdrawal->payment_provider == 'instapay')
                                            <span class="badge badge-info">
                                                <i class="fas fa-credit-card"></i> إنستا باي
                                            </span>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $withdrawal->provider_account_id }}</small>
                                    </td>
                                    <td>{{ $withdrawal->requested_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($withdrawal->status == 'pending')
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock"></i> Pending
                                            </span>
                                        @elseif($withdrawal->status == 'completed')
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle"></i> Completed
                                            </span>
                                        @elseif($withdrawal->status == 'rejected')
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times-circle"></i> Rejected
                                            </span>
                                        @elseif($withdrawal->status == 'cancelled')
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-ban"></i> Cancelled
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.instructor-earnings.show-withdrawal', $withdrawal->withdrawal_id) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($withdrawal->status == 'pending')
                                                <button type="button" 
                                                        class="btn btn-sm btn-success" 
                                                        data-toggle="modal" 
                                                        data-target="#approveModal{{ $withdrawal->withdrawal_id }}"
                                                        title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        data-toggle="modal" 
                                                        data-target="#rejectModal{{ $withdrawal->withdrawal_id }}"
                                                        title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
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

    <!-- Withdrawal Process Guide -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Withdrawal Process Guide</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Approval Process</h5>
                    <ol>
                        <li>Review the withdrawal request details</li>
                        <li>Verify the instructor's payment account information</li>
                        <li>Process the payment through the appropriate payment gateway</li>
                        <li>Approve the withdrawal in the system</li>
                        <li>The system will automatically update the instructor's earnings</li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h5>Rejection Guidelines</h5>
                    <ul>
                        <li>Reject withdrawals only if there are legitimate issues</li>
                        <li>Always provide a clear reason for rejection</li>
                        <li>Common reasons for rejection:
                            <ul>
                                <li>Invalid payment account details</li>
                                <li>Suspicious activity</li>
                                <li>Instructor account under review</li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
