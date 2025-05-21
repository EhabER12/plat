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

        // Initialize
        this.setupEventListeners();
        this.startMessagesPolling();
        this.scrollToBottom();

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
        if (!this.messageInput || !this.messageInput.value.trim()) return;

        const messageText = this.messageInput.value.trim();
        const receiverId = this.getReceiverId();
        const courseId = this.courseSelector ? this.courseSelector.value : '';

        if (!receiverId) return;

        // Create and add new message to DOM with temporary display
        const tempMessageEl = this.addMessageToDOM(messageText, true);

        // Clear input
        this.messageInput.value = '';
        this.autoResizeTextarea();

        // Play sound effect if enabled
        if (this.options.enableSoundEffects) {
            this.playSound('send');
        }

        // Send to server using fetch API
        this.sendMessageToServer(receiverId, messageText, courseId);
    }

    sendMessageToServer(receiverId, messageText, courseId = '') {
        // Get the current URL to determine if we're in instructor or student context
        const endpoint = this.isInstructor
            ? '/instructor/messages'
            : '/student/messages';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        console.log('Sending message to server:', {
            receiverId,
            message: messageText,
            courseId,
            endpoint,
            isInstructor: this.isInstructor,
            csrfToken: csrfToken ? 'Token exists' : 'No token!'
        });

        // استخدام fetch API بدلاً من XMLHttpRequest
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                receiver_id: receiverId,
                content: messageText,
                course_id: courseId || null
            })
        })
        .then(response => {
            console.log('Fetch Response Status:', response.status);
            return response.json().catch(() => {
                // إذا فشل تحليل JSON، نعيد كائن خطأ
                throw new Error('Invalid JSON response');
            });
        })
        .then(data => {
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
        })
        .catch(error => {
            console.error('Fetch Error:', error);

            // Show error indicator on the message
            const messages = document.querySelectorAll('.message.sent:not([data-id])');
            if (messages.length > 0) {
                const lastMessage = messages[messages.length - 1];
                if (lastMessage) {
                    lastMessage.classList.add('error');
                    lastMessage.setAttribute('title', 'Error sending message: ' + error.message);
                }
            }
        });
    }

    // DOM manipulation
    addMessageToDOM(messageText, isSent = true, messageId = null, createdAt = null) {
        if (!this.messagesContainer) return;

        const messagesWrapper = this.messagesContainer.querySelector('.messages-container');
        if (!messagesWrapper) return;

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

        // Add to DOM
        messagesWrapper.appendChild(messageGroup);

        // Scroll to bottom
        this.scrollToBottom();

        if (messageId) {
            messageDiv.dataset.id = messageId;
        }

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
        if (!receiverId) return;

        // Determine the appropriate endpoint based on context
        const endpoint = this.isInstructor
            ? '/instructor/messages/get-new'
            : '/student/messages/get-new';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Create the appropriate request body based on context
        const requestData = {
            last_message_id: this.lastMessageId
        };

        if (this.isInstructor) {
            requestData.student_id = receiverId;
        } else {
            requestData.instructor_id = receiverId;
        }

        console.log('Checking for new messages:', {
            endpoint,
            receiverId,
            lastMessageId: this.lastMessageId,
            isInstructor: this.isInstructor
        });

        // استخدام fetch API
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('New messages response:', data);

            if (data && data.messages && data.messages.length > 0) {
                let addedMessages = 0;

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

        const messagesWrapper = this.messagesContainer.querySelector('.messages-container');
        if (!messagesWrapper) return;

        const messageDate = new Date(dateString).toLocaleDateString();
        const lastDivider = messagesWrapper.querySelector('.date-divider:last-of-type');

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
            messagesWrapper.appendChild(dateDivider);
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
    // Set user ID for the messaging system
    window.currentUserId = document.body.dataset.userId || null;

    // Create the messaging system instance
    window.messagingSystem = new MessagingSystem({
        typingDelay: 2000,
        checkNewMessagesInterval: 5000,
        animateNewMessages: true,
        showTypingIndicator: true,
        enableSoundEffects: false // Set to true to enable sounds (requires sound files)
    });

    // Add dark/light mode toggle if found
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
            document.body.classList.toggle('light-mode');
        });
    }
});