// File: messaging.js - Handles real-time messaging functions

import './bootstrap';

// Set up message handling once DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('message-input');
    const messagesContainer = document.getElementById('messages-container');
    const receiverId = document.getElementById('receiver-id')?.value;
    const courseId = document.getElementById('course-id')?.value;
    const lastMessageId = document.getElementById('last-message-id')?.value;
    const userRole = document.getElementById('user-role')?.value;
    
    // Set up Echo listeners for real-time messages
    if (receiverId) {
        window.Echo.private(`chat.${document.getElementById('current-user-id').value}`)
            .listen('NewMessageSent', (e) => {
                // Only add message if it's from the user we're currently chatting with
                if (e.message.sender_id == receiverId) {
                    addMessageToContainer(e.message, 'received');
                    markMessageAsRead(e.message.id);
                } else {
                    // Update unread count for other contacts
                    updateContactUnreadCount(e.message.sender_id);
                }
            });
    }
    
    // Handle message submission
    if (messageForm) {
        messageForm.addEventListener('submit', function(event) {
            event.preventDefault();
            sendMessage();
        });
    }
    
    // Send message via AJAX
    function sendMessage() {
        if (!messageInput.value.trim()) return;
        
        // Create form data
        const formData = new FormData();
        formData.append('content', messageInput.value);
        formData.append('receiver_id', receiverId);
        if (courseId) formData.append('course_id', courseId);
        
        // Get CSRF token from meta tag
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Determine endpoint based on user role
        const endpoint = userRole === 'instructor' 
            ? '/instructor/messages' 
            : '/student/messages';
        
        // Send AJAX request
        axios.post(endpoint, formData, {
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(response => {
            if (response.data.success) {
                // Add sent message to UI
                addMessageToContainer(response.data.message, 'sent');
                
                // Clear input field
                messageInput.value = '';
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
        });
    }
    
    // Add a message to the messages container
    function addMessageToContainer(message, type) {
        // Create message element
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', type === 'sent' ? 'sent' : 'received');
        messageElement.dataset.id = message.id || message.message_id;
        
        // Create message content
        const messageContent = document.createElement('div');
        messageContent.classList.add('message-content');
        messageContent.textContent = message.content;
        
        // Create message time
        const messageTime = document.createElement('div');
        messageTime.classList.add('message-time');
        
        // Format the time
        const messageDate = new Date(message.created_at);
        messageTime.textContent = messageDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        // Assemble message element
        messageElement.appendChild(messageContent);
        messageElement.appendChild(messageTime);
        
        // Add to container
        messagesContainer.appendChild(messageElement);
        
        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Update last message ID for polling
        document.getElementById('last-message-id').value = message.id || message.message_id;
    }
    
    // Mark message as read
    function markMessageAsRead(messageId) {
        // Implementation depends on backend API
    }
    
    // Update unread count for a contact in the sidebar
    function updateContactUnreadCount(senderId) {
        const contactElement = document.querySelector(`.contact-item[data-id="${senderId}"]`);
        if (contactElement) {
            const badgeElement = contactElement.querySelector('.unread-badge');
            if (badgeElement) {
                const currentCount = parseInt(badgeElement.textContent) || 0;
                badgeElement.textContent = currentCount + 1;
                badgeElement.style.display = 'flex';
            } else {
                // Create new badge if it doesn't exist
                const newBadge = document.createElement('div');
                newBadge.classList.add('unread-badge');
                newBadge.textContent = '1';
                contactElement.querySelector('.contact-info').appendChild(newBadge);
            }
        }
    }
    
    // Poll for new messages as fallback for Echo
    function pollForNewMessages() {
        if (!receiverId) return;
        
        const endpoint = userRole === 'instructor' 
            ? '/instructor/messages/get-new' 
            : '/student/messages/get-new';
            
        const requestData = userRole === 'instructor'
            ? { student_id: receiverId, last_message_id: lastMessageId }
            : { instructor_id: receiverId, last_message_id: lastMessageId };
            
        axios.post(endpoint, requestData, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.data.success && response.data.messages.length > 0) {
                response.data.messages.forEach(message => {
                    // Check if message is not already in the container
                    if (!document.querySelector(`.message[data-id="${message.message_id}"]`)) {
                        const type = message.sender_id == document.getElementById('current-user-id').value ? 'sent' : 'received';
                        addMessageToContainer(message, type);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error polling for messages:', error);
        });
    }
    
    // Set up polling as a fallback
    if (receiverId) {
        // Poll every 10 seconds
        setInterval(pollForNewMessages, 10000);
    }
}); 