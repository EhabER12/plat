@extends('layouts.app')

@section('title', 'استكشف الدورات - منصة تعليمية')

@php
// دالة مساعدة للحصول على أيقونة الفئة
function getCategoryIcon($categoryName) {
    $iconMap = [
        'البرمجة' => 'code',
        'التصميم' => 'paint-brush',
        'الأعمال' => 'briefcase',
        'التسويق' => 'bullhorn',
        'اللغات' => 'language',
        'تطوير الويب' => 'globe',
        'تطوير التطبيقات' => 'mobile-alt',
        'الذكاء الاصطناعي' => 'robot',
        'قواعد البيانات' => 'database',
        'الشبكات' => 'network-wired',
        'الأمن السيبراني' => 'shield-alt',
        'التصميم الجرافيكي' => 'palette',
        'تصميم واجهات المستخدم' => 'desktop',
        'التصوير' => 'camera',
        'الفيديو' => 'video',
        'الصوت' => 'microphone',
        'الموسيقى' => 'music',
        'الرياضيات' => 'calculator',
        'العلوم' => 'flask',
        'الصحة' => 'heartbeat',
        'الرياضة' => 'running',
        'الطبخ' => 'utensils',
        'السفر' => 'plane',
        'الفن' => 'palette',
        'التاريخ' => 'book',
        'الجغرافيا' => 'map',
        'الفلسفة' => 'brain',
        'علم النفس' => 'brain',
        'الاقتصاد' => 'chart-line',
        'القانون' => 'gavel',
        'الطب' => 'stethoscope',
        'الهندسة' => 'cogs',
        'العمارة' => 'building',
        'الزراعة' => 'leaf',
        'البيئة' => 'tree',
        'الفلك' => 'star',
        'الفيزياء' => 'atom',
        'الكيمياء' => 'vial',
        'الأحياء' => 'dna',
        'الجيولوجيا' => 'mountain',
        'الإحصاء' => 'chart-bar',
        'المحاسبة' => 'calculator',
        'التمويل' => 'money-bill-wave',
        'الإدارة' => 'tasks',
        'الموارد البشرية' => 'users',
        'التسويق الرقمي' => 'ad',
        'وسائل التواصل الاجتماعي' => 'hashtag',
        'تحسين محركات البحث' => 'search',
        'التجارة الإلكترونية' => 'shopping-cart',
        'ريادة الأعمال' => 'lightbulb',
        'المبيعات' => 'handshake',
        'خدمة العملاء' => 'headset',
        'المهارات الشخصية' => 'user',
        'القيادة' => 'crown',
        'إدارة الوقت' => 'clock',
        'التواصل' => 'comments',
        'العرض التقديمي' => 'presentation',
        'كتابة المحتوى' => 'pen',
        'الترجمة' => 'language',
        'الكتابة الإبداعية' => 'feather',
        'الصحافة' => 'newspaper',
        'التدريس' => 'chalkboard-teacher',
        'التعليم' => 'graduation-cap',
    ];

    // تحويل اسم الفئة إلى حروف صغيرة للمطابقة
    $lowerCaseName = mb_strtolower($categoryName);

    // البحث عن الأيقونة المناسبة
    foreach ($iconMap as $key => $icon) {
        if (mb_strpos($lowerCaseName, mb_strtolower($key)) !== false) {
            return $icon;
        }
    }

    // أيقونة افتراضية إذا لم يتم العثور على مطابقة
    return 'folder';
}
@endphp

