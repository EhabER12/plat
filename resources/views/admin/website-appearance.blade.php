@extends('layouts.admin')

@section('title', 'Website Appearance')

@push('styles')
<style>
    :root {
        --primary-color: #003366;
        --secondary-color: #FFD700;
        --accent-color: #FFD700;
        --background-color: #FAFAFA;
        --text-dark: #1F1F1F;
        --text-light: #666666;
        --border-color: #003366;
        --success-color: #2ECC71;
        --error-color: #E74C3C;
        --card-bg: #FFFFFF;
        --card-shadow: 0 4px 15px rgba(0, 51, 102, 0.08);
    }

    /* تحسين تباعد الحقول */
    .form-label {
        margin-bottom: 0.3rem; /* تقليل المسافة السفلية للعنوان */
        font-weight: 500;
        display: block; /* ضمان أن يأخذ العرض الكامل */
        color: var(--text-dark);
    }
    .form-control, .form-select {
        margin-bottom: 1rem;
        border-color: #dee2e6;
        border-radius: 0.5rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(0, 51, 102, 0.15);
    }
    /* تعديل خاص لحقل اللون */
    .form-control-color {
        max-width: 60px; /* تحديد عرض مناسب */
        height: calc(1.5em + 0.75rem + 2px); /* ليتناسب مع ارتفاع الحقول الأخرى */
        padding: 0.2rem 0.3rem; /* تعديل الـ padding */
        margin-bottom: 1rem;
        vertical-align: middle; /* محاذاة رأسية أفضل */
    }
    /* تحسين معاينة الصورة */
    .image-preview-container {
        background-color: #e9ecef;
        padding: 0.5rem;
        border-radius: 0.5rem;
        display: inline-block; /* لتجنب أخذ العرض الكامل */
        margin-bottom: 0.5rem;
        border: 1px solid #dee2e6;
    }
    .img-thumbnail {
        max-width: 150px; 
        max-height: 80px; /* تحديد ارتفاع أقصى */
        object-fit: contain; /* ضمان ظهور الصورة كاملة */
        background-color: #fff; /* خلفية بيضاء للصورة */
    }

    /* تحسين تصميم الأقسام المتكررة */
    .repeater-item-container {
        border: 1px solid #dee2e6;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        background-color: var(--card-bg); /* تغيير الخلفية للأبيض */
        position: relative; 
        box-shadow: var(--card-shadow); /* إضافة ظل خفيف */
        transition: all 0.3s ease;
    }
    .repeater-item-container:hover {
        box-shadow: 0 10px 25px rgba(0, 51, 102, 0.12);
        transform: translateY(-5px);
    }
    .repeater-item-container .remove-repeater {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        padding: 0.25rem 0.5rem; /* تصغير حجم الزر */
        line-height: 1; /* تحسين محاذاة الأيقونة */
        background-color: #fff;
        border: 1px solid #dee2e6;
        color: var(--error-color);
        border-radius: 50%;
        transition: all 0.2s ease;
    }
    .repeater-item-container .remove-repeater:hover {
        background-color: var(--error-color);
        color: #fff;
        border-color: var(--error-color);
    }
    .repeater-item-container h6 {
        margin-bottom: 1.25rem; /* زيادة المسافة تحت العنوان */
        color: var(--primary-color); /* تغيير لون العنوان */
        border-bottom: 1px solid #eee; /* خط فاصل تحت العنوان */
        padding-bottom: 0.75rem;
        font-weight: 600;
    }
    .repeater-item-container .row {
        margin-left: -0.75rem; /* تعديل لـ padding الكارد */
        margin-right: -0.75rem;
    }
    .repeater-item-container .row > div {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        margin-bottom: 0.5rem; 
    }

    #add-feature, #add-partner {
        margin-top: 1rem;
        background-color: var(--primary-color);
        color: var(--secondary-color);
        border: none;
        transition: all 0.3s ease;
    }
    #add-feature:hover, #add-partner:hover {
        background-color: #002244;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 51, 102, 0.2);
    }
    .section-divider {
        margin-top: 2.5rem;
        margin-bottom: 2rem;
        border-top: 1px dashed #ced4da;
    }
    h5.section-subtitle {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        color: var(--primary-color); /* تغيير للون النيفي */
        font-size: 1.1rem;
        font-weight: 600;
    }
    /* إعادة تصميم Tabs قليلاً */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
    }
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        padding: 0.75rem 1.25rem; /* زيادة الـ padding */
        color: var(--text-light);
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .nav-tabs .nav-link.active {
        color: var(--primary-color);
        background-color: #fff; /* تغيير الخلفية للأبيض */
        border-color: #dee2e6 #dee2e6 #fff; /* مطابقة الحدود */
        font-weight: 600;
        border-top: 3px solid var(--secondary-color);
    }
    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        isolation: isolate;
        color: var(--primary-color);
        background-color: rgba(0, 51, 102, 0.03);
    }
    .tab-content {
        background-color: #fff; /* تغيير الخلفية للأبيض */
        border: 1px solid #dee2e6;
        border-top: 0;
        padding: 2rem; /* زيادة الـ padding */
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    /* إزالة حدود الكارد الداخلية للـ Tab */
    .tab-pane > .card {
        border: none;
        box-shadow: none;
        background-color: transparent;
    }
    .tab-pane > .card .card-header {
        background-color: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 0;
        margin-bottom: 1.5rem;
    }
    .tab-pane > .card .card-header h5 {
        font-size: 1.2rem;
        color: var(--primary-color);
        font-weight: 600;
    }
    .tab-pane > .card .card-body {
        padding: 1.5rem 0 0 0; /* تعديل الـ padding */
    }

    .website-appearance-content {
        padding-top: 1.5rem; /* Add padding to push content below the fixed navbar */
    }
    
    /* زر حفظ الإعدادات */
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: var(--secondary-color);
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #002244;
        border-color: #002244;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 51, 102, 0.2);
    }
    
    /* تنسيق زر تحديث الصفحة الرئيسية */
    .card-body .btn-primary {
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
    }
    
    /* تحسين تمييز الرسائل */
    .alert-success {
        background-color: rgba(46, 204, 113, 0.1);
        border-color: var(--success-color);
        color: #1d8348;
    }
    .alert-danger {
        background-color: rgba(231, 76, 60, 0.1);
        border-color: var(--error-color);
        color: #922b21;
    }
    
    /* تنسيق الكارد العلوي */
    .card {
        border-radius: 0.75rem;
        box-shadow: var(--card-shadow);
        border: 1px solid #dee2e6;
    }
    .card-body {
        padding: 1.5rem;
    }
