@extends('layouts.instructor')

@section('title', 'Manage Exams')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Exams</h1>
        <a href="{{ route('instructor.exams.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Exam
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

    <div class="card shadow-sm">
        <div class="card-body">
            @if($exams->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Course</th>
                                <th>Duration</th>
                                <th>Questions</th>
                                <th>Status</th>
                                <th>Availability</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exams as $exam)
                                <tr>
                                    <td>{{ $exam->title }}</td>
                                    <td>{{ $exam->course->title }}</td>
                                    <td>{{ $exam->duration }} minutes</td>
                                    <td>{{ $exam->questions->count() }}</td>
                                    <td>
                                        @if($exam->is_published)
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($exam->available_from && $exam->available_to)
                                            {{ $exam->available_from->format('M d, Y') }} - {{ $exam->available_to->format('M d, Y') }}
                                        @elseif($exam->available_from)
                                            From {{ $exam->available_from->format('M d, Y') }}
                                        @elseif($exam->available_to)
                                            Until {{ $exam->available_to->format('M d, Y') }}
                                        @else
                                            Always available
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('instructor.exams.show', $exam->exam_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('instructor.exams.edit', $exam->exam_id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('instructor.exams.results', $exam->exam_id) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="if(confirm('Are you sure you want to delete this exam?')) {
                                                        document.getElementById('delete-exam-{{ $exam->exam_id }}').submit();
                                                    }">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-exam-{{ $exam->exam_id }}" action="{{ route('instructor.exams.destroy', $exam->exam_id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <img src="{{ asset('images/empty-state.svg') }}" alt="No exams" class="img-fluid mb-3" style="max-height: 200px;">
                    <h3>No Exams Yet</h3>
                    <p class="text-muted">Create your first exam to assess your students' knowledge.</p>
                    <a href="{{ route('instructor.exams.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Exam
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
