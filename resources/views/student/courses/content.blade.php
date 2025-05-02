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

    .video-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
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

    .video-loading {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(0,0,0,0.7);
        color: white;
        z-index: 10;
    }

    .video-error {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(0,0,0,0.7);
        color: white;
        z-index: 10;
        padding: 20px;
        text-align: center;
    }
    
    /* Section styles */
    .section-title {
        font-size: 1rem;
        font-weight: 600;
        padding: 10px 15px;
        background-color: #e9f7f7;
        border-radius: 5px;
        margin-bottom: 10px;
        color: #20b7b7;
        border-left: 3px solid #20b7b7;
    }
    
    .section-videos {
        margin-bottom: 20px;
    }
</style>
@endsection

@section('scripts')
<!-- Include Plyr.io for better video player -->
<script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

<!-- Include HLS.js for M3U8 streaming support in browsers that don't support it natively -->
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize variables
        const courseId = {{ $course->course_id }};
        let currentVideoId = null;
        let currentVideoIndex = 0;
        let player = null;
        const videoContainer = document.getElementById('video-container');
        const videoPlayer = document.getElementById('video-player');
        const videoTitle = document.getElementById('current-video-title');
        const prevButton = document.getElementById('prev-video-btn');
        const nextButton = document.getElementById('next-video-btn');
        const lessonItems = document.querySelectorAll('.lesson-item');
        const videos = [];

        // Collect video data
        lessonItems.forEach(item => {
            if (item.dataset.videoId) {
                videos.push({
                    id: parseInt(item.dataset.videoId),
                    title: item.dataset.videoTitle,
                    description: item.dataset.videoDescription,
                    index: parseInt(item.dataset.videoIndex)
                });
            }
        });

        // Set up click handlers for lesson items
        lessonItems.forEach(item => {
            item.addEventListener('click', function() {
                const videoId = parseInt(this.dataset.videoId);
                const videoIndex = parseInt(this.dataset.videoIndex);

                if (videoId && videoId !== currentVideoId) {
                    loadVideo(videoId, videoIndex);
                }
            });
        });

        // Set up navigation buttons
        prevButton.addEventListener('click', function() {
            if (currentVideoIndex > 0) {
                const prevVideo = videos[currentVideoIndex - 1];
                loadVideo(prevVideo.id, prevVideo.index);
            }
        });

        nextButton.addEventListener('click', function() {
            if (currentVideoIndex < videos.length - 1) {
                const nextVideo = videos[currentVideoIndex + 1];
                loadVideo(nextVideo.id, nextVideo.index);
            }
        });

        // Function to load video
        function loadVideo(videoId, videoIndex) {
            // Update UI
            currentVideoId = videoId;
            currentVideoIndex = videoIndex;

            // Update active class
            lessonItems.forEach(item => item.classList.remove('active'));
            lessonItems[videoIndex].classList.add('active');

            // Update navigation buttons
            prevButton.disabled = (videoIndex === 0);
            nextButton.disabled = (videoIndex === videos.length - 1);

            // Update video title
            videoTitle.textContent = videos[videoIndex].title;

            // Show loading indicator
            videoPlayer.innerHTML = `
                <div class="video-loading">
                    <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            // First get video info to check if it's an external video
            fetch(`/api/videos/${videoId}/info`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to get video info');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Video info:', data);
                    
                    // If it's an external video (YouTube, Vimeo, etc.)
                    if (data.success && data.videoInfo && data.videoInfo.is_external && data.videoInfo.video_url) {
                        const videoUrl = data.videoInfo.video_url;
                        
                        // Handle YouTube videos
                        if (videoUrl.includes('youtube.com/') || videoUrl.includes('youtu.be/')) {
                            const youtubeId = extractYouTubeId(videoUrl);
                            if (youtubeId) {
                                videoPlayer.innerHTML = `
                                    <iframe src="https://www.youtube.com/embed/${youtubeId}" 
                                            width="100%" 
                                            height="100%" 
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                            allowfullscreen></iframe>
                                `;
                                return; // Exit early
                            }
                        }
                        
                        // Handle Vimeo videos
                        if (videoUrl.includes('vimeo.com/')) {
                            const match = videoUrl.match(/vimeo\.com\/(\d+)/);
                            if (match && match[1]) {
                                const vimeoId = match[1];
                                videoPlayer.innerHTML = `
                                    <iframe src="https://player.vimeo.com/video/${vimeoId}" 
                                            width="100%" 
                                            height="100%" 
                                            frameborder="0" 
                                            allow="autoplay; fullscreen; picture-in-picture" 
                                            allowfullscreen></iframe>
                                `;
                                return; // Exit early
                            }
                        }
                        
                        // For other external URLs, try to embed directly
                        videoPlayer.innerHTML = `
                            <iframe src="${videoUrl}" 
                                    width="100%" 
                                    height="100%" 
                                    frameborder="0" 
                                    allowfullscreen></iframe>
                        `;
                        return; // Exit early
                    }
                    
                    // For regular videos, continue with token generation and streaming
                    return getVideoToken(videoId);
                })
                .catch(error => {
                    console.error('Error getting video info:', error);
                    // Fall back to token generation
                    return getVideoToken(videoId);
                });
        }
        
        // Function to get video token and stream the video
        function getVideoToken(videoId) {
            // Get video access token
            return fetch(`/video/token/${courseId}/${videoId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to get video access token');
                    }
                    return response.json();
                })
                .then(data => {
                    const token = data.token;

                    // Create video element
                    videoPlayer.innerHTML = '';

                    // Check if the video is encrypted (segmented)
                    fetch(`/api/videos/${videoId}/info`)
                        .then(response => {
                            if (!response.ok) {
                                // Try loading the video directly if we can't get info
                                return { is_encrypted: false };
                            }
                            return response.json();
                        })
                        .then(videoInfo => {
                            // Create video element
                            const video = document.createElement('video');
                            video.controls = true;
                            video.crossOrigin = 'anonymous';
                            video.className = 'plyr__video-embed';
                            video.style.width = '100%';
                            video.style.height = 'auto';
                            video.playsInline = true;

                            // Add event listeners for error handling
                            video.addEventListener('error', function(e) {
                                console.error('Video error:', e);
                                const errorCode = video.error ? video.error.code : 'unknown';
                                const errorMessage = getVideoErrorMessage(errorCode);
                                
                                videoPlayer.innerHTML = `
                                    <div class="video-error">
                                        <div>
                                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                            <h5>Video Playback Error</h5>
                                            <p>${errorMessage}</p>
                                            <button class="btn btn-sm btn-light mt-3" onclick="loadVideo(${videoId}, ${videoIndex})">
                                                <i class="fas fa-redo"></i> Try Again
                                            </button>
                                        </div>
                                    </div>
                                `;
                            });

                            if (videoInfo.is_encrypted) {
                                // For encrypted videos, we need to handle segments
                                const totalSegments = videoInfo.segments || 1;
                                let currentSegment = 0;

                                // Create source element for the first segment
                                const source = document.createElement('source');
                                source.src = `/video/stream/${token}?segment=0`;
                                source.type = 'video/mp4';

                                // Add source to video
                                video.appendChild(source);

                                // Add video to player
                                videoPlayer.appendChild(video);

                                // Handle segment switching
                            } else {
                                // Check if this is an HLS stream
                                const videoUrl = `/video/stream/${token}`;
                                const isHLS = videoUrl.toLowerCase().endsWith('.m3u8');
                                
                                if (isHLS) {
                                    // Set up HLS.js for browsers that don't support HLS natively
                                    if (Hls.isSupported()) {
                                        const hls = new Hls({
                                            xhrSetup: function(xhr, url) {
                                                // Add CORS headers if needed
                                                xhr.withCredentials = false;
                                            }
                                        });
                                        
                                        // Add error handling for HLS
                                        hls.on(Hls.Events.ERROR, function(event, data) {
                                            console.error('HLS error:', data);
                                            
                                            if (data.fatal) {
                                                switch(data.type) {
                                                    case Hls.ErrorTypes.NETWORK_ERROR:
                                                        // Try to recover network error
                                                        console.log('Fatal network error encountered, try to recover');
                                                        hls.startLoad();
                                                        break;
                                                    case Hls.ErrorTypes.MEDIA_ERROR:
                                                        console.log('Fatal media error encountered, try to recover');
                                                        hls.recoverMediaError();
                                                        break;
                                                    default:
                                                        // Cannot recover
                                                        hls.destroy();
                                                        videoPlayer.innerHTML = `
                                                            <div class="video-error">
                                                                <div>
                                                                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                                                    <h5>HLS Streaming Error</h5>
                                                                    <p>There was a problem loading the video stream. Error type: ${data.type}.</p>
                                                                    <button class="btn btn-sm btn-light mt-3" onclick="loadVideo(${videoId}, ${videoIndex})">
                                                                        <i class="fas fa-redo"></i> Try Again
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        `;
                                                        break;
                                                }
                                            }
                                        });
                                        
                                        hls.loadSource(videoUrl);
                                        hls.attachMedia(video);
                                        
                                        // Handle HLS manifest loaded event
                                        hls.on(Hls.Events.MANIFEST_PARSED, function() {
                                            video.play()
                                                .catch(e => console.warn('Autoplay prevented:', e));
                                        });
                                    }
                                    // For browsers with native HLS support like Safari
                                    else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                                        video.src = videoUrl;
                                    }
                                    // For browsers with neither HLS.js support nor native support
                                    else {
                                        videoPlayer.innerHTML = `
                                            <div class="video-error">
                                                <div>
                                                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                                    <h5>HLS Not Supported</h5>
                                                    <p>Your browser does not support HLS streaming and the compatibility library failed to load.</p>
                                                </div>
                                            </div>
                                        `;
                                        return;
                                    }
                                } else {
                                    // For regular videos, just use the token
                                    const source = document.createElement('source');
                                    source.src = videoUrl;
                                    source.type = 'video/mp4';
                                    source.onerror = function() {
                                        console.error('Source error');
                                    };

                                    // Add source to video
                                    video.appendChild(source);
                                }

                                // Add video to player
                                videoPlayer.appendChild(video);
                                
                                // Initialize Plyr player for better UI
                                if (window.Plyr) {
                                    new Plyr(video, {
                                        controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
                                        hideControls: false,
                                        autoplay: false
                                    });
                                }
                            }
                            
                            // Record video access
                            recordVideoView(videoId);
                        })
                        .catch(error => {
                            console.error('Error getting video info:', error);

                            // Show error message
                                videoPlayer.innerHTML = `
                                    <div class="video-error">
                                        <div>
                                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                            <h5>Video Loading Error</h5>
                                            <p>There was a problem loading this video. Please try again later.</p>
                                            <button class="btn btn-sm btn-light mt-3" onclick="loadVideo(${videoId}, ${videoIndex})">
                                                <i class="fas fa-redo"></i> Try Again
                                            </button>
                                        </div>
                                    </div>
                                `;
                        });
                })
                .catch(error => {
                    console.error('Error getting video token:', error);
                    
                    videoPlayer.innerHTML = `
                        <div class="video-error">
                            <div>
                                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                <h5>Authentication Error</h5>
                                <p>There was a problem authenticating your video access. Please try again later.</p>
                                <button class="btn btn-sm btn-light mt-3" onclick="loadVideo(${videoId}, ${videoIndex})">
                                    <i class="fas fa-redo"></i> Try Again
                                </button>
                            </div>
                        </div>
                    `;
                });
        }

        // Function to extract YouTube ID from URL
        function extractYouTubeId(url) {
            const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
            const match = url.match(regExp);
            return (match && match[7].length === 11) ? match[7] : null;
        }

        // Function to record video view
        function recordVideoView(videoId) {
            fetch(`/api/videos/${videoId}/view`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    console.warn('Failed to record video view');
                }
                return response.json();
            })
            .then(data => {
                console.log('Video view recorded:', data);
            })
            .catch(error => {
                console.error('Error updating progress:', error);
            });
        }

        // Function to get error message based on error code
        function getVideoErrorMessage(errorCode) {
            switch (errorCode) {
                case 1:
                    return "The video loading was aborted.";
                case 2:
                    return "Network error occurred while loading the video.";
                case 3:
                    return "There was a problem decoding the video. The file may be corrupted or use an unsupported format.";
                case 4:
                    return "The video is not supported by your browser.";
                default:
                    return "An unknown error occurred during video playback.";
            }
        }

        // Check if user is blocked before loading video
        function checkIfBlocked(videoId) {
            return fetch(`/api/videos/${videoId}/check-access`)
                .then(response => response.json())
                .catch(() => ({ blocked: false }));
        }

        // Load the first video if available
        if (videos.length > 0) {
            const firstVideoId = videos[0].id;

            // Check if user is blocked for this video
            checkIfBlocked(firstVideoId)
                .then(data => {
                    if (!data.blocked) {
                        // Load the first video if not blocked
                        loadVideo(firstVideoId, 0);
                    }
                });
        }
    });
