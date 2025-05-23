/**
 * Enhanced Messaging System JavaScript
 * Adds animations, interactive elements, and improved functionality
 * to both student and instructor messaging interfaces
 */

class MessagingSystem {
    constructor(options = {}) {
        // Core elements
        this.messageInput = document.getElementById('messageInput');
        this.sendButton = document.getElementById('sendButton');
        this.messagesContainer = document.getElementById('messagesContainer');
        this.searchInput = document.getElementById('searchInput');
        this.contactsList = document.getElementById('contactsList');
        this.typingIndicator = document.getElementById('typingIndicator');
        this.onlineStatus = document.getElementById('onlineStatus');
        this.courseSelector = document.getElementById('courseSelector');

        // Settings
        this.options = {
            typingDelay: 2000,
            checkNewMessagesInterval: 5000,
            animateNewMessages: true,
            showTypingIndicator: true,
            enableSoundEffects: false,
            ...options
        };

        // State
        this.lastMessageId = this.getLastMessageId();
        this.isTyping = false;
        this.typingTimeout = null;
        this.checkMessagesInterval = null;
        this.unreadCount = 0;
        this.isInstructor = window.location.href.includes('/instructor/');
        this.messagesInitialized = false;

        // Log important elements
        console.log('MessagesContainer found:', !!this.messagesContainer);
        console.log('MessageInput found:', !!this.messageInput);
        console.log('SendButton found:', !!this.sendButton);

        // Initialize
        this.setupEventListeners();
        this.startMessagesPolling();
        this.scrollToBottom();

        // Set flag to prevent message duplication
        if (this.messagesContainer) {
            this.messagesContainer.dataset.initialized = 'true';
            this.messagesInitialized = true;
        }

        console.log('Messaging system initialized with options:', this.options);
        console.log('User context:', this.isInstructor ? 'Instructor' : 'Student');
        console.log('Initial lastMessageId:', this.lastMessageId);
    }

