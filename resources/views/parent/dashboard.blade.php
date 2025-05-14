@extends('layouts.app')

@section('title', 'لوحة تحكم ولي الأمر')

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
    
    .parent-dashboard {
        padding: 30px 0;
    }
    
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #002244 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-header h1 {
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .dashboard-header p {
        font-size: 1.1rem;
        opacity: 0.8;
    }
    
    .dashboard-header::after {
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
    
    .quick-actions {
        display: flex;
        margin-top: 20px;
    }
    
    .quick-action-button {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 8px 15px;
        border-radius: 5px;
        margin-left: 10px;
        text-decoration: none;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .quick-action-button:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }
    
    .quick-action-button i {
        margin-left: 5px;
    }
    
    .student-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid #eee;
    }
    
    .student-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .student-card .card-header {
        background-color: var(--primary-color);
        color: white;
        padding: 15px 20px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .student-card .card-body {
        padding: 20px;
    }
    
    .student-card .student-info {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .student-card .student-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        overflow: hidden;
        margin-left: 15px;
        border: 3px solid var(--secondary-color);
    }
    
    .student-card .student-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .student-card .student-details h3 {
        font-size: 1.3rem;
        margin-bottom: 5px;
        color: var(--primary-color);
    }
    
    .student-card .stat-box {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        margin-bottom: 15px;
    }
    
    .student-card .stat-box .stat-value {
        font-size: 1.8rem;
        font-weight: bold;
        color: var(--primary-color);
        margin-bottom: 5px;
    }
    
    .student-card .stat-box .stat-label {
        font-size: 0.9rem;
        color: #666;
    }
    
    .latest-activity {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        padding: 20px;
        margin-bottom: 25px;
    }
    
    .latest-activity h3 {
        color: var(--primary-color);
        margin-bottom: 20px;
        font-size: 1.2rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .activity-item {
        padding: 12px 0;
        border-bottom: 1px solid #f5f5f5;
        display: flex;
        align-items: center;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 15px;
        color: white;
    }
    
    .activity-icon.login {
        background-color: #4CAF50;
    }
    
    .activity-icon.course {
        background-color: #2196F3;
    }
    
    .activity-icon.exam {
        background-color: #FF9800;
    }
    
    .activity-details {
        flex-grow: 1;
    }
    
    .activity-details p {
        margin-bottom: 3px;
        font-size: 0.95rem;
    }
    
    .activity-time {
        color: #999;
        font-size: 0.85rem;
    }
    
    .alert-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    
    .alert-indicator.high {
        background-color: #F44336;
    }
    
    .alert-indicator.medium {
        background-color: #FF9800;
    }
    
    .alert-indicator.low {
        background-color: #4CAF50;
    }
    
    .alert-card {
        background-color: #FFF7E5;
        border-right: 4px solid #FF9800;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
    }
    
    .alert-card.high {
        background-color: #FFEBEE;
        border-right-color: #F44336;
    }
    
    .alert-card.low {
        background-color: #E8F5E9;
        border-right-color: #4CAF50;
    }
    
    .alert-card h4 {
        color: #333;
        font-size: 1.1rem;
        margin-bottom: 8px;
    }
    
    .alerts-section {
        margin-bottom: 30px;
    }
    
    .pending-verification {
        background-color: #FFFDE7;
        border: 1px dashed #FFC107;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .rejected-verification {
        background-color: #FFEBEE;
        border: 1px dashed #F44336;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .view-all-btn {
        display: block;
        text-align: center;
        padding: 10px;
        background-color: #f5f5f5;
        color: #666;
        border-radius: 5px;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .view-all-btn:hover {
        background-color: #e9e9e9;
        color: #333;
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
        margin-bottom: 15px;
    }
    
    .progress-bar {
        background-color: var(--primary-color);
    }
</style>
@endsection

@section('content')
<div class="container parent-dashboard">
    <div class="dashboard-header">
        <h1>مرحباً بك، {{ Auth::user()->name }}</h1>
        <p>من هنا يمكنك متابعة أبنائك ورصد تقدمهم في الدراسة.</p>
        
        <div class="quick-actions">
            <a href="{{ route('parent.profile') }}" class="quick-action-button">
                <i class="fas fa-user-circle"></i> الملف الشخصي
            </a>
            <a href="{{ route('parent.link-request') }}" class="quick-action-button">
                <i class="fas fa-link"></i> ربط طالب جديد
            </a>
            <a href="{{ route('parent.activities') }}" class="quick-action-button">
                <i class="fas fa-clock"></i> سجل النشاطات
            </a>
        </div>
    </div>
    
    @if(count($pendingRelations) > 0)
    <div class="pending-verification mb-4">
        <h3><i class="fas fa-exclamation-circle text-warning me-2"></i> طلبات في انتظار التحقق</h3>
        <p>لديك {{ count($pendingRelations) }} من طلبات التحقق من علاقة ولي الأمر-الطالب في انتظار المراجعة من قبل الإدارة.</p>
        <ul class="list-group mt-3">
            @foreach($pendingRelations as $relation)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>{{ $relation->student_name }}</span>
                <span class="badge bg-warning">في انتظار التحقق</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
    
    @if(count($rejectedRelations) > 0)
    <div class="rejected-verification mb-4">
        <h3><i class="fas fa-times-circle text-danger me-2"></i> طلبات مرفوضة</h3>
        <p>تم رفض {{ count($rejectedRelations) }} من طلبات التحقق من علاقة ولي الأمر-الطالب.</p>
        <ul class="list-group mt-3">
            @foreach($rejectedRelations as $relation)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <span>{{ $relation->student_name }}</span>
                    @if($relation->verification_notes)
                    <p class="text-muted small mt-1">{{ $relation->verification_notes }}</p>
                    @endif
                </div>
                <span class="badge bg-danger">مرفوض</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
    
    @if(count($students) > 0)
        <div class="alerts-section">
            <h3 class="section-title"><i class="fas fa-bell me-2"></i>تنبيهات هامة</h3>
            <div class="row">
                @foreach($alerts as $alert)
                <div class="col-md-6">
                    <div class="alert-card {{ $alert['priority'] }}">
                        <div class="d-flex align-items-start">
                            <div class="alert-indicator {{ $alert['priority'] }} mt-2 ms-2"></div>
                            <div>
                                <h4>{{ $alert['title'] }}</h4>
                                <p class="mb-0">{{ $alert['message'] }}</p>
                                <small class="text-muted">{{ $alert['student_name'] }} - {{ $alert['time'] }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <div class="row">
            @foreach($students as $student)
            <div class="col-md-6">
                <div class="student-card">
                    <div class="card-header">
                        <h3 class="mb-0">معلومات الطالب</h3>
                        <a href="{{ route('parent.student-activity', $student->user_id) }}" class="btn btn-sm" style="background-color: var(--secondary-color); color: var(--primary-color);">
                            <i class="fas fa-external-link-alt me-1"></i> عرض التفاصيل
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="student-info">
                            <div class="student-avatar">
                                @if($student->profile_picture)
                                <img src="{{ asset($student->profile_picture) }}" alt="{{ $student->name }}">
                                @else
                                <img src="{{ asset('img/default-avatar.png') }}" alt="{{ $student->name }}">
                                @endif
                            </div>
                            <div class="student-details">
                                <h3>{{ $student->name }}</h3>
                                <p class="mb-1"><i class="fas fa-envelope me-1"></i> {{ $student->email }}</p>
                                @if($student->phone)
                                <p class="mb-0"><i class="fas fa-phone me-1"></i> {{ $student->phone }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-4">
                                <div class="stat-box">
                                    <div class="stat-value">{{ $studentStats[$student->user_id]['courses_count'] }}</div>
                                    <div class="stat-label">الدورات</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box">
                                    <div class="stat-value">{{ $studentStats[$student->user_id]['exams_count'] }}</div>
                                    <div class="stat-label">الاختبارات</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box">
                                    <div class="stat-value">{{ $studentStats[$student->user_id]['avg_progress'] }}%</div>
                                    <div class="stat-label">التقدم</div>
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="mt-4 mb-3">آخر الدورات</h4>
                        @if(count($studentStats[$student->user_id]['recent_courses']) > 0)
                            @foreach($studentStats[$student->user_id]['recent_courses'] as $course)
                            <div class="mb-3">
                                <div class="progress-title">
                                    <span class="label">{{ $course['title'] }}</span>
                                    <span class="value">{{ $course['progress'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $course['progress'] }}%" aria-valuenow="{{ $course['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted">آخر نشاط: {{ $course['last_activity'] }}</small>
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted">لم يشترك في أي دورات بعد.</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="latest-activity">
                    <h3><i class="fas fa-history me-2"></i>آخر الأنشطة</h3>
                    
                    @if(count($recentActivities) > 0)
                        @foreach($recentActivities as $activity)
                        <div class="activity-item">
                            <div class="activity-icon {{ $activity['type'] }}">
                                @if($activity['type'] == 'login')
                                    <i class="fas fa-sign-in-alt"></i>
                                @elseif($activity['type'] == 'course')
                                    <i class="fas fa-book"></i>
                                @elseif($activity['type'] == 'exam')
                                    <i class="fas fa-tasks"></i>
                                @endif
                            </div>
                            <div class="activity-details">
                                <p><strong>{{ $activity['student_name'] }}</strong> {{ $activity['description'] }}</p>
                                <span class="activity-time">{{ $activity['time'] }}</span>
                            </div>
                        </div>
                        @endforeach
                        <a href="{{ route('parent.activities') }}" class="view-all-btn mt-3">عرض كل الأنشطة</a>
                    @else
                        <p class="text-muted">لا توجد أنشطة حديثة.</p>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i>لا يوجد طلاب مرتبطين</h4>
            <p>ليس لديك أي طلاب مرتبطين بحسابك حالياً. يرجى التواصل مع إدارة المنصة لربط حسابك بحسابات أبنائك الطلاب.</p>
        </div>
        
        <div class="card mt-4">
            <div class="card-body">
                <h4>كيفية ربط حسابك بحسابات الطلاب؟</h4>
                <ol>
                    <li>تأكد من امتلاك أبنائك لحسابات طالب مفعلة على المنصة.</li>
                    <li>قم بتقديم طلب ربط من خلال <a href="{{ route('parent.student.link.request') }}">نموذج طلب الربط</a>.</li>
                    <li>سيتم مراجعة طلبك من قبل الإدارة والرد عليه خلال 24 ساعة.</li>
                    <li>بعد الموافقة على الطلب، ستتمكن من رؤية حسابات أبنائك الطلاب ومتابعة تقدمهم.</li>
                </ol>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Any additional JavaScript can go here
</script>
@endsection 