</script>
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

            <div class="mb-4" id="video-container">
                <div class="video-wrapper">
                    <!-- Video player will be loaded here -->
                    <div id="video-player" class="w-100 h-100"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 id="current-video-title">{{ $course->videos->first()->title ?? 'No video available' }}</h3>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary" id="prev-video-btn" {{ $course->videos->count() <= 1 ? 'disabled' : '' }}>
                            <i class="fas fa-step-backward"></i> Previous
                        </button>
                        <button class="btn btn-sm btn-outline-primary" id="next-video-btn" {{ $course->videos->count() <= 1 ? 'disabled' : '' }}>
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

                <!-- Organize videos by sections -->
                @if($course->sections && $course->sections->count() > 0)
                    @php
                        $videoIndex = 0;
                    @endphp
                    
                    @foreach($course->sections as $section)
                        <div class="section-container mb-3">
                            <div class="section-title">
                                <i class="fas fa-layer-group me-2"></i> {{ $section->title }}
                            </div>
                            
                            @if($section->videos && $section->videos->count() > 0)
                                <ul class="lesson-list section-videos">
                                    @foreach($section->videos as $video)
                                        <li class="lesson-item {{ $videoIndex === 0 ? 'active' : '' }}"
                                            data-video-id="{{ $video->video_id }}"
                                            data-video-title="{{ $video->title }}"
                                            data-video-description="{{ $video->description ?? '' }}"
                                            data-video-index="{{ $videoIndex }}">
                                            <div class="lesson-title">
                                                <div>
                                                    <i class="fas fa-play-circle lesson-icon"></i>
                                                    {{ $video->title }}
                                                    @if($video->is_free_preview)
                                                        <span class="badge bg-success ms-2">Free Preview</span>
                                                    @endif
                                                </div>
                                                <span class="lesson-duration">{{ gmdate("i:s", $video->duration_seconds ?? $video->duration ?? 0) }}</span>
                                            </div>
                                        </li>
                                        @php
                                            $videoIndex++;
                                        @endphp
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center py-2 bg-light rounded mb-3">
                                    <p class="mb-0 text-muted small">No videos in this section.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <!-- If no sections are defined, show videos without sections -->
                    <ul class="lesson-list">
                        @if(count($course->videos) > 0)
                            @foreach($course->videos as $index => $video)
                                <li class="lesson-item {{ $index === 0 ? 'active' : '' }}"
                                    data-video-id="{{ $video->video_id }}"
                                    data-video-title="{{ $video->title }}"
                                    data-video-description="{{ $video->description }}"
                                    data-video-index="{{ $index }}">
                                    <div class="lesson-title">
                                        <div>
                                            <i class="fas fa-play-circle lesson-icon"></i>
                                            {{ $video->title }}
                                            @if($video->is_free_preview)
                                                <span class="badge bg-success ms-2">Free Preview</span>
                                            @endif
                                        </div>
                                        <span class="lesson-duration">{{ gmdate("i:s", $video->duration_seconds ?? $video->duration ?? 0) }}</span>
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <li class="lesson-item">No videos available for this course.</li>
                        @endif
                    </ul>
                @endif

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
