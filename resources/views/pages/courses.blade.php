@extends('layouts.app')

@section('title', 'All Courses - Laravel App')

@section('content')
    <h1 class="mb-4">Explore Our Courses</h1>

    @if(isset($error))
        <div class="alert alert-warning">
            <p>Note: There was an issue connecting to the database. This is a demo view.</p>
            <small>Error: {{ $error }}</small>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ url('/courses') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id ?? $category->category_id ?? '' }}" {{ isset($currentCategory) && $currentCategory == ($category->id ?? $category->category_id ?? '') ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ $currentSearch ?? '' }}" placeholder="Search courses...">
                </div>

                <div class="col-md-3">
                    <label for="sort" class="form-label">Sort By</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="newest" {{ isset($currentSort) && $currentSort == 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="oldest" {{ isset($currentSort) && $currentSort == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="price-low" {{ isset($currentSort) && $currentSort == 'price-low' ? 'selected' : '' }}>Price (Low to High)</option>
                        <option value="price-high" {{ isset($currentSort) && $currentSort == 'price-high' ? 'selected' : '' }}>Price (High to Low)</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="row">
        @if(isset($error))
            <div class="col-12">
                <div class="alert alert-warning">
                    {{ $error }}
                </div>
            </div>
        @elseif(count($courses) > 0)
            @foreach($courses as $course)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="bg-secondary text-white text-center py-5">
                            <h5>{{ $course->title }}</h5>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $course->title }}</h5>
                            <p class="card-text">{{ \Illuminate\Support\Str::limit($course->description, 100) }}</p>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span>${{ $course->price }}</span>
                                </div>
                                <span class="badge bg-primary">{{ $course->category_name ?? 'Uncategorized' }}</span>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <small class="text-muted">By {{ $course->instructor_name ?? 'Unknown' }}</small>
                            <a href="{{ url('/courses/' . ($course->course_id ?? $course->id ?? '')) }}" class="btn btn-sm btn-outline-primary">View Course</a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="alert alert-info">
                    No courses match your criteria. Try adjusting your filters.
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if(isset($courses) && $courses instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="d-flex justify-content-center mt-4">
            {{ $courses->appends(request()->query())->links() }}
        </div>
    @endif
@endsection