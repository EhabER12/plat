<!-- Featured Instructors Section -->
<section class="featured-instructors-section">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <h2 class="section-title">Learn from the <span class="text-highlight">Best</span></h2>
            <p class="section-description">
                Our instructors are passionate experts with years of experience in their fields.
                Join their courses and gain valuable knowledge and skills.
            </p>
        </div>

        <div class="row" data-aos="fade-up">
            @if(isset($featuredInstructors) && count($featuredInstructors) > 0)
                @foreach($featuredInstructors as $instructor)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="instructor-card">
                            <div class="instructor-image">
                                @if($instructor->profile_image)
                                    <img src="{{ asset($instructor->profile_image) }}" alt="{{ $instructor->name }}" class="img-fluid">
                                @else
                                    <img src="https://img.freepik.com/free-photo/teacher-explaining-lesson-her-students_23-2148668634.jpg" alt="{{ $instructor->name }}" class="img-fluid">
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
                                    <span class="rating-value">{{ number_format($instructor->average_rating, 1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> No featured instructors available at the moment.
                    </div>
                </div>
            @endif
        </div>

        <div class="text-center mt-4" data-aos="fade-up">
            <a href="{{ route('instructors.index') }}" class="btn btn-outline-primary">View All Instructors <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>

<style>
    .featured-instructors-section {
        padding: 80px 0;
        background-color: #f8f9fa;
    }

    .instructor-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        height: 100%;
        background-color: #fff;
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
    }

    .rating-stars {
        color: #ffc107;
        margin-right: 5px;
    }

    .rating-value {
        font-size: 0.85rem;
        color: #666;
    }
</style>
