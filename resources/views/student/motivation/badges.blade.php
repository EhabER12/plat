@extends('layouts.student')

@section('title', 'الشارات')

@section('styles')
<style>
    .badge-card {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        height: 100%;
    }
    
    .badge-card:hover {
        transform: translateY(-5px);
    }
    
    .badge-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .badge-card:hover .badge-icon {
        transform: scale(1.1);
    }
    
    .badge-icon.explore {
        color: #17a2b8;
    }
    
    .badge-icon.persistence {
        color: #fd7e14;
    }
    
    .badge-icon.streak {
        color: #dc3545;
    }
    
    .badge-icon.star {
        color: #ffc107;
    }
    
    .badge-icon.perfect {
        color: #6f42c1;
    }
    
    .badge-level {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        background-color: #e9ecef;
        color: #495057;
        margin-bottom: 10px;
    }
    
    .badge-level.level-1 {
        background-color: #cfe2ff;
        color: #0d6efd;
    }
    
    .badge-level.level-2 {
        background-color: #d1e7dd;
        color: #198754;
    }
    
    .badge-level.level-3 {
        background-color: #fff3cd;
        color: #ffc107;
    }
    
    .badge-level.level-4 {
        background-color: #f8d7da;
        color: #dc3545;
    }
    
    .badge-level.level-5 {
        background-color: #e2d9f3;
        color: #6f42c1;
    }
    
    .locked-badge {
        opacity: 0.5;
        filter: grayscale(1);
    }
    
    .locked-badge .badge-icon {
        color: #adb5bd;
    }
    
    .locked-badge-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .locked-badge-overlay i {
        font-size: 2rem;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>الشارات</h1>
        <a href="{{ route('student.motivation.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right ml-1"></i> العودة إلى لوحة التحفيز
        </a>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle ml-2"></i>
                الشارات هي رموز للإنجازات التي تحققها في رحلة التعلم. اجمع المزيد من الشارات من خلال إكمال الاختبارات والحصول على درجات عالية!
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Earned Badges -->
        @if(count($motivationalContent['badges']) > 0)
            @foreach($motivationalContent['badges'] as $badge)
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card badge-card">
                        <div class="card-body text-center py-4">
                            <div class="badge-icon {{ $badge['icon'] }}">
                                <i class="fas fa-{{ $badge['icon'] == 'explore' ? 'compass' : ($badge['icon'] == 'persistence' ? 'tasks' : ($badge['icon'] == 'streak' ? 'fire' : ($badge['icon'] == 'star' ? 'star' : 'award'))) }}"></i>
                            </div>
                            <div class="badge-level level-{{ $badge['level'] }}">المستوى {{ $badge['level'] }}</div>
                            <h5>{{ $badge['name'] }}</h5>
                            <p class="text-muted">{{ $badge['description'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
        
        <!-- Locked Badges (Examples) -->
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card badge-card locked-badge">
                <div class="card-body text-center py-4 position-relative">
                    <div class="locked-badge-overlay">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="badge-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="badge-level">مقفل</div>
                    <h5>المتفوق</h5>
                    <p class="text-muted">أكمل 10 اختبارات بدرجة 90% أو أعلى</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card badge-card locked-badge">
                <div class="card-body text-center py-4 position-relative">
                    <div class="locked-badge-overlay">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="badge-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div class="badge-level">مقفل</div>
                    <h5>السريع</h5>
                    <p class="text-muted">أكمل 5 اختبارات في أقل من نصف الوقت المخصص</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card badge-card locked-badge">
                <div class="card-body text-center py-4 position-relative">
                    <div class="locked-badge-overlay">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="badge-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="badge-level">مقفل</div>
                    <h5>العبقري</h5>
                    <p class="text-muted">احصل على الدرجة الكاملة في 3 اختبارات متتالية</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
