@extends('layouts.app')

@section('title', 'Our Instructors - TOTO Learning Platform')

@section('content')
    <!-- Instructors Header Section -->
    <section class="instructors-header-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1 class="section-title">Meet Our <span class="text-highlight">Expert Instructors</span></h1>
                    <p class="section-description">
                        Learn from industry professionals with years of experience in their fields.
                        Our instructors are passionate about teaching and committed to your success.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Instructors Grid Section -->
    <section class="instructors-grid-section">
        <div class="container">
            <div class="row">
                @if(count($instructors) > 0)
                    @foreach($instructors as $instructor)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="instructor-card">
                                <div class="instructor-image">
                                    @if($instructor->profile_image)
                                        <img src="{{ asset($instructor->profile_image) }}" alt="{{ $instructor->name }}" class="img-fluid">
                                    @else
                                        <div class="default-profile-icon">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                    @endif
                                    <div class="instructor-overlay">
                                        <a href="{{ route('instructors.show', $instructor->user_id) }}" class="btn btn-sm btn-primary">View Profile</a>
                                    </div>
                                </div>
                                <div class="instructor-details">
                                    <h4 class="instructor-name">{{ $instructor->name }}</h4>
                                    <div class="instructor-meta">
                                        <span class="instructor-courses"><i class="fas fa-book-open"></i> {{ $instructor->courses_count }} Courses</span>
                                        <span class="instructor-students"><i class="fas fa-user-graduate"></i> {{ $instructor->enrollments_count }} Students</span>
                                    </div>
                                    <div class="instructor-rating">
                                        <div class="rating-stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $instructor->average_rating)
                                                    <i class="fas fa-star"></i>
                                                @elseif($i - 0.5 <= $instructor->average_rating)
                                                    <i class="fas fa-star-half-alt"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="rating-value">{{ number_format($instructor->average_rating, 1) }} ({{ $instructor->total_reviews }})</span>
                                    </div>
                                    <p class="instructor-bio">{{ Str::limit($instructor->bio ?? 'Professional instructor dedicated to helping students achieve their learning goals.', 100) }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No instructors found. Please check back later.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Become an Instructor Section -->
    <section class="become-instructor-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="become-instructor-content">
                        <h2>Want to Share Your Knowledge?</h2>
                        <p>Join our community of instructors and help students around the world learn new skills. Create engaging courses and earn money while making a difference.</p>
                        <a href="{{ route('instructor.verification.form') }}" class="btn btn-primary">Become an Instructor</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="become-instructor-image">
                        <img src="https://img.freepik.com/free-photo/teacher-explaining-lesson-her-students_23-2148668633.jpg" alt="Become an Instructor" class="img-fluid rounded">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('styles')
<style>
    .instructors-header-section {
        padding: 80px 0 40px;
        background-color: #f8f9fa;
    }

    .instructors-grid-section {
        padding: 60px 0;
    }

    .instructor-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .instructor-card:hover {
        transform: translateY(-5px);
    }

    .instructor-image {
        position: relative;
        overflow: hidden;
        height: 250px;
    }

    .instructor-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .default-profile-icon {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f0f0f0;
        color: #aaa;
        font-size: 8rem;
    }

    .instructor-card:hover .instructor-image img {
        transform: scale(1.05);
    }

    .instructor-overlay {
        position: absolute;
        bottom: -50px;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.7);
        padding: 15px;
        transition: bottom 0.3s ease;
        text-align: center;
    }

    .instructor-card:hover .instructor-overlay {
        bottom: 0;
    }

    .instructor-details {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .instructor-name {
        font-size: 1.2rem;
        margin-bottom: 10px;
        color: #333;
    }

    .instructor-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.85rem;
        color: #666;
    }

    .instructor-rating {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .rating-stars {
        color: #ffc107;
        margin-right: 5px;
    }

    .rating-value {
        font-size: 0.85rem;
        color: #666;
    }

    .instructor-bio {
        font-size: 0.9rem;
        color: #666;
        margin-top: auto;
    }

    .become-instructor-section {
        padding: 80px 0;
        background-color: #f8f9fa;
        margin-top: 40px;
    }

    .become-instructor-content {
        padding-right: 30px;
    }

    .become-instructor-content h2 {
        margin-bottom: 20px;
        color: #333;
    }

    .become-instructor-content p {
        margin-bottom: 30px;
        color: #666;
    }

    @media (max-width: 991px) {
        .become-instructor-content {
            padding-right: 0;
            margin-bottom: 30px;
            text-align: center;
        }
    }
</style>
@endsection
