@extends('admin.layout')

@section('title', 'Course Approvals')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Course Approvals</h1>
            <a href="{{ route('admin.courses') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Back to Courses
            </a>
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
        
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-clipboard-check me-1"></i>
                Pending Course Approvals
            </div>
            <div class="card-body">
                @if($pendingCourses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Instructor</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingCourses as $course)
                                    <tr>
                                        <td>{{ $course->course_id }}</td>
                                        <td>{{ $course->title }}</td>
                                        <td>{{ $course->instructor->name }}</td>
                                        <td>{{ $course->category->name }}</td>
                                        <td>${{ number_format($course->price, 2) }}</td>
                                        <td>{{ $course->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.courses.show', $course->course_id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveCourseModal{{ $course->course_id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectCourseModal{{ $course->course_id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Approve Course Modal -->
                                            <div class="modal fade" id="approveCourseModal{{ $course->course_id }}" tabindex="-1" aria-labelledby="approveCourseModalLabel{{ $course->course_id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="approveCourseModalLabel{{ $course->course_id }}">Approve Course</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('admin.course.process-approval', $course->course_id) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status" value="published">
                                                            <div class="modal-body">
                                                                <p>You are about to approve the course <strong>{{ $course->title }}</strong>.</p>
                                                                <p>This will make the course visible to students and available for enrollment.</p>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="feedback{{ $course->course_id }}" class="form-label">Feedback (Optional)</label>
                                                                    <textarea class="form-control" id="feedback{{ $course->course_id }}" name="feedback" rows="3" placeholder="Provide any feedback to the instructor..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-success">Approve Course</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Reject Course Modal -->
                                            <div class="modal fade" id="rejectCourseModal{{ $course->course_id }}" tabindex="-1" aria-labelledby="rejectCourseModalLabel{{ $course->course_id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="rejectCourseModalLabel{{ $course->course_id }}">Reject Course</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('admin.course.process-approval', $course->course_id) }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="status" value="rejected">
                                                            <div class="modal-body">
                                                                <p>You are about to reject the course <strong>{{ $course->title }}</strong>.</p>
                                                                <div class="alert alert-warning">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    Please provide feedback to the instructor explaining why the course was rejected.
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="feedback{{ $course->course_id }}" class="form-label">Rejection Feedback <span class="text-danger">*</span></label>
                                                                    <textarea class="form-control" id="feedback{{ $course->course_id }}" name="feedback" rows="5" required placeholder="Explain to the instructor why this course is being rejected and what changes are needed..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">Reject Course</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $pendingCourses->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-1"></i> There are no pending courses awaiting approval.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection 