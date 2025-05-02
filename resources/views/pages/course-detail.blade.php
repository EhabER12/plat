@extends('layouts.app')

@section('title', $course->title . ' - منصة تعليمية')

@section('styles')
<style>
    /* Course Detail Styles */
    .course-header {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        padding: 100px 0 50px;
        color: white;
        margin-bottom: 50px;
        position: relative;
        overflow: hidden;
    }

    .course-header::before {
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

    .course-header::after {
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

    .course-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .course-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
    }

    .course-price-tag {
        position: absolute;
        top: 20px;
        right: 20px;
        background: white;
        color: #20b7b7;
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 18px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .course-category {
        display: inline-block;
        background: rgba(255, 255, 255, 0.2);
        padding: 5px 15px;
        border-radius: 20px;
        margin-bottom: 15px;
    }

    .course-title {
        font-size: 36px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .course-stats {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .stat-item {
        display: flex;
        align-items: center;
    }

    .stat-item i {
        margin-right: 8px;
    }

    .course-action-container {
        background: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .course-features {
        margin-bottom: 20px;
    }

    .feature-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .feature-item i {
        color: #20b7b7;
        margin-right: 10px;
        font-size: 18px;
    }

    .enroll-btn {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 30px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
    }

    .enroll-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
    }

    .section-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 15px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: #4361ee;
    }

    .course-description {
        margin-bottom: 50px;
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(67, 97, 238, 0.1);
        color: #4361ee;
    }

    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }

    .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%234361ee'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }

    .curriculum-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .curriculum-item:last-child {
        border-bottom: none;
    }

    .curriculum-item i {
        margin-right: 10px;
        color: #4361ee;
    }

    .instructor-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 30px;
        margin-bottom: 50px;
    }

    .instructor-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 20px;
    }

    .instructor-info h4 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .instructor-info p {
        margin-bottom: 15px;
    }

    .rating-bar {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .rating-label {
        min-width: 30px;
        margin-right: 10px;
    }

    .rating-progress {
        flex-grow: 1;
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        margin-right: 10px;
        overflow: hidden;
    }

    .rating-progress-bar {
        height: 100%;
        background: #ffc107;
    }

    .rating-count {
        min-width: 30px;
        text-align: right;
    }

    .star-rating {
        color: #ffc107;
    }

    .review-card {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .reviewer-name {
        font-weight: 600;
    }

    .review-date {
        color: #6c757d;
        font-size: 14px;
    }

    .related-course-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
    }

    .related-course-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .related-course-image {
        height: 180px;
        object-fit: cover;
    }

    .related-course-content {
        padding: 20px;
    }

    .related-course-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .related-course-instructor {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 15px;
    }

    .related-course-price {
        font-weight: 700;
        color: #20b7b7;
    }
</style>
@endsection

@section('content')
    <!-- Course Header -->
    <div class="course-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7" style="position: relative; z-index: 2;">
                    <div class="course-category">{{ $course->category->name ?? 'Uncategorized' }}</div>
                    <h1 class="course-title">{{ $course->title }}</h1>
                    <div class="course-stats">
                        <div class="stat-item">
                            <i class="fas fa-star"></i>
                            <span>{{ number_format($averageRating, 1) }} ({{ $totalRatings }} {{ app()->getLocale() == 'ar' ? 'تقييم' : 'reviews' }})</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-users"></i>
                            <span>{{ $course->students->count() ?? 0 }} {{ app()->getLocale() == 'ar' ? 'طالب' : 'students' }}</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock"></i>
                            <span>{{ $course->videos->count() ?? 0 }} {{ app()->getLocale() == 'ar' ? 'درس' : 'lessons' }}</span>
                        </div>
                    </div>
                    <p>{{ app()->getLocale() == 'ar' ? 'أنشئت بواسطة' : 'Created by' }} <strong>{{ $course->instructor->name }}</strong></p>
                </div>
                <div class="col-lg-5" style="position: relative; z-index: 2;">
                    <div class="course-image-container">
                        @if(isset($course->thumbnail) && !empty($course->thumbnail))
                            <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}" class="course-image">
                        @else
                            <img src="https://img.freepik.com/free-photo/education-day-arrangement-table-with-copy-space_23-2149068021.jpg" alt="{{ $course->title }}" class="course-image">
                        @endif
                        <div class="course-price-tag">${{ $course->price }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Course Description -->
                <div class="course-description">
                    <h2 class="section-title">{{ app()->getLocale() == 'ar' ? 'نبذة عن هذه الدورة' : 'About This Course' }}</h2>
                    <p>{{ $course->description }}</p>
                </div>

                <!-- Course Curriculum -->
                <div class="course-curriculum mb-5">
                    <h2 class="section-title">{{ app()->getLocale() == 'ar' ? 'منهج الدورة' : 'Course Curriculum' }}</h2>

                    <div class="accordion" id="curriculumAccordion">
                        @if(isset($course->videos) && $course->videos->count() > 0)
                            @php
                                // Check if videos have sections
                                $hasSection = $course->videos->first() && isset($course->videos->first()->section);

                                if ($hasSection) {
                                    // Group videos by their sections
                                    $sections = $course->videos->groupBy('section');
                                } else {
                                    // Create default sections based on video count
                                    $totalVideos = $course->videos->count();
                                    $sectionsCount = min(3, ceil($totalVideos / 3));
                                    $videosPerSection = ceil($totalVideos / $sectionsCount);

                                    $sections = collect();
                                    $sectionTitles = [
                                        app()->getLocale() == 'ar' ? 'مقدمة' : 'Introduction',
                                        app()->getLocale() == 'ar' ? 'البداية' : 'Getting Started',
                                        app()->getLocale() == 'ar' ? 'موضوعات متقدمة' : 'Advanced Topics'
                                    ];

                                    for ($i = 0; $i < $sectionsCount; $i++) {
                                        $start = $i * $videosPerSection;
                                        $sectionVideos = $course->videos->slice($start, $videosPerSection);
                                        if ($sectionVideos->count() > 0) {
                                            $sections->put($sectionTitles[$i], $sectionVideos);
                                        }
                                    }
                                }
                            @endphp

                            @foreach($sections as $sectionTitle => $sectionVideos)
                                @if(count($sectionVideos) > 0)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ Str::slug($sectionTitle) }}">
                                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::slug($sectionTitle) }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ Str::slug($sectionTitle) }}">
                                                {{ $sectionTitle }} <span class="ms-auto">{{ count($sectionVideos) }} {{ app()->getLocale() == 'ar' ? 'محاضرة' : 'lectures' }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ Str::slug($sectionTitle) }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ Str::slug($sectionTitle) }}">
                                            <div class="accordion-body">
                                                @foreach($sectionVideos as $video)
                                                    <div class="curriculum-item">
                                                        <i class="fas fa-play-circle"></i>
                                                        <span>{{ $video->title }}</span>
                                                        <span class="ms-auto">
                                                            @if(isset($video->duration))
                                                                {{ gmdate("i:s", $video->duration) }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                {{ app()->getLocale() == 'ar' ? 'لا توجد فيديوهات متاحة لهذه الدورة حاليًا.' : 'No videos available for this course yet.' }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Instructor Info -->
                <div class="instructor-info mb-5">
                    <h2 class="section-title">{{ app()->getLocale() == 'ar' ? 'المدرب' : 'Instructor' }}</h2>

                    <div class="instructor-card">
                        <div class="d-flex">
                            @if(isset($course->instructor->profile_image) && !empty($course->instructor->profile_image))
                                <img src="{{ asset($course->instructor->profile_image) }}" alt="{{ $course->instructor->name }}" class="instructor-image">
                            @else
                                <img src="https://img.freepik.com/free-photo/confident-teacher-with-students-background_23-2148201042.jpg" alt="{{ $course->instructor->name }}" class="instructor-image">
                            @endif
                            <div>
                                <h4>{{ $course->instructor->name }}</h4>
                                <p class="text-muted">{{ $course->category->name ?? 'Instructor' }} {{ app()->getLocale() == 'ar' ? 'خبير' : 'Expert' }}</p>

                                @php
                                    $instructorRating = 0;
                                    $instructorStudentsCount = 0;
                                    $instructorCoursesCount = 0;

                                    if(isset($course->instructor->ratings)) {
                                        $instructorRating = $course->instructor->ratings->avg('rating_value') ?? 0;
                                    }

                                    if(isset($course->instructor->courses)) {
                                        $instructorCoursesCount = $course->instructor->courses->count();
                                        $instructorStudentsCount = $course->instructor->courses->sum(function($course) {
                                            return $course->students->count() ?? 0;
                                        });
                                    }

                                    $instructorRating = number_format($instructorRating, 1);
                                @endphp

                                <div class="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($instructorRating))
                                            <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $instructorRating)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-2">{{ $instructorRating }} {{ app()->getLocale() == 'ar' ? 'تقييم المدرب' : 'Instructor Rating' }}</span>
                                </div>
                                <div class="mt-2">
                                    <span><i class="fas fa-user-graduate me-2"></i>{{ $instructorStudentsCount }} {{ app()->getLocale() == 'ar' ? 'طالب' : 'Students' }}</span>
                                    <span class="ms-3"><i class="fas fa-book me-2"></i>{{ $instructorCoursesCount }} {{ app()->getLocale() == 'ar' ? 'دورة' : 'Courses' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            @if(isset($course->instructor->bio) && !empty($course->instructor->bio))
                                <p>{{ $course->instructor->bio }}</p>
                            @else
                                <p>{{ app()->getLocale() == 'ar' ? 'مدرب متخصص في مجال ' . ($course->category->name ?? '') . ' مع خبرة واسعة في التدريس والتطوير.' : 'A specialized instructor in the field of ' . ($course->category->name ?? '') . ' with extensive experience in teaching and development.' }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Student Reviews -->
                <div class="reviews mb-5">
                    <h2 class="section-title">{{ app()->getLocale() == 'ar' ? 'تقييمات الطلاب' : 'Student Reviews' }}</h2>

                    <!-- Rating Summary -->
                    <div class="row mb-4">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            @php
                                $courseRatings = $course->ratings ?? $course->reviews ?? collect([]);
                                $avgRating = 0;
                                $totalRatings = 0;
                                $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

                                if ($courseRatings->count() > 0) {
                                    $totalRatings = $courseRatings->count();
                                    $avgRating = $courseRatings->avg('rating') ?? $courseRatings->avg('rating_value') ?? 0;

                                    // Count ratings by value
                                    foreach ($courseRatings as $rating) {
                                        $ratingValue = isset($rating->rating) ? $rating->rating : (isset($rating->rating_value) ? $rating->rating_value : 0);
                                        $ratingValue = min(5, max(1, round($ratingValue))); // Ensure it's between 1-5
                                        $ratingCounts[$ratingValue]++;
                                    }
                                }

                                $avgRating = number_format($avgRating, 1);
                                $fullStars = floor($avgRating);
                                $halfStar = ($avgRating - $fullStars) >= 0.5;
                            @endphp

                            <h1 class="display-4 fw-bold text-primary">{{ $avgRating }}</h1>
                            <div class="star-rating mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $fullStars)
                                        <i class="fas fa-star"></i>
                                    @elseif($i == $fullStars + 1 && $halfStar)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <p>{{ $totalRatings }} {{ app()->getLocale() == 'ar' ? 'تقييم' : 'ratings' }}</p>
                        </div>
                        <div class="col-md-8">
                            @foreach(range(5, 1) as $rating)
                                <div class="rating-bar">
                                    <div class="rating-label">{{ $rating }}</div>
                                    <div class="rating-progress">
                                        @php
                                            $percentage = $totalRatings > 0 ? ($ratingCounts[$rating] / $totalRatings) * 100 : 0;
                                        @endphp
                                        <div class="rating-progress-bar" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <div class="rating-count">{{ $ratingCounts[$rating] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Review List -->
                    <div class="reviews-list">
                        @php
                            // Use reviews from controller if available, otherwise fallback to course relationship
                            $courseRatings = isset($reviews) && $reviews->count() > 0 ? $reviews : ($course->ratings ?? $course->reviews ?? collect([]));
                        @endphp

                        @if($courseRatings->count() > 0)
                            @foreach($courseRatings->take(5) as $rating)
                                <div class="review-card">
                                    <div class="review-header">
                                        <span class="reviewer-name">
                                            @if(isset($rating->student) && isset($rating->student->name))
                                                {{ $rating->student->name }}
                                            @elseif(isset($rating->user) && isset($rating->user->name))
                                                {{ $rating->user->name }}
                                            @else
                                                {{ app()->getLocale() == 'ar' ? 'طالب' : 'Student' }}
                                            @endif
                                        </span>
                                        <span class="review-date">{{ isset($rating->created_at) ? $rating->created_at->format('M d, Y') : 'Unknown date' }}</span>
                                    </div>
                                    <div class="star-rating mb-2">
                                        @php
                                            $ratingValue = isset($rating->rating) ? $rating->rating : (isset($rating->rating_value) ? $rating->rating_value : 0);
                                            $ratingValue = min(5, max(1, round($ratingValue))); // Ensure it's between 1-5
                                        @endphp

                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $ratingValue)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <p>{{ $rating->comment ?? $rating->review_text ?? $rating->review ?? (app()->getLocale() == 'ar' ? 'دورة رائعة! مفيدة جدا ومنظمة بشكل جيد.' : 'Great course! Very informative and well-structured.') }}</p>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                {{ app()->getLocale() == 'ar' ? 'لا توجد مراجعات حتى الآن. كن أول من يراجع هذه الدورة!' : 'No reviews yet. Be the first to review this course!' }}
                            </div>
                        @endif

                        <!-- Add Review Form -->
                        @auth
                            @php
                                $isEnrolled = App\Models\Enrollment::where('student_id', auth()->user()->user_id)
                                    ->where('course_id', $course->course_id)
                                    ->exists();

                                $hasReviewed = false;
                                if ($courseReviewsTableExists ?? false) {
                                    $hasReviewed = App\Models\CourseReview::where('user_id', auth()->user()->user_id)
                                        ->where('course_id', $course->course_id)
                                        ->exists();
                                } elseif ($ratingsTableExists ?? false) {
                                    $hasReviewed = App\Models\Rating::where(function($query) {
                                            $query->where('user_id', auth()->user()->user_id)
                                                ->orWhere('student_id', auth()->user()->user_id);
                                        })
                                        ->where('course_id', $course->course_id)
                                        ->exists();
                                }
                            @endphp

                            @if($isEnrolled)
                                <div class="add-review-form mt-5">
                                    <h4>{{ app()->getLocale() == 'ar' ? 'أضف تقييمك' : 'Add Your Review' }}</h4>
                                    <form action="{{ route('student.review', $course->course_id) }}" method="POST" class="mt-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="rating" class="form-label">{{ app()->getLocale() == 'ar' ? 'التقييم' : 'Rating' }}</label>
                                            <div class="rating-input">
                                                <div class="star-rating-input">
                                                    @for($i = 5; $i >= 1; $i--)
                                                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ $i == 5 ? 'checked' : '' }} />
                                                        <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="comment" class="form-label">{{ app()->getLocale() == 'ar' ? 'تعليقك' : 'Your Review' }}</label>
                                            <textarea class="form-control" id="comment" name="comment" rows="4" required minlength="10"></textarea>
                                            <div class="form-text">{{ app()->getLocale() == 'ar' ? 'شارك تجربتك مع هذه الدورة. الحد الأدنى 10 أحرف.' : 'Share your experience with this course. Minimum 10 characters.' }}</div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">{{ app()->getLocale() == 'ar' ? 'إرسال التقييم' : 'Submit Review' }}</button>
                                    </form>
                                </div>

                                <style>
                                    .rating-input {
                                        display: flex;
                                        align-items: center;
                                    }
                                    .star-rating-input {
                                        display: flex;
                                        flex-direction: row-reverse;
                                        justify-content: flex-end;
                                    }
                                    .star-rating-input input {
                                        display: none;
                                    }
                                    .star-rating-input label {
                                        cursor: pointer;
                                        font-size: 25px;
                                        color: #ddd;
                                        margin-right: 5px;
                                    }
                                    .star-rating-input label:hover,
                                    .star-rating-input label:hover ~ label,
                                    .star-rating-input input:checked ~ label {
                                        color: #ffc107;
                                    }
                                </style>
                            @elseif(!$hasReviewed)
                                <div class="alert alert-info mt-4">
                                    {{ app()->getLocale() == 'ar' ? 'يجب أن تكون مسجلاً في هذه الدورة لإضافة تقييم.' : 'You must be enrolled in this course to add a review.' }}
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info mt-4">
                                <a href="{{ route('login') }}?redirect={{ url()->current() }}" class="alert-link">{{ app()->getLocale() == 'ar' ? 'تسجيل الدخول' : 'Login' }}</a> {{ app()->getLocale() == 'ar' ? 'لإضافة تقييم.' : 'to add a review.' }}
                            </div>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="course-action-container sticky-top" style="top: 100px;">
                    <h3 class="mb-4">${{ $course->price }}</h3>

                    <div class="course-features">
                        <div class="feature-item">
                            <i class="fas fa-video"></i>
                            <span>{{ $course->videos->count() ?? 0 }} {{ app()->getLocale() == 'ar' ? 'درس فيديو' : 'Video Lessons' }}</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-clock"></i>
                            @php
                                $hours = 0;
                                if(isset($course->duration)) {
                                    $hours = $course->duration;
                                } elseif(isset($course->videos) && $course->videos->count() > 0) {
                                    $hours = ceil($course->videos->sum('duration') / 60);
                                }
                            @endphp
                            <span>{{ $hours }} {{ app()->getLocale() == 'ar' ? 'ساعة من المحتوى' : 'Hours of Content' }}</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-file-alt"></i>
                            <span>{{ $course->materials->count() ?? 0 }} {{ app()->getLocale() == 'ar' ? 'مورد قابل للتنزيل' : 'Downloadable Resources' }}</span>
                        </div>
                        @if(isset($course->level))
                        <div class="feature-item">
                            <i class="fas fa-signal"></i>
                            <span>
                                @if($course->level == 'beginner')
                                    {{ app()->getLocale() == 'ar' ? 'مستوى مبتدئ' : 'Beginner Level' }}
                                @elseif($course->level == 'intermediate')
                                    {{ app()->getLocale() == 'ar' ? 'مستوى متوسط' : 'Intermediate Level' }}
                                @elseif($course->level == 'advanced')
                                    {{ app()->getLocale() == 'ar' ? 'مستوى متقدم' : 'Advanced Level' }}
                                @else
                                    {{ $course->level }}
                                @endif
                            </span>
                        </div>
                        @endif
                        @if(isset($course->language))
                        <div class="feature-item">
                            <i class="fas fa-language"></i>
                            <span>
                                @if($course->language == 'en')
                                    {{ app()->getLocale() == 'ar' ? 'اللغة الإنجليزية' : 'English' }}
                                @elseif($course->language == 'ar')
                                    {{ app()->getLocale() == 'ar' ? 'اللغة العربية' : 'Arabic' }}
                                @else
                                    {{ $course->language }}
                                @endif
                            </span>
                        </div>
                        @endif
                        <div class="feature-item">
                            <i class="fas fa-medal"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'شهادة إتمام' : 'Certificate of Completion' }}</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-infinity"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'وصول كامل مدى الحياة' : 'Full Lifetime Access' }}</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-mobile-alt"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'الوصول على الجوال والتلفزيون' : 'Access on Mobile and TV' }}</span>
                        </div>
                    </div>

                    @auth
                        @if(auth()->user()->hasRole('student') || auth()->user()->hasRole('parent'))
                            @php
                                $isEnrolled = App\Models\Enrollment::where('student_id', auth()->user()->user_id)
                                    ->where('course_id', $course->course_id)
                                    ->exists();
                            @endphp

                            @if($isEnrolled)
                                <a href="{{ route('student.course-content', $course->course_id) }}" class="btn enroll-btn mb-4">
                                    <i class="fas fa-play-circle me-2"></i> {{ app()->getLocale() == 'ar' ? 'متابعة التعلم' : 'Continue Learning' }}
                                </a>
                            @else
                                <form action="{{ route('student.enroll', $course->course_id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn enroll-btn mb-4">
                                        <i class="fas fa-graduation-cap me-2"></i> {{ app()->getLocale() == 'ar' ? 'التسجيل في هذه الدورة' : 'Enroll in this Course' }}
                                    </button>
                                </form>
                            @endif
                        @elseif(auth()->user()->hasRole('instructor'))
                            <div class="alert alert-info">
                                {{ app()->getLocale() == 'ar' ? 'أنت مسجل الدخول كمدرب ولا يمكنك التسجيل في الدورات.' : 'You are logged in as an instructor and cannot enroll in courses.' }}
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}?redirect={{ url()->current() }}" class="btn enroll-btn mb-4">
                            <i class="fas fa-lock me-2"></i> {{ app()->getLocale() == 'ar' ? 'تسجيل الدخول للتسجيل' : 'Login to Enroll' }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Related Courses -->
        @if(isset($relatedCourses) && count($relatedCourses) > 0)
            <div class="related-courses mt-5">
                <h2 class="section-title">{{ app()->getLocale() == 'ar' ? 'دورات ذات صلة' : 'Related Courses' }}</h2>

                <div class="row">
                    @foreach($relatedCourses as $relatedCourse)
                        <div class="col-md-4 mb-4">
                            <div class="related-course-card">
                                @if(isset($relatedCourse->thumbnail) && !empty($relatedCourse->thumbnail))
                                    <img src="{{ asset($relatedCourse->thumbnail) }}" alt="{{ $relatedCourse->title }}" class="related-course-image img-fluid">
                                @else
                                    <img src="https://img.freepik.com/free-photo/student-success-education-lifestyle-concept_23-2148766904.jpg?t=st=1710008242~exp=1710008842~hmac=83a3ad0a86d9b6e6a4c0ef8d61ae46b58e9b9d5b6c08b641a0ad657543b7c0b7" alt="{{ $relatedCourse->title }}" class="related-course-image img-fluid">
                                @endif
                                <div class="related-course-content">
                                    <h5 class="related-course-title">{{ $relatedCourse->title }}</h5>
                                    <p class="related-course-instructor">
                                        @if(isset($relatedCourse->instructor) && isset($relatedCourse->instructor->name))
                                            {{ $relatedCourse->instructor->name }}
                                        @elseif(isset($relatedCourse->instructor_name))
                                            {{ $relatedCourse->instructor_name }}
                                        @else
                                            {{ app()->getLocale() == 'ar' ? 'مدرب متميز' : 'Expert Instructor' }}
                                        @endif
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        @php
                                            $relatedRatings = $relatedCourse->ratings ?? $relatedCourse->reviews ?? collect([]);
                                            $relatedAvgRating = 0;

                                            if ($relatedRatings->count() > 0) {
                                                $relatedAvgRating = $relatedRatings->avg('rating') ?? $relatedRatings->avg('rating_value') ?? 0;
                                            }

                                            $relatedAvgRating = number_format($relatedAvgRating, 1);
                                            $relatedFullStars = floor($relatedAvgRating);
                                            $relatedHalfStar = ($relatedAvgRating - $relatedFullStars) >= 0.5;
                                        @endphp

                                        <div class="star-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $relatedFullStars)
                                                    <i class="fas fa-star"></i>
                                                @elseif($i == $relatedFullStars + 1 && $relatedHalfStar)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="related-course-price">${{ $relatedCourse->price }}</span>
                                    </div>
                                    <a href="{{ route('course.detail', $relatedCourse->course_id ?? $relatedCourse->id ?? '') }}" class="btn btn-outline-primary btn-sm w-100 mt-3">{{ app()->getLocale() == 'ar' ? 'عرض الدورة' : 'View Course' }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection