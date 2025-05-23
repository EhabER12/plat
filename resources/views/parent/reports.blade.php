@extends('layouts.app')

@section('title', 'التقارير التفصيلية')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    :root {
        --primary-color: #003366;
        --secondary-color: #FFD700;
        --accent-color: #FF6B35;
        --text-color: #333;
        --light-bg: #f8f9fa;
    }

    body {
        font-family: 'Cairo', sans-serif;
        background-color: var(--light-bg);
        color: var(--text-color);
        direction: rtl;
    }

    .dashboard-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #002244 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
    }

    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 1.5rem;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .stat-box {
        text-align: center;
        padding: 1rem;
        border-radius: 10px;
        background: white;
        margin-bottom: 1rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #666;
    }

    .section-title {
        color: var(--primary-color);
        border-bottom: 3px solid var(--secondary-color);
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .report-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .progress-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        position: relative;
    }

    .chart-container {
        position: relative;
        height: 300px;
        margin: 1rem 0;
    }

    .filter-tabs {
        background: white;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 2rem;
    }

    .nav-pills .nav-link {
        border-radius: 25px;
        margin: 0 0.25rem;
        padding: 0.5rem 1.5rem;
    }

    .nav-pills .nav-link.active {
        background-color: var(--primary-color);
    }

    .comparison-table {
        background: white;
        border-radius: 15px;
        overflow: hidden;
    }

    .table th {
        background-color: var(--primary-color);
        color: white;
        border: none;
    }

    .table td {
        border-color: #e9ecef;
        vertical-align: middle;
    }

    .trend-up {
        color: #28a745;
    }

    .trend-down {
        color: #dc3545;
    }

    .trend-neutral {
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="dashboard-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-0"><i class="fas fa-chart-line me-3"></i>التقارير التفصيلية</h1>
                <p class="mb-0 mt-2">تقارير شاملة عن أداء جميع الأبناء</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-right me-2"></i>العودة للوحة التحكم
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <ul class="nav nav-pills justify-content-center" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview" type="button" role="tab">
                    <i class="fas fa-chart-pie me-2"></i>نظرة عامة
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="performance-tab" data-bs-toggle="pill" data-bs-target="#performance" type="button" role="tab">
                    <i class="fas fa-chart-bar me-2"></i>الأداء
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="progress-tab" data-bs-toggle="pill" data-bs-target="#progress" type="button" role="tab">
                    <i class="fas fa-tasks me-2"></i>التقدم
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="comparison-tab" data-bs-toggle="pill" data-bs-target="#comparison" type="button" role="tab">
                    <i class="fas fa-balance-scale me-2"></i>المقارنة
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="reportTabsContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>نشاط الأبناء خلال الشهر الماضي</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="monthlyActivityChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">إحصائيات سريعة</h6>
                        </div>
                        <div class="card-body">
                            <div class="stat-box mb-3">
                                <div class="stat-value text-primary">{{ $overallStats['total_students'] ?? 0 }}</div>
                                <div class="stat-label">إجمالي الأبناء</div>
                            </div>
                            <div class="stat-box mb-3">
                                <div class="stat-value text-success">{{ $overallStats['avg_progress'] ?? 0 }}%</div>
                                <div class="stat-label">متوسط التقدم</div>
                            </div>
                            <div class="stat-box mb-3">
                                <div class="stat-value text-info">{{ $overallStats['total_courses'] ?? 0 }}</div>
                                <div class="stat-label">إجمالي الدورات</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-value text-warning">{{ $overallStats['total_certificates'] ?? 0 }}</div>
                                <div class="stat-label">الشهادات المكتسبة</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Tab -->
        <div class="tab-pane fade" id="performance" role="tabpanel">
            <div class="row">
                @if(isset($students) && count($students) > 0)
                    @foreach($students as $student)
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-user-graduate me-2"></i>{{ $student->name }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="stat-box">
                                            <div class="stat-value text-primary">{{ $studentStats[$student->user_id]['avg_progress'] ?? 0 }}%</div>
                                            <div class="stat-label">التقدم العام</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stat-box">
                                            <div class="stat-value text-success">{{ $studentStats[$student->user_id]['avg_exam_score'] ?? 0 }}%</div>
                                            <div class="stat-label">متوسط الدرجات</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('parent.student-activity', $student->user_id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-2"></i>عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i>لا توجد بيانات طلاب متاحة حالياً
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Progress Tab -->
        <div class="tab-pane fade" id="progress" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>تطور الأداء عبر الوقت</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="progressChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison Tab -->
        <div class="tab-pane fade" id="comparison" role="tabpanel">
            <div class="row">
                <div class="col-12">
                    <div class="comparison-table">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>اسم الطالب</th>
                                    <th>الدورات المسجلة</th>
                                    <th>التقدم العام</th>
                                    <th>الاختبارات</th>
                                    <th>متوسط الدرجات</th>
                                    <th>الشهادات</th>
                                    <th>الاتجاه</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($students) && count($students) > 0)
                                    @foreach($students as $student)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    {{ substr($student->name, 0, 1) }}
                                                </div>
                                                <strong>{{ $student->name }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $studentStats[$student->user_id]['courses_count'] ?? 0 }}</td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-primary" style="width: {{ $studentStats[$student->user_id]['avg_progress'] ?? 0 }}%"></div>
                                            </div>
                                            <small>{{ $studentStats[$student->user_id]['avg_progress'] ?? 0 }}%</small>
                                        </td>
                                        <td>{{ $studentStats[$student->user_id]['exams_count'] ?? 0 }}</td>
                                        <td>
                                            <span class="badge 
                                                @if(($studentStats[$student->user_id]['avg_exam_score'] ?? 0) >= 80) bg-success
                                                @elseif(($studentStats[$student->user_id]['avg_exam_score'] ?? 0) >= 60) bg-warning
                                                @else bg-danger
                                                @endif">
                                                {{ $studentStats[$student->user_id]['avg_exam_score'] ?? 0 }}%
                                            </span>
                                        </td>
                                        <td>{{ $studentStats[$student->user_id]['certificates_earned'] ?? 0 }}</td>
                                        <td>
                                            <i class="fas fa-arrow-up trend-up"></i>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">لا توجد بيانات متاحة</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sample data for charts - replace with actual data from backend
    const monthlyActivityData = {
        labels: ['الأسبوع 1', 'الأسبوع 2', 'الأسبوع 3', 'الأسبوع 4'],
        datasets: [{
            label: 'ساعات الدراسة',
            data: [12, 19, 15, 25],
            borderColor: '#003366',
            backgroundColor: 'rgba(0, 51, 102, 0.1)',
            fill: true,
            tension: 0.4
        }]
    };

    const progressData = {
        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
        datasets: [{
            label: 'متوسط التقدم %',
            data: [65, 70, 75, 80, 85, 90],
            borderColor: '#FFD700',
            backgroundColor: 'rgba(255, 215, 0, 0.1)',
            fill: true,
            tension: 0.4
        }]
    };

    // Monthly Activity Chart
    const monthlyCtx = document.getElementById('monthlyActivityChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: monthlyActivityData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Progress Chart
    const progressCtx = document.getElementById('progressChart').getContext('2d');
    new Chart(progressCtx, {
        type: 'line',
        data: progressData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
