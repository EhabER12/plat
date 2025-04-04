@extends('layouts.instructor')

@section('title', 'Edit Course')
@section('page-title', 'Edit Course')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('instructor.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $course->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Course Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description', $course->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">Select a category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ (old('category_id', $course->category_id) == $category->id) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $course->price) }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="course_image" class="form-label">Course Thumbnail</label>
                                    @if($course->image_path)
                                        <div class="mb-2">
                                            <img src="{{ asset($course->image_path) }}" alt="{{ $course->title }}" class="img-thumbnail" style="max-height: 150px;">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('course_image') is-invalid @enderror" id="course_image" name="course_image">
                                    <small class="text-muted">Leave empty to keep the current image. Recommended size: 1280x720 pixels.</small>
                                    @error('course_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mb-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading">About Course Approval</h5>
                                    <p class="mb-0">
                                        @if($course->status === 'published')
                                            Your course is currently published and visible to students. Significant changes may require re-approval.
                                        @elseif($course->status === 'pending')
                                            Your course is pending approval. Any changes will be reviewed by administrators.
                                        @else
                                            Your course was previously rejected. Please address the feedback before resubmitting.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('instructor.courses') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Course Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        @if($course->status === 'published')
                            <div class="me-3 text-success">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Published</h6>
                                <p class="small text-muted mb-0">Your course is live</p>
                            </div>
                        @elseif($course->status === 'pending')
                            <div class="me-3 text-warning">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Pending Approval</h6>
                                <p class="small text-muted mb-0">Awaiting review</p>
                            </div>
                        @else
                            <div class="me-3 text-danger">
                                <i class="fas fa-times-circle fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Rejected</h6>
                                <p class="small text-muted mb-0">Please address feedback</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Course Stats</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Enrolled Students</span>
                            <span class="fw-bold">{{ $course->students_count ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Video Lessons</span>
                            <span class="fw-bold">{{ $course->videos_count ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="d-flex justify-content-between">
                            <span>Course Materials</span>
                            <span class="fw-bold">{{ $course->materials_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quick Links</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('instructor.courses.manage', $course->id) }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-video me-3 text-primary"></i> Manage Course Content
                        </a>
                        <a href="{{ route('course.detail', $course->id) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-eye me-3 text-primary"></i> Preview Course
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-users me-3 text-primary"></i> View Enrolled Students
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 