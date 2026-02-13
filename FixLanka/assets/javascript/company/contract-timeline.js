/**
 * Contract Timeline System
 * Visual timeline display of all contract events
 * 
 * @package FixLanka
 * @version 1.0.0
 */

/**
 * Load and display contract timeline
 * @param {number} contractId - Contract ID
 * @param {string} containerId - Timeline container element ID
 */
async function loadContractTimeline(contractId, containerId) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`Timeline container ${containerId} not found`);
        return;
    }
    
    // Show loading
    container.innerHTML = `
        <div class="timeline-loading">
            <div class="spinner"></div>
            <p>Loading timeline...</p>
        </div>
    `;
    
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'get_timeline',
                contract_id: contractId
            })
        });
        
        const result = await response.json();
        
        if (result.success && result.timeline) {
            displayTimeline(result.timeline, container);
        } else {
            container.innerHTML = `
                <div class="timeline-empty">
                    <i class="fas fa-history"></i>
                    <p>No timeline events yet</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Load timeline error:', error);
        container.innerHTML = `
            <div class="timeline-error">
                <i class="fas fa-exclamation-circle"></i>
                <p>Failed to load timeline</p>
            </div>
        `;
    }
}

/**
 * Display timeline events
 * @param {Array} events - Array of timeline events
 * @param {HTMLElement} container - Container element
 */
function displayTimeline(events, container) {
    if (!events || events.length === 0) {
        container.innerHTML = `
            <div class="timeline-empty">
                <i class="fas fa-history"></i>
                <p>No events recorded yet</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="timeline-wrapper">';
    
    events.forEach((event, index) => {
        html += createTimelineEvent(event, index === events.length - 1);
    });
    
    html += '</div>';
    container.innerHTML = html;
}

/**
 * Create HTML for single timeline event
 * @param {Object} event - Event object
 * @param {boolean} isLast - Is this the last event?
 * @returns {string} - HTML string
 */
function createTimelineEvent(event, isLast) {
    const icon = getTimelineIcon(event.event_type);
    const color = getTimelineColor(event.event_type);
    const timeAgo = getTimeAgo(event.created_at);
    
    return `
        <div class="timeline-event ${event.event_type}" data-event-id="${event.timeline_id}">
            <div class="timeline-marker" style="background: ${color}">
                <i class="fas fa-${icon}"></i>
            </div>
            ${!isLast ? '<div class="timeline-line"></div>' : ''}
            <div class="timeline-content">
                <div class="timeline-header">
                    <h4>${event.event_title}</h4>
                    <span class="timeline-time">${timeAgo}</span>
                </div>
                ${event.event_description ? `<p class="timeline-description">${event.event_description}</p>` : ''}
                ${event.actor_name ? `<span class="timeline-actor"><i class="fas fa-user"></i> ${event.actor_name}</span>` : ''}
                ${event.is_milestone ? '<span class="timeline-milestone-badge"><i class="fas fa-flag"></i> Milestone</span>' : ''}
            </div>
        </div>
    `;
}

/**
 * Get icon for event type
 * @param {string} eventType - Event type
 * @returns {string} - Font Awesome icon name
 */
function getTimelineIcon(eventType) {
    const icons = {
        'created': 'file-contract',
        'sent': 'paper-plane',
        'accepted': 'check-circle',
        'rejected': 'times-circle',
        'cancelled': 'ban',
        'cancellation': 'ban',
        'milestone_created': 'flag',
        'milestone_submitted': 'flag-checkered',
        'milestone_approved': 'thumbs-up',
        'milestone_rejected': 'thumbs-down',
        'milestone_completed': 'check-double',
        'payment_received': 'money-bill-wave',
        'payment_released': 'hand-holding-usd',
        'work_started': 'play-circle',
        'work_completed': 'check',
        'chat_initiated': 'comments',
        'budget_adjusted': 'chart-line',
        'contract_updated': 'edit',
        'escrow_funded': 'lock',
        'escrow_released': 'unlock',
        'invoice_generated': 'file-invoice-dollar',
        'default': 'circle'
    };
    return icons[eventType] || icons.default;
}

/**
 * Get color for event type
 * @param {string} eventType - Event type
 * @returns {string} - Color code
 */
function getTimelineColor(eventType) {
    const colors = {
        'created': '#6c757d',
        'sent': '#007bff',
        'accepted': '#28a745',
        'rejected': '#dc3545',
        'cancelled': '#dc3545',
        'cancellation': '#dc3545',
        'milestone_created': '#17a2b8',
        'milestone_submitted': '#17a2b8',
        'milestone_approved': '#28a745',
        'milestone_rejected': '#dc3545',
        'milestone_completed': '#28a745',
        'payment_received': '#28a745',
        'payment_released': '#28a745',
        'work_started': '#007bff',
        'work_completed': '#28a745',
        'chat_initiated': '#6f42c1',
        'budget_adjusted': '#ffc107',
        'contract_updated': '#17a2b8',
        'escrow_funded': '#007bff',
        'escrow_released': '#28a745',
        'invoice_generated': '#17a2b8',
        'default': '#6c757d'
    };
    return colors[eventType] || colors.default;
}

/**
 * Get human-readable time ago
 * @param {string} datetime - Datetime string
 * @returns {string} - Time ago string
 */
function getTimeAgo(datetime) {
    const now = new Date();
    const then = new Date(datetime);
    const diffMs = now - then;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
    if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
    if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
    if (diffDays < 30) {
        const weeks = Math.floor(diffDays / 7);
        return `${weeks} week${weeks > 1 ? 's' : ''} ago`;
    }
    return then.toLocaleDateString();
}

/**
 * Export timeline as PDF
 * @param {number} contractId - Contract ID
 */
async function exportTimelinePDF(contractId) {
    alert('PDF export feature coming soon!');
    // TODO: Implement PDF generation
}

// Export functions
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        loadContractTimeline,
        exportTimelinePDF
    };
}