    setupEventListeners() {
        // Message input setup
        if (this.messageInput) {
            // Auto-resize textarea
            this.messageInput.addEventListener('input', () => {
                this.handleTyping();
                this.autoResizeTextarea();
            });

            // Send on Enter press (without Shift)
            this.messageInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });
        }

        // Send button
        if (this.sendButton) {
            this.sendButton.addEventListener('click', () => this.sendMessage());

            // Remove pulse effect after 5 seconds
            setTimeout(() => {
                this.sendButton.classList.remove('pulse');
            }, 5000);
        }

        // Search function
        if (this.searchInput && this.contactsList) {
            this.searchInput.addEventListener('input', () => this.filterContacts());
        }

        // Make messages container clickable to dismiss new message alert
        if (this.messagesContainer) {
            this.messagesContainer.addEventListener('click', (e) => {
                const alert = document.querySelector('.new-message-alert');
                if (alert) alert.remove();
            });
        }

        // Window focus/blur
        window.addEventListener('focus', () => this.handleWindowFocus());
        window.addEventListener('blur', () => this.handleWindowBlur());
    }

    // Message sending
    sendMessage() {
        if (!this.messageInput || !this.messageInput.value.trim()) {
            console.log('No message to send');
            return;
        }

        const messageText = this.messageInput.value.trim();
        const receiverId = this.getReceiverId();
        const courseId = this.courseSelector ? this.courseSelector.value : '';

        if (!receiverId) {
            console.error('No receiver ID found');
            return;
        }

        console.log('Preparing to send message:', {
            text: messageText,
            to: receiverId,
            courseId: courseId
        });

        // Create and add new message to DOM with temporary display
        const tempMessageEl = this.addMessageToDOM(messageText, true);

        // Clear input
        this.messageInput.value = '';
        this.autoResizeTextarea();

        // Play sound effect if enabled
        if (this.options.enableSoundEffects) {
            this.playSound('send');
        }

        // Try to send using the form directly first
        const messageForm = document.getElementById('messageForm');
        if (messageForm) {
            try {
                // Update form fields if needed
                const receiverIdInput = messageForm.querySelector('[name="receiver_id"]');
                const contentInput = messageForm.querySelector('[name="content"]');
                const courseIdInput = messageForm.querySelector('[name="course_id"]');

                if (receiverIdInput && contentInput) {
                    receiverIdInput.value = receiverId;
                    contentInput.value = messageText;

                    if (courseIdInput && courseId) {
                        courseIdInput.value = courseId;
                    }

                    // Create a hidden iframe for the form submission
                    const iframe = document.createElement('iframe');
                    iframe.name = 'message-submit-frame';
                    iframe.style.display = 'none';
                    document.body.appendChild(iframe);

                    // Set form target to iframe
                    const originalTarget = messageForm.target;
                    messageForm.target = 'message-submit-frame';

                    // Store original onsubmit
                    const originalOnSubmit = messageForm.onsubmit;
                    messageForm.onsubmit = null;

                    // Submit the form
                    messageForm.submit();

                    // Restore original form properties
                    setTimeout(() => {
                        messageForm.target = originalTarget;
                        messageForm.onsubmit = originalOnSubmit;
                        document.body.removeChild(iframe);
                    }, 1000);

                    console.log('Message sent using form submission');
                    return;
                }
            } catch (e) {
                console.error('Error submitting form directly:', e);
                // Fall back to the API method
            }
        }

        // Fall back to API method if form submission fails
        console.log('Falling back to API method for sending message');
        this.sendMessageToServer(receiverId, messageText, courseId);
    }

    sendMessageToServer(receiverId, messageText, courseId = '') {
        // Get the current URL to determine if we're in instructor or student context
        const endpoint = this.isInstructor
            ? '/instructor/messages'
            : '/student/messages';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        if (!csrfToken) {
            console.error('CSRF token not found! Make sure the meta tag exists in the page.');
            alert('CSRF token not found! Please refresh the page and try again.');
            return;
        }

        console.log('Sending message to server:', {
            receiverId,
            message: messageText,
            courseId,
            endpoint,
            isInstructor: this.isInstructor,
            csrfToken: csrfToken ? 'Token exists' : 'No token!'
        });

        // Create a real form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = endpoint;
        form.style.display = 'none';

        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);

        // Add receiver ID
        const receiverInput = document.createElement('input');
        receiverInput.type = 'hidden';
        receiverInput.name = 'receiver_id';
        receiverInput.value = receiverId;
        form.appendChild(receiverInput);

        // Add message content
        const contentInput = document.createElement('input');
        contentInput.type = 'hidden';
        contentInput.name = 'content';
        contentInput.value = messageText;
        form.appendChild(contentInput);

        // Add course ID if provided
        if (courseId) {
            const courseInput = document.createElement('input');
            courseInput.type = 'hidden';
            courseInput.name = 'course_id';
            courseInput.value = courseId;
            form.appendChild(courseInput);
        }

        // Add AJAX header
        const ajaxInput = document.createElement('input');
        ajaxInput.type = 'hidden';
        ajaxInput.name = 'X-Requested-With';
        ajaxInput.value = 'XMLHttpRequest';
        form.appendChild(ajaxInput);

        // Add to document, submit, and remove
        document.body.appendChild(form);

        // Create a temporary iframe to handle the form submission
        const iframe = document.createElement('iframe');
        iframe.name = 'message-submit-frame';
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        // Set form target to iframe
        form.target = 'message-submit-frame';

        // Listen for iframe load event
        iframe.onload = () => {
            try {
                const iframeContent = iframe.contentDocument || iframe.contentWindow.document;
                const responseText = iframeContent.body.innerText;

                console.log('Response received:', responseText);

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Failed to parse response:', e);
                    data = { success: false, message: 'Invalid response format' };
                }

                this.handleMessageResponse(data, messageText, receiverId);

                // Clean up
                setTimeout(() => {
                    document.body.removeChild(iframe);
                    document.body.removeChild(form);
                }, 1000);

            } catch (e) {
                console.error('Error processing iframe response:', e);

                // Clean up
                setTimeout(() => {
                    document.body.removeChild(iframe);
                    document.body.removeChild(form);
                }, 1000);
            }
        };

        // Submit the form
        form.submit();
    }

    handleMessageResponse(data, messageText, receiverId) {
        console.log('Response data:', data);

        if (data && data.success) {
            // Remove placeholder message if any and add the proper one
            const messages = document.querySelectorAll('.message.sent:not([data-id])');
            if (messages.length > 0) {
                const lastMessage = messages[messages.length - 1];
                if (lastMessage) {
                    lastMessage.parentNode.removeChild(lastMessage);
                }
            }

            // Add message with the correct message ID from server
            const messageEl = this.addMessageToDOM(
                messageText,
                true,
                data.message.message_id,
                data.message.created_at
            );

            // Update lastMessageId to include this message
            this.lastMessageId = data.message.message_id;
            if (document.getElementById('last-message-id')) {
                document.getElementById('last-message-id').value = data.message.message_id;
            }

            // Update contact preview
            this.updateContactPreview(receiverId, messageText);
        } else {
            console.error('Error sending message:', (data && data.message) || 'Unknown error');

            // Show error indicator on the message
            const messages = document.querySelectorAll('.message.sent:not([data-id])');
            if (messages.length > 0) {
                const lastMessage = messages[messages.length - 1];
                if (lastMessage) {
                    lastMessage.classList.add('error');
                    lastMessage.setAttribute('title', 'Error: ' + ((data && data.message) || 'Unknown error'));
                }
            }
        }
    }

    // DOM manipulation
    addMessageToDOM(messageText, isSent = true, messageId = null, createdAt = null) {
        if (!this.messagesContainer) {
            console.error('messagesContainer not found');
            return;
        }

        // Check if this message already exists in the DOM
        if (messageId && document.querySelector(`.message[data-id="${messageId}"]`)) {
            console.log(`Message ${messageId} already exists in DOM, skipping`);
            return;
        }

        // Create message elements
        const messageGroup = document.createElement('div');
        messageGroup.className = 'message-group';

        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
        if (this.options.animateNewMessages) {
            messageDiv.classList.add('animate__animated', isSent ? 'animate__fadeInRight' : 'animate__fadeInLeft');
        }

        const messageContent = document.createElement('p');
        messageContent.textContent = messageText;

        const messageTime = document.createElement('div');
        messageTime.className = `message-time ${isSent ? 'sent' : 'received'}`;
        const now = new Date(createdAt || Date.now());
        messageTime.textContent = now.toLocaleTimeString([], {hour: 'numeric', minute:'2-digit'});

        // Assemble message
        messageDiv.appendChild(messageContent);
        messageDiv.appendChild(messageTime);
        messageGroup.appendChild(messageDiv);

        // Set message ID before adding to DOM
        if (messageId) {
            messageDiv.dataset.id = messageId;
        }

        // Add to DOM - directly to messagesContainer since that's the actual structure
        this.messagesContainer.appendChild(messageGroup);

        // Scroll to bottom
        this.scrollToBottom();

        console.log(`Added message to DOM: ${messageId || 'temporary'}, sent by ${isSent ? 'current user' : 'other user'}`);

        return messageDiv;
    }

    updateContactPreview(contactId, messageText) {
        if (!this.contactsList) return;

        const contactItem = this.contactsList.querySelector(`.contact-item[data-contact-id="${contactId}"]`);
        if (!contactItem) return;

        // Update preview text
        const preview = contactItem.querySelector('.contact-preview');
        if (preview) {
            preview.innerHTML = messageText;
        }

        // Update time
        const time = contactItem.querySelector('.contact-time');
        if (time) {
            time.textContent = 'Just now';
        }

        // Move contact to top of list
        if (contactItem.previousElementSibling && contactItem.parentNode) {
            contactItem.parentNode.insertBefore(contactItem, contactItem.parentNode.firstChild);
        }

        // Add subtle highlight animation
        contactItem.classList.add('fade-in');
        setTimeout(() => {
            contactItem.classList.remove('fade-in');
        }, 300);
    }

    scrollToBottom() {
        if (this.messagesContainer) {
            this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
        }
    }

    showNewMessageAlert(count = 1) {
        // Remove existing alert if any
        const existingAlert = document.querySelector('.new-message-alert');
        if (existingAlert) existingAlert.remove();

        // Create new alert
        const alert = document.createElement('div');
        alert.className = 'new-message-alert';
        alert.textContent = count > 1 ? `${count} new messages` : 'New message';
        alert.addEventListener('click', () => {
            alert.remove();
            this.scrollToBottom();
        });

        // Add to DOM
        document.body.appendChild(alert);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    // Typing indicator
    handleTyping() {
        if (!this.options.showTypingIndicator) return;

        this.isTyping = true;
        this.showTypingIndicator();

        // Reset typing timeout
        clearTimeout(this.typingTimeout);
        this.typingTimeout = setTimeout(() => {
            this.isTyping = false;
            this.hideTypingIndicator();
        }, this.options.typingDelay);
    }

    showTypingIndicator() {
        if (this.typingIndicator && this.onlineStatus) {
            this.onlineStatus.style.display = 'none';
            this.typingIndicator.classList.add('active');
        }
    }

    hideTypingIndicator() {
        if (this.typingIndicator && this.onlineStatus) {
            this.typingIndicator.classList.remove('active');
            this.onlineStatus.style.display = 'inline';
        }
    }

    // Polling for new messages
    startMessagesPolling() {
        if (this.checkMessagesInterval) clearInterval(this.checkMessagesInterval);
        this.checkMessagesInterval = setInterval(() => {
            this.checkForNewMessages();
        }, this.options.checkNewMessagesInterval);

        console.log(`Started message polling with interval: ${this.options.checkNewMessagesInterval}ms`);
    }

    stopMessagesPolling() {
        if (this.checkMessagesInterval) {
            clearInterval(this.checkMessagesInterval);
            this.checkMessagesInterval = null;
        }
    }

    checkForNewMessages() {
        const receiverId = this.getReceiverId();
        if (!receiverId) {
            console.warn('No receiver ID found for polling');
            return;
        }

        // Check if messages container is initialized
        if (!this.messagesContainer) {
            console.warn('Messages container not found, skipping polling');
            return;
        }

        // Determine the appropriate endpoint based on context
        const endpoint = this.isInstructor
            ? '/instructor/messages/get-new'
            : '/student/messages/get-new';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        if (!csrfToken) {
            console.error('CSRF token not found for polling!');
        }

        // Create the appropriate request body based on context
        const formData = new FormData();
        formData.append('last_message_id', this.lastMessageId || 0);

        if (this.isInstructor) {
            formData.append('student_id', receiverId);
        } else {
            formData.append('instructor_id', receiverId);
        }

        console.log('Checking for new messages:', {
            endpoint,
            receiverId,
            lastMessageId: this.lastMessageId,
            isInstructor: this.isInstructor
        });

        // Use FormData for better compatibility
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                console.warn('Polling response not OK:', response.status);
            }
            return response.json().catch(err => {
                console.error('JSON parse error in polling:', err);
                return { success: false, messages: [] };
            });
        })
        .then(data => {
            console.log('New messages response:', data);

            if (data && data.success && data.messages && data.messages.length > 0) {
                let addedMessages = 0;

                // Process messages in order
                data.messages.forEach(message => {
                    // Skip if message is already displayed
                    if (document.querySelector(`.message[data-id="${message.message_id}"]`)) {
                        console.log(`Message ${message.message_id} already displayed, skipping`);
                        return;
                    }

                    const isCurrentUser = parseInt(message.sender_id) === parseInt(this.getCurrentUserId());
                    console.log(`Processing message ${message.message_id} from ${message.sender_id}, current user: ${this.getCurrentUserId()}, isCurrentUser: ${isCurrentUser}`);

                    // Add message to DOM
                    addedMessages++;

                    // Check if we need to add a date divider
                    this.addDateDividerIfNeeded(message.created_at);

                    // Add message to DOM
                    const messageEl = this.addMessageToDOM(
                        message.content,
                        isCurrentUser,
                        message.message_id,
                        message.created_at
                    );
                });

                // Update last message ID
                if (data.messages.length > 0) {
                    this.lastMessageId = data.messages[data.messages.length - 1].message_id;
                    console.log('Updated lastMessageId to:', this.lastMessageId);

                    // Also update the hidden input if it exists
                    const lastMessageIdInput = document.getElementById('last-message-id');
                    if (lastMessageIdInput) {
                        lastMessageIdInput.value = this.lastMessageId;
                    }
                }

                // Show notification if user has scrolled up
                if (addedMessages > 0 && !this.isScrolledToBottom()) {
                    this.unreadCount += addedMessages;
                    this.showNewMessageAlert(this.unreadCount);

                    // Play notification sound if enabled
                    if (this.options.enableSoundEffects) {
                        this.playSound('receive');
                    }
                } else if (addedMessages > 0) {
                    // Auto-scroll if already at bottom
                    this.scrollToBottom();
                    this.unreadCount = 0;

                    // Play notification sound if enabled
                    if (this.options.enableSoundEffects) {
                        this.playSound('receive');
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error checking for new messages:', error);
        });
    }

    // Helper methods
    getLastMessageId() {
        // Try to get from the hidden input
        const lastMessageIdInput = document.getElementById('last-message-id');
        if (lastMessageIdInput) {
            return parseInt(lastMessageIdInput.value) || 0;
        }

        // Fallback: Find last message in the DOM
        if (this.messagesContainer) {
            const messages = this.messagesContainer.querySelectorAll('.message[data-id]');
            if (messages.length > 0) {
                const lastMessage = messages[messages.length - 1];
                const messageId = parseInt(lastMessage.dataset.id);
                return isNaN(messageId) ? 0 : messageId;
            }
        }

        return 0;
    }

    getReceiverId() {
        // Try to get the receiver ID from the hidden input
        const receiverIdInput = document.getElementById('receiver-id');
        if (receiverIdInput) {
            return receiverIdInput.value;
        }

        // Fallback: Try to get from the URL
        const url = window.location.href;
        const match = url.match(/\/messages\/(\d+)/);
        if (match && match[1]) {
            return match[1];
        }

        return null;
    }

    getCurrentUserId() {
        // Try to get from the hidden input
        const userIdInput = document.getElementById('current-user-id');
        if (userIdInput) {
            return userIdInput.value;
        }

        // Fallback: get from the container data attribute
        const container = document.querySelector('.container[data-user-id], .container-fluid[data-user-id]');
        return container ? container.dataset.userId : null;
    }

    isScrolledToBottom() {
        if (!this.messagesContainer) return true;

        const tolerance = 50; // pixels of tolerance
        const scrollPosition = this.messagesContainer.scrollTop + this.messagesContainer.clientHeight;
        const scrollHeight = this.messagesContainer.scrollHeight;

        return scrollHeight - scrollPosition <= tolerance;
    }

    filterContacts() {
        if (!this.searchInput || !this.contactsList) return;

        const searchTerm = this.searchInput.value.toLowerCase();
        const contacts = this.contactsList.querySelectorAll('.contact-item');

        contacts.forEach(contact => {
            const name = contact.querySelector('.contact-name')?.textContent.toLowerCase() || '';
            const preview = contact.querySelector('.contact-preview')?.textContent.toLowerCase() || '';

            if (name.includes(searchTerm) || preview.includes(searchTerm)) {
                contact.style.display = 'flex';
                // Highlight matching text
                this.highlightText(contact, searchTerm);
            } else {
                contact.style.display = 'none';
            }
        });
    }

    highlightText(contactElement, searchTerm) {
        if (!searchTerm) return;

        // Reset any previous highlighting
        const name = contactElement.querySelector('.contact-name');
        const preview = contactElement.querySelector('.contact-preview');

        if (name) {
            name.innerHTML = name.textContent;
        }

        if (preview) {
            // Preserve unread indicator if present
            const hasUnread = preview.querySelector('.unread-indicator') !== null;
            const previewText = preview.textContent.trim();

            if (hasUnread) {
                const indicator = document.createElement('span');
                indicator.className = 'unread-indicator';
                preview.innerHTML = '';
                preview.appendChild(indicator);
                preview.appendChild(document.createTextNode(previewText));
            } else {
                preview.textContent = previewText;
            }
        }

        // Skip highlighting if search term is empty
        if (!searchTerm.trim()) return;

        // Highlight matching text
        const highlightMatches = (element, term) => {
            if (!element) return;

            const innerHTML = element.innerHTML;
            const index = element.textContent.toLowerCase().indexOf(term.toLowerCase());
            if (index >= 0) {
                const text = element.textContent;
                const before = text.substring(0, index);
                const match = text.substring(index, index + term.length);
                const after = text.substring(index + term.length);

                // Skip if element has complex HTML
                if (innerHTML !== element.textContent) return;

                element.innerHTML = before + `<span class="highlight">${match}</span>` + after;
            }
        };

        highlightMatches(name, searchTerm);

        // For preview, don't highlight inside child elements
        if (preview && preview.childNodes.length > 0) {
            // Get last text node
            const textNodes = Array.from(preview.childNodes).filter(node => node.nodeType === 3);
            if (textNodes.length > 0) {
                const lastTextNode = textNodes[textNodes.length - 1];
                const text = lastTextNode.textContent;
                const index = text.toLowerCase().indexOf(searchTerm.toLowerCase());

                if (index >= 0) {
                    const before = text.substring(0, index);
                    const match = text.substring(index, index + searchTerm.length);
                    const after = text.substring(index + searchTerm.length);

                    const fragment = document.createDocumentFragment();
                    fragment.appendChild(document.createTextNode(before));

                    const span = document.createElement('span');
                    span.className = 'highlight';
                    span.textContent = match;
                    fragment.appendChild(span);

                    fragment.appendChild(document.createTextNode(after));

                    preview.replaceChild(fragment, lastTextNode);
                }
            }
        }
    }

    addDateDividerIfNeeded(dateString) {
        if (!this.messagesContainer) return;

        const messageDate = new Date(dateString).toLocaleDateString();
        const lastDivider = this.messagesContainer.querySelector('.date-divider:last-of-type');

        // If no dividers yet, or if date is different from last divider, add new one
        if (!lastDivider ||
            !lastDivider.querySelector('.date-text') ||
            lastDivider.querySelector('.date-text').textContent !== messageDate) {

            const dateDivider = document.createElement('div');
            dateDivider.className = 'date-divider';

            const dateText = document.createElement('span');
            dateText.className = 'date-text';
            dateText.textContent = new Date(dateString).toLocaleDateString('en-US', {
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });

            dateDivider.appendChild(dateText);
            this.messagesContainer.appendChild(dateDivider);

            console.log('Added date divider for:', messageDate);
        }
    }

    autoResizeTextarea() {
        if (!this.messageInput) return;

        this.messageInput.style.height = 'auto';
        this.messageInput.style.height = (this.messageInput.scrollHeight) + 'px';
    }

    handleWindowFocus() {
        // Resume polling when window gets focus
        if (!this.checkMessagesInterval) {
            this.startMessagesPolling();
        }

        // Reset unread count
        this.unreadCount = 0;

        // Remove any new message alerts
        const alert = document.querySelector('.new-message-alert');
        if (alert) alert.remove();
    }

    handleWindowBlur() {
        // Optional: Can stop polling when window loses focus to save resources
        // this.stopMessagesPolling();
    }

    playSound(type) {
        // Simple sound effect function - can be expanded
        const sounds = {
            send: 'message-sent.mp3',
            receive: 'message-received.mp3',
            notification: 'notification.mp3'
        };

        const sound = sounds[type] || null;
        if (!sound) return;

        try {
            const audio = new Audio(`/sounds/${sound}`);
            audio.volume = 0.5;
            audio.play().catch(e => console.log('Sound play error:', e));
        } catch (error) {
            console.log('Sound error:', error);
        }
    }
}

// Initialize the messaging system when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing messaging system');

    // Set user ID for the messaging system
    const container = document.querySelector('.container[data-user-id], .container-fluid[data-user-id]');
    window.currentUserId = container ? container.dataset.userId : null;

    console.log('Current user ID from container:', window.currentUserId);

    // Check if we're on a messaging page
    const messagesContainer = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageInput');

    // Check if messaging system is already initialized
    if (messagesContainer && messagesContainer.dataset.initialized === 'true') {
        console.log('Messaging system already initialized, skipping');
        return;
    }

    if (messagesContainer && messageInput) {
        console.log('Messaging elements found, creating messaging system');

        // Clear any existing messages to prevent duplication on page refresh
        if (messagesContainer.children.length > 0) {
            console.log('Preserving server-rendered messages');
            // We don't clear the container because we want to keep the server-rendered messages
            // Just mark them as initialized
            messagesContainer.dataset.initialized = 'true';
        }

        // Create the messaging system instance
        window.messagingSystem = new MessagingSystem({
            typingDelay: 2000,
            checkNewMessagesInterval: 5000,
            animateNewMessages: true,
            showTypingIndicator: true,
            enableSoundEffects: false // Set to true to enable sounds (requires sound files)
        });

        console.log('Messaging system initialized');
    } else {
        console.log('Not on a messaging page, skipping initialization');
    }

    // Add dark/light mode toggle if found
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            document.body.classList.toggle('light-mode');
        });
    }
});