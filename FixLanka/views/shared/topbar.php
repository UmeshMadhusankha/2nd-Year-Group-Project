<?php
/**
 * Shared role-aware topbar renderer.
 *
 * Expected config keys:
 * - role, userId, userName, userEmail, avatarUrl
 * - pageTitle, pageSlogan
 * - profileLinks: [['href' => '', 'label' => '', 'icon' => 'fas fa-user']]
 * - notificationsPageUrl
 * - searchPlaceholder
 * - searchEndpoint (optional)
 * - searchCategories (optional)
 * - quickSearchLinks (optional)
 */
if (!function_exists('renderSharedTopbar')) {
    function renderSharedTopbar(array $cfg = []): void
    {
        $role = (string)($cfg['role'] ?? 'company');
        $userId = (int)($cfg['userId'] ?? 0);
        $userName = (string)($cfg['userName'] ?? 'User');
        $userEmail = (string)($cfg['userEmail'] ?? '');
        $avatarUrl = (string)($cfg['avatarUrl'] ?? '/2nd-Year-Group-Project/FixLanka/assets/images/user.png');
        $pageTitle = (string)($cfg['pageTitle'] ?? 'Dashboard');
        $pageSlogan = (string)($cfg['pageSlogan'] ?? 'Welcome to FixLanka');
        $searchPlaceholder = (string)($cfg['searchPlaceholder'] ?? 'Search...');
        $notificationsPageUrl = (string)($cfg['notificationsPageUrl'] ?? '#');
        $searchEndpoint = (string)($cfg['searchEndpoint'] ?? '');
        $searchCategories = is_array($cfg['searchCategories'] ?? null) ? $cfg['searchCategories'] : [];
        $quickSearchLinks = is_array($cfg['quickSearchLinks'] ?? null) ? $cfg['quickSearchLinks'] : [];
        $profileLinks = is_array($cfg['profileLinks'] ?? null) ? $cfg['profileLinks'] : [];

        $initials = '';
        if (!empty($userName)) {
            $parts = preg_split('/\s+/', trim($userName));
            if (!empty($parts[0])) {
                $initials .= strtoupper(substr($parts[0], 0, 1));
            }
            if (!empty($parts[1])) {
                $initials .= strtoupper(substr($parts[1], 0, 1));
            }
        }

        if (empty($searchCategories)) {
            $searchCategories = [
                ['value' => 'all', 'label' => 'All']
            ];
        }

        $showCategorySearch = count($searchCategories) > 1;
        $topbarState = [
            'role' => $role,
            'userId' => $userId,
            'userName' => $userName,
            'userEmail' => $userEmail,
            'searchEndpoint' => $searchEndpoint,
            'notificationsPageUrl' => $notificationsPageUrl,
            'quickSearchLinks' => $quickSearchLinks,
            'searchCategories' => $searchCategories
        ];
        ?>
        <header class="header">
            <div class="header-left">
                <label for="sidebar-toggle" class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </label>
                <div class="logo">
                    <img src="/2nd-Year-Group-Project/FixLanka/assets/images/fixlanka.png" alt="FixLanka" class="logo-image">
                </div>
                <div class="page-info">
                    <h1 class="page-title" id="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
                    <p class="page-slogan page-subtitle" id="page-slogan"><?php echo htmlspecialchars($pageSlogan); ?></p>
                </div>
            </div>

            <div class="header-right">
                <div class="search-box">
                    <?php if ($showCategorySearch): ?>
                        <div class="custom-select-wrapper" id="searchCategoryWrapper">
                            <div class="custom-select-trigger">
                                <span id="selectedCategoryText"><?php echo htmlspecialchars((string)$searchCategories[0]['label']); ?></span>
                                <i class="fas fa-chevron-down category-icon"></i>
                            </div>
                            <div class="custom-options">
                                <?php foreach ($searchCategories as $idx => $cat): ?>
                                    <span class="custom-option <?php echo $idx === 0 ? 'selected' : ''; ?>" data-value="<?php echo htmlspecialchars((string)$cat['value']); ?>">
                                        <?php echo htmlspecialchars((string)$cat['label']); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" id="searchCategory" value="<?php echo htmlspecialchars((string)$searchCategories[0]['value']); ?>">
                        </div>
                        <div class="search-divider"></div>
                    <?php else: ?>
                        <i class="fas fa-search"></i>
                    <?php endif; ?>
                    <input type="text" id="globalSearchInput" placeholder="<?php echo htmlspecialchars($searchPlaceholder); ?>">
                    <div id="searchResults" class="search-results-dropdown"></div>
                </div>

                <div class="notification-bell" id="notificationBell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationCount" style="display:none;">0</span>
                </div>

                <div class="profile-menu" id="profileMenu">
                    <div class="profile-trigger">
                        <?php if (!empty($avatarUrl) && strpos($avatarUrl, 'user.png') === false): ?>
                            <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Profile" class="profile-avatar">
                        <?php elseif (!empty($initials)): ?>
                            <div class="profile-avatar profile-avatar-initials"><?php echo htmlspecialchars($initials); ?></div>
                        <?php else: ?>
                            <img src="/2nd-Year-Group-Project/FixLanka/assets/images/user.png" alt="Profile" class="profile-avatar">
                        <?php endif; ?>
                        <div class="profile-status-indicator"></div>
                    </div>

                    <div class="dropdown-menu profile-dropdown" id="profileDropdown">
                        <div class="dropdown-header profile-dropdown-header">
                            <?php if (!empty($avatarUrl) && strpos($avatarUrl, 'user.png') === false): ?>
                                <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Profile" class="dropdown-avatar profile-avatar">
                            <?php elseif (!empty($initials)): ?>
                                <div class="dropdown-avatar dropdown-avatar-initials"><?php echo htmlspecialchars($initials); ?></div>
                            <?php else: ?>
                                <img src="/2nd-Year-Group-Project/FixLanka/assets/images/user.png" alt="Profile" class="dropdown-avatar profile-avatar">
                            <?php endif; ?>
                            <div class="dropdown-user-info">
                                <h4 class="profile-dropdown-name"><?php echo htmlspecialchars($userName); ?></h4>
                                <p class="profile-dropdown-email"><?php echo htmlspecialchars($userEmail); ?></p>
                            </div>
                        </div>
                        <ul class="profile-dropdown-menu">
                            <?php foreach ($profileLinks as $link): ?>
                                <li class="dropdown-item profile-dropdown-item">
                                    <a href="<?php echo htmlspecialchars((string)($link['href'] ?? '#')); ?>" class="dropdown-item profile-dropdown-link">
                                        <i class="<?php echo htmlspecialchars((string)($link['icon'] ?? 'fas fa-circle')); ?>"></i>
                                        <span><?php echo htmlspecialchars((string)($link['label'] ?? 'Link')); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li class="dropdown-divider profile-dropdown-divider"></li>
                            <li class="dropdown-item profile-dropdown-item">
                                <a href="/2nd-Year-Group-Project/FixLanka/logout" class="dropdown-item profile-dropdown-link logout" onclick="return confirm('Are you sure you want to logout?');">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <style>
            .notification-bell {
                position: relative;
            }

            .notification-badge {
                min-width: 18px;
                height: 18px;
                display: none;
                align-items: center;
                justify-content: center;
            }

            .notification-dropdown {
                width: 360px;
                max-height: min(420px, calc(100vh - 90px));
                border-radius: 16px;
                overflow: hidden;
                background: rgba(255, 255, 255, 0.98);
                box-shadow: 0 18px 50px rgba(15, 23, 42, 0.18);
                border: 1px solid rgba(148, 163, 184, 0.18);
            }

            .notification-dropdown-header,
            .notification-dropdown-footer {
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(10px);
            }

            .notification-dropdown-list {
                max-height: min(320px, calc(100vh - 220px));
                padding: 10px 10px 12px;
                background: #fff;
            }

            .notification-dropdown .notification-item {
                margin: 0 0 8px;
                padding: 12px 14px;
                border: 1px solid rgba(148, 163, 184, 0.16);
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
                cursor: pointer;
                transition: background-color 0.15s ease, border-color 0.15s ease;
            }

            .notification-dropdown .notification-item:last-child {
                margin-bottom: 0;
            }

            .notification-dropdown .notification-item:hover {
                background: rgba(15, 23, 42, 0.03);
                border-color: rgba(10, 186, 181, 0.35);
            }

            .notification-dropdown .notification-item.unread {
                background: rgba(10, 186, 181, 0.08);
                border-color: rgba(10, 186, 181, 0.22);
            }

            .notification-dropdown .notification-item.unread::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 3px;
                border-radius: 12px 0 0 12px;
                background: var(--primary-color, #0ABAB5);
            }

            .notification-dropdown .notification-content h5 {
                font-size: 0.92rem;
                line-height: 1.35;
            }

            .notification-dropdown .notification-content p {
                font-size: 0.84rem;
                opacity: 0.85;
            }

            .notification-dropdown .notification-content .notification-creator {
                margin-top: 4px;
                font-size: 0.78rem;
                color: #64748b;
            }

            .notification-dropdown .notification-empty,
            .notification-dropdown .notification-loading {
                padding: 18px 14px;
            }

            @media (max-width: 520px) {
                .notification-dropdown {
                    width: min(360px, calc(100vw - 16px));
                }
            }

            .search-results-dropdown {
                position: absolute;
                top: calc(100% + 8px);
                left: 0;
                right: 0;
                background: #fff;
                border: 1px solid var(--border-color, #e5e7eb);
                border-radius: 10px;
                box-shadow: 0 10px 24px rgba(0,0,0,0.12);
                z-index: 1200;
                display: none;
                max-height: 320px;
                overflow: auto;
            }

            .search-results-dropdown.active {
                display: block;
            }

            .search-result-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 12px;
                text-decoration: none;
                color: inherit;
                border-bottom: 1px solid var(--border-color, #eef2f7);
            }

            .search-result-item:last-child {
                border-bottom: none;
            }

            .search-result-item:hover {
                background: rgba(15, 23, 42, 0.04);
            }

            .search-result-item .result-icon {
                width: 28px;
                height: 28px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(10, 186, 181, 0.12);
                color: var(--primary-color, #0ABAB5);
                flex-shrink: 0;
            }

            .search-result-item .result-content h4 {
                margin: 0;
                font-size: 0.9rem;
            }

            .search-result-item .result-content p {
                margin: 2px 0 0;
                font-size: 0.8rem;
                opacity: 0.8;
            }

            .no-results {
                padding: 12px;
                opacity: 0.75;
                font-size: 0.9rem;
            }
        </style>

        <script>
            window.TOPBAR_CONTEXT = <?php echo json_encode($topbarState); ?>;
        </script>
        <script>
            (function () {
                if (window.__sharedTopbarInitialized) {
                    return;
                }
                window.__sharedTopbarInitialized = true;

                const ctx = window.TOPBAR_CONTEXT || {};
                const userId = Number(ctx.userId || 0);
                const role = String(ctx.role || 'company');
                const searchEndpoint = String(ctx.searchEndpoint || '');
                const notificationsPageUrl = String(ctx.notificationsPageUrl || '#');
                const quickLinks = Array.isArray(ctx.quickSearchLinks) ? ctx.quickSearchLinks : [];

                if (role === 'company' && userId > 0) {
                    window.CURRENT_COMPANY_ID = userId;
                }
                if (role === 'repairer' && userId > 0) {
                    window.CURRENT_REPAIRER_ID = userId;
                }

                const profileMenu = document.getElementById('profileMenu');
                const profileDropdown = document.getElementById('profileDropdown');
                if (profileMenu && profileDropdown) {
                    profileMenu.addEventListener('click', function (event) {
                        event.stopPropagation();
                        const isActive = profileMenu.classList.toggle('active');
                        profileDropdown.classList.toggle('active', isActive);
                        profileDropdown.classList.toggle('show', isActive);
                    });
                }

                const categoryWrapper = document.getElementById('searchCategoryWrapper');
                const categoryTrigger = categoryWrapper ? categoryWrapper.querySelector('.custom-select-trigger') : null;
                const categoryOptions = categoryWrapper ? categoryWrapper.querySelectorAll('.custom-option') : [];
                const selectedCategoryText = document.getElementById('selectedCategoryText');
                const searchCategoryInput = document.getElementById('searchCategory');

                if (categoryTrigger && categoryWrapper) {
                    categoryTrigger.addEventListener('click', function (event) {
                        event.stopPropagation();
                        categoryWrapper.classList.toggle('open');
                    });

                    categoryOptions.forEach(function (option) {
                        option.addEventListener('click', function (event) {
                            event.stopPropagation();
                            const value = this.getAttribute('data-value') || 'all';
                            const label = this.textContent || 'All';
                            if (selectedCategoryText) selectedCategoryText.textContent = label;
                            if (searchCategoryInput) searchCategoryInput.value = value;
                            categoryOptions.forEach(function (opt) { opt.classList.remove('selected'); });
                            this.classList.add('selected');
                            categoryWrapper.classList.remove('open');
                        });
                    });
                }

                const searchInput = document.getElementById('globalSearchInput');
                const searchResults = document.getElementById('searchResults');
                let searchDebounce = null;
                let notificationRefreshTimer = null;
                let currentNotificationRows = [];

                function openSearchResults(html) {
                    if (!searchResults) return;
                    searchResults.innerHTML = html;
                    searchResults.classList.add('active');
                }

                function closeSearchResults() {
                    if (!searchResults) return;
                    searchResults.classList.remove('active');
                }

                function renderLocalSearch(query) {
                    const q = query.toLowerCase();
                    const matches = quickLinks.filter(function (item) {
                        return String(item.title || '').toLowerCase().includes(q) || String(item.subtitle || '').toLowerCase().includes(q);
                    }).slice(0, 8);

                    if (matches.length === 0) {
                        openSearchResults('<div class="no-results">No results found</div>');
                        return;
                    }

                    const html = matches.map(function (item) {
                        const icon = item.icon || 'fa-search';
                        return '<a href="' + item.url + '" class="search-result-item">' +
                            '<div class="result-icon"><i class="fas ' + icon + '"></i></div>' +
                            '<div class="result-content"><h4>' + item.title + '</h4><p>' + (item.subtitle || '') + '</p></div>' +
                            '</a>';
                    }).join('');
                    openSearchResults(html);
                }

                async function performRemoteSearch(query, category) {
                    if (!searchEndpoint) {
                        renderLocalSearch(query);
                        return;
                    }

                    try {
                        const url = searchEndpoint + '?q=' + encodeURIComponent(query) + '&category=' + encodeURIComponent(category || 'all');
                        const response = await fetch(url);
                        const data = await response.json();

                        const rows = (data && data.success && Array.isArray(data.results)) ? data.results : [];
                        if (rows.length === 0) {
                            openSearchResults('<div class="no-results">No results found</div>');
                            return;
                        }

                        const html = rows.map(function (item) {
                            const icon = item.icon || 'fa-search';
                            return '<a href="' + item.url + '" class="search-result-item">' +
                                '<div class="result-icon"><i class="fas ' + icon + '"></i></div>' +
                                '<div class="result-content"><h4>' + item.title + '</h4><p>' + (item.subtitle || '') + '</p></div>' +
                                '</a>';
                        }).join('');
                        openSearchResults(html);
                    } catch (error) {
                        renderLocalSearch(query);
                    }
                }

                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        const query = String(searchInput.value || '').trim();
                        if (searchDebounce) {
                            clearTimeout(searchDebounce);
                        }
                        if (query.length < 2) {
                            closeSearchResults();
                            return;
                        }
                        searchDebounce = setTimeout(function () {
                            const category = searchCategoryInput ? searchCategoryInput.value : 'all';
                            performRemoteSearch(query, category);
                        }, 250);
                    });
                }

                function getNotificationType(roleName) {
                    if (roleName === 'repairer') return 'repairer';
                    if (roleName === 'company') return 'company';
                    if (roleName === 'customer' || roleName === 'user') return 'customer';
                    return 'all';
                }

                function safeText(value) {
                    const raw = value == null ? '' : String(value);
                    const div = document.createElement('div');
                    div.textContent = raw;
                    return div.innerHTML;
                }

                function formatRelativeTime(value) {
                    const ms = Date.parse(value);
                    if (!Number.isFinite(ms)) return '';
                    const diff = Date.now() - ms;
                    const minutes = Math.floor(diff / 60000);
                    if (minutes < 1) return 'Just now';
                    if (minutes < 60) return `${minutes}m ago`;
                    const hours = Math.floor(minutes / 60);
                    if (hours < 24) return `${hours}h ago`;
                    const days = Math.floor(hours / 24);
                    if (days < 7) return `${days}d ago`;
                    return new Date(ms).toLocaleDateString();
                }

                function getNotificationStorageKey(scopeId, roleName) {
                    return `fixlanka:${roleName}:${scopeId}:notificationsLastSeenAt`;
                }

                function getNotificationLastSeenMs(scopeId, roleName) {
                    try {
                        const raw = localStorage.getItem(getNotificationStorageKey(scopeId, roleName));
                        if (!raw) return 0;
                        const ms = Date.parse(raw);
                        return Number.isFinite(ms) ? ms : 0;
                    } catch {
                        return 0;
                    }
                }

                function setNotificationLastSeenMs(scopeId, roleName, ms) {
                    try {
                        localStorage.setItem(getNotificationStorageKey(scopeId, roleName), new Date(ms).toISOString());
                    } catch {
                        // ignore
                    }
                }

                function getNotificationTimeMs(notification) {
                    if (!notification || typeof notification !== 'object') return 0;
                    const fields = [notification.created_at, notification.send_date, notification.updated_at];
                    for (const value of fields) {
                        if (!value) continue;
                        const ms = Date.parse(value);
                        if (Number.isFinite(ms)) return ms;
                    }
                    if (notification.date && notification.time) {
                        const ms = Date.parse(`${notification.date} ${notification.time}`);
                        if (Number.isFinite(ms)) return ms;
                    }
                    return 0;
                }

                function isNotificationUnread(notification, lastSeenMs) {
                    if (!notification || typeof notification !== 'object') return false;

                    const timeMs = getNotificationTimeMs(notification);

                    // Once the user has a local last-seen marker, prefer that over backend flags.
                    if (timeMs > 0 && lastSeenMs > 0) {
                        return timeMs > lastSeenMs;
                    }

                    // Fallback to backend read flag if available.
                    if (Object.prototype.hasOwnProperty.call(notification, 'is_read')) {
                        const isRead = notification.is_read;
                        if (isRead === 0 || isRead === '0' || isRead === false) return true;
                        if (isRead === 1 || isRead === '1' || isRead === true) return false;
                    }

                    return false;
                }

                function getLatestNotificationMs(notifications) {
                    return (Array.isArray(notifications) ? notifications : [])
                        .map(getNotificationTimeMs)
                        .filter((t) => Number.isFinite(t) && t > 0)
                        .reduce((max, value) => Math.max(max, value), 0);
                }

                function updateNotificationBadge(count) {
                    const badge = document.getElementById('notificationCount');
                    if (!badge) return;

                    const numeric = Number(count || 0);
                    if (numeric > 0) {
                        badge.textContent = numeric > 99 ? '99+' : String(numeric);
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                        badge.textContent = '0';
                    }
                }

                async function loadNotificationCount() {
                    if (!userId) return;
                    const badge = document.getElementById('notificationCount');
                    if (!badge) return;

                    try {
                        const userType = getNotificationType(role);
                        const res = await fetch('/2nd-Year-Group-Project/FixLanka/api/notifications.php?action=count&user_id=' + encodeURIComponent(userId) + '&user_type=' + encodeURIComponent(userType));
                        const data = await res.json();
                        let count = Number((data && data.success) ? data.count : 0) || 0;

                        // If backend count is unavailable/incorrect, derive a stable count from the notification list.
                        if (count === 0) {
                            const listRes = await fetch('/2nd-Year-Group-Project/FixLanka/api/notifications.php?action=list&limit=30&user_id=' + encodeURIComponent(userId) + '&user_type=' + encodeURIComponent(userType));
                            const listData = await listRes.json();
                            const notifications = (listData && listData.success && Array.isArray(listData.notifications)) ? listData.notifications : [];
                            const lastSeenMs = getNotificationLastSeenMs(userId, role);
                            const unread = notifications.filter((n) => isNotificationUnread(n, lastSeenMs));
                            count = unread.length;
                        }

                        updateNotificationBadge(count);
                    } catch (e) {
                        badge.style.display = 'none';
                    }
                }

                async function toggleNotifications() {
                    const bell = document.getElementById('notificationBell');
                    if (!bell) return;

                    function positionNotificationDropdown(dropdownEl) {
                        if (!dropdownEl || !bell) return;
                        const rect = bell.getBoundingClientRect();
                        const viewportWidth = window.innerWidth || document.documentElement.clientWidth || 1200;
                        const viewportHeight = window.innerHeight || document.documentElement.clientHeight || 800;

                        const targetWidth = viewportWidth < 600
                            ? Math.min(340, Math.max(280, viewportWidth - 20))
                            : 360;
                        dropdownEl.style.width = targetWidth + 'px';

                        const margin = 12;
                        let left = rect.right - targetWidth;
                        if (left < margin) left = margin;
                        if (left + targetWidth > viewportWidth - margin) {
                            left = viewportWidth - targetWidth - margin;
                        }

                        let top = rect.bottom + 10;
                        const maxHeight = Math.floor(viewportHeight * 0.8);
                        dropdownEl.style.maxHeight = maxHeight + 'px';

                        if (top + Math.min(420, maxHeight) > viewportHeight - margin) {
                            top = Math.max(margin, rect.top - Math.min(420, maxHeight) - 10);
                        }

                        dropdownEl.style.left = left + 'px';
                        dropdownEl.style.top = top + 'px';
                    }

                    const existing = bell.querySelector('.notification-dropdown');
                    if (existing) {
                        existing.remove();
                        bell.classList.remove('active');
                        return;
                    }

                    const dropdown = document.createElement('div');
                    dropdown.className = 'notification-dropdown';
                    dropdown.innerHTML =
                        '<div class="notification-header notification-dropdown-header">' +
                            '<h4 class="notification-dropdown-title">Notifications</h4>' +
                            '<button type="button" class="mark-all-read">Mark all as read</button>' +
                        '</div>' +
                        '<div class="notification-list notification-dropdown-list" id="topbarNotificationList">' +
                            '<div class="notification-loading" style="padding:12px;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>' +
                        '</div>' +
                        '<div class="notification-footer notification-dropdown-footer">' +
                            '<a href="' + notificationsPageUrl + '">View all notifications</a>' +
                        '</div>';

                    bell.appendChild(dropdown);
                    bell.classList.add('active');
                    dropdown.classList.add('active');
                    positionNotificationDropdown(dropdown);

                    const listEl = dropdown.querySelector('#topbarNotificationList');
                    const markAllBtn = dropdown.querySelector('.mark-all-read');

                    try {
                        const userType = getNotificationType(role);
                        const res = await fetch('/2nd-Year-Group-Project/FixLanka/api/notifications.php?action=list&limit=6&user_id=' + encodeURIComponent(userId) + '&user_type=' + encodeURIComponent(userType));
                        const data = await res.json();
                        const rows = (data && data.success && Array.isArray(data.notifications)) ? data.notifications : [];
                        const lastSeenMs = getNotificationLastSeenMs(userId, role);
                        currentNotificationRows = rows;

                        if (!rows.length) {
                            listEl.innerHTML = '<div class="no-notifications" style="padding:12px;">No notifications</div>';
                        } else {
                            listEl.innerHTML = rows.map(function (n) {
                                const title = safeText(n.title || 'Notification');
                                const message = safeText(n.message || '');
                                const creatorNameRaw = String(n.created_by_name || '').trim();
                                const creatorRole = String(n.created_by_role || '').toLowerCase();
                                const creatorName = safeText(creatorNameRaw || (creatorRole === 'admin' ? 'Admin' : (creatorRole === 'moderator' ? 'Moderator' : 'System')));
                                const timeLabel = n.created_at || n.send_date || n.time ? safeText(formatRelativeTime(n.created_at || n.send_date || n.time)) : '';
                                const unread = isNotificationUnread(n, lastSeenMs);
                                return '<div class="notification-item' + (unread ? ' unread' : '') + '" data-notification-id="' + String(n.notification_id || '') + '">' +
                                    '<div class="notification-content"><h5 style="margin:0 0 4px;">' + title + '</h5><p style="margin:0;opacity:.8;">' + message + '</p><div class="notification-creator">Created by: ' + creatorName + '</div>' +
                                    (timeLabel ? '<div class="notification-time">' + timeLabel + '</div>' : '') +
                                    '</div>' +
                                '</div>';
                            }).join('');
                        }

                        // Treat the currently visible dropdown contents as seen after render.
                        const latestMs = getLatestNotificationMs(rows);
                        if (latestMs > 0) {
                            setNotificationLastSeenMs(userId, role, latestMs);
                        }

                        // Refresh badge after updating the seen marker.
                        loadNotificationCount();
                    } catch (e) {
                        listEl.innerHTML = '<div class="no-notifications" style="padding:12px;">Failed to load notifications</div>';
                    }

                    if (markAllBtn) {
                        markAllBtn.addEventListener('click', async function (event) {
                            event.preventDefault();
                            const latestMs = getLatestNotificationMs(currentNotificationRows);
                            if (latestMs > 0) {
                                setNotificationLastSeenMs(userId, role, latestMs);
                            }
                            try {
                                const userType = getNotificationType(role);
                                await fetch('/2nd-Year-Group-Project/FixLanka/api/notifications.php?action=mark_all_read', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ user_id: userId, user_type: userType })
                                });
                            } catch (e) {}
                            toggleNotifications();
                            loadNotificationCount();
                        });
                    }

                    if (listEl) {
                        listEl.addEventListener('click', function (event) {
                            const item = event.target.closest('.notification-item');
                            if (!item) {
                                return;
                            }
                            if (notificationsPageUrl && notificationsPageUrl !== '#') {
                                const notificationId = String(item.getAttribute('data-notification-id') || '').trim();
                                try {
                                    const targetUrl = new URL(notificationsPageUrl, window.location.origin);
                                    if (notificationId) {
                                        targetUrl.searchParams.set('notification_id', notificationId);
                                    }
                                    window.location.href = targetUrl.toString();
                                } catch (urlError) {
                                    if (notificationId) {
                                        const glue = notificationsPageUrl.includes('?') ? '&' : '?';
                                        window.location.href = notificationsPageUrl + glue + 'notification_id=' + encodeURIComponent(notificationId);
                                    } else {
                                        window.location.href = notificationsPageUrl;
                                    }
                                }
                            }
                        });
                    }

                }

                const bell = document.getElementById('notificationBell');
                if (bell) {
                    bell.addEventListener('click', function (event) {
                        event.stopPropagation();
                        toggleNotifications();
                    });

                    if (!window.__sharedTopbarNotificationViewportHandlers) {
                        window.__sharedTopbarNotificationViewportHandlers = true;
                        const reposition = function () {
                            const currentBell = document.getElementById('notificationBell');
                            if (!currentBell) return;
                            const currentDropdown = currentBell.querySelector('.notification-dropdown');
                            if (!currentDropdown) return;

                            const rect = currentBell.getBoundingClientRect();
                            const viewportWidth = window.innerWidth || document.documentElement.clientWidth || 1200;
                            const viewportHeight = window.innerHeight || document.documentElement.clientHeight || 800;
                            const targetWidth = Math.min(380, Math.max(280, viewportWidth - 16));

                            let left = rect.right - targetWidth;
                            if (left < 8) left = 8;
                            if (left + targetWidth > viewportWidth - 8) {
                                left = viewportWidth - targetWidth - 8;
                            }

                            let top = rect.bottom + 10;
                            const maxHeight = Math.floor(viewportHeight * 0.8);
                            if (top + Math.min(420, maxHeight) > viewportHeight - 8) {
                                top = Math.max(8, rect.top - Math.min(420, maxHeight) - 10);
                            }

                            currentDropdown.style.width = targetWidth + 'px';
                            currentDropdown.style.maxHeight = maxHeight + 'px';
                            currentDropdown.style.left = left + 'px';
                            currentDropdown.style.top = top + 'px';
                        };

                        window.addEventListener('resize', reposition, { passive: true });
                        window.addEventListener('scroll', reposition, { passive: true });
                    }
                }

                if (!window.__sharedTopbarNotificationRefreshTimer) {
                    window.__sharedTopbarNotificationRefreshTimer = true;
                    setInterval(function () {
                        if (document.visibilityState === 'visible') {
                            loadNotificationCount();
                        }
                    }, 30000);

                    document.addEventListener('visibilitychange', function () {
                        if (document.visibilityState === 'visible') {
                            loadNotificationCount();
                        }
                    });
                }

                document.addEventListener('click', function (event) {
                    if (categoryWrapper && !categoryWrapper.contains(event.target)) {
                        categoryWrapper.classList.remove('open');
                    }
                    if (profileMenu && !profileMenu.contains(event.target) && profileDropdown) {
                        profileMenu.classList.remove('active');
                        profileDropdown.classList.remove('active');
                        profileDropdown.classList.remove('show');
                    }
                    if (!event.target.closest('.search-box')) {
                        closeSearchResults();
                    }
                    if (bell && !bell.contains(event.target)) {
                        const d = bell.querySelector('.notification-dropdown');
                        if (d) d.remove();
                        bell.classList.remove('active');
                    }
                });

                loadNotificationCount();
            })();
        </script>
        <?php
    }
}
