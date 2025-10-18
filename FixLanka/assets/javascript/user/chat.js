// ================================================
// CHAT PAGE - MESSAGING, AGREEMENTS, AND PAYMENTS
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================================
    // SAMPLE CHAT DATA
    // ================================================
    const chatData = [
        {
            id: 1,
            name: "Kasun Silva",
            avatar: "https://via.placeholder.com/50",
            type: "Individual",
            lastMessage: "I can start the job tomorrow morning",
            lastMessageTime: "10:30 AM",
            unreadCount: 2,
            online: true,
            agreement: {
                status: "accepted",
                jobTitle: "Kitchen Sink Repair",
                budget: 5000,
                milestones: false
            },
            messages: [
                {
                    id: 1,
                    type: "incoming",
                    text: "Hi! I saw your job posting for kitchen sink repair. I have 5 years of experience in plumbing.",
                    time: "9:00 AM",
                    timestamp: "2025-01-17"
                },
                {
                    id: 2,
                    type: "outgoing",
                    text: "Great! Can you provide some details about your previous work?",
                    time: "9:15 AM",
                    timestamp: "2025-01-17"
                },
                {
                    id: 3,
                    type: "incoming",
                    text: "Sure! I've worked on residential and commercial projects. I can fix leaks, install new fixtures, and handle pipe replacements.",
                    time: "9:20 AM",
                    timestamp: "2025-01-17"
                },
                {
                    id: 4,
                    type: "outgoing",
                    text: "Sounds perfect! When can you start?",
                    time: "10:00 AM",
                    timestamp: "2025-01-17"
                },
                {
                    id: 5,
                    type: "incoming",
                    text: "I can start the job tomorrow morning",
                    time: "10:30 AM",
                    timestamp: "2025-01-17"
                }
            ]
        },
        {
            id: 2,
            name: "Quick Fix Solutions",
            avatar: "https://via.placeholder.com/50",
            type: "Company",
            lastMessage: "We have reviewed the milestone requirements",
            lastMessageTime: "Yesterday",
            unreadCount: 0,
            online: false,
            agreement: {
                status: "pending",
                jobTitle: "Office Deep Cleaning",
                budget: 15000,
                milestones: true,
                milestoneCount: 3,
                milestonesCompleted: 1
            },
            messages: [
                {
                    id: 1,
                    type: "incoming",
                    text: "Hello! We're interested in your deep cleaning project.",
                    time: "2:00 PM",
                    timestamp: "2025-01-16"
                },
                {
                    id: 2,
                    type: "outgoing",
                    text: "Hi! That's great. Do you have a team available for this weekend?",
                    time: "2:15 PM",
                    timestamp: "2025-01-16"
                },
                {
                    id: 3,
                    type: "incoming",
                    text: "We have reviewed the milestone requirements",
                    time: "3:00 PM",
                    timestamp: "2025-01-16"
                }
            ]
        },
        {
            id: 3,
            name: "Nimal Perera",
            avatar: "https://via.placeholder.com/50",
            type: "Individual",
            lastMessage: "What time works best for you?",
            lastMessageTime: "2 days ago",
            unreadCount: 0,
            online: true,
            agreement: null,
            messages: [
                {
                    id: 1,
                    type: "incoming",
                    text: "I'm available for the electrical wiring job you posted.",
                    time: "11:00 AM",
                    timestamp: "2025-01-15"
                },
                {
                    id: 2,
                    type: "outgoing",
                    text: "Perfect! When would be a good time for you to visit?",
                    time: "11:30 AM",
                    timestamp: "2025-01-15"
                },
                {
                    id: 3,
                    type: "incoming",
                    text: "What time works best for you?",
                    time: "12:00 PM",
                    timestamp: "2025-01-15"
                }
            ]
        },
        {
            id: 4,
            name: "HomeServe Lanka",
            avatar: "https://via.placeholder.com/50",
            type: "Company",
            lastMessage: "We can provide a detailed quote by tomorrow",
            lastMessageTime: "3 days ago",
            unreadCount: 1,
            online: false,
            agreement: null,
            messages: [
                {
                    id: 1,
                    type: "incoming",
                    text: "We offer comprehensive HVAC services. Interested in your AC maintenance job.",
                    time: "4:00 PM",
                    timestamp: "2025-01-14"
                },
                {
                    id: 2,
                    type: "outgoing",
                    text: "Great! Can you send me a quote?",
                    time: "4:30 PM",
                    timestamp: "2025-01-14"
                },
                {
                    id: 3,
                    type: "incoming",
                    text: "We can provide a detailed quote by tomorrow",
                    time: "5:00 PM",
                    timestamp: "2025-01-14"
                }
            ]
        }
    ];

    // ================================================
    // DOM ELEMENTS
    // ================================================
    const chatList = document.getElementById('chatList');
    const chatSearchInput = document.getElementById('chatSearchInput');
    const filterTabs = document.querySelectorAll('.filter-tab');
    const emptyChatState = document.getElementById('emptyChatState');
    const activeChat = document.getElementById('activeChat');
    const chatAvatar = document.getElementById('chatAvatar');
    const chatUserName = document.getElementById('chatUserName');
    const chatUserStatus = document.getElementById('chatUserStatus');
    const chatMessages = document.getElementById('chatMessages');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const attachBtn = document.getElementById('attachBtn');
    const fileInput = document.getElementById('fileInput');
    
    // Agreement modal
    const agreementModal = document.getElementById('agreementModal');
    const sendAgreementBtn = document.getElementById('sendAgreementBtn');
    const agreementModalClose = document.getElementById('agreementModalClose');
    const cancelAgreement = document.getElementById('cancelAgreement');
    const agreementForm = document.getElementById('agreementForm');
    const milestonePayment = document.getElementById('milestonePayment');
    const milestoneDetails = document.getElementById('milestoneDetails');
    const milestoneCount = document.getElementById('milestoneCount');
    const milestonesList = document.getElementById('milestonesList');
    const agreementDeadline = document.getElementById('agreementDeadline');
    const agreementBadge = document.getElementById('agreementBadge');
    const agreementStatus = document.getElementById('agreementStatus');
    
    // Payment modal
    const paymentModal = document.getElementById('paymentModal');
    const makePaymentBtn = document.getElementById('makePaymentBtn');
    const paymentModalClose = document.getElementById('paymentModalClose');
    const cancelPayment = document.getElementById('cancelPayment');
    const proceedPayment = document.getElementById('proceedPayment');
    const paymentDetails = document.getElementById('paymentDetails');
    
    // Profile and mobile
    const profileAvatar = document.getElementById('profileAvatar');
    const profileDropdown = document.getElementById('profileDropdown');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    const backToList = document.getElementById('backToList');
    const chatSidebar = document.getElementById('chatSidebar');
    
    // Toast
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');

    // ================================================
    // STATE
    // ================================================
    let currentFilter = 'all';
    let currentSearchTerm = '';
    let activeConversation = null;

    // ================================================
    // INITIALIZE
    // ================================================
    function init() {
        renderChatList();
        setupEventListeners();
        setMinDate();
    }

    // ================================================
    // RENDER CHAT LIST
    // ================================================
    function renderChatList() {
        const filteredChats = getFilteredChats();
        
        if (filteredChats.length === 0) {
            chatList.innerHTML = '<div class="empty-chat-state" style="padding: 20px; text-align: center; color: var(--text-muted);">No conversations found</div>';
            return;
        }

        chatList.innerHTML = filteredChats.map(chat => `
            <div class="chat-item ${activeConversation?.id === chat.id ? 'active' : ''}" data-chat-id="${chat.id}">
                <div class="chat-item-avatar">
                    <img src="${chat.avatar}" alt="${chat.name}">
                    ${chat.online ? '<span class="online-indicator"></span>' : ''}
                </div>
                <div class="chat-item-content">
                    <div class="chat-item-header">
                        <span class="chat-item-name">${chat.name}</span>
                        <span class="chat-item-time">${chat.lastMessageTime}</span>
                    </div>
                    <p class="chat-item-message">${chat.lastMessage}</p>
                    <div class="chat-item-footer">
                        <span class="chat-item-type">${chat.type}</span>
                        ${chat.unreadCount > 0 ? `<span class="unread-badge">${chat.unreadCount}</span>` : ''}
                    </div>
                </div>
            </div>
        `).join('');

        // Add click listeners
        document.querySelectorAll('.chat-item').forEach(item => {
            item.addEventListener('click', function() {
                const chatId = parseInt(this.dataset.chatId);
                openConversation(chatId);
            });
        });
    }

    // ================================================
    // FILTER CHATS
    // ================================================
    function getFilteredChats() {
        let filtered = chatData;

        // Filter by type
        if (currentFilter === 'companies') {
            filtered = filtered.filter(chat => chat.type === 'Company');
        } else if (currentFilter === 'individuals') {
            filtered = filtered.filter(chat => chat.type === 'Individual');
        }

        // Filter by search term
        if (currentSearchTerm) {
            filtered = filtered.filter(chat =>
                chat.name.toLowerCase().includes(currentSearchTerm.toLowerCase()) ||
                chat.lastMessage.toLowerCase().includes(currentSearchTerm.toLowerCase())
            );
        }

        return filtered;
    }

    // ================================================
    // OPEN CONVERSATION
    // ================================================
    function openConversation(chatId) {
        const chat = chatData.find(c => c.id === chatId);
        if (!chat) return;

        activeConversation = chat;
        chat.unreadCount = 0; // Mark as read

        // Update UI
        emptyChatState.style.display = 'none';
        activeChat.style.display = 'flex';

        // Update header
        chatAvatar.src = chat.avatar;
        chatUserName.textContent = chat.name;
        chatUserStatus.textContent = chat.online ? 'Online' : 'Offline';
        chatUserStatus.style.color = chat.online ? 'var(--success-color)' : 'var(--text-muted)';

        // Show/hide action buttons based on agreement status
        if (chat.agreement) {
            agreementBadge.style.display = 'flex';
            if (chat.agreement.status === 'accepted') {
                agreementStatus.textContent = 'Agreement Accepted - Payment Required';
                makePaymentBtn.style.display = 'flex';
            } else if (chat.agreement.status === 'pending') {
                agreementStatus.textContent = 'Agreement Pending Acceptance';
                makePaymentBtn.style.display = 'none';
            }
        } else {
            agreementBadge.style.display = 'none';
            makePaymentBtn.style.display = 'none';
        }

        // Render messages
        renderMessages(chat.messages);

        // Update chat list to show active state
        renderChatList();

        // Mobile: show chat panel
        if (window.innerWidth <= 1024) {
            chatSidebar.classList.remove('show');
        }
    }

    // ================================================
    // RENDER MESSAGES
    // ================================================
    function renderMessages(messages) {
        let lastTimestamp = '';
        let messagesHTML = '';

        messages.forEach((message, index) => {
            // Add timestamp separator if date changed
            if (message.timestamp !== lastTimestamp) {
                messagesHTML += `<div class="message-timestamp">${formatDate(message.timestamp)}</div>`;
                lastTimestamp = message.timestamp;
            }

            messagesHTML += `
                <div class="message ${message.type}">
                    <img src="${activeConversation.avatar}" alt="" class="message-avatar">
                    <div class="message-content">
                        <div class="message-bubble">${message.text}</div>
                        <span class="message-time">${message.time}</span>
                    </div>
                </div>
            `;
        });

        // Add milestone badge if applicable
        if (activeConversation.agreement?.milestones) {
            const completed = activeConversation.agreement.milestonesCompleted || 0;
            const total = activeConversation.agreement.milestoneCount || 0;
            messagesHTML += `
                <div class="message incoming">
                    <img src="${activeConversation.avatar}" alt="" class="message-avatar">
                    <div class="message-content">
                        <div class="message-bubble">
                            Milestone-based payment enabled
                            <span class="milestone-badge">
                                <i class="fas fa-tasks"></i>
                                ${completed}/${total} completed
                            </span>
                        </div>
                    </div>
                </div>
            `;
        }

        chatMessages.innerHTML = messagesHTML;
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // ================================================
    // SEND MESSAGE
    // ================================================
    function sendMessage() {
        const text = messageInput.value.trim();
        if (!text || !activeConversation) return;

        const newMessage = {
            id: activeConversation.messages.length + 1,
            type: 'outgoing',
            text: text,
            time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
            timestamp: new Date().toISOString().split('T')[0]
        };

        activeConversation.messages.push(newMessage);
        activeConversation.lastMessage = text;
        activeConversation.lastMessageTime = 'Just now';

        messageInput.value = '';
        renderMessages(activeConversation.messages);
        renderChatList();
    }

    // ================================================
    // AGREEMENT MODAL FUNCTIONS
    // ================================================
    function openAgreementModal() {
        if (!activeConversation) return;
        
        agreementModal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Pre-fill if agreement exists
        if (activeConversation.agreement) {
            document.getElementById('agreementJobTitle').value = activeConversation.agreement.jobTitle || '';
            document.getElementById('agreementBudget').value = activeConversation.agreement.budget || '';
        }
    }

    function closeAgreementModal() {
        agreementModal.classList.remove('show');
        document.body.style.overflow = '';
        agreementForm.reset();
        milestoneDetails.style.display = 'none';
    }

    function generateMilestones() {
        const count = parseInt(milestoneCount.value);
        const budget = parseFloat(document.getElementById('agreementBudget').value) || 0;
        const perMilestone = count > 0 ? (budget / count).toFixed(2) : 0;

        let milestonesHTML = '';
        for (let i = 1; i <= count; i++) {
            milestonesHTML += `
                <div class="milestone-item">
                    <div>
                        <label>Milestone ${i} Description</label>
                        <input 
                            type="text" 
                            class="form-input milestone-description"
                            placeholder="e.g., Initial site preparation"
                            required
                        >
                    </div>
                    <div>
                        <label>Payment Amount (LKR)</label>
                        <input 
                            type="number" 
                            class="form-input milestone-amount"
                            value="${perMilestone}"
                            min="0"
                            step="100"
                            required
                        >
                    </div>
                </div>
            `;
        }

        milestonesList.innerHTML = milestonesHTML;
    }

    function submitAgreement(e) {
        e.preventDefault();

        const formData = {
            jobTitle: document.getElementById('agreementJobTitle').value,
            description: document.getElementById('agreementDescription').value,
            deadline: document.getElementById('agreementDeadline').value,
            budget: parseFloat(document.getElementById('agreementBudget').value),
            paymentMethod: document.getElementById('paymentMethod').value,
            milestones: milestonePayment.checked,
            milestoneData: []
        };

        if (formData.milestones) {
            const descriptions = document.querySelectorAll('.milestone-description');
            const amounts = document.querySelectorAll('.milestone-amount');
            
            descriptions.forEach((desc, index) => {
                formData.milestoneData.push({
                    description: desc.value,
                    amount: parseFloat(amounts[index].value)
                });
            });

            formData.milestoneCount = formData.milestoneData.length;
        }

        console.log('Agreement created:', formData);

        // Update active conversation
        if (activeConversation) {
            activeConversation.agreement = {
                status: 'pending',
                jobTitle: formData.jobTitle,
                budget: formData.budget,
                milestones: formData.milestones,
                milestoneCount: formData.milestoneCount || 0,
                milestonesCompleted: 0
            };

            // Add system message
            const agreementMessage = {
                id: activeConversation.messages.length + 1,
                type: 'outgoing',
                text: `📄 Job agreement sent: ${formData.jobTitle} - LKR ${formData.budget.toLocaleString()}`,
                time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
                timestamp: new Date().toISOString().split('T')[0]
            };

            activeConversation.messages.push(agreementMessage);
            activeConversation.lastMessage = 'Agreement sent';
            activeConversation.lastMessageTime = 'Just now';

            renderMessages(activeConversation.messages);
            renderChatList();
        }

        closeAgreementModal();
        showToast('Agreement sent successfully!');
    }

    // ================================================
    // PAYMENT MODAL FUNCTIONS
    // ================================================
    function openPaymentModal() {
        if (!activeConversation?.agreement) return;

        const agreement = activeConversation.agreement;
        
        let detailsHTML = `
            <p><strong>Job:</strong> <span>${agreement.jobTitle}</span></p>
            <p><strong>Total Amount:</strong> <span>LKR ${agreement.budget.toLocaleString()}</span></p>
        `;

        if (agreement.milestones) {
            const completed = agreement.milestonesCompleted || 0;
            const total = agreement.milestoneCount || 0;
            const remaining = total - completed;
            
            detailsHTML += `
                <p><strong>Payment Type:</strong> <span>Milestone-based</span></p>
                <p><strong>Milestones Completed:</strong> <span>${completed}/${total}</span></p>
                <p><strong>Remaining Milestones:</strong> <span>${remaining}</span></p>
            `;
        } else {
            detailsHTML += `<p><strong>Payment Type:</strong> <span>Full Payment</span></p>`;
        }

        paymentDetails.innerHTML = detailsHTML;
        paymentModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closePaymentModal() {
        paymentModal.classList.remove('show');
        document.body.style.overflow = '';
    }

    function proceedToPayment() {
        console.log('Proceeding to payment for:', activeConversation.agreement);
        
        // Simulate payment processing
        closePaymentModal();
        showToast('Redirecting to payment gateway...');
        
        // Redirect to payment page (uncomment when ready)
        // window.location.href = 'payment.html?job=' + activeConversation.id;
        
        // For demo: update milestone completion
        setTimeout(() => {
            if (activeConversation.agreement.milestones) {
                activeConversation.agreement.milestonesCompleted += 1;
                
                if (activeConversation.agreement.milestonesCompleted >= activeConversation.agreement.milestoneCount) {
                    activeConversation.agreement.status = 'completed';
                    showToast('All milestones completed! Job is now complete.');
                } else {
                    showToast('Milestone payment successful!');
                }
                
                renderMessages(activeConversation.messages);
            }
        }, 2000);
    }

    // ================================================
    // EVENT LISTENERS
    // ================================================
    function setupEventListeners() {
        // Filter tabs
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                renderChatList();
            });
        });

        // Search
        chatSearchInput.addEventListener('input', function() {
            currentSearchTerm = this.value;
            renderChatList();
        });

        // Send message
        sendBtn.addEventListener('click', sendMessage);
        
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Auto-resize textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // File attachment
        attachBtn.addEventListener('click', () => fileInput.click());
        
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                showToast(`File selected: ${fileName}`);
            }
        });

        // Agreement modal
        sendAgreementBtn.addEventListener('click', openAgreementModal);
        agreementModalClose.addEventListener('click', closeAgreementModal);
        cancelAgreement.addEventListener('click', closeAgreementModal);
        agreementForm.addEventListener('submit', submitAgreement);

        // Milestone toggle
        milestonePayment.addEventListener('change', function() {
            milestoneDetails.style.display = this.checked ? 'block' : 'none';
            if (this.checked) {
                generateMilestones();
            }
        });

        milestoneCount.addEventListener('change', generateMilestones);
        
        document.getElementById('agreementBudget').addEventListener('input', function() {
            if (milestonePayment.checked) {
                generateMilestones();
            }
        });

        // Payment modal
        makePaymentBtn.addEventListener('click', openPaymentModal);
        paymentModalClose.addEventListener('click', closePaymentModal);
        cancelPayment.addEventListener('click', closePaymentModal);
        proceedPayment.addEventListener('click', proceedToPayment);

        // Click outside modals to close
        agreementModal.addEventListener('click', function(e) {
            if (e.target === this) closeAgreementModal();
        });

        paymentModal.addEventListener('click', function(e) {
            if (e.target === this) closePaymentModal();
        });

        // Profile dropdown
        if (profileAvatar) {
            profileAvatar.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function() {
                profileDropdown.classList.remove('show');
            });
        }

        // Mobile menu
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                mobileMenu.classList.toggle('show');
            });
        }

        // Back to chat list (mobile)
        if (backToList) {
            backToList.addEventListener('click', function() {
                chatSidebar.classList.add('show');
                activeChat.style.display = 'none';
                emptyChatState.style.display = 'flex';
            });
        }
    }

    // ================================================
    // UTILITY FUNCTIONS
    // ================================================
    function setMinDate() {
        const today = new Date().toISOString().split('T')[0];
        agreementDeadline.setAttribute('min', today);
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);

        if (date.toDateString() === today.toDateString()) {
            return 'Today';
        } else if (date.toDateString() === yesterday.toDateString()) {
            return 'Yesterday';
        } else {
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric',
                year: date.getFullYear() !== today.getFullYear() ? 'numeric' : undefined
            });
        }
    }

    function showToast(message) {
        toastMessage.textContent = message;
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // ================================================
    // INITIALIZE APP
    // ================================================
    init();
});

// ================================================
// MOBILE MENU ANIMATIONS
// ================================================
const style = document.createElement('style');
style.textContent = `
    .mobile-menu-toggle.active .hamburger:nth-child(1) {
        transform: rotate(45deg) translate(6px, 6px);
    }
    
    .mobile-menu-toggle.active .hamburger:nth-child(2) {
        opacity: 0;
    }
    
    .mobile-menu-toggle.active .hamburger:nth-child(3) {
        transform: rotate(-45deg) translate(6px, -6px);
    }
`;
document.head.appendChild(style);
