@extends('instructor.layout')

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
                <form action="{{ route('instructor.reports') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="reportType" class="form-label">Report Type</label>
                        <select class="form-select" id="reportType" name="type" onchange="this.form.submit()">
                            <option value="enrollment" {{ $reportType == 'enrollment' ? 'selected' : '' }}>Enrollments</option>
                            <option value="revenue" {{ $reportType == 'revenue' ? 'selected' : '' }}>Revenue</option>
                            <option value="ratings" {{ $reportType == 'ratings' ? 'selected' : '' }}>Ratings</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="timeframe" class="form-label">Timeframe</label>
                        <select class="form-select" id="timeframe" name="timeframe" onchange="this.form.submit()">
                            <option value="week" {{ $timeframe == 'week' ? 'selected' : '' }}>Weekly</option>
                            <option value="month" {{ $timeframe == 'month' ? 'selected' : '' }}>Monthly</option>
                            <option value="year" {{ $timeframe == 'year' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="course_id" class="form-label">Course</label>
                        <select class="form-select" id="course_id" name="course_id" onchange="this.form.submit()">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->course_id }}" {{ $courseId == $course->course_id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
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
                        @if($courseId)
                            for {{ $courses->where('course_id', $courseId)->first()->title }}
                        @endif
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
                                        @if($reportType == 'ratings')
                                            <th>Avg. Rating</th>
                                            <th>Count</th>
                                        @elseif($reportType == 'revenue')
                                            <th>Revenue</th>
                                        @else
                                            <th>Count</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($data['labels']) > 0)
                                        @foreach($data['labels'] as $index => $label)
                                            <tr>
                                                <td>{{ $label }}</td>
                                                @if($reportType == 'ratings')
                                                    <td>{{ number_format($data['avg_ratings'][$index], 1) }}</td>
                                                    <td>{{ $data['counts'][$index] }}</td>
                                                @elseif($reportType == 'revenue')
                                                    <td>${{ number_format($data['data'][$index], 2) }}</td>
                                                @else
                                                    <td>{{ $data['data'][$index] }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ $reportType == 'ratings' ? 3 : 2 }}" class="text-center">No data available</td>
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
                            <a href="{{ route('instructor.reports') }}?type={{ $reportType }}&timeframe={{ $timeframe }}&course_id={{ $courseId }}&export=csv" class="btn btn-success">
                                <i class="fas fa-file-csv me-1"></i> Export CSV
                            </a>
                            <a href="{{ route('instructor.reports') }}?type={{ $reportType }}&timeframe={{ $timeframe }}&course_id={{ $courseId }}&export=pdf" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity Section -->
        @if($reportType == 'enrollment' && isset($recentEnrollments))
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-history me-1"></i>
                Recent Enrollments
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($recentEnrollments) > 0)
                                @foreach($recentEnrollments as $enrollment)
                                    <tr>
                                        <td>{{ $enrollment->enrollment_id }}</td>
                                        <td>{{ $enrollment->student_name }}</td>
                                        <td>{{ $enrollment->course_title }}</td>
                                        <td>{{ date('M d, Y H:i', strtotime($enrollment->enrolled_at)) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $enrollment->status == 'active' ? 'success' : ($enrollment->status == 'completed' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">No recent enrollments found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @elseif($reportType == 'revenue' && isset($recentPayments))
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-history me-1"></i>
                Recent Payments
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($recentPayments) > 0)
                                @foreach($recentPayments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_id }}</td>
                                        <td>{{ $payment->student_name }}</td>
                                        <td>{{ $payment->course_title }}</td>
                                        <td>${{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ ucfirst($payment->payment_method) }}</td>
                                        <td>{{ date('M d, Y H:i', strtotime($payment->paid_at)) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $payment->status == 'completed' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">No recent payments found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @elseif($reportType == 'ratings' && isset($recentRatings))
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-history me-1"></i>
                Recent Ratings
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Rating</th>
                                <th>Review</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($recentRatings) > 0)
                                @foreach($recentRatings as $rating)
                                    <tr>
                                        <td>{{ $rating->review_id }}</td>
                                        <td>{{ $rating->student_name }}</td>
                                        <td>{{ $rating->course_title }}</td>
                                        <td>
                                            <div class="text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $rating->rating ? '' : '-o' }}"></i>
                                                @endfor
                                            </div>
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit($rating->review, 50) }}</td>
                                        <td>{{ date('M d, Y', strtotime($rating->created_at)) }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">No recent ratings found</td>
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
        
        const chartType = '{{ $reportType }}';
        const timeframe = '{{ $timeframe }}';
        
        // Determine chart configuration based on report type
        let chartConfig = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: []
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
        };
        
        // Configure datasets based on report type
        if (chartType === 'ratings') {
            // For ratings, we show both average rating and count
            const avgRatings = {!! json_encode($data['avg_ratings'] ?? []) !!};
            const counts = {!! json_encode($data['counts'] ?? []) !!};
            
            chartConfig.type = 'line';
            chartConfig.data.datasets = [
                {
                    label: 'Average Rating',
                    data: avgRatings,
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 2,
                    yAxisID: 'y'
                },
                {
                    label: 'Number of Ratings',
                    data: counts,
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 2,
                    type: 'bar',
                    yAxisID: 'y1'
                }
            ];
            
            // Configure dual y-axes for ratings
            chartConfig.options.scales = {
                y: {
                    beginAtZero: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Average Rating'
                    },
                    max: 5
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Number of Ratings'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            };
        } else {
            // For enrollment and revenue
            const chartData = {!! json_encode($data['data'] ?? []) !!};
            
            // Determine chart color based on report type
            let backgroundColor, borderColor;
            switch(chartType) {
                case 'revenue':
                    backgroundColor = 'rgba(40, 167, 69, 0.2)';
                    borderColor = 'rgba(40, 167, 69, 1)';
                    break;
                default: // enrollments
                    backgroundColor = 'rgba(255, 193, 7, 0.2)';
                    borderColor = 'rgba(255, 193, 7, 1)';
            }
            
            chartConfig.data.datasets = [{
                label: chartType === 'revenue' ? 'Revenue ($)' : 'Enrollments',
                data: chartData,
                backgroundColor: backgroundColor,
                borderColor: borderColor,
                borderWidth: 1
            }];
        }
        
        // Create chart
        new Chart(ctx, chartConfig);
    });
    
    function printReport() {
        window.print();
    }
</script>
@endsection
