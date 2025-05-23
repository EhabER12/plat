// File: messaging.js - تعامل مع وظائف الرسائل في الواجهة

import './bootstrap';

// تهيئة النظام عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // عناصر واجهة المستخدم
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('message-input');
    const messagesContainer = document.getElementById('messages-container');
    const receiverId = document.getElementById('receiver-id')?.value;
    const courseId = document.getElementById('course-id')?.value;
    const lastMessageIdElement = document.getElementById('last-message-id');
    const userRole = document.getElementById('user-role')?.value;
    
    // إضافة عنصر لعرض حالة الرسائل (نجاح/فشل)
    const statusElement = document.createElement('div');
    if (messageForm) {
        statusElement.className = 'alert';
        statusElement.style.display = 'none';
        statusElement.style.marginBottom = '10px';
        messageForm.parentNode.insertBefore(statusElement, messageForm);
    }
    
    // إظهار رسالة حالة
    function showStatus(message, isError = false) {
        statusElement.textContent = message;
        statusElement.className = isError ? 'alert alert-danger' : 'alert alert-success';
        statusElement.style.display = 'block';
        
        // إخفاء بعد 5 ثوان
        setTimeout(() => {
            statusElement.style.display = 'none';
        }, 5000);
    }
    
    // إضافة مستمع لحدث النموذج
    if (messageForm) {
        messageForm.addEventListener('submit', function(event) {
            event.preventDefault();
            sendMessage();
        });
    }
    
    // إرسال رسالة عبر AJAX
    function sendMessage() {
        // التحقق من القيمة
        if (!messageInput || !messageInput.value.trim()) return;
        
        // تعطيل النموذج أثناء الإرسال
        const submitButton = messageForm.querySelector('button[type="submit"]');
        if (submitButton) submitButton.disabled = true;
        messageInput.disabled = true;
        
        // إنشاء بيانات النموذج
        const formData = new FormData();
        formData.append('content', messageInput.value);
        formData.append('receiver_id', receiverId);
        if (courseId) formData.append('course_id', courseId);
        
        // الحصول على رمز CSRF من الصفحة
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) {
            showStatus('لم يتم العثور على رمز CSRF. يرجى تحديث الصفحة.', true);
            if (submitButton) submitButton.disabled = false;
            messageInput.disabled = false;
            return;
        }
        
        // تحديد نقطة النهاية استنادًا إلى دور المستخدم
        const endpoint = userRole === 'instructor' 
            ? '/instructor/messages' 
            : '/student/messages';
        
        // إرسال طلب AJAX
        axios.post(endpoint, formData, {
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(response => {
            if (response.data.success) {
                // إضافة الرسالة المرسلة إلى واجهة المستخدم
                addMessageToContainer(response.data.message, 'sent');
                
                // مسح حقل الإدخال
                messageInput.value = '';
                
                // التمرير لأسفل
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            } else {
                showStatus('خطأ: ' + (response.data.message || 'خطأ غير معروف'), true);
            }
        })
        .catch(error => {
            console.error('خطأ في إرسال الرسالة:', error);
            showStatus('حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.', true);
        })
        .finally(() => {
            // إعادة تمكين النموذج
            if (submitButton) submitButton.disabled = false;
            messageInput.disabled = false;
        });
    }
    
    // إضافة رسالة إلى حاوية الرسائل
    function addMessageToContainer(message, type) {
        if (!messagesContainer) return;
        
        // إنشاء عنصر الرسالة
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', type === 'sent' ? 'sent' : 'received');
        messageElement.dataset.id = message.id || message.message_id;
        
        // إنشاء محتوى الرسالة
        const messageContent = document.createElement('div');
        messageContent.classList.add('message-content');
        messageContent.textContent = message.content;
        
        // إنشاء وقت الرسالة
        const messageTime = document.createElement('div');
        messageTime.classList.add('message-time');
        
        // تنسيق الوقت
        const messageDate = new Date(message.created_at);
        messageTime.textContent = messageDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        // تجميع عنصر الرسالة
        messageElement.appendChild(messageContent);
        messageElement.appendChild(messageTime);
        
        // إضافة إلى الحاوية
        messagesContainer.appendChild(messageElement);
        
        // التمرير لأسفل
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // تحديث معرف آخر رسالة للاستطلاع
        if (lastMessageIdElement) {
            lastMessageIdElement.value = message.id || message.message_id;
        }
        
        // تحديد الرسالة كمقروءة إذا كانت مستلمة
        if (type === 'received') {
            markMessageAsRead(message.id || message.message_id);
        }
    }
    
    // وضع علامة الرسالة كمقروءة
    function markMessageAsRead(messageId) {
        if (!messageId) return;
        
        const endpoint = userRole === 'instructor' 
            ? '/instructor/messages/mark-read' 
            : '/student/messages/mark-read';
            
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) return;
        
        axios.post(endpoint, {
            message_id: messageId
        }, {
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).catch(error => {
            console.error('خطأ في تحديث حالة القراءة:', error);
        });
    }
    
    // تحديث عدد الرسائل غير المقروءة لجهة اتصال في الشريط الجانبي
    function updateContactUnreadCount(senderId) {
        const contactElement = document.querySelector(`.contact-item[data-id="${senderId}"]`);
        if (!contactElement) return;
        
        const badgeElement = contactElement.querySelector('.unread-badge');
        if (badgeElement) {
            const currentCount = parseInt(badgeElement.textContent) || 0;
            badgeElement.textContent = currentCount + 1;
            badgeElement.style.display = 'flex';
        } else {
            // إنشاء شارة جديدة إذا لم تكن موجودة
            const newBadge = document.createElement('div');
            newBadge.classList.add('unread-badge');
            newBadge.textContent = '1';
            const contactInfo = contactElement.querySelector('.contact-info');
            if (contactInfo) contactInfo.appendChild(newBadge);
        }
    }
    
    // استطلاع للرسائل الجديدة
    function pollForNewMessages() {
        if (!receiverId || !lastMessageIdElement) return;
        
        const lastMessageId = lastMessageIdElement.value;
        const endpoint = userRole === 'instructor' 
            ? '/instructor/messages/get-new' 
            : '/student/messages/get-new';
            
        const requestData = userRole === 'instructor'
            ? { student_id: receiverId, last_message_id: lastMessageId }
            : { instructor_id: receiverId, last_message_id: lastMessageId };
            
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) return;
        
        axios.post(endpoint, requestData, {
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.data.success && response.data.messages && response.data.messages.length > 0) {
                response.data.messages.forEach(message => {
                    // التحقق من أن الرسالة ليست موجودة بالفعل في الحاوية
                    const messageId = message.id || message.message_id;
                    const currentUserId = document.getElementById('current-user-id')?.value;
                    if (messageId && !document.querySelector(`.message[data-id="${messageId}"]`)) {
                        const type = message.sender_id == currentUserId ? 'sent' : 'received';
                        addMessageToContainer(message, type);
                    }
                });
            }
        })
        .catch(error => {
            console.error('خطأ في استطلاع الرسائل الجديدة:', error);
        });
    }
    
    // إعداد استطلاع دوري للرسائل الجديدة
    if (receiverId) {
        // استطلاع كل 5 ثوان
        setInterval(pollForNewMessages, 5000);
        
        // استطلاع فوري عند تحميل الصفحة
        setTimeout(pollForNewMessages, 1000);
    }
    
    // استمع لأحداث Echo إذا كانت متاحة
    const currentUserId = document.getElementById('current-user-id')?.value;
    if (currentUserId && window.Echo) {
        try {
            // الاستماع للرسائل الجديدة
            window.Echo.private(`chat.${currentUserId}`)
                .listen('NewMessageSent', (e) => {
                    if (e.message.sender_id == receiverId) {
                        // إضافة الرسالة المستلمة إلى الحاوية
                        addMessageToContainer(e.message, 'received');
                    } else {
                        // تحديث عدد الرسائل غير المقروءة لجهات الاتصال الأخرى
                        updateContactUnreadCount(e.message.sender_id);
                    }
                });
            console.log('تم إعداد مستمع Echo بنجاح');
        } catch (error) {
            console.warn('فشل إعداد Echo، سيتم الاعتماد على الاستطلاع:', error);
        }
    }
}); 