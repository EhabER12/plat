@extends('layouts.admin')

@section('title', 'تفاصيل الإشعار')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-bell me-2 text-primary"></i> تفاصيل الإشعار
        </h1>
        <div>
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-light border shadow-sm me-2">
                <i class="fas fa-arrow-left me-1"></i> العودة للإشعارات
            </a>
            
            @if(!$notification->is_read)
            <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success shadow-sm me-2">
                    <i class="fas fa-check me-1"></i> تعليم كمقروء
                </button>
            </form>
            @endif
            
            <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="d-inline delete-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger shadow-sm">
                    <i class="fas fa-trash me-1"></i> حذف الإشعار
                </button>
            </form>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Notification Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4 border-0 notification-details-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i> تفاصيل الإشعار
                    </h6>
                    <div class="notification-badges">
                        @if($notification->type == 'flagged_content')
                            <span class="badge badge-warning py-2 px-3">
                                <i class="fas fa-flag me-1"></i> محتوى محظور
                            </span>
                        @elseif($notification->type == 'system_alert')
                            <span class="badge badge-danger py-2 px-3">
                                <i class="fas fa-exclamation-circle me-1"></i> تنبيه نظام
                            </span>
                        @else
                            <span class="badge badge-secondary py-2 px-3">
                                <i class="fas fa-bell me-1"></i> {{ $notification->type }}
                            </span>
                        @endif

                        @if($notification->severity == 'high')
                            <span class="badge badge-danger py-2 px-3">
                                <i class="fas fa-exclamation-triangle me-1"></i> خطير
                            </span>
                        @elseif($notification->severity == 'medium')
                            <span class="badge badge-warning py-2 px-3">
                                <i class="fas fa-exclamation me-1"></i> متوسط الخطورة
                            </span>
                        @else
                            <span class="badge badge-info py-2 px-3">
                                <i class="fas fa-info-circle me-1"></i> منخفض الخطورة
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4 notification-content">
                        <div class="notification-meta d-flex justify-content-between align-items-center mb-3">
                            <span class="notification-date text-muted">
                                <i class="fas fa-calendar-alt me-1"></i> {{ $notification->created_at->format('Y-m-d H:i:s') }}
                            </span>
                            <span class="notification-status">
                                @if($notification->is_read)
                                    <span class="badge bg-success py-2 px-3">
                                        <i class="fas fa-check-circle me-1"></i> مقروء
                                    </span>
                                @else
                                    <span class="badge bg-warning py-2 px-3">
                                        <i class="fas fa-envelope me-1"></i> غير مقروء
                                    </span>
                                @endif
                            </span>
                        </div>
                        
                        <h5 class="content-title border-bottom pb-2 mb-3">العنوان</h5>
                        <div class="p-4 bg-light rounded content-box mb-4">
                            <h4>{{ $notification->title }}</h4>
                        </div>

                        <h5 class="content-title border-bottom pb-2 mb-3">الرسالة</h5>
                        <div class="p-4 bg-light rounded content-box">
                            {{ $notification->message }}
                        </div>
                    </div>
                    
                    @if($notification->type == 'flagged_content' && isset($notification->data['banned_words']))
                    <div class="mb-4">
                        <h5 class="content-title border-bottom pb-2 mb-3">
                            <i class="fas fa-ban me-2 text-danger"></i> الكلمات المحظورة
                        </h5>
                        <div class="p-4 bg-light rounded content-box">
                            @foreach($notification->data['banned_words'] as $word)
                            <span class="badge badge-danger px-3 py-2 m-1">{{ $word }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if($notification->type == 'flagged_content' && isset($notification->data['original_message']))
                    <div class="mb-4">
                        <h5 class="content-title border-bottom pb-2 mb-3">
                            <i class="fas fa-comment-dots me-2 text-danger"></i> الرسالة الأصلية
                        </h5>
                        <div class="p-4 bg-danger text-white rounded content-box">
                            {{ $notification->data['original_message'] }}
                        </div>
                    </div>
                    @endif
                    
                    @if(isset($notification->data) && is_array($notification->data) && count($notification->data) > 0)
                    <div class="mb-4">
                        <h5 class="content-title border-bottom pb-2 mb-3">
                            <i class="fas fa-database me-2 text-primary"></i> بيانات إضافية
                        </h5>
                        <div class="p-4 bg-light rounded content-box">
                            <pre class="json-data">{{ json_encode($notification->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif
                    
                    <div class="notification-actions text-center mt-4 pt-3 border-top">
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-light border me-2">
                            <i class="fas fa-arrow-left me-1"></i> العودة للإشعارات
                        </a>
                        
                        @if(!$notification->is_read)
                        <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fas fa-check me-1"></i> تعليم كمقروء
                            </button>
                        </form>
                        @endif
                        
                        <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> حذف الإشعار
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Notification Type Info -->
            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i> معلومات النوع
                    </h6>
                </div>
                <div class="card-body">
                    <div class="info-item p-3 bg-light rounded mb-3">
                        <span class="info-label d-block mb-1 text-primary">نوع الإشعار</span>
                        <span class="info-value d-flex align-items-center">
                            @if($notification->type == 'flagged_content')
                                <i class="fas fa-flag me-2 text-warning"></i>
                                <span>محتوى محظور</span>
                            @elseif($notification->type == 'system_alert')
                                <i class="fas fa-exclamation-circle me-2 text-danger"></i>
                                <span>تنبيه نظام</span>
                            @else
                                <i class="fas fa-bell me-2 text-secondary"></i>
                                <span>{{ $notification->type }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item p-3 bg-light rounded mb-3">
                        <span class="info-label d-block mb-1 text-primary">مستوى الخطورة</span>
                        <span class="info-value d-flex align-items-center">
                            @if($notification->severity == 'high')
                                <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                                <span>عالي</span>
                            @elseif($notification->severity == 'medium')
                                <i class="fas fa-exclamation me-2 text-warning"></i>
                                <span>متوسط</span>
                            @else
                                <i class="fas fa-info-circle me-2 text-info"></i>
                                <span>منخفض</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-item p-3 bg-light rounded">
                        <span class="info-label d-block mb-1 text-primary">تاريخ الإنشاء</span>
                        <span class="info-value d-flex align-items-center">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i>
                            <span>{{ $notification->created_at->format('Y-m-d H:i:s') }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.notification-details-card {
    border-radius: 10px;
}

.notification-badges .badge {
    margin-left: 5px;
    font-size: 0.85rem;
}

.content-title {
    color: #4e73df;
}

.content-box {
    border: 1px solid rgba(0,0,0,0.1);
}

.notification-actions .btn {
    min-width: 120px;
}

.info-item {
    border: 1px solid rgba(0,0,0,0.1);
}

.info-label {
    font-weight: 500;
    color: #4e73df;
}

.json-data {
    background: #f8f9fc;
    font-family: monospace;
    font-size: 0.9rem;
    padding: 15px;
    border-radius: 5px;
    max-height: 300px;
    overflow-y: auto;
    white-space: pre-wrap;
}

.user-avatar {
    border: 3px solid #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.role-badge {
    bottom: 5px;
    right: 5px;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    border: 2px solid #fff;
}

.avatar-container {
    width: 100px;
    height: 100px;
}

.metadata-label {
    font-weight: 500;
}

.metadata-item {
    transition: all 0.3s ease;
}

.metadata-item:hover {
    transform: translateX(-5px);
    background-color: #f1f5ff !important;
}

.stat-item {
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تأكيد الحذف
        $('.delete-form').on('submit', function(e) {
            e.preventDefault();
            if (confirm('هل أنت متأكد من حذف هذا الإشعار؟')) {
                this.submit();
            }
        });
    });
</script>
@endsection 