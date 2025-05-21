@extends('layouts.app')

@section('title', 'Messages')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="{{ asset('css/messaging.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container py-4" data-user-id="{{ Auth::id() }}">
    <div class="chat-container">
        <!-- Contacts Sidebar -->
        <div class="contacts-sidebar">
            <div class="contacts-header">
                <h1 class="text-lg font-bold text-white">Instructors</h1>
                <div class="mt-3">
                    <input type="text" id="searchInput" placeholder="Search instructors..." class="search-input">
                </div>
            </div>
            <div class="contacts-list" id="contactsList">
                @if($contacts->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">👋</div>
                        <p class="empty-state-message">No instructors found</p>
                        <p class="empty-state-text">When you enroll in courses, your instructors will appear here</p>
                    </div>
                @else
                    @foreach($contacts as $contact)
                        <div class="contact-item{{ $selectedContact && $selectedContact->user_id == $contact->user_id ? ' active' : '' }}" 
                             data-id="{{ $contact->user_id }}"
                             onclick="window.location.href='{{ route('student.messages.show', $contact->user_id) }}'">
                            <div class="contact-avatar{{ rand(0,1) ? ' online' : '' }}">
                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">{{ $contact->name }}</div>
                                <div class="contact-time">{{ $contact->last_message_time }}</div>
                                <div class="contact-preview">
                                    @if($contact->unread_count > 0)
                                        <span class="unread-badge">{{ $contact->unread_count }}</span>
                                    @endif
                                    {{ $contact->last_message_preview }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            @if(!$selectedContact)
                <div class="empty-state">
                    <div class="empty-state-icon">💬</div>
                    <p class="empty-state-message">Select an instructor to start chatting</p>
                    <p class="empty-state-text">Your conversations with instructors will appear here</p>
                </div>
            @else
                <!-- Chat Header -->
                <div class="chat-header">
                    <div class="contact-avatar{{ rand(0,1) ? ' online' : '' }}">
                        {{ strtoupper(substr($selectedContact->name, 0, 1)) }}
                    </div>
                    <div class="chat-header-info">
                        <div class="chat-header-name">{{ $selectedContact->name }}</div>
                        <div class="chat-header-status">
                            <span class="typing-indicator" id="typingIndicator">
                                <span>typing</span>
                                <span class="typing-dot"></span>
                                <span class="typing-dot"></span>
                                <span class="typing-dot"></span>
                            </span>
                            <span id="onlineStatus">Active now</span>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="chat-messages" id="messagesContainer">
                    @if($messages->isEmpty())
                            <div class="empty-state">
                                <div class="empty-state-icon">✉️</div>
                                <p class="empty-state-message">Start a conversation</p>
                                <p class="empty-state-text">Send a message to {{ $selectedContact->name }}</p>
                        </div>
                    @else
                            @php $prevDate = null; @endphp
                            @foreach($messages as $message)
                                @php 
                                    $currDate = $message->created_at->format('Y-m-d');
                                    $showDate = $prevDate !== $currDate;
                                    $prevDate = $currDate;
                                    $isCurrentUser = $message->sender_id == Auth::user()->user_id;
                                @endphp
                                
                                @if($showDate)
                                    <div class="date-divider">
                                        <span class="date-text">{{ $message->created_at->format('F j, Y') }}</span>
                                    </div>
                                @endif
                                
                                <div class="message-group">
                                    <div class="message {{ $isCurrentUser ? 'sent' : 'received' }} animate__animated {{ $isCurrentUser ? 'animate__fadeInRight' : 'animate__fadeInLeft' }}" 
                                        data-id="{{ $message->message_id }}">
                                    <p>{{ $message->content }}</p>
                                    <div class="message-time">{{ $message->created_at->format('g:i A') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                </div>

                <!-- Message Input -->
                <div class="chat-input">
                    <form id="messageForm">
                        <div class="chat-input-field">
                            <textarea 
                                id="messageInput" 
                                placeholder="Type a message..." 
                                rows="1" 
                                class="chat-input"
                                autofocus></textarea>
                            <!-- Hidden form fields -->
                            <input type="hidden" id="receiver-id" value="{{ $selectedContact->user_id }}">
                            <input type="hidden" id="current-user-id" value="{{ Auth::user()->user_id }}">
                            <input type="hidden" id="user-role" value="student">
                            @if($messages->isNotEmpty())
                                <input type="hidden" id="last-message-id" value="{{ $messages->last()->message_id }}">
                            @else
                                <input type="hidden" id="last-message-id" value="0">
                            @endif
                        </div>
                        <div class="chat-input-buttons">
                            <button type="button" class="chat-input-button" title="Attach file">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button type="submit" id="sendButton" class="chat-input-button send-button">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script src="{{ asset('js/messaging.js') }}"></script>
@endsection