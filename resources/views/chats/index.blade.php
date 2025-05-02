@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-800">My Chats</h1>
            <a href="{{ route('chats.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                New Chat
            </a>
        </div>

        @if($chats->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h5 class="text-lg font-medium mb-2">You don't have any chats yet</h5>
                <p class="text-gray-500 mb-4">Start a new conversation with your course mates</p>
                <a href="{{ route('chats.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-md transition-colors">
                    Start a New Chat
                </a>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($chats as $chat)
                    <a href="{{ route('chats.show', $chat->chat_id) }}" class="block p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                @if($chat->is_group_chat)
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                @else
                                    @php
                                        $otherParticipant = $chat->participants->where('user_id', '!=', auth()->user()->user_id)->first();
                                        $otherUser = $otherParticipant ? $otherParticipant->user : null;
                                    @endphp
                                    @if($otherUser && $otherUser->profile_image)
                                        <img src="{{ asset('storage/' . $otherUser->profile_image) }}" alt="{{ $otherUser->name }}" class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $chat->title }}</h3>
                                    <span class="text-xs text-gray-500">{{ $chat->last_message_at ? $chat->last_message_at->diffForHumans() : $chat->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $chat->course->title }}
                                </p>
                                <div class="flex justify-between items-center mt-1">
                                    @if($chat->latestMessage)
                                        <p class="text-sm text-gray-600 truncate max-w-md">
                                            <span class="font-medium">{{ $chat->latestMessage->sender->name }}:</span>
                                            {{ $chat->latestMessage->content }}
                                        </p>
                                    @else
                                        <p class="text-sm text-gray-500 italic">
                                            No messages yet
                                        </p>
                                    @endif
                                    
                                    @php
                                        $unreadCount = $chat->unreadMessagesCount(auth()->user()->user_id);
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="flex items-center justify-center w-6 h-6 bg-blue-500 text-white text-xs font-medium rounded-full">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
