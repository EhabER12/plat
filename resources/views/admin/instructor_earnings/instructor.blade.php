@extends('layouts.admin')

@section('title', 'Instructor Earnings')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Instructor Earnings: {{ $instructor->name }}</h1>
        <a href="{{ route('admin.instructor-earnings.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Earnings
        </a>
    </div>

    <!-- Instructor Profile -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center">
                    <img class="img-profile rounded-circle mb-3" src="{{ $instructor->profile_image ? asset('storage/' . $instructor->profile_image) : asset('img/undraw_profile.svg') }}" width="120">
                </div>
                <div class="col-md-5">
                    <h4>{{ $instructor->name }}</h4>
                    <p><i class="fas fa-envelope"></i> {{ $instructor->email }}</p>
                    <p><i class="fas fa-phone"></i> {{ $instructor->phone ?? 'N/A' }}</p>
                    <p><i class="fas fa-calendar-alt"></i> Joined: {{ $instructor->created_at->format('M d, Y') }}</p>
                </div>
                <div class="col-md-5">
                    <div class="row">
                        <div class="col-6">
                            <div class="card bg-primary text-white mb-3">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Total Earnings</div>
                                    <div class="h5 mb-0 font-weight-bold">${{ number_format($totalEarnings, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Available</div>
                                    <div class="h5 mb-0 font-weight-bold">${{ number_format($availableEarnings, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-warning text-white mb-3">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Pending</div>
                                    <div class="h5 mb-0 font-weight-bold">${{ number_format($pendingEarnings, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-info text-white mb-3">
                                <div class="card-body py-2">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Withdrawn</div>
                                    <div class="h5 mb-0 font-weight-bold">${{ number_format($withdrawnEarnings, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Earnings History -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Earnings History</h6>
                </div>
                <div class="card-body">
                    @if($earnings->isEmpty())
                        <div class="alert alert-info">
                            <p class="mb-0">No earnings data available for this instructor.</p>
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $earnings->links() }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Withdrawal History -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Withdrawal History</h6>
                </div>
                <div class="card-body">
                    @if($withdrawals->isEmpty())
                        <div class="alert alert-info">
                            <p class="mb-0">No withdrawal history available for this instructor.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($withdrawals as $withdrawal)
                                        <tr>
                                            <td>{{ $withdrawal->requested_at->format('M d, Y') }}</td>
                                            <td>${{ number_format($withdrawal->amount, 2) }}</td>
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
                                            <td>
                                                <a href="{{ route('admin.instructor-earnings.show-withdrawal', $withdrawal->withdrawal_id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
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

            <!-- Earnings Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Earnings Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="earningsStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Available
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Pending
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Withdrawn
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Earnings Status Chart
    var ctx = document.getElementById("earningsStatusChart");
    var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ["Available", "Pending", "Withdrawn"],
            datasets: [{
                data: [{{ $availableEarnings }}, {{ $pendingEarnings }}, {{ $withdrawnEarnings }}],
                backgroundColor: ['#1cc88a', '#f6c23e', '#36b9cc'],
                hoverBackgroundColor: ['#17a673', '#dda20a', '#2c9faf'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.labels[tooltipItem.index];
                        var value = data.datasets[0].data[tooltipItem.index];
                        return label + ': $' + parseFloat(value).toFixed(2);
                    }
                }
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });
</script>
@endsection
@endsection
