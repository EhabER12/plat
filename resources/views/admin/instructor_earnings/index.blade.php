@extends('layouts.admin')

@section('title', 'Instructor Earnings')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Instructor Earnings</h1>
        <div>
            <a href="{{ route('admin.instructor-earnings.withdrawals') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-2">
                <i class="fas fa-money-bill-wave fa-sm text-white-50"></i> Manage Withdrawals
            </a>
            <a href="{{ route('admin.instructor-earnings.settings') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
                <i class="fas fa-cog fa-sm text-white-50"></i> Commission Settings
            </a>
        </div>
    </div>

    <!-- Earnings Overview -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Instructor Earnings</div>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Platform Fees</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($platformFees, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Earning Instructors -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Earning Instructors</h6>
                </div>
                <div class="card-body">
                    @if($topInstructors->isEmpty())
                        <div class="alert alert-info">
                            <p class="mb-0">No instructor earnings data available yet.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Instructor</th>
                                        <th>Total Earnings</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topInstructors as $instructor)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img class="img-profile rounded-circle mr-2" src="{{ $instructor->profile_image ? asset('storage/' . $instructor->profile_image) : asset('img/undraw_profile.svg') }}" width="40">
                                                    <div>
                                                        <div>{{ $instructor->name }}</div>
                                                        <small class="text-muted">{{ $instructor->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>${{ number_format($instructor->earnings_sum_amount ?? 0, 2) }}</td>
                                            <td>
                                                <a href="{{ route('admin.instructor-earnings.instructor', $instructor->user_id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Earnings Chart -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Earnings Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="earningsDistributionChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Instructor Earnings
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Platform Fees
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Earnings -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Earnings</h6>
        </div>
        <div class="card-body">
            @if($recentEarnings->isEmpty())
                <div class="alert alert-info">
                    <p class="mb-0">No earnings data available yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Instructor</th>
                                <th>Course</th>
                                <th>Student</th>
                                <th>Amount</th>
                                <th>Platform Fee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentEarnings as $earning)
                                <tr>
                                    <td>{{ $earning->created_at->format('M d, Y') }}</td>
                                    <td>{{ $earning->instructor->name }}</td>
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
                {{ $recentEarnings->links() }}
            @endif
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Earnings Distribution Chart
    var ctx = document.getElementById("earningsDistributionChart");
    var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ["Instructor Earnings", "Platform Fees"],
            datasets: [{
                data: [{{ $totalEarnings }}, {{ $platformFees }}],
                backgroundColor: ['#4e73df', '#1cc88a'],
                hoverBackgroundColor: ['#2e59d9', '#17a673'],
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
