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

                        @if($notification->severity >= 4)
                            <span class="badge badge-danger py-2 px-3">
                                <i class="fas fa-exclamation-triangle me-1"></i> خطير
                            </span>
                        @elseif($notification->severity >= 3)
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
                                        <small class="ms-1">{{ $notification->read_at ? $notification->read_at->format('Y-m-d H:i') : '' }}</small>
                                    </span>
                                @else
                                    <span class="badge bg-warning py-2 px-3">
                                        <i class="fas fa-envelope me-1"></i> غير مقروء
                                    </span>
                                @endif
                            </span>
                        </div>
                        
                        <h5 class="content-title border-bottom pb-2 mb-3">المحتوى</h5>
                        <div class="p-4 bg-light rounded content-box">
                            {{ $notification->content }}
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
                    
                    @if($relatedItem && $relatedItem instanceof \App\Models\DirectMessage)
                    <div class="mb-4">
                        <h5 class="content-title border-bottom pb-2 mb-3">
                            <i class="fas fa-envelope-open-text me-2 text-primary"></i> معلومات الرسالة
                        </h5>
                        <div class="related-message-info">
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <div class="info-item p-3 bg-light rounded mb-3">
                                        <span class="info-label d-block mb-1 text-primary">المرسل</span>
                                        <span class="info-value d-flex align-items-center">
                                            <i class="fas fa-user me-2"></i>
                                            {{ $relatedItem->sender->name ?? 'غير معروف' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item p-3 bg-light rounded mb-3">
                                        <span class="info-label d-block mb-1 text-primary">المستقبل</span>
                                        <span class="info-value d-flex align-items-center">
                                            <i class="fas fa-user me-2"></i>
                                            {{ $relatedItem->receiver->name ?? 'غير معروف' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <div class="info-item p-3 bg-light rounded mb-3">
                                        <span class="info-label d-block mb-1 text-primary">تاريخ الإرسال</span>
                                        <span class="info-value d-flex align-items-center">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            {{ $relatedItem->created_at->format('Y-m-d H:i:s') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item p-3 bg-light rounded mb-3">
                                        <span class="info-label d-block mb-1 text-primary">الحالة</span>
                                        <span class="info-value d-flex align-items-center">
                                            <i class="fas fa-check-circle me-2"></i>
                                            مرسلة
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="info-item p-3 bg-light rounded">
                                        <span class="info-label d-block mb-1 text-primary">المحتوى بعد الفلترة</span>
                                        <span class="info-value d-block p-3 border rounded-3 mt-2">
                                            {{ $relatedItem->content }}
                                        </span>
                                    </div>
                                </div>
                            </div>
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
            <!-- User Info -->
            @if($notification->user)
            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i> معلومات المستخدم
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-container mx-auto mb-3 position-relative">
                            <img src="{{ $notification->user->profile_image ? asset('storage/' . $notification->user->profile_image) : asset('images/default-avatar.png') }}" 
                                 alt="{{ $notification->user->name }}" 
                                 class="rounded-circle user-avatar shadow" 
                                 width="100" height="100">
                            
                            @if(isset($notification->data['user_roles']))
                                @php
                                    $roleClass = '';
                                    $roleIcon = '';
                                    foreach($notification->data['user_roles'] as $role) {
                                        if ($role == 'admin') {
                                            $roleClass = 'bg-danger';
                                            $roleIcon = 'crown';
                                            break;
                                        } elseif ($role == 'instructor') {
                                            $roleClass = 'bg-primary';
                                            $roleIcon = 'chalkboard-teacher';
                                        } elseif ($role == 'student') {
                                            $roleClass = 'bg-info';
                                            $roleIcon = 'user-graduate';
                                        }
                                    }
                                @endphp
                                <div class="role-badge position-absolute {{ $roleClass }} text-white rounded-circle">
                                    <i class="fas fa-{{ $roleIcon }}"></i>
                                </div>
                            @endif
                        </div>
                        
                        <h5 class="mb-1">{{ $notification->user->name }}</h5>
                        
                        <div class="user-roles mb-3">
                            @if(isset($notification->data['user_roles']))
                                @foreach($notification->data['user_roles'] as $role)
                                    @if($role == 'student')
                                        <span class="badge badge-info py-1 px-2">
                                            <i class="fas fa-user-graduate me-1"></i> طالب
                                        </span>
                                    @elseif($role == 'instructor')
                                        <span class="badge badge-primary py-1 px-2">
                                            <i class="fas fa-chalkboard-teacher me-1"></i> مدرس
                                        </span>
                                    @elseif($role == 'admin')
                                        <span class="badge badge-danger py-1 px-2">
                                            <i class="fas fa-crown me-1"></i> مشرف
                                        </span>
                                    @else
                                        <span class="badge badge-secondary py-1 px-2">
                                            <i class="fas fa-user me-1"></i> {{ $role }}
                                        </span>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                    <div class="user-info-section bg-light p-3 rounded mb-3">
                        <div class="user-info-item d-flex align-items-center mb-2">
                            <div class="icon-wrapper me-2 text-primary">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label d-block text-muted small">البريد الإلكتروني</span>
                                <span class="info-value">{{ $notification->user->email }}</span>
                            </div>
                        </div>
                        
                        <div class="user-info-item d-flex align-items-center mb-2">
                            <div class="icon-wrapper me-2 text-primary">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label d-block text-muted small">تاريخ التسجيل</span>
                                <span class="info-value">{{ $notification->user->created_at->format('Y-m-d') }}</span>
                            </div>
                        </div>
                        
                        <div class="user-info-item d-flex align-items-center">
                            <div class="icon-wrapper me-2 text-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label d-block text-muted small">الإنذارات</span>
                                <span class="info-value">
                                    @php
                                        $warningCount = \App\Models\AdminNotification::where('user_id', $notification->user->user_id)
                                            ->where('type', 'flagged_content')
                                            ->count();
                                    @endphp
                                    <span class="badge bg-{{ $warningCount > 5 ? 'danger' : 'warning' }} text-white py-1 px-2">
                                        {{ $warningCount }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="user-actions text-center mt-3">
                        <a href="#" class="btn btn-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i> تحذير المستخدم
                        </a>
                        
                        <a href="#" class="btn btn-outline-primary mt-2 mx-auto d-block">
                            <i class="fas fa-user me-1"></i> عرض الملف الشخصي
                        </a>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Notification Metadata -->
            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tags me-2"></i> بيانات الإشعار
                    </h6>
                </div>
                <div class="card-body">
                    <div class="metadata-section">
                        <div class="metadata-item py-2 px-3 mb-2 d-flex justify-content-between align-items-center bg-light rounded">
                            <div class="metadata-label text-primary">
                                <i class="fas fa-calendar-alt me-1"></i> تاريخ الإنشاء
                            </div>
                            <div class="metadata-value">
                                {{ $notification->created_at->format('Y-m-d H:i:s') }}
                            </div>
                        </div>
                        
                        <div class="metadata-item py-2 px-3 mb-2 d-flex justify-content-between align-items-center bg-light rounded">
                            <div class="metadata-label text-primary">
                                <i class="fas fa-check-circle me-1"></i> الحالة
                            </div>
                            <div class="metadata-value">
                                @if($notification->is_read)
                                    <span class="badge bg-success text-white">
                                        مقروء
                                        <small class="ms-1">{{ $notification->read_at ? $notification->read_at->format('Y-m-d H:i') : '' }}</small>
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">غير مقروء</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="metadata-item py-2 px-3 mb-2 d-flex justify-content-between align-items-center bg-light rounded">
                            <div class="metadata-label text-primary">
                                <i class="fas fa-exclamation-circle me-1"></i> مستوى الخطورة
                            </div>
                            <div class="metadata-value">
                                <div class="severity-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $notification->severity)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>
                        
                        <div class="metadata-item py-2 px-3 mb-2 d-flex justify-content-between align-items-center bg-light rounded">
                            <div class="metadata-label text-primary">
                                <i class="fas fa-tag me-1"></i> نوع الإشعار
                            </div>
                            <div class="metadata-value">
                                @if($notification->type == 'flagged_content')
                                    <span class="badge badge-warning">محتوى محظور</span>
                                @elseif($notification->type == 'system_alert')
                                    <span class="badge badge-danger">تنبيه نظام</span>
                                @else
                                    <span class="badge badge-secondary">{{ $notification->type }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="metadata-item py-2 px-3 mb-2 d-flex justify-content-between align-items-center bg-light rounded">
                            <div class="metadata-label text-primary">
                                <i class="fas fa-link me-1"></i> العنصر المرتبط
                            </div>
                            <div class="metadata-value">
                                @if($notification->related_type)
                                    <span class="badge bg-primary text-white">
                                        {{ class_basename($notification->related_type) }} #{{ $notification->related_id }}
                                    </span>
                                @else
                                    <span class="text-muted">لا يوجد</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="notification-stats mt-4">
                        <h6 class="font-weight-bold border-bottom pb-2 mb-3">إحصائيات سريعة</h6>
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="stat-item p-2 bg-light rounded">
                                    <div class="stat-icon text-primary mb-2">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                    <div class="stat-value">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </div>
                                    <div class="stat-label small text-muted">
                                        منذ الإنشاء
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="stat-item p-2 bg-light rounded">
                                    <div class="stat-icon text-danger mb-2">
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                    <div class="stat-value">
                                        {{ $notification->severity }}/5
                                    </div>
                                    <div class="stat-label small text-muted">
                                        مستوى الخطورة
                                    </div>
                                </div>
                            </div>
                        </div>
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