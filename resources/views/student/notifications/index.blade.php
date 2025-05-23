@extends('layouts.student')

@section('title', 'الإشعارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">الإشعارات</h6>
                    <div>
                        <form action="{{ route('student.notifications.mark-all-read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-check-double ml-1"></i> تعليم الكل كمقروء
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <div class="btn-group" role="group">
                            <a href="{{ route('student.notifications.index', ['filter' => 'all']) }}" class="btn btn-{{ $filter == 'all' ? 'primary' : 'outline-primary' }}">
                                الكل <span class="badge bg-secondary">{{ $stats['total'] }}</span>
                            </a>
                            <a href="{{ route('student.notifications.index', ['filter' => 'unread']) }}" class="btn btn-{{ $filter == 'unread' ? 'primary' : 'outline-primary' }}">
                                غير مقروءة <span class="badge bg-secondary">{{ $stats['unread'] }}</span>
                            </a>
                            <a href="{{ route('student.notifications.index', ['filter' => 'read']) }}" class="btn btn-{{ $filter == 'read' ? 'primary' : 'outline-primary' }}">
                                مقروءة <span class="badge bg-secondary">{{ $stats['total'] - $stats['unread'] }}</span>
                            </a>
                        </div>
                    </div>

                    @if($notifications->isEmpty())
                        <div class="alert alert-info">
                            لا توجد إشعارات {{ $filter == 'unread' ? 'غير مقروءة' : ($filter == 'read' ? 'مقروءة' : '') }} حالياً.
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <div class="list-group-item list-group-item-action {{ $notification->isRead() ? '' : 'bg-light' }} d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">
                                                @if(!$notification->isRead())
                                                    <span class="badge bg-primary">جديد</span>
                                                @endif
                                                {{ $notification->title }}
                                            </h5>
                                            <small>{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ Str::limit($notification->message, 100) }}</p>
                                        <div class="d-flex mt-2">
                                            <a href="{{ route('student.notifications.show', $notification->notification_id) }}" class="btn btn-sm btn-info ml-1">
                                                <i class="fas fa-eye"></i> عرض
                                            </a>
                                            @if(!$notification->isRead())
                                                <form action="{{ route('student.notifications.mark-as-read', $notification->notification_id) }}" method="POST" class="ml-1">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-check"></i> تعليم كمقروء
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('student.notifications.destroy', $notification->notification_id) }}" method="POST" class="ml-1" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> حذف
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
