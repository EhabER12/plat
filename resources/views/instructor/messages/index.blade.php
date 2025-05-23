@extends('layouts.instructor')

@section('title', 'Messages')
@section('page-title', 'Messages')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link rel="stylesheet" href="{{ asset('css/messaging.css') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container-fluid py-2" data-user-id="{{ Auth::id() }}">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="chat-container">
        <!-- Contacts Sidebar -->
        <div class="contacts-sidebar">
            <div class="contacts-header">
                <h1 class="text-lg font-bold text-white">Students</h1>
                <div class="mt-3">
                    <input type="text" id="searchInput" placeholder="Search students..." class="search-input">
                </div>
            </div>
            <div class="contacts-list" id="contactsList">
                @if($contacts->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">👋</div>
                        <p class="empty-state-message">No students found</p>
                        <p class="empty-state-text">When students message you about your courses, they'll appear here</p>
                    </div>
                @else
                    @foreach($contacts as $contact)
                        <div class="contact-item {{ $selectedContact && $selectedContact->user_id == $contact->user_id ? 'active' : '' }}"
                            data-id="{{ $contact->user_id }}"
                            onclick="window.location.href='{{ route('instructor.messages.show', $contact->user_id) }}'">
                            <div class="contact-avatar{{ rand(0,1) ? ' online' : '' }}">
                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                            </div>
                            <div class="contact-info">
                                <div class="contact-name">{{ $contact->name }}</div>
                                <div class="contact-time">{{ $contact->last_message_time ?? 'No messages yet' }}</div>
                                <div class="contact-preview">
                                    @if($contact->unread_count > 0)
                                        <span class="unread-badge">{{ $contact->unread_count }}</span>
                                    @endif
                                    {{ $contact->last_message_preview ?? 'Start a conversation' }}
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
                    <p class="empty-state-message">Select a student to start chatting</p>
                    <p class="empty-state-text">Your conversations with students will appear here. Keeping in touch with your students helps increase course engagement and satisfaction.</p>
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
                            <span id="onlineStatus">{{ rand(0,1) ? 'Active now' : 'Last active ' . rand(1, 60) . ' min ago' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="chat-messages" id="messagesContainer" data-initialized="true">
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
                                <div class="message {{ $isCurrentUser ? 'sent' : 'received' }} {{ $message->is_filtered ? 'filtered' : '' }} animate__animated {{ $isCurrentUser ? 'animate__fadeInRight' : 'animate__fadeInLeft' }}"
                                    data-id="{{ $message->message_id }}">
                                    <p>{{ $message->content }}</p>
                                    <div class="message-time {{ $isCurrentUser ? 'sent' : 'received' }}">
                                        {{ $message->created_at->format('g:i A') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Message Input -->
                <div class="chat-input">
                    <form id="directMessageForm" method="POST" action="{{ route('instructor.messages.send') }}">
                        @csrf
                        <div class="chat-input-field">
                            <textarea
                                id="messageInput"
                                name="content"
                                placeholder="Type a message..."
                                rows="1"
                                class="chat-input"
                                required
                                autofocus></textarea>
                            <!-- Hidden form fields -->
                            <input type="hidden" name="receiver_id" value="{{ $selectedContact->user_id }}">
                            @if(isset($courses) && count($courses) > 0)
                                <input type="hidden" name="course_id" value="{{ $courses->first()->course_id }}">
                            @endif
                        </div>
                        <div class="chat-input-buttons">
                            <button type="submit" class="chat-input-button send-button pulse">
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
<script src="{{ asset('js/simple-messaging.js') }}"></script>
@endsection
