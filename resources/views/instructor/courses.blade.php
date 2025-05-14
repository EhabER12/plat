@extends('layouts.instructor')

@section('title', 'My Courses')
@section('page-title', 'My Courses')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted">Manage your courses and track their performance</p>
        <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i> Create New Course
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            @if(count($courses) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Students</th>
                                <th>Videos</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3">
                                                <i class="fas fa-book text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $course->title }}</h6>
                                                <small>Created: {{ \Carbon\Carbon::parse($course->created_at)->format('M d, Y') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $course->category->name ?? 'Uncategorized' }}</td>
                                    <td>${{ $course->price }}</td>
                                    <td>{{ $course->students_count }}</td>
                                    <td>{{ $course->videos_count }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $avgRating = $course->average_rating ?? 0;
                                                $ratingCount = $course->reviews_count ?? 0;
                                            @endphp
                                            <div class="me-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($avgRating))
                                                        <i class="fas fa-star text-warning small"></i>
                                                    @elseif($i - 0.5 <= $avgRating)
                                                        <i class="fas fa-star-half-alt text-warning small"></i>
                                                    @else
                                                        <i class="far fa-star text-warning small"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <span>{{ number_format($avgRating, 1) }} ({{ $ratingCount }})</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($course->status === 'published')
                                            <span class="badge bg-success">Published</span>
                                        @elseif($course->status === 'pending')
                                            <span class="badge bg-warning">Pending Approval</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionDropdown{{ $course->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="actionDropdown{{ $course->id }}">
                                                <li>
                                                    <a href="{{ route('course.detail', $course->id) }}" class="dropdown-item" target="_blank">
                                                        <i class="fas fa-eye me-2"></i> View
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('instructor.courses.edit', $course->id) }}" class="dropdown-item">
                                                        <i class="fas fa-edit me-2"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('instructor.courses.manage', $course->id) }}" class="dropdown-item">
                                                        <i class="fas fa-cogs me-2"></i> Manage Content
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a href="#" class="dropdown-item text-danger" 
                                                       onclick="if(confirm('Are you sure you want to delete this course? This action cannot be undone.')) { document.getElementById('delete-form-{{ $course->id }}').submit(); }">
                                                        <i class="fas fa-trash-alt me-2"></i> Delete
                                                    </a>
                                                    <form id="delete-form-{{ $course->id }}" action="#" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </li>
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
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-book-open fa-4x text-muted"></i>
                    </div>
                    <h4>You haven't created any courses yet</h4>
                    <p class="text-muted">Start creating your first course to share your knowledge with students!</p>
                    <a href="{{ route('instructor.courses.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus-circle me-2"></i> Create Your First Course
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 