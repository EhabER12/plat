@extends('admin.layout')

@section('title', 'Conversation with ' . $user->name)
@section('page-title', 'محادثة مع ' . $user->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="height: 70vh;">
            <!-- Chat Header -->
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.messages.index') }}" class="btn btn-light btn-sm me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="avatar-circle bg-white text-primary d-flex align-items-center justify-content-center me-3" 
                         style="width: 40px; height: 40px; border-radius: 50%;">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $user->name }}</h6>
                        <small class="opacity-75">{{ $user->email }}</small>
                        <div class="mt-1">
                            @php
                                $userRoles = $user->getUserRoles();
                            @endphp
                            @foreach($userRoles as $role)
                                <span class="badge bg-light text-primary small me-1">
                                    @switch($role)
                                        @case('instructor')
                                            مدرس
                                            @break
                                        @case('student')
                                            طالب
                                            @break
                                        @case('parent')
                                            ولي أمر
                                            @break
                                        @default
                                            {{ $role }}
                                    @endswitch
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <small class="opacity-75">{{ $messages->count() }} رسالة</small>
                </div>
            </div>

            <!-- Messages Container -->
            <div class="card-body p-0 d-flex flex-column" style="height: calc(70vh - 140px);">
                <div id="messages-container" class="flex-grow-1 overflow-auto p-3" style="max-height: calc(70vh - 200px);">
                    @if($messages->count() > 0)
                        @foreach($messages as $message)
                            <div class="message-item mb-3 {{ $message->sender_id == Auth::user()->user_id ? 'admin-message' : 'user-message' }}" 
                                 data-message-id="{{ $message->message_id }}">
                                <div class="d-flex {{ $message->sender_id == Auth::user()->user_id ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="message-bubble {{ $message->sender_id == Auth::user()->user_id ? 'bg-primary text-white' : 'bg-light' }}" 
                                         style="max-width: 70%; padding: 12px 16px; border-radius: 18px;">
                                        <div class="message-content">
                                            {{ $message->content }}
                                        </div>
                                        <div class="message-time mt-1">
                                            <small class="{{ $message->sender_id == Auth::user()->user_id ? 'text-white-50' : 'text-muted' }}">
                                                {{ $message->created_at->format('H:i') }}
                                                @if($message->sender_id == Auth::user()->user_id)
                                                    @if($message->is_read)
                                                        <i class="fas fa-check-double ms-1" title="تم القراءة"></i>
                                                    @else
                                                        <i class="fas fa-check ms-1" title="تم الإرسال"></i>
                                                    @endif
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-comments fa-3x mb-3"></i>
                            <h5>لا توجد رسائل بعد</h5>
                            <p>ابدأ المحادثة بإرسال رسالة</p>
                        </div>
                    @endif
                </div>

                <!-- Message Input -->
                <div class="border-top p-3">
                    <form id="message-form" class="d-flex align-items-end">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $user->user_id }}">
                        <div class="flex-grow-1 me-2">
                            <textarea class="form-control border-0 bg-light" 
                                      id="message-input" 
                                      name="content" 
                                      rows="2" 
                                      placeholder="اكتب رسالتك هنا..."
                                      maxlength="1000"
                                      style="resize: none; border-radius: 20px;"></textarea>
                            <div class="form-text small">
                                <span id="char-count">0</span> / 1000
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center" 
                                style="width: 45px; height: 45px;" id="send-btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.avatar-circle {
    font-weight: bold;
    font-size: 1.2rem;
}

.message-bubble {
    word-wrap: break-word;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.admin-message .message-bubble {
    border-bottom-right-radius: 4px !important;
}

.user-message .message-bubble {
    border-bottom-left-radius: 4px !important;
}

#messages-container {
    scroll-behavior: smooth;
}

#message-input:focus {
    box-shadow: none;
    border: 1px solid #dee2e6;
}

.message-time {
    font-size: 0.75rem;
}

