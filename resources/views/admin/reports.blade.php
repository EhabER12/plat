@extends('admin.layout')

@section('title', 'Reports & Analytics')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4">Reports & Analytics</h1>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-line me-1"></i>
                Report Settings
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reports') }}" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label for="reportType" class="form-label">Report Type</label>
                        <select class="form-select" id="reportType" name="type" onchange="this.form.submit()">
                            <option value="enrollment" {{ $reportType == 'enrollment' ? 'selected' : '' }}>Enrollments</option>
                            <option value="revenue" {{ $reportType == 'revenue' ? 'selected' : '' }}>Revenue</option>
                            <option value="users" {{ $reportType == 'users' ? 'selected' : '' }}>User Registrations</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="timeframe" class="form-label">Timeframe</label>
                        <select class="form-select" id="timeframe" name="timeframe" onchange="this.form.submit()">
                            <option value="week" {{ $timeframe == 'week' ? 'selected' : '' }}>Weekly</option>
                            <option value="month" {{ $timeframe == 'month' ? 'selected' : '' }}>Monthly</option>
                            <option value="year" {{ $timeframe == 'year' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sync-alt me-1"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-1"></i>
                        {{ ucfirst($reportType) }} {{ ucfirst($timeframe) }}ly Report
                    </div>
                    <div class="card-body">
                        <canvas id="reportChart" width="100%" height="40"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Report Data
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>
                                            @if($reportType == 'revenue')
                                                Revenue
                                            @else
                                                Count
                                            @endif
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($data['labels']) > 0)
                                        @foreach($data['labels'] as $index => $label)
                                            <tr>
                                                <td>{{ $label }}</td>
                                                <td>
                                                    @if($reportType == 'revenue')
                                                        ${{ number_format($data['data'][$index], 2) }}
                                                    @else
                                                        {{ $data['data'][$index] }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-center">No data available</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-download me-1"></i>
                        Export Options
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="printReport()">
                                <i class="fas fa-print me-1"></i> Print Report
                            </button>
                            <a href="{{ route('admin.reports') }}?type={{ $reportType }}&timeframe={{ $timeframe }}&export=csv" class="btn btn-success">
                                <i class="fas fa-file-csv me-1"></i> Export CSV
                            </a>
                            <a href="{{ route('admin.reports') }}?type={{ $reportType }}&timeframe={{ $timeframe }}&export=pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($reportType == 'revenue')
        <!-- Payment Methods Distribution -->
        <div class="row">
            <div class="col-xl-5">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-1"></i>
                        Payment Methods Distribution
                    </div>
                    <div class="card-body">
                        @if(isset($data['method_data']) && count($data['method_data']) > 0)
                            <canvas id="paymentMethodsChart" width="100%" height="50"></canvas>
                        @else
                            <div class="alert alert-info">No payment method data available.</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-7">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-money-bill-wave me-1"></i>
                        Payment Method Details
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Payment Method</th>
                                        <th>Transactions</th>
                                        <th>Total Amount</th>
                                        <th>Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($data['payment_methods']) && count($data['payment_methods']) > 0)
                                        @foreach($data['payment_methods'] as $method)
                                            <tr>
                                                <td>
                                                    <span class="badge rounded-pill" style="background-color: {{ $data['method_colors'][$loop->index] ?? '#6c757d' }}">
                                                        {{ ucfirst($method->payment_method) }}
                                                    </span>
                                                </td>
                                                <td>{{ $method->count }}</td>
                                                <td>${{ number_format($method->total_amount, 2) }}</td>
                                                <td>{{ number_format(($method->total_amount / $data['total_revenue']) * 100, 1) }}%</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">No payment methods data available</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Activity Section -->
        @if($reportType == 'enrollment' || $reportType == 'revenue')
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-history me-1"></i>
                Recent {{ $reportType == 'revenue' ? 'Payments' : 'Enrollments' }}
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Course</th>
                                @if($reportType == 'revenue')
                                <th>Amount</th>
                                <th>Method</th>
                                @endif
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($reportType == 'revenue' && isset($recentPayments) && count($recentPayments) > 0)
                                @foreach($recentPayments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_id }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.edit', $payment->student->user_id) }}">
                                                {{ $payment->student->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.courses.show', $payment->course->course_id) }}">
                                                {{ $payment->course->title }}
                                            </a>
                                        </td>
                                        <td>${{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ ucfirst($payment->payment_method) }}</td>
                                        <td>{{ isset($payment->payment_date) ? (is_string($payment->payment_date) ? $payment->payment_date : $payment->payment_date->format('M d, Y H:i')) : 'N/A' }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @elseif($reportType == 'enrollment' && isset($recentEnrollments) && count($recentEnrollments) > 0)
                                @foreach($recentEnrollments as $enrollment)
                                    <tr>
                                        <td>{{ $enrollment->enrollment_id }}</td>
                                        <td>
                                            <a href="{{ route('admin.users.edit', $enrollment->student->user_id) }}">
                                                {{ $enrollment->student->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.courses.show', $enrollment->course->course_id) }}">
                                                {{ $enrollment->course->title }}
                                            </a>
                                        </td>
                                        <td>{{ isset($enrollment->enrollment_date) ? (is_string($enrollment->enrollment_date) ? $enrollment->enrollment_date : $enrollment->enrollment_date->format('M d, Y H:i')) : 'N/A' }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="{{ $reportType == 'revenue' ? 7 : 5 }}" class="text-center">No recent activity found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configure and build the main report chart
        const reportChartCtx = document.getElementById('reportChart');

        if(reportChartCtx) {
        const chartType = '{{ $reportType }}';
            const chartLabels = @json($data['labels']);
            const chartData = @json($data['data']);
            
            const reportChart = new Chart(reportChartCtx, {
            type: 'bar',
            data: {
                    labels: chartLabels,
                datasets: [{
                        label: chartType === 'revenue' ? 'Revenue ($)' : 'Enrollments',
                    data: chartData,
                        backgroundColor: chartType === 'revenue' ? '#3498db' : '#2ecc71',
                        borderColor: chartType === 'revenue' ? '#2980b9' : '#27ae60',
                    borderWidth: 1
                }]
            },
            options: {
                    responsive: true,
                scales: {
                    y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    if (chartType === 'revenue') {
                                        return '$' + value;
                                    }
                                    return value;
                                }
                            }
                    }
                },
                plugins: {
                        legend: {
                        display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (chartType === 'revenue') {
                                        return 'Revenue: $' + context.raw.toFixed(2);
                                    }
                                    return 'Count: ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Configure and build the payment methods pie chart
        const paymentMethodsChartCtx = document.getElementById('paymentMethodsChart');
        
        if(paymentMethodsChartCtx) {
            const methodLabels = @json($data['method_labels'] ?? []);
            const methodData = @json($data['method_data'] ?? []);
            const methodColors = @json($data['method_colors'] ?? []);
            
            if(methodLabels.length > 0) {
                const paymentMethodsChart = new Chart(paymentMethodsChartCtx, {
                    type: 'pie',
                    data: {
                        labels: methodLabels,
                        datasets: [{
                            data: methodData,
                            backgroundColor: methodColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.raw;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${context.label}: $${value.toFixed(2)} (${percentage}%)`;
                                    }
                                }
                    }
                }
            }
        });
            }
        }
    });

    // Print report function
    function printReport() {
        window.print();
    }
</script>
@endsection