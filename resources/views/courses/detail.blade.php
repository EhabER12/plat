<div class="course-actions mt-4">
    @auth
        @if(auth()->user()->hasRole('student') || auth()->user()->hasRole('parent'))
            @php
                $isEnrolled = App\Models\Enrollment::where('student_id', auth()->user()->user_id)
                    ->where('course_id', $course->course_id)
                    ->exists();
            @endphp
            
            @if($isEnrolled)
                <a href="{{ route('student.course-content', $course->course_id) }}" class="btn btn-success btn-lg w-100 mb-3">
                    <i class="fas fa-play-circle me-2"></i> Continue Learning
                </a>
            @else
                <form action="{{ route('student.enroll', $course->course_id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                        <i class="fas fa-graduation-cap me-2"></i> Enroll Now
                    </button>
                </form>
            @endif
        @elseif(auth()->user()->hasRole('instructor'))
            <div class="alert alert-info">
                You are logged in as an instructor and cannot enroll in courses.
            </div>
        @endif
    @else
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100 mb-3">
            <i class="fas fa-lock me-2"></i> Login to Enroll
        </a>
    @endauth
    
    <button class="btn btn-outline-secondary btn-lg w-100">
        <i class="far fa-bookmark me-2"></i> Save to Wishlist
    </button>
</div> 