@extends('layouts.instructor')

@section('title', 'Create New Course')
@section('page-title', 'Create New Course')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('instructor.courses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Choose a clear, descriptive title that accurately represents your course.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Course Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Provide a comprehensive description of what students will learn in your course.</small>
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
                                            <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
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
                                    <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Set a price that reflects the value of your course content. Enter 0 for a free course.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="thumbnail" class="form-label">Course Thumbnail</label>
                                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail">
                                    @error('thumbnail')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Recommended size: 1280x720 pixels. Max file size: 2MB. Supported formats: JPG, PNG.</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('instructor.courses') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Create Course
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Course Creation Guide</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-bold"><i class="fas fa-lightbulb text-warning me-2"></i> Tips</h6>
                        <ul class="small mb-0">
                            <li class="mb-2">Use a clear, specific title that includes keywords students might search for.</li>
                            <li class="mb-2">Write a detailed description that explains what students will learn and why it's valuable.</li>
                            <li class="mb-2">Select the most relevant category for better discovery.</li>
                            <li>Price your course based on the depth of content, your expertise, and market rates.</li>
                        </ul>
                    </div>

                    <div class="mb-0">
                        <h6 class="fw-bold"><i class="fas fa-info-circle text-primary me-2"></i> What's Next?</h6>
                        <p class="small mb-0">
                            After creating your course, you'll be able to add video lessons, downloadable materials, and set up quizzes.
                            Your course will be submitted for admin approval before being published.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection