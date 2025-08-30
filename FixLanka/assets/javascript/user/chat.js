// Fix Lanka Chat Page JavaScript
// ===============================

// DOM Elements
const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const mobileMenu = document.querySelector('.mobile-menu');
const profileAvatar = document.getElementById('profileAvatar');
const profileDropdown = document.getElementById('profileDropdown');
const backBtn = document.getElementById('backBtn');
const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
const chatSidebar = document.getElementById('chatSidebar');
const chatList = document.getElementById('chatList');
const chatMessages = document.getElementById('chatMessages');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
const attachmentBtn = document.getElementById('attachmentBtn');
const messageInputContainer = document.getElementById('messageInputContainer');
const welcomeMessage = document.getElementById('welcomeMessage');
const currentChatInfo = document.getElementById('currentChatInfo');
const currentChatAvatar = document.getElementById('currentChatAvatar');
const currentChatName = document.getElementById('currentChatName');
const currentChatStatus = document.getElementById('currentChatStatus');
const searchInput = document.getElementById('searchInput');

// Chat data
const chatData = {
    1: {
        name: "Sarah Johnson",
        avatar: "https://via.placeholder.com/50",
        status: "Online",
        messages: [
            {
                id: 1,
                sender: "them",
                text: "Hi! Thank you for booking our cleaning service. I wanted to confirm the appointment details with you.",
                time: "2:15 PM",
                avatar: "https://via.placeholder.com/36"
            },
            {
                id: 2,
                sender: "me",
                text: "Hello Sarah! Yes, I'm looking forward to it. What time works best for you tomorrow?",
                time: "2:18 PM",
                avatar: "https://via.placeholder.com/36"
            },
            {
                id: 3,
                sender: "them",
                text: "Perfect! I'll be there at 3 PM tomorrow for the cleaning service. I'll bring all the necessary equipment and eco-friendly cleaning supplies.",
                time: "2:30 PM",
                avatar: "https://via.placeholder.com/36"
            },
            {
                id: 4,
                sender: "me",
                text: "That sounds great! Should I prepare anything beforehand?",
                time: "2:32 PM",
                avatar: "https://via.placeholder.com/36"
            },
            {
                id: 5,
                sender: "them",
                text: "Just make sure the areas to be cleaned are accessible. I'll take care of everything else! See you tomorrow.",
                time: "2:35 PM",
                avatar: "https://via.placeholder.com/36"
            }
        ]
    },
    2: {
        name: "Kamal Silva",
        avatar: "https://via.placeholder.com/50",
        status: "Online",
        messages: [
            {
                id: 1,
                sender: "them",
                text: "Good afternoon! The electrical work has been completed successfully. All wiring is now up to code.",
                time: "1:00 PM",
                avatar: "https://via.placeholder.com/36"
            },
            {
                id: 2,
                sender: "me",
                text: "Excellent! How does everything look? Any issues I should be aware of?",
                time: "1:05 PM",
                avatar: "https://via.placeholder.com/36"
            },
            {
                id: 3,
                sender: "them",
                text: "Everything is working perfectly! I've tested all the circuits and outlets. You should have no issues.",
                time: "1:15 PM",
                avatar: "https://via.placeholder.com/36"
            }
        ]
    },
    3: {
        name: "Nimal Perera",
        avatar: "https://via.placeholder.com/50",
        status: "Online",
        messages: [
            {
                id: 1,
                sender: "them",
                text: "Hello! I've reviewed the plumbing issue you described. I can fix it this afternoon.",
                time: "11:30 AM",
                avatar: "https://via.placeholder.com/36"
            },
            {
                id: 2,
                sender: "me",
                text: "That would be perfect! What time would work for you?",
                time: "11:35 AM",
                avatar: "https://via.placeholder.com/36"
            },
            {
                id: 3,
                sender: "them",
                text: "I can fix the plumbing issue this afternoon. What time works for you? I'm available from 2 PM onwards.",
                time: "11:45 AM",
                avatar: "https://via.placeholder.com/36"
            }
        ]
    },
    4: {
        name: "Priya Jayasinghe",
        avatar: "https://via.placeholder.com/50",
        status: "Offline",
        messages: [
            {
                id: 1,
                sender: "them",
                text: "Thank you for choosing our HVAC service! Your air conditioning system is now running efficiently.",
                time: "Yesterday",
                avatar: "https://via.placeholder.com/36"
            },
            {
                id: 2,
                sender: "me",
                text: "Thank you so much! The difference is noticeable immediately. Great work!",
                time: "Yesterday",
                avatar: "https://via.placeholder.com/36"
            },
            {
                id: 3,
                sender: "them",
                text: "We appreciate your feedback! Don't hesitate to contact us if you need any future HVAC services.",
                time: "Yesterday",
                avatar: "https://via.placeholder.com/36"
            }
        ]
    },
    5: {
        name: "Chaminda Rathnayake",
        avatar: "https://via.placeholder.com/50",
        status: "Offline",
        messages: [
            {
                id: 1,
                sender: "them",
                text: "Good morning! I've prepared a detailed quote for the carpentry work you requested.",
                time: "Yesterday",
                avatar: "https://via.placeholder.com/36"
            },
            {
                id: 2,
                sender: "me",
                text: "Great! Could you send me the details?",
                time: "Yesterday",
                avatar: "https://via.placeholder.com/36"
            },
            {
                id: 3,
                sender: "them",
                text: "The carpentry work quote has been prepared. Please review and let me know if you have any questions.",
                time: "Yesterday",
                avatar: "https://via.placeholder.com/36"
            }
        ]
    }
};

