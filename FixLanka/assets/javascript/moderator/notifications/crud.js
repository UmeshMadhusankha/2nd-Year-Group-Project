// CRUD Operations for Notifications
// Handles Create, Read, Update, Delete operations with API

let allNotificationsData = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    console.log('[CRUD] Initializing notification CRUD module...');
    console.log('[CRUD] API URL:', window.API_URL);
    
    // Hide loading overlay initially
    hideLoadingOverlay();
    
    setupNotificationForm();
    setupEditNotificationForm();
    setupDeleteNotificationForm();
    loadNotificationsFromAPI();
});

/**
 * Setup notification creation form
 */
function setupNotificationForm() {
    const form = document.getElementById('notificationForm');
    if (!form) {
        console.error('[CRUD] Notification form not found!');
        return;
    }

    console.log('[CRUD] Setting up notification form submit handler');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent other handlers
        
        console.log('[CRUD] Form submitted - CREATE operation started');
        
        const formData = new FormData(this);
        formData.set('action', 'add');
        
        // Log form data
        console.log('[CRUD] Form data:', {
            title: formData.get('title'),
            message: formData.get('message'),
            recipients: formData.get('recipients'),
            action: formData.get('action')
        });
        
        const sendBtn = document.getElementById('sendBtn');
        const sendBtnText = document.getElementById('sendBtnText');
        
        // Disable button and show loading
        sendBtn.disabled = true;
        sendBtnText.textContent = 'Sending...';
        
        try {
            console.log('[CRUD] Fetching API:', window.API_URL);
            const response = await fetch(window.API_URL, {
                method: 'POST',
                body: formData
            });
            
            console.log('[CRUD] API Response status:', response.status);
            
            const result = await response.json();
            console.log('[CRUD] API Response data:', result);
            
            if (result.success) {
                showSuccessAlert(result.message || 'Notification sent successfully');
                form.reset();
                await loadNotificationsFromAPI();
            } else {
                showErrorAlert(result.message || 'Failed to send notification');
            }
        } catch (error) {
            console.error('[CRUD] Error sending notification:', error);
            showErrorAlert('Failed to send notification: ' + error.message);
        } finally {
            // Re-enable button
            sendBtn.disabled = false;
            sendBtnText.textContent = 'Send Now';
        }
    });
}

/**
 * Setup edit notification form
 */
function setupEditNotificationForm() {
    const form = document.getElementById('editNotificationForm');
    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const updateBtn = document.getElementById('updateBtn');
        const updateBtnText = document.getElementById('updateBtnText');
        const updateBtnLoader = document.getElementById('updateBtnLoader');
        
        // Disable button and show loader
        updateBtn.disabled = true;
        updateBtnText.style.display = 'none';
        updateBtnLoader.style.display = 'inline';
        
        try {
            const response = await fetch(window.API_URL, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showSuccessAlert(result.message || 'Notification updated successfully');
                closeEditModal();
                await loadNotificationsFromAPI();
            } else {
                showErrorAlert(result.message || 'Failed to update notification');
            }
        } catch (error) {
            console.error('Error updating notification:', error);
            showErrorAlert('Failed to update notification');
        } finally {
            // Re-enable button
            updateBtn.disabled = false;
            updateBtnText.style.display = 'inline';
            updateBtnLoader.style.display = 'none';
        }
    });
}

/**
 * Setup delete notification form
 */
function setupDeleteNotificationForm() {
    const form = document.getElementById('deleteNotificationForm');
    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch(window.API_URL, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showSuccessAlert(result.message || 'Notification deleted successfully');
                closeDeleteModal();
                await loadNotificationsFromAPI();
            } else {
                showErrorAlert(result.message || 'Failed to delete notification');
            }
        } catch (error) {
            console.error('Error deleting notification:', error);
            showErrorAlert('Failed to delete notification');
        }
    });
}

/**
 * Load notifications from API
 */
async function loadNotificationsFromAPI() {
    console.log('[CRUD] Loading notifications from API...');
    console.log('[CRUD] API URL:', `${window.API_URL}?action=getRecent&limit=5`);
    
    try {
        const response = await fetch(`${window.API_URL}?action=getRecent&limit=5`);
        console.log('[CRUD] Load response status:', response.status);
        
        const result = await response.json();
        console.log('[CRUD] Load response data:', result);
        
        if (result.success) {
            allNotificationsData = result.data;
            console.log('[CRUD] Loaded notifications:', allNotificationsData);
            renderNotificationsList(result.data);
        } else {
            console.error('[CRUD] Failed to load notifications:', result.message);
            showErrorAlert('Failed to load notifications: ' + result.message);
        }
    } catch (error) {
        console.error('[CRUD] Error fetching notifications:', error);
        showErrorAlert('Failed to load notifications: ' + error.message);
    } finally {
        // Hide loading skeleton
        const loader = document.getElementById('notificationsLoader');
        if (loader) loader.style.display = 'none';
        
        // Show notifications list
        const list = document.getElementById('notificationsList');
        if (list) list.style.display = 'block';
    }
}

/**
 * Render notifications list
 */
function renderNotificationsList(notifications) {
    const listContainer = document.getElementById('notificationsList');
    const gridContainer = document.getElementById('notificationsGrid');
    
    if (!listContainer || !gridContainer) return;

    if (notifications.length === 0) {
        const emptyHTML = `
            <div class="text-center py-8 text-muted-foreground">
                <i data-lucide="inbox" class="h-12 w-12 mx-auto mb-2 opacity-50"></i>
                <p>No notifications yet</p>
            </div>
        `;
        listContainer.innerHTML = emptyHTML;
        gridContainer.innerHTML = emptyHTML;
        listContainer.style.display = 'block';
        lucide.createIcons();
        return;
    }

    const notificationsHTML = notifications.map(notification => {
        const recipientTypeMap = {
            'all': 'All Users',
            'user': 'Customers',
            'repairer': 'Service Providers',
            'company': 'Companies'
        };

        const statusBadgeClass = {
            'sent': 'badge-sent',
            'pending': 'badge-scheduled',
            'failed': 'badge-high'
        };

        return `
            <div class="notification-item">
                <div class="notification-header">
                    <div>
                        <h4 class="font-medium text-foreground">${escapeHtml(notification.title)}</h4>
                        <p class="text-sm text-muted-foreground mt-1">${escapeHtml(notification.message)}</p>
                    </div>
                </div>
                <div class="notification-meta">
                    <div class="flex items-center gap-2">
                        <span class="badge ${statusBadgeClass[notification.status] || 'badge-draft'}">
                            ${notification.status || 'Unknown'}
                        </span>
                        <span class="text-xs text-muted-foreground">
                            ${recipientTypeMap[notification.recipient_type] || notification.recipient_type}
                        </span>
                    </div>
                    <span class="text-xs text-muted-foreground">
                        ${formatDate(notification.send_date)}
                    </span>
                </div>
                <div class="notification-footer">
                    <button onclick="editNotification(${notification.notification_id})" class="notification-action-btn edit-btn">
                        <i data-lucide="pencil" class="h-3 w-3"></i>
                        Edit
                    </button>
                    <button onclick="deleteNotificationConfirm(${notification.notification_id})" class="notification-action-btn delete-btn">
                        <i data-lucide="trash-2" class="h-3 w-3"></i>
                        Delete
                    </button>
                </div>
            </div>
        `;
    }).join('');

    listContainer.innerHTML = notificationsHTML;
    gridContainer.innerHTML = notificationsHTML;
    
    // Show list view by default
    listContainer.style.display = 'block';
    gridContainer.style.display = 'none';
    
    lucide.createIcons();
}

/**
 * Edit notification - Open modal with data
 */
function editNotification(notificationId) {
    const notification = allNotificationsData.find(n => parseInt(n.notification_id) === parseInt(notificationId));
    
    if (!notification) {
        showErrorAlert('Notification not found');
        return;
    }

    // Map recipient type back to form values
    const recipientFormMap = {
        'all': 'all',
        'user': 'customers',
        'repairer': 'providers',
        'company': 'companies'
    };

    document.getElementById('editNotificationId').value = notification.notification_id;
    document.getElementById('editTitle').value = notification.title;
    document.getElementById('editMessage').value = notification.message;
    document.getElementById('editRecipients').value = recipientFormMap[notification.recipient_type] || 'all';
    
    openEditModal();
}

/**
 * Delete notification - Show confirmation modal
 */
function deleteNotificationConfirm(notificationId) {
    document.getElementById('deleteNotificationId').value = notificationId;
    openDeleteModal();
}

/**
 * Open edit modal
 */
function openEditModal() {
    const modal = document.getElementById('editNotificationModal');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Close edit modal
 */
function closeEditModal() {
    const modal = document.getElementById('editNotificationModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

/**
 * Open delete modal
 */
function openDeleteModal() {
    const modal = document.getElementById('deleteNotificationModal');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Close delete modal
 */
function closeDeleteModal() {
    const modal = document.getElementById('deleteNotificationModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

/**
 * Show success alert
 */
function showSuccessAlert(message) {
    const alert = document.getElementById('successAlert');
    const messageEl = document.getElementById('successMessage');
    
    if (alert && messageEl) {
        messageEl.textContent = message;
        alert.classList.remove('hidden');
        
        setTimeout(() => {
            alert.classList.add('hidden');
        }, 5000);
    }
}

/**
 * Show error alert
 */
function showErrorAlert(message) {
    const alert = document.getElementById('errorAlert');
    const messageEl = document.getElementById('errorMessage');
    
    if (alert && messageEl) {
        messageEl.textContent = message;
        alert.classList.remove('hidden');
        
        setTimeout(() => {
            alert.classList.add('hidden');
        }, 5000);
    }
}

/**
 * Close success alert
 */
function closeSuccess() {
    const alert = document.getElementById('successAlert');
    if (alert) alert.classList.add('hidden');
}

/**
 * Close error alert
 */
function closeError() {
    const alert = document.getElementById('errorAlert');
    if (alert) alert.classList.add('hidden');
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Format date string
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

/**
 * Refresh notifications
 */
async function refreshNotifications() {
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.disabled = true;
        refreshBtn.innerHTML = '<i data-lucide="refresh-cw" class="mr-2 h-4 w-4 animate-spin"></i>Refreshing...';
    }
    
    await loadNotificationsFromAPI();
    
    if (refreshBtn) {
        refreshBtn.disabled = false;
        refreshBtn.innerHTML = '<i data-lucide="refresh-cw" class="mr-2 h-4 w-4"></i>Refresh';
        lucide.createIcons();
    }
}

/**
 * Hide loading overlay
 */
function hideLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

console.log('Notification CRUD module loaded successfully');
