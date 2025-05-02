@extends('layouts.instructor')

@section('title', 'Instructor Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<!-- استيراد خط تجوال العربي -->
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    /* تطبيق الخط على الصفحة بالكامل */
    body, button, input, select, textarea {
        font-family: 'Tajawal', 'Helvetica Neue', Arial, sans-serif !important;
    }

    .stat-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .stat-card .card-body {
        padding: 1.5rem;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #6c757d;
        font-size: 14px;
    }

    .course-card {
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .activity-item {
        border-radius: 8px;
        margin-bottom: 10px;
        padding: 15px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .activity-item:hover {
        background-color: #f1f3f5;
    }

    .dashboard-section {
        margin-bottom: 30px;
    }

    .dashboard-section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }

    .dashboard-section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background: #20b7b7;
    }

    .review-item {
        padding: 15px;
        border-radius: 8px;
        background-color: #f8f9fa;
        margin-bottom: 10px;
    }

    .star-rating {
        color: #ffc107;
    }

    .approval-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .approval-badge.approved {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .approval-badge.pending {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .approval-badge.rejected {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    /* تحسين تصميم قسم الرسم البياني للإيرادات */
    .chart-card {
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: none;
        overflow: hidden;
    }

    .chart-card .card-header {
        background-color: #ffffff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.25rem;
    }

    .chart-card .card-body {
        padding: 1.5rem;
        position: relative;
        height: 350px;
        background-color: #fbfbfb;
    }

    .chart-card canvas {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .chart-card h5 {
        font-weight: 600;
        color: #333;
        display: flex;
        align-items: center;
    }

    .chart-card h5 i {
        color: #20b7b7;
    }

    @media (max-width: 768px) {
        .chart-card .card-body {
            height: 280px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Welcome Message -->
    <div class="welcome-area fade-in">
        <div class="welcome-shape"></div>
        <div class="welcome-shape-2"></div>
        <h3>مرحباً، {{ Auth::user()->name }}!</h3>
        <p>هذه هي لوحة تحكم المدرب الخاصة بك. يمكنك إدارة دوراتك ومتابعة إحصائياتك من هنا.</p>
        <a href="{{ route('instructor.courses.create') }}" class="btn">
            <i class="fas fa-plus-circle me-2"></i>إنشاء دورة جديدة
        </a>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
            <div class="stat-card fade-in" style="animation-delay: 0.1s">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="fas fa-book"></i>
                        </div>
                        <h4 class="stat-value">{{ $totalCourses }}</h4>
                        <p class="stat-label mb-0">الدورات</p>
                    </div>
                    <div class="align-self-end">
                        <div class="small mb-2">
                            <span class="text-success">{{ $approvedCourses }}</span> معتمدة
                        </div>
                        <div class="small">
                            <span class="text-warning">{{ $pendingCourses }}</span> قيد المراجعة
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
            <div class="stat-card fade-in" style="animation-delay: 0.2s">
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="stat-value">{{ $totalStudents }}</h4>
                <p class="stat-label mb-0">الطلاب المسجلين</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
            <div class="stat-card fade-in" style="animation-delay: 0.3s">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <h4 class="stat-value">${{ number_format($totalRevenue, 2) }}</h4>
                <p class="stat-label mb-0">إجمالي الإيرادات</p>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card fade-in" style="animation-delay: 0.4s">
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fas fa-star"></i>
                </div>
                <h4 class="stat-value">
                    {{ isset($courses) && $courses->sum('ratings_count') > 0 ? number_format($courses->sum('ratings_count') / $courses->count(), 1) : '0.0' }}
                </h4>
                <p class="stat-label mb-0">متوسط التقييم</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-xl-8">
            <!-- Revenue Chart -->
            <div class="card chart-card mb-4 fade-in" style="animation-delay: 0.5s">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> الإيرادات الشهرية</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>

            <!-- Latest Courses -->
            <div class="card table-card mb-4 fade-in" style="animation-delay: 0.6s">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0"><i class="fas fa-book-open me-2"></i> دوراتك الأخيرة</h5>
                    <a href="{{ route('instructor.courses') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body p-0">
                    @if(count($courses) > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>العنوان</th>
                                        <th>السعر</th>
                                        <th>الطلاب</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses->take(5) as $course)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded p-2 me-3">
                                                        <i class="fas fa-book text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $course->title }}</h6>
                                                        <small>{{ \Carbon\Carbon::parse($course->created_at)->format('d M Y') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>${{ $course->price }}</td>
                                            <td>{{ $course->students_count }}</td>
                                            <td>
                                                @if($course->status === 'published')
                                                    <span class="approval-badge approved">معتمدة</span>
                                                @elseif($course->status === 'pending')
                                                    <span class="approval-badge pending">قيد المراجعة</span>
                                                @else
                                                    <span class="approval-badge rejected">مرفوضة</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        الإجراءات
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="{{ route('instructor.courses.manage', $course->course_id) }}">إدارة</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('instructor.courses.edit', $course->course_id) }}">تعديل</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('course.detail', $course->course_id) }}" target="_blank">معاينة</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 bg-light rounded">
                            <div class="mb-3">
                                <i class="fas fa-book-open fa-3x text-muted"></i>
                            </div>
                            <h5>ليس لديك أي دورات حتى الآن</h5>
                            <p class="text-muted">ابدأ مشاركة معرفتك من خلال إنشاء دورتك الأولى.</p>
                            <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus-circle me-2"></i> إنشاء دورة
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-xl-4">
            <!-- Recent Enrollments -->
            <div class="card mb-4 fade-in" style="animation-delay: 0.7s">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i> آخر التسجيلات</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($recentEnrollments) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentEnrollments as $enrollment)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">تسجيل جديد</h6>
                                            <p class="mb-0 small">
                                                <span class="fw-semibold">{{ $enrollment->student->name }}</span> سجل في
                                                <span class="fw-semibold">{{ $enrollment->course->title }}</span>
                                            </p>
                                        </div>
                                        <small class="text-muted">{{ $enrollment->enrolled_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-user-graduate fa-3x text-muted"></i>
                            </div>
                            <p class="mb-0">لا يوجد تسجيلات حتى الآن.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Reviews -->
            <div class="card fade-in" style="animation-delay: 0.8s">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i> آخر التقييمات</h5>
                </div>
                <div class="card-body">
                    @if(isset($recentRatings) && count($recentRatings) > 0)
                        @foreach($recentRatings as $rating)
                            <div class="review-item">
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $rating->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($rating->created_at)->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 small">{{ $rating->comment ?? 'لا يوجد تعليق.' }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">عن: <strong>{{ $rating->course_title }}</strong></small>
                                    <small class="text-muted">بواسطة: {{ $rating->is_anonymous ? 'مجهول' : $rating->user_name }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-star fa-3x text-muted"></i>
                            </div>
                            <p class="mb-0">لا يوجد تقييمات حتى الآن.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');

        // تحديد الاتجاه للرسم البياني
        Chart.defaults.font.family = "'Tajawal', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
        Chart.defaults.font.size = 14;
        
        // Parse the JSON safely with fallbacks
        let chartLabels = [];
        let chartData = [];

        try {
            chartLabels = JSON.parse('{!! $chartLabels ?? "[]" !!}');
        } catch (e) {
            console.error('Error parsing chart labels');
            chartLabels = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'];
        }

        try {
            chartData = JSON.parse('{!! $chartData ?? "[]" !!}');
        } catch (e) {
            console.error('Error parsing chart data');
            chartData = [500, 800, 1200, 1600, 1800, 2000];
        }

        const monthlyRevenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'الإيرادات ($)',
                    data: chartData,
                    backgroundColor: 'rgba(32, 183, 183, 0.2)',
                    borderColor: '#20b7b7',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#20b7b7',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleAlign: 'center',
                        bodyAlign: 'center',
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `$${context.raw.toFixed(2)}`;
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            padding: 10,
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            padding: 10
                        }
                    }
                },
                elements: {
                    line: {
                        tension: 0.4
                    }
                }
            }
        });
    });
</script>
@endsection