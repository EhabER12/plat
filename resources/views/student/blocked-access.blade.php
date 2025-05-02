@extends('layouts.app')

@section('title', 'Access Temporarily Blocked')

@section('styles')
<style>
    .blocked-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .blocked-icon {
        font-size: 64px;
        color: #dc3545;
        margin-bottom: 20px;
    }
    
    .countdown {
        font-size: 24px;
        font-weight: bold;
        margin: 20px 0;
        color: #dc3545;
    }
    
    .blocked-message {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .blocked-details {
        background-color: #f8d7da;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        border-left: 5px solid #dc3545;
    }
    
    .blocked-help {
        background-color: #e2f3fc;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        border-left: 5px solid #17a2b8;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="blocked-container text-center">
        <div class="blocked-icon">
            <i class="fas fa-ban"></i>
        </div>
        
        <h1 class="mb-4">Access Temporarily Blocked</h1>
        
        <div class="blocked-message">
            <p class="lead">Your access to this video has been temporarily blocked due to suspicious activity that may violate our terms of service.</p>
            
            <div class="countdown" id="countdown">
                Time remaining: <span id="hours">{{ floor($remainingTime / 60) }}</span>h <span id="minutes">{{ $remainingTime % 60 }}</span>m
            </div>
        </div>
        
        <div class="blocked-details text-start">
            <h4>Block Details:</h4>
            <ul>
                <li><strong>Video:</strong> {{ $video->title ?? 'Unknown video' }}</li>
                <li><strong>Course:</strong> {{ $course->title ?? 'Unknown course' }}</li>
                <li><strong>Blocked until:</strong> {{ $block->blocked_until->format('F j, Y, g:i a') }}</li>
                <li><strong>Reason:</strong> Multiple suspicious download attempts detected</li>
            </ul>
        </div>
        
        <div class="blocked-help text-start">
            <h4>What happened?</h4>
            <p>Our system detected multiple attempts to download or extract video content, which is not permitted according to our terms of service.</p>
            
            <h4>What can I do?</h4>
            <p>You can continue watching other videos in your enrolled courses. Access to this specific video will be restored automatically after the block period expires.</p>
            
            <h4>Need help?</h4>
            <p>If you believe this is a mistake, please contact our support team for assistance.</p>
        </div>
        
        <div class="mt-4">
            <a href="{{ route('student.my-courses') }}" class="btn btn-primary me-2">
                <i class="fas fa-book-reader me-2"></i> Go to My Courses
            </a>
            
            @if($courseId)
            <a href="{{ route('student.course-content', $courseId) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Course
            </a>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Countdown timer
    document.addEventListener('DOMContentLoaded', function() {
        let remainingSeconds = {{ $remainingTime * 60 }};
        const countdownElement = document.getElementById('countdown');
        const hoursElement = document.getElementById('hours');
        const minutesElement = document.getElementById('minutes');
        
        const countdownInterval = setInterval(function() {
            remainingSeconds--;
            
            if (remainingSeconds <= 0) {
                clearInterval(countdownInterval);
                countdownElement.innerHTML = '<span class="text-success">Block period has expired. You can now access the video.</span>';
                
                // Reload the page after a short delay
                setTimeout(function() {
                    window.location.reload();
                }, 3000);
                
                return;
            }
            
            const hours = Math.floor(remainingSeconds / 3600);
            const minutes = Math.floor((remainingSeconds % 3600) / 60);
            const seconds = remainingSeconds % 60;
            
            hoursElement.textContent = hours;
            minutesElement.textContent = minutes;
        }, 1000);
    });
</script>
@endsection
