@extends('admin.layout')

@section('title', 'Courses Management')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Courses Management</h1>
            <div>
                <a href="{{ route('admin.course.approvals') }}" class="btn btn-info me-2">
                    <i class="fas fa-clipboard-check me-1"></i> Pending Approvals
                </a>
                <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add New Course
                </a>
            </div>
        </div>
        
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
        
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.courses') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request()->get('search') }}" placeholder="Search by title">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}" {{ request()->get('category') == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="published" {{ request()->get('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="pending" {{ request()->get('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="draft" {{ request()->get('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="rejected" {{ request()->get('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="sort" class="form-label">Sort By</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="latest" {{ request()->get('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="oldest" {{ request()->get('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="title_asc" {{ request()->get('sort') == 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
                            <option value="title_desc" {{ request()->get('sort') == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
                            <option value="price_low" {{ request()->get('sort') == 'price_low' ? 'selected' : '' }}>Price (Low-High)</option>
                            <option value="price_high" {{ request()->get('sort') == 'price_high' ? 'selected' : '' }}>Price (High-Low)</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Courses Table -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Courses List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Instructor</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courses as $course)
                                <tr>
                                    <td>{{ $course->course_id }}</td>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->instructor->name }}</td>
                                    <td>{{ $course->category->name }}</td>
                                    <td>${{ number_format($course->price, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $course->status == 'published' ? 'success' : ($course->status == 'pending' ? 'warning' : ($course->status == 'rejected' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst($course->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $course->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.courses.show', $course->course_id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.courses.edit', $course->course_id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCourseModal{{ $course->course_id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Delete Course Modal -->
                                        <div class="modal fade" id="deleteCourseModal{{ $course->course_id }}" tabindex="-1" aria-labelledby="deleteCourseModalLabel{{ $course->course_id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteCourseModalLabel{{ $course->course_id }}">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete the course <strong>{{ $course->title }}</strong>?</p>
                                                        <p class="text-danger">This action cannot be undone. All data associated with this course will be permanently deleted.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <form action="{{ route('admin.courses.destroy', $course->course_id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Delete Course</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No courses found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $courses->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection 