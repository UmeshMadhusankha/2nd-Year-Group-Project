// ================================================
// SUPPORT PAGE JAVASCRIPT
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize support page functionality
    initializeSupportForm();
    initializeTicketModal();
    initializeFileUpload();
    
    // Sample ticket data
    const sampleTickets = [
        {
            id: 'TKT-2025-001',
            subject: 'Payment issue with job completion',
            status: 'open',
            created: 'Aug 30, 2025 2:30 PM',
            updated: 'Aug 30, 2025 2:30 PM',
            messages: [
                {
                    author: 'You',
                    time: 'Aug 30, 2025 2:30 PM',
                    text: 'I completed a repair job yesterday but the payment hasn\'t been processed yet. The job was marked as complete by the customer but I still don\'t see the payment in my earnings. Can you please help me with this issue?',
                    isSupport: false
                },
                {
                    author: 'FixLanka Support',
                    time: 'Aug 30, 2025 3:45 PM',
                    text: 'Hello John! Thank you for contacting us about your payment issue. I understand your concern about the delayed payment processing.\n\nI\'ve checked your account and can see the completed job. Payment processing typically takes 1-2 business days after job completion. Since you completed the job yesterday, the payment should be processed by tomorrow.\n\nI\'ll monitor your case and if the payment doesn\'t appear by tomorrow evening, I\'ll escalate this to our payments team immediately.\n\nIs there anything else I can help you with regarding this issue?',
                    isSupport: true
                }
            ]
        },
        {
            id: 'TKT-2025-002',
            subject: 'Unable to upload profile picture',
            status: 'resolved',
            created: 'Aug 28, 2025 10:15 AM',
            updated: 'Aug 28, 2025 11:15 AM',
            messages: [
                {
                    author: 'You',
                    time: 'Aug 28, 2025 10:15 AM',
                    text: 'I\'m trying to upload a new profile picture but it keeps failing. I\'ve tried different image formats but none of them work.',
                    isSupport: false
                },
                {
                    author: 'FixLanka Support',
                    time: 'Aug 28, 2025 11:15 AM',
                    text: 'Hi John! I\'ve identified and fixed the issue with profile picture uploads. Please try uploading your image again. The system now supports JPG, PNG, and GIF formats up to 5MB.\n\nIf you continue to experience issues, please let us know!',
                    isSupport: true
                }
            ]
        },
        {
            id: 'TKT-2025-003',
            subject: 'App crashes on job search',
            status: 'in-progress',
            created: 'Aug 25, 2025 4:45 PM',
            updated: 'Aug 25, 2025 4:45 PM',
            messages: [
                {
                    author: 'You',
                    time: 'Aug 25, 2025 4:45 PM',
                    text: 'The mobile app crashes every time I try to search for jobs in my area. This has been happening for the past 3 days.',
                    isSupport: false
                },
                {
                    author: 'FixLanka Support',
                    time: 'Aug 26, 2025 9:30 AM',
                    text: 'Thank you for reporting this issue. Our development team is currently investigating the crash reports from the mobile app. We\'ll have a fix ready in the next app update.\n\nIn the meantime, you can use the web version to search for jobs.',
                    isSupport: true
                }
            ]
        }
    ];

    // Store tickets data
    window.supportTickets = sampleTickets;
});

// ===== SUPPORT FORM HANDLING =====
function initializeSupportForm() {
    const form = document.getElementById('support-form');
    const submitBtn = document.getElementById('submit-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const newTicketBtn = document.getElementById('new-ticket-btn');
    const createFirstTicketBtn = document.getElementById('create-first-ticket');

    // Handle form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmission();
        });
    }

    // Handle cancel button
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to cancel? All entered information will be lost.')) {
                form.reset();
                clearFileUpload();
            }
        });
    }

    // Handle new ticket button
    if (newTicketBtn) {
        newTicketBtn.addEventListener('click', function() {
            scrollToForm();
        });
    }

    // Handle create first ticket button
    if (createFirstTicketBtn) {
        createFirstTicketBtn.addEventListener('click', function() {
            scrollToForm();
        });
    }
}

function handleFormSubmission() {
    const form = document.getElementById('support-form');
    const submitBtn = document.getElementById('submit-btn');
    const subject = document.getElementById('issue-subject').value;
    const message = document.getElementById('issue-message').value;
    const fileInput = document.getElementById('issue-file');

    // Validate form
    if (!subject.trim() || !message.trim()) {
        showNotification('Please fill in all required fields.', 'error');
        return;
    }

    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    submitBtn.disabled = true;

    // Simulate API call
    setTimeout(() => {
        // Create new ticket ID
        const ticketId = `TKT-2025-${String(Date.now()).slice(-3).padStart(3, '0')}`;
        
        // Create new ticket object
        const newTicket = {
            id: ticketId,
            subject: subject,
            status: 'open',
            created: new Date().toLocaleString(),
            updated: new Date().toLocaleString(),
            messages: [
                {
                    author: 'You',
                    time: new Date().toLocaleString(),
                    text: message,
                    isSupport: false
                }
            ]
        };

        // Add to tickets array
        if (window.supportTickets) {
            window.supportTickets.unshift(newTicket);
            updateTicketsTable();
        }

        // Reset form
        form.reset();
        clearFileUpload();

        // Show success message
        showNotification(`Support ticket ${ticketId} has been created successfully!`, 'success');

        // Reset button
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit';
        submitBtn.disabled = false;

        // Scroll to tickets section
        setTimeout(() => {
            document.getElementById('support-tickets-section').scrollIntoView({ 
                behavior: 'smooth' 
            });
        }, 1000);

    }, 2000); // Simulate 2 second delay
}

