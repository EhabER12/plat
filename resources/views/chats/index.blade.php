@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">My Chats</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('chats.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Chat
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    @if($chats->isEmpty())
                        <div class="text-center p-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <p class="lead">You don't have any chats yet.</p>
                            <a href="{{ route('chats.create') }}" class="btn btn-primary">
                                Start a New Chat
                            </a>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($chats as $chat)
                                <a href="{{ route('chats.show', $chat->chat_id) }}" class="list-group-item list-group-item-action d-flex align-items-center p-3">
                                    <div class="flex-shrink-0">
                                        @if($chat->is_group_chat)
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        @else
                                            @php
                                                $otherParticipant = $chat->participants->where('user_id', '!=', auth()->user()->user_id)->first();
                                                $otherUser = $otherParticipant ? $otherParticipant->user : null;
                                            @endphp
                                            @if($otherUser && $otherUser->profile_image)
                                                <img src="{{ asset('storage/' . $otherUser->profile_image) }}" alt="{{ $otherUser->name }}" class="rounded-circle" width="50" height="50">
                                            @else
                                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-1">{{ $chat->title }}</h5>
                                            <small class="text-muted">{{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : $chat->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1 text-muted">
                                            <small>{{ $chat->course->title }}</small>
                                        </p>
                                        @if($chat->latestMessage)
                                            <p class="mb-0 text-truncate" style="max-width: 500px;">
                                                <small>
                                                    <strong>{{ $chat->latestMessage->sender->name }}:</strong>
                                                    {{ $chat->latestMessage->content }}
                                                </small>
                                            </p>
                                        @else
                                            <p class="mb-0 text-muted">
                                                <small>No messages yet</small>
                                            </p>
                                        @endif
                                    </div>
                                    @php
                                        $unreadCount = $chat->unreadMessagesCount(auth()->user()->user_id);
                                    @endphp
                                    @if($unreadCount > 0)
                                        <div class="ms-2">
                                            <span class="badge bg-primary rounded-pill">{{ $unreadCount }}</span>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