/* Custom scrollbar */
#messages-container::-webkit-scrollbar {
    width: 6px;
}

#messages-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#messages-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#messages-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let lastMessageId = {{ $messages->last() ? $messages->last()->message_id : 0 }};
    let isPolling = true;
    
    // Character count
    $('#message-input').on('input', function() {
        const length = $(this).val().length;
        $('#char-count').text(length);
        
        if (length > 900) {
            $('#char-count').addClass('text-warning');
        } else {
            $('#char-count').removeClass('text-warning');
        }
    });
    
    // Auto-resize textarea
    $('#message-input').on('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
    
    // Send message on Enter (Shift+Enter for new line)
    $('#message-input').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $('#message-form').submit();
        }
    });
    
    // Send message
    $('#message-form').on('submit', function(e) {
        e.preventDefault();
        
        const content = $('#message-input').val().trim();
        if (!content) return;
        
        const formData = {
            receiver_id: $('input[name="receiver_id"]').val(),
            content: content,
            _token: $('input[name="_token"]').val()
        };
        
        // Disable send button
        $('#send-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ route("admin.messages.send") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Add message to chat
                    addMessageToChat(response.message, true);
                    
                    // Clear input
                    $('#message-input').val('').trigger('input');
                    
                    // Update last message ID
                    lastMessageId = response.message.message_id;
                    
                    // Scroll to bottom
                    scrollToBottom();
                }
            },
            error: function(xhr) {
                let errorMessage = 'حدث خطأ أثناء إرسال الرسالة';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                alert(errorMessage);
            },
            complete: function() {
                // Re-enable send button
                $('#send-btn').prop('disabled', false).html('<i class="fas fa-paper-plane"></i>');
            }
        });
    });
    
    // Poll for new messages
    function pollNewMessages() {
        if (!isPolling) return;
        
        $.ajax({
            url: '{{ route("admin.messages.get-new") }}',
            method: 'POST',
            data: {
                user_id: {{ $user->user_id }},
                last_message_id: lastMessageId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success && response.messages.length > 0) {
                    response.messages.forEach(function(message) {
                        addMessageToChat(message, message.is_admin);
                        lastMessageId = Math.max(lastMessageId, message.message_id);
                    });
                    scrollToBottom();
                }
            },
            error: function() {
                // Silently handle polling errors
            },
            complete: function() {
                // Schedule next poll
                setTimeout(pollNewMessages, 3000);
            }
        });
    }
    
    function addMessageToChat(message, isAdmin) {
        const messageHtml = `
            <div class="message-item mb-3 ${isAdmin ? 'admin-message' : 'user-message'}" data-message-id="${message.message_id}">
                <div class="d-flex ${isAdmin ? 'justify-content-end' : 'justify-content-start'}">
                    <div class="message-bubble ${isAdmin ? 'bg-primary text-white' : 'bg-light'}" 
                         style="max-width: 70%; padding: 12px 16px; border-radius: 18px;">
                        <div class="message-content">
                            ${message.content}
                        </div>
                        <div class="message-time mt-1">
                            <small class="${isAdmin ? 'text-white-50' : 'text-muted'}">
                                ${formatTime(message.created_at)}
                                ${isAdmin ? '<i class="fas fa-check ms-1" title="تم الإرسال"></i>' : ''}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#messages-container').append(messageHtml);
    }
    
    function formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('ar-EG', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: false 
        });
    }
    
    function scrollToBottom() {
        const container = $('#messages-container');
        container.scrollTop(container[0].scrollHeight);
    }
    
    // Initial scroll to bottom
    scrollToBottom();
    
    // Start polling for new messages
    setTimeout(pollNewMessages, 3000);
    
    // Stop polling when page is hidden
    document.addEventListener('visibilitychange', function() {
        isPolling = !document.hidden;
        if (isPolling) {
            setTimeout(pollNewMessages, 1000);
        }
    });
});
</script>
@endsection
