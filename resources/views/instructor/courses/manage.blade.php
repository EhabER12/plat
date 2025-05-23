@extends('layouts.instructor')

@section('title', 'Manage Course: ' . $course->title)
@section('page-title', 'Manage Course')

@section('styles')
<style>
    .content-nav .nav-link {
        border-radius: 0;
        font-weight: 500;
    }

    .content-nav .nav-link.active {
        background-color: #20b7b7;
        color: white;
    }

    .video-item, .material-item {
        background-color: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .video-item:hover, .material-item:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .sortable-drag {
        opacity: 0.5;
    }

    .sortable-ghost {
        background-color: #e9ecef;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Course Info Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>{{ $course->title }}</h4>
                <div>
                    <a href="{{ route('course.detail', $course->course_id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> View Course
                    </a>
                    <a href="{{ route('instructor.courses.edit', $course->course_id) }}" class="btn btn-sm btn-outline-secondary ms-2">
                        <i class="fas fa-edit me-1"></i> Edit Details
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <p class="mb-1"><strong>Category:</strong> {{ $course->category->name ?? 'Uncategorized' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1"><strong>Price:</strong> ${{ $course->price }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1">
                        <strong>Status:</strong>
                        @if($course->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @elseif($course->status === 'pending')
                            <span class="badge bg-warning">Pending Approval</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Navigation -->
    <ul class="nav nav-tabs content-nav mb-4">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#sections">
                <i class="fas fa-layer-group me-2"></i> Sections
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#videos">
                <i class="fas fa-video me-2"></i> Videos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#materials">
                <i class="fas fa-file-alt me-2"></i> Materials
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#settings">
                <i class="fas fa-cog me-2"></i> Settings
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Sections Tab -->
        <div class="tab-pane fade show active" id="sections">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Course Sections</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                        <i class="fas fa-plus-circle me-2"></i> Add Section
                    </button>
                </div>
                <div class="card-body">
                    @if(isset($course->sections) && count($course->sections) > 0)
                        <p class="text-muted small mb-4">Drag and drop to reorder sections.</p>
                        <div class="section-list" id="sectionSortable">
                            @foreach($course->sections as $section)
                                <div class="card mb-4 section-item" data-id="{{ $section->section_id }}">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="fas fa-grip-vertical text-muted handle"></i>
                                            </div>
                                            <h6 class="mb-0">{{ $section->title }}</h6>
                                            @if(!$section->is_published)
                                                <span class="badge bg-warning ms-2">Draft</span>
                                            @endif
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary edit-section" 
                                                    data-id="{{ $section->section_id }}"
                                                    data-title="{{ $section->title }}"
                                                    data-description="{{ $section->description }}"
                                                    data-published="{{ $section->is_published }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-section" 
                                                    data-id="{{ $section->section_id }}"
                                                    data-title="{{ $section->title }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if($section->description)
                                            <p class="text-muted">{{ $section->description }}</p>
                                        @endif
                                        
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6><i class="fas fa-video me-2"></i> Videos</h6>
                                                <button type="button" class="btn btn-sm btn-primary add-video-to-section" 
                                                        data-section-id="{{ $section->section_id }}"
                                                        data-section-title="{{ $section->title }}">
                                                    <i class="fas fa-plus-circle me-1"></i> Add Video
                                                </button>
                                            </div>
                                            
                                            @if(count($section->videos) > 0)
                                                <div class="video-list" id="videoSortable-{{ $section->section_id }}">
                                                    @foreach($section->videos as $video)
                                                        <div class="video-item p-3 mb-2" data-id="{{ $video->video_id }}">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-3">
                                                                        <i class="fas fa-grip-vertical text-muted handle"></i>
                                                                    </div>
                                                                    <div>
                                                                        <h6 class="mb-1">{{ $video->title }}</h6>
                                                                        <p class="mb-0 small text-muted">
                                                                            <i class="fas fa-clock me-1"></i> {{ gmdate("i:s", $video->duration_seconds ?? 0) }}
                                                                            @if($video->is_free_preview)
                                                                                <span class="badge bg-success ms-2">Free Preview</span>
                                                                            @endif
                                                                            @if($video->video_url)
                                                                                <span class="badge bg-info ms-2">External Video</span>
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <button class="btn btn-sm btn-outline-primary edit-video" 
                                                                            data-id="{{ $video->video_id }}"
                                                                            data-section-id="{{ $section->section_id }}">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <button class="btn btn-sm btn-outline-danger delete-video" 
                                                                            data-id="{{ $video->video_id }}"
                                                                            data-title="{{ $video->title }}">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-3 bg-light rounded">
                                                    <p class="mb-0 text-muted">No videos in this section.</p>
                                                </div>
                                            @endif
                                            
                                            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                                                <h6><i class="fas fa-file-alt me-2"></i> Materials</h6>
                                                <button type="button" class="btn btn-sm btn-primary add-material-to-section" 
                                                        data-section-id="{{ $section->section_id }}"
                                                        data-section-title="{{ $section->title }}">
                                                    <i class="fas fa-plus-circle me-1"></i> Add Material
                                                </button>
                                            </div>
                                            
                                            @if(count($section->materials) > 0)
                                                <div class="material-list">
                                                    @foreach($section->materials as $material)
                                                        <div class="material-item p-3 mb-2" data-id="{{ $material->material_id }}">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-3">
                                                                        @php
                                                                            $fileType = strtolower($material->file_type ?? 'file');
                                                                            $fileIcon = 'fa-file';

                                                                            if (in_array($fileType, ['pdf'])) {
                                                                                $fileIcon = 'fa-file-pdf text-danger';
                                                                            } elseif (in_array($fileType, ['doc', 'docx'])) {
                                                                                $fileIcon = 'fa-file-word text-primary';
                                                                            } elseif (in_array($fileType, ['xls', 'xlsx'])) {
                                                                                $fileIcon = 'fa-file-excel text-success';
                                                                            } elseif (in_array($fileType, ['ppt', 'pptx'])) {
                                                                                $fileIcon = 'fa-file-powerpoint text-warning';
                                                                            } elseif (in_array($fileType, ['zip', 'rar'])) {
                                                                                $fileIcon = 'fa-file-archive text-secondary';
                                                                            } elseif (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                                                $fileIcon = 'fa-file-image text-info';
                                                                            }
                                                                        @endphp
                                                                        <i class="fas {{ $fileIcon }} fa-2x"></i>
                                                                    </div>
                                                                    <div>
                                                                        <h6 class="mb-1">{{ $material->title }}</h6>
                                                                        <p class="mb-0 small text-muted">
                                                                            <i class="fas fa-download me-1"></i> {{ $material->download_count ?? 0 }} downloads
                                                                            <span class="mx-2">•</span>
                                                                            <span>{{ $material->getFormattedFileSizeAttribute() }}</span>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <a href="{{ route('courses.materials.download', ['courseId' => $course->course_id, 'materialId' => $material->material_id]) }}" 
                                                                       class="btn btn-sm btn-outline-primary">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>
                                                                    <button class="btn btn-sm btn-outline-danger delete-material" 
                                                                            data-id="{{ $material->material_id }}"
                                                                            data-title="{{ $material->title }}">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-3 bg-light rounded">
                                                    <p class="mb-0 text-muted">No materials in this section.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-layer-group fa-3x text-muted"></i>
                            </div>
                            <h5>No sections added yet</h5>
                            <p class="text-muted">Organize your course content by creating sections.</p>
                            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                                <i class="fas fa-plus-circle me-2"></i> Add First Section
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Videos Tab -->
        <div class="tab-pane fade" id="videos">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Course Videos</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVideoModal">
                        <i class="fas fa-plus-circle me-2"></i> Add Video
                    </button>
                </div>
                <div class="card-body">
                    @if(count($course->videos) > 0)
                        <p class="text-muted small mb-4">Drag and drop to reorder videos.</p>
                        <div class="video-list" id="videoSortable">
                            @foreach($course->videos as $video)
                                <div class="video-item p-3" data-id="{{ $video->video_id }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="fas fa-grip-vertical text-muted handle"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $video->title }}</h6>
                                                <p class="mb-0 small text-muted">
                                                    <i class="fas fa-clock me-1"></i> {{ gmdate("i:s", $video->duration) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary edit-video" data-id="{{ $video->video_id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-video" data-id="{{ $video->video_id }}" data-title="{{ $video->title }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-video fa-3x text-muted"></i>
                            </div>
                            <h5>No videos added yet</h5>
                            <p class="text-muted">Add videos to your course to get started.</p>
                            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addVideoModal">
                                <i class="fas fa-plus-circle me-2"></i> Add First Video
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Materials Tab -->
        <div class="tab-pane fade" id="materials">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Course Materials</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                        <i class="fas fa-plus-circle me-2"></i> Add Material
                    </button>
                </div>
                <div class="card-body">
                    @if(count($course->materials) > 0)
                        <div class="material-list">
                            @foreach($course->materials as $material)
                                <div class="material-item p-3" data-id="{{ $material->id }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @php
                                                    $fileType = strtolower($material->file_type ?? 'file');
                                                    $fileIcon = 'fa-file';

                                                    if (in_array($fileType, ['pdf'])) {
                                                        $fileIcon = 'fa-file-pdf text-danger';
                                                    } elseif (in_array($fileType, ['doc', 'docx'])) {
                                                        $fileIcon = 'fa-file-word text-primary';
                                                    } elseif (in_array($fileType, ['xls', 'xlsx'])) {
                                                        $fileIcon = 'fa-file-excel text-success';
                                                    } elseif (in_array($fileType, ['ppt', 'pptx'])) {
                                                        $fileIcon = 'fa-file-powerpoint text-warning';
                                                    } elseif (in_array($fileType, ['zip', 'rar'])) {
                                                        $fileIcon = 'fa-file-archive text-secondary';
                                                    } elseif (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                        $fileIcon = 'fa-file-image text-info';
                                                    }
                                                @endphp
                                                <i class="fas {{ $fileIcon }} fa-2x"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $material->title }}</h6>
                                                <p class="mb-0 small text-muted">
                                                    <i class="fas fa-download me-1"></i> {{ $material->download_count ?? 0 }} downloads
                                                    <span class="mx-2">•</span>
                                                    <span>
                                                        @if($material->file_size)
                                                            @php
                                                                $size = $material->file_size;
                                                                if ($size < 1024) {
                                                                    echo $size . ' B';
                                                                } elseif ($size < 1024 * 1024) {
                                                                    echo round($size / 1024, 1) . ' KB';
                                                                } else {
                                                                    echo round($size / (1024 * 1024), 1) . ' MB';
                                                                }
                                                            @endphp
                                                        @endif
                                                    </span>
                                                </p>
                                                @if($material->description)
                                                    <p class="mt-2 mb-0 text-muted small">{{ $material->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ route('courses.materials.download', ['courseId' => $course->course_id, 'materialId' => $material->material_id]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger delete-material" data-id="{{ $material->material_id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-file-alt fa-3x text-muted"></i>
                            </div>
                            <h5>No materials added yet</h5>
                            <p class="text-muted">Add downloadable resources like PDFs, worksheets, or code examples.</p>
                            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                                <i class="fas fa-plus-circle me-2"></i> Add First Material
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <div class="tab-pane fade" id="settings">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Course Settings</h5>
                </div>
                <div class="card-body">
                    <form action="#" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Visibility</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visibility" id="visibilityPublic" value="public" checked>
                                <label class="form-check-label" for="visibilityPublic">
                                    Public - Show in course listings and search results
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visibility" id="visibilityPrivate" value="private">
                                <label class="form-check-label" for="visibilityPrivate">
                                    Private - Only accessible via direct link
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Course Requirements</label>
                            <textarea class="form-control" rows="3" placeholder="List any prerequisites or requirements for this course"></textarea>
                            <small class="text-muted">Separate each requirement with a new line.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">What Students Will Learn</label>
                            <textarea class="form-control" rows="3" placeholder="List the main learning outcomes"></textarea>
                            <small class="text-muted">Separate each outcome with a new line.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Certificate Options</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enableCertificate" checked>
                                <label class="form-check-label" for="enableCertificate">
                                    Enable course completion certificate
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Video Modal -->
<div class="modal fade" id="addVideoModal" tabindex="-1" aria-labelledby="addVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVideoModalLabel">Add New Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addVideoForm" action="{{ route('instructor.courses.videos.store', $course->course_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Section Selection -->
                    <div class="mb-3">
                        <label for="sectionId" class="form-label">Section</label>
                        <select class="form-select" id="sectionId" name="section_id">
                            <option value="">-- No Section --</option>
                            @foreach($course->sections as $section)
                                <option value="{{ $section->section_id }}">{{ $section->title }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Optional: Choose a section to organize your videos. You can also create sections first from the Sections tab.</small>
                    </div>
                    
                    <div id="upload-progress-container" class="mb-3 d-none">
                        <label class="form-label">Uploading... <span id="upload-percentage">0%</span></label>
                        <div class="progress">
                            <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <div id="upload-success" class="alert alert-success d-none">
                        Video uploaded successfully!
                    </div>
                    
                    <div id="upload-error" class="alert alert-danger d-none">
                        An error occurred while uploading the video. Please try again.
                    </div>

                    <div class="mb-3">
                        <label for="videoTitle" class="form-label">Video Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="videoTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="videoDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="videoDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Video Source <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="videoTypeUpload" value="upload" checked>
                            <label class="form-check-label" for="videoTypeUpload">
                                Upload Video File
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="video_type" id="videoTypeExternal" value="external">
                            <label class="form-check-label" for="videoTypeExternal">
                                External URL (YouTube, Vimeo, etc.)
                            </label>
                        </div>
                    </div>

                    <div id="uploadVideoSection" class="mb-3">
                        <label for="videoFile" class="form-label">Video File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="videoFile" name="video_file" accept="video/*">
                        <small class="text-muted">Max file size: 500MB. Recommended format: MP4.</small>
                    </div>

                    <div id="externalVideoSection" class="mb-3 d-none">
                        <label for="videoUrl" class="form-label">Video URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="videoUrl" name="video_url" placeholder="e.g. https://youtube.com/watch?v=XXXXXXXXXXX">
                        <small class="text-muted">Enter YouTube, Vimeo, or other video platform URL. Example: https://www.youtube.com/watch?v=XXXXXXXXXXX</small>
                    </div>

                    <div class="mb-3">
                        <label for="videoDuration" class="form-label">Duration (seconds)</label>
                        <input type="number" class="form-control" id="videoDuration" name="duration_seconds" min="0">
                        <small class="text-muted">Leave empty to auto-detect for uploaded videos</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isFreePreview" name="is_free_preview" value="1">
                            <label class="form-check-label" for="isFreePreview">
                                Free Preview (available to non-enrolled students)
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="videoThumbnail" class="form-label">Thumbnail Image</label>
                        <input type="file" class="form-control" id="videoThumbnail" name="thumbnail" accept="image/*">
                        <small class="text-muted">Max file size: 2MB. Recommended dimensions: 1280×720px.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelUploadBtn">Cancel</button>
                <button type="button" id="uploadVideoBtn" class="btn btn-primary">Add Video</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // متغيرات للتحكم في تحميل الفيديو
        const addVideoForm = document.getElementById('addVideoForm');
        const uploadVideoBtn = document.getElementById('uploadVideoBtn');
        const cancelUploadBtn = document.getElementById('cancelUploadBtn');
        const uploadProgressContainer = document.getElementById('upload-progress-container');
        const uploadProgressBar = document.getElementById('upload-progress-bar');
        const uploadPercentage = document.getElementById('upload-percentage');
        const uploadSuccess = document.getElementById('upload-success');
        const uploadError = document.getElementById('upload-error');
        let xhr; // لتخزين طلب XMLHttpRequest
        let uploadStartTime; // لتتبع وقت بدء التحميل
        let uploadSize = 0; // لتخزين حجم الملف
        let uploadTimer; // لتخزين مؤقت تحديث الشريط

        // معالجة نقر زر التحميل
        if (uploadVideoBtn) {
            uploadVideoBtn.addEventListener('click', function() {
                // التحقق من نوع الفيديو وتطبيق التحقق المناسب
                const videoType = document.querySelector('input[name="video_type"]:checked').value;

                if (videoType === 'upload') {
                    // تأكد من أن حقل URL غير مطلوب عند تحميل ملف
                    videoUrlInput.removeAttribute('required');
                    videoFileInput.setAttribute('required', '');
                    
                    // التحقق من وجود ملف فيديو
                    if (!videoFileInput.files || videoFileInput.files.length === 0) {
                        uploadError.textContent = 'Please select a video file to upload.';
                        uploadError.classList.remove('d-none');
                        return;
                    }
                    
                    // التحقق من حجم الملف
                    const maxFileSize = 200 * 1024 * 1024; // 200MB
                    uploadSize = videoFileInput.files[0].size;
                    
                    if (uploadSize === 0) {
                        uploadError.textContent = 'The selected file appears to be empty (0 bytes). Please select a valid video file.';
                        uploadError.classList.remove('d-none');
                        return;
                    }
                    
                    if (uploadSize > maxFileSize) {
                        uploadError.textContent = 'The selected file exceeds the maximum allowed size (200MB).';
                        uploadError.classList.remove('d-none');
                        return;
                    }
                    
                    // عرض معلومات الملف للمستخدم
                    uploadError.classList.add('d-none');
                    uploadSuccess.textContent = `File size: ${formatFileSize(uploadSize)}. Starting upload...`;
                    uploadSuccess.classList.remove('d-none');
                    
                    // تحقق من نوع الملف
                    const acceptedTypes = ['video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo'];
                    const fileType = videoFileInput.files[0].type;
                    
                    if (!acceptedTypes.includes(fileType)) {
                        uploadError.textContent = `File type ${fileType} may not be supported. Please use MP4, WebM, or MOV format.`;
                        uploadError.classList.remove('d-none');
                        // لا نتوقف، فقط نحذر المستخدم
                    }
                } else {
                    // تأكد من أن حقل الملف غير مطلوب عند استخدام URL
                    videoFileInput.removeAttribute('required');
                    videoUrlInput.setAttribute('required', '');
                }

                // التحقق من صحة النموذج
                if (!addVideoForm.checkValidity()) {
                    addVideoForm.reportValidity();
                    return;
                }

                // إعادة تعيين شريط التقدم
                uploadProgressBar.style.width = '0%';
                uploadProgressBar.setAttribute('aria-valuenow', 0);
                uploadPercentage.textContent = '0%';

                // إظهار شريط التقدم
                uploadProgressContainer.classList.remove('d-none');
                
                // تسجيل وقت بدء التحميل
                uploadStartTime = new Date();

                // إنشاء كائن FormData لإرسال البيانات
                const formData = new FormData(addVideoForm);

                // إنشاء طلب AJAX
                xhr = new XMLHttpRequest();

                // مراقبة تقدم التحميل
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const now = new Date();
                        const elapsedTime = (now - uploadStartTime) / 1000; // بالثواني
                        const bytesUploaded = e.loaded;
                        const bytesTotal = e.total;
                        const percentComplete = Math.round((bytesUploaded / bytesTotal) * 100);
                        
                        // حساب السرعة (ميجابايت/ثانية)
                        const uploadSpeed = bytesUploaded / elapsedTime / (1024 * 1024);
                        const remainingBytes = bytesTotal - bytesUploaded;
                        // تقدير الوقت المتبقي (بالثواني)
                        const estimatedTimeRemaining = uploadSpeed > 0 ? remainingBytes / uploadSpeed / (1024 * 1024) : 0;
                        
                        // تحديث شريط التقدم
                        uploadProgressBar.style.width = percentComplete + '%';
                        uploadProgressBar.setAttribute('aria-valuenow', percentComplete);
                        
                        // تحديث النص
                        uploadPercentage.textContent = `${percentComplete}% - ${formatFileSize(bytesUploaded)} of ${formatFileSize(bytesTotal)} at ${uploadSpeed.toFixed(2)} MB/s - ${formatTime(estimatedTimeRemaining)} remaining`;
                        
                        // للملفات الكبيرة، إذا كان التقدم سريعًا جدًا، فقد يكون هناك خطأ
                        if (bytesTotal > 10 * 1024 * 1024 && percentComplete > 50 && elapsedTime < 2) {
                            uploadError.textContent = 'Warning: Upload speed is suspiciously fast. The upload may not be processing the complete file data.';
                            uploadError.classList.remove('d-none');
                        }
                    }
                });

                // معالجة الاستجابة
                xhr.onload = function() {
                    console.log('Response status:', xhr.status);
                    console.log('Response text:', xhr.responseText);
                    
                    // حساب إجمالي وقت التحميل
                    const now = new Date();
                    const totalUploadTime = (now - uploadStartTime) / 1000; // بالثواني
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        console.log('Parsed response:', response);

                        if (xhr.status === 200 || xhr.status === 201) {
                            if (response.success) {
                                // نجاح التحميل
                                uploadSuccess.textContent = `Upload successful! Total time: ${formatTime(totalUploadTime)} for ${formatFileSize(uploadSize)}.`;
                                uploadSuccess.classList.remove('d-none');
                                uploadProgressContainer.classList.add('d-none');

                                // إعادة تحميل الصفحة بعد ثانيتين
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                // استجابة ناجحة ولكن مع رسالة خطأ
                                uploadError.textContent = response.message || 'An error occurred while uploading the video. Please try again.';
                                uploadError.classList.remove('d-none');
                                uploadProgressContainer.classList.add('d-none');
                            }
                        } else if (xhr.status === 422) {
                            // خطأ في التحقق من صحة البيانات
                            let errorMessage = 'Validation error: ';
                            if (response.errors) {
                                // جمع جميع رسائل الخطأ
                                const errorMessages = [];
                                for (const field in response.errors) {
                                    errorMessages.push(response.errors[field].join(', '));
                                }
                                errorMessage += errorMessages.join('; ');
                            } else if (response.message) {
                                errorMessage = response.message;
                            }
                            uploadError.textContent = errorMessage;
                            uploadError.classList.remove('d-none');
                            uploadProgressContainer.classList.add('d-none');
                        } else {
                            // فشل التحميل لسبب آخر
                            uploadError.textContent = response.message || 'An error occurred while uploading the video. Please try again.';
                            uploadError.classList.remove('d-none');
                            uploadProgressContainer.classList.add('d-none');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        uploadError.textContent = 'An error occurred while processing the server response.';
                        uploadError.classList.remove('d-none');
                        uploadProgressContainer.classList.add('d-none');
                    }
                };

                // معالجة الأخطاء
                xhr.onerror = function(e) {
                    console.error('XHR Error:', e);
                    uploadError.textContent = 'Network error occurred. Please check your connection and try again.';
                    uploadError.classList.remove('d-none');
                    uploadProgressContainer.classList.add('d-none');
                };

                // إرسال الطلب
                xhr.open('POST', addVideoForm.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.send(formData);
            });
        }

        // إلغاء التحميل
        if (cancelUploadBtn) {
            cancelUploadBtn.addEventListener('click', function() {
                if (xhr && xhr.readyState !== 4) {
                    xhr.abort();
                    uploadError.textContent = 'Upload cancelled by user.';
                    uploadError.classList.remove('d-none');
                    uploadProgressContainer.classList.add('d-none');
                }
            });
        }

        // Toggle video input sections based on selected type
        const videoTypeUpload = document.getElementById('videoTypeUpload');
        const videoTypeExternal = document.getElementById('videoTypeExternal');
        const uploadSection = document.getElementById('uploadVideoSection');
        const externalSection = document.getElementById('externalVideoSection');
        const videoFileInput = document.getElementById('videoFile');
        const videoUrlInput = document.getElementById('videoUrl');

        function toggleVideoSections() {
            if (videoTypeUpload.checked) {
                uploadSection.classList.remove('d-none');
                externalSection.classList.add('d-none');
                videoFileInput.setAttribute('required', '');
                videoUrlInput.removeAttribute('required');
                // Clear the URL field to avoid validation errors
                videoUrlInput.value = '';
            } else {
                uploadSection.classList.add('d-none');
                externalSection.classList.remove('d-none');
                videoFileInput.removeAttribute('required');
                videoUrlInput.setAttribute('required', '');
                // Clear the file input to avoid unnecessary uploads
                videoFileInput.value = '';
            }
        }

        videoTypeUpload.addEventListener('change', toggleVideoSections);
        videoTypeExternal.addEventListener('change', toggleVideoSections);

        // Initialize on page load
        toggleVideoSections();

        // تنسيق حجم الملف بشكل مقروء
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // تنسيق الوقت بشكل مقروء
        function formatTime(seconds) {
            if (seconds < 60) {
                return Math.round(seconds) + ' seconds';
            } else if (seconds < 3600) {
                return Math.floor(seconds / 60) + ' minutes ' + Math.round(seconds % 60) + ' seconds';
            } else {
                return Math.floor(seconds / 3600) + ' hours ' + 
                       Math.floor((seconds % 3600) / 60) + ' minutes';
            }
        }
    });
</script>

<!-- Add Material Modal -->
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaterialModalLabel">Add Course Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMaterialForm" action="{{ route('instructor.courses.materials.store', $course->course_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="materialTitle" class="form-label">Material Title</label>
                        <input type="text" class="form-control" id="materialTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="materialDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="materialDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="materialSection" class="form-label">Section</label>
                        <select class="form-select" id="materialSection" name="section_id">
                            <option value="">Select a section (optional)</option>
                            @foreach($course->sections as $section)
                                <option value="{{ $section->section_id }}">{{ $section->title }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Choose a section to organize your materials.</small>
                    </div>
                    <div class="mb-3">
                        <label for="materialFile" class="form-label">File</label>
                        <input type="file" class="form-control" id="materialFile" name="material_file" required>
                        <small class="text-muted">Max file size: 50MB. Supported formats: PDF, ZIP, DOC, XLS, PPT.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addMaterialForm" class="btn btn-primary">Add Material</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSectionModalLabel">Add New Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('instructor.courses.sections.store', $course->course_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="section_title" class="form-label">Section Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="section_title" name="title" placeholder="e.g. Introduction to the Course" required>
                    </div>
                    <div class="mb-3">
                        <label for="section_description" class="form-label">Description</label>
                        <textarea class="form-control" id="section_description" name="description" rows="3" placeholder="Brief description of this section..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" aria-labelledby="editSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSectionModalLabel">Edit Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSectionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_section_title" class="form-label">Section Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_section_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_section_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_section_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="edit_section_published" name="is_published" value="1">
                        <label class="form-check-label" for="edit_section_published">
                            Published (visible to students)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Section Confirmation Modal -->
<div class="modal fade" id="deleteSectionModal" tabindex="-1" aria-labelledby="deleteSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSectionModalLabel">Delete Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the section "<span id="delete_section_title"></span>"?</p>
                <p class="text-danger">This will also remove all videos and materials assigned to this section. This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteSectionForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Section</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize sortable sections
        const sectionSortable = document.getElementById('sectionSortable');
        if (sectionSortable) {
            new Sortable(sectionSortable, {
                handle: '.handle',
                animation: 150,
                onEnd: function(evt) {
                    updateSectionPositions();
                }
            });
        }

        // Initialize sortable videos in each section
        document.querySelectorAll('[id^="videoSortable-"]').forEach(function(el) {
            new Sortable(el, {
                handle: '.handle',
                animation: 150,
                onEnd: function(evt) {
                    const sectionId = el.id.split('-')[1];
                    updateVideoPositions(sectionId);
                }
            });
        });

        // Section add/edit/delete handlers
        const editSectionButtons = document.querySelectorAll('.edit-section');
        const deleteSectionButtons = document.querySelectorAll('.delete-section');
        const editSectionForm = document.getElementById('editSectionForm');
        const deleteSectionForm = document.getElementById('deleteSectionForm');
        const deleteSectionTitle = document.getElementById('delete_section_title');

        // Edit section button handler
        editSectionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const sectionId = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const description = this.getAttribute('data-description') || '';
                const published = this.getAttribute('data-published') === '1';

                document.getElementById('edit_section_title').value = title;
                document.getElementById('edit_section_description').value = description;
                document.getElementById('edit_section_published').checked = published;

                // Set the form action URL
                editSectionForm.action = `/instructor/courses/{{ $course->course_id }}/sections/${sectionId}`;

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('editSectionModal'));
                modal.show();
            });
        });

        // Delete section button handler
        deleteSectionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const sectionId = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');

                deleteSectionTitle.textContent = title;
                deleteSectionForm.action = `/instructor/courses/{{ $course->course_id }}/sections/${sectionId}`;

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('deleteSectionModal'));
                modal.show();
            });
        });

        // Delete video button handler
        const deleteVideoButtons = document.querySelectorAll('.delete-video');
        if (deleteVideoButtons.length > 0) {
            deleteVideoButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const videoId = this.getAttribute('data-id');
                    const videoTitle = this.getAttribute('data-title');

                    if (confirm(`هل أنت متأكد من حذف الفيديو "${videoTitle}"؟ لا يمكن التراجع عن هذا الإجراء.`)) {
                        // إنشاء نموذج POST بدلاً من استخدام fetch
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ route('instructor.courses.videos.delete', ['courseId' => $course->course_id, 'videoId' => ':videoId']) }}".replace(':videoId', videoId);
                        form.style.display = 'none';

                        // إضافة CSRF token
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        // إضافة النموذج للصفحة وإرساله
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        }

        // Add video to section button handler
        const addVideoToSectionButtons = document.querySelectorAll('.add-video-to-section');
        const sectionIdSelect = document.getElementById('sectionId');
        
        addVideoToSectionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const sectionId = this.getAttribute('data-section-id');
                
                // Set the selected section in the dropdown
                if (sectionIdSelect) {
                    sectionIdSelect.value = sectionId;
                }
                
                // Show the video modal
                const modal = new bootstrap.Modal(document.getElementById('addVideoModal'));
                modal.show();
            });
        });

        // Add material to section button handler
        const addMaterialToSectionButtons = document.querySelectorAll('.add-material-to-section');
        const materialSectionSelect = document.getElementById('materialSection');
        
        addMaterialToSectionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const sectionId = this.getAttribute('data-section-id');
                
                // Set the selected section in the dropdown
                if (materialSectionSelect) {
                    materialSectionSelect.value = sectionId;
                }
                
                // Show the material modal
                const modal = new bootstrap.Modal(document.getElementById('addMaterialModal'));
                modal.show();
            });
        });

        // Function to update section positions
        function updateSectionPositions() {
            const sections = document.querySelectorAll('.section-item');
            const positions = [];

            sections.forEach((section, index) => {
                positions.push({
                    section_id: section.getAttribute('data-id'),
                    position: index
                });
            });

            fetch(`/instructor/courses/{{ $course->course_id }}/sections/positions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ positions: positions })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Positions updated:', data);
            })
            .catch(error => console.error('Error updating positions:', error));
        }

        // Function to update video positions within a section
        function updateVideoPositions(sectionId) {
            const videos = document.querySelectorAll(`#videoSortable-${sectionId} .video-item`);
            const positions = [];

            videos.forEach((video, index) => {
                positions.push({
                    video_id: video.getAttribute('data-id'),
                    position: index
                });
            });

            // Update video positions via AJAX
            fetch(`/instructor/courses/{{ $course->course_id }}/videos/positions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ positions: positions })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Video positions updated:', data);
            })
            .catch(error => console.error('Error updating video positions:', error));
        }
    });
</script>
@endsection