function scrollToForm() {
    document.getElementById('support-form-section').scrollIntoView({ 
        behavior: 'smooth' 
    });
    document.getElementById('issue-subject').focus();
}

// ===== FILE UPLOAD HANDLING =====
function initializeFileUpload() {
    const fileInput = document.getElementById('issue-file');
    const fileLabel = document.querySelector('.file-upload-label');

    if (fileInput && fileLabel) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                updateFileUploadUI(file);
            }
        });

        // Handle drag and drop
        fileLabel.addEventListener('dragover', function(e) {
            e.preventDefault();
            fileLabel.classList.add('drag-over');
        });

        fileLabel.addEventListener('dragleave', function(e) {
            e.preventDefault();
            fileLabel.classList.remove('drag-over');
        });

        fileLabel.addEventListener('drop', function(e) {
            e.preventDefault();
            fileLabel.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                updateFileUploadUI(files[0]);
            }
        });
    }
}

function updateFileUploadUI(file) {
    const fileLabel = document.querySelector('.file-upload-label');
    const fileText = document.querySelector('.file-upload-text');
    
    if (file) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
        fileText.innerHTML = `${file.name} (${fileSize} MB)`;
        fileLabel.classList.add('file-selected');
    }
}

function clearFileUpload() {
    const fileInput = document.getElementById('issue-file');
    const fileLabel = document.querySelector('.file-upload-label');
    const fileText = document.querySelector('.file-upload-text');
    
    if (fileInput) fileInput.value = '';
    if (fileText) fileText.innerHTML = 'Choose file or drag here';
    if (fileLabel) fileLabel.classList.remove('file-selected');
}

// ===== TICKET MODAL HANDLING =====
function initializeTicketModal() {
    const modal = document.getElementById('ticket-modal-overlay');
    const closeBtn = document.getElementById('close-ticket-modal');
    const viewButtons = document.querySelectorAll('.view-ticket');

    // Handle view ticket buttons
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const ticketId = this.getAttribute('data-ticket-id');
            openTicketModal(ticketId);
        });
    });

    // Handle close button
    if (closeBtn) {
        closeBtn.addEventListener('click', closeTicketModal);
    }

    // Handle click outside modal
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeTicketModal();
            }
        });
    }

    // Handle reply form
    initializeReplyForm();
}

