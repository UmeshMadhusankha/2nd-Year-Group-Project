document.addEventListener('DOMContentLoaded', function () {
    const listEl = document.getElementById('notificationsList');
    const markAllBtn = document.getElementById('markAllReadBtn');

    const companyId = window.NOTIFICATIONS_PAGE_USER_ID || window.CURRENT_COMPANY_ID || 0;
    const userType = 'company';

    if (!listEl) return;

    async function fetchNotifications() {
        try {
            const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/notifications.php?action=list&user_id=${companyId}&user_type=${userType}&limit=100`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to load notifications');
            }

            const notifications = Array.isArray(data.notifications) ? data.notifications : [];

            if (notifications.length === 0) {
                listEl.innerHTML = `
                    <div class="notification-empty">
                        <i class="fas fa-bell-slash"></i>
                        <p>No notifications yet</p>
                    </div>
                `;
                return;
            }

            listEl.innerHTML = notifications.map((notif) => renderNotificationItem(notif)).join('');

            // Bind click handlers to mark single notification as read
            listEl.querySelectorAll('[data-notification-id]').forEach((item) => {
                item.addEventListener('click', async function () {
                    const notificationId = parseInt(item.getAttribute('data-notification-id') || '0', 10);
                    if (!notificationId) return;

                    await markRead(notificationId);
                    item.classList.remove('unread');
                    if (typeof window.refreshNotifications === 'function') {
                        window.refreshNotifications();
                    }
                });
            });
        } catch (error) {
            console.error('Error loading notifications page:', error);
            listEl.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Failed to load notifications</p>
                </div>
            `;
        }
    }

    function safeText(value) {
        const raw = value == null ? '' : String(value);
        if (typeof window.escapeHtml === 'function') {
            return window.escapeHtml(raw);
        }
        const div = document.createElement('div');
        div.textContent = raw;
        return div.innerHTML;
    }

    function timeAgo(value) {
        if (typeof window.formatTimeAgo === 'function') {
            return window.formatTimeAgo(value);
        }
        try {
            const time = new Date(value);
            return isNaN(time.getTime()) ? '' : time.toLocaleString();
        } catch {
            return '';
        }
    }

    function renderNotificationItem(notif) {
        const id = notif.notification_id || 0;
        const isUnread = String(notif.is_read) === '0' || notif.is_read === 0 || notif.is_read === null;
        const typeClass = notif.type ? String(notif.type) : '';
        const icon = (typeof window.getNotificationIcon === 'function') ? window.getNotificationIcon(notif.type) : 'fa-bell';

        return `
            <div class="notification-item ${isUnread ? 'unread' : ''} ${typeClass}" data-notification-id="${id}">
                <div class="notification-icon">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="notification-content">
                    <h5>${safeText(notif.title || 'Notification')}</h5>
                    <p>${safeText(notif.message || '')}</p>
                    <span class="notification-time">${safeText(timeAgo(notif.created_at))}</span>
                </div>
            </div>
        `;
    }

    async function markAllRead() {
        try {
            const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/notifications.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'mark_all_read',
                    user_id: companyId,
                    user_type: userType
                })
            });

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Failed to mark all as read');
            }

            listEl.querySelectorAll('.notification-item.unread').forEach((el) => el.classList.remove('unread'));
            if (typeof window.refreshNotifications === 'function') {
                window.refreshNotifications();
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
            if (typeof window.showToast === 'function') {
                window.showToast('Failed to mark notifications as read', 'error');
            }
        }
    }

    async function markRead(notificationId) {
        try {
            await fetch('/2nd-Year-Group-Project/FixLanka/api/notifications.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'mark_read',
                    notification_id: notificationId
                })
            });
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    if (markAllBtn) {
        markAllBtn.addEventListener('click', markAllRead);
    }

    fetchNotifications();
});
