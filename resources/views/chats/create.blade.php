@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Create New Chat</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('chats.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Chats
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('chats.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Chat Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Select Course</label>
                            <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                                <option value="">-- Select a Course --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->course_id }}" {{ old('course_id') == $course->course_id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_group_chat" name="is_group_chat" value="1" {{ old('is_group_chat') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_group_chat">
                                    Create as Group Chat
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3" id="participants-container">
                            <label class="form-label">Select Participants</label>
                            <div id="participants-list" class="border rounded p-3 mb-2" style="max-height: 200px; overflow-y: auto;">
                                <div class="text-center text-muted py-3">
                                    Please select a course first to see available participants
                                </div>
                            </div>
                            @error('participants')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Create Chat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const courseSelect = document.getElementById('course_id');
        const participantsList = document.getElementById('participants-list');
        
        courseSelect.addEventListener('change', function() {
            const courseId = this.value;
            
            if (!courseId) {
                participantsList.innerHTML = '<div class="text-center text-muted py-3">Please select a course first to see available participants</div>';
                return;
            }
            
            // Show loading
            participantsList.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading participants...</div>';
            
            // Fetch participants for the selected course
            fetch(`/api/courses/${courseId}/participants`)
                .then(response => response.json())
                .then(data => {
                    if (data.participants.length === 0) {
                        participantsList.innerHTML = '<div class="text-center text-muted py-3">No participants found for this course</div>';
                        return;
                    }
                    
                    let html = '';
                    data.participants.forEach(participant => {
                        if (participant.user_id !== {{ auth()->user()->user_id }}) {
                            html += `
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="participants[]" value="${participant.user_id}" id="participant-${participant.user_id}">
                                    <label class="form-check-label d-flex align-items-center" for="participant-${participant.user_id}">
                                        <span class="me-2">${participant.name}</span>
                                        <span class="badge bg-secondary">${participant.role}</span>
                                    </label>
                                </div>
                            `;
                        }
                    });
                    
                    participantsList.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching participants:', error);
                    participantsList.innerHTML = '<div class="text-center text-danger py-3">Error loading participants. Please try again.</div>';
                });
        });
    });
</script>
@endpush
@endsection