function openTicketModal(ticketId) {
    const ticket = window.supportTickets?.find(t => t.id === ticketId);
    if (!ticket) return;

    const modal = document.getElementById('ticket-modal-overlay');
    const modalTitle = document.getElementById('modal-ticket-title');
    const modalTicketId = document.getElementById('modal-ticket-id');
    const modalStatus = document.getElementById('modal-ticket-status');
    const modalCreated = document.getElementById('modal-ticket-created');
    const modalUpdated = document.getElementById('modal-ticket-updated');
    const chatContainer = document.getElementById('chat-container');

    // Update modal content
    if (modalTitle) modalTitle.textContent = ticket.subject;
    if (modalTicketId) modalTicketId.textContent = `#${ticket.id}`;
    if (modalStatus) {
        modalStatus.textContent = ticket.status.charAt(0).toUpperCase() + ticket.status.slice(1).replace('-', ' ');
        modalStatus.className = `status-badge status-${ticket.status}`;
    }
    if (modalCreated) modalCreated.textContent = ticket.created;
    if (modalUpdated) modalUpdated.textContent = ticket.updated;

    // Update chat messages
    if (chatContainer) {
        chatContainer.innerHTML = '';
        ticket.messages.forEach(message => {
            const messageElement = createMessageElement(message);
            chatContainer.appendChild(messageElement);
        });
    }

    // Show modal
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeTicketModal() {
    const modal = document.getElementById('ticket-modal-overlay');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function createMessageElement(message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${message.isSupport ? 'support-message' : 'user-message'}`;

    const avatarDiv = document.createElement('div');
    avatarDiv.className = 'message-avatar';

    if (message.isSupport) {
        avatarDiv.innerHTML = '<div class="support-avatar"><i class="fas fa-headset"></i></div>';
    } else {
        avatarDiv.innerHTML = '<img src="../common/user.png" alt="You" class="avatar-img">';
    }

    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';

    const headerDiv = document.createElement('div');
    headerDiv.className = 'message-header';
    headerDiv.innerHTML = `
        <span class="message-author">${message.author}</span>
        <span class="message-time">${message.time}</span>
    `;

    const textDiv = document.createElement('div');
    textDiv.className = 'message-text';
    
    // Convert line breaks to paragraphs
    const paragraphs = message.text.split('\n\n').filter(p => p.trim());
    textDiv.innerHTML = paragraphs.map(p => `<p>${p.trim()}</p>`).join('');

    contentDiv.appendChild(headerDiv);
    contentDiv.appendChild(textDiv);
    messageDiv.appendChild(avatarDiv);
    messageDiv.appendChild(contentDiv);

    return messageDiv;
}

// ===== REPLY FORM HANDLING =====
function initializeReplyForm() {
    const sendReplyBtn = document.getElementById('send-reply-btn');
    const attachFileBtn = document.getElementById('attach-file-btn');
    const replyInput = document.getElementById('reply-message');

    if (sendReplyBtn) {
        sendReplyBtn.addEventListener('click', function() {
            sendReply();
        });
    }

    if (attachFileBtn) {
        attachFileBtn.addEventListener('click', function() {
            // Create and trigger file input
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = '.jpg,.jpeg,.png,.gif,.pdf,.doc,.docx';
            fileInput.onchange = function(e) {
                const file = e.target.files[0];
                if (file) {
                    showNotification(`File "${file.name}" attached`, 'success');
                }
            };
            fileInput.click();
        });
    }

    if (replyInput) {
        replyInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
                sendReply();
            }
        });
    }
}

function sendReply() {
    const replyInput = document.getElementById('reply-message');
    const sendBtn = document.getElementById('send-reply-btn');
    const chatContainer = document.getElementById('chat-container');

    if (!replyInput || !replyInput.value.trim()) {
        showNotification('Please enter a message', 'error');
        return;
    }

    const message = replyInput.value.trim();
    
    // Show loading state
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    sendBtn.disabled = true;

    // Create new message
    const newMessage = {
        author: 'You',
        time: new Date().toLocaleString(),
        text: message,
        isSupport: false
    };

    // Add message to chat
    const messageElement = createMessageElement(newMessage);
    chatContainer.appendChild(messageElement);

    // Clear input
    replyInput.value = '';

    // Scroll to bottom
    chatContainer.scrollTop = chatContainer.scrollHeight;

    // Reset button after delay
    setTimeout(() => {
        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send';
        sendBtn.disabled = false;
        showNotification('Reply sent successfully!', 'success');
    }, 1000);
}

// ===== UTILITY FUNCTIONS =====
function updateTicketsTable() {
    const tbody = document.getElementById('tickets-tbody');
    const emptyState = document.getElementById('tickets-empty-state');
    
    if (!tbody || !window.supportTickets) return;

    if (window.supportTickets.length === 0) {
        tbody.style.display = 'none';
        if (emptyState) emptyState.style.display = 'block';
        return;
    }

    tbody.style.display = '';
    if (emptyState) emptyState.style.display = 'none';

    tbody.innerHTML = '';
    window.supportTickets.forEach(ticket => {
        const row = document.createElement('tr');
        row.className = 'ticket-row';
        row.setAttribute('data-ticket-id', ticket.id);

        const statusClass = ticket.status.toLowerCase().replace(' ', '-');
        const statusText = ticket.status.charAt(0).toUpperCase() + ticket.status.slice(1).replace('-', ' ');

        row.innerHTML = `
            <td class="ticket-id">#${ticket.id}</td>
            <td class="ticket-subject">${ticket.subject}</td>
            <td class="ticket-status">
                <span class="status-badge status-${statusClass}">${statusText}</span>
            </td>
            <td class="ticket-updated">${ticket.updated}</td>
            <td class="ticket-actions">
                <button class="btn-icon view-ticket" data-ticket-id="${ticket.id}" title="View Ticket">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        `;

        tbody.appendChild(row);
    });

    // Re-initialize view buttons
    const viewButtons = document.querySelectorAll('.view-ticket');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const ticketId = this.getAttribute('data-ticket-id');
            openTicketModal(ticketId);
        });
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button class="notification-close"><i class="fas fa-times"></i></button>
    `;

    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#4caf50' : type === 'error' ? '#f44336' : '#2196f3'};
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 400px;
        animation: slideIn 0.3s ease;
    `;

    // Add to document
    document.body.appendChild(notification);

    // Handle close button
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        notification.remove();
    });

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Add CSS animation for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .notification-close {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    .notification-close:hover {
        opacity: 1;
    }
    .file-upload-label.drag-over {
        border-color: var(--primary-color);
        background: rgba(var(--primary-rgb), 0.1);
    }
    .file-upload-label.file-selected {
        border-color: var(--success-color, #4caf50);
        background: rgba(76, 175, 80, 0.1);
    }
`;
document.head.appendChild(style);
