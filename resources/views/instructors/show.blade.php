@extends('layouts.app')

@section('title', $instructor->name . ' - Instructor Profile')

@section('content')
    <!-- Instructor Profile Header -->
    <section class="instructor-profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 col-md-5">
                    <div class="instructor-profile-image">
                        @if($instructor->profile_image)
                            <img src="{{ asset($instructor->profile_image) }}" alt="{{ $instructor->name }}" class="img-fluid">
                        @else
                            <img src="https://img.freepik.com/free-photo/teacher-with-students-background_23-2148201046.jpg" alt="{{ $instructor->name }}" class="img-fluid">
                        @endif
                    </div>
                </div>
                <div class="col-lg-8 col-md-7">
                    <div class="instructor-profile-details">
                        <h1 class="instructor-name">{{ $instructor->name }}</h1>

                        <div class="instructor-stats">
                            <div class="stat-item">
                                <span class="stat-value">{{ $instructor->courses_count }}</span>
                                <span class="stat-label">Courses</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value">{{ $instructor->enrollments_count }}</span>
                                <span class="stat-label">Students</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value">{{ number_format($averageRating, 1) }}</span>
                                <span class="stat-label">Rating</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value">{{ $totalReviews }}</span>
                                <span class="stat-label">Reviews</span>
                            </div>
                        </div>

                        <div class="instructor-rating">
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $averageRating)
                                        <i class="fas fa-star"></i>
                                    @elseif($i - 0.5 <= $averageRating)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="rating-text">{{ number_format($averageRating, 1) }} instructor rating</span>
                        </div>

                        <div class="instructor-bio">
                            <p>{{ $instructor->bio ?? 'Professional instructor dedicated to helping students achieve their learning goals through engaging and comprehensive courses. Join my classes to gain practical skills and knowledge in a supportive learning environment.' }}</p>
                        </div>

                        <div class="instructor-social-links">
                            @if(isset($instructor->linkedin_profile))
                                <a href="{{ $instructor->linkedin_profile }}" target="_blank" class="social-link linkedin" title="LinkedIn Profile">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            @endif
                            @if(isset($instructor->twitter_profile))
                                <a href="{{ $instructor->twitter_profile }}" target="_blank" class="social-link twitter" title="Twitter Profile">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif
                            @if(isset($instructor->website))
                                <a href="{{ $instructor->website }}" target="_blank" class="social-link website" title="Personal Website">
                                    <i class="fas fa-globe"></i>
                                </a>
                            @endif
                            <a href="mailto:{{ $instructor->email ?? 'contact@example.com' }}" class="social-link email" title="Contact via Email">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Instructor Courses Section -->
    <section class="instructor-courses-section">
        <div class="container">
            <h2 class="section-title">Courses by {{ $instructor->name }}</h2>

            <div class="row">
                @if(count($courses) > 0)
                    @foreach($courses as $course)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="course-card">
                                <div class="course-image">
                                    @if($course->thumbnail)
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="img-fluid">
                                    @else
                                        <img src="https://img.freepik.com/free-photo/students-working-together-project_23-2149038396.jpg" alt="{{ $course->title }}" class="img-fluid">
                                    @endif
                                    <div class="course-overlay">
                                        <a href="{{ url('/courses/' . $course->course_id) }}" class="btn btn-light">View Course</a>
                                    </div>
                                </div>
                                <div class="course-details">
                                    <h3 class="course-title">{{ $course->title }}</h3>
                                    <div class="course-meta">
                                        <span class="course-students"><i class="fas fa-user-graduate"></i> {{ $course->enrollments_count }} students</span>
                                        <span class="course-lessons"><i class="fas fa-book-open"></i> {{ $course->videos_count ?? 0 }} lessons</span>
                                    </div>
                                    <div class="course-rating">
                                        <div class="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $course->average_rating)
                                                    <i class="fas fa-star"></i>
                                                @elseif($i - 0.5 <= $course->average_rating)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="rating-value">{{ number_format($course->average_rating, 1) }} ({{ $course->reviews_count }})</span>
                                    </div>
                                    <p class="course-description">{{ Str::limit($course->description, 120) }}</p>
                                    <div class="course-footer">
                                        <span class="course-price">${{ number_format($course->price, 2) }}</span>
                                        @auth
                                            @if(Auth::user()->hasRole('student'))
                                                @php
                                                    $isEnrolled = App\Models\Enrollment::where('student_id', Auth::user()->user_id)
                                                        ->where('course_id', $course->course_id)
                                                        ->exists();
                                                @endphp

                                                @if($isEnrolled)
                                                    <a href="{{ route('student.course-content', $course->course_id) }}" class="btn btn-success">Continue Learning</a>
                                                @else
                                                    <form action="{{ route('student.enroll', $course->course_id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary">Enroll Now</button>
                                                    </form>
                                                @endif
                                            @else
                                                <a href="{{ url('/courses/' . $course->course_id) }}" class="btn btn-primary">View Course</a>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}?redirect={{ url('/courses/' . $course->course_id) }}" class="btn btn-primary">Login to Enroll</a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> This instructor hasn't published any courses yet.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Instructor Reviews Section -->
    <section class="instructor-reviews-section">
        <div class="container">
            <h2 class="section-title">What Students Say</h2>

            @if(count($recentReviews) > 0)
                <div class="reviews-list">
                    @foreach($recentReviews as $review)
                        <div class="review-card">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        @if($review->user->profile_image)
                                            <img src="{{ asset($review->user->profile_image) }}" alt="{{ $review->user->name }}">
                                        @else
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&background=random&color=fff&background=4361ee" alt="{{ $review->user->name }}">
                                        @endif
                                    </div>
                                    <div class="reviewer-details">
                                        <h4 class="reviewer-name">{{ $review->user->name }}</h4>
                                        <div class="review-course">
                                            <span>Enrolled in: <a href="{{ url('/courses/' . $review->course->course_id) }}">{{ $review->course->title }}</a></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="review-date">{{ $review->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="review-content">
                                <p>{{ $review->comment ?? 'Great instructor! The course content was well-structured and easy to follow. I learned a lot and would definitely recommend this instructor to others.' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h4>No Reviews Yet</h4>
                    <p class="mb-0">Be the first to leave a review for this instructor's courses!</p>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('styles')
<style>
    /* Main Variables */
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3a0ca3;
        --accent-color: #f72585;
        --success-color: #4cc9f0;
        --warning-color: #ffd166;
        --light-color: #f8f9fa;
        --dark-color: #212529;
        --text-color: #333;
        --text-muted: #6c757d;
        --border-radius: 12px;
        --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }

    /* General Styles */
    body {
        color: var(--text-color);
        background-color: #f5f7fa;
    }

    /* Instructor Profile Header */
    .instructor-profile-header {
        padding: 100px 0 80px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 80px;
    }

    .instructor-profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('data:image/svg+xml;charset=utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"%3E%3Cpath fill="%23ffffff" fill-opacity="0.05" d="M0,96L48,112C96,128,192,160,288,186.7C384,213,480,235,576,213.3C672,192,768,128,864,128C960,128,1056,192,1152,208C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"%3E%3C/path%3E%3C/svg%3E');
        background-size: cover;
        background-position: center bottom;
        opacity: 0.2;
    }

    .instructor-profile-image {
        position: relative;
        z-index: 1;
    }

    .instructor-profile-image img {
        width: 100%;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        border: 5px solid rgba(255, 255, 255, 0.2);
        transition: var(--transition);
    }

    .instructor-profile-image img:hover {
        transform: scale(1.02);
        border-color: rgba(255, 255, 255, 0.4);
    }

    .instructor-name {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .instructor-stats {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 30px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: var(--border-radius);
        padding: 20px;
        backdrop-filter: blur(5px);
    }

    .stat-item {
        text-align: center;
        margin-right: 40px;
        position: relative;
    }

    .stat-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: -20px;
        top: 50%;
        transform: translateY(-50%);
        height: 30px;
        width: 1px;
        background: rgba(255, 255, 255, 0.3);
    }

    .stat-value {
        display: block;
        font-size: 2.2rem;
        font-weight: 700;
        color: white;
        line-height: 1;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.8);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .instructor-rating {
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50px;
        padding: 10px 20px;
        width: fit-content;
    }

    .rating-stars {
        color: var(--warning-color);
        font-size: 1.3rem;
        margin-right: 15px;
    }

    .rating-text {
        font-size: 1.1rem;
        color: white;
        font-weight: 500;
    }

    .instructor-bio {
        margin-bottom: 30px;
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.8;
        font-size: 1.1rem;
        max-width: 800px;
    }

    .instructor-social-links {
        display: flex;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        margin-right: 15px;
        transition: var(--transition);
        font-size: 1.2rem;
    }

    .social-link:hover {
        transform: translateY(-5px);
        background-color: white;
    }

    .social-link.linkedin:hover {
        color: #0077b5;
    }

    .social-link.twitter:hover {
        color: #1da1f2;
    }

    .social-link.website:hover {
        color: var(--accent-color);
    }

    /* Instructor Courses Section */
    .instructor-courses-section {
        padding: 0 0 80px;
        position: relative;
    }

    .section-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 50px;
        text-align: center;
        color: var(--text-color);
        position: relative;
        padding-bottom: 15px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    .course-card {
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        height: 100%;
        display: flex;
        flex-direction: column;
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .course-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .course-image {
        position: relative;
        overflow: hidden;
        height: 220px;
    }

    .course-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s ease;
    }

    .course-card:hover .course-image img {
        transform: scale(1.1);
    }

    .course-overlay {
        position: absolute;
        bottom: -60px;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
        padding: 30px 20px 20px;
        transition: bottom 0.4s ease;
        text-align: center;
    }

    .course-card:hover .course-overlay {
        bottom: 0;
    }

    .course-details {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .course-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: var(--text-color);
        line-height: 1.4;
        transition: var(--transition);
    }

    .course-card:hover .course-title {
        color: var(--primary-color);
    }

    .course-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .course-meta i {
        color: var(--primary-color);
        margin-right: 5px;
    }

    .course-rating {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .course-rating .rating-stars {
        color: var(--warning-color);
        font-size: 1rem;
        margin-right: 10px;
    }

    .rating-value {
        font-size: 0.9rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .course-description {
        font-size: 0.95rem;
        color: var(--text-muted);
        margin-bottom: 20px;
        line-height: 1.6;
        flex-grow: 1;
    }

    .course-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }

    .course-price {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--success-color);
    }

    /* Instructor Reviews Section */
    .instructor-reviews-section {
        padding: 80px 0;
        background-color: var(--light-color);
        position: relative;
    }

    .instructor-reviews-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('data:image/svg+xml;charset=utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"%3E%3Cpath fill="%234361ee" fill-opacity="0.03" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,202.7C672,203,768,181,864,186.7C960,192,1056,224,1152,218.7C1248,213,1344,171,1392,149.3L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"%3E%3C/path%3E%3C/svg%3E');
        background-size: cover;
        background-position: center top;
        opacity: 1;
    }

    .reviews-list {
        max-width: 900px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    .review-card {
        background-color: white;
        border-radius: var(--border-radius);
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        border-left: 5px solid var(--primary-color);
    }

    .review-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .reviewer-info {
        display: flex;
        align-items: center;
    }

    .reviewer-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 20px;
        border: 3px solid var(--primary-color);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .reviewer-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .reviewer-name {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 5px;
        color: var(--text-color);
    }

    .review-course {
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .review-course a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
    }

    .review-course a:hover {
        color: var(--secondary-color);
        text-decoration: underline;
    }

    .review-rating {
        text-align: right;
    }

    .review-rating .rating-stars {
        color: var(--warning-color);
        font-size: 1.2rem;
        margin-bottom: 5px;
    }

    .review-date {
        display: block;
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 5px;
    }

    .review-content {
        color: var(--text-color);
        line-height: 1.8;
        font-size: 1rem;
        position: relative;
        padding-left: 25px;
    }

    .review-content::before {
        content: '\201C';
        font-size: 4rem;
        position: absolute;
        left: 0;
        top: -20px;
        color: var(--primary-color);
        opacity: 0.2;
        font-family: Georgia, serif;
    }

    /* Responsive Styles */
    @media (max-width: 1199px) {
        .instructor-profile-header {
            padding: 80px 0 60px;
        }

        .instructor-name {
            font-size: 2.4rem;
        }

        .stat-value {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 991px) {
        .instructor-profile-header {
            padding: 60px 0 40px;
            margin-bottom: 60px;
        }

        .instructor-profile-image {
            margin-bottom: 40px;
            text-align: center;
        }

        .instructor-profile-image img {
            max-width: 300px;
            margin: 0 auto;
        }

        .instructor-name {
            font-size: 2.2rem;
            text-align: center;
        }

        .instructor-stats {
            justify-content: center;
        }

        .instructor-rating {
            margin: 0 auto 25px;
        }

        .instructor-bio {
            text-align: center;
            margin: 0 auto 30px;
        }

        .instructor-social-links {
            justify-content: center;
        }
    }

    @media (max-width: 767px) {
        .instructor-profile-header {
            padding: 50px 0 30px;
        }

        .instructor-name {
            font-size: 2rem;
        }

        .instructor-stats {
            padding: 15px 10px;
        }

        .stat-item {
            margin-right: 20px;
        }

        .stat-item:not(:last-child)::after {
            right: -10px;
        }

        .stat-value {
            font-size: 1.6rem;
        }

        .review-header {
            flex-direction: column;
        }

        .review-rating {
            text-align: left;
            margin-top: 15px;
            margin-left: 80px;
        }

        .section-title {
            font-size: 1.8rem;
        }

        .course-title {
            font-size: 1.2rem;
        }
    }

    @media (max-width: 575px) {
        .instructor-stats {
            flex-wrap: wrap;
        }

        .stat-item {
            width: 45%;
            margin-bottom: 15px;
        }

        .stat-item:not(:last-child)::after {
            display: none;
        }

        .review-card {
            padding: 20px;
        }

        .reviewer-avatar {
            width: 50px;
            height: 50px;
        }

        .review-content {
            padding-left: 0;
        }

        .review-content::before {
            display: none;
        }
    }
</style>
@endsection
