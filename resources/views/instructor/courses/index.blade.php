@extends('layouts.instructor')

@section('title', 'My Courses')
@section('page-title', 'My Courses')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Manage Your Courses</h4>
            <p class="text-muted">Create and manage your educational content</p>
        </div>
        <div>
            <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Create New Course
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(count($courses) > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Students</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="course-image me-3">
                                                @if($course->image_path)
                                                    <img src="{{ asset($course->image_path) }}" alt="{{ $course->title }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                        <i class="fas fa-book fa-2x text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $course->title }}</h6>
                                                <small class="text-muted">{{ Str::limit($course->description, 60) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $course->category->name ?? 'Uncategorized' }}</td>
                                    <td>${{ number_format($course->price, 2) }}</td>
                                    <td>
                                        @if($course->status === 'published')
                                            <span class="badge bg-success">Published</span>
                                        @elseif($course->status === 'pending')
                                            <span class="badge bg-warning">Pending Approval</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $course->students->count() }}</td>
                                    <td>{{ $course->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="courseActionsDropdown-{{ $course->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="courseActionsDropdown-{{ $course->id }}">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('instructor.courses.manage', $course->course_id) }}">
                                                        <i class="fas fa-tasks me-2"></i> Manage Content
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('instructor.courses.edit', $course->course_id) }}">
                                                        <i class="fas fa-edit me-2"></i> Edit Details
                                                    </a>
                                                </li>
                                                @if($course->status === 'published')
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('course.detail', $course->course_id) }}" target="_blank">
                                                        <i class="fas fa-eye me-2"></i> View Course
                                                    </a>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-book-open fa-4x text-muted"></i>
                </div>
                <h4>No Courses Yet</h4>
                <p class="text-muted">You haven't created any courses yet. Get started by creating your first course.</p>
                <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Create Your First Course
                </a>
            </div>
        </div>
    @endif
</div>
@endsection