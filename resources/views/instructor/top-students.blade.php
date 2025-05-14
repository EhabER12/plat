@extends('layouts.instructor')

@section('title', 'تحليل أداء الطلاب المتميزين')
@section('page-title', 'تحليل أداء الطلاب المتميزين')

@section('styles')
<style>
    .student-performance-card {
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    
    .student-performance-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .student-header {
        padding: 15px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #eee;
    }
    
    .student-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        margin-left: 15px;
        background-color: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 24px;
        color: #fff;
    }
    
    .student-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .student-info h5 {
        margin-bottom: 5px;
        font-weight: 600;
    }
    
    .student-info p {
        color: #666;
        margin-bottom: 0;
    }
    
    .performance-stats {
        padding: 15px;
    }
    
    .stats-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    
    .stats-value {
        font-size: 28px;
        font-weight: 700;
        line-height: 1;
    }
    
    .stats-label {
        font-size: 13px;
        color: #666;
    }
    
    .score-meter {
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        background-color: #e9ecef;
        margin-bottom: 15px;
    }
    
    .score-value {
        height: 100%;
        border-radius: 4px;
    }
    
    .course-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        margin-right: 5px;
        margin-bottom: 5px;
        background-color: #f8f9fa;
        border: 1px solid #eee;
    }
    
    .filter-wrapper {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .filter-item {
        flex: 1;
        min-width: 200px;
    }
    
    .overview-card {
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
    }
    
    .overview-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 15px;
        font-size: 24px;
    }
    
    .overview-content h5 {
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .overview-content p {
        margin-bottom: 0;
        color: #666;
    }
    
    /* Color classes for performance scores */
    .score-excellent { background-color: #28a745; }
    .score-good { background-color: #5bc0de; }
    .score-average { background-color: #ffc107; }
    .score-needs-improvement { background-color: #dc3545; }
    
    /* Color classes for overview cards */
    .bg-purple-light { background-color: rgba(111, 66, 193, 0.1); }
    .bg-teal-light { background-color: rgba(32, 201, 151, 0.1); }
    .bg-blue-light { background-color: rgba(13, 110, 253, 0.1); }
    .bg-yellow-light { background-color: rgba(255, 193, 7, 0.1); }
    
    .text-purple { color: #6f42c1; }
    .text-teal { color: #20c997; }
    .text-blue { color: #0d6efd; }
    .text-yellow { color: #ffc107; }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    /* أنماط جديدة للتحليل المفصل للطلاب */
    .recommendation-item {
        padding: 2px 0;
        font-size: 0.9rem;
        display: flex;
        align-items: flex-start;
    }
    
    .recommendation-item i {
        margin-top: 3px;
    }
    
    .activity-stat {
        transition: all 0.2s ease;
        border: 1px solid rgba(0,0,0,0.1);
    }
    
    .activity-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .course-progress-item {
        padding: 0.5rem;
        border-radius: 6px;
        background-color: #f9f9f9;
        transition: all 0.2s ease;
    }
    
    .course-progress-item:hover {
        background-color: #f0f0f0;
    }
    
    .course-title {
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .progress-percent {
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .analysis-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #666;
        margin-bottom: 5px;
        border-bottom: 1px solid #eee;
        padding-bottom: 3px;
    }
    
    .analysis-metric {
        flex: 0 0 48%;
    }
    
    .action-plan {
        border-left: 3px solid #20c997;
    }
    
    .action-plan li {
        margin-bottom: 8px;
    }
    
    /* تحسينات التصميم المتجاوب */
    @media (max-width: 768px) {
        .overview-card {
            flex-direction: column;
            text-align: center;
        }
        
        .overview-icon {
            margin-left: 0;
            margin-bottom: 15px;
        }
        
        .analysis-metric {
            flex: 0 0 100%;
            margin-bottom: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-search me-2"></i> تصفية النتائج
                        </h5>
                        
                        <a href="{{ route('instructor.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-right me-1"></i> العودة للوحة التحكم
                        </a>
                    </div>
                    
                    <div class="filter-wrapper">
                        <div class="filter-item">
                            <label for="courseFilter" class="form-label">فلترة حسب الدورة</label>
                            <select class="form-select" id="courseFilter">
                                <option value="all">جميع الدورات</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->course_id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="filter-item">
                            <label for="performanceFilter" class="form-label">فلترة حسب الأداء</label>
                            <select class="form-select" id="performanceFilter">
                                <option value="all">جميع مستويات الأداء</option>
                                <option value="excellent">ممتاز (80-100%)</option>
                                <option value="good">جيد (70-79%)</option>
                                <option value="average">متوسط (60-69%)</option>
                                <option value="needs-improvement">يحتاج تحسين (أقل من 60%)</option>
                            </select>
                        </div>
                        
                        <div class="filter-item">
                            <label for="examFilter" class="form-label">فلترة حسب الامتحانات</label>
                            <select class="form-select" id="examFilter">
                                <option value="all">جميع الامتحانات</option>
                                <option value="passed">اجتاز امتحان واحد على الأقل</option>
                                <option value="failed">لم يجتز أي امتحان</option>
                                <option value="not-taken">لم يأخذ أي امتحان</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Overview -->
    <div class="row mb-4">
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="overview-card bg-purple-light">
                <div class="overview-icon text-purple">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="overview-content">
                    <h5>متوسط أداء الطلاب</h5>
                    <p class="fs-4 fw-bold">{{ number_format($overallStats['avg_performance_score'], 1) }}%</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="overview-card bg-teal-light">
                <div class="overview-icon text-teal">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="overview-content">
                    <h5>متوسط درجة الامتحانات</h5>
                    <p class="fs-4 fw-bold">{{ number_format($overallStats['avg_score'], 1) }}%</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="overview-card bg-blue-light">
                <div class="overview-icon text-blue">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="overview-content">
                    <h5>متوسط عدد الامتحانات المجتازة</h5>
                    <p class="fs-4 fw-bold">{{ number_format($overallStats['avg_exams_passed'], 1) }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-3 mb-3">
            <div class="overview-card bg-yellow-light">
                <div class="overview-icon text-yellow">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="overview-content">
                    <h5>أعلى أداء</h5>
                    <p class="fs-4 fw-bold">{{ $overallStats['highest_performer'] ? $overallStats['highest_performer']->name : 'غير متوفر' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    @if(count($examStats) > 0)
    <!-- Exam Performance Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> أداء الطلاب في الامتحانات</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="examPerformanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Student Performance Cards -->
    <div class="row" id="studentsContainer">
        @forelse($topStudents as $student)
            <div class="col-md-6 col-xl-4 student-card" 
                data-performance="{{ $student->performance_score }}" 
                data-exams-passed="{{ $student->exams_passed }}"
                data-exams-taken="{{ $student->exams_taken }}"
                data-courses="{{ implode(',', $student->course_performance->pluck('course_id')->toArray()) }}">
                
                <div class="card student-performance-card">
                    <div class="student-header">
                        <div class="student-avatar" style="background-color: {{ '#' . substr(md5($student->user_id), 0, 6) }}">
                            @if($student->profile_picture)
                                <img src="{{ asset($student->profile_picture) }}" alt="{{ $student->name }}">
                            @else
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="student-info">
                            <h5>{{ $student->name }}</h5>
                            <p>{{ $student->email }}</p>
                            <span class="badge rounded-pill {{ $student->performance_score >= 80 ? 'bg-success' : ($student->performance_score >= 70 ? 'bg-info' : ($student->performance_score >= 60 ? 'bg-warning' : 'bg-danger')) }}">
                                {{ $student->performance_score >= 80 ? 'ممتاز' : ($student->performance_score >= 70 ? 'جيد جداً' : ($student->performance_score >= 60 ? 'جيد' : 'يحتاج إلى تحسين')) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="performance-stats">
                        <div class="stats-header">
                            <div>
                                <div class="stats-value">{{ number_format($student->performance_score, 0) }}%</div>
                                <div class="stats-label">درجة الأداء الكلية</div>
                            </div>
                            
                            <div class="text-end">
                                <div class="stats-value">{{ $student->exams_passed }}</div>
                                <div class="stats-label">امتحانات مجتازة</div>
                            </div>
                        </div>
                        
                        <div class="score-meter">
                            <div class="score-value 
                                {{ $student->performance_score >= 80 ? 'score-excellent' : 
                                   ($student->performance_score >= 70 ? 'score-good' : 
                                   ($student->performance_score >= 60 ? 'score-average' : 'score-needs-improvement')) }}" 
                                style="width: {{ $student->performance_score }}%"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <div class="stats-label">متوسط الدرجات</div>
                                <div class="stats-value">{{ $student->avg_score ? number_format($student->avg_score, 1) : 'N/A' }}</div>
                            </div>
                            
                            <div class="text-end">
                                <div class="stats-label">الدورات المسجلة</div>
                                <div class="stats-value">{{ $student->courses_enrolled }}</div>
                            </div>
                        </div>
                        
                        <!-- تفاصيل نشاط الطالب -->
                        <div class="activity-overview mb-3">
                            <div class="stats-label mb-2">ملخص النشاط:</div>
                            <div class="row row-cols-2 g-2">
                                <div class="col">
                                    <div class="activity-stat bg-light p-2 rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock text-secondary me-2"></i>
                                            <div>
                                                <small class="d-block text-muted">آخر نشاط</small>
                                                <span class="fw-bold">
                                                    @php
                                                        $hasRecentActivity = false;
                                                        foreach($student->course_performance as $course) {
                                                            if (isset($course['last_activity_at'])) {
                                                                $hasRecentActivity = true;
                                                                break;
                                                            }
                                                        }
                                                    @endphp
                                                    
                                                    @if($hasRecentActivity)
                                                        {{ \Carbon\Carbon::parse($course['last_activity_at'])->diffForHumans() }}
                                                    @else
                                                        غير متوفر
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="activity-stat bg-light p-2 rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-hourglass-half text-warning me-2"></i>
                                            <div>
                                                <small class="d-block text-muted">إجمالي الوقت المستغرق</small>
                                                <span class="fw-bold">
                                                    @php
                                                        $totalMinutes = 0;
                                                        foreach($student->course_performance as $course) {
                                                            $totalMinutes += $course['time_spent'] ?? 0;
                                                        }
                                                        $hours = floor($totalMinutes / 60);
                                                        $minutes = $totalMinutes % 60;
                                                    @endphp
                                                    
                                                    @if($totalMinutes > 0)
                                                        {{ $hours }}س {{ $minutes }}د
                                                    @else
                                                        غير متوفر
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="activity-stat bg-light p-2 rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle {{ $student->exams_passed > 0 ? 'text-success' : 'text-secondary' }} me-2"></i>
                                            <div>
                                                <small class="d-block text-muted">نسبة النجاح في الامتحانات</small>
                                                <span class="fw-bold">
                                                    @if($student->exams_taken > 0)
                                                        {{ number_format(($student->exams_passed / $student->exams_taken) * 100, 0) }}%
                                                    @else
                                                        غير متوفر
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="activity-stat bg-light p-2 rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-graduation-cap {{ $student->course_performance->where('completed', true)->count() > 0 ? 'text-primary' : 'text-secondary' }} me-2"></i>
                                            <div>
                                                <small class="d-block text-muted">الدورات المكتملة</small>
                                                <span class="fw-bold">
                                                    {{ $student->course_performance->where('completed', true)->count() }} / {{ $student->courses_enrolled }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- الدورات والتقدم -->
                        <div class="mb-3">
                            <div class="stats-label mb-2">الدورات والتقدم:</div>
                            <div class="courses-progress">
                                @foreach($student->course_performance as $course)
                                    <div class="course-progress-item mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="course-title">{{ \Illuminate\Support\Str::limit($course['course_title'], 25) }}</span>
                                            <span class="progress-percent {{ $course['progress'] >= 100 ? 'text-success' : ($course['progress'] >= 50 ? 'text-primary' : 'text-warning') }}">
                                                {{ $course['progress'] }}%
                                            </span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar {{ $course['progress'] >= 100 ? 'bg-success' : ($course['progress'] >= 50 ? 'bg-primary' : 'bg-warning') }}" 
                                                 role="progressbar" style="width: {{ $course['progress'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- نصائح وتوصيات مخصصة -->
                        <div class="recommendations mb-3">
                            <div class="stats-label mb-2">نصائح وتوصيات مخصصة:</div>
                            <div class="card border-0 bg-light">
                                <div class="card-body py-2 px-3">
                                    @php
                                        // تحديد نوع النصائح بناءً على مستوى أداء الطالب
                                        $lowCompletionRate = $student->courses_enrolled > 0 && $student->course_performance->where('completed', true)->count() / $student->courses_enrolled < 0.3;
                                        $lowExamPassRate = $student->exams_taken > 0 && $student->exams_passed / $student->exams_taken < 0.5;
                                        $inactiveStudent = !$hasRecentActivity;
                                        $excellentStudent = $student->performance_score >= 85;
                                        $goodStudent = $student->performance_score >= 70 && $student->performance_score < 85;
                                        $averageStudent = $student->performance_score >= 50 && $student->performance_score < 70;
                                        $strugglingStudent = $student->performance_score < 50;
                                    @endphp
                                    
                                    @if($excellentStudent)
                                        <div class="recommendation-item">
                                            <i class="fas fa-trophy text-success me-2"></i>
                                            <strong>طالب متميز:</strong> يمكن تشجيعه على الانضمام لدورات متقدمة أو مساعدة زملائه.
                                        </div>
                                        @if($student->courses_enrolled < 3)
                                            <div class="recommendation-item mt-2">
                                                <i class="fas fa-level-up-alt text-primary me-2"></i>
                                                اقترح عليه المزيد من الدورات المتقدمة في نفس المجال لتطوير مهاراته.
                                            </div>
                                        @endif
                                    @endif
                                    
                                    @if($goodStudent)
                                        <div class="recommendation-item">
                                            <i class="fas fa-thumbs-up text-primary me-2"></i>
                                            <strong>أداء جيد:</strong> يمكن تحفيزه من خلال تقديم تحديات إضافية وأنشطة تفاعلية.
                                        </div>
                                        @if($lowExamPassRate)
                                            <div class="recommendation-item mt-2">
                                                <i class="fas fa-pen-alt text-warning me-2"></i>
                                                يحتاج إلى دعم إضافي في الامتحانات، يُقترح تقديم نماذج امتحانات تدريبية.
                                            </div>
                                        @endif
                                    @endif
                                    
                                    @if($averageStudent)
                                        <div class="recommendation-item">
                                            <i class="fas fa-adjust text-info me-2"></i>
                                            <strong>أداء متوسط:</strong> يحتاج إلى متابعة منتظمة ومساعدة في فهم المفاهيم الصعبة.
                                        </div>
                                        @if($lowCompletionRate)
                                            <div class="recommendation-item mt-2">
                                                <i class="fas fa-tasks text-warning me-2"></i>
                                                نسبة إكمال الدورات منخفضة، يُقترح تشجيعه على إكمال دورة واحدة قبل البدء بأخرى.
                                            </div>
                                        @endif
                                        @if($lowExamPassRate)
                                            <div class="recommendation-item mt-2">
                                                <i class="fas fa-pen-fancy text-warning me-2"></i>
                                                أداؤه في الامتحانات يحتاج إلى تحسين، يُقترح جلسات مراجعة دورية.
                                            </div>
                                        @endif
                                    @endif
                                    
                                    @if($strugglingStudent)
                                        <div class="recommendation-item">
                                            <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                            <strong>يحتاج إلى دعم عاجل:</strong> يُوصى بجلسة توجيه فردية لمعرفة العوائق التي يواجهها.
                                        </div>
                                        <div class="recommendation-item mt-2">
                                            <i class="fas fa-user-check text-warning me-2"></i>
                                            تقديم مواد تعليمية إضافية مبسطة وتقسيم المحتوى إلى أجزاء أصغر.
                                        </div>
                                    @endif
                                    
                                    @if($inactiveStudent)
                                        <div class="recommendation-item mt-2">
                                            <i class="fas fa-bell text-danger me-2"></i>
                                            الطالب غير نشط حالياً، يُقترح التواصل معه للاطمئنان ومعرفة أسباب عدم النشاط.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- أزرار التفاعل -->
                        <div class="d-grid gap-2">
                            <button class="btn btn-sm btn-primary">
                                <i class="fas fa-envelope me-1"></i> مراسلة الطالب
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#studentDetailedAnalysis{{ $student->user_id }}">
                                <i class="fas fa-chart-bar me-1"></i> تحليل تفصيلي
                            </button>
                        </div>
                        
                        <!-- قسم التحليل التفصيلي (مخفي) -->
                        <div class="collapse mt-3" id="studentDetailedAnalysis{{ $student->user_id }}">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">تحليل أداء مفصل للطالب {{ $student->name }}</h6>
                                </div>
                                <div class="card-body">
                                    <!-- الامتحانات ونتائجها -->
                                    <div class="mb-3">
                                        <h6 class="fw-bold text-uppercase small text-muted mb-2">
                                            <i class="fas fa-clipboard-check me-1"></i> سجل الامتحانات
                                        </h6>
                                        
                                        @if($student->exams_taken > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>الامتحان</th>
                                                            <th>الدرجة</th>
                                                            <th>النتيجة</th>
                                                            <th>التاريخ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($student->exam_attempts ?? [] as $attempt)
                                                            <tr>
                                                                <td>{{ $attempt['exam_title'] ?? 'امتحان' }}</td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="progress flex-grow-1 me-2" style="height: 5px;">
                                                                            <div class="progress-bar {{ $attempt['score'] >= 80 ? 'bg-success' : ($attempt['score'] >= 60 ? 'bg-info' : ($attempt['score'] >= 40 ? 'bg-warning' : 'bg-danger')) }}" 
                                                                                 role="progressbar" style="width: {{ $attempt['score'] }}%"></div>
                                                                        </div>
                                                                        <span>{{ $attempt['score'] }}%</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @if($attempt['is_passed'] ?? false)
                                                                        <span class="badge bg-success">اجتياز</span>
                                                                    @else
                                                                        <span class="badge bg-danger">لم يجتز</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $attempt['attempt_date'] ? \Carbon\Carbon::parse($attempt['attempt_date'])->format('Y-m-d') : 'غير متوفر' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-light border text-center py-2">
                                                <i class="fas fa-info-circle me-1"></i> لم يقم الطالب بأخذ أي امتحانات بعد
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- مخطط تقدم الطالب -->
                                    <div class="mb-3">
                                        <h6 class="fw-bold text-uppercase small text-muted mb-2">
                                            <i class="fas fa-chart-line me-1"></i> تحليل تطور الأداء
                                        </h6>
                                        
                                        <div class="performance-analysis">
                                            <div class="d-flex justify-content-between">
                                                <div class="analysis-metric">
                                                    <div class="analysis-label">النقاط القوية</div>
                                                    <ul class="list-unstyled mb-0 small">
                                                        @if($student->avg_score >= 70)
                                                            <li><i class="fas fa-check-circle text-success me-1"></i> درجات امتحانات عالية</li>
                                                        @endif
                                                        
                                                        @if($student->exams_taken > 0 && $student->exams_passed / $student->exams_taken >= 0.7)
                                                            <li><i class="fas fa-check-circle text-success me-1"></i> معدل نجاح جيد في الاختبارات</li>
                                                        @endif
                                                        
                                                        @if($student->course_performance->where('progress', '>=', 75)->count() > 0)
                                                            <li><i class="fas fa-check-circle text-success me-1"></i> تقدم جيد في {{ $student->course_performance->where('progress', '>=', 75)->count() }} دورات</li>
                                                        @endif
                                                        
                                                        @if(!$student->avg_score >= 70 && !($student->exams_taken > 0 && $student->exams_passed / $student->exams_taken >= 0.7) && !($student->course_performance->where('progress', '>=', 75)->count() > 0))
                                                            <li><i class="fas fa-info-circle text-muted me-1"></i> بيانات غير كافية</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                
                                                <div class="analysis-metric">
                                                    <div class="analysis-label">مجالات التحسين</div>
                                                    <ul class="list-unstyled mb-0 small">
                                                        @if($student->avg_score < 60 && $student->exams_taken > 0)
                                                            <li><i class="fas fa-exclamation-circle text-danger me-1"></i> درجات امتحانات منخفضة</li>
                                                        @endif
                                                        
                                                        @if($student->exams_taken > 0 && $student->exams_passed / $student->exams_taken < 0.5)
                                                            <li><i class="fas fa-exclamation-circle text-danger me-1"></i> معدل نجاح منخفض في الاختبارات</li>
                                                        @endif
                                                        
                                                        @if($lowCompletionRate)
                                                            <li><i class="fas fa-exclamation-circle text-danger me-1"></i> معدل إكمال الدورات منخفض</li>
                                                        @endif
                                                        
                                                        @if($inactiveStudent)
                                                            <li><i class="fas fa-exclamation-circle text-danger me-1"></i> نشاط ضعيف على المنصة</li>
                                                        @endif
                                                        
                                                        @if(!($student->avg_score < 60 && $student->exams_taken > 0) && !($student->exams_taken > 0 && $student->exams_passed / $student->exams_taken < 0.5) && !$lowCompletionRate && !$inactiveStudent)
                                                            <li><i class="fas fa-check-circle text-success me-1"></i> لا توجد مشاكل كبيرة</li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- خطة العمل المقترحة -->
                                    <div>
                                        <h6 class="fw-bold text-uppercase small text-muted mb-2">
                                            <i class="fas fa-tasks me-1"></i> خطة العمل المقترحة
                                        </h6>
                                        
                                        <div class="action-plan bg-light p-3 rounded">
                                            <ol class="mb-0 ps-3 small">
                                                @if($excellentStudent)
                                                    <li class="mb-1">تقديم محتوى متقدم ومشاريع إضافية لتحدي قدراته.</li>
                                                    <li class="mb-1">دعوته للمشاركة في منتديات النقاش ومساعدة الطلاب الآخرين.</li>
                                                    <li class="mb-1">اقتراح دورات متقدمة في نفس المجال أو مجالات مكملة.</li>
                                                @elseif($goodStudent)
                                                    <li class="mb-1">تقديم ملاحظات مفصلة على أدائه في الامتحانات لتحسين النقاط الضعيفة.</li>
                                                    <li class="mb-1">تشجيعه على إكمال الدورات المتبقية بجدول زمني منتظم.</li>
                                                    @if($lowExamPassRate)
                                                        <li class="mb-1">توفير أسئلة تدريبية إضافية ونصائح لتحسين أدائه في الامتحانات.</li>
                                                    @endif
                                                @elseif($averageStudent)
                                                    <li class="mb-1">جدولة جلسة متابعة دورية للتأكد من فهمه للمفاهيم الأساسية.</li>
                                                    <li class="mb-1">تقديم مصادر تعليمية إضافية تناسب أسلوب تعلمه.</li>
                                                    <li class="mb-1">تقسيم المحتوى التعليمي إلى أجزاء أصغر وأكثر تركيزًا.</li>
                                                    @if($lowExamPassRate)
                                                        <li class="mb-1">تقديم دروس مراجعة مركزة قبل الامتحانات.</li>
                                                    @endif
                                                @elseif($strugglingStudent)
                                                    <li class="mb-1">جدولة جلسة توجيه فردية لتحديد العوائق التي يواجهها.</li>
                                                    <li class="mb-1">تقديم مواد تعليمية أساسية ومبسطة لبناء أساس متين.</li>
                                                    <li class="mb-1">وضع خطة تعلم مخصصة مع أهداف قصيرة المدى قابلة للتحقيق.</li>
                                                    <li class="mb-1">متابعة أسبوعية للتأكد من تقدمه وتقديم المساعدة اللازمة.</li>
                                                @endif
                                                
                                                @if($inactiveStudent)
                                                    <li class="mb-1 text-danger">التواصل مع الطالب عبر البريد الإلكتروني للاطمئنان ومعرفة أسباب عدم نشاطه.</li>
                                                @endif
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent text-end">
                                    <button class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-file-export me-1"></i> تصدير التقرير
                                    </button>
                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-share me-1"></i> مشاركة مع الطالب
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    لا يوجد بيانات كافية لعرض تحليل أداء الطلاب. يمكن أن يكون ذلك لأن طلابك لم يكملوا أي امتحانات بعد.
                </div>
            </div>
        @endforelse
    </div>
    
    <!-- Exam Statistics Table -->
    @if(count($examStats) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i> إحصائيات الامتحانات للطلاب المتميزين</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>الامتحان</th>
                                    <th>الدورة</th>
                                    <th>عدد المشاركين</th>
                                    <th>متوسط الدرجات</th>
                                    <th>أعلى درجة</th>
                                    <th>أدنى درجة</th>
                                    <th>عدد الناجحين</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($examStats as $stat)
                                <tr>
                                    <td>{{ $stat->exam_title }}</td>
                                    <td>{{ $stat->course_title }}</td>
                                    <td>{{ $stat->participants }}</td>
                                    <td>{{ number_format($stat->avg_score, 1) }}%</td>
                                    <td>{{ number_format($stat->max_score, 1) }}%</td>
                                    <td>{{ number_format($stat->min_score, 1) }}%</td>
                                    <td>{{ $stat->passed_count }} ({{ $stat->participants > 0 ? number_format(($stat->passed_count / $stat->participants) * 100, 0) : 0 }}%)</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
        // Filter functionality
        const courseFilter = document.getElementById('courseFilter');
        const performanceFilter = document.getElementById('performanceFilter');
        const examFilter = document.getElementById('examFilter');
        const studentsContainer = document.getElementById('studentsContainer');
        
        function applyFilters() {
            const courseValue = courseFilter.value;
            const performanceValue = performanceFilter.value;
            const examValue = examFilter.value;
            
            const studentCards = document.querySelectorAll('.student-card');
            
            studentCards.forEach(card => {
                let shouldShow = true;
                
                // Course filter
                if (courseValue !== 'all') {
                    const studentCourses = card.dataset.courses.split(',');
                    if (!studentCourses.includes(courseValue)) {
                        shouldShow = false;
                    }
                }
                
                // Performance filter
                const performanceScore = parseFloat(card.dataset.performance);
                if (performanceValue === 'excellent' && performanceScore < 80) shouldShow = false;
                if (performanceValue === 'good' && (performanceScore < 70 || performanceScore >= 80)) shouldShow = false;
                if (performanceValue === 'average' && (performanceScore < 60 || performanceScore >= 70)) shouldShow = false;
                if (performanceValue === 'needs-improvement' && performanceScore >= 60) shouldShow = false;
                
                // Exam filter
                const examsPassed = parseInt(card.dataset.examsPassed);
                const examsTaken = parseInt(card.dataset.examsTaken);
                
                if (examValue === 'passed' && examsPassed === 0) shouldShow = false;
                if (examValue === 'failed' && (examsPassed > 0 || examsTaken === 0)) shouldShow = false;
                if (examValue === 'not-taken' && examsTaken > 0) shouldShow = false;
                
                // Apply visibility
                card.style.display = shouldShow ? 'block' : 'none';
            });
        }
        
        // Add event listeners to filters
        courseFilter.addEventListener('change', applyFilters);
        performanceFilter.addEventListener('change', applyFilters);
        examFilter.addEventListener('change', applyFilters);
        
        // Exam Performance Chart
        @if(count($examStats) > 0)
        const ctx = document.getElementById('examPerformanceChart').getContext('2d');
        
        const examLabels = [
            @foreach($examStats as $stat)
                '{{ $stat->exam_title }}',
            @endforeach
        ];
        
        const avgScores = [
            @foreach($examStats as $stat)
                {{ $stat->avg_score }},
            @endforeach
        ];
        
        const passRates = [
            @foreach($examStats as $stat)
                {{ $stat->participants > 0 ? ($stat->passed_count / $stat->participants) * 100 : 0 }},
            @endforeach
        ];
        
        const examPerformanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: examLabels,
                datasets: [
                    {
                        label: 'متوسط الدرجات (%)',
                        data: avgScores,
                        backgroundColor: 'rgba(32, 201, 151, 0.6)',
                        borderColor: '#20c997',
                        borderWidth: 2
                    },
                    {
                        label: 'نسبة النجاح (%)',
                        data: passRates,
                        backgroundColor: 'rgba(13, 110, 253, 0.6)',
                        borderColor: '#0d6efd',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection 