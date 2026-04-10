/**
 * Notification System
 * Real-time notification management for FixLanka
 * 
 * @package FixLanka
 * @version 1.0.0
 */

// Notification state
let notificationCount = 0;
let notifications = [];
let notificationInterval = null;

/**
 * Initialize notification system
 */
function initializeNotifications() {
    // Load initial notifications
    loadNotifications();
    
    // Poll for new notifications every 30 seconds
    notificationInterval = setInterval(loadNotifications, 30000);
    
    // Setup click outside to close
    document.addEventListener('click', handleOutsideClick);
}

/**
 * Load notifications from API
 */
async function loadNotifications() {
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'get_notifications'
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            notifications = result.notifications || [];
            updateNotificationUI();
        }
    } catch (error) {
        console.error('Failed to load notifications:', error);
    }
}

/**
 * Update notification UI
 */
function updateNotificationUI() {
    // Update count
    const unreadCount = notifications.filter(n => !n.is_read).length;
    notificationCount = unreadCount;
    
    const badge = document.getElementById('notificationCount');
    if (badge) {
        if (unreadCount > 0) {
            badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
    
    // Update notification list
    const list = document.getElementById('notificationList');
    if (list) {
        if (notifications.length === 0) {
            list.innerHTML = `
                <div class="no-notifications">
                    <i class="fas fa-bell-slash"></i>
                    <p>No notifications</p>
                </div>
            `;
        } else {
            list.innerHTML = notifications.map(n => createNotificationHTML(n)).join('');
        }
    }
}

/**
 * Create HTML for single notification
 * @param {Object} notification - Notification object
 * @returns {string} - HTML string
 */
function createNotificationHTML(notification) {
    const icon = getNotificationIcon(notification.notification_type);
    const timeAgo = getTimeAgo(notification.created_at);
    const readClass = notification.is_read ? 'read' : 'unread';
    const priorityClass = notification.priority || 'medium';
    
    return `
        <div class="notification-item ${readClass} priority-${priorityClass}" 
             data-notification-id="${notification.notification_id}"
             onclick="handleNotificationClick(${notification.notification_id}, '${notification.action_url || ''}')">
            <div class="notification-icon ${notification.notification_type}">
                <i class="fas fa-${icon}"></i>
            </div>
            <div class="notification-content">
                <h4>${notification.title}</h4>
                <p>${notification.message}</p>
                <span class="notification-time">
                    <i class="fas fa-clock"></i> ${timeAgo}
                </span>
            </div>
            ${!notification.is_read ? '<div class="notification-dot"></div>' : ''}
        </div>
    `;
}

/**
 * Get icon for notification type
 * @param {string} type - Notification type
 * @returns {string} - Font Awesome icon name
 */
function getNotificationIcon(type) {
    const icons = {
        'contract_sent': 'file-contract',
        'contract_accepted': 'check-circle',
        'contract_rejected': 'times-circle',
        'contract_cancelled': 'ban',
        'milestone_submitted': 'flag-checkered',
        'milestone_approved': 'thumbs-up',
        'milestone_rejected': 'thumbs-down',
        'payment_received': 'money-bill-wave',
        'payment_pending': 'hourglass-half',
        'chat_message': 'comment',
        'work_started': 'play-circle',
        'work_completed': 'check-double',
        'budget_adjustment_requested': 'hand-holding-usd',
        'budget_adjustment_approved': 'check',
        'undo_deadline_approaching': 'clock',
        'escrow_released': 'unlock',
        'invoice_generated': 'file-invoice-dollar',
        'default': 'bell'
    };
    
    return icons[type] || icons.default;
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
    return then.toLocaleDateString();
}

/**
 * Toggle notification panel
 */
function toggleNotifications() {
    const panel = document.getElementById('notificationPanel');
    if (!panel) return;
    
    const isVisible = panel.classList.contains('active');
    
    if (isVisible) {
        panel.classList.remove('active');
    } else {
        panel.classList.add('active');
        // Mark all as read after viewing
        setTimeout(markAllRead, 2000);
    }
}

/**
 * Handle notification click
 * @param {number} notificationId - Notification ID
 * @param {string} actionUrl - URL to navigate to
 */
async function handleNotificationClick(notificationId, actionUrl) {
    // Mark as read
    await markNotificationRead(notificationId);
    
    // Navigate if URL provided
    if (actionUrl && actionUrl !== '' && actionUrl !== 'null') {
        window.location.href = actionUrl;
    }
}

/**
 * Mark notification as read
 * @param {number} notificationId - Notification ID
 */
async function markNotificationRead(notificationId) {
    try {
        await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'mark_notification_read',
                notification_id: notificationId
            })
        });
        
        // Update local state
        const notification = notifications.find(n => n.notification_id === notificationId);
        if (notification) {
            notification.is_read = true;
            updateNotificationUI();
        }
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
}

/**
 * Mark all notifications as read
 */
async function markAllRead() {
    try {
        await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'mark_all_notifications_read'
            })
        });
        
        // Update local state
        notifications.forEach(n => n.is_read = true);
        updateNotificationUI();
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
}

/**
 * Handle click outside notification panel
 * @param {Event} event - Click event
 */
function handleOutsideClick(event) {
    const panel = document.getElementById('notificationPanel');
    const bell = document.querySelector('.notification-bell');
    
    if (!panel || !bell) return;
    
    if (!panel.contains(event.target) && !bell.contains(event.target)) {
        panel.classList.remove('active');
    }
}

/**
 * Cleanup on page unload
 */
function cleanupNotifications() {
    if (notificationInterval) {
        clearInterval(notificationInterval);
    }
    document.removeEventListener('click', handleOutsideClick);
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', initializeNotifications);

// Cleanup on unload
window.addEventListener('beforeunload', cleanupNotifications);

// Export functions
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initializeNotifications,
        loadNotifications,
        toggleNotifications,
        markAllRead,
        markNotificationRead
    };
}
