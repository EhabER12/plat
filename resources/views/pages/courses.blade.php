@extends('layouts.app')

@section('title', app()->getLocale() == 'ar' ? 'استكشف الدورات - منصة تعليمية' : 'Explore Courses - منصة تعليمية')

@section('styles')
<style>
    /* Courses Page Styles */
    .courses-header {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        padding: 80px 0 50px;
        color: white;
        margin-bottom: 50px;
        position: relative;
        overflow: hidden;
    }

    .courses-header::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
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
        left: -80px;
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
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .courses-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        max-width: 600px;
        margin-bottom: 30px;
    }

    .filter-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        margin-top: -25px;
        position: relative;
        z-index: 10;
        background-color: white;
    }

    .filter-card .card-body {
        padding: 25px;
    }

    .form-label {
        font-weight: 600;
        color: #444;
        font-size: 0.9rem;
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 12px 15px;
        border-color: #e0e0e0;
        font-size: 0.95rem;
        box-shadow: none;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
    }

    .filter-btn {
        border-radius: 10px;
        padding: 12px 20px;
        font-weight: 600;
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        border: none;
        transition: all 0.3s ease;
    }

    .filter-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }

    .course-card {
        border-radius: 15px;
        border: none;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .course-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .course-image {
        height: 200px;
        position: relative;
        overflow: hidden;
    }

    .course-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .course-card:hover .course-image img {
        transform: scale(1.1);
    }

    .course-category {
        position: absolute;
        bottom: 15px;
        left: 15px;
        background-color: rgba(255, 255, 255, 0.9);
        color: #4361ee;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        z-index: 2;
    }

    .course-price {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-weight: 700;
        z-index: 2;
    }

    .course-content {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .course-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 10px;
        color: #333;
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
        color: #666;
        font-size: 0.9rem;
    }

    .course-instructor i {
        margin-right: 5px;
        color: #4361ee;
    }

    .course-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 0.85rem;
        color: #666;
    }

    .course-stat {
        display: flex;
        align-items: center;
    }

    .course-stat i {
        margin-right: 5px;
        color: #4361ee;
    }

    .course-rating {
        color: #ffc107;
    }

    .course-description {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 20px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.6;
        flex-grow: 1;
    }

    .course-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background-color: #f8f9fa;
        border-top: 1px solid #f0f0f0;
    }

    .course-btn {
        border-radius: 10px;
        padding: 8px 20px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .course-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.2);
    }

    .pagination {
        margin-top: 50px;
    }

    .page-link {
        border-radius: 10px;
        margin: 0 5px;
        color: #4361ee;
        border-color: #e0e0e0;
        padding: 10px 15px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background-color: #4361ee;
        color: white;
        border-color: #4361ee;
    }

    .page-item.active .page-link {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .no-courses {
        text-align: center;
        padding: 50px 0;
    }

    .no-courses i {
        font-size: 4rem;
        color: #e0e0e0;
        margin-bottom: 20px;
    }

    .no-courses h3 {
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }

    .no-courses p {
        color: #666;
        max-width: 500px;
        margin: 0 auto 20px;
    }
</style>
@endsection

@section('content')
    <!-- Courses Header -->
    <div class="courses-header">
        <div class="container">
            <div class="courses-header-content text-center">
                <h1 class="courses-title">{{ app()->getLocale() == 'ar' ? 'استكشف دوراتنا التعليمية' : 'Explore Our Courses' }}</h1>
                <p class="courses-subtitle mx-auto">{{ app()->getLocale() == 'ar' ? 'اكتشف مجموعة واسعة من الدورات عالية الجودة التي تقدمها نخبة من المدربين المتميزين في مختلف المجالات' : 'Discover a wide range of high-quality courses offered by our expert instructors across various fields' }}</p>
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="alert alert-warning">
            <p>Note: There was an issue connecting to the database. This is a demo view.</p>
            <small>Error: {{ $error }}</small>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="container">
        <div class="card filter-card mb-5">
            <div class="card-body">
                <form action="{{ url('/courses') }}" method="GET" class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label for="category" class="form-label">{{ app()->getLocale() == 'ar' ? 'الفئة' : 'Category' }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-folder text-muted"></i></span>
                            <select class="form-select border-start-0" id="category" name="category">
                                <option value="">{{ app()->getLocale() == 'ar' ? 'جميع الفئات' : 'All Categories' }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id ?? $category->category_id ?? '' }}" {{ isset($currentCategory) && $currentCategory == ($category->id ?? $category->category_id ?? '') ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label for="search" class="form-label">{{ app()->getLocale() == 'ar' ? 'البحث' : 'Search' }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" id="search" name="search" value="{{ $currentSearch ?? '' }}" placeholder="{{ app()->getLocale() == 'ar' ? 'ابحث عن الدورات...' : 'Search courses...' }}">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label for="sort" class="form-label">{{ app()->getLocale() == 'ar' ? 'ترتيب حسب' : 'Sort By' }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-sort text-muted"></i></span>
                            <select class="form-select border-start-0" id="sort" name="sort">
                                <option value="newest" {{ isset($currentSort) && $currentSort == 'newest' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'الأحدث' : 'Newest' }}</option>
                                <option value="oldest" {{ isset($currentSort) && $currentSort == 'oldest' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'الأقدم' : 'Oldest' }}</option>
                                <option value="price-low" {{ isset($currentSort) && $currentSort == 'price-low' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'السعر (من الأقل إلى الأعلى)' : 'Price (Low to High)' }}</option>
                                <option value="price-high" {{ isset($currentSort) && $currentSort == 'price-high' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'السعر (من الأعلى إلى الأقل)' : 'Price (High to Low)' }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn filter-btn w-100">
                            <i class="fas fa-filter me-2"></i> {{ app()->getLocale() == 'ar' ? 'تصفية' : 'Filter' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="container">
        @if(isset($error))
            <div class="alert alert-warning">
                {{ $error }}
            </div>
        @elseif(count($courses) > 0)
            <div class="row g-4">
                @foreach($courses as $course)
                    <div class="col-lg-4 col-md-6">
                        <div class="course-card">
                            <div class="course-image">
                                @if(isset($course->thumbnail) && !empty($course->thumbnail))
                                    <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}">
                                @else
                                    <img src="https://img.freepik.com/free-photo/students-using-laptop-together_23-2149038413.jpg" alt="{{ $course->title }}">
                                @endif
                                <div class="course-category">{{ $course->category_name ?? 'Uncategorized' }}</div>
                                <div class="course-price">${{ $course->price }}</div>
                            </div>
                            <div class="course-content">
                                <h3 class="course-title">{{ $course->title }}</h3>
                                <p class="course-instructor">
                                    <i class="fas fa-user-tie"></i> {{ $course->instructor_name ?? 'Unknown' }}
                                </p>
                                <div class="course-stats">
                                    <div class="course-stat">
                                        <i class="fas fa-users"></i>
                                        <span>
                                            {{ $course->students_count ?? $course->enrollments_count ?? '0' }}
                                            {{ app()->getLocale() == 'ar' ? 'طالب' : 'students' }}
                                        </span>
                                    </div>
                                    <div class="course-stat">
                                        <i class="fas fa-clock"></i>
                                        <span>
                                            @php
                                                $duration = $course->duration ?? 0;
                                                if (!$duration && isset($course->videos) && $course->videos->count() > 0) {
                                                    $duration = ceil($course->videos->sum('duration') / 60);
                                                }
                                            @endphp
                                            {{ $duration }}
                                            {{ app()->getLocale() == 'ar' ? 'ساعة' : 'hours' }}
                                        </span>
                                    </div>
                                    <div class="course-stat">
                                        <i class="fas fa-signal"></i>
                                        <span>
                                            @if(isset($course->level))
                                                @if($course->level == 'beginner')
                                                    {{ app()->getLocale() == 'ar' ? 'مبتدئ' : 'Beginner' }}
                                                @elseif($course->level == 'intermediate')
                                                    {{ app()->getLocale() == 'ar' ? 'متوسط' : 'Intermediate' }}
                                                @elseif($course->level == 'advanced')
                                                    {{ app()->getLocale() == 'ar' ? 'متقدم' : 'Advanced' }}
                                                @else
                                                    {{ $course->level }}
                                                @endif
                                            @else
                                                {{ app()->getLocale() == 'ar' ? 'جميع المستويات' : 'All Levels' }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <p class="course-description">{{ \Illuminate\Support\Str::limit($course->description, 150) }}</p>

                                @if(isset($course->ratings) || isset($course->reviews))
                                    @php
                                        $ratings = $course->ratings ?? $course->reviews ?? collect([]);
                                        $avgRating = $ratings->avg('rating') ?? $ratings->avg('rating_value') ?? 0;
                                        $avgRating = number_format($avgRating, 1);
                                    @endphp
                                    <div class="course-rating mt-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($avgRating))
                                                <i class="fas fa-star"></i>
                                            @elseif($i - 0.5 <= $avgRating)
                                                <i class="fas fa-star-half-alt"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                        <span class="ms-2">{{ $avgRating }} ({{ $ratings->count() }})</span>
                                    </div>
                                @endif
                            </div>
                            <div class="course-footer">
                                <span class="text-muted">{{ app()->getLocale() == 'ar' ? 'تم التحديث' : 'Updated' }} {{ \Carbon\Carbon::parse($course->updated_at ?? now())->diffForHumans() }}</span>
                                <a href="{{ url('/courses/' . ($course->course_id ?? $course->id ?? '')) }}" class="btn btn-outline-primary course-btn">
                                    {{ app()->getLocale() == 'ar' ? 'عرض الدورة' : 'View Course' }} <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-courses">
                <i class="fas fa-search"></i>
                <h3>{{ app()->getLocale() == 'ar' ? 'لم يتم العثور على دورات' : 'No Courses Found' }}</h3>
                <p>{{ app()->getLocale() == 'ar' ? 'لا توجد دورات تطابق معايير البحث الخاصة بك. حاول تعديل عوامل التصفية الخاصة بك.' : 'No courses match your criteria. Try adjusting your filters.' }}</p>
                <a href="{{ url('/courses') }}" class="btn btn-primary">
                    <i class="fas fa-sync-alt me-2"></i> {{ app()->getLocale() == 'ar' ? 'إعادة ضبط المرشحات' : 'Reset Filters' }}
                </a>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if(isset($courses) && $courses instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="container">
            <div class="d-flex justify-content-center pagination">
                {{ $courses->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
@endsection