@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-800">Create New Chat</h1>
            <a href="{{ route('chats.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Chats
            </a>
        </div>

        <div class="p-6">
            <form action="{{ route('chats.store') }}" method="POST">
                @csrf
                
                <div class="mb-5">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Chat Title</label>
                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-5">
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Select Course</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('course_id') border-red-500 @enderror" id="course_id" name="course_id" required>
                        <option value="">-- Select a Course --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->course_id }}" {{ old('course_id') == $course->course_id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-5">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500" id="is_group_chat" name="is_group_chat" value="1" {{ old('is_group_chat') ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Create as Group Chat</span>
                    </label>
                </div>
                
                <div class="mb-5" id="participants-container">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Participants</label>
                    <div id="participants-list" class="border border-gray-200 rounded-md p-4 mb-2 max-h-[300px] overflow-y-auto">
                        <div class="text-center text-gray-500 py-4">
                            Please select a course first to see available participants
                        </div>
                    </div>
                    @error('participants')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-md transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Chat
                    </button>
                </div>
            </form>
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
                participantsList.innerHTML = '<div class="text-center text-gray-500 py-4">Please select a course first to see available participants</div>';
                return;
            }
            
            // Show loading
            participantsList.innerHTML = '<div class="flex justify-center items-center py-4"><div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div><span class="ml-2 text-gray-600">Loading participants...</span></div>';
            
            // Fetch participants for the selected course
            fetch(`/api/courses/${courseId}/participants`)
                .then(response => response.json())
                .then(data => {
                    if (data.participants.length === 0) {
                        participantsList.innerHTML = '<div class="text-center text-gray-500 py-4">No participants found for this course</div>';
                        return;
                    }
                    
                    let html = '';
                    data.participants.forEach(participant => {
                        if (participant.user_id !== {{ auth()->user()->user_id }}) {
                            html += `
                                <div class="flex items-center py-2 border-b border-gray-100">
                                    <input class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" type="checkbox" name="participants[]" value="${participant.user_id}" id="participant-${participant.user_id}">
                                    <label class="ml-3 flex items-center cursor-pointer" for="participant-${participant.user_id}">
                                        <span class="mr-2">${participant.name}</span>
                                        <span class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded-full">${participant.role}</span>
                                    </label>
                                </div>
                            `;
                        }
                    });
                    
                    participantsList.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching participants:', error);
                    participantsList.innerHTML = '<div class="text-center text-red-500 py-4">Error loading participants. Please try again.</div>';
                });
        });
    });
</script>
@endpush
@endsection
