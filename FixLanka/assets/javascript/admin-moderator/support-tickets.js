document.addEventListener('DOMContentLoaded', () => {
    loadSupportTickets();
    setupModalHandlers();
    setupFilters();
});

function loadSupportTickets() {
    const tbody = document.getElementById('supportTicketsBody');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="7" style="padding:24px;text-align:center;color:#64748b">Loading tickets...</td></tr>';

    const query = buildFilterQuery();

    fetch(`/2nd-Year-Group-Project/FixLanka/api/support-ticket-admin.php${query}`)
        .then(response => response.json().then(result => ({ response, result })))
        .then(({ response, result }) => {
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Failed to load tickets');
            }

            const tickets = Array.isArray(result.tickets) ? result.tickets : [];
            updateStats(tickets);
            if (tickets.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="padding:24px;text-align:center;color:#64748b">No tickets found.</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            tickets.forEach(ticket => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>#${ticket.ticket_number || ticket.ticket_id}</td>
                    <td>${ticket.user_type}</td>
                    <td>${ticket.user_id}</td>
                    <td>${ticket.title}</td>
                    <td><span class="ticket-status ${formatStatusClass(ticket.status)}">${formatStatusLabel(ticket.status)}</span></td>
                    <td>${formatDate(ticket.updated_at || ticket.created_at)}</td>
                    <td><button class="ticket-action-btn" data-ticket-id="${ticket.ticket_id}">View</button></td>
                `;
                tbody.appendChild(row);
            });

            document.querySelectorAll('.ticket-action-btn').forEach(btn => {
                btn.addEventListener('click', () => openTicketModal(btn.dataset.ticketId));
            });
        })
        .catch(error => {
            console.error(error);
            updateStats([]);
            tbody.innerHTML = `<tr><td colspan="7" style="padding:24px;text-align:center;color:#ef4444">${error.message}</td></tr>`;
        });
}

function setupFilters() {
    const userType = document.getElementById('filterUserType');
    const status = document.getElementById('filterStatus');
    const category = document.getElementById('filterCategory');
    const resetBtn = document.getElementById('filterResetBtn');

    [userType, status, category].forEach(element => {
        if (!element) return;
        element.addEventListener('change', () => loadSupportTickets());
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            if (userType) userType.value = '';
            if (status) status.value = '';
            if (category) category.value = '';
            loadSupportTickets();
        });
    }
}

function buildFilterQuery() {
    const params = new URLSearchParams();
    const userType = document.getElementById('filterUserType')?.value || '';
    const status = document.getElementById('filterStatus')?.value || '';
    const category = document.getElementById('filterCategory')?.value || '';

    if (userType) params.set('user_type', userType);
    if (status) params.set('status', status);
    if (category) params.set('category', category);

    const query = params.toString();
    return query ? `?${query}` : '';
}

function updateStats(tickets) {
    const stats = {
        total: tickets.length,
        open: 0,
        pending: 0,
        inProgress: 0,
        resolved: 0,
        closed: 0
    };

    tickets.forEach(ticket => {
        const status = normalizeStatus(ticket.status);
        if (status === 'open') stats.open += 1;
        if (status === 'pending') stats.pending += 1;
        if (status === 'in-progress') stats.inProgress += 1;
        if (status === 'resolved') stats.resolved += 1;
        if (status === 'closed') stats.closed += 1;
    });

    setStatValue('statsTotal', stats.total);
    setStatValue('statsOpen', stats.open);
    setStatValue('statsPending', stats.pending);
    setStatValue('statsInProgress', stats.inProgress);
    setStatValue('statsResolved', stats.resolved);
    setStatValue('statsClosed', stats.closed);
}

function setStatValue(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.textContent = String(value);
    }
}

function setupModalHandlers() {
    const overlay = document.getElementById('ticketModalOverlay');
    const closeBtn = document.getElementById('ticketModalClose');

    if (closeBtn) {
        closeBtn.addEventListener('click', closeTicketModal);
    }

    if (overlay) {
        overlay.addEventListener('click', event => {
            if (event.target === overlay) {
                closeTicketModal();
            }
        });
    }

    const replyForm = document.getElementById('ticketReplyForm');
    if (replyForm) {
        replyForm.addEventListener('submit', handleReplySubmit);
    }
}

function openTicketModal(ticketId) {
    const overlay = document.getElementById('ticketModalOverlay');
    if (!overlay) return;

    overlay.classList.add('active');
    overlay.dataset.ticketId = ticketId;

    const conversation = document.getElementById('ticketConversation');
    if (conversation) {
        conversation.innerHTML = '<p style="color:#64748b">Loading conversation...</p>';
    }

    fetch(`/2nd-Year-Group-Project/FixLanka/api/support-ticket-admin.php?ticket_id=${ticketId}`)
        .then(response => response.json().then(result => ({ response, result })))
        .then(({ response, result }) => {
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Failed to load ticket');
            }

            populateTicketModal(result.ticket, result.responses || []);
        })
        .catch(error => {
            if (conversation) {
                conversation.innerHTML = `<p style="color:#ef4444">${error.message}</p>`;
            }
        });
}

function populateTicketModal(ticket, responses) {
    const title = document.getElementById('ticketModalTitle');
    const meta = document.getElementById('ticketMeta');
    const conversation = document.getElementById('ticketConversation');

    if (title) {
        title.textContent = ticket.title || 'Support Ticket';
    }

    if (meta) {
        meta.innerHTML = `
            <div><strong>Ticket #</strong>${ticket.ticket_number || ticket.ticket_id}</div>
            <div><strong>User</strong>${ticket.user_type} #${ticket.user_id}</div>
            <div><strong>Category</strong>${ticket.category || 'other'}</div>
            <div><strong>Priority</strong>${ticket.priority || 'medium'}</div>
            <div><strong>Status</strong>${formatStatusLabel(ticket.status)}</div>
            <div><strong>Last Updated</strong>${formatDate(ticket.updated_at || ticket.created_at)}</div>
        `;
    }

    if (conversation) {
        conversation.innerHTML = '';

        if (ticket.description) {
            conversation.appendChild(renderMessage('Reporter', formatDate(ticket.created_at), ticket.description));
        }

        responses.forEach(response => {
            const author = response.responder_type ? response.responder_type.toUpperCase() : 'STAFF';
            conversation.appendChild(renderMessage(author, formatDate(response.created_at), response.message));
        });

        if (!ticket.description && responses.length === 0) {
            conversation.innerHTML = '<p style="color:#64748b">No conversation yet.</p>';
        }

        conversation.scrollTop = conversation.scrollHeight;
    }

    const statusSelect = document.getElementById('ticketStatusSelect');
    if (statusSelect) {
        statusSelect.value = ticket.status || 'open';
    }
}

function renderMessage(author, time, text) {
    const wrapper = document.createElement('div');
    wrapper.className = 'ticket-message';
    wrapper.innerHTML = `
        <div><span class="author">${author}</span><span class="time">${time}</span></div>
        <div class="text">${escapeHtml(text)}</div>
    `;
    return wrapper;
}

function handleReplySubmit(event) {
    event.preventDefault();

    const overlay = document.getElementById('ticketModalOverlay');
    if (!overlay) return;

    const ticketId = overlay.dataset.ticketId;
    const messageInput = document.getElementById('ticketReplyMessage');
    const statusSelect = document.getElementById('ticketStatusSelect');

    const message = messageInput ? messageInput.value.trim() : '';
    const status = statusSelect ? statusSelect.value : '';

    if (!message && !status) {
        return;
    }

    fetch('/2nd-Year-Group-Project/FixLanka/api/support-ticket-admin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            ticket_id: ticketId,
            message: message,
            status: status
        })
    })
        .then(response => response.json().then(result => ({ response, result })))
        .then(({ response, result }) => {
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Failed to send reply');
            }
            if (messageInput) messageInput.value = '';
            openTicketModal(ticketId);
            loadSupportTickets();
        })
        .catch(error => {
            console.error(error);
            alert(error.message);
        });
}

function closeTicketModal() {
    const overlay = document.getElementById('ticketModalOverlay');
    if (overlay) {
        overlay.classList.remove('active');
        overlay.dataset.ticketId = '';
    }
}

function formatDate(value) {
    if (!value) return '—';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }
    return date.toLocaleString();
}

function formatStatusClass(status) {
    return String(status || 'open').toLowerCase().replace(/_/g, '-');
}

function formatStatusLabel(status) {
    return String(status || 'open').replace(/_/g, ' ').replace(/-/g, ' ');
}

function normalizeStatus(status) {
    return String(status || 'open').toLowerCase().replace(/_/g, '-');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}
