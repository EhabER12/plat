/**
 * Simple Messaging System
 * A lightweight messaging system for direct messages between users
 */
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textarea
    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }

    // Form submission
    const messageForm = document.getElementById('directMessageForm');
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            // Form will be submitted normally - no need to prevent default
            console.log('Message form submitted');
        });
    }

    // Auto-scroll to bottom of messages
    const messagesContainer = document.querySelector('.chat-messages');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Refresh messages every 10 seconds
    setInterval(function() {
        if (window.location.href.includes('/messages/')) {
            location.reload();
        }
    }, 10000);
});
