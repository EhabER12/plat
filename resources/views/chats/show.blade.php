@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="flex h-[calc(100vh-70px)] bg-gray-50">
        <!-- Chat Header -->
        <div class="flex flex-col w-full h-full">
            <!-- Top navigation bar -->
            <div class="flex items-center justify-between bg-white p-3 border-b shadow-sm">
                <div class="flex items-center">
                    <a href="{{ route('chats.index') }}" class="mr-3 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-lg font-semibold">{{ $chat->title }}</h1>
                    <span class="ml-2 px-2 py-1 text-xs font-medium bg-gray-200 text-gray-700 rounded-full">{{ $chat->course->title }}</span>
                </div>
                <div class="relative">
                    <button class="flex items-center justify-center w-8 h-8 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100" type="button" id="chatOptionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chatOptionsDropdown">
                        @if($chat->is_group_chat)
                            <li>
                                <a class="dropdown-item flex items-center py-2 px-4 text-gray-700 hover:bg-gray-100" href="#" data-bs-toggle="modal" data-bs-target="#addParticipantsModal">
                                    <i class="fas fa-user-plus mr-2"></i> Add Participants
                                </a>
                            </li>
                        @endif
                        <li>
                            <form action="{{ route('chats.leave', $chat->chat_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave this chat?');">
                                @csrf
                                <button type="submit" class="dropdown-item flex items-center py-2 px-4 text-red-600 hover:bg-gray-100 w-full text-left">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Leave Chat
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Chat content -->
            <div class="flex flex-1 overflow-hidden">
                <!-- Messages area -->
                <div class="flex-1 flex flex-col">
                    <!-- Messages container -->
                    <div class="flex-1 overflow-y-auto p-4 bg-gray-50" id="chat-messages">
                        @if($chat->messages->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full text-gray-500">
                                <i class="fas fa-comments text-4xl mb-3"></i>
                                <p>No messages yet. Start the conversation!</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @php
                                    $previousDate = null;
                                @endphp
                                
                                @foreach($chat->messages as $message)
                                    @php
                                        $messageDate = $message->created_at->format('Y-m-d');
                                        $isToday = $message->created_at->isToday();
                                        $isYesterday = $message->created_at->isYesterday();
                                        $displayDate = $isToday ? 'Today' : ($isYesterday ? 'Yesterday' : $message->created_at->format('F j, Y'));
                                        $isMyMessage = $message->user_id == auth()->user()->user_id;
                                    @endphp
                                    
                                    @if($previousDate != $messageDate)
                                        <div class="flex justify-center my-4">
                                            <div class="px-3 py-1 text-xs bg-gray-200 text-gray-600 rounded-full">
                                                {{ $displayDate }}
                                            </div>
                                        </div>
                                        @php
                                            $previousDate = $messageDate;
                                        @endphp
                                    @endif
                                    
                                    <div class="flex {{ $isMyMessage ? 'justify-end' : 'justify-start' }}">
                                        @if(!$isMyMessage)
                                            <div class="mr-2">
                                                @if($message->sender->profile_image)
                                                    <img src="{{ asset('storage/' . $message->sender->profile_image) }}" alt="{{ $message->sender->name }}" class="w-8 h-8 rounded-full">
                                                @else
                                                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white">
                                                        <i class="fas fa-user text-sm"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <div class="max-w-[75%]">
                                            @if(!$isMyMessage)
                                                <div class="text-xs text-gray-500 mb-1 ml-1">{{ $message->sender->name }}</div>
                                            @endif
                                            
                                            <div class="{{ $isMyMessage ? 'bg-blue-500 text-white' : 'bg-white text-gray-800' }} rounded-2xl px-4 py-2 shadow-sm">
                                                <div>{{ $message->content }}</div>
                                                
                                                @if($message->hasAttachment())
                                                    <div class="mt-2">
                                                        <a href="{{ asset('storage/' . $message->attachment_url) }}" class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full {{ $isMyMessage ? 'bg-blue-400 text-white' : 'bg-blue-100 text-blue-800' }}" target="_blank">
                                                            <i class="fas fa-paperclip mr-1"></i> 
                                                            Attachment
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="{{ $isMyMessage ? 'text-right' : 'text-left' }} text-xs text-gray-500 mt-1">
                                                {{ $message->created_at->format('g:i a') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- Message input -->
                    <div class="bg-white border-t p-3">
                        <form action="{{ route('chats.send-message', $chat->chat_id) }}" method="POST" enctype="multipart/form-data" id="message-form" class="flex items-end">
                            @csrf
                            <label class="cursor-pointer text-gray-500 hover:text-gray-700 p-2 mr-1" for="attachment">
                                <i class="fas fa-paperclip"></i>
                                <input type="file" class="hidden" id="attachment" name="attachment">
                            </label>
                            <div class="flex-1 relative">
                                <input type="text" class="w-full border border-gray-300 rounded-full py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-transparent" name="content" placeholder="Type your message..." required>
                                <div id="attachment-preview" class="hidden absolute bottom-full left-0 mb-1 bg-white shadow-md rounded p-2 w-full">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="fas fa-file mr-2 text-blue-500"></i>
                                            <span id="attachment-name" class="text-sm truncate max-w-[250px]"></span>
                                        </div>
                                        <button type="button" class="text-red-500 hover:text-red-700" id="remove-attachment">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button class="ml-2 bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-full w-10 h-10 flex items-center justify-center" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Participants sidebar -->
                <div class="w-64 border-l bg-white hidden md:block">
                    <div class="p-3 border-b">
                        <h5 class="font-semibold text-gray-700">Participants</h5>
                    </div>
                    <div class="overflow-y-auto" style="max-height: calc(100vh - 130px);">
                        <ul class="divide-y">
                            @foreach($chat->participants as $participant)
                                <li class="flex items-center p-3 hover:bg-gray-50">
                                    @if($participant->user->profile_image)
                                        <img src="{{ asset('storage/' . $participant->user->profile_image) }}" alt="{{ $participant->user->name }}" class="w-10 h-10 rounded-full mr-3">
                                    @else
                                        <div class="w-10 h-10 bg-gray-300 text-white rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-medium">{{ $participant->user->name }}</div>
                                        <div class="text-xs text-gray-500">
                                            @if($participant->is_admin)
                                                Admin
                                            @elseif($chat->course->instructor_id == $participant->user->user_id)
                                                Instructor
                                            @else
                                                Student
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($chat->is_group_chat)
<!-- Add Participants Modal -->
<div class="modal fade" id="addParticipantsModal" tabindex="-1" aria-labelledby="addParticipantsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addParticipantsModalLabel">Add Participants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('chats.add-participants', $chat->chat_id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Participants</label>
                        <div id="participants-list" class="border rounded p-3 max-h-[300px] overflow-y-auto">
                            <div class="flex justify-center items-center py-3">
                                <div class="spinner-border spinner-border-sm text-blue-500" role="status"></div>
                                <span class="ml-2">Loading participants...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-100" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Add Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll to bottom of chat messages
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Handle file attachment
        const attachmentInput = document.getElementById('attachment');
        const attachmentPreview = document.getElementById('attachment-preview');
        const attachmentName = document.getElementById('attachment-name');
        const removeAttachment = document.getElementById('remove-attachment');
        
        attachmentInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                attachmentName.textContent = this.files[0].name;
                attachmentPreview.classList.remove('hidden');
            } else {
                attachmentPreview.classList.add('hidden');
            }
        });
        
        removeAttachment.addEventListener('click', function() {
            attachmentInput.value = '';
            attachmentPreview.classList.add('hidden');
        });
        
        // Load participants for add participants modal
        const addParticipantsModal = document.getElementById('addParticipantsModal');
        if (addParticipantsModal) {
            addParticipantsModal.addEventListener('shown.bs.modal', function() {
                const participantsList = document.getElementById('participants-list');
                
                // Fetch available participants
                fetch(`/api/courses/{{ $chat->course_id }}/participants`)
                    .then(response => response.json())
                    .then(data => {
                        // Get current participant IDs
                        const currentParticipantIds = [
                            @foreach($chat->participants as $participant)
                                {{ $participant->user_id }},
                            @endforeach
                        ];
                        
                        // Filter out current participants
                        const availableParticipants = data.participants.filter(
                            participant => !currentParticipantIds.includes(participant.user_id)
                        );
                        
                        if (availableParticipants.length === 0) {
                            participantsList.innerHTML = '<div class="text-center text-gray-500 py-3">No more participants available to add</div>';
                            return;
                        }
                        
                        let html = '';
                        availableParticipants.forEach(participant => {
                            html += `
                                <div class="flex items-center py-2 border-b">
                                    <input class="form-check-input mr-3" type="checkbox" name="participants[]" value="${participant.user_id}" id="participant-${participant.user_id}">
                                    <label class="flex items-center cursor-pointer w-full" for="participant-${participant.user_id}">
                                        <span class="mr-2">${participant.name}</span>
                                        <span class="px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded-full">${participant.role}</span>
                                    </label>
                                </div>
                            `;
                        });
                        
                        participantsList.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error fetching participants:', error);
                        participantsList.innerHTML = '<div class="text-center text-red-500 py-3">Error loading participants. Please try again.</div>';
                    });
            });
        }
    });
</script>
@endpush
@endsection
