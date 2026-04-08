(function () {
    const userId = Number(window.NOTIFICATIONS_PAGE_USER_ID || window.CURRENT_REPAIRER_ID || 0);
    const userType = 'repairer';

    const listEl = document.getElementById('repairerNotificationsList');
    const markAllBtn = document.getElementById('markAllRepairerRead');

    const overlay = document.getElementById('repairerNotificationDetailsOverlay');
    const titleEl = document.getElementById('repairerNotificationTitle');
    const metaEl = document.getElementById('repairerNotificationMeta');
    const messageEl = document.getElementById('repairerNotificationMessage');
    const closeIconBtn = document.getElementById('closeRepairerNotificationDetails');
    const closeBtn = document.getElementById('closeRepairerNotificationBtn');
    const reportBtn = document.getElementById('reportIssueFromNotificationBtn');

    let notifications = [];
    let selectedNotification = null;

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatTime(value) {
        if (!value) return '';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return String(value);
        return date.toLocaleString();
    }

    function creatorLabel(notification) {
        const role = String(notification.created_by_role || '').toLowerCase();
        const name = String(notification.created_by_name || '').trim();
        if (name) return name;
        if (role === 'company') return 'Company';
        if (role === 'admin') return 'Admin';
        if (role === 'moderator') return 'Moderator';
        return 'System';
    }

    async function markRead(notificationId) {
        if (!notificationId) return;
        try {
            await fetch('/2nd-Year-Group-Project/FixLanka/api/notifications.php?action=mark_read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ notification_id: Number(notificationId) })
            });
        } catch (e) {
            console.warn('Failed to mark notification read', e);
        }
    }

    function renderList() {
        if (!listEl) return;

        if (!notifications.length) {
            listEl.innerHTML = `
                <div class="repairer-notifications-empty">
                    <i class="fas fa-bell-slash"></i>
                    <p>No notifications found.</p>
                </div>
            `;
            return;
        }

        listEl.innerHTML = notifications.map((n) => {
            const id = Number(n.notification_id || 0);
            const title = escapeHtml(n.title || 'Notification');
            const message = escapeHtml(n.message || '');
            const createdAt = n.created_at || n.send_date || n.updated_at || '';
            const createdBy = escapeHtml(creatorLabel(n));
            const unread = String(n.is_read ?? '0') === '0';
            return `
                <article class="repairer-notification-item ${unread ? 'unread' : ''}" data-notification-id="${id}">
                    <div class="repairer-notification-icon"><i class="fas fa-bell"></i></div>
                    <div class="repairer-notification-content">
                        <h4 class="repairer-notification-title">${title}</h4>
                        <p class="repairer-notification-message">${message}</p>
                        <div class="repairer-notification-meta">
                            <span>By: ${createdBy}</span>
                            <span>${escapeHtml(formatTime(createdAt))}</span>
                        </div>
                    </div>
                </article>
            `;
        }).join('');
    }

    function openDetails(notification) {
        if (!notification || !overlay) return;
        selectedNotification = notification;

        const title = String(notification.title || 'Notification');
        const message = String(notification.message || '');
        const createdAt = notification.created_at || notification.send_date || notification.updated_at || '';
        const createdBy = creatorLabel(notification);

        if (titleEl) titleEl.textContent = title;
        if (metaEl) {
            metaEl.textContent = `By: ${createdBy} • ${formatTime(createdAt)}`;
        }
        if (messageEl) messageEl.textContent = message;

        overlay.classList.add('active');

        const notificationId = Number(notification.notification_id || 0);
        if (notificationId) {
            markRead(notificationId);
            notifications = notifications.map((row) => (
                Number(row.notification_id || 0) === notificationId
                    ? { ...row, is_read: 1 }
                    : row
            ));
            renderList();
        }
    }

    function closeDetails() {
        if (!overlay) return;
        overlay.classList.remove('active');
    }

    function buildSupportPrefillUrl(notification) {
        const title = String(notification?.title || 'Notification issue').trim();
        const body = String(notification?.message || '').trim();
        const notificationId = Number(notification?.notification_id || 0);
        const createdBy = creatorLabel(notification || {});
        const createdAt = formatTime(notification?.created_at || notification?.send_date || notification?.updated_at || '');

        const params = new URLSearchParams();
        params.set('from_notification', '1');
        if (notificationId > 0) params.set('notification_id', String(notificationId));
        params.set('subject', `Issue about notification: ${title}`);
        params.set('message', `Notification Details:\n- Title: ${title}\n- Created by: ${createdBy}\n- Time: ${createdAt}\n\nOriginal message:\n${body}\n\nIssue:`);

        return `/2nd-Year-Group-Project/FixLanka/repairer-support?${params.toString()}`;
    }

    async function loadNotifications() {
        if (!userId) {
            if (listEl) {
                listEl.innerHTML = '<div class="repairer-notifications-empty"><p>User not found.</p></div>';
            }
            return;
        }

        try {
            const url = `/2nd-Year-Group-Project/FixLanka/api/notifications.php?action=list&limit=100&user_id=${encodeURIComponent(userId)}&user_type=${encodeURIComponent(userType)}`;
            const res = await fetch(url);
            const data = await res.json();
            notifications = (data && data.success && Array.isArray(data.notifications)) ? data.notifications : [];
            renderList();

            const queryId = Number(new URLSearchParams(window.location.search).get('notification_id') || 0);
            if (queryId > 0) {
                const target = notifications.find(n => Number(n.notification_id || 0) === queryId);
                if (target) {
                    openDetails(target);
                }
            }
        } catch (error) {
            if (listEl) {
                listEl.innerHTML = '<div class="repairer-notifications-empty"><p>Failed to load notifications.</p></div>';
            }
            console.error('Failed loading notifications:', error);
        }
    }

    if (markAllBtn) {
        markAllBtn.addEventListener('click', async function () {
            if (!userId) return;
            try {
                await fetch('/2nd-Year-Group-Project/FixLanka/api/notifications.php?action=mark_all_read', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: userId, user_type: userType })
                });
                notifications = notifications.map((row) => ({ ...row, is_read: 1 }));
                renderList();
            } catch (e) {
                console.error('Failed to mark all read', e);
            }
        });
    }

    if (listEl) {
        listEl.addEventListener('click', function (event) {
            const card = event.target.closest('.repairer-notification-item');
            if (!card) return;
            const id = Number(card.getAttribute('data-notification-id') || 0);
            const row = notifications.find((n) => Number(n.notification_id || 0) === id);
            if (row) openDetails(row);
        });
    }

    if (closeIconBtn) closeIconBtn.addEventListener('click', closeDetails);
    if (closeBtn) closeBtn.addEventListener('click', closeDetails);
    if (overlay) {
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) closeDetails();
        });
    }

    if (reportBtn) {
        reportBtn.addEventListener('click', function () {
            if (!selectedNotification) return;
            window.location.href = buildSupportPrefillUrl(selectedNotification);
        });
    }

    loadNotifications();
})();
