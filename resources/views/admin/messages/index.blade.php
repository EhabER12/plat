@extends('admin.layout')

@section('title', 'Messages')
@section('page-title', 'Messages')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-envelope me-2"></i>
                    الرسائل
                </h5>
                <a href="{{ route('admin.messages.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus me-1"></i>
                    رسالة جديدة
                </a>
            </div>
            <div class="card-body p-0">
                @if(count($conversationData) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($conversationData as $conversation)
                            <a href="{{ route('admin.messages.conversation', $conversation['user']->user_id) }}" 
                               class="list-group-item list-group-item-action border-0 py-3 px-4 {{ $conversation['unread_count'] > 0 ? 'bg-light' : '' }}">
                                <div class="d-flex align-items-center">
                                    <!-- User Avatar -->
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px; border-radius: 50%;">
                                            {{ substr($conversation['user']->name, 0, 1) }}
                                        </div>
                                    </div>
                                    
                                    <!-- Conversation Info -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0 fw-bold text-dark">
                                                {{ $conversation['user']->name }}
                                                @if($conversation['unread_count'] > 0)
                                                    <span class="badge bg-danger rounded-pill ms-2">{{ $conversation['unread_count'] }}</span>
                                                @endif
                                            </h6>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($conversation['last_message_time'])->diffForHumans() }}
                                            </small>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-0 text-muted small">
                                                    {{ $conversation['user']->email }}
                                                </p>
                                                @if($conversation['last_message'])
                                                    <p class="mb-0 text-muted small">
                                                        <strong>{{ $conversation['last_message']->sender_id == Auth::user()->user_id ? 'أنت' : $conversation['user']->name }}:</strong>
                                                        {{ Str::limit($conversation['last_message']->content, 50) }}
                                                    </p>
                                                @endif
                                            </div>
                                            
                                            <div class="text-end">
                                                <small class="text-muted">
                                                    {{ $conversation['message_count'] }} رسالة
                                                </small>
                                                @php
                                                    $userRoles = $conversation['user']->getUserRoles();
                                                @endphp
                                                @if(!empty($userRoles))
                                                    <div class="mt-1">
                                                        @foreach($userRoles as $role)
                                                            <span class="badge bg-secondary small">
                                                                @switch($role)
                                                                    @case('instructor')
                                                                        مدرس
                                                                        @break
                                                                    @case('student')
                                                                        طالب
                                                                        @break
                                                                    @case('parent')
                                                                        ولي أمر
                                                                        @break
                                                                    @default
                                                                        {{ $role }}
                                                                @endswitch
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-envelope-open-text fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد محادثات</h5>
                        <p class="text-muted">لم تبدأ أي محادثات بعد</p>
                        <a href="{{ route('admin.messages.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            ابدأ محادثة جديدة
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">إجمالي المحادثات</h6>
                        <h3 class="mb-0">{{ count($conversationData) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-comments fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">رسائل غير مقروءة</h6>
                        <h3 class="mb-0">
                            @php
                                $totalUnread = collect($conversationData)->sum('unread_count');
                            @endphp
                            {{ $totalUnread }}
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-envelope fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">محادثات نشطة</h6>
                        <h3 class="mb-0">
                            @php
                                $activeConversations = collect($conversationData)->filter(function($conv) {
                                    return \Carbon\Carbon::parse($conv['last_message_time'])->isAfter(now()->subDays(7));
                                })->count();
                            @endphp
                            {{ $activeConversations }}
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-comment-dots fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">إجمالي الرسائل</h6>
                        <h3 class="mb-0">
                            @php
                                $totalMessages = collect($conversationData)->sum('message_count');
                            @endphp
                            {{ $totalMessages }}
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-paper-plane fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.avatar-circle {
    font-weight: bold;
    font-size: 1.2rem;
}

.list-group-item:hover {
    background-color: #f8f9fa !important;
}

.badge {
    font-size: 0.7rem;
}
</style>
@endsection
