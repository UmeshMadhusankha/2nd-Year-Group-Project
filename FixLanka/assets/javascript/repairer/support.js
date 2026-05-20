// ================================================
// SUPPORT PAGE JAVASCRIPT
// ================================================

document.addEventListener('DOMContentLoaded', function () {
    // Initialize support page functionality
    initializeSupportForm();
    initializeTicketModal();
    initializeFileUpload();
    prefillSupportFromNotificationQuery();
    window.supportTickets = [];
    window.activeSupportTicketId = null;
    loadTicketsFromBackend();
});

function formatDateTime(dateValue) {
    if (!dateValue) return '—';
    const date = new Date(dateValue);
    if (Number.isNaN(date.getTime())) {
        return String(dateValue);
    }
    return date.toLocaleString();
}

function parseSubjectAndMessage(description) {
    const text = String(description || '').trim();
    if (!text) {
        return {
            subject: 'Support Issue',
            message: 'No message provided.'
        };
    }

    const splitIndex = text.indexOf('\n\n');
    if (splitIndex === -1) {
        return {
            subject: text,
            message: text
        };
    }

    return {
        subject: text.slice(0, splitIndex).trim() || 'Support Issue',
        message: text.slice(splitIndex + 2).trim() || text.slice(0, splitIndex).trim() || 'No message provided.'
    };
}

function mapIssueToTicket(issue) {
    const issueId = Number(issue.issue_id || 0);
    const parsed = parseSubjectAndMessage(issue.description);
    const createdAt = formatDateTime(issue.created_at || issue.date);
    const updatedAt = formatDateTime(issue.updated_at || issue.created_at || issue.date);

    return {
        id: issueId > 0 ? `ISSUE-${issueId}` : `ISSUE-${Date.now()}`,
        subject: parsed.subject,
        status: String(issue.status || 'pending').toLowerCase(),
        created: createdAt,
        updated: updatedAt,
        messages: [
            {
                author: 'You',
                time: createdAt,
                text: parsed.message,
                isSupport: false
            }
        ]
    };
}

function mapSupportTicketToRow(ticket) {
    const ticketId = Number(ticket.ticket_id || 0);
    return {
        id: ticketId,
        number: ticket.ticket_number || `#${ticketId}`,
        subject: ticket.title || 'Support Ticket',
        status: String(ticket.status || 'open').toLowerCase(),
        support: String(ticket.support || '').toLowerCase(),
        priority: String(ticket.priority || 'medium').toLowerCase(),
        urgency: String(ticket.urgency || 'soon').toLowerCase(),
        created: formatDateTime(ticket.created_at),
        updated: formatDateTime(ticket.updated_at || ticket.created_at)
    };
}

async function loadTicketsFromBackend(showFailureToast = false) {
    const tbody = document.getElementById('tickets-tbody');
    const emptyState = document.getElementById('tickets-empty-state');

    if (tbody) {
        tbody.style.display = '';
        if (emptyState) emptyState.style.display = 'none';
        tbody.innerHTML = `
            <tr id="tickets-loading-row">
                <td colspan="8" style="text-align:center;padding:40px;color:var(--text-secondary)">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p style="margin-top:12px">Loading tickets...</p>
                </td>
            </tr>
        `;
    }

    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/repairer-support.php', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Failed to load support tickets');
        }

        const tickets = Array.isArray(result.tickets) ? result.tickets : [];
        window.supportTickets = tickets.map(mapSupportTicketToRow);
        updateTicketsTable();
    } catch (error) {
        console.error('Failed to load support tickets:', error);
        window.supportTickets = [];
        updateTicketsTable();
        if (showFailureToast) {
            showNotification(error.message || 'Failed to load support tickets.', 'error');
        }
    }
}

function prefillSupportFromNotificationQuery() {
    const params = new URLSearchParams(window.location.search || '');
    if (params.get('from_notification') !== '1') {
        return;
    }

    const subject = params.get('subject') || '';
    const message = params.get('message') || '';
    const notificationId = params.get('notification_id') || '';

    const subjectInput = document.getElementById('issue-subject');
    const messageInput = document.getElementById('issue-message');

    if (subjectInput && subject) {
        subjectInput.value = subject;
    }
    if (messageInput && message) {
        messageInput.value = message;
    }

    if (subjectInput || messageInput) {
        scrollToForm();
        const idLabel = notificationId ? ` (#${notificationId})` : '';
        showNotification(`Notification details copied to Report Issue form${idLabel}.`, 'success');
    }
}

// ===== SUPPORT FORM HANDLING =====
function initializeSupportForm() {
    const form = document.getElementById('support-form');
    const submitBtn = document.getElementById('submit-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const newTicketBtn = document.getElementById('new-ticket-btn');
    const createFirstTicketBtn = document.getElementById('create-first-ticket');

    // Handle form submission
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            handleFormSubmission();
        });
    }

    // Handle cancel button
    if (cancelBtn) {
        cancelBtn.addEventListener('click', async function () {
            const confirmed = await window.showConfirm('Are you sure you want to cancel? All entered information will be lost.', {
                title: 'Cancel Action',
                confirmText: 'Yes, Cancel',
                type: 'warning'
            });

            if (confirmed) {
                form.reset();
                clearFileUpload();
            }
        });
    }

    // Handle new ticket button
    if (newTicketBtn) {
        newTicketBtn.addEventListener('click', function () {
            scrollToForm();
        });
    }

    // Handle create first ticket button
    if (createFirstTicketBtn) {
        createFirstTicketBtn.addEventListener('click', function () {
            scrollToForm();
        });
    }
}

async function handleFormSubmission() {
    const form = document.getElementById('support-form');
    const submitBtn = document.getElementById('submit-btn');
    const subject = document.getElementById('issue-subject').value;
    const message = document.getElementById('issue-message').value;
    const category = document.getElementById('issue-category')?.value || '';
    const support = document.getElementById('issue-support')?.value || '';
    const priority = document.getElementById('issue-priority')?.value || 'medium';
    const urgency = document.getElementById('issue-urgency')?.value || 'soon';
    const attachment = document.getElementById('issue-file')?.files?.[0];

    // Validate form
    if (!subject.trim() || !message.trim() || !category) {
        showNotification('Please fill in all required fields.', 'error');
        return;
    }

    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    submitBtn.disabled = true;

    try {
        const formData = new FormData();
        formData.append('title', subject.trim());
        formData.append('description', message.trim());
        formData.append('category', category);
        formData.append('support', support);
        formData.append('priority', priority);
        formData.append('urgency', urgency);
        if (attachment) {
            formData.append('attachment', attachment);
        }

        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/repairer-support.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Failed to submit issue report');
        }

        const ticketId = Number(result.ticket_id || 0);
        const ticketLabel = ticketId > 0 ? `#${ticketId}` : '#—';

        form.reset();
        clearFileUpload();

        await loadTicketsFromBackend(false);

        showNotification(`Support ticket ${ticketLabel} submitted successfully!`, 'success');

        setTimeout(() => {
            document.getElementById('support-tickets-section').scrollIntoView({
                behavior: 'smooth'
            });
        }, 600);
    } catch (error) {
        console.error('Issue report submission failed:', error);
        showNotification(error.message || 'Failed to submit support ticket.', 'error');
    } finally {
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit';
        submitBtn.disabled = false;
    }
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
        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                updateFileUploadUI(file);
            }
        });

        // Handle drag and drop
        fileLabel.addEventListener('dragover', function (e) {
            e.preventDefault();
            fileLabel.classList.add('drag-over');
        });

        fileLabel.addEventListener('dragleave', function (e) {
            e.preventDefault();
            fileLabel.classList.remove('drag-over');
        });

        fileLabel.addEventListener('drop', function (e) {
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
        button.addEventListener('click', function () {
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
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeTicketModal();
            }
        });
    }

    // Handle reply form
    initializeReplyForm();
}