@section('styles')
<style>
    /* أنماط صفحة الكورسات */
    :root {
        --primary-color: #003366;
        --secondary-color: #002244;
        --accent-color: #FFD700;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --text-color: #333;
        --text-light-color: #666;
        --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
        --hover-shadow: 0 15px 35px rgba(0,0,0,0.15);
        --transition: all 0.3s ease;
    }

    body {
        direction: rtl;
        text-align: right;
        font-family: 'Tajawal', 'Cairo', sans-serif;
    }

    .courses-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        padding: 80px 0 50px;
        color: white;
        margin-bottom: 50px;
        position: relative;
        overflow: hidden;
        text-align: center;
    }

    .courses-header::before {
        content: '';
        position: absolute;
        top: -50px;
        left: -50px;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        z-index: 1;
    }

    .courses-header::after {
        content: '';
        position: absolute;
        bottom: -80px;
        right: -80px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        z-index: 1;
    }

    .courses-header-content {
        position: relative;
        z-index: 2;
    }

    .courses-title {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 15px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        background: linear-gradient(90deg, var(--accent-color) 0%, #FFFFFF 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: inline-block;
    }

    .courses-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        max-width: 700px;
        margin: 0 auto 30px;
        line-height: 1.6;
    }

    /* أنماط نظام الفلترة الجديد */
    .filters-container {
        background-color: white;
        border-radius: 15px;
        box-shadow: var(--card-shadow);
        margin-bottom: 30px;
        padding: 20px;
        position: relative;
    }

    .filters-row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-label {
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 10px;
        display: block;
        font-size: 0.95rem;
    }

    .filter-dropdown {
        width: 100%;
        padding: 10px 15px;
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        background-color: white;
        font-size: 0.95rem;
        color: var(--text-color);
        cursor: pointer;
        transition: var(--transition);
    }

    .filter-dropdown:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(0, 51, 102, 0.15);
        outline: none;
    }

    .category-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 20px;
    }

    .category-filter {
        display: inline-flex;
        align-items: center;
        padding: 8px 15px;
        background-color: #f0f2f5;
        border-radius: 30px;
        border: 2px solid transparent;
        cursor: pointer;
        transition: var(--transition);
        font-size: 0.9rem;
        color: var(--text-color);
    }

    .category-filter:hover {
        background-color: #e6e9f0;
        transform: translateY(-2px);
    }

    .category-filter.active {
        background-color: rgba(0, 51, 102, 0.1);
        border-color: var(--primary-color);
        color: var(--primary-color);
        font-weight: 600;
    }

    .category-filter i {
        margin-left: 8px;
        font-size: 1rem;
        color: var(--primary-color);
    }

    .filter-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .filter-btn {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: var(--transition);
    }

    .apply-filters-btn {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        border: none;
    }

    .apply-filters-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);
    }

    .reset-filters-btn {
        background-color: transparent;
        color: var(--text-color);
        border: 1px solid #e0e0e0;
    }

    .reset-filters-btn:hover {
        background-color: #f8f9fa;
        border-color: #d0d0d0;
    }

    /* فلاتر الأسعار */
    .price-range-slider {
        margin-top: 15px;
        padding: 0 10px;
    }

    .price-inputs {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
    }

    .price-input {
        width: 45%;
    }

    .price-input input {
        width: 100%;
        padding: 8px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        text-align: center;
    }

    /* فلاتر التقييم */
    .rating-filters {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .rating-option {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: var(--transition);
    }

    .rating-option:hover {
        background-color: #f0f2f5;
    }

    .rating-option.active {
        background-color: rgba(0, 51, 102, 0.1);
    }

    .rating-stars {
        color: #ffc107;
        margin-left: 8px;
    }

    /* فلاتر المستوى */
    .level-filters {
        display: flex;
        gap: 10px;
    }

    .level-option {
        flex: 1;
        text-align: center;
        padding: 10px;
        border-radius: 10px;
        background-color: #f0f2f5;
        cursor: pointer;
        transition: var(--transition);
        border: 2px solid transparent;
    }

    .level-option:hover {
        background-color: #e6e9f0;
    }

    .level-option.active {
        background-color: rgba(0, 51, 102, 0.1);
        border-color: var(--primary-color);
        color: var(--primary-color);
        font-weight: 600;
    }

    /* فلاتر نشطة */
    .active-filters {
        margin: 0 0 20px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .filter-pill {
        display: inline-flex;
        align-items: center;
        background-color: rgba(0, 51, 102, 0.08);
        color: var(--primary-color);
        padding: 6px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid rgba(0, 51, 102, 0.15);
        transition: var(--transition);
    }

    .filter-pill:hover {
        background-color: rgba(0, 51, 102, 0.12);
        transform: translateY(-2px);
    }

    .filter-pill .remove-filter {
        cursor: pointer;
        margin-right: 5px;
        margin-left: 8px;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 51, 102, 0.15);
        border-radius: 50%;
        transition: var(--transition);
    }

    .filter-pill:hover .remove-filter {
        background-color: rgba(0, 51, 102, 0.25);
    }

    .filter-pill .remove-filter i {
        font-size: 0.7rem;
        color: var(--primary-color);
    }

    /* Form Controls */
    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 15px;
        border-color: #e0e0e0;
        font-size: 0.95rem;
        box-shadow: none;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
    }

    /* بطاقات الكورسات */
    .course-card {
        border-radius: 15px;
        border: none;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        height: 100%;
        display: flex;
        flex-direction: column;
        background-color: white;
    }

    .course-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--hover-shadow);
    }

    .course-image {
        height: 220px;
        position: relative;
        overflow: hidden;
    }

    .course-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.7s ease;
    }

    .course-card:hover .course-image img {
        transform: scale(1.1);
    }

    .course-image::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0) 50%);
        z-index: 1;
    }

    .course-category {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background-color: rgba(255, 255, 255, 0.9);
        color: var(--primary-color);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        z-index: 2;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .course-price {
        position: absolute;
        top: 15px;
        left: 15px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 700;
        z-index: 2;
        box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    }

    .course-rating-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: rgba(255, 255, 255, 0.9);
        color: #333;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        z-index: 2;
        display: flex;
        align-items: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .course-rating-badge i {
        color: #ffc107;
        margin-left: 5px;
    }

    .course-content {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .course-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 12px;
        color: var(--text-color);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.5;
    }

    .course-instructor {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        color: var(--text-light-color);
        font-size: 0.95rem;
    }

    .course-instructor i {
        margin-left: 8px;
        color: var(--primary-color);
    }

    .course-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 0.9rem;
        color: var(--text-light-color);
    }

    .course-stat {
        display: flex;
        align-items: center;
    }

    .course-stat i {
        margin-left: 8px;
        color: var(--primary-color);
    }

    .course-rating {
        color: #ffc107;
    }

    .course-description {
        color: var(--text-light-color);
        font-size: 0.95rem;
        margin-bottom: 20px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.7;
        flex-grow: 1;
    }

    .course-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 25px;
        background-color: #f8f9fa;
        border-top: 1px solid #f0f0f0;
    }

    .course-btn {
        border-radius: 10px;
        padding: 8px 20px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .course-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
    }

    .course-btn i {
        font-size: 0.85rem;
    }

    .course-meta-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        color: var(--text-light-color);
        font-size: 0.9rem;
    }

    .course-meta-item i {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 51, 102, 0.1);
        color: var(--primary-color);
        border-radius: 50%;
        margin-left: 10px;
        font-size: 0.8rem;
    }

    .course-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .badge-item {
        background-color: rgba(255, 255, 255, 0.9);
        color: var(--primary-color);
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .badge-item i {
        margin-left: 5px;
    }

    .badge-featured {
        background-color: var(--accent-color);
        color: var(--primary-color);
    }

    /* الترقيم */
    .pagination {
        margin-top: 50px;
        display: flex;
        justify-content: center;
        gap: 8px;
        direction: ltr; /* نحتفظ باتجاه LTR للترقيم */
    }

    .page-item:first-child .page-link,
    .page-item:last-child .page-link {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* تبديل أيقونات السابق والتالي */
    .page-item:first-child .page-link i.fa-chevron-left {
        transform: rotate(180deg);
    }

    .page-item:last-child .page-link i.fa-chevron-right {
        transform: rotate(180deg);
    }

    .page-link {
        min-width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        border-color: #e0e0e0;
        font-weight: 600;
        border-radius: 8px !important;
        margin: 0;
        padding: 0.5rem 0.75rem;
        transition: var(--transition);
    }

    .page-link:hover {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 51, 102, 0.2);
    }

    .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 51, 102, 0.2);
    }

    .page-item.disabled .page-link {
        color: #aaa;
        background-color: #f8f9fa;
        border-color: #e0e0e0;
        pointer-events: none;
    }

    /* قسم لا توجد كورسات */
    .no-courses {
        text-align: center;
        padding: 80px 0;
        background-color: white;
        border-radius: 15px;
        box-shadow: var(--card-shadow);
        margin: 30px 0;
    }

    .no-courses-icon {
        width: 100px;
        height: 100px;
        background-color: rgba(0, 51, 102, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
    }

    .no-courses i {
        font-size: 3rem;
        color: var(--primary-color);
    }

    .no-courses h3 {
        font-weight: 700;
        margin-bottom: 15px;
        color: var(--text-color);
        font-size: 1.8rem;
    }

    .no-courses p {
        color: var(--text-light-color);
        max-width: 500px;
        margin: 0 auto 25px;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .no-courses .btn {
        padding: 10px 25px;
        font-weight: 600;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border: none;
        transition: var(--transition);
    }

    .no-courses .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 51, 102, 0.2);
    }

    /* Loading Spinner */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #4361ee;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive Adjustments */
    @media (max-width: 991.98px) {
        .filter-sidebar {
            position: relative;
            top: 0;
            margin-bottom: 20px;
        }
    }
