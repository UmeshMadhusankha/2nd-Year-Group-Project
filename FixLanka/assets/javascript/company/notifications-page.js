document.addEventListener('DOMContentLoaded', function () {
    const listEl = document.getElementById('notificationsList');
    const markAllBtn = document.getElementById('markAllReadBtn');

    const companyId = window.NOTIFICATIONS_PAGE_USER_ID || window.CURRENT_COMPANY_ID || 0;
    const userType = 'company';

    if (!listEl) return;

    let currentNotifications = [];
    let clickHandlerBound = false;

    async function fetchNotifications() {
        try {
            const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/notifications.php?action=list&user_id=${companyId}&user_type=${userType}&limit=100`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to load notifications');
            }

            const notifications = Array.isArray(data.notifications) ? data.notifications : [];
            currentNotifications = notifications;

            if (notifications.length === 0) {
                listEl.innerHTML = `
                    <div class="notification-empty">
                        <i class="fas fa-bell-slash"></i>
                        <p>No notifications yet</p>
                    </div>
                `;
                return;
            }

            // Update "last seen" timestamp so bell indicator clears after visiting this page.
            markCompanyNotificationsSeenFromList(companyId, notifications);

            listEl.innerHTML = notifications.map((notif) => renderNotificationItem(notif)).join('');

            // Bind ONE delegated click handler (more reliable than binding per-item).
            if (!clickHandlerBound) {
                clickHandlerBound = true;
                listEl.addEventListener('click', async function (e) {
                    const item = e.target.closest('[data-notification-id]');
                    if (!item || !listEl.contains(item)) return;

                    const notificationId = parseInt(item.getAttribute('data-notification-id') || '0', 10);
                    if (!notificationId) return;

                    const notif = currentNotifications.find((n) => String(n.notification_id) === String(notificationId));
                    if (notif) {
                        if (typeof window.openCompanyNotificationDetailsModal === 'function') {
                            window.openCompanyNotificationDetailsModal(notif);
                        } else {
                            openLocalNotificationDetailsModal(notif);
                        }
                    }

                    // Best-effort: mark read in backend if supported.
                    await markRead(notificationId);
                    item.classList.remove('unread');
                    if (typeof window.refreshNotifications === 'function') {
                        window.refreshNotifications();
                    }
                });
            }
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

    function closeLocalNotificationDetailsModal() {
        const existing = document.getElementById('companyNotificationDetailsModal');
        if (existing) existing.remove();
        document.body.style.overflow = '';
        document.removeEventListener('keydown', onLocalModalKeyDown);
    }

    function onLocalModalKeyDown(e) {
        if (e.key === 'Escape') closeLocalNotificationDetailsModal();
    }

    function openLocalNotificationDetailsModal(notification) {
        if (!notification || typeof notification !== 'object') return;

        closeLocalNotificationDetailsModal();

        const title = notification.title || 'Notification';
        const message = notification.message || '';
        const createdAt = notification.created_at || notification.send_date || '';

        const overlay = document.createElement('div');
        overlay.className = 'notification-details-overlay';
        overlay.id = 'companyNotificationDetailsModal';
        overlay.innerHTML = `
            <div class="notification-details-modal" role="dialog" aria-modal="true" aria-label="Notification details">
                <div class="notification-details-header">
                    <h3 class="notification-details-title">${safeText(title)}</h3>
                    <button type="button" class="notification-details-close" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="notification-details-body">
                    <div class="notification-details-meta">${safeText(createdAt ? timeAgo(createdAt) : '')}</div>
                    <p class="notification-details-message">${safeText(String(message))}</p>
                </div>
                <div class="notification-details-footer">
                    <button type="button" class="btn btn-secondary notification-details-close-btn">Close</button>
                </div>
            </div>
        `;

        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';

        // If CSS didn't apply (cached/old), force minimal inline styles so the modal is still usable.
        try {
            const overlayPos = window.getComputedStyle(overlay).position;
            if (overlayPos !== 'fixed') {
                overlay.style.cssText = [
                    'position:fixed',
                    'inset:0',
                    'background:rgba(0,0,0,0.35)',
                    'z-index:10001',
                    'display:flex',
                    'align-items:center',
                    'justify-content:center',
                    'padding:20px'
                ].join(';');
            }

            const modal = overlay.querySelector('.notification-details-modal');
            if (modal) {
                const bg = window.getComputedStyle(modal).backgroundColor;
                if (bg === 'rgba(0, 0, 0, 0)' || bg === 'transparent') {
                    modal.style.cssText = [
                        'width:520px',
                        'max-width:100%',
                        'background:rgba(255,255,255,0.98)',
                        'border-radius:16px',
                        'overflow:hidden',
                        'box-shadow:0 10px 40px -10px rgba(0,0,0,0.2),0 0 0 1px rgba(0,0,0,0.06)'
                    ].join(';');
                }
            }
        } catch {
            // ignore
        }
        overlay.querySelectorAll('.notification-details-close, .notification-details-close-btn')
            .forEach((btn) => btn.addEventListener('click', closeLocalNotificationDetailsModal));

        overlay.addEventListener('click', function (e) {
            const modal = overlay.querySelector('.notification-details-modal');
            if (modal && !modal.contains(e.target)) closeLocalNotificationDetailsModal();
        });

        document.addEventListener('keydown', onLocalModalKeyDown);
    }

    function getCompanyNotificationsStorageKey(companyId) {
        return `fixlanka:company:${companyId}:notificationsLastSeenAt`;
    }

    function setCompanyNotificationsLastSeenMs(companyId, ms) {
        try {
            const iso = new Date(ms).toISOString();
            localStorage.setItem(getCompanyNotificationsStorageKey(companyId), iso);
        } catch {
            // ignore
        }
    }

    function getNotificationTimeMs(notif) {
        if (!notif || typeof notif !== 'object') return 0;
        const candidates = [notif.created_at, notif.send_date];
        for (const value of candidates) {
            if (!value) continue;
            const ms = Date.parse(value);
            if (Number.isFinite(ms)) return ms;
        }
        if (notif.date && notif.time) {
            const ms = Date.parse(`${notif.date} ${notif.time}`);
            if (Number.isFinite(ms)) return ms;
        }
        return 0;
    }

    function markCompanyNotificationsSeenFromList(companyId, notifications) {
        const times = (Array.isArray(notifications) ? notifications : [])
            .map(getNotificationTimeMs)
            .filter((t) => Number.isFinite(t) && t > 0);
        if (times.length === 0) {
            setCompanyNotificationsLastSeenMs(companyId, Date.now());
            return;
        }
        const latest = Math.max(...times);
        setCompanyNotificationsLastSeenMs(companyId, latest);
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
                    <span class="notification-time">${safeText(timeAgo(notif.created_at || notif.send_date))}</span>
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
