@extends('layouts.app')

@section('title', $course->title . ' - E-Learning')

@section('styles')
<style>
    /* Course Detail Styles */
    .course-header {
        background: linear-gradient(135deg, #41cdcd 0%, #2bc9c9 50%, #20b7b7 100%);
        padding: 100px 0 50px;
        color: white;
        margin-bottom: 50px;
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
        background: linear-gradient(135deg, #41cdcd 0%, #20b7b7 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 30px;
        font-weight: 600;
        width: 100%;
        transition: all 0.3s ease;
    }

    .enroll-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(32, 183, 183, 0.3);
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
        background: #20b7b7;
    }

    .course-description {
        margin-bottom: 50px;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e7f9f9;
        color: #20b7b7;
    }

    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(32, 183, 183, 0.25);
    }

    .curriculum-item {
        display: flex;
        align-items: center;
        padding: 10px 0;
    }

    .curriculum-item i {
        margin-right: 10px;
        color: #20b7b7;
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
                <div class="col-lg-7">
                    <div class="course-category">{{ $course->category->name ?? 'Uncategorized' }}</div>
                    <h1 class="course-title">{{ $course->title }}</h1>
                    <div class="course-stats">
                        <div class="stat-item">
                            <i class="fas fa-star"></i>
                            <span>{{ number_format($averageRating, 1) }} ({{ $totalRatings }} reviews)</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-users"></i>
                            <span>{{ $course->students->count() ?? 0 }} students</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-clock"></i>
                            <span>{{ $course->videos->count() ?? 0 }} lessons</span>
                        </div>
                    </div>
                    <p>Created by <strong>{{ $course->instructor->name }}</strong></p>
                </div>
                <div class="col-lg-5">
                    <div class="course-image-container">
                        <img src="https://img.freepik.com/free-photo/education-day-arrangement-table-with-copy-space_23-2148721266.jpg" alt="{{ $course->title }}" class="course-image">
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
                    <h2 class="section-title">About This Course</h2>
                    <p>{{ $course->description }}</p>
                </div>

                <!-- Course Curriculum -->
                <div class="course-curriculum mb-5">
                    <h2 class="section-title">Course Curriculum</h2>

                    <div class="accordion" id="curriculumAccordion">
                        @php
                            // Group videos by sections (for demo purposes we'll create mock sections)
                            $sections = [
                                'Introduction' => $course->videos->take(2),
                                'Getting Started' => $course->videos->slice(2, 3),
                                'Advanced Topics' => $course->videos->slice(5)
                            ];
                        @endphp

                        @foreach($sections as $sectionTitle => $sectionVideos)
                            @if(count($sectionVideos) > 0)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ Str::slug($sectionTitle) }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::slug($sectionTitle) }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ Str::slug($sectionTitle) }}">
                                            {{ $sectionTitle }} <span class="ms-auto">{{ count($sectionVideos) }} lectures</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ Str::slug($sectionTitle) }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ Str::slug($sectionTitle) }}">
                                        <div class="accordion-body">
                                            @foreach($sectionVideos as $video)
                                                <div class="curriculum-item">
                                                    <i class="fas fa-play-circle"></i>
                                                    <span>{{ $video->title }}</span>
                                                    <span class="ms-auto">{{ gmdate("i:s", $video->duration) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Instructor Info -->
                <div class="instructor-info mb-5">
                    <h2 class="section-title">Instructor</h2>

                    <div class="instructor-card">
                        <div class="d-flex">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="{{ $course->instructor->name }}" class="instructor-image">
                            <div>
                                <h4>{{ $course->instructor->name }}</h4>
                                <p class="text-muted">{{ $course->category->name }} Expert</p>
                                <div class="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star"></i>
                                    @endfor
                                    <span class="ms-2">4.8 Instructor Rating</span>
                                </div>
                                <div class="mt-2">
                                    <span><i class="fas fa-user-graduate me-2"></i>2,345 Students</span>
                                    <span class="ms-3"><i class="fas fa-book me-2"></i>12 Courses</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, ipsum eget sagittis aliquam, nunc nunc aliquet nunc, vitae aliquam nisl nunc eu nisl. Sed euismod, ipsum eget sagittis aliquam, nunc nunc aliquet nunc, vitae aliquam nisl nunc eu nisl.</p>
                        </div>
                    </div>
                </div>

                <!-- Reviews -->
                <div class="reviews mb-5">
                    <h2 class="section-title">Student Reviews</h2>

                    <!-- Rating Summary -->
                    <div class="row mb-4">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            <h1 class="display-4 fw-bold text-primary">{{ number_format($averageRating, 1) }}</h1>
                            <div class="star-rating mb-2">
                                @php
                                    $fullStars = floor($averageRating);
                                    $halfStar = ($averageRating - $fullStars) >= 0.5;
                                @endphp

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
                            <p>{{ $totalRatings }} ratings</p>
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
                        @if(count($course->ratings) > 0)
                            @foreach($course->ratings->take(5) as $rating)
                                <div class="review-card">
                                    <div class="review-header">
                                        <span class="reviewer-name">Student Name</span>
                                        <span class="review-date">{{ $rating->created_at ? $rating->created_at->format('M d, Y') : 'Unknown date' }}</span>
                                    </div>
                                    <div class="star-rating mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $rating->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <p>{{ $rating->comment ?? 'Great course! Very informative and well-structured.' }}</p>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                No reviews yet. Be the first to review this course!
                            </div>
                        @endif
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
                            <span>{{ $course->videos->count() }} Video Lessons</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-clock"></i>
                            <span>{{ $course->videos->sum('duration') / 60 }} Hours of Content</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-file-alt"></i>
                            <span>{{ $course->materials->count() }} Downloadable Resources</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-medal"></i>
                            <span>Certificate of Completion</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-infinity"></i>
                            <span>Full Lifetime Access</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Access on Mobile and TV</span>
                        </div>
                    </div>

                    @include('courses.detail')
                </div>
            </div>
        </div>

        <!-- Related Courses -->
        @if(count($relatedCourses) > 0)
            <div class="related-courses mt-5">
                <h2 class="section-title">Related Courses</h2>

                <div class="row">
                    @foreach($relatedCourses as $relatedCourse)
                        <div class="col-md-4 mb-4">
                            <div class="related-course-card">
                                <img src="https://img.freepik.com/free-photo/student-success-education-lifestyle-concept_23-2148766904.jpg?t=st=1710008242~exp=1710008842~hmac=83a3ad0a86d9b6e6a4c0ef8d61ae46b58e9b9d5b6c08b641a0ad657543b7c0b7" alt="{{ $relatedCourse->title }}" class="related-course-image img-fluid">
                                <div class="related-course-content">
                                    <h5 class="related-course-title">{{ $relatedCourse->title }}</h5>
                                    <p class="related-course-instructor">{{ $relatedCourse->instructor->name }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="star-rating">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <span class="related-course-price">${{ $relatedCourse->price }}</span>
                                    </div>
                                    <a href="{{ route('course.detail', $relatedCourse->course_id ?? $relatedCourse->id ?? '') }}" class="btn btn-outline-primary btn-sm w-100 mt-3">View Course</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection