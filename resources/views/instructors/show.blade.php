@extends('layouts.app')

@section('title', $instructor->name . ' - Instructor Profile')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <!-- Instructor Profile Header -->
    <section class="instructor-profile-header text-center">
        <div class="instructor-banner" style="height: 300px; overflow: hidden; position: relative; background: none;">
            @if($instructor->banner_image)
                <img src="{{ asset($instructor->banner_image) }}" alt="{{ $instructor->name }}'s Banner" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; z-index: 1;">
            @else
                <div style="width: 100%; height: 100%; background-image: url('{{ asset('images/default-banner.jpg') }}'); background-size: cover; background-position: center; position: absolute; top: 0; left: 0; z-index: 1;"></div>
            @endif
        </div>
        
        <div class="container">
            <div class="instructor-profile-image mx-auto" style="margin-top: -75px; position: relative; z-index: 3;">
                @if($instructor->profile_image)
                    <img src="{{ asset($instructor->profile_image) }}" alt="{{ $instructor->name }}" class="img-fluid rounded-circle border border-white border-3" style="width: 150px; height: 150px; object-fit: cover; background-color: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($instructor->name) }}&background=random&color=fff&size=150" alt="{{ $instructor->name }}" class="img-fluid rounded-circle border border-white border-3" style="width: 150px; height: 150px; object-fit: cover; background-color: white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                @endif
            </div>
            
            <h1 class="instructor-name mt-4">{{ $instructor->name }}</h1>
            
            <div class="instructor-rating mx-auto">
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
                <span class="rating-text">{{ number_format($averageRating, 1) }} {{ __('instructor rating') }}</span>
            </div>
            
            <div class="instructor-stats mx-auto mt-4">
                <div class="stat-item">
                    <span class="stat-value">{{ $instructor->courses_count }}</span>
                    <span class="stat-label">{{ __('Courses') }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">{{ $instructor->enrollments_count }}</span>
                    <span class="stat-label">{{ __('Students') }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">{{ $totalReviews }}</span>
                    <span class="stat-label">{{ __('Reviews') }}</span>
                </div>
            </div>
            
            <div class="instructor-actions mt-4">
                @auth
                    @if(Auth::user()->hasRole('student'))
                        <a href="{{ route('student.messages.show', $instructor->user_id) }}" class="btn btn-primary">
                            <i class="fas fa-envelope me-2"></i> {{ __('Message Instructor') }}
                        </a>
                    @elseif(!Auth::user()->hasRole('instructor') || Auth::user()->user_id != $instructor->user_id)
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-envelope me-2"></i> {{ __('Message Instructor') }}
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}?redirect={{ route('instructors.show', $instructor->user_id) }}" class="btn btn-primary">
                        <i class="fas fa-envelope me-2"></i> {{ __('Login to Message') }}
                    </a>
                @endauth
            </div>
            
            <div class="instructor-social-links mt-4">
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
    </section>

    <!-- Scroll Popup -->
    <div class="scroll-popup" id="scrollPopup">
        <div class="popup-content">
            <button class="close-popup" id="closePopup"><i class="fas fa-times"></i></button>
            <div class="popup-header">
                <i class="fas fa-graduation-cap popup-icon"></i>
                <h3>{{ __('Enhance Your Learning') }}</h3>
            </div>
            <div class="popup-body">
                <p>{{ __('Get exclusive access to premium courses and special offers from') }} <strong>{{ $instructor->name }}</strong></p>
                <form class="popup-form">
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="{{ __('Your Email Address') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary popup-btn">{{ __('Subscribe Now') }}</button>
                </form>
                <div class="popup-footer">
                    <p class="small text-muted">{{ __('No spam, unsubscribe at any time') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructor Bio Section -->
    <section class="instructor-bio-section">
        <div class="container">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="section-title text-center mb-4">About {{ $instructor->name }}</h2>
                    
                    <div class="instructor-short-bio text-center mb-4">
                        <p>{{ $instructor->bio ?? 'Professional instructor dedicated to helping students achieve their learning goals through engaging and comprehensive courses. Join my classes to gain practical skills and knowledge in a supportive learning environment.' }}</p>
                    </div>
                    
                    @if(isset($instructor->detailed_description) && !empty($instructor->detailed_description))
                        <div class="instructor-detailed-description mt-4">
                            {!! nl2br(e($instructor->detailed_description)) !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Instructor Courses Section -->
    <section class="instructor-courses-section">
        <div class="container">
            <h2 class="section-title text-center">{{ __('Courses by') }} {{ $instructor->name }}</h2>

            <div class="row">
                @if(count($courses) > 0)
                    @foreach($courses as $course)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="course-card">
                                <div class="course-image">
                                    @if($course->thumbnail)
                                        <img src="{{ asset($course->thumbnail) }}" alt="{{ $course->title }}" class="img-fluid">
                                    @elseif($course->image_path)
                                        <img src="{{ asset($course->image_path) }}" alt="{{ $course->title }}" class="img-fluid">
                                    @else
                                        <img src="{{ asset('images/default-course-thumbnail.jpg') }}" alt="{{ $course->title }}" class="img-fluid">
                                    @endif
                                    <div class="course-category-badge">{{ $course->category->name ?? __('Uncategorized') }}</div>
                                    <div class="course-overlay">
                                        <a href="{{ url('/courses/' . $course->course_id) }}" class="btn btn-light">{{ __('View Course') }}</a>
                                    </div>
                                </div>
                                <div class="course-details">
                                    <h3 class="course-title">{{ $course->title }}</h3>
                                    <div class="course-meta">
                                        <span class="course-students"><i class="fas fa-user-graduate"></i> {{ $course->enrollments_count }} {{ __('students') }}</span>
                                        <span class="course-lessons"><i class="fas fa-book-open"></i> {{ $course->videos_count ?? $course->lessons_count ?? 0 }} {{ __('lessons') }}</span>
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
                                        @if($course->price > 0)
                                            <span class="course-price">${{ number_format($course->price, 2) }}</span>
                                        @else
                                            <span class="course-price free">{{ __('Free') }}</span>
                                        @endif
                                        @auth
                                            @if(Auth::user()->hasRole('student'))
                                                @php
                                                    $isEnrolled = App\Models\Enrollment::where('student_id', Auth::user()->user_id)
                                                        ->where('course_id', $course->course_id)
                                                        ->exists();
                                                @endphp

                                                @if($isEnrolled)
                                                    <a href="{{ route('student.course-content', $course->course_id) }}" class="btn btn-success">{{ __('Continue Learning') }}</a>
                                                @else
                                                    <form action="{{ route('student.enroll', $course->course_id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary">{{ __('Enroll Now') }}</button>
                                                    </form>
                                                @endif
                                            @else
                                                <a href="{{ url('/courses/' . $course->course_id) }}" class="btn btn-primary">{{ __('View Course') }}</a>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}?redirect={{ url('/courses/' . $course->course_id) }}" class="btn btn-primary">{{ __('Login to Enroll') }}</a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle me-2"></i> {{ __('This instructor hasn\'t published any courses yet.') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Subscription Plans Section -->
    <section class="subscription-plans-section">
        <div class="container">
            <h2 class="section-title text-center">Subscription Plans</h2>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="plan-card">
                        <div class="plan-header">
                            <h3 class="plan-title">Basic Plan</h3>
                            <div class="plan-price">$9.99<span>/month</span></div>
                        </div>
                        <div class="plan-features">
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Access to 5 courses</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Basic course materials</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Email support</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-times-circle text-muted"></i>
                                <span class="text-muted">Premium content</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-times-circle text-muted"></i>
                                <span class="text-muted">Direct instructor access</span>
                            </div>
                        </div>
                        <div class="plan-footer">
                            <a href="#" class="plan-btn">Subscribe Now</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="plan-card">
                        <div class="popular-plan">Popular</div>
                        <div class="plan-header">
                            <h3 class="plan-title">Pro Plan</h3>
                            <div class="plan-price">$19.99<span>/month</span></div>
                        </div>
                        <div class="plan-features">
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Access to all courses</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Full course materials</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Priority email support</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Premium content</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-times-circle text-muted"></i>
                                <span class="text-muted">Direct instructor access</span>
                            </div>
                        </div>
                        <div class="plan-footer">
                            <a href="#" class="plan-btn">Subscribe Now</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="plan-card">
                        <div class="plan-header">
                            <h3 class="plan-title">Premium Plan</h3>
                            <div class="plan-price">$29.99<span>/month</span></div>
                        </div>
                        <div class="plan-features">
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Access to all courses</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Full course materials + extras</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>24/7 support</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Premium content</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Direct instructor access</span>
                            </div>
                        </div>
                        <div class="plan-footer">
                            <a href="#" class="plan-btn">Subscribe Now</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-muted">Plans are billed monthly and can be canceled at any time.</p>
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
        --reveal-transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    }

    /* General Styles */
    body {
        color: var(--text-color);
        background-color: #f5f7fa;
        overflow-x: hidden;
    }

    /* Animation Classes */
    .fade-in {
        opacity: 0;
        transform: translateY(30px);
        animation: fadeIn 0.8s forwards;
    }

    .slide-in-left {
        opacity: 0;
        transform: translateX(-50px);
        animation: slideInLeft 0.8s forwards;
    }

    .slide-in-right {
        opacity: 0;
        transform: translateX(50px);
        animation: slideInRight 0.8s forwards;
    }

    .scale-in {
        opacity: 0;
        transform: scale(0.8);
        animation: scaleIn 0.8s forwards;
    }

    .reveal {
        opacity: 0;
        transform: translateY(40px);
        transition: var(--reveal-transition);
    }

    .reveal.active {
        opacity: 1;
        transform: translateY(0);
    }

    /* Animation Delays */
    .delay-1 {
        animation-delay: 0.1s;
    }
    .delay-2 {
        animation-delay: 0.2s;
    }
    .delay-3 {
        animation-delay: 0.3s;
    }
    .delay-4 {
        animation-delay: 0.4s;
    }
    .delay-5 {
        animation-delay: 0.5s;
    }

    /* Animation Keyframes */
    @keyframes fadeIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInRight {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes scaleIn {
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
        100% {
            transform: translateY(0px);
        }
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }

    /* Instructor Profile Header */
    .instructor-profile-header {
        padding: 80px 0 60px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 40px;
        animation: gradientShift 15s ease infinite;
    }

    @keyframes gradientShift {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
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
        animation: wavyAnimation 25s linear infinite;
    }

    @keyframes wavyAnimation {
        0% {
            background-position: 0% bottom;
        }
        100% {
            background-position: 100% bottom;
        }
    }

    .instructor-profile-image {
        position: relative;
        z-index: 1;
        width: 180px;
        height: 180px;
    }

    .instructor-profile-image img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        box-shadow: var(--box-shadow);
        border: 5px solid rgba(255, 255, 255, 0.2);
        transition: var(--transition);
        animation: float 5s ease-in-out infinite;
    }

    .instructor-profile-image:hover img {
        border-color: rgba(255, 255, 255, 0.5);
        transform: scale(1.05);
    }

    .instructor-name {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        animation: fadeIn 1s both;
    }

    .instructor-stats {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        margin-bottom: 30px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: var(--border-radius);
        padding: 20px;
        backdrop-filter: blur(5px);
        max-width: 500px;
        animation: fadeIn 1.2s both;
        transition: var(--transition);
        transform-origin: center;
    }

    .instructor-stats:hover {
        transform: scale(1.02);
        background: rgba(255, 255, 255, 0.15);
    }

    .stat-item {
        text-align: center;
        margin: 0 25px;
        position: relative;
        transition: var(--transition);
    }

    .stat-item:hover {
        transform: translateY(-5px);
    }

    .stat-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: -25px;
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
        transition: var(--transition);
    }

    .stat-item:hover .stat-value {
        transform: scale(1.1);
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
    }

    .stat-label {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.8);
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: var(--transition);
    }

    .instructor-rating {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50px;
        padding: 10px 20px;
        width: fit-content;
        animation: fadeIn 1.4s both;
        transition: var(--transition);
    }

    .instructor-rating:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
    }

    .rating-stars {
        color: var(--warning-color);
        font-size: 1.3rem;
        margin-right: 15px;
        transition: var(--transition);
    }

    .instructor-rating:hover .rating-stars {
        animation: pulse 1s infinite;
    }

    .rating-text {
        font-size: 1.1rem;
        color: white;
        font-weight: 500;
    }

    .instructor-actions {
        margin-bottom: 20px;
        animation: fadeIn 1.6s both;
    }

    .instructor-actions .btn {
        padding: 10px 25px;
        font-weight: 600;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .instructor-actions .btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: 0.5s;
    }

    .instructor-actions .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }

    .instructor-actions .btn:hover:before {
        left: 100%;
    }

    .instructor-social-links {
        display: flex;
        justify-content: center;
        animation: fadeIn 1.8s both;
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
        margin: 0 10px;
        transition: var(--transition);
        font-size: 1.2rem;
        transform: translateY(0);
    }

    .social-link:hover {
        transform: translateY(-5px);
        background-color: white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
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

    /* Instructor Bio Section */
    .instructor-bio-section {
        padding: 0 0 60px;
    }

    .instructor-short-bio {
        font-size: 1.1rem;
        color: var(--text-color);
        line-height: 1.8;
        max-width: 800px;
        margin: 0 auto;
        transition: var(--transition);
    }

    .instructor-detailed-description {
        font-size: 1.05rem;
        color: var(--text-color);
        line-height: 1.8;
        padding: 20px;
        background-color: var(--light-color);
        border-radius: var(--border-radius);
        border-left: 4px solid var(--primary-color);
        transition: var(--transition);
    }

    .instructor-detailed-description:hover {
        border-left-width: 8px;
        transform: translateX(5px);
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
        color: var(--text-color);
        position: relative;
        padding-bottom: 15px;
        text-align: center;
        transition: var(--transition);
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
        transition: var(--transition);
    }

    .section-title:hover::after {
        width: 120px;
    }

    /* Course Cards Style */
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
        transform: translateY(0);
    }

    .course-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .course-image {
        position: relative;
        overflow: hidden;
        height: 200px;
        background-color: #f0f2f5;
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

    .course-image::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.2);
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1;
    }

    .course-card:hover .course-image::before {
        opacity: 1;
    }

    .course-category-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: var(--primary-color);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        z-index: 3;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: var(--transition);
    }
    
    .course-card:hover .course-category-badge {
        background-color: var(--secondary-color);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .course-overlay {
        position: absolute;
        bottom: -60px;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
        padding: 30px 20px 20px;
        transition: all 0.4s ease;
        text-align: center;
        opacity: 0;
        z-index: 2;
    }

    .course-card:hover .course-overlay {
        bottom: 0;
        opacity: 1;
    }

    .course-details {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        transition: var(--transition);
    }

    .course-card:hover .course-details {
        background-color: rgba(67, 97, 238, 0.03);
    }

    .course-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: var(--text-color);
        line-height: 1.4;
        transition: var(--transition);
        min-height: 50px;
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
        transition: var(--transition);
    }

    .course-meta i {
        color: var(--primary-color);
        margin-right: 5px;
        transition: var(--transition);
    }

    .course-card:hover .course-meta i {
        transform: scale(1.2);
    }

    .course-rating {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        transition: var(--transition);
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
        transition: var(--transition);
    }

    .course-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        transition: var(--transition);
    }

    .course-price {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--success-color);
        transition: var(--transition);
    }

    .course-card:hover .course-price {
        transform: scale(1.05);
    }

    .course-price.free {
        color: var(--success-color);
        background: rgba(76, 201, 240, 0.1);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 1rem;
        transition: var(--transition);
    }

    .course-card:hover .course-price.free {
        background: rgba(76, 201, 240, 0.2);
    }

    .course-footer .btn {
        padding: 8px 15px;
        font-size: 0.9rem;
        border-radius: 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .course-footer .btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: 0.5s;
    }

    .course-footer .btn:hover:before {
        left: 100%;
    }

    /* Subscription Plans Section */
    .subscription-plans-section {
        padding: 80px 0;
        background-color: var(--light-color);
        position: relative;
    }

    .plan-card {
        background-color: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        height: 100%;
        position: relative;
        border: 1px solid rgba(0, 0, 0, 0.05);
        transform: translateY(0);
    }

    .plan-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .plan-header {
        padding: 30px 20px;
        text-align: center;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        transition: var(--transition);
        background-size: 200% 200%;
    }

    .plan-card:hover .plan-header {
        animation: gradientShift 5s ease infinite;
    }

    .plan-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 5px;
        transition: var(--transition);
    }

    .plan-card:hover .plan-title {
        transform: scale(1.05);
    }

    .plan-price {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        transition: var(--transition);
    }

    .plan-card:hover .plan-price {
        transform: translateY(-5px);
    }

    .plan-price span {
        font-size: 1rem;
        font-weight: 400;
    }

    .plan-features {
        padding: 30px 20px;
        transition: var(--transition);
    }

    .feature-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        font-size: 1rem;
        transition: var(--transition);
    }

    .plan-card:hover .feature-item {
        transform: translateX(5px);
    }

    .feature-item i {
        color: var(--success-color);
        margin-right: 10px;
        transition: var(--transition);
    }

    .plan-card:hover .feature-item i {
        transform: scale(1.2);
    }

    .plan-footer {
        padding: 0 20px 30px;
        text-align: center;
    }

    .plan-btn {
        display: inline-block;
        padding: 12px 30px;
        background-color: var(--primary-color);
        color: white;
        border-radius: 50px;
        font-weight: 600;
        transition: var(--transition);
        border: none;
        width: 100%;
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }

    .plan-btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: 0.5s;
    }

    .plan-btn:hover {
        background-color: var(--secondary-color);
        transform: translateY(-3px);
        color: white;
        text-decoration: none;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    .plan-btn:hover:before {
        left: 100%;
    }

    .popular-plan {
        position: absolute;
        top: 0;
        right: 0;
        background-color: var(--accent-color);
        color: white;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 5px 15px;
        text-transform: uppercase;
        transform: rotate(45deg) translateX(30%) translateY(-100%);
        transform-origin: top right;
        width: 150px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: var(--transition);
    }

    .plan-card:hover .popular-plan {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
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
        animation: wavyAnimation 30s linear infinite reverse;
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
        transform: translateY(0);
    }

    .review-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        border-left-width: 10px;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        transition: var(--transition);
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
        transition: var(--transition);
    }

    .review-card:hover .reviewer-avatar {
        transform: scale(1.1);
        border-width: 4px;
    }

    .reviewer-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition);
    }

    .reviewer-name {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 5px;
        color: var(--text-color);
        transition: var(--transition);
    }

    .review-card:hover .reviewer-name {
        color: var(--primary-color);
    }

    .review-course {
        font-size: 0.9rem;
        color: var(--text-muted);
        transition: var(--transition);
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
        transition: var(--transition);
    }

    .review-rating .rating-stars {
        color: var(--warning-color);
        font-size: 1.2rem;
        margin-bottom: 5px;
    }

    .review-card:hover .review-rating .rating-stars {
        transform: scale(1.1);
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
        transition: var(--transition);
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
        transition: var(--transition);
    }

    .review-card:hover .review-content::before {
        opacity: 0.5;
        transform: scale(1.2);
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

        .course-image {
            height: 180px;
        }
    }

    @media (max-width: 991px) {
        .instructor-profile-header {
            padding: 60px 0 40px;
        }

        .instructor-name {
            font-size: 2.2rem;
        }

        .course-title {
            font-size: 1.1rem;
            min-height: 60px;
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
            margin: 0 15px;
        }

        .stat-item:not(:last-child)::after {
            right: -15px;
        }

        .stat-value {
            font-size: 1.6rem;
        }

        .section-title {
            font-size: 1.8rem;
        }

        .course-card {
            margin-bottom: 30px;
        }
    }

    @media (max-width: 575px) {
        .instructor-profile-image {
            width: 150px;
            height: 150px;
        }

        .instructor-stats {
            flex-wrap: wrap;
        }

        .stat-item {
            width: 45%;
            margin: 0 10px 15px;
        }

        .stat-item:not(:last-child)::after {
            display: none;
        }

        .instructor-social-links {
            flex-wrap: wrap;
        }

        .course-meta {
            flex-direction: column;
            gap: 8px;
        }

        .course-footer {
            flex-direction: column;
            gap: 15px;
            align-items: stretch;
        }

        .course-price {
            text-align: center;
            margin-bottom: 5px;
        }
    }

    /* Scroll Popup Styles */
    .scroll-popup {
        position: fixed;
        bottom: -400px;
        right: 30px;
        width: 360px;
        max-width: 90%;
        background-color: white;
        border-radius: var(--border-radius);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        opacity: 0;
        transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
        overflow: hidden;
        transform: translateY(0) scale(0.95);
    }

    .scroll-popup.show {
        opacity: 1;
        bottom: 30px;
        transform: translateY(0) scale(1);
    }

    .popup-content {
        position: relative;
        padding: 25px;
    }

    .close-popup {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.05);
        border: none;
        border-radius: 50%;
        color: var(--text-muted);
        cursor: pointer;
        transition: var(--transition);
        z-index: 2;
    }

    .close-popup:hover {
        background: rgba(0, 0, 0, 0.1);
        color: var(--dark-color);
        transform: rotate(90deg);
    }

    .popup-header {
        text-align: center;
        margin-bottom: 20px;
        position: relative;
    }

    .popup-icon {
        display: block;
        font-size: 2.5rem;
        margin: 0 auto 15px;
        color: var(--primary-color);
        background: rgba(67, 97, 238, 0.1);
        width: 70px;
        height: 70px;
        line-height: 70px;
        border-radius: 50%;
        transition: var(--transition);
        animation: float 3s ease-in-out infinite;
    }

    .popup-header h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 10px;
    }

    .popup-body {
        text-align: center;
    }

    .popup-body p {
        font-size: 1rem;
        color: var(--text-color);
        margin-bottom: 20px;
    }

    .popup-form {
        margin: 20px 0;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .popup-btn {
        width: 100%;
        padding: 12px;
        font-weight: 600;
        border-radius: 50px;
        position: relative;
        overflow: hidden;
        transition: var(--transition);
    }

    .popup-btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: 0.5s;
    }

    .popup-btn:hover:before {
        left: 100%;
    }

    .popup-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .popup-footer {
        margin-top: 15px;
    }

    @keyframes popup-entry {
        0% {
            opacity: 0;
            transform: translateY(50px) scale(0.9);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .popup-appear {
        animation: popup-entry 0.5s forwards;
    }
</style>

<script>
// Initialize animations when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add animation classes to elements
    addInitialAnimations();
    
    // Initialize scroll reveal
    initScrollReveal();
    
    // Initialize scroll popup
    initScrollPopup();
});

// Add initial animation classes to elements that should animate on page load
function addInitialAnimations() {
    // Instructor profile elements
    document.querySelector('.instructor-name').classList.add('fade-in');
    document.querySelector('.instructor-stats').classList.add('fade-in', 'delay-1');
    document.querySelector('.instructor-rating').classList.add('fade-in', 'delay-2');
    document.querySelector('.instructor-actions').classList.add('fade-in', 'delay-3');
    document.querySelector('.instructor-social-links').classList.add('fade-in', 'delay-4');
    
    // Apply animations to course cards and plans
    animateItemsWithDelay('.course-card', 'scale-in', 100);
    animateItemsWithDelay('.plan-card', 'slide-in-left', 150);
    animateItemsWithDelay('.review-card', 'slide-in-right', 200);
}

// Initialize scroll reveal for elements that should animate when scrolled into view
function initScrollReveal() {
    const revealElements = document.querySelectorAll('.section-title, .instructor-bio-section .card, .review-card, .plan-card, .course-card');
    
    // Create an intersection observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    });
    
    // Observe each element
    revealElements.forEach(element => {
        element.classList.add('reveal');
        observer.observe(element);
    });
}

// Apply animation with staggered delay to a collection of elements
function animateItemsWithDelay(selector, animationClass, delayIncrement) {
    const items = document.querySelectorAll(selector);
    
    items.forEach((item, index) => {
        // Add animation class
        item.classList.add(animationClass);
        
        // Set animation delay
        const delay = (index + 1) * delayIncrement;
        item.style.animationDelay = delay + 'ms';
    });
}

// Initialize scroll popup
function initScrollPopup() {
    const popup = document.getElementById('scrollPopup');
    const closeBtn = document.getElementById('closePopup');
    let hasShown = false;
    let popupClosed = false;
    
    // Close popup when close button is clicked
    closeBtn.addEventListener('click', () => {
        popup.classList.remove('show');
        popupClosed = true;
        
        // Store in session that user has closed the popup
        sessionStorage.setItem('popupClosed', 'true');
    });
    
    // Show popup when user scrolls down
    window.addEventListener('scroll', () => {
        // Check if popup was already closed in this session
        if(sessionStorage.getItem('popupClosed') === 'true') {
            return;
        }
        
        // Show popup after scrolling down 50% of the viewport height
        const scrollPosition = window.scrollY;
        const viewportHeight = window.innerHeight;
        const pageHeight = document.documentElement.scrollHeight;
        
        if (!hasShown && !popupClosed && scrollPosition > viewportHeight * 0.5) {
            popup.classList.add('show');
            hasShown = true;
            
            // Add appear animation
            popup.classList.add('popup-appear');
        }
    });
    
    // Prevent form submission (for demo)
    const form = popup.querySelector('form');
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Subscription successful! This is a demo.');
        popup.classList.remove('show');
        popupClosed = true;
        sessionStorage.setItem('popupClosed', 'true');
    });
}
</script>
@endsection
