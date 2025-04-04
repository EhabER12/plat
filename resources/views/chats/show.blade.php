@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <a href="{{ route('chats.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0">{{ $chat->title }}</h1>
                <span class="badge bg-secondary ms-2">{{ $chat->course->title }}</span>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="chatOptionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chatOptionsDropdown">
                    @if($chat->is_group_chat)
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addParticipantsModal">
                                <i class="fas fa-user-plus me-2"></i> Add Participants
                            </a>
                        </li>
                    @endif
                    <li>
                        <form action="{{ route('chats.leave', $chat->chat_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave this chat?');">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Leave Chat
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9">
            <div class="card" style="height: calc(100vh - 200px);">
                <div class="card-body d-flex flex-column p-0">
                    <div class="chat-messages flex-grow-1 overflow-auto p-3" id="chat-messages" style="max-height: calc(100vh - 300px);">
                        @if($chat->messages->isEmpty())
                            <div class="text-center text-muted my-5">
                                <i class="fas fa-comments fa-3x mb-3"></i>
                                <p>No messages yet. Start the conversation!</p>
                            </div>
                        @else
                            @foreach($chat->messages as $message)
                                <div class="message mb-3 {{ $message->user_id == auth()->user()->user_id ? 'text-end' : '' }}">
                                    <div class="d-inline-block {{ $message->user_id == auth()->user()->user_id ? 'bg-primary text-white' : 'bg-light' }} rounded p-3" style="max-width: 75%;">
                                        @if($message->user_id != auth()->user()->user_id)
                                            <div class="fw-bold mb-1">{{ $message->sender->name }}</div>
                                        @endif
                                        <div>{{ $message->content }}</div>
                                        @if($message->hasAttachment())
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $message->attachment_url) }}" class="btn btn-sm {{ $message->user_id == auth()->user()->user_id ? 'btn-light' : 'btn-primary' }}" target="_blank">
                                                    <i class="fas fa-paperclip me-1"></i> 
                                                    Attachment
                                                </a>
                                            </div>
                                        @endif
                                        <div class="text-{{ $message->user_id == auth()->user()->user_id ? 'light' : 'muted' }} mt-1">
                                            <small>{{ $message->created_at->format('M d, g:i a') }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="chat-input p-3 border-top">
                        <form action="{{ route('chats.send-message', $chat->chat_id) }}" method="POST" enctype="multipart/form-data" id="message-form">
                            @csrf
                            <div class="input-group">
                                <input type="text" class="form-control" name="content" placeholder="Type your message..." required>
                                <label class="input-group-text" for="attachment">
                                    <i class="fas fa-paperclip"></i>
                                </label>
                                <input type="file" class="d-none" id="attachment" name="attachment">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <div id="attachment-preview" class="mt-2 d-none">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file me-2"></i>
                                    <span id="attachment-name"></span>
                                    <button type="button" class="btn btn-sm text-danger ms-2" id="remove-attachment">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Participants</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($chat->participants as $participant)
                            <li class="list-group-item d-flex align-items-center">
                                @if($participant->user->profile_image)
                                    <img src="{{ asset('storage/' . $participant->user->profile_image) }}" alt="{{ $participant->user->name }}" class="rounded-circle me-2" width="32" height="32">
                                @else
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold">{{ $participant->user->name }}</div>
                                    <small class="text-muted">
                                        @if($participant->is_admin)
                                            Admin
                                        @elseif($chat->course->instructor_id == $participant->user->user_id)
                                            Instructor
                                        @else
                                            Student
                                        @endif
                                    </small>
                                </div>
                            </li>
                        @endforeach
                    </ul>
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
                        <div id="participants-list" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <span class="ms-2">Loading participants...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Selected</button>
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
                attachmentPreview.classList.remove('d-none');
            } else {
                attachmentPreview.classList.add('d-none');
            }
        });
        
        removeAttachment.addEventListener('click', function() {
            attachmentInput.value = '';
            attachmentPreview.classList.add('d-none');
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
                            participantsList.innerHTML = '<div class="text-center text-muted py-3">No more participants available to add</div>';
                            return;
                        }
                        
                        let html = '';
                        availableParticipants.forEach(participant => {
                            html += `
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="participants[]" value="${participant.user_id}" id="participant-${participant.user_id}">
                                    <label class="form-check-label d-flex align-items-center" for="participant-${participant.user_id}">
                                        <span class="me-2">${participant.name}</span>
                                        <span class="badge bg-secondary">${participant.role}</span>
                                    </label>
                                </div>
                            `;
                        });
                        
                        participantsList.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error fetching participants:', error);
                        participantsList.innerHTML = '<div class="text-center text-danger py-3">Error loading participants. Please try again.</div>';
                    });
            });
        }
    });
</script>
@endpush
@endsection
