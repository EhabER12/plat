@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="container py-4">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="flex h-[calc(100vh-200px)] min-h-[500px]">
            <!-- Contacts Sidebar -->
            <div class="w-full md:w-1/3 lg:w-1/4 border-r border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h1 class="text-xl font-semibold text-gray-800">Messages</h1>
                    <div class="mt-2">
                        <div class="relative">
                            <input type="text" placeholder="Search conversations" class="w-full pl-10 pr-4 py-2 bg-gray-100 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-y-auto h-[calc(100%-80px)]">
                    @if($contacts->isEmpty())
                        <div class="flex flex-col items-center justify-center h-full text-gray-500 p-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <h5 class="text-lg font-medium">No Messages Yet</h5>
                            <p class="text-center mt-2">You haven't exchanged messages with any instructors yet.</p>
                        </div>
                    @else
                        @foreach($contacts as $contact)
                            <div class="contact-item hover:bg-gray-50 {{ $selectedContact && $selectedContact->user_id == $contact->user_id ? 'bg-blue-50' : '' }}" 
                                onclick="window.location.href='{{ route('student.messages.show', $contact->user_id) }}'">
                                <div class="flex items-center p-3 cursor-pointer">
                                    <div class="flex-shrink-0 relative">
                                        @if($contact->profile_image)
                                            <img src="{{ asset($contact->profile_image) }}" alt="{{ $contact->name }}" class="w-12 h-12 rounded-full object-cover">
                                        @else
                                            <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">
                                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="flex justify-between items-center">
                                            <h3 class="text-sm font-semibold text-gray-900">{{ $contact->name }}</h3>
                                            <span class="text-xs text-gray-500">12m</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <p class="text-xs text-gray-500 truncate">Last message preview...</p>
                                            @if($contact->unread_count > 0)
                                                <span class="w-5 h-5 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs">{{ $contact->unread_count }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            
            <!-- Chat Area -->
            <div class="hidden md:flex flex-col w-2/3 lg:w-3/4">
                @if(!$selectedContact)
                    <div class="flex flex-col items-center justify-center h-full text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <h2 class="text-xl font-semibold mb-2">Your Messages</h2>
                        <p class="text-center max-w-sm">Select a conversation to start messaging</p>
                    </div>
                @else
                    <!-- Chat Header -->
                    <div class="border-b border-gray-200 p-4 flex items-center">
                        <div class="flex-shrink-0 relative">
                            @if($selectedContact->profile_image)
                                <img src="{{ asset($selectedContact->profile_image) }}" alt="{{ $selectedContact->name }}" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($selectedContact->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full border-2 border-white"></div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-md font-semibold text-gray-900">{{ $selectedContact->name }}</h3>
                            <p class="text-xs text-green-500">Online</p>
                        </div>
                        <div class="ml-auto flex space-x-2">
                            <button class="p-2 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" />
                                    <path d="M14 6a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z" />
                                </svg>
                            </button>
                            <button class="p-2 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button class="p-2 rounded-full hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Chat Messages -->
                    <div class="flex-1 overflow-y-auto p-4 bg-gray-50" id="messagesContainer">
                        @if($messages->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <h5 class="text-lg font-medium">No Messages Yet</h5>
                                <p class="text-center mt-2">Start the conversation with {{ $selectedContact->name }}!</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @php $prevDate = null; @endphp
                                @foreach($messages as $message)
                                    @php 
                                        $currDate = $message->created_at->format('Y-m-d');
                                        $showDate = $prevDate !== $currDate;
                                        $prevDate = $currDate;
                                    @endphp
                                    
                                    @if($showDate)
                                        <div class="flex justify-center my-4">
                                            <div class="bg-gray-200 text-gray-500 text-xs px-3 py-1 rounded-full">
                                                {{ $message->created_at->format('F j, Y') }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="flex {{ $message->sender_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                                        @if($message->sender_id != Auth::id())
                                            <div class="flex-shrink-0 mr-2">
                                                @if($selectedContact->profile_image)
                                                    <img src="{{ asset($selectedContact->profile_image) }}" alt="{{ $selectedContact->name }}" 
                                                         class="w-8 h-8 rounded-full object-cover">
                                                @else
                                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xs">
                                                        {{ strtoupper(substr($selectedContact->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <div class="max-w-[70%]">
                                            <div class="{{ $message->sender_id == Auth::id() ? 
                                                'bg-gradient-to-r from-blue-500 to-blue-600 text-white' : 
                                                'bg-white text-gray-800 border border-gray-200' }} 
                                                px-4 py-2 rounded-2xl {{ $message->sender_id == Auth::id() ? 'rounded-tr-none' : 'rounded-tl-none' }}">
                                                <p class="text-sm">{{ $message->content }}</p>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1 {{ $message->sender_id == Auth::id() ? 'text-right' : 'text-left' }}">
                                                {{ $message->created_at->format('g:i A') }}
                                                @if($message->sender_id == Auth::id())
                                                    @if($message->is_read)
                                                        <span class="ml-1 text-blue-500">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline -ml-1" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    @else
                                                        <span class="ml-1 text-gray-400">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- Message Input -->
                    <div class="p-4 border-t border-gray-200">
                        <form action="{{ route('student.messages.send') }}" method="POST" class="flex items-center">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $selectedContact->user_id }}">
                            
                            <button type="button" class="p-2 text-gray-500 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                            </button>
                            
                            <div class="relative flex-1 mx-2">
                                <input type="text" name="content" class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                    placeholder="Type a message..." required>
                            </div>
                            
                            <button type="submit" class="p-2 bg-blue-500 text-white rounded-full hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll to bottom of messages container
        const messagesContainer = document.getElementById('messagesContainer');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    });
</script>
@endsection