function openTicketModal(ticketId) {
    const ticket = window.supportTickets?.find(t => String(t.id) === String(ticketId));
    if (!ticket) return;

    window.activeSupportTicketId = ticket.id;

    const modal = document.getElementById('ticket-modal-overlay');
    const modalTitle = document.getElementById('modal-ticket-title');
    const modalTicketId = document.getElementById('modal-ticket-id');
    const modalStatus = document.getElementById('modal-ticket-status');
    const modalCreated = document.getElementById('modal-ticket-created');
    const modalUpdated = document.getElementById('modal-ticket-updated');
    const chatContainer = document.getElementById('chat-container');

    // Update modal content
    if (modalTitle) modalTitle.textContent = ticket.subject;
    if (modalTicketId) modalTicketId.textContent = ticket.number ? `#${ticket.number}` : `#${ticket.id}`;
    if (modalStatus) {
        modalStatus.textContent = ticket.status.charAt(0).toUpperCase() + ticket.status.slice(1).replace('-', ' ');
        modalStatus.className = `status-badge status-${ticket.status}`;
    }
    if (modalCreated) modalCreated.textContent = ticket.created;
    if (modalUpdated) modalUpdated.textContent = ticket.updated;

    // Update chat messages
    if (chatContainer) {
        chatContainer.innerHTML = '';
    }

    loadTicketConversation(ticket.id);

    // Show modal
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

async function loadTicketConversation(ticketId) {
    const chatContainer = document.getElementById('chat-container');
    if (!chatContainer) return;

    chatContainer.innerHTML = '<p style="color:var(--text-secondary)">Loading conversation...</p>';

    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/repairer-support.php?ticket_id=${ticketId}`);
        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Failed to load conversation');
        }

        const ticket = result.ticket || {};
        const responses = Array.isArray(result.responses) ? result.responses : [];
        const messages = [];

        if (ticket.description) {
            messages.push({
                author: 'You',
                time: formatDateTime(ticket.created_at),
                text: ticket.description,
                isSupport: false
            });
        }

        responses.forEach(item => {
            const isSupport = ['admin', 'moderator', 'system'].includes(String(item.responder_type || 'user'));
            messages.push({
                author: isSupport ? 'Support' : 'You',
                time: formatDateTime(item.created_at),
                text: item.message || '',
                isSupport: isSupport
            });
        });

        chatContainer.innerHTML = '';
        if (messages.length === 0) {
            chatContainer.innerHTML = '<p style="color:var(--text-secondary)">No replies yet. Send a message to start the conversation.</p>';
            return;
        }

        messages.forEach(message => {
            const messageElement = createMessageElement(message);
            chatContainer.appendChild(messageElement);
        });
        chatContainer.scrollTop = chatContainer.scrollHeight;
    } catch (error) {
        console.error('Failed to load conversation:', error);
        chatContainer.innerHTML = '<p style="color:var(--text-secondary)">Unable to load conversation.</p>';
    }
}

function closeTicketModal() {
    const modal = document.getElementById('ticket-modal-overlay');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
    window.activeSupportTicketId = null;
}

function createMessageElement(message) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${message.isSupport ? 'support-message' : 'user-message'}`;

    const avatarDiv = document.createElement('div');
    avatarDiv.className = 'message-avatar';

    if (message.isSupport) {
        avatarDiv.innerHTML = '<div class="support-avatar"><i class="fas fa-headset"></i></div>';
    } else {
        avatarDiv.innerHTML = '<img src="/2nd-Year-Group-Project/FixLanka/assets/images/user.png" alt="You" class="avatar-img">';
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
        sendReplyBtn.addEventListener('click', function () {
            sendReply();
        });
    }

    if (attachFileBtn) {
        attachFileBtn.addEventListener('click', function () {
            showNotification('Reply attachments are not available yet. Please attach files when creating the ticket.', 'info');
        });
    }

    if (replyInput) {
        replyInput.addEventListener('keydown', function (e) {
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
    const activeTicketId = window.activeSupportTicketId;

    if (!replyInput || !replyInput.value.trim()) {
        showNotification('Please enter a message', 'error');
        return;
    }

    if (!activeTicketId) {
        showNotification('Please select a ticket to reply to.', 'error');
        return;
    }

    const message = replyInput.value.trim();

    // Show loading state
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    sendBtn.disabled = true;

    fetch('/2nd-Year-Group-Project/FixLanka/api/repairer-support.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            ticket_id: activeTicketId,
            message: message
        })
    })
        .then(response => response.json().then(result => ({ response, result })))
        .then(({ response, result }) => {
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Failed to send reply');
            }

            replyInput.value = '';
            loadTicketConversation(activeTicketId);
            showNotification('Reply sent successfully!', 'success');
        })
        .catch(error => {
            console.error('Reply failed:', error);
            showNotification(error.message || 'Failed to send reply.', 'error');
        })
        .finally(() => {
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send';
            sendBtn.disabled = false;
        });
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
        const supportText = formatTicketLabel(ticket.support);
        const priorityText = formatTicketLabel(ticket.priority);
        const urgencyText = formatTicketLabel(ticket.urgency);

        row.innerHTML = `
            <td class="ticket-id">${ticket.number ? `#${ticket.number}` : `#${ticket.id}`}</td>
            <td class="ticket-subject">${ticket.subject}</td>
            <td class="ticket-status">
                <span class="status-badge status-${statusClass}">${statusText}</span>
            </td>
            <td class="ticket-support">${supportText}</td>
            <td class="ticket-priority">${priorityText}</td>
            <td class="ticket-urgency">${urgencyText}</td>
            <td class="ticket-updated">${ticket.updated}</td>
            <td class="ticket-actions">
                <button class="btn-icon view-ticket" data-ticket-id="${ticket.id}" title="View Ticket">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        `;

        tbody.appendChild(row);
    });


function formatTicketLabel(value) {
    if (!value) return '—';
    const text = String(value).replace(/-/g, ' ').trim();
    if (!text) return '—';
    return text.charAt(0).toUpperCase() + text.slice(1);
}
    // Re-initialize view buttons
    const viewButtons = document.querySelectorAll('.view-ticket');
    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
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
