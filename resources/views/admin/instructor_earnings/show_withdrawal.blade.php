@extends('layouts.admin')

@section('title', 'Withdrawal Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Withdrawal Details #{{ $withdrawal->withdrawal_id }}</h1>
        <a href="{{ route('admin.instructor-earnings.withdrawals') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Withdrawals
        </a>
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

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Withdrawal Information</h6>
                    <div>
                        @if($withdrawal->status == 'pending')
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Withdrawal Details</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th>Amount:</th>
                                    <td>${{ number_format($withdrawal->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
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
                                </tr>
                                <tr>
                                    <th>Requested:</th>
                                    <td>{{ $withdrawal->requested_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Processed:</th>
                                    <td>{{ $withdrawal->processed_at ? $withdrawal->processed_at->format('M d, Y h:i A') : 'Not yet processed' }}</td>
                                </tr>
                                @if($withdrawal->processed_by)
                                    <tr>
                                        <th>Processed By:</th>
                                        <td>{{ $withdrawal->processor->name }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Instructor Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $withdrawal->instructor->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $withdrawal->instructor->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $withdrawal->instructor->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Joined:</th>
                                    <td>{{ $withdrawal->instructor->created_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5>Payment Method</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Method:</strong> {{ ucfirst($withdrawal->payment_method) }}</p>
                                            
                                            @if($withdrawal->payment_details)
                                                @php $details = json_decode($withdrawal->payment_details, true); @endphp
                                                <p><strong>Account Name:</strong> {{ $details['account_name'] ?? 'N/A' }}</p>
                                                
                                                @if(isset($details['account_details']))
                                                    @php $accountDetails = $details['account_details']; @endphp
                                                    <p><strong>Email:</strong> {{ $accountDetails['email'] ?? 'N/A' }}</p>
                                                    <p><strong>Phone:</strong> {{ $accountDetails['phone'] ?? 'N/A' }}</p>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            @if($withdrawal->payment_details)
                                                @php $details = json_decode($withdrawal->payment_details, true); @endphp
                                                @if(isset($details['account_details']))
                                                    @php $accountDetails = $details['account_details']; @endphp
                                                    @if(isset($accountDetails['bank_name']))
                                                        <p><strong>Bank Name:</strong> {{ $accountDetails['bank_name'] }}</p>
                                                    @endif
                                                    @if(isset($accountDetails['account_number']))
                                                        <p><strong>Account Number:</strong> {{ $accountDetails['account_number'] }}</p>
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($withdrawal->notes)
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Notes</h5>
                                <p class="card-text">{{ $withdrawal->notes }}</p>
                            </div>
                        </div>
                    @endif

                    @if($withdrawal->status == 'pending')
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Pending Withdrawal</h5>
                            <p>This withdrawal request is pending your approval. Please review the details and take appropriate action.</p>
                        </div>
                    @elseif($withdrawal->status == 'completed')
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> Completed Withdrawal</h5>
                            <p>This withdrawal has been processed and completed.</p>
                        </div>
                    @elseif($withdrawal->status == 'rejected')
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-times-circle"></i> Rejected Withdrawal</h5>
                            <p>This withdrawal request was rejected.</p>
                        </div>
                    @elseif($withdrawal->status == 'cancelled')
                        <div class="alert alert-secondary">
                            <h5><i class="fas fa-ban"></i> Cancelled Withdrawal</h5>
                            <p>This withdrawal request was cancelled by the instructor.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Earnings Included in This Withdrawal</h6>
                </div>
                <div class="card-body">
                    @if($earnings->isEmpty())
                        <div class="alert alert-info">
                            <p class="mb-0">No earnings details available for this withdrawal.</p>
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
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-right">Total:</th>
                                        <th>${{ number_format($earnings->sum('amount'), 2) }}</th>
                                        <th>${{ number_format($earnings->sum('platform_fee'), 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Instructor Earnings Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Total Earnings</h5>
                        <h2 class="text-primary">${{ number_format($withdrawal->instructor->earnings()->sum('amount'), 2) }}</h2>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Available Balance</h5>
                        <h3 class="text-success">${{ number_format($withdrawal->instructor->available_earnings, 2) }}</h3>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Pending Earnings</h5>
                        <h3 class="text-warning">${{ number_format($withdrawal->instructor->pending_earnings, 2) }}</h3>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Withdrawn Earnings</h5>
                        <h3 class="text-info">${{ number_format($withdrawal->instructor->withdrawn_earnings, 2) }}</h3>
                    </div>
                    
                    <a href="{{ route('admin.instructor-earnings.instructor', $withdrawal->instructor_id) }}" class="btn btn-block btn-primary">
                        <i class="fas fa-chart-line"></i> View Full Earnings Report
                    </a>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Withdrawal Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Withdrawal Requested</h6>
                                <p class="timeline-date">{{ $withdrawal->requested_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        
                        @if($withdrawal->status == 'pending')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Awaiting Approval</h6>
                                    <p class="timeline-date">Current Status</p>
                                </div>
                            </div>
                        @elseif($withdrawal->status == 'completed')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Withdrawal Approved</h6>
                                    <p class="timeline-date">{{ $withdrawal->processed_at->format('M d, Y h:i A') }}</p>
                                    <p class="text-muted">By: {{ $withdrawal->processor->name }}</p>
                                </div>
                            </div>
                        @elseif($withdrawal->status == 'rejected')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Withdrawal Rejected</h6>
                                    <p class="timeline-date">{{ $withdrawal->processed_at->format('M d, Y h:i A') }}</p>
                                    <p class="text-muted">By: {{ $withdrawal->processor->name }}</p>
                                </div>
                            </div>
                        @elseif($withdrawal->status == 'cancelled')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-secondary"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Withdrawal Cancelled</h6>
                                    <p class="timeline-date">{{ $withdrawal->processed_at ? $withdrawal->processed_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.instructor-earnings.process-withdrawal', $withdrawal->withdrawal_id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveModalLabel">Approve Withdrawal</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to approve this withdrawal request?</p>
                        <p><strong>Amount:</strong> ${{ number_format($withdrawal->amount, 2) }}</p>
                        <p><strong>Instructor:</strong> {{ $withdrawal->instructor->name }}</p>
                        
                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve Withdrawal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('admin.instructor-earnings.process-withdrawal', $withdrawal->withdrawal_id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Reject Withdrawal</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to reject this withdrawal request?</p>
                        <p><strong>Amount:</strong> ${{ number_format($withdrawal->amount, 2) }}</p>
                        <p><strong>Instructor:</strong> {{ $withdrawal->instructor->name }}</p>
                        
                        <div class="form-group">
                            <label for="notes">Reason for Rejection (Required)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Withdrawal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        top: 5px;
    }
    .timeline-content {
        position: relative;
    }
    .timeline-title {
        margin-bottom: 5px;
    }
    .timeline-date {
        color: #6c757d;
        font-size: 0.85rem;
        margin-bottom: 0;
    }
    .timeline:before {
        content: '';
        position: absolute;
        left: -23px;
        width: 2px;
        height: 100%;
        background-color: #e3e6f0;
    }
</style>
@endsection
