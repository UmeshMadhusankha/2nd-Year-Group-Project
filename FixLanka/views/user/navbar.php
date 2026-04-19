<?php
// Include session helper if not already included
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/../../config/session.php';
}
$isLoggedIn = isLoggedIn();
$userData = getUserData();
$isUserSession = $isLoggedIn && hasRole('user');
?>

<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-left">
            <div class="logo">
                <a href="/2nd-Year-Group-Project/FixLanka/" class="logo-link">
                    <span class="logo-text">Fix Lanka</span>
                </a>
            </div>
        </div>
        
        <div class="navbar-center">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="/2nd-Year-Group-Project/FixLanka/views/user/services.php" class="nav-link">Services</a>
                </li>
                <li class="nav-item">
                    <a href="/2nd-Year-Group-Project/FixLanka/views/user/how-it-works.php" class="nav-link">How it works</a>
                </li>
                <li class="nav-item">
                    <a href="/2nd-Year-Group-Project/FixLanka/views/user/support.php" class="nav-link">Support</a>
                </li>
            </ul>
        </div>
        
        <div class="navbar-right">
            <?php if ($isUserSession): ?>
                <!-- Logged In User Section -->
                <div class="notification-wrap" id="userNotificationWrap">
                    <button type="button" class="notification-bell" id="userNotificationBell" aria-label="Notifications" aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="userNotificationBadge" style="display:none;"></span>
                    </button>
                    <div class="notification-dropdown" id="userNotificationDropdown">
                        <div class="notification-dropdown-header">
                            <h4 class="notification-dropdown-title">Notifications</h4>
                        </div>
                        <div class="notification-dropdown-list" id="userNotificationList">
                            <div class="notification-empty">No notifications yet.</div>
                        </div>
                    </div>
                </div>
                
                <div class="profile-dropdown-container">
                    <div class="profile-avatar" id="profileAvatar">
                        <div class="avatar-placeholder">
                            <?php echo strtoupper(substr($userData['name'], 0, 1)); ?>
                        </div>
                    </div>
                    
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="dropdown-header">
                            <p class="user-name"><?php echo htmlspecialchars($userData['name']); ?></p>
                            <p class="user-email"><?php echo htmlspecialchars($userData['email']); ?></p>
                        </div>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item">
                                <a href="/2nd-Year-Group-Project/FixLanka/profile" class="dropdown-link">
                                    <i class="fas fa-user"></i>
                                    My Profile
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="/2nd-Year-Group-Project/FixLanka/job-history" class="dropdown-link">
                                    <i class="fas fa-calendar-check"></i>
                                    My Jobs
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="/2nd-Year-Group-Project/FixLanka/my-contracts" class="dropdown-link">
                                    <i class="fas fa-file-contract"></i>
                                    My Contracts
                                </a>
                            </li>

                            <li class="dropdown-item">
                                <a href="/2nd-Year-Group-Project/FixLanka/settings" class="dropdown-link">
                                    <i class="fas fa-cog"></i>
                                    Settings
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="/2nd-Year-Group-Project/FixLanka/help-center" class="dropdown-link">
                                    <i class="fas fa-question-circle"></i>
                                    Help Center
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item">
                                <form action="/2nd-Year-Group-Project/FixLanka/logout" method="POST" id="logoutForm" style="margin: 0;">
                                    <button type="submit" class="dropdown-link logout" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; font-size: inherit; font-family: inherit; padding: 12px 20px;">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <!-- Guest User Section - Login Button -->
                <a href="/2nd-Year-Group-Project/FixLanka/login" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Mobile menu toggle -->
        <div class="mobile-menu-toggle" id="mobileMenuToggle">
            <span class="hamburger"></span>
            <span class="hamburger"></span>
            <span class="hamburger"></span>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div class="mobile-menu" id="mobileMenu">
        <ul class="mobile-nav-menu">
            <li><a href="/2nd-Year-Group-Project/FixLanka/views/user/services.php" class="mobile-nav-link">Services</a></li>
            <li><a href="/2nd-Year-Group-Project/FixLanka/views/user/how-it-works.php" class="mobile-nav-link">How it works</a></li>
            <li><a href="/2nd-Year-Group-Project/FixLanka/views/user/support.php" class="mobile-nav-link">Support</a></li>
            
            <?php if ($isUserSession): ?>
                <li><a href="/2nd-Year-Group-Project/FixLanka/profile" class="mobile-nav-link">My Profile</a></li>
                <li><a href="/2nd-Year-Group-Project/FixLanka/job-history" class="mobile-nav-link">My Jobs</a></li>
                <li><a href="/2nd-Year-Group-Project/FixLanka/my-contracts" class="mobile-nav-link">My Contracts</a></li>

                <li><a href="/2nd-Year-Group-Project/FixLanka/settings" class="mobile-nav-link">Settings</a></li>
                <li><a href="/2nd-Year-Group-Project/FixLanka/help-center" class="mobile-nav-link">Help Center</a></li>
                <li>
                    <form action="/2nd-Year-Group-Project/FixLanka/logout" method="POST" style="margin: 0;">
                        <button type="submit" class="mobile-nav-link" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; font-size: inherit; font-family: inherit;">Logout</button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="/2nd-Year-Group-Project/FixLanka/login" class="mobile-nav-link mobile-login-btn">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<script>
