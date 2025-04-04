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
                    <a href="{{ route('course.detail', $course->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> View Course
                    </a>
                    <a href="{{ route('instructor.courses.edit', $course->id) }}" class="btn btn-sm btn-outline-secondary ms-2">
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
            <a class="nav-link active" data-bs-toggle="tab" href="#videos">
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
        <!-- Videos Tab -->
        <div class="tab-pane fade show active" id="videos">
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
                                <div class="video-item p-3" data-id="{{ $video->id }}">
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
                                            <button class="btn btn-sm btn-outline-primary edit-video" data-id="{{ $video->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger delete-video" data-id="{{ $video->id }}">
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
                                            <a href="{{ route('courses.materials.download', ['courseId' => $course->id, 'materialId' => $material->id]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger delete-material" data-id="{{ $material->id }}">
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVideoModalLabel">Add New Video</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addVideoForm" action="{{ route('instructor.courses.videos.store', $course->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="videoTitle" class="form-label">Video Title</label>
                        <input type="text" class="form-control" id="videoTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="videoDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="videoDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="videoFile" class="form-label">Video File</label>
                        <input type="file" class="form-control" id="videoFile" name="video_file" accept="video/*" required>
                        <small class="text-muted">Max file size: 200MB. Supported formats: MP4, MOV, AVI.</small>
                    </div>
                    <div class="mb-3">
                        <label for="videoPosition" class="form-label">Position</label>
                        <input type="number" class="form-control" id="videoPosition" name="position" min="1" value="{{ count($course->videos) + 1 }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addVideoForm" class="btn btn-primary">Add Video</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Material Modal -->
<div class="modal fade" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaterialModalLabel">Add Course Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMaterialForm" action="{{ route('instructor.courses.materials.store', $course->id) }}" method="POST" enctype="multipart/form-data">
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize sortable for videos
        if (document.getElementById('videoSortable')) {
            new Sortable(document.getElementById('videoSortable'), {
                handle: '.handle',
                animation: 150,
                onEnd: function(evt) {
                    // Update video positions when dragging ends
                    const items = evt.to.children;
                    const positions = [];
                    
                    for (let i = 0; i < items.length; i++) {
                        positions.push({
                            id: items[i].getAttribute('data-id'),
                            position: i + 1
                        });
                    }
                    
                    // Send position update to server via AJAX
                    fetch('{{ route('instructor.courses.videos.positions', $course->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ positions: positions })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            // Show success message briefly
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            
                            document.querySelector('.video-list').before(alertDiv);
                            
                            // Auto dismiss after 3 seconds
                            setTimeout(() => {
                                alertDiv.remove();
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating positions:', error);
                        alert('Failed to update video positions. Please try again.');
                    });
                }
            });
        }
        
        // Delete video confirmation
        document.querySelectorAll('.delete-video').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this video? This action cannot be undone.')) {
                    const videoId = this.getAttribute('data-id');
                    
                    // Delete video via AJAX
                    fetch(`{{ url('instructor/courses/' . $course->id . '/videos') }}/${videoId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            // Remove the video element from DOM
                            this.closest('.video-item').remove();
                            
                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            
                            document.querySelector('.video-list').before(alertDiv);
                            
                            // Auto dismiss after 3 seconds
                            setTimeout(() => {
                                alertDiv.remove();
                            }, 3000);
                            
                            // If no videos left, show the empty state
                            if (document.querySelectorAll('.video-item').length === 0) {
                                location.reload(); // Simple solution to show the empty state
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting video:', error);
                        alert('Failed to delete the video. Please try again.');
                    });
                }
            });
        });
        
        // Delete material confirmation
        document.querySelectorAll('.delete-material').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this material? This action cannot be undone.')) {
                    const materialId = this.getAttribute('data-id');
                    
                    // Delete material via AJAX
                    fetch(`{{ url('instructor/courses/' . $course->id . '/materials') }}/${materialId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            // Remove the material element from DOM
                            this.closest('.material-item').remove();
                            
                            // Show success message
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-success alert-dismissible fade show';
                            alertDiv.innerHTML = `
                                ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            
                            document.querySelector('.material-list').before(alertDiv);
                            
                            // Auto dismiss after 3 seconds
                            setTimeout(() => {
                                alertDiv.remove();
                            }, 3000);
                            
                            // If no materials left, show the empty state
                            if (document.querySelectorAll('.material-item').length === 0) {
                                location.reload(); // Simple solution to show the empty state
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting material:', error);
                        alert('Failed to delete the material. Please try again.');
                    });
                }
            });
        });
    });
</script>
@endsection 