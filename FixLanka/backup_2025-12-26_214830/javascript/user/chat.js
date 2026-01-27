// Chat Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const messagesArea = document.getElementById('messagesArea');
    const conversationItems = document.querySelectorAll('.conversation-item');
    const searchInput = document.getElementById('searchConversations');

    // Auto-resize textarea
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Send message on Enter (without Shift)
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    // Send button click
    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }

    // Send message function
    function sendMessage() {
        const message = messageInput.value.trim();
        
        if (message === '') return;

        // Create message element
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message sent';
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        
        const messageBubble = document.createElement('div');
        messageBubble.className = 'message-bubble';
        
        const messageText = document.createElement('p');
        messageText.textContent = message;
        
        const messageTime = document.createElement('span');
        messageTime.className = 'message-time';
        messageTime.textContent = getCurrentTime();
        
        messageBubble.appendChild(messageText);
        messageContent.appendChild(messageBubble);
        messageContent.appendChild(messageTime);
        messageDiv.appendChild(messageContent);
        
        // Add to messages area
        messagesArea.appendChild(messageDiv);
        
        // Clear input
        messageInput.value = '';
        messageInput.style.height = 'auto';
        
        // Scroll to bottom
        scrollToBottom();
        
        // Simulate typing indicator (optional)
        // showTypingIndicator();
    }

    // Get current time in 12-hour format
    function getCurrentTime() {
        const now = new Date();
        let hours = now.getHours();
        const minutes = now.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        const minutesStr = minutes < 10 ? '0' + minutes : minutes;
        return hours + ':' + minutesStr + ' ' + ampm;
    }

    // Scroll to bottom of messages
    function scrollToBottom() {
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    // Conversation item click
    conversationItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all
            conversationItems.forEach(i => i.classList.remove('active'));
            
            // Add active class to clicked item
            this.classList.add('active');
            
            // Get user info
            const userName = this.querySelector('.conversation-name').textContent;
            const userAvatar = this.querySelector('.conversation-avatar img').src;
            
            // Update chat header
            const chatUserName = document.querySelector('.chat-user-name');
            const chatAvatar = document.querySelector('.chat-avatar img');
            
            if (chatUserName) chatUserName.textContent = userName;
            if (chatAvatar) chatAvatar.src = userAvatar;
            
            // Clear messages (in real app, load conversation)
            // For demo, we'll keep existing messages
            
            // Remove unread badge
            const unreadBadge = this.querySelector('.unread-badge');
            if (unreadBadge) {
                unreadBadge.remove();
            }
        });
    });

    // Search conversations
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            conversationItems.forEach(item => {
                const name = item.querySelector('.conversation-name').textContent.toLowerCase();
                const message = item.querySelector('.last-message').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || message.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Typing indicator (optional)
    function showTypingIndicator() {
        const typingIndicator = document.querySelector('.typing-indicator');
        if (typingIndicator) {
            typingIndicator.style.display = 'flex';
            scrollToBottom();
            
            // Hide after 2 seconds (simulate response)
            setTimeout(() => {
                typingIndicator.style.display = 'none';
                // You can add a received message here
            }, 2000);
        }
    }

    // Initial scroll to bottom
    scrollToBottom();

    // Info panel toggle (if you want to add this functionality)
    const chatActionBtns = document.querySelectorAll('.chat-action-btn');
    const chatInfoPanel = document.getElementById('chatInfoPanel');
    const closePanelBtn = document.getElementById('closePanelBtn');

    if (chatActionBtns.length > 2) {
        // The third button (More Options) toggles info panel
        chatActionBtns[2].addEventListener('click', function() {
            if (chatInfoPanel) {
                chatInfoPanel.style.display = chatInfoPanel.style.display === 'none' ? 'block' : 'none';
            }
        });
    }

    if (closePanelBtn && chatInfoPanel) {
        closePanelBtn.addEventListener('click', function() {
            chatInfoPanel.style.display = 'none';
        });
    }

    // Mobile: Toggle conversations sidebar
    const menuToggle = document.querySelector('.menu-toggle');
    const conversationsSidebar = document.querySelector('.conversations-sidebar');
    
    if (menuToggle && conversationsSidebar) {
        menuToggle.addEventListener('click', function() {
            conversationsSidebar.classList.toggle('show');
        });
    }

    // Click outside to close sidebar on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (conversationsSidebar && 
                !conversationsSidebar.contains(e.target) && 
                !e.target.classList.contains('menu-toggle')) {
                conversationsSidebar.classList.remove('show');
            }
        }
    });
});