</style>
@endsection

@section('content')
    <!-- Loading Overlay -->
    <div class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- رأس صفحة الكورسات -->
    <div class="courses-header">
        <div class="container">
            <div class="courses-header-content">
                <h1 class="courses-title">استكشف الدورات التعليمية</h1>
                <p class="courses-subtitle">اكتشف مجموعة واسعة من الدورات التي يقدمها مدربون خبراء في مجالاتهم</p>
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="alert alert-warning">
            <p>Note: There was an issue connecting to the database. This is a demo view.</p>
            <small>Error: {{ $error }}</small>
        </div>
    @endif

    <!-- قسم الفلاتر الجديد -->
    <div class="container">
        <!-- نظام الفلترة الأفقي الجديد -->
        <div class="filters-container">
            <form id="filterForm" action="{{ route('courses.index') }}" method="GET">
                <!-- صف الفلاتر الرئيسي -->
                <div class="filters-row">
                    <!-- فلتر الترتيب -->
                    <div class="filter-group">
                        <label for="sortFilter" class="filter-label">ترتيب حسب</label>
                        <select name="sort" class="filter-dropdown" id="sortFilter">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>الأحدث</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>الأكثر شعبية</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>الأعلى تقييماً</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>السعر: من الأقل للأعلى</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>السعر: من الأعلى للأقل</option>
                        </select>
                    </div>

                    <!-- فلتر المستوى -->
                    <div class="filter-group">
                        <label class="filter-label">المستوى</label>
                        <div class="level-filters">
                            <div class="level-option {{ request('level') == 'beginner' ? 'active' : '' }}" data-value="beginner">
                                مبتدئ
                            </div>
                            <div class="level-option {{ request('level') == 'intermediate' ? 'active' : '' }}" data-value="intermediate">
                                متوسط
                            </div>
                            <div class="level-option {{ request('level') == 'advanced' ? 'active' : '' }}" data-value="advanced">
                                متقدم
                            </div>
                            <input type="hidden" name="level" id="levelInput" value="{{ request('level') }}">
                        </div>
                    </div>

                    <!-- فلتر السعر -->
                    <div class="filter-group">
                        <label class="filter-label">السعر</label>
                        <select name="price_range" class="filter-dropdown" id="priceFilter">
                            <option value="" {{ !request('price_range') ? 'selected' : '' }}>جميع الأسعار</option>
                            <option value="free" {{ request('price_range') == 'free' ? 'selected' : '' }}>مجاني</option>
                            <option value="paid" {{ request('price_range') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                            <option value="0-25" {{ request('price_range') == '0-25' ? 'selected' : '' }}>أقل من 25$</option>
                            <option value="25-50" {{ request('price_range') == '25-50' ? 'selected' : '' }}>25$ - 50$</option>
                            <option value="50-100" {{ request('price_range') == '50-100' ? 'selected' : '' }}>50$ - 100$</option>
                            <option value="100+" {{ request('price_range') == '100+' ? 'selected' : '' }}>أكثر من 100$</option>
                        </select>
                    </div>
                </div>

                <!-- فلتر الفئات -->
                <div class="filter-group">
                    <label class="filter-label">الفئات</label>
                    <div class="category-filters">
                        <div class="category-filter {{ !request('categories') ? 'active' : '' }}" data-value="">
                            <i class="fas fa-th-large"></i> جميع الفئات
                        </div>
                        @foreach($categories as $category)
                            <div class="category-filter {{ (is_array(request()->get('categories')) && in_array($category->category_id, request()->get('categories'))) ? 'active' : '' }}"
                                 data-value="{{ $category->category_id }}">
                                <i class="fas fa-{{ getCategoryIcon($category->name) }}"></i> {{ $category->name }}
                            </div>
                        @endforeach
                        <input type="hidden" name="categories" id="categoriesInput" value="{{ is_array(request()->get('categories')) ? implode(',', request()->get('categories')) : request()->get('categories') }}">
                    </div>
                </div>

                <!-- أزرار الفلترة -->
                <div class="filter-actions">
                    <div>
                        <!-- الفلاتر النشطة -->
                        <div class="active-filters" id="activeFilters">
                            @if(request()->has('categories') || request()->has('sort') || request()->has('level') || request()->has('price_range'))
                                <span class="me-2">الفلاتر النشطة:</span>

                                @if(request()->has('categories') && is_array(request()->get('categories')))
                                    @foreach(request('categories') as $categoryId)
                                        @php
                                            $foundCategory = null;
                                            foreach ($categories as $cat) {
                                                if ($cat->category_id == $categoryId) {
                                                    $foundCategory = $cat;
                                                    break;
                                                }
                                            }
                                        @endphp
                                        @if($foundCategory)
                                        <div class="filter-pill">
                                            {{ $foundCategory->name }}
                                            <span class="remove-filter" data-type="category" data-value="{{ $categoryId }}">
                                                <i class="fas fa-times"></i>
                                            </span>
                                        </div>
                                        @endif
                                    @endforeach
                                @endif

                                @if(request('level'))
                                    <div class="filter-pill">
                                        @php
                                            $levelText = [
                                                'beginner' => 'مبتدئ',
                                                'intermediate' => 'متوسط',
                                                'advanced' => 'متقدم'
                                            ][request('level')] ?? request('level');
                                        @endphp
                                        {{ $levelText }}
                                        <span class="remove-filter" data-type="level">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    </div>
                                @endif

                                @if(request('price_range'))
                                    <div class="filter-pill">
                                        @php
                                            $priceText = [
                                                'free' => 'مجاني',
                                                'paid' => 'مدفوع',
                                                '0-25' => 'أقل من 25$',
                                                '25-50' => '25$ - 50$',
                                                '50-100' => '50$ - 100$',
                                                '100+' => 'أكثر من 100$'
                                            ][request('price_range')] ?? request('price_range');
                                        @endphp
                                        {{ $priceText }}
                                        <span class="remove-filter" data-type="price_range">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    </div>
                                @endif

                                @if(request('sort') && request('sort') != 'newest')
                                    <div class="filter-pill">
                                        @php
                                            $sortText = [
                                                'popular' => 'الأكثر شعبية',
                                                'rating' => 'الأعلى تقييماً',
                                                'price_low' => 'السعر: من الأقل للأعلى',
                                                'price_high' => 'السعر: من الأعلى للأقل'
                                            ][request('sort')] ?? request('sort');
                                        @endphp
                                        {{ $sortText }}
                                        <span class="remove-filter" data-type="sort">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    </div>
                                @endif
                            @else
                                <span class="text-muted">لا توجد فلاتر نشطة</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn filter-btn apply-filters-btn">
                            <i class="fas fa-filter ms-1"></i> تطبيق الفلاتر
                        </button>
                        <a href="{{ route('courses.index') }}" class="btn filter-btn reset-filters-btn">
                            <i class="fas fa-sync-alt ms-1"></i> إعادة ضبط
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-12">

                <!-- حاوية الكورسات -->
                <div id="coursesContentContainer">
                    @if(isset($error))
                        <div class="alert alert-warning">
                            {{ $error }}
                        </div>
                    @elseif(count($courses) > 0)
                        <div class="row g-4" id="coursesList">
                            @foreach($courses as $course)
                                <div class="col-lg-4 col-md-6">
                                    <div class="course-card">
                                        <div class="course-image">
                                            @if(isset($course->thumbnail) && !empty($course->thumbnail))
                                                <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}">
                                            @else
                                                <img src="https://img.freepik.com/free-photo/education-day-arrangement-table-with-copy-space_23-2148721266.jpg" alt="{{ $course->title }}">
                                            @endif

                                            <!-- شارات الكورس -->
                                            <div class="course-badge">
                                                @if($course->featured)
                                                <div class="badge-item badge-featured">
                                                    <i class="fas fa-star"></i> مميز
                                                </div>
                                                @endif

                                                @php
                                                    $avgRating = $course->average_rating ??
                                                        ($course->reviews && $course->reviews->count() > 0 ?
                                                        $course->reviews->avg('rating') : 0);
                                                @endphp

                                                @if($avgRating > 0)
                                                <div class="course-rating-badge">
                                                    {{ number_format($avgRating, 1) }} <i class="fas fa-star"></i>
                                                </div>
                                                @endif
                                            </div>

                                            <div class="course-category">{{ $course->category->name ?? 'غير مصنف' }}</div>
                                            <div class="course-price">{{ $course->price > 0 ? "$course->price$" : 'مجاني' }}</div>
                                        </div>
                                        <div class="course-content">
                                            <h3 class="course-title">{{ $course->title }}</h3>

                                            <div class="course-meta-item">
                                                <i class="fas fa-user-tie"></i>
                                                <span>{{ $course->instructor->name ?? 'غير معروف' }}</span>
                                            </div>

                                            <div class="course-meta-item">
                                                <i class="fas fa-users"></i>
                                                <span>{{ $course->students_count ?? $course->enrollments_count ?? '0' }} طالب</span>
                                            </div>

                                            <div class="course-meta-item">
                                                <i class="fas fa-clock"></i>
                                                <span>
                                                    @php
                                                        $duration = $course->duration ?? 0;
                                                        if (!$duration && isset($course->videos) && $course->videos->count() > 0) {
                                                            $duration = ceil($course->videos->sum('duration') / 60);
                                                        }
                                                    @endphp
                                                    {{ $duration }} ساعة
                                                </span>
                                            </div>

                                            @if(isset($course->level))
                                            <div class="course-meta-item">
                                                <i class="fas fa-signal"></i>
                                                <span>
                                                    @php
                                                        $levelText = [
                                                            'beginner' => 'مبتدئ',
                                                            'intermediate' => 'متوسط',
                                                            'advanced' => 'متقدم'
                                                        ][$course->level] ?? $course->level;
                                                    @endphp
                                                    {{ $levelText }}
                                                </span>
                                            </div>
                                            @endif

                                            <p class="course-description">{{ \Illuminate\Support\Str::limit($course->description, 120) }}</p>
                                        </div>
                                        <div class="course-footer">
                                            <span class="text-muted">تم التحديث {{ \Carbon\Carbon::parse($course->updated_at ?? now())->diffForHumans() }}</span>
                                            <a href="{{ url('/courses/' . ($course->course_id ?? $course->id ?? '')) }}" class="btn btn-outline-primary course-btn">
                                                <i class="fas fa-eye"></i> عرض الدورة
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($courses instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="d-flex justify-content-center pagination" id="pagination">
                                {{ $courses->appends(request()->except('page'))->links() }}
                            </div>
                        @endif
                    @else
                        <div class="no-courses">
                            <div class="no-courses-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3>لم يتم العثور على دورات</h3>
                            <p>لا توجد دورات تطابق معايير البحث الخاصة بك. حاول تعديل الفلاتر وإعادة البحث.</p>
                            <a href="{{ route('courses.index') }}" class="btn btn-primary">
                                <i class="fas fa-sync-alt ms-2"></i> إعادة ضبط الفلاتر
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تحديد العناصر
            const categoryFilters = document.querySelectorAll('.category-filter');
            const levelOptions = document.querySelectorAll('.level-option');
            const removeFilters = document.querySelectorAll('.remove-filter');
            const categoriesInput = document.getElementById('categoriesInput');
            const levelInput = document.getElementById('levelInput');
            const filterForm = document.getElementById('filterForm');

            // وظيفة مساعدة للحصول على أيقونة الفئة
            function getCategoryIcon(categoryName) {
                const iconMap = {
                    'البرمجة': 'code',
                    'التصميم': 'paint-brush',
                    'الأعمال': 'briefcase',
                    'التسويق': 'bullhorn',
                    'اللغات': 'language',
                    'تطوير الويب': 'globe',
                    'تطوير التطبيقات': 'mobile-alt',
                    'الذكاء الاصطناعي': 'robot',
                    'قواعد البيانات': 'database',
                    'الشبكات': 'network-wired',
                    'الأمن السيبراني': 'shield-alt',
                    'التصميم الجرافيكي': 'palette',
                    'تصميم واجهات المستخدم': 'desktop',
                    'التصوير': 'camera',
                    'الفيديو': 'video',
                    'الصوت': 'microphone',
                    'الموسيقى': 'music',
                    'الرياضيات': 'calculator',
                    'العلوم': 'flask',
                    'الصحة': 'heartbeat',
                    'الرياضة': 'running',
                    'الطبخ': 'utensils',
                    'السفر': 'plane',
                    'الفن': 'palette',
                    'التاريخ': 'book',
                    'الجغرافيا': 'map',
                    'الفلسفة': 'brain',
                    'علم النفس': 'brain',
                    'الاقتصاد': 'chart-line',
                    'القانون': 'gavel',
                    'الطب': 'stethoscope',
                    'الهندسة': 'cogs',
                    'العمارة': 'building',
                    'الزراعة': 'leaf',
                    'البيئة': 'tree',
                    'الفلك': 'star',
                    'الفيزياء': 'atom',
                    'الكيمياء': 'vial',
                    'الأحياء': 'dna',
                    'الجيولوجيا': 'mountain',
                    'الإحصاء': 'chart-bar',
                    'المحاسبة': 'calculator',
                    'التمويل': 'money-bill-wave',
                    'الإدارة': 'tasks',
                    'الموارد البشرية': 'users',
                    'التسويق الرقمي': 'ad',
                    'وسائل التواصل الاجتماعي': 'hashtag',
                    'تحسين محركات البحث': 'search',
                    'التجارة الإلكترونية': 'shopping-cart',
                    'ريادة الأعمال': 'lightbulb',
                    'المبيعات': 'handshake',
                    'خدمة العملاء': 'headset',
                    'المهارات الشخصية': 'user',
                    'القيادة': 'crown',
                    'إدارة الوقت': 'clock',
                    'التواصل': 'comments',
                    'العرض التقديمي': 'presentation',
                    'كتابة المحتوى': 'pen',
                    'الترجمة': 'language',
                    'الكتابة الإبداعية': 'feather',
                    'الصحافة': 'newspaper',
                    'التدريس': 'chalkboard-teacher',
                    'التعليم': 'graduation-cap',
                    'الطفولة المبكرة': 'child',
                    'التربية الخاصة': 'hands-helping',
                    'علوم الحاسوب': 'laptop-code',
                    'الشبكات الاجتماعية': 'share-alt',
                    'تحليل البيانات': 'chart-pie',
                    'البيانات الضخمة': 'database',
                    'علم البيانات': 'chart-network',
                    'التعلم الآلي': 'brain',
                    'الروبوتات': 'robot',
                    'إنترنت الأشياء': 'wifi',
                    'الواقع الافتراضي': 'vr-cardboard',
                    'الواقع المعزز': 'glasses',
                    'تطوير الألعاب': 'gamepad',
                    'البلوكتشين': 'cubes',
                    'العملات الرقمية': 'bitcoin',
                    'الأمن المعلوماتي': 'lock',
                    'الخصوصية': 'user-shield',
                    'الحوسبة السحابية': 'cloud',
                    'DevOps': 'server',
                    'تطوير البرمجيات': 'code-branch',
                    'اختبار البرمجيات': 'bug',
                    'إدارة المشاريع': 'project-diagram',
                    'أجايل': 'sync',
                    'سكرم': 'users-cog',
                    'لينكس': 'linux',
                    'ويندوز': 'windows',
                    'ماك': 'apple',
                    'أندرويد': 'android',
                    'iOS': 'apple',
                    'الطباعة ثلاثية الأبعاد': 'cube',
                    'التصنيع': 'industry',
                    'الطاقة': 'bolt',
                    'الطاقة المتجددة': 'solar-panel',
                    'السيارات': 'car',
                    'الطيران': 'plane',
                    'الفضاء': 'rocket',
                    'البحرية': 'ship',
                    'النقل': 'truck',
                    'اللوجستيات': 'shipping-fast',
                    'العقارات': 'home',
                    'البناء': 'hammer',
                    'الديكور الداخلي': 'couch',
                    'الموضة': 'tshirt',
                    'الجمال': 'spa',
                    'المجوهرات': 'gem',
                    'الحرف اليدوية': 'cut',
                    'الخياطة': 'cut',
                    'الزراعة المنزلية': 'seedling',
                    'الحدائق': 'leaf',
                    'الحيوانات الأليفة': 'paw',
                    'الطب البيطري': 'stethoscope',
                    'الصيد': 'fish',
                    'التخييم': 'campground',
                    'المغامرات': 'mountain',
                    'اليوغا': 'om',
                    'التأمل': 'om',
                    'اللياقة البدنية': 'dumbbell',
                    'التغذية': 'apple-alt',
                    'فقدان الوزن': 'weight',
                    'الطب البديل': 'mortar-pestle',
                    'العلاج الطبيعي': 'hand-holding-medical',
                    'الإسعافات الأولية': 'first-aid',
                    'الصحة العقلية': 'brain',
                    'العلاقات': 'heart',
                    'تربية الأطفال': 'baby',
                    'الزواج': 'rings-wedding',
                    'التنمية الذاتية': 'user-graduate',
                    'السعادة': 'smile',
                    'الإيجابية': 'sun',
                    'الروحانية': 'pray',
                    'الدين': 'pray',
                    'الثقافة': 'landmark',
                    'الفنون': 'paint-brush',
                    'الموسيقى': 'music',
                    'الرقص': 'running',
                    'المسرح': 'theater-masks',
                    'السينما': 'film',
                    'التلفزيون': 'tv',
                    'الألعاب': 'gamepad',
                    'الرياضة': 'futbol',
                    'كرة القدم': 'futbol',
                    'كرة السلة': 'basketball-ball',
                    'التنس': 'table-tennis',
                    'الجولف': 'golf-ball',
                    'السباحة': 'swimmer',
                    'الجري': 'running',
                    'ركوب الدراجات': 'bicycle',
                    'اليوجا': 'om',
                    'الفنون القتالية': 'fist-raised',
                    'الملاكمة': 'fist-raised',
                    'الرماية': 'bullseye',
                    'الصيد': 'fish',
                    'صيد السمك': 'fish',
                    'التزلج': 'skiing',
                    'التزلج على الجليد': 'skating',
                    'التزلج على الماء': 'water',
                    'ركوب الأمواج': 'water',
                    'الغوص': 'diving-mask',
                    'التجديف': 'ship',
                    'الإبحار': 'ship',
                    'التخييم': 'campground',
                    'المشي لمسافات طويلة': 'hiking',
                    'تسلق الجبال': 'mountain',
                    'القفز بالمظلات': 'parachute-box',
                    'القفز المظلي': 'parachute-box',
                    'الطيران الشراعي': 'wind',
                    'ركوب المنطاد': 'cloud',
                    'السفر': 'plane',
                    'السياحة': 'plane',
                    'الفنادق': 'hotel',
                    'المطاعم': 'utensils',
                    'الطبخ': 'utensils',
                    'الخبز': 'bread-slice',
                    'الحلويات': 'cookie',
                    'المشروبات': 'coffee',
                    'النبيذ': 'wine-glass',
                    'البيرة': 'beer',
                    'الكوكتيلات': 'cocktail',
                    'القهوة': 'coffee',
                    'الشاي': 'mug-hot',
                    'العصائر': 'glass-whiskey',
                    'الطعام الصحي': 'carrot',
                    'النباتية': 'leaf',
                    'الطعام العضوي': 'seedling',
                    'الطعام المحلي': 'store',
                    'المطبخ الإيطالي': 'pizza-slice',
                    'المطبخ الفرنسي': 'cheese',
                    'المطبخ الصيني': 'utensils',
                    'المطبخ الياباني': 'fish',
                    'المطبخ المكسيكي': 'pepper-hot',
                    'المطبخ الهندي': 'pepper-hot',
                    'المطبخ التايلاندي': 'pepper-hot',
                    'المطبخ العربي': 'utensils',
                    'المطبخ المتوسطي': 'lemon',
                    'المطبخ الأمريكي': 'hamburger',
                    'الوجبات السريعة': 'hamburger',
                    'الوجبات الخفيفة': 'cookie',
                    'الوجبات الصحية': 'apple-alt',
                    'الوجبات النباتية': 'leaf',
                    'الوجبات الخالية من الغلوتين': 'bread-slice',
                    'الوجبات الخالية من اللاكتوز': 'cheese',
                    'الوجبات الخالية من السكر': 'cookie',
                    'الوجبات الخالية من الدهون': 'drumstick-bite',
                    'الوجبات الخالية من الملح': 'salt-shaker',
                    'الوجبات الخالية من البيض': 'egg',
                    'الوجبات الخالية من المكسرات': 'seedling',
                    'الوجبات الخالية من فول الصويا': 'seedling',
                    'الوجبات الخالية من القمح': 'wheat',
                    'الوجبات الخالية من الألبان': 'cheese',
                    'الوجبات الخالية من اللحوم': 'drumstick-bite',
                    'الوجبات الخالية من الأسماك': 'fish',
                    'الوجبات الخالية من المأكولات البحرية': 'fish',
                };

                // تحويل اسم الفئة إلى حروف صغيرة للمطابقة
                const lowerCaseName = categoryName.toLowerCase();

                // البحث عن الأيقونة المناسبة
                for (const [key, icon] of Object.entries(iconMap)) {
                    if (lowerCaseName.includes(key.toLowerCase())) {
                        return icon;
                    }
                }

                // أيقونة افتراضية إذا لم يتم العثور على مطابقة
                return 'folder';
            }

            // تحديث حقل الفئات المخفي
            function updateCategoriesInput() {
                const activeCategories = [];
                categoryFilters.forEach(filter => {
                    if (filter.classList.contains('active') && filter.dataset.value) {
                        activeCategories.push(filter.dataset.value);
                    }
                });
                categoriesInput.value = activeCategories.join(',');
                console.log('تم تحديث فلاتر الفئات:', categoriesInput.value);
            }

            // تحديث حقل المستوى المخفي
            function updateLevelInput() {
                let activeLevel = '';
                levelOptions.forEach(option => {
                    if (option.classList.contains('active')) {
                        activeLevel = option.dataset.value;
                    }
                });
                levelInput.value = activeLevel;
                console.log('تم تحديث فلتر المستوى:', levelInput.value);
            }

            // إضافة مستمعي الأحداث لفلاتر الفئات
            categoryFilters.forEach(filter => {
                filter.addEventListener('click', function(e) {
                    e.preventDefault();

                    // إذا كان هذا هو فلتر "جميع الفئات"
                    if (!this.dataset.value) {
                        categoryFilters.forEach(f => f.classList.remove('active'));
                        this.classList.add('active');
                    } else {
                        // إزالة النشاط من فلتر "جميع الفئات"
                        categoryFilters[0].classList.remove('active');

                        // تبديل حالة النشاط لهذا الفلتر
                        this.classList.toggle('active');

                        // إذا لم يكن هناك فلاتر نشطة، قم بتنشيط "جميع الفئات"
                        const hasActive = Array.from(categoryFilters).some(f => f !== categoryFilters[0] && f.classList.contains('active'));
                        if (!hasActive) {
                            categoryFilters[0].classList.add('active');
                        }
                    }

                    updateCategoriesInput();

                    // تقديم النموذج تلقائيًا عند تغيير الفئة
                    // filterForm.submit();
                });
            });

            // إضافة مستمعي الأحداث لخيارات المستوى
            levelOptions.forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();

                    // إزالة النشاط من جميع الخيارات
                    levelOptions.forEach(o => o.classList.remove('active'));

                    // تنشيط هذا الخيار
                    this.classList.add('active');

                    updateLevelInput();

                    // تقديم النموذج تلقائيًا عند تغيير المستوى
                    // filterForm.submit();
                });
            });

            // إضافة مستمعي الأحداث لأزرار إزالة الفلاتر
            removeFilters.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filterType = this.dataset.type;
                    const filterValue = this.dataset.value;

                    console.log('إزالة الفلتر:', filterType, filterValue);

                    switch (filterType) {
                        case 'category':
                            // إزالة الفئة من الفلاتر النشطة
                            categoryFilters.forEach(filter => {
                                if (filter.dataset.value === filterValue) {
                                    filter.classList.remove('active');
                                }
                            });
                            updateCategoriesInput();
                            break;
                        case 'level':
                            // إعادة ضبط فلتر المستوى
                            levelOptions.forEach(option => option.classList.remove('active'));
                            levelInput.value = '';
                            break;
                        case 'price_range':
                            // إعادة ضبط فلتر السعر
                            document.getElementById('priceFilter').value = '';
                            break;
                        case 'sort':
                            // إعادة ضبط فلتر الترتيب
                            document.getElementById('sortFilter').value = 'newest';
                            break;
                    }

                    // تقديم النموذج لتطبيق التغييرات
                    console.log('تقديم النموذج بعد إزالة الفلتر');
                    filterForm.submit();
                });
            });

            // إضافة مستمعي الأحداث للقوائم المنسدلة
            document.getElementById('sortFilter').addEventListener('change', function() {
                console.log('تم تغيير الترتيب إلى:', this.value);
                // filterForm.submit();
            });

            document.getElementById('priceFilter').addEventListener('change', function() {
                console.log('تم تغيير نطاق السعر إلى:', this.value);
                // filterForm.submit();
            });

            // تطبيق أيقونات الفئات
            document.querySelectorAll('.category-filter').forEach(filter => {
                const categoryName = filter.textContent.trim();
                if (categoryName !== 'جميع الفئات') {
                    const iconElement = filter.querySelector('i');
                    if (iconElement) {
                        const iconClass = getCategoryIcon(categoryName);
                        iconElement.className = `fas fa-${iconClass}`;
                    }
                }
            });
        });
    </script>
    @endpush
@endsection