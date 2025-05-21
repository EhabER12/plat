@extends('layouts.admin')

@section('title', 'إشعارات النظام')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">إشعارات النظام</h1>
        <div>
            <a href="{{ route('admin.notifications.test-create') }}" class="btn btn-danger shadow-sm me-2">
                <i class="fas fa-plus me-1"></i> إنشاء إشعار تجريبي
            </a>
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary shadow-sm">
                    <i class="fas fa-check-double me-1"></i> تعليم الكل كمقروء
                </button>
            </form>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row mb-4">
        <!-- Total Notifications Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card h-100">
                <div class="stats-icon stats-primary">
                    <i class="fas fa-bell fa-lg text-white"></i>
                </div>
                <div class="stats-number">{{ $stats['total'] }}</div>
                <div class="stats-label">إجمالي الإشعارات</div>
                <a href="{{ route('admin.notifications.index') }}" class="view-details">
                    <span>عرض الكل</span> <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Unread Notifications Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card h-100">
                <div class="stats-icon stats-danger">
                    <i class="fas fa-envelope fa-lg text-white"></i>
                </div>
                <div class="stats-number">{{ $stats['unread'] }}</div>
                <div class="stats-label">إشعارات غير مقروءة</div>
                <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}" class="view-details">
                    <span>عرض غير المقروءة</span> <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Flagged Content Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card h-100">
                <div class="stats-icon stats-warning">
                    <i class="fas fa-flag fa-lg text-white"></i>
                </div>
                <div class="stats-number">{{ $stats['flagged_content'] }}</div>
                <div class="stats-label">محتوى محظور</div>
                <a href="{{ route('admin.notifications.index', ['type' => 'flagged_content']) }}" class="view-details">
                    <span>عرض المحتوى المحظور</span> <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Critical Notifications Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card h-100">
                <div class="stats-icon" style="background: linear-gradient(135deg, #E74C3C 0%, #C0392B 100%);">
                    <i class="fas fa-exclamation-triangle fa-lg text-white"></i>
                </div>
                <div class="stats-number">{{ $stats['high_severity'] }}</div>
                <div class="stats-label">إشعارات عالية الخطورة</div>
                <a href="{{ route('admin.notifications.index', ['severity' => 'high']) }}" class="view-details">
                    <span>عرض الإشعارات الخطيرة</span> <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Notifications List -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell me-2"></i> الإشعارات
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-filter fa-fw text-gray-400"></i> تصفية
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="filterDropdown">
                            <div class="dropdown-header">تصفية الإشعارات:</div>
                            <a class="dropdown-item {{ $filter == 'all' ? 'active' : '' }}" href="{{ route('admin.notifications.index', ['filter' => 'all']) }}">
                                <i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i> جميع الإشعارات
                            </a>
                            <a class="dropdown-item {{ $filter == 'unread' ? 'active' : '' }}" href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}">
                                <i class="fas fa-envelope fa-sm fa-fw me-2 text-gray-400"></i> غير مقروءة
                            </a>
                            <a class="dropdown-item {{ $filter == 'read' ? 'active' : '' }}" href="{{ route('admin.notifications.index', ['filter' => 'read']) }}">
                                <i class="fas fa-envelope-open fa-sm fa-fw me-2 text-gray-400"></i> مقروءة
                            </a>
                            <div class="dropdown-divider"></div>
                            <div class="dropdown-header">تصفية حسب النوع:</div>
                            <a class="dropdown-item {{ $type == 'flagged_content' ? 'active' : '' }}" href="{{ route('admin.notifications.index', ['type' => 'flagged_content']) }}">
                                <i class="fas fa-flag fa-sm fa-fw me-2 text-warning"></i> محتوى محظور
                            </a>
                            <a class="dropdown-item {{ $type == 'system_alert' ? 'active' : '' }}" href="{{ route('admin.notifications.index', ['type' => 'system_alert']) }}">
                                <i class="fas fa-exclamation-circle fa-sm fa-fw me-2 text-danger"></i> تنبيهات النظام
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                    <form action="{{ route('admin.notifications.mark-multiple-read') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="check-all">
                                <label class="custom-control-label" for="check-all">تحديد/إلغاء تحديد الكل</label>
                            </div>
                        </div>
                        
                        <div class="notification-list">
                            @foreach($notifications as $notification)
                            <div class="notification-item p-3 mb-3 rounded {{ $notification->is_read ? 'bg-white' : 'bg-light-blue' }} position-relative shadow-sm">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="check-{{ $notification->id }}" name="notification_ids[]" value="{{ $notification->id }}">
                                            <label class="custom-control-label" for="check-{{ $notification->id }}"></label>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        @if($notification->type == 'flagged_content')
                                            <div class="notification-icon bg-warning text-white">
                                                <i class="fas fa-flag"></i>
                                            </div>
                                        @elseif($notification->type == 'system_alert')
                                            <div class="notification-icon bg-danger text-white">
                                                <i class="fas fa-exclamation-circle"></i>
                                            </div>
                                        @else
                                            <div class="notification-icon bg-primary text-white">
                                                <i class="fas fa-bell"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 {{ $notification->is_read ? 'font-weight-normal' : 'font-weight-bold' }}">
                                                @if($notification->type == 'flagged_content')
                                                    <span class="badge badge-warning">محتوى محظور</span>
                                                @elseif($notification->type == 'system_alert')
                                                    <span class="badge badge-danger">تنبيه نظام</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $notification->type }}</span>
                                                @endif

                                                @if($notification->severity == 'high')
                                                    <span class="badge badge-danger ms-1">خطير</span>
                                                @endif
                                            </h6>
                                            <span class="text-muted small">{{ $notification->created_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                        <div class="notification-content mb-2">
                                            <a href="{{ route('admin.notifications.show', $notification->id) }}" class="text-reset">
                                                <strong>{{ $notification->title }}</strong>: {{ Str::limit($notification->message, 100) }}
                                            </a>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <!-- No user relationship -->
                                            </div>
                                            <div class="notification-actions">
                                                <a href="{{ route('admin.notifications.show', $notification->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(!$notification->is_read)
                                                <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                @endif
                                                <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @if(!$notification->is_read)
                                <span class="position-absolute top-0 start-0 translate-middle bg-danger rounded-circle unread-indicator"></span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-double me-1"></i> تعليم المحدد كمقروء
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-bell-slash fa-4x text-gray-300 mb-3"></i>
                        <p class="text-muted">لا توجد إشعارات متطابقة مع معايير البحث</p>
                        <a href="{{ route('admin.notifications.index') }}" class="btn btn-primary mt-3">
                            عرض كل الإشعارات
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Flagged Users Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell me-2"></i> الإشعارات حسب النوع
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('admin.notifications.index', ['type' => 'general']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-bell text-primary me-2"></i> إشعارات عامة</span>
                            <span class="badge badge-primary badge-pill">{{ \App\Models\AdminNotification::where('type', 'general')->count() }}</span>
                        </a>
                        <a href="{{ route('admin.notifications.index', ['type' => 'flagged_content']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-flag text-warning me-2"></i> محتوى محظور</span>
                            <span class="badge badge-warning badge-pill">{{ \App\Models\AdminNotification::where('type', 'flagged_content')->count() }}</span>
                        </a>
                        <a href="{{ route('admin.notifications.index', ['type' => 'system_alert']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-exclamation-circle text-danger me-2"></i> تنبيهات النظام</span>
                            <span class="badge badge-danger badge-pill">{{ \App\Models\AdminNotification::where('type', 'system_alert')->count() }}</span>
                        </a>
                    </div>

                    <div class="mt-4">
                        <h6 class="font-weight-bold mb-3">حسب مستوى الخطورة</h6>
                        <div class="list-group">
                            <a href="{{ route('admin.notifications.index', ['severity' => 'low']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-arrow-down text-info me-2"></i> منخفضة</span>
                                <span class="badge badge-info badge-pill">{{ \App\Models\AdminNotification::where('severity', 'low')->count() }}</span>
                            </a>
                            <a href="{{ route('admin.notifications.index', ['severity' => 'medium']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-minus text-secondary me-2"></i> متوسطة</span>
                                <span class="badge badge-secondary badge-pill">{{ \App\Models\AdminNotification::where('severity', 'medium')->count() }}</span>
                            </a>
                            <a href="{{ route('admin.notifications.index', ['severity' => 'high']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-arrow-up text-danger me-2"></i> عالية</span>
                                <span class="badge badge-danger badge-pill">{{ \App\Models\AdminNotification::where('severity', 'high')->count() }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i> إجراءات سريعة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="{{ route('admin.notifications.test-create') }}" class="quick-action-btn bg-danger text-white p-3 rounded text-center d-block">
                                <i class="fas fa-plus fa-2x mb-2"></i>
                                <span>إنشاء إشعار</span>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}" class="quick-action-btn bg-primary text-white p-3 rounded text-center d-block">
                                <i class="fas fa-envelope fa-2x mb-2"></i>
                                <span>غير مقروءة</span>
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="d-inline w-100">
                                @csrf
                                <button type="submit" class="quick-action-btn bg-success text-white p-3 rounded text-center d-block w-100 border-0">
                                    <i class="fas fa-check-double fa-2x mb-2"></i>
                                    <span>قراءة الكل</span>
                                </button>
                            </form>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="{{ route('admin.notifications.index', ['type' => 'flagged_content']) }}" class="quick-action-btn bg-warning text-dark p-3 rounded text-center d-block">
                                <i class="fas fa-flag fa-2x mb-2"></i>
                                <span>محتوى محظور</span>
                            </a>
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
.bg-light-blue {
    background-color: rgba(0, 123, 255, 0.05);
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.notification-item {
    border-left: 4px solid #e3e6f0;
    transition: all 0.3s ease;
}

.notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1) !important;
}

.notification-item:not(.bg-light-blue) {
    border-left-color: #d1d3e2;
}

.notification-item.bg-light-blue {
    border-left-color: #4e73df;
}

.unread-indicator {
    width: 12px;
    height: 12px;
    transform: translate(-50%, -50%);
    box-shadow: 0 0 0 3px #fff;
}

.notification-actions .btn {
    margin-left: 5px;
}

.flagged-user-item {
    transition: all 0.3s ease;
    background-color: white;
}

.flagged-user-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1) !important;
}

.quick-action-btn {
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.quick-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // تحديد/إلغاء تحديد جميع الإشعارات
        $("#check-all").on('change', function() {
            $('input[name="notification_ids[]"]').prop('checked', $(this).prop('checked'));
        });
        
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