</style>
@endpush

@section('content')
    <div class="container-fluid website-appearance-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Clear Cache Button -->
        <div class="card mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0" style="color: var(--primary-color); font-weight: 600;">مشكلة في عرض الصور والإعدادات على الموقع؟</h5>
                    <p class="text-muted mb-0">استخدم هذا الزر لمسح ذاكرة التخزين المؤقت وتحديث الصفحة الرئيسية فوراً</p>
                </div>
                <form action="{{ route('admin.website-appearance.clear-cache') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color); border-color: var(--primary-color); color: var(--secondary-color);">
                        <i class="fas fa-sync-alt me-2"></i> تحديث الصفحة الرئيسية
                    </button>
                </form>
            </div>
        </div>
    
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" id="appearanceTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="navbar-banner-tab" data-bs-toggle="tab" data-bs-target="#navbar-banner" 
                        type="button" role="tab" aria-controls="navbar-banner" aria-selected="true">
                    <i class="fas fa-bars me-1" style="color: var(--secondary-color);"></i> بانر شريط التنقل
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" 
                        type="button" role="tab" aria-controls="hero" aria-selected="false">
                    <i class="fas fa-image me-1" style="color: var(--secondary-color);"></i> القسم الرئيسي
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="features-tab" data-bs-toggle="tab" data-bs-target="#features" 
                        type="button" role="tab" aria-controls="features" aria-selected="false">
                    <i class="fas fa-th-large me-1" style="color: var(--secondary-color);"></i> المميزات
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" 
                        type="button" role="tab" aria-controls="stats" aria-selected="false">
                    <i class="fas fa-chart-bar me-1" style="color: var(--secondary-color);"></i> الإحصائيات
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" 
                        type="button" role="tab" aria-controls="about" aria-selected="false">
                    <i class="fas fa-info-circle me-1" style="color: var(--secondary-color);"></i> قسم من نحن
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="video-tab" data-bs-toggle="tab" data-bs-target="#video" 
                        type="button" role="tab" aria-controls="video" aria-selected="false">
                    <i class="fas fa-video me-1" style="color: var(--secondary-color);"></i> قسم الفيديو
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="partners-tab" data-bs-toggle="tab" data-bs-target="#partners" 
                        type="button" role="tab" aria-controls="partners" aria-selected="false">
                    <i class="fas fa-handshake me-1" style="color: var(--secondary-color);"></i> قسم الشركاء
                </button>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="appearanceTabsContent">
        
            <!-- Navbar Banner Tab -->
            <div class="tab-pane fade show active" id="navbar-banner" role="tabpanel" aria-labelledby="navbar-banner-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0" style="color: var(--primary-color); font-weight: 600;">إعدادات بانر شريط التنقل</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.website-appearance.navbar-banner') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="banner_title" class="form-label">Banner Title</label>
                                    <input type="text" class="form-control" id="banner_title" name="banner_title" 
                                           value="{{ $navbarBannerSettings['banner_title'] ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="banner_bg_color" class="form-label">Background Color</label>
                                    <input type="color" class="form-control form-control-color" id="banner_bg_color" name="banner_bg_color" 
                                           value="{{ $navbarBannerSettings['banner_bg_color'] ?? '#f8f9fa' }}">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="banner_subtitle" class="form-label">Banner Subtitle</label>
                                <textarea class="form-control" id="banner_subtitle" name="banner_subtitle" rows="2">{{ $navbarBannerSettings['banner_subtitle'] ?? '' }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="banner_image" class="form-label">Banner Background Image</label>
                                @if(isset($navbarBannerSettings['banner_image']))
                                    <div class="image-preview-container">
                                        <img src="{{ asset('storage/' . $navbarBannerSettings['banner_image']) }}" 
                                             alt="Current Banner" class="img-thumbnail">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="banner_image" name="banner_image">
                                <small class="text-muted">Recommended size: 1920x200px. Leave empty to keep current image.</small>
                            </div>
                            
                            <h6 class="fw-bold mt-4 mb-3">Statistics Settings</h6>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="students_count" class="form-label">Students Count</label>
                                    <input type="number" class="form-control" id="students_count" name="students_count" 
                                           value="{{ $navbarBannerSettings['students_count'] ?? 0 }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="courses_count" class="form-label">Courses Count</label>
                                    <input type="number" class="form-control" id="courses_count" name="courses_count" 
                                           value="{{ $navbarBannerSettings['courses_count'] ?? 0 }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="instructors_count" class="form-label">Instructors Count</label>
                                    <input type="number" class="form-control" id="instructors_count" name="instructors_count" 
                                           value="{{ $navbarBannerSettings['instructors_count'] ?? 0 }}">
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> حفظ إعدادات البانر
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Hero Section Tab -->
            <div class="tab-pane fade" id="hero" role="tabpanel" aria-labelledby="hero-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0" style="color: var(--primary-color); font-weight: 600;">إعدادات القسم الرئيسي</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.website-appearance.hero') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="hero_title" class="form-label">Hero Title</label>
                                <input type="text" class="form-control" id="hero_title" name="hero_title" 
                                       value="{{ $heroSettings['hero_title'] ?? '' }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="hero_subtitle" class="form-label">Hero Subtitle</label>
                                <textarea class="form-control" id="hero_subtitle" name="hero_subtitle" rows="3">{{ $heroSettings['hero_subtitle'] ?? '' }}</textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="hero_bg_color" class="form-label">Background Color</label>
                                    <input type="color" class="form-control form-control-color" id="hero_bg_color" name="hero_bg_color" 
                                           value="{{ $heroSettings['hero_bg_color'] ?? '#4361ee' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="hero_image" class="form-label">Hero Image</label>
                                    @if(isset($heroSettings['hero_image']))
                                        <div class="image-preview-container">
                                            <img src="{{ asset('storage/' . $heroSettings['hero_image']) }}" 
                                                 alt="Current Hero" class="img-thumbnail">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control" id="hero_image" name="hero_image">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="button_text" class="form-label">Button Text</label>
                                    <input type="text" class="form-control" id="button_text" name="button_text" 
                                           value="{{ $heroSettings['button_text'] ?? 'Get Started' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="button_url" class="form-label">Button URL</label>
                                    <input type="text" class="form-control" id="button_url" name="button_url" 
                                           value="{{ $heroSettings['button_url'] ?? '/courses' }}">
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> حفظ إعدادات القسم الرئيسي
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Features Tab -->
            <div class="tab-pane fade" id="features" role="tabpanel" aria-labelledby="features-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0" style="color: var(--primary-color); font-weight: 600;">إعدادات قسم المميزات</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.website-appearance.features') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="features_title" class="form-label">Section Title</label>
                                <input type="text" class="form-control" id="features_title" name="features_title" 
                                       value="{{ $featuresSettings['features_title'] ?? 'Our Features' }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="features_subtitle" class="form-label">Section Subtitle</label>
                                <textarea class="form-control" id="features_subtitle" name="features_subtitle" rows="2">{{ $featuresSettings['features_subtitle'] ?? 'Discover what makes our platform different' }}</textarea>
                            </div>
                            
                            <div class="mt-4 mb-3">
                                <h6 class="fw-bold">Features List</h6>
                                <div id="features-container">
                                    @if(isset($featuresSettings['features_list']) && is_array($featuresSettings['features_list']))
                                        @foreach($featuresSettings['features_list'] as $index => $feature)
                                            <div class="repeater-item-container">
                                                <h6 style="color: var(--primary-color); font-weight: 600;">Feature #{{ $index + 1 }}</h6>
                                                <div class="row">
                                                    <div class="col-md-5 mb-3">
                                                        <label class="form-label">العنوان</label>
                                                        <input type="text" class="form-control" name="features[{{ $index }}][title]" 
                                                               value="{{ $feature['title'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label">الأيقونة</label>
                                                        <input type="text" class="form-control" name="features[{{ $index }}][icon]" 
                                                               value="{{ $feature['icon'] ?? 'fa-star' }}">
                                                        <small class="text-muted">اسم أيقونة FontAwesome (مثال: fa-star)</small>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label">اللون</label>
                                                        <input type="color" class="form-control form-control-color" name="features[{{ $index }}][color]" 
                                                               value="{{ $feature['color'] ?? '#4361ee' }}">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label class="form-label">الوصف</label>
                                                        <textarea class="form-control" name="features[{{ $index }}][description]" rows="2">{{ $feature['description'] ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger remove-feature remove-repeater">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <button type="button" id="add-feature" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus me-1"></i> Add Feature
                                </button>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> حفظ إعدادات المميزات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Stats Tab -->
            <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0" style="color: var(--primary-color); font-weight: 600;">إعدادات الإحصائيات</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.website-appearance.stats') }}" method="POST">
                            @csrf
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="students_count" class="form-label">Students Count</label>
                                    <input type="number" class="form-control" id="students_count" name="students_count" 
                                           value="{{ $statsSettings['students_count'] ?? 0 }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="students_text" class="form-label">Students Text</label>
                                    <input type="text" class="form-control" id="students_text" name="students_text" 
                                           value="{{ $statsSettings['students_text'] ?? 'Happy Students' }}">
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="courses_count" class="form-label">Courses Count</label>
                                    <input type="number" class="form-control" id="courses_count" name="courses_count" 
                                           value="{{ $statsSettings['courses_count'] ?? 0 }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="courses_text" class="form-label">Courses Text</label>
                                    <input type="text" class="form-control" id="courses_text" name="courses_text" 
                                           value="{{ $statsSettings['courses_text'] ?? 'Online Courses' }}">
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="instructors_count" class="form-label">Instructors Count</label>
                                    <input type="number" class="form-control" id="instructors_count" name="instructors_count" 
                                           value="{{ $statsSettings['instructors_count'] ?? 0 }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="instructors_text" class="form-label">Instructors Text</label>
                                    <input type="text" class="form-control" id="instructors_text" name="instructors_text" 
                                           value="{{ $statsSettings['instructors_text'] ?? 'Expert Instructors' }}">
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> حفظ إعدادات الإحصائيات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- About Section Tab -->
            <div class="tab-pane fade" id="about" role="tabpanel" aria-labelledby="about-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0" style="color: var(--primary-color); font-weight: 600;">إعدادات قسم من نحن</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.website-appearance.about') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="about_title" class="form-label">Section Title</label>
                                <input type="text" class="form-control" id="about_title" name="about_title" 
                                       value="{{ $aboutSettings['about_title'] ?? 'ما هو TOTO؟' }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="about_description" class="form-label">Section Description</label>
                                <textarea class="form-control" id="about_description" name="about_description" rows="4">{{ $aboutSettings['about_description'] ?? 'هي منصة تعليمية متكاملة تتيح للمعلمين إنشاء فصول دراسية عبر الإنترنت حيث يمكنهم تخزين المواد التعليمية عبر الإنترنت، وإدارة الواجبات والاختبارات ومتابعة مواعيد التسليم TOTO وتقييم النتائج وتزويد الطلاب بالملاحظات، كل ذلك في مكان واحد' }}</textarea>
                            </div>
                            
                            <div class="section-divider"></div>
                            <h5 class="section-subtitle">For Instructors Card</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="instructor_title" class="form-label">Instructor Title</label>
                                    <input type="text" class="form-control" id="instructor_title" name="instructor_title" 
                                           value="{{ $aboutSettings['instructor_title'] ?? 'للمدرسين' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="instructor_button_text" class="form-label">Instructor Button Text</label>
                                    <input type="text" class="form-control" id="instructor_button_text" name="instructor_button_text" 
                                           value="{{ $aboutSettings['instructor_button_text'] ?? 'أنشئ فصلاً دراسياً الآن' }}">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="instructor_image" class="form-label">Instructor Image</label>
                                @if(isset($aboutSettings['instructor_image']))
                                    <div class="image-preview-container">
                                        <img src="{{ asset('storage/' . $aboutSettings['instructor_image']) }}" 
                                             alt="Instructor Image" class="img-thumbnail">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="instructor_image" name="instructor_image">
                                <small class="text-muted">Recommended size: 400x300px. Leave empty to keep current image.</small>
                            </div>
                            
                            <div class="section-divider"></div>
                            <h5 class="section-subtitle">For Students Card</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="student_title" class="form-label">Student Title</label>
                                    <input type="text" class="form-control" id="student_title" name="student_title" 
                                           value="{{ $aboutSettings['student_title'] ?? 'للطلاب' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="student_button_text" class="form-label">Student Button Text</label>
                                    <input type="text" class="form-control" id="student_button_text" name="student_button_text" 
                                           value="{{ $aboutSettings['student_button_text'] ?? 'أدخل رمز الوصول' }}">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="student_image" class="form-label">Student Image</label>
                                @if(isset($aboutSettings['student_image']))
                                    <div class="image-preview-container">
                                        <img src="{{ asset('storage/' . $aboutSettings['student_image']) }}" 
                                             alt="Student Image" class="img-thumbnail">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="student_image" name="student_image">
                                <small class="text-muted">Recommended size: 400x300px. Leave empty to keep current image.</small>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> حفظ إعدادات قسم من نحن
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Video Section Tab -->
            <div class="tab-pane fade" id="video" role="tabpanel" aria-labelledby="video-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0" style="color: var(--primary-color); font-weight: 600;">إعدادات قسم الفيديو</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.website-appearance.video') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="video_title" class="form-label">Video Main Title</label>
                                <input type="text" class="form-control" id="video_title" name="video_title" 
                                       value="{{ $videoSettings['video_title'] ?? 'كل ما يمكنك فعله في الفصل الدراسي التقليدي' }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="video_subtitle" class="form-label">Video Subtitle</label>
                                <input type="text" class="form-control" id="video_subtitle" name="video_subtitle" 
                                       value="{{ $videoSettings['video_subtitle'] ?? 'يمكنك فعله مع TOTO' }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="video_description" class="form-label">Video Description</label>
                                <textarea class="form-control" id="video_description" name="video_description" rows="4">{{ $videoSettings['video_description'] ?? 'تساعد منصة TOTO التعليمية المدارس التقليدية والإلكترونية على إدارة الجداول الدراسية، وتسجيل الحضور، وإدارة المدفوعات، والفصول الافتراضية، كل ذلك في نظام آمن قائم على الحوسبة السحابية.' }}</textarea>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="video_button_text" class="form-label">Button Text</label>
                                    <input type="text" class="form-control" id="video_button_text" name="video_button_text" 
                                           value="{{ $videoSettings['video_button_text'] ?? 'اكتشف المزيد' }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="video_button_url" class="form-label">Button URL</label>
                                    <input type="text" class="form-control" id="video_button_url" name="video_button_url" 
                                           value="{{ $videoSettings['video_button_url'] ?? '#features' }}">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="video_embed_url" class="form-label">YouTube Embed URL</label>
                                <input type="text" class="form-control" id="video_embed_url" name="video_embed_url" 
                                       value="{{ $videoSettings['video_embed_url'] ?? 'https://www.youtube.com/embed/dQw4w9WgXcQ' }}">
                                <small class="text-muted">Enter the YouTube video embed URL (e.g., https://www.youtube.com/embed/VIDEO_ID)</small>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> حفظ إعدادات قسم الفيديو
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Partners Section Tab -->
            <div class="tab-pane fade" id="partners" role="tabpanel" aria-labelledby="partners-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0" style="color: var(--primary-color); font-weight: 600;">إعدادات قسم الشركاء</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.website-appearance.partners') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="partners_title" class="form-label">Section Title</label>
                                <input type="text" class="form-control" id="partners_title" name="partners_title" 
                                       value="{{ $partnersSettings['partners_title'] ?? 'شركاؤنا المميزون' }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="partners_subtitle" class="form-label">Section Subtitle</label>
                                <textarea class="form-control" id="partners_subtitle" name="partners_subtitle" rows="2">{{ $partnersSettings['partners_subtitle'] ?? 'نتعاون مع أفضل المؤسسات والشركات لتقديم تجربة تعليمية متميزة' }}</textarea>
                            </div>
                            
                            <div class="mt-4 mb-3">
                                <h6 class="fw-bold">Partners List</h6>
                                <div id="partners-container">
                                    @if(isset($partnersSettings['partners_list']) && is_array($partnersSettings['partners_list']))
                                        @foreach($partnersSettings['partners_list'] as $index => $partner)
                                            <div class="repeater-item-container">
                                                <h6 style="color: var(--primary-color); font-weight: 600;">Partner #{{ $index + 1 }}</h6>
                                                <div class="row">
                                                    <div class="col-md-5 mb-3">
                                                        <label class="form-label">اسم الشريك</label>
                                                        <input type="text" class="form-control" name="partners[{{ $index }}][name]" 
                                                               value="{{ $partner['name'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-7 mb-3">
                                                        <label class="form-label">رابط الموقع</label>
                                                        <input type="text" class="form-control" name="partners[{{ $index }}][url]" 
                                                               value="{{ $partner['url'] ?? '' }}">
                                                        <small class="text-muted">رابط اختياري إلى موقع الشريك</small>
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <label class="form-label">الشعار</label>
                                                        @if(isset($partner['logo']))
                                                            <div class="image-preview-container">
                                                                <img src="{{ asset('storage/' . $partner['logo']) }}" 
                                                                     alt="{{ $partner['name'] }}" class="img-thumbnail">
                                                            </div>
                                                        @endif
                                                        <input type="file" class="form-control" name="partners[{{ $index }}][logo]">
                                                        <small class="text-muted">الحجم الموصى به: 200×100 بكسل مع خلفية شفافة.</small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger remove-partner remove-repeater">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <button type="button" id="add-partner" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus me-1"></i> Add Partner
                                </button>
                            </div>
                            
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> حفظ إعدادات الشركاء
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- إضافة قسم تعديل الفوتر بعد الأقسام الأخرى -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-shoe-prints me-2"></i> إعدادات تذييل الصفحة (الفوتر)</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.website-appearance.update-footer') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="footer_description" class="form-label">وصف الفوتر</label>
                    <textarea name="footer_description" id="footer_description" rows="3" class="form-control">{{ $footerSettings['footer_description'] ?? '' }}</textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="footer_phone" class="form-label">رقم الهاتف</label>
                            <input type="text" name="footer_phone" id="footer_phone" class="form-control" value="{{ $footerSettings['footer_phone'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="footer_email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="footer_email" id="footer_email" class="form-control" value="{{ $footerSettings['footer_email'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="footer_address" class="form-label">العنوان</label>
                            <input type="text" name="footer_address" id="footer_address" class="form-control" value="{{ $footerSettings['footer_address'] ?? '' }}">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="footer_copyright" class="form-label">نص حقوق النشر</label>
                    <input type="text" name="footer_copyright" id="footer_copyright" class="form-control" value="{{ $footerSettings['footer_copyright'] ?? 'جميع الحقوق محفوظة.' }}">
                </div>
                
                <h6 class="mt-4 mb-3">روابط سريعة</h6>
                <div id="quick-links-container">
                    @php
                        $quickLinks = json_decode($footerSettings['footer_links'] ?? '[]', true);
                        if (empty($quickLinks)) {
                            $quickLinks = [
                                ['title' => 'الرئيسية', 'url' => '/'],
                                ['title' => 'الكورسات', 'url' => '/courses'],
                                ['title' => 'من نحن', 'url' => '/about'],
                                ['title' => 'اتصل بنا', 'url' => '/contact'],
                            ];
                        }
                    @endphp
                    
                    @foreach($quickLinks as $index => $link)
                    <div class="row link-row mb-2">
                        <div class="col-md-5">
                            <input type="text" name="quick_links[{{ $index }}][title]" class="form-control" placeholder="عنوان الرابط" value="{{ $link['title'] ?? '' }}">
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="quick_links[{{ $index }}][url]" class="form-control" placeholder="مسار الرابط" value="{{ $link['url'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-danger remove-link"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" id="add-quick-link" class="btn btn-sm btn-primary mt-2">
                    <i class="fas fa-plus"></i> إضافة رابط
                </button>
                
                <h6 class="mt-4 mb-3">روابط التواصل الاجتماعي</h6>
                <div id="social-links-container">
                    @php
                        $socialLinks = json_decode($footerSettings['footer_social_links'] ?? '[]', true);
                        if (empty($socialLinks)) {
                            $socialLinks = [
                                ['platform' => 'facebook', 'url' => '#', 'icon' => 'fab fa-facebook-f'],
                                ['platform' => 'twitter', 'url' => '#', 'icon' => 'fab fa-twitter'],
                                ['platform' => 'instagram', 'url' => '#', 'icon' => 'fab fa-instagram'],
                                ['platform' => 'linkedin', 'url' => '#', 'icon' => 'fab fa-linkedin-in'],
                            ];
                        }
                    @endphp
                    
                    @foreach($socialLinks as $index => $link)
                    <div class="row social-row mb-2">
                        <div class="col-md-3">
                            <select name="social_links[{{ $index }}][platform]" class="form-select social-platform-select">
                                <option value="facebook" data-icon="fab fa-facebook-f" {{ $link['platform'] == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                <option value="twitter" data-icon="fab fa-twitter" {{ $link['platform'] == 'twitter' ? 'selected' : '' }}>Twitter</option>
                                <option value="instagram" data-icon="fab fa-instagram" {{ $link['platform'] == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                <option value="linkedin" data-icon="fab fa-linkedin-in" {{ $link['platform'] == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                                <option value="youtube" data-icon="fab fa-youtube" {{ $link['platform'] == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="whatsapp" data-icon="fab fa-whatsapp" {{ $link['platform'] == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="social_links[{{ $index }}][url]" class="form-control" placeholder="رابط الصفحة" value="{{ $link['url'] ?? '' }}">
                            <input type="hidden" name="social_links[{{ $index }}][icon]" class="social-icon-input" value="{{ $link['icon'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-danger remove-social"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" id="add-social-link" class="btn btn-sm btn-primary mt-2">
                    <i class="fas fa-plus"></i> إضافة رابط اجتماعي
                </button>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">حفظ إعدادات الفوتر</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to update the repeater item title/numbering
        function updateRepeaterTitles(containerSelector, itemSelector, titlePrefix) {
            const container = document.querySelector(containerSelector);
            if (!container) return;
            const items = container.querySelectorAll(itemSelector);
            items.forEach((item, index) => {
                const titleElement = item.querySelector('h6');
                if (titleElement) {
                    titleElement.textContent = `${titlePrefix} #${index + 1}`;
                }
                // Update input names if necessary (example for features)
                if (titlePrefix === 'Feature') {
                    const inputs = item.querySelectorAll('[name^="features["]');
                    inputs.forEach(input => {
                        const name = input.getAttribute('name');
                        const newName = name.replace(/features\[\d+\]/, `features[${index}]`);
                        input.setAttribute('name', newName);
                    });
                }
                // Update input names if necessary (example for partners)
                if (titlePrefix === 'Partner') {
                    const inputs = item.querySelectorAll('[name^="partners["]');
                    inputs.forEach(input => {
                        const name = input.getAttribute('name');
                        const newName = name.replace(/partners\[\d+\]/, `partners[${index}]`);
                        input.setAttribute('name', newName);
                    });
                }
            });
        }

        // Features add/remove functionality
        const featuresContainer = document.getElementById('features-container');
        const addFeatureBtn = document.getElementById('add-feature');
        
        if (featuresContainer && addFeatureBtn) {
            let featureCount = featuresContainer.querySelectorAll('.repeater-item-container').length;
            
            addFeatureBtn.addEventListener('click', function() {
                const featureHtml = `
                    <div class="repeater-item-container">
                        <h6 style="color: var(--primary-color); font-weight: 600;">Feature #${featureCount + 1}</h6>
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label">العنوان</label>
                                <input type="text" class="form-control" name="features[${featureCount}][title]" value="">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">الأيقونة</label>
                                <input type="text" class="form-control" name="features[${featureCount}][icon]" value="fa-star">
                                <small class="text-muted">اسم أيقونة FontAwesome (مثال: fa-star)</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">اللون</label>
                                <input type="color" class="form-control form-control-color" name="features[${featureCount}][color]" value="#003366">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea class="form-control" name="features[${featureCount}][description]" rows="2"></textarea>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger remove-feature remove-repeater">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                
                featuresContainer.insertAdjacentHTML('beforeend', featureHtml);
                featureCount++;
                attachRemoveFeatureListeners(); // Attach listener to the new item
                updateRepeaterTitles('#features-container', '.repeater-item-container', 'Feature');
            });
            
            function attachRemoveFeatureListeners() {
                featuresContainer.querySelectorAll('.remove-feature').forEach(button => {
                    button.removeEventListener('click', removeFeature);
                    button.addEventListener('click', removeFeature);
                });
            }
            
            function removeFeature() {
                this.closest('.repeater-item-container').remove();
                updateRepeaterTitles('#features-container', '.repeater-item-container', 'Feature');
                featureCount = featuresContainer.querySelectorAll('.repeater-item-container').length; // Update count
            }
            
            attachRemoveFeatureListeners(); // Initial attachment
        }

        // Partners add/remove functionality
        const partnersContainer = document.getElementById('partners-container');
        const addPartnerBtn = document.getElementById('add-partner');

        if (partnersContainer && addPartnerBtn) {
            let partnerCount = partnersContainer.querySelectorAll('.repeater-item-container').length;

            addPartnerBtn.addEventListener('click', function() {
                const partnerHtml = `
                    <div class="repeater-item-container">
                         <h6 style="color: var(--primary-color); font-weight: 600;">Partner #${partnerCount + 1}</h6>
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label">اسم الشريك</label>
                                <input type="text" class="form-control" name="partners[${partnerCount}][name]" value="">
                            </div>
                            <div class="col-md-7 mb-3">
                                <label class="form-label">رابط الموقع</label>
                                <input type="text" class="form-control" name="partners[${partnerCount}][url]" value="">
                                <small class="text-muted">رابط اختياري إلى موقع الشريك</small>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">الشعار</label>
                                <input type="file" class="form-control" name="partners[${partnerCount}][logo]">
                                <small class="text-muted">الحجم الموصى به: 200×100 بكسل مع خلفية شفافة.</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger remove-partner remove-repeater">
                           <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                `;
                
                partnersContainer.insertAdjacentHTML('beforeend', partnerHtml);
                partnerCount++;
                attachRemovePartnerListeners(); // Attach listener to the new item
                updateRepeaterTitles('#partners-container', '.repeater-item-container', 'Partner');
            });

            function attachRemovePartnerListeners() {
                partnersContainer.querySelectorAll('.remove-partner').forEach(button => {
                    button.removeEventListener('click', removePartner);
                    button.addEventListener('click', removePartner);
                });
            }

            function removePartner() {
                this.closest('.repeater-item-container').remove();
                updateRepeaterTitles('#partners-container', '.repeater-item-container', 'Partner');
                partnerCount = partnersContainer.querySelectorAll('.repeater-item-container').length; // Update count
            }

            attachRemovePartnerListeners(); // Initial attachment
        }

        // Initial update for existing items
        updateRepeaterTitles('#features-container', '.repeater-item-container', 'Feature');
        updateRepeaterTitles('#partners-container', '.repeater-item-container', 'Partner');
    });

    // Quick Links Management
    $(document).ready(function() {
        // Add New Quick Link
        $('#add-quick-link').click(function() {
            const index = $('.link-row').length;
            const newRow = `
                <div class="row link-row mb-2">
                    <div class="col-md-5">
                        <input type="text" name="quick_links[${index}][title]" class="form-control" placeholder="عنوان الرابط">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="quick_links[${index}][url]" class="form-control" placeholder="مسار الرابط">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger remove-link"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            $('#quick-links-container').append(newRow);
        });
        
        // Remove Quick Link
        $(document).on('click', '.remove-link', function() {
            $(this).closest('.link-row').remove();
            reindexLinks();
        });
        
        // Add New Social Link
        $('#add-social-link').click(function() {
            const index = $('.social-row').length;
            const newRow = `
                <div class="row social-row mb-2">
                    <div class="col-md-3">
                        <select name="social_links[${index}][platform]" class="form-select social-platform-select">
                            <option value="facebook" data-icon="fab fa-facebook-f">Facebook</option>
                            <option value="twitter" data-icon="fab fa-twitter">Twitter</option>
                            <option value="instagram" data-icon="fab fa-instagram">Instagram</option>
                            <option value="linkedin" data-icon="fab fa-linkedin-in">LinkedIn</option>
                            <option value="youtube" data-icon="fab fa-youtube">YouTube</option>
                            <option value="whatsapp" data-icon="fab fa-whatsapp">WhatsApp</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="text" name="social_links[${index}][url]" class="form-control" placeholder="رابط الصفحة">
                        <input type="hidden" name="social_links[${index}][icon]" class="social-icon-input" value="fab fa-facebook-f">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger remove-social"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            $('#social-links-container').append(newRow);
            updateSocialIcons();
        });
        
        // Remove Social Link
        $(document).on('click', '.remove-social', function() {
            $(this).closest('.social-row').remove();
            reindexSocial();
        });
        
        // Update social icons when platform changes
        $(document).on('change', '.social-platform-select', function() {
            updateSocialIcons();
        });
        
        // Initialize icons
        updateSocialIcons();
        
        function updateSocialIcons() {
            $('.social-platform-select').each(function() {
                const selectedOption = $(this).find('option:selected');
                const icon = selectedOption.data('icon');
                $(this).closest('.social-row').find('.social-icon-input').val(icon);
            });
        }
        
        function reindexLinks() {
            $('.link-row').each(function(index) {
                $(this).find('input').each(function() {
                    const name = $(this).attr('name').replace(/quick_links\[\d+\]/, `quick_links[${index}]`);
                    $(this).attr('name', name);
                });
            });
        }
        
        function reindexSocial() {
            $('.social-row').each(function(index) {
                $(this).find('select, input').each(function() {
                    const name = $(this).attr('name').replace(/social_links\[\d+\]/, `social_links[${index}]`);
                    $(this).attr('name', name);
                });
            });
        }
    });
</script>
@endsection 