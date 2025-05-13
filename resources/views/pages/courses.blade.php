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

    /* Filter Sidebar Styles */
    .filter-sidebar {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        background-color: white;
        position: sticky;
        top: 100px;
    }

    .filter-sidebar .card-header {
        background: #f8f9fa;
        border-radius: 15px 15px 0 0;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }

    .filter-sidebar .card-body {
        padding: 20px;
    }

    .filter-heading {
        font-weight: 600;
        color: #333;
        margin-bottom: 12px;
        font-size: 1rem;
        position: relative;
        padding-bottom: 8px;
    }

    .filter-heading:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background: #4361ee;
        border-radius: 2px;
    }

    .filter-group {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .filter-group:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .filter-options {
        max-height: 200px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .filter-options::-webkit-scrollbar {
        width: 5px;
    }

    .filter-options::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .filter-options::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }

    .filter-options::-webkit-scrollbar-thumb:hover {
        background: #ccc;
    }

    .form-check {
        margin-bottom: 10px;
        padding-left: 30px;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 0.2rem;
        cursor: pointer;
        border-color: #ccc;
    }

    .form-check-input:checked {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .form-check-label {
        font-size: 0.95rem;
        cursor: pointer;
        color: #555;
        transition: all 0.2s;
    }

    .form-check-input:checked + .form-check-label {
        color: #222;
        font-weight: 500;
    }

    .apply-filters-btn {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .apply-filters-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
    }

    .apply-filters-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Active Filter Pills */
    .active-filters {
        margin: 0 0 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .filter-pill {
        background: #e7eaf4;
        color: #4361ee;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
    }

    .filter-pill .remove-filter {
        cursor: pointer;
        color: #4361ee;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: rgba(67, 97, 238, 0.1);
        margin-left: 5px;
    }

    .filter-pill:hover {
        background: #d1d7f0;
        transform: translateY(-2px);
    }

    .filter-pill .remove-filter:hover {
        background: rgba(67, 97, 238, 0.2);
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

    /* Course Cards */
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

    /* Pagination */
    .pagination {
        margin-top: 50px;
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .page-item:first-child .page-link,
    .page-item:last-child .page-link {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page-link {
        min-width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4361ee;
        border-color: #e0e0e0;
        font-weight: 600;
        border-radius: 8px !important;
        margin: 0;
        padding: 0.5rem 0.75rem;
        transition: all 0.2s ease;
    }

    .page-link:hover {
        background-color: #4361ee;
        color: white;
        border-color: #4361ee;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(67, 97, 238, 0.2);
    }

    .page-item.active .page-link {
        background-color: #4361ee;
        border-color: #4361ee;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(67, 97, 238, 0.2);
    }

    .page-item.disabled .page-link {
        color: #aaa;
        background-color: #f8f9fa;
        border-color: #e0e0e0;
        pointer-events: none;
    }

    /* No Courses */
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

    <!-- Courses Header -->
    <div class="courses-header">
        <div class="container">
            <div class="courses-header-content">
                <h1 class="courses-title">{{ __('Explore Courses') }}</h1>
                <p class="courses-subtitle">{{ __('Discover a wide range of courses taught by expert instructors') }}</p>
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
        <div class="row">
            <div class="col-md-3">
                <div class="filter-sidebar card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Filter Courses') }}</h5>
                    </div>
                    <div class="card-body">
                        <!-- Use standard form submission as a fallback -->
                        <form id="filterForm" action="{{ route('courses.index') }}" method="GET">
                            <!-- Category Filter -->
                            <div class="filter-group mb-4">
                                <h6 class="filter-heading">{{ __('Categories') }}</h6>
                                <div class="filter-options">
                                    @foreach($categories as $category)
                                    <div class="form-check">
                                        <input class="form-check-input filter-checkbox" type="checkbox" name="categories[]" 
                                            id="category_{{ $category->id }}" value="{{ $category->id }}"
                                            {{ (is_array(request()->get('categories')) && in_array($category->id, request()->get('categories'))) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Sort Options -->
                            <div class="filter-group mb-4">
                                <h6 class="filter-heading">{{ __('Sort By') }}</h6>
                                <div class="form-group">
                                    <select name="sort" class="form-select" id="sortFilter">
                                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('Newest') }}</option>
                                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>{{ __('Most Popular') }}</option>
                                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>{{ __('Highest Rated') }}</option>
                                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Submit Buttons - Using standard form submission -->
                            <div class="d-grid mb-2">
                                <button type="submit" class="btn btn-primary apply-filters-btn" id="applyFilters">
                                    <i class="fas fa-filter me-2"></i>{{ __('Apply Filters') }}
                                </button>
                            </div>
                            
                            <div class="d-grid">
                                <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync-alt me-2"></i>{{ __('Reset') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <!-- Active Filters -->
                <div class="active-filters mb-4" id="activeFilters">
                    @if(request()->has('categories') || request()->has('sort'))
                        @if(request()->has('categories'))
                            @foreach(request('categories') as $categoryId)
                                @php
                                    $category = $categories->firstWhere('id', $categoryId);
                                @endphp
                                @if($category)
                                <div class="filter-pill">
                                    {{ $category->name }}
                                    <a href="{{ request()->fullUrlWithQuery(['categories' => array_diff(request('categories'), [$categoryId])]) }}" class="remove-filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                                @endif
                            @endforeach
                        @endif
                        
                        @if(request('sort') && request('sort') != 'newest')
                            <div class="filter-pill">
                                @php
                                    $sortText = [
                                        'popular' => __('Most Popular'),
                                        'rating' => __('Highest Rated'),
                                        'price_low' => __('Price: Low to High'),
                                        'price_high' => __('Price: High to Low')
                                    ][request('sort')] ?? request('sort');
                                @endphp
                                {{ $sortText }}
                                <a href="{{ request()->fullUrlWithQuery(['sort' => null]) }}" class="remove-filter">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @endif
                        
                        <div class="ms-2">
                            <a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>{{ __('Clear All') }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Courses Container -->
                <div id="coursesContentContainer">
                    @if(isset($error))
                        <div class="alert alert-warning">
                            {{ $error }}
                        </div>
                    @elseif(count($courses) > 0)
                        <div class="row g-4" id="coursesList">
                            @foreach($courses as $course)
                                <div class="col-lg-6 col-md-12">
                                    <div class="course-card">
                                        <div class="course-image">
                                            @if(isset($course->thumbnail) && !empty($course->thumbnail))
                                                <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}">
                                            @else
                                                <img src="https://img.freepik.com/free-photo/students-using-laptop-together_23-2149038413.jpg" alt="{{ $course->title }}">
                                            @endif
                                            <div class="course-category">{{ $course->category->name ?? 'Uncategorized' }}</div>
                                            <div class="course-price">${{ $course->price }}</div>
                                        </div>
                                        <div class="course-content">
                                            <h3 class="course-title">{{ $course->title }}</h3>
                                            <p class="course-instructor">
                                                <i class="fas fa-user-tie"></i> {{ $course->instructor->name ?? 'Unknown' }}
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
                                            </div>
                                            <p class="course-description">{{ \Illuminate\Support\Str::limit($course->description, 150) }}</p>
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

                        <!-- Pagination -->
                        @if($courses instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="d-flex justify-content-center pagination" id="pagination">
                                {{ $courses->appends(request()->except('page'))->links() }}
                            </div>
                        @endif
                    @else
                        <div class="no-courses">
                            <i class="fas fa-search"></i>
                            <h3>{{ app()->getLocale() == 'ar' ? 'لم يتم العثور على دورات' : 'No Courses Found' }}</h3>
                            <p>{{ app()->getLocale() == 'ar' ? 'لا توجد دورات تطابق معايير البحث الخاصة بك. حاول تعديل عوامل التصفية الخاصة بك.' : 'No courses match your criteria. Try adjusting your filters.' }}</p>
                            <button type="button" class="btn btn-primary" id="resetAllFilters">
                                <i class="fas fa-sync-alt me-2"></i>{{ app()->getLocale() == 'ar' ? 'إعادة ضبط المرشحات' : 'Reset Filters' }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Any custom scripts can go here if needed in the future
    </script>
    @endpush
@endsection