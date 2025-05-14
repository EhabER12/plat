@extends('layouts.app')

@section('title', 'نشاط الطالب - ' . $student->name)

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<style>
    :root {
        --primary-color: #003366;
        --secondary-color: #FFD700;
        --accent-color: #FFD700;
        --background-color: #FAFAFA;
        --text-color: #1F1F1F;
        --border-color: #003366;
    }

    .student-activity {
        padding: 30px 0;
    }
    
    .profile-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #002244 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 20px;
        border: 4px solid var(--secondary-color);
    }
    
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .profile-header::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 215, 0, 0.1);
        border-radius: 50%;
        z-index: 1;
    }
    
    .course-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid #eee;
    }
    
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
    }
    
    .course-card .card-header {
        background-color: var(--primary-color);
        color: white;
        padding: 15px 20px;
        border-bottom: none;
    }
    
    .course-card .course-progress {
        padding: 15px;
        display: flex;
        align-items: center;
    }
    
    .course-card .progress-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: conic-gradient(var(--primary-color) var(--percentage), #f1f1f1 0);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        margin-right: 20px;
    }
    
    .course-card .progress-circle::before {
        content: '';
        position: absolute;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: white;
    }
    
    .course-card .progress-value {
        position: relative;
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--primary-color);
    }
    
    .course-card .progress-details {
        flex-grow: 1;
    }
    
    .course-card .progress-details h4 {
        font-size: 1.2rem;
        margin-bottom: 8px;
        color: var(--primary-color);
    }
    
    .course-card .progress-details p {
        margin-bottom: 5px;
        color: #666;
        font-size: 0.9rem;
    }
    
    .exam-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 15px;
        border: 1px solid #eee;
        transition: all 0.3s ease;
    }
    
    .exam-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    
    .exam-card .card-body {
        padding: 15px;
    }
    
    .exam-card .exam-title {
        font-size: 1.1rem;
        color: var(--primary-color);
        margin-bottom: 8px;
    }
    
    .exam-card .exam-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .exam-card .exam-score {
        background-color: #f8f9fa;
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .exam-card .exam-score.pass {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
    
    .exam-card .exam-score.fail {
        background-color: #ffebee;
        color: #c62828;
    }
    
    .activity-timeline {
        position: relative;
    }
    
    .activity-timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e9ecef;
        right: 20px;
    }
    
    .timeline-item {
        position: relative;
        padding-right: 45px;
        margin-bottom: 20px;
    }
    
    .timeline-icon {
        position: absolute;
        top: 0;
        right: 11px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: white;
        border: 2px solid var(--primary-color);
        z-index: 1;
    }
    
    .timeline-content {
        background-color: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .timeline-content h4 {
        font-size: 1rem;
        margin-bottom: 8px;
    }
    
    .timeline-content p {
        margin-bottom: 5px;
        color: #666;
        font-size: 0.9rem;
    }
    
    .timeline-time {
        color: #999;
        font-size: 0.8rem;
    }
    
    .section-title {
        margin-bottom: 20px;
        color: var(--primary-color);
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .video-progress-item {
        padding: 12px 15px;
        border-bottom: 1px solid #f5f5f5;
    }
    
    .video-progress-item:last-child {
        border-bottom: none;
    }
    
    .video-title {
        font-weight: 500;
        margin-bottom: 8px;
    }
    
    .badge-section {
        padding: 20px 0;
    }
    
    .badge-section h3 {
        margin-bottom: 20px;
    }
    
    .badge-item {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .badge-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        background-color: #f5f5f5;
        color: var(--primary-color);
        font-size: 2rem;
    }
    
    .badge-icon.completed {
        background-color: #e8f5e9;
        color: #2e7d32;
    }
    
    .badge-icon.progress {
        background-color: #fff8e1;
        color: #ff8f00;
    }
    
    .badge-icon.locked {
        background-color: #f5f5f5;
        color: #9e9e9e;
    }
    
    .badge-title {
        font-size: 0.9rem;
        color: #333;
        margin-bottom: 5px;
    }
    
    .badge-desc {
        font-size: 0.8rem;
        color: #666;
    }
    
    .progress-container {
        margin-bottom: 20px;
    }
    
    .progress-title {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    
    .progress-title .label {
        font-weight: 500;
    }
    
    .progress-title .value {
        color: #666;
    }
    
    .progress {
        height: 10px;
        border-radius: 5px;
        margin-bottom: 5px;
    }
    
    .progress-bar {
        background-color: var(--primary-color);
    }
    
    .notification-box {
        background-color: #FFF8E1;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        border-right: 4px solid #FFC107;
    }
    
    .notification-box.alert {
        background-color: #FFF5F5;
        border-right-color: #FF3B30;
    }
    
    .notification-box.info {
        background-color: #E3F2FD;
        border-right-color: #2196F3;
    }
    
    .notification-box h4 {
        font-size: 1.1rem;
        margin-bottom: 10px;
        color: #333;
    }
    
    .notification-box p {
        margin-bottom: 0;
        color: #555;
    }
    
    .attendance-table {
        width: 100%;
        margin-bottom: 20px;
    }
    
    .attendance-table th,
    .attendance-table td {
        padding: 12px 15px;
        text-align: center;
    }
    
    .attendance-table thead th {
        background-color: var(--primary-color);
        color: white;
        font-weight: 500;
    }
    
    .attendance-table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    
    .attendance-status {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    
    .attendance-status.present {
        background-color: #4CAF50;
    }
    
    .attendance-status.absent {
        background-color: #F44336;
    }
    
    .attendance-status.late {
        background-color: #FF9800;
    }
</style>
@endsection

@section('content')
<div class="container student-activity">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right me-1"></i> العودة إلى لوحة التحكم
        </a>
    </div>
    
    <!-- Profile Header -->
    <div class="profile-header mb-4">
        <div class="row">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="profile-avatar">
                        @if($student->profile_picture)
                        <img src="{{ asset($student->profile_picture) }}" alt="{{ $student->name }}">
                        @else
                        <img src="{{ asset('img/default-avatar.png') }}" alt="{{ $student->name }}">
                        @endif
                    </div>
                    <div>
                        <h2 class="mb-1">{{ $student->name }}</h2>
                        <p class="mb-1"><i class="fas fa-envelope me-1"></i> {{ $student->email }}</p>
                        @if($student->phone)
                        <p class="mb-1"><i class="fas fa-phone me-1"></i> {{ $student->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="stats text-center mt-3">
                    <div class="row">
                        <div class="col-4">
                            <h2>{{ count($enrolledCourses) }}</h2>
                            <p>الدورات</p>
                        </div>
                        <div class="col-4">
                            <h2>{{ $completedExams }}</h2>
                            <p>الاختبارات</p>
                        </div>
                        <div class="col-4">
                            <h2>{{ $avgScore }}%</h2>
                            <p>متوسط الدرجات</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alert Notifications -->
    @if(count($alerts) > 0)
    <div class="mb-4">
        <h3 class="section-title"><i class="fas fa-bell me-2"></i>تنبيهات وملاحظات</h3>
        @foreach($alerts as $alert)
        <div class="notification-box {{ $alert['type'] }}">
            <h4><i class="fas fa-exclamation-circle me-2"></i>{{ $alert['title'] }}</h4>
            <p>{{ $alert['message'] }}</p>
            <small class="text-muted d-block mt-2">{{ $alert['time'] }}</small>
        </div>
        @endforeach
    </div>
    @endif
    
    <div class="row">
        <!-- Course Progress -->
        <div class="col-md-8">
            <h3 class="section-title"><i class="fas fa-graduation-cap me-2"></i>تقدم الدورات</h3>
            
            @if(count($enrolledCourses) > 0)
                @foreach($enrolledCourses as $course)
                <div class="course-card">
                    <div class="card-header">
                        <h3 class="mb-0">{{ $course->title }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="course-progress">
                            <div class="progress-circle" style="--percentage: {{ $course->pivot->progress ?? $course->progress ?? 0 }}%;">
                                <div class="progress-value">{{ $course->pivot->progress ?? $course->progress ?? 0 }}%</div>
                            </div>
                            <div class="progress-details">
                                <h4>{{ $course->title }}</h4>
                                <p><i class="fas fa-chalkboard-teacher me-1"></i> المدرس: {{ $course->instructor->name ?? 'غير محدد' }}</p>
                                <p><i class="fas fa-calendar-alt me-1"></i> تاريخ الالتحاق: {{ \Carbon\Carbon::parse($course->pivot->enrolled_at ?? $course->enrolled_at)->format('Y/m/d') }}</p>
                                @if(isset($course->pivot->last_activity_at) || isset($course->last_activity_at))
                                <p><i class="fas fa-clock me-1"></i> آخر نشاط: {{ \Carbon\Carbon::parse($course->pivot->last_activity_at ?? $course->last_activity_at)->diffForHumans() }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3">محتوى الدورة</h5>
                        
                        @if(count($courseContents[$course->course_id]) > 0)
                        <div class="videos-progress">
                            @foreach($courseContents[$course->course_id] as $content)
                            <div class="video-progress-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="video-title">{{ $content['title'] }}</h6>
                                        <p class="mb-1 text-muted">{{ $content['type'] }} • {{ $content['duration'] }}</p>
                                    </div>
                                    <span class="badge bg-{{ $content['status_color'] }}">{{ $content['status'] }}</span>
                                </div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $content['progress'] }}%" aria-valuenow="{{ $content['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-muted">لا يوجد محتوى متاح بعد.</p>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> الطالب غير مسجل في أي دورات حالياً.
            </div>
            @endif
        </div>
        
        <!-- Exams and Activities -->
        <div class="col-md-4">
            <h3 class="section-title"><i class="fas fa-tasks me-2"></i>الاختبارات</h3>
            
            @if(count($examAttempts) > 0)
                @foreach($examAttempts as $attempt)
                <div class="exam-card">
                    <div class="card-body">
                        <h5 class="exam-title">{{ $attempt['title'] }}</h5>
                        <div class="exam-details">
                            <span class="text-muted">{{ $attempt['date'] }}</span>
                            <span class="exam-score {{ $attempt['passed'] ? 'pass' : 'fail' }}">
                                {{ $attempt['score'] }}%
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> الطالب لم يخضع لأي اختبارات بعد.
            </div>
            @endif
            
            <h3 class="section-title mt-4"><i class="fas fa-history me-2"></i>النشاط الأخير</h3>
            
            @if(count($activities) > 0)
                <div class="activity-timeline">
                    @foreach($activities as $activity)
                    <div class="timeline-item">
                        <div class="timeline-icon"></div>
                        <div class="timeline-content">
                            <h4>{{ $activity['title'] }}</h4>
                            <p>{{ $activity['description'] }}</p>
                            <span class="timeline-time">{{ $activity['time'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> لا يوجد نشاط مسجل للطالب.
            </div>
            @endif
            
            <h3 class="section-title mt-4"><i class="fas fa-medal me-2"></i>الإنجازات والشارات</h3>
            
            <div class="row">
                @foreach($badges as $badge)
                <div class="col-4">
                    <div class="badge-item">
                        <div class="badge-icon {{ $badge['status'] }}">
                            <i class="fas {{ $badge['icon'] }}"></i>
                        </div>
                        <h6 class="badge-title">{{ $badge['title'] }}</h6>
                        <p class="badge-desc">{{ $badge['description'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Any additional JavaScript can go here
</script>
@endsection 