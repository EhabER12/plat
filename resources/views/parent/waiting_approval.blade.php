@extends('layouts.app')

@section('title', 'في انتظار الموافقة')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<style>
    .waiting-container {
        max-width: 800px;
        margin: 50px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }
    
    .status-icon {
        font-size: 4rem;
        color: #ffc107;
        margin-bottom: 20px;
    }
    
    .status-message {
        background-color: #fff8e1;
        border: 1px solid #ffecb3;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .action-buttons {
        margin-top: 30px;
    }
    
    .verification-details {
        margin-top: 30px;
        border-top: 1px solid #eee;
        padding-top: 20px;
    }
    
    .verification-card {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
    }
    
    .verification-status {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .status-pending {
        background-color: #fff8e1;
        color: #f57c00;
    }
    
    .status-rejected {
        background-color: #ffebee;
        color: #d32f2f;
    }
</style>
@endsection

@section('content')
<div class="container waiting-container">
    <div class="text-center mb-4">
        <div class="status-icon">
            <i class="fas fa-clock"></i>
        </div>
        <h1 class="mb-3">في انتظار الموافقة</h1>
        <p class="lead">مرحباً {{ Auth::user()->name }}، حسابك كولي أمر في انتظار التحقق من قبل الإدارة.</p>
    </div>
    
    <div class="status-message">
        <p><i class="fas fa-info-circle me-2"></i> ستتمكن من الوصول إلى لوحة تحكم ولي الأمر بعد التحقق من هويتك ومراجعة المستندات المقدمة.</p>
    </div>
    
    <div class="verification-details">
        <h4 class="mb-3">حالة طلبات التحقق</h4>
        
        @php
            $pendingRelations = \App\Models\ParentStudentRelation::where('parent_id', Auth::id())
                ->where('verification_status', 'pending')
                ->get();
                
            $rejectedRelations = \App\Models\ParentStudentRelation::where('parent_id', Auth::id())
                ->where('verification_status', 'rejected')
                ->get();
        @endphp
        
        @if(count($pendingRelations) > 0)
            <h5 class="mt-4 mb-2">طلبات قيد المراجعة</h5>
            @foreach($pendingRelations as $relation)
                <div class="verification-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>اسم الطالب:</strong> {{ $relation->student_name }}
                        </div>
                        <div>
                            <span class="verification-status status-pending"><i class="fas fa-clock me-1"></i> قيد المراجعة</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">تاريخ التقديم: {{ $relation->created_at->format('Y-m-d') }}</small>
                    </div>
                </div>
            @endforeach
        @endif
        
        @if(count($rejectedRelations) > 0)
            <h5 class="mt-4 mb-2">طلبات مرفوضة</h5>
            @foreach($rejectedRelations as $relation)
                <div class="verification-card">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>اسم الطالب:</strong> {{ $relation->student_name }}
                        </div>
                        <div>
                            <span class="verification-status status-rejected"><i class="fas fa-times me-1"></i> مرفوض</span>
                        </div>
                    </div>
                    @if($relation->verification_notes)
                        <div class="mt-2">
                            <strong>سبب الرفض:</strong> {{ $relation->verification_notes }}
                        </div>
                    @endif
                    <div class="mt-2">
                        <small class="text-muted">تاريخ التقديم: {{ $relation->created_at->format('Y-m-d') }}</small>
                        @if($relation->verified_at)
                            <small class="text-muted me-2">تاريخ المراجعة: {{ $relation->verified_at->format('Y-m-d') }}</small>
                        @endif
                    </div>
                    <div class="mt-2">
                        <form action="{{ route('parent.resubmit-link-request', $relation->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">إعادة تقديم الطلب</button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
        
        @if(count($pendingRelations) == 0 && count($rejectedRelations) == 0)
            <div class="alert alert-warning">
                لم يتم العثور على أي طلبات تحقق. يمكنك تقديم طلب جديد.
            </div>
        @endif
    </div>
    
    <div class="action-buttons text-center">
        <a href="{{ route('parent.link-request') }}" class="btn btn-primary">
            <i class="fas fa-link me-1"></i> تقديم طلب ربط جديد
        </a>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary ms-2">
            <i class="fas fa-home me-1"></i> العودة للرئيسية
        </a>
    </div>
</div>
@endsection 