@extends('layouts.app')

@section('title', $course->title . ' - Course Content')

@section('styles')
<style>
    .content-sidebar {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
    }
    
    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .video-wrapper iframe,
    .video-wrapper video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    /* Add style to prevent video caching */
    .video-player[data-nocache="true"] {
        transform: translateZ(0);
        backface-visibility: hidden;
        will-change: transform;
    }
    
    .lesson-list {
        list-style-type: none;
        padding-left: 0;
    }
    
    .lesson-item {
        padding: 12px 15px;
        border-radius: 5px;
        margin-bottom: 8px;
        background-color: #fff;
        border-left: 3px solid transparent;
        transition: all 0.2s ease;
    }
    
    .lesson-item:hover {
        background-color: #e9f7f7;
        border-left-color: #20b7b7;
    }
    
    .lesson-item.active {
        background-color: #e9f7f7;
        border-left-color: #20b7b7;
        font-weight: 600;
    }
    
    .lesson-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .lesson-icon {
        margin-right: 10px;
        color: #20b7b7;
    }
    
    .lesson-duration {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .material-item {
        display: flex;
        align-items: center;
        padding: 10px;
        background-color: #fff;
        border-radius: 5px;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }
    
    .material-item:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
    }
    
    .material-icon {
        font-size: 24px;
        margin-right: 15px;
        color: #6c757d;
    }
    
    .material-info {
        flex-grow: 1;
    }
    
    .material-title {
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .material-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .review-form {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-top: 30px;
    }
    
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
    }
    
    .star-rating input {
        display: none;
    }
    
    .star-rating label {
        cursor: pointer;
        width: 30px;
        height: 30px;
        margin: 0 2px;
        position: relative;
        color: #ddd;
    }
    
    .star-rating label:before {
        content: '\f005';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        font-size: 24px;
    }
    
    .star-rating input:checked ~ label {
        color: #FFD700;
    }
    
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #FFD700;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Course Content Main Area -->
            <h1 class="mb-4">{{ $course->title }}</h1>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="mb-4">
                <div class="video-wrapper">
                    <!-- This would be the actual video. For the demo, using a placeholder -->
                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen data-nocache="true"></iframe>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Introduction to the Course</h3>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-step-backward"></i> Previous
                        </button>
                        <button class="btn btn-sm btn-outline-primary">
                            Next <i class="fas fa-step-forward"></i>
                        </button>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h4>Lesson Description</h4>
                        <p>This is the introduction to the course. You'll learn about the course structure, objectives, and what to expect from the lessons ahead.</p>
                    </div>
                </div>
            </div>
            
            <!-- Course Materials -->
            <div class="mb-5">
                <h3 class="mb-4">Course Materials</h3>
                
                @if(count($course->materials) > 0)
                    @foreach($course->materials as $material)
                        <div class="material-item">
                            <div class="material-icon">
                                @if(in_array($material->file_type, ['pdf', 'doc', 'docx']))
                                    <i class="fas fa-file-pdf"></i>
                                @elseif(in_array($material->file_type, ['xls', 'xlsx', 'csv']))
                                    <i class="fas fa-file-excel"></i>
                                @elseif(in_array($material->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                    <i class="fas fa-file-image"></i>
                                @elseif(in_array($material->file_type, ['zip', 'rar']))
                                    <i class="fas fa-file-archive"></i>
                                @else
                                    <i class="fas fa-file"></i>
                                @endif
                            </div>
                            <div class="material-info">
                                <div class="material-title">{{ $material->title }}</div>
                                <div class="material-meta">
                                    <span>{{ $material->file_type }}</span> • 
                                    <span>{{ $material->getFormattedFileSizeAttribute() }}</span>
                                </div>
                            </div>
                            <a href="{{ route('courses.materials.download', ['courseId' => $course->course_id, 'materialId' => $material->material_id]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info">
                        No materials available for this course yet.
                    </div>
                @endif
            </div>
            
            <!-- Review Form -->
            <div class="review-form">
                <h3 class="mb-4">Rate This Course</h3>
                
                <form action="{{ route('student.review', $course->course_id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="rating" class="form-label">Your Rating</label>
                        <div class="star-rating">
                            <input type="radio" id="star5" name="rating" value="5" />
                            <label for="star5" title="5 stars"></label>
                            <input type="radio" id="star4" name="rating" value="4" />
                            <label for="star4" title="4 stars"></label>
                            <input type="radio" id="star3" name="rating" value="3" />
                            <label for="star3" title="3 stars"></label>
                            <input type="radio" id="star2" name="rating" value="2" />
                            <label for="star2" title="2 stars"></label>
                            <input type="radio" id="star1" name="rating" value="1" />
                            <label for="star1" title="1 star"></label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="comment" class="form-label">Your Review</label>
                        <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="Share your experience with this course..."></textarea>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_anonymous" name="is_anonymous">
                        <label class="form-check-label" for="is_anonymous">Post anonymously</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="content-sidebar">
                <h4 class="mb-4">Course Content</h4>
                
                <div class="progress mb-3" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 30%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100">30%</div>
                </div>
                
                <p class="text-muted mb-4">3 of 10 lessons completed</p>
                
                <ul class="lesson-list">
                    @if(count($course->videos) > 0)
                        @foreach($course->videos as $index => $video)
                            <li class="lesson-item {{ $index === 0 ? 'active' : '' }}">
                                <div class="lesson-title">
                                    <div>
                                        <i class="fas fa-play-circle lesson-icon"></i>
                                        {{ $video->title }}
                                    </div>
                                    <span class="lesson-duration">{{ gmdate("i:s", $video->duration) }}</span>
                                </div>
                            </li>
                        @endforeach
                    @else
                        <li class="lesson-item">No videos available for this course.</li>
                    @endif
                </ul>
                
                <div class="mt-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h5 class="mb-0">Instructor</h5>
                        </div>
                        <img src="{{ $course->instructor->profile_image ? asset('storage/' . $course->instructor->profile_image) : asset('images/default-profile.png') }}" alt="{{ $course->instructor->name }}" class="rounded-circle" width="40" height="40">
                    </div>
                    <p class="mb-1"><strong>{{ $course->instructor->name }}</strong></p>
                    <p class="text-muted small mb-3">{{ $course->category->name ?? 'Uncategorized' }} Expert</p>
                    <button class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-envelope me-2"></i> Contact Instructor
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/video-cache-buster.js') }}?v={{ time() }}"></script>
@endsection 