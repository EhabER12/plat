@extends('layouts.student')

@section('title', 'عرض الإشعار')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">عرض الإشعار</h6>
                    <a href="{{ route('student.notifications.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-right ml-1"></i> العودة للإشعارات
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="notification-details p-4 border rounded">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>{{ $notification->title }}</h4>
                            <span class="badge {{ $notification->isRead() ? 'bg-secondary' : 'bg-primary' }}">
                                {{ $notification->isRead() ? 'مقروء' : 'غير مقروء' }}
                            </span>
                        </div>
                        
                        <div class="notification-meta mb-3 text-muted">
                            <small>
                                <i class="fas fa-clock ml-1"></i> {{ $notification->created_at->format('Y-m-d H:i') }}
                                @if($notification->isRead())
                                    <span class="mx-2">|</span>
                                    <i class="fas fa-check-double ml-1"></i> تمت القراءة: {{ $notification->read_at->format('Y-m-d H:i') }}
                                @endif
                            </small>
                        </div>
                        
                        <div class="notification-content p-3 bg-light rounded mb-4">
                            <p class="mb-0">{{ $notification->message }}</p>
                        </div>
                        
                        @if($notification->related_entity && $notification->entity_id)
                            <div class="notification-related mb-4">
                                <h6>متعلق بـ:</h6>
                                @if($notification->related_entity == 'courses')
                                    <a href="{{ route('student.course-content', $notification->entity_id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-book ml-1"></i> عرض الدورة
                                    </a>
                                @elseif($notification->related_entity == 'exams')
                                    <a href="{{ route('student.exams.show', $notification->entity_id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-file-alt ml-1"></i> عرض الاختبار
                                    </a>
                                @elseif($notification->related_entity == 'quizzes')
                                    <a href="{{ route('student.quizzes.show', $notification->entity_id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-question-circle ml-1"></i> عرض الاختبار القصير
                                    </a>
                                @endif
                            </div>
                        @endif
                        
                        <div class="notification-actions d-flex mt-4">
                            @if(!$notification->isRead())
                                <form action="{{ route('student.notifications.mark-as-read', $notification->notification_id) }}" method="POST" class="ml-2">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check ml-1"></i> تعليم كمقروء
                                    </button>
                                </form>
                            @endif
                            
                            <form action="{{ route('student.notifications.destroy', $notification->notification_id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash ml-1"></i> حذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