// State variables
let currentChatId = null;
let isMobileSidebarOpen = false;

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeMobileMenu();
    initializeProfileDropdown();
    initializeChatSelection();
    initializeMessageSending();
    initializeMobileSidebar();
    initializeSearch();
    initializeBackButton();
    
    // Auto-select first chat on desktop
    if (window.innerWidth > 768) {
        selectChat('1');
    }
});

// Mobile Menu Functionality (reuse from landing page)
function initializeMobileMenu() {
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            
            // Animate hamburger menu
            const hamburgers = mobileMenuToggle.querySelectorAll('.hamburger');
            hamburgers.forEach((line, index) => {
                if (mobileMenu.classList.contains('active')) {
                    if (index === 0) line.style.transform = 'rotate(45deg) translate(5px, 5px)';
                    if (index === 1) line.style.opacity = '0';
                    if (index === 2) line.style.transform = 'rotate(-45deg) translate(7px, -6px)';
                } else {
                    line.style.transform = 'none';
                    line.style.opacity = '1';
                }
            });
        });

        // Close mobile menu when clicking on links
        const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                // Reset hamburger animation
                const hamburgers = mobileMenuToggle.querySelectorAll('.hamburger');
                hamburgers.forEach(line => {
                    line.style.transform = 'none';
                    line.style.opacity = '1';
                });
            });
        });
    }
}

// Profile Dropdown Functionality (reuse from landing page)
function initializeProfileDropdown() {
    if (profileAvatar && profileDropdown) {
        // Toggle dropdown when clicking profile avatar
        profileAvatar.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileAvatar.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('active');
            }
        });

        // Close dropdown when clicking on dropdown links
        const dropdownLinks = profileDropdown.querySelectorAll('.dropdown-link');
        dropdownLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Handle logout separately
                if (this.classList.contains('logout')) {
                    e.preventDefault();
                    handleLogout();
                } else {
                    console.log('Navigating to:', this.getAttribute('href'));
                }
                
                // Close dropdown
                profileDropdown.classList.remove('active');
            });
        });

        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && profileDropdown.classList.contains('active')) {
                profileDropdown.classList.remove('active');
            }
        });
    }
}

// Handle Logout
function handleLogout() {
    if (confirm('Are you sure you want to logout?')) {
        console.log('Logging out...');
        alert('You have been logged out successfully!');
    }
}

// Chat Selection Functionality
function initializeChatSelection() {
    if (chatList) {
        const chatItems = chatList.querySelectorAll('.chat-item');
        chatItems.forEach(item => {
            item.addEventListener('click', function() {
                const chatId = this.getAttribute('data-chat-id');
                selectChat(chatId);
                
                // Close mobile sidebar after selection
                if (window.innerWidth <= 768) {
                    closeMobileSidebar();
                }
            });
        });
    }
}

// Select and display chat
function selectChat(chatId) {
    if (!chatData[chatId]) return;
    
    currentChatId = chatId;
    const chat = chatData[chatId];
    
    // Update active chat item in sidebar
    const chatItems = chatList.querySelectorAll('.chat-item');
    chatItems.forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('data-chat-id') === chatId) {
            item.classList.add('active');
            
            // Remove unread badge
            const unreadBadge = item.querySelector('.unread-badge');
            if (unreadBadge) {
                unreadBadge.remove();
            }
        }
    });
    
    // Update header info
    updateChatHeader(chat);
    
    // Display messages
    displayMessages(chat.messages);
    
    // Show message input
    showMessageInput();
}

