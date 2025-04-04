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
                                        <td>{{ is_string($payment->payment_date) ? $payment->payment_date : $payment->payment_date->format('M d, Y H:i') }}</td>
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
                                        <td>{{ is_string($enrollment->enrollment_date) ? $enrollment->enrollment_date : $enrollment->enrollment_date->format('M d, Y H:i') }}</td>
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
        const ctx = document.getElementById('reportChart');

        // Extract data from PHP
        const labels = {!! json_encode($data['labels']) !!};
        const chartData = {!! json_encode($data['data']) !!};

        const chartType = '{{ $reportType }}';
        const timeframe = '{{ $timeframe }}';

        // Determine chart color based on report type
        let backgroundColor, borderColor;
        switch(chartType) {
            case 'revenue':
                backgroundColor = 'rgba(40, 167, 69, 0.2)';
                borderColor = 'rgba(40, 167, 69, 1)';
                break;
            case 'users':
                backgroundColor = 'rgba(0, 123, 255, 0.2)';
                borderColor = 'rgba(0, 123, 255, 1)';
                break;
            default: // enrollments
                backgroundColor = 'rgba(255, 193, 7, 0.2)';
                borderColor = 'rgba(255, 193, 7, 1)';
        }

        // Create chart
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: chartType == 'revenue' ? 'Revenue ($)' : (chartType == 'users' ? 'New Users' : 'Enrollments'),
                    data: chartData,
                    backgroundColor: backgroundColor,
                    borderColor: borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: chartType.charAt(0).toUpperCase() + chartType.slice(1) + ' by ' + timeframe.charAt(0).toUpperCase() + timeframe.slice(1)
                    }
                }
            }
        });
    });

    function printReport() {
        window.print();
    }
</script>
@endsection