// Profile dropdown toggle
document.addEventListener('DOMContentLoaded', function() {
    const profileAvatar = document.getElementById('profileAvatar');
    const profileDropdown = document.getElementById('profileDropdown');
    const notificationWrap = document.getElementById('userNotificationWrap');
    const notificationBell = document.getElementById('userNotificationBell');
    const notificationDropdown = document.getElementById('userNotificationDropdown');
    const notificationBadge = document.getElementById('userNotificationBadge');
    const notificationList = document.getElementById('userNotificationList');
    const userId = <?php echo (int)($userData['id'] ?? 0); ?>;

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function formatDateLabel(value) {
        if (!value) return 'Just now';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return 'Just now';
        return date.toLocaleString(undefined, {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        });
    }

    async function fetchJson(url) {
        const response = await fetch(url, { credentials: 'same-origin' });
        const text = await response.text();
        let data;

        try {
            data = JSON.parse(text);
        } catch (error) {
            throw new Error('Invalid notification response');
        }

        if (!response.ok || data?.success === false) {
            throw new Error(data?.message || `Request failed (${response.status})`);
        }

        return data;
    }

    function setBadgeCount(count) {
        if (!notificationBadge) return;
        const numeric = Number(count) || 0;
        if (numeric > 0) {
            notificationBadge.style.display = 'inline-flex';
            notificationBadge.textContent = numeric > 99 ? '99+' : String(numeric);
        } else {
            notificationBadge.style.display = 'none';
            notificationBadge.textContent = '';
        }
    }

    async function loadNotificationCount() {
        if (!notificationBadge || userId <= 0) return;
        try {
            const data = await fetchJson('/2nd-Year-Group-Project/FixLanka/api/user-notifications.php?action=count');
            setBadgeCount(data.count || 0);
        } catch (error) {
            setBadgeCount(0);
        }
    }

    async function loadNotifications() {
        if (!notificationList || userId <= 0) return;
        notificationList.innerHTML = '<div class="notification-loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';

        try {
            const data = await fetchJson('/2nd-Year-Group-Project/FixLanka/api/user-notifications.php?action=list&limit=8');
            const rows = Array.isArray(data.notifications) ? data.notifications : [];

            if (!rows.length) {
                notificationList.innerHTML = '<div class="notification-empty">No notifications yet.</div>';
                return;
            }

            notificationList.innerHTML = rows.map((item) => {
                const title = escapeHtml(item.title || 'Notification');
                const message = escapeHtml(item.message || '');
                const createdAt = escapeHtml(formatDateLabel(item.created_at || item.send_date || item.time));
                return `
                    <div class="notification-item" tabindex="0">
                        <h5>${title}</h5>
                        <p>${message}</p>
                        <span class="notification-time">${createdAt}</span>
                    </div>
                `;
            }).join('');
        } catch (error) {
            notificationList.innerHTML = '<div class="notification-empty">Failed to load notifications.</div>';
        }
    }

    function openNotifications() {
        if (!notificationWrap || !notificationBell || !notificationDropdown) return;
        notificationWrap.classList.add('open');
        notificationBell.setAttribute('aria-expanded', 'true');
        loadNotifications();
    }

    function closeNotifications() {
        if (!notificationWrap || !notificationBell) return;
        notificationWrap.classList.remove('open');
        notificationBell.setAttribute('aria-expanded', 'false');
    }

    if (notificationBell && notificationWrap) {
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            if (notificationWrap.classList.contains('open')) {
                closeNotifications();
            } else {
                openNotifications();
            }
        });
    }
    
    if (profileAvatar && profileDropdown) {
        profileAvatar.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
            closeNotifications();
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (notificationWrap && !notificationWrap.contains(e.target)) {
                closeNotifications();
            }
            if (!profileAvatar.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    }
    
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('show');
        });
    }

    // Initial count load + periodic refresh from DB notifications table.
    loadNotificationCount();
    window.setInterval(loadNotificationCount, 30000);
});
</script>