// Update chat header
function updateChatHeader(chat) {
    if (currentChatAvatar) {
        currentChatAvatar.src = chat.avatar;
        currentChatAvatar.alt = chat.name;
    }
    
    if (currentChatName) {
        currentChatName.textContent = chat.name;
    }
    
    if (currentChatStatus) {
        currentChatStatus.textContent = chat.status;
        currentChatStatus.className = `current-chat-status ${chat.status.toLowerCase()}`;
    }
}

// Display messages
function displayMessages(messages) {
    if (!chatMessages) return;
    
    // Hide welcome message
    if (welcomeMessage) {
        welcomeMessage.style.display = 'none';
    }
    
    // Clear existing messages
    chatMessages.innerHTML = '';
    
    // Add messages
    messages.forEach((message, index) => {
        setTimeout(() => {
            const messageElement = createMessageElement(message);
            chatMessages.appendChild(messageElement);
            
            // Scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, index * 50); // Stagger message appearance
    });
}

// Create message element
function createMessageElement(message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-bubble ${message.sender}`;
    
    messageDiv.innerHTML = `
        <img src="${message.avatar}" alt="Avatar" class="message-avatar">
        <div class="message-content">
            <p class="message-text">${message.text}</p>
            <div class="message-time">${message.time}</div>
        </div>
    `;
    
    return messageDiv;
}

// Show message input
function showMessageInput() {
    if (messageInputContainer) {
        messageInputContainer.style.display = 'block';
    }
}

// Message Sending Functionality
function initializeMessageSending() {
    if (sendBtn && messageInput) {
        // Send button click
        sendBtn.addEventListener('click', function() {
            sendMessage();
        });
        
        // Enter key to send
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
        
        // Input validation
        messageInput.addEventListener('input', function() {
            const hasText = this.value.trim().length > 0;
            sendBtn.disabled = !hasText;
        });
    }
    
    // Attachment button
    if (attachmentBtn) {
        attachmentBtn.addEventListener('click', function() {
            alert('Attachment feature coming soon!\n\nYou will be able to send:\n• Photos\n• Documents\n• Voice messages');
        });
    }
}

// Send message
function sendMessage() {
    if (!currentChatId || !messageInput) return;
    
    const messageText = messageInput.value.trim();
    if (messageText === '') return;
    
    // Show sending state
    const originalText = sendBtn.innerHTML;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    sendBtn.disabled = true;
    
    // Create new message
    const newMessage = {
        id: Date.now(),
        sender: "me",
        text: messageText,
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        avatar: "https://via.placeholder.com/36"
    };
    
    // Add to chat data
    chatData[currentChatId].messages.push(newMessage);
    
    // Display the message
    const messageElement = createMessageElement(newMessage);
    chatMessages.appendChild(messageElement);
    
    // Clear input
    messageInput.value = '';
    
    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    // Simulate sending delay
    setTimeout(() => {
        // Reset send button
        sendBtn.innerHTML = originalText;
        sendBtn.disabled = false;
        
        // Show success message
        const providerName = chatData[currentChatId].name;
        showToast(`Message sent to ${providerName}!`);
        
        // Simulate provider response (for demo)
        setTimeout(() => {
            simulateProviderResponse();
        }, 2000);
        
    }, 1000);
    
    // Focus back to input
    messageInput.focus();
}

// Simulate provider response
function simulateProviderResponse() {
    if (!currentChatId) return;
    
    const responses = [
        "Thanks for your message! I'll get back to you shortly.",
        "Got it! Let me check my schedule and confirm.",
        "Perfect! I'll make sure everything is ready.",
        "Thank you for the update. Looking forward to working with you!",
        "Understood. I'll keep you posted on the progress."
    ];
    
    const randomResponse = responses[Math.floor(Math.random() * responses.length)];
    const chat = chatData[currentChatId];
    
    const responseMessage = {
        id: Date.now(),
        sender: "them",
        text: randomResponse,
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        avatar: chat.avatar
    };
    
    // Add to chat data
    chat.messages.push(responseMessage);
    
    // Display the message
    const messageElement = createMessageElement(responseMessage);
    chatMessages.appendChild(messageElement);
    
    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    // Play notification sound (placeholder)
    console.log('New message received!');
}

// Show toast notification
function showToast(message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: var(--success-color);
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        font-weight: 500;
        animation: slideIn 0.3s ease;
    `;
    toast.textContent = message;
    
    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
    
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => {
            document.body.removeChild(toast);
            document.head.removeChild(style);
        }, 300);
    }, 3000);
}

