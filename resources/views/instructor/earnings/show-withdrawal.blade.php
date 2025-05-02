@extends('layouts.instructor')

@section('title', 'Withdrawal Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Withdrawal Details</h1>
        <a href="{{ route('instructor.earnings.withdrawals') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Withdrawal #{{ $withdrawal->withdrawal_id }}</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Withdrawal Information</h5>
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
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Payment Method</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th>Method:</th>
                                    <td>{{ ucfirst($withdrawal->payment_method) }}</td>
                                </tr>
                                @if($withdrawal->payment_details)
                                    @php $details = json_decode($withdrawal->payment_details, true); @endphp
                                    <tr>
                                        <th>Account:</th>
                                        <td>{{ $details['account_name'] ?? 'N/A' }}</td>
                                    </tr>
                                @endif
                            </table>
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
                            <h5><i class="fas fa-clock"></i> Pending Withdrawal</h5>
                            <p>Your withdrawal request is being processed. This typically takes 3 business days.</p>
                            <form action="{{ route('instructor.earnings.cancel-withdrawal', $withdrawal->withdrawal_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this withdrawal request?')">
                                    <i class="fas fa-times"></i> Cancel Withdrawal
                                </button>
                            </form>
                        </div>
                    @elseif($withdrawal->status == 'completed')
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> Completed Withdrawal</h5>
                            <p>Your withdrawal has been processed and the funds have been transferred to your payment account.</p>
                        </div>
                    @elseif($withdrawal->status == 'rejected')
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-circle"></i> Rejected Withdrawal</h5>
                            <p>Your withdrawal request was rejected. Please check the notes for more information or contact support.</p>
                        </div>
                    @elseif($withdrawal->status == 'cancelled')
                        <div class="alert alert-secondary">
                            <h5><i class="fas fa-ban"></i> Cancelled Withdrawal</h5>
                            <p>This withdrawal request was cancelled.</p>
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
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($earnings as $earning)
                                        <tr>
                                            <td>{{ $earning->created_at->format('M d, Y') }}</td>
                                            <td>{{ $earning->course->title }}</td>
                                            <td>${{ number_format($earning->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-right">Total:</th>
                                        <th>${{ number_format($earnings->sum('amount'), 2) }}</th>
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
                                    <h6 class="timeline-title">Processing</h6>
                                    <p class="timeline-date">In progress</p>
                                </div>
                            </div>
                        @elseif($withdrawal->status == 'completed')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Withdrawal Completed</h6>
                                    <p class="timeline-date">{{ $withdrawal->processed_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        @elseif($withdrawal->status == 'rejected')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Withdrawal Rejected</h6>
                                    <p class="timeline-date">{{ $withdrawal->processed_at->format('M d, Y h:i A') }}</p>
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

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Need Help?</h6>
                </div>
                <div class="card-body">
                    <p>If you have any questions about your withdrawal, please contact our support team.</p>
                    <a href="#" class="btn btn-block btn-primary">
                        <i class="fas fa-headset"></i> Contact Support
                    </a>
                </div>
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
