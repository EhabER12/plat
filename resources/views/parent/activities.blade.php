@extends('layouts.app')

@section('title', 'سجل الأنشطة')

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
    
    .activities-container {
        padding: 30px 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #002244 100%);
        color: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }
    
    .page-header h1 {
        font-weight: bold;
        margin-bottom: 10px;
    }
    
    .page-header p {
        font-size: 1.1rem;
        opacity: 0.8;
    }
    
    .page-header::after {
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
    
    .filter-bar {
        background-color: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .activity-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 15px;
        border: 1px solid #eee;
        transition: all 0.3s ease;
    }
    
    .activity-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    
    .activity-card .card-body {
        padding: 15px;
    }
    
    .activity-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 15px;
        color: white;
        font-size: 1.2rem;
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
    
    .activity-icon.quiz {
        background-color: #9C27B0;
    }
    
    .activity-icon.material {
        background-color: #00BCD4;
    }
    
    .activity-icon.certificate {
        background-color: #F44336;
    }
    
    .activity-content {
        flex-grow: 1;
    }
    
    .activity-title {
        font-size: 1.1rem;
        margin-bottom: 5px;
        color: var(--primary-color);
    }
    
    .activity-details {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }
    
    .activity-time {
        color: #999;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
    }
    
    .student-badge {
        background-color: #e3f2fd;
        color: #1976D2;
        font-size: 0.85rem;
        padding: 3px 8px;
        border-radius: 12px;
        margin-right: 8px;
    }
    
    .date-separator {
        display: flex;
        align-items: center;
        margin: 20px 0;
        color: #666;
    }
    
    .date-separator::before,
    .date-separator::after {
        content: '';
        flex-grow: 1;
        height: 1px;
        background-color: #ddd;
    }
    
    .date-separator::before {
        margin-left: 15px;
    }
    
    .date-separator::after {
        margin-right: 15px;
    }
    
    .date-text {
        background-color: var(--background-color);
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: 500;
    }
    
    .pagination {
        margin-top: 30px;
        justify-content: center;
    }
    
    .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .pagination .page-link {
        color: var(--primary-color);
    }
    
    .no-activities {
        text-align: center;
        padding: 50px 0;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .no-activities .icon {
        font-size: 3rem;
        color: #ccc;
        margin-bottom: 20px;
    }
    
    .no-activities h3 {
        margin-bottom: 10px;
        color: #555;
    }
    
    .no-activities p {
        color: #777;
        max-width: 500px;
        margin: 0 auto;
    }
</style>
@endsection

@section('content')
<div class="container activities-container">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right me-1"></i> العودة إلى لوحة التحكم
        </a>
    </div>
    
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-md-8">
                <h1>سجل الأنشطة</h1>
                <p>متابعة كافة أنشطة أبنائك الطلاب على المنصة</p>
            </div>
            <div class="col-md-4 text-end">
                <img src="{{ asset('img/activities.svg') }}" alt="Activities" class="img-fluid" style="max-height: 100px;">
            </div>
        </div>
    </div>
    
    <!-- Filter Bar -->
    <div class="filter-bar">
        <form action="{{ route('parent.activities') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="student" class="form-label">الطالب</label>
                <select name="student" id="student" class="form-select">
                    <option value="">جميع الطلاب</option>
                    @foreach($students as $student)
                    <option value="{{ $student->user_id }}" {{ request('student') == $student->user_id ? 'selected' : '' }}>
                        {{ $student->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="activity_type" class="form-label">نوع النشاط</label>
                <select name="activity_type" id="activity_type" class="form-select">
                    <option value="">جميع الأنشطة</option>
                    <option value="login" {{ request('activity_type') == 'login' ? 'selected' : '' }}>تسجيل الدخول</option>
                    <option value="course" {{ request('activity_type') == 'course' ? 'selected' : '' }}>الدورات</option>
                    <option value="exam" {{ request('activity_type') == 'exam' ? 'selected' : '' }}>الاختبارات</option>
                    <option value="quiz" {{ request('activity_type') == 'quiz' ? 'selected' : '' }}>الاختبارات القصيرة</option>
                    <option value="material" {{ request('activity_type') == 'material' ? 'selected' : '' }}>المواد التعليمية</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="date_from" class="form-label">من تاريخ</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            
            <div class="col-md-3">
                <label for="date_to" class="form-label">إلى تاريخ</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
            
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter me-1"></i> تصفية
                </button>
                <a href="{{ route('parent.activities') }}" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-redo me-1"></i> إعادة ضبط
                </a>
            </div>
        </form>
    </div>
    
    <!-- Activities List -->
    @if(count($activities) > 0)
        @php
            $currentDate = null;
        @endphp
        
        @foreach($activities as $activity)
            @php
                $activityDate = \Carbon\Carbon::parse($activity['timestamp'])->format('Y-m-d');
            @endphp
            
            @if($currentDate !== $activityDate)
                <div class="date-separator">
                    <span class="date-text">{{ \Carbon\Carbon::parse($activity['timestamp'])->format('d M, Y') }}</span>
                </div>
                @php
                    $currentDate = $activityDate;
                @endphp
            @endif
            
            <div class="activity-card">
                <div class="card-body d-flex">
                    <div class="activity-icon {{ $activity['type'] }}">
                        @if($activity['type'] == 'login')
                            <i class="fas fa-sign-in-alt"></i>
                        @elseif($activity['type'] == 'course')
                            <i class="fas fa-book"></i>
                        @elseif($activity['type'] == 'exam')
                            <i class="fas fa-tasks"></i>
                        @elseif($activity['type'] == 'quiz')
                            <i class="fas fa-question-circle"></i>
                        @elseif($activity['type'] == 'material')
                            <i class="fas fa-file-alt"></i>
                        @elseif($activity['type'] == 'certificate')
                            <i class="fas fa-certificate"></i>
                        @else
                            <i class="fas fa-info-circle"></i>
                        @endif
                    </div>
                    
                    <div class="activity-content">
                        <h4 class="activity-title">{{ $activity['title'] }}</h4>
                        <p class="activity-details">{{ $activity['description'] }}</p>
                        <div class="activity-time">
                            <span class="student-badge">{{ $activity['student_name'] }}</span>
                            <i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($activity['timestamp'])->format('h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        
        <!-- Pagination -->
        <div class="pagination">
            {{ $activitiesPagination->links() }}
        </div>
    @else
        <div class="no-activities">
            <div class="icon">
                <i class="fas fa-history"></i>
            </div>
            <h3>لا توجد أنشطة مسجلة</h3>
            <p>لم يتم تسجيل أي نشاط بعد. قد يكون ذلك بسبب عدم وجود طلاب مرتبطين بحسابك أو أن الطلاب لم يقوموا بأي نشاط على المنصة حتى الآن.</p>
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