// Mobile Sidebar Functionality
function initializeMobileSidebar() {
    if (sidebarToggleBtn && chatSidebar) {
        sidebarToggleBtn.addEventListener('click', function() {
            toggleMobileSidebar();
        });
    }
}

function toggleMobileSidebar() {
    if (window.innerWidth <= 768) {
        isMobileSidebarOpen = !isMobileSidebarOpen;
        
        if (isMobileSidebarOpen) {
            chatSidebar.classList.add('mobile-open');
            sidebarToggleBtn.innerHTML = '<i class="fas fa-times"></i><span class="sidebar-toggle-text">Close</span>';
        } else {
            closeMobileSidebar();
        }
    }
}

function closeMobileSidebar() {
    isMobileSidebarOpen = false;
    chatSidebar.classList.remove('mobile-open');
    sidebarToggleBtn.innerHTML = '<i class="fas fa-comments"></i><span class="sidebar-toggle-text">Chats</span>';
}

// Search Functionality
function initializeSearch() {
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const chatItems = chatList.querySelectorAll('.chat-item');
            
            chatItems.forEach(item => {
                const chatName = item.querySelector('.chat-name').textContent.toLowerCase();
                const chatMessage = item.querySelector('.chat-message').textContent.toLowerCase();
                
                if (chatName.includes(searchTerm) || chatMessage.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
}

// Back Button Functionality
function initializeBackButton() {
    if (backBtn) {
        backBtn.addEventListener('click', function() {
            // Navigate back or to home page
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = 'index.html';
            }
        });
    }
}

// Handle Window Resize
window.addEventListener('resize', function() {
    // Close mobile menu on resize to larger screen
    if (window.innerWidth > 768) {
        const mobileMenu = document.querySelector('.mobile-menu');
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        
        if (mobileMenu && mobileMenu.classList.contains('active')) {
            mobileMenu.classList.remove('active');
            
            // Reset hamburger animation
            if (mobileMenuToggle) {
                const hamburgers = mobileMenuToggle.querySelectorAll('.hamburger');
                hamburgers.forEach(line => {
                    line.style.transform = 'none';
                    line.style.opacity = '1';
                });
            }
        }
        
        // Close mobile sidebar
        closeMobileSidebar();
        
        // Auto-select first chat if none selected
        if (!currentChatId) {
            selectChat('1');
        }
    }
    
    // Close profile dropdown on resize
    if (profileDropdown && profileDropdown.classList.contains('active')) {
        profileDropdown.classList.remove('active');
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape key functionality
    if (e.key === 'Escape') {
        // Close mobile menu
        if (mobileMenu && mobileMenu.classList.contains('active')) {
            mobileMenu.classList.remove('active');
            mobileMenuToggle.focus();
        }
        
        // Close profile dropdown
        if (profileDropdown && profileDropdown.classList.contains('active')) {
            profileDropdown.classList.remove('active');
            profileAvatar.focus();
        }
        
        // Close mobile sidebar
        if (isMobileSidebarOpen) {
            closeMobileSidebar();
        }
    }
    
    // Focus message input with Ctrl/Cmd + /
    if ((e.ctrlKey || e.metaKey) && e.key === '/') {
        e.preventDefault();
        if (messageInput && currentChatId) {
            messageInput.focus();
        }
    }
});

// Auto-scroll chat messages when new ones arrive
function autoScrollMessages() {
    if (chatMessages) {
        const isNearBottom = chatMessages.scrollTop + chatMessages.clientHeight >= chatMessages.scrollHeight - 100;
        if (isNearBottom) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }
}

// Initialize auto-scroll observer
const messagesObserver = new MutationObserver(autoScrollMessages);
if (chatMessages) {
    messagesObserver.observe(chatMessages, { childList: true });
}

// Console log for debugging
console.log('Fix Lanka Chat Page JavaScript loaded successfully');
console.log('Available chats:', Object.keys(chatData).length);
