<?php
// Include session helper if not already included
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/../../config/session.php';
}
$isLoggedIn = isLoggedIn();
$userData = getUserData();
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
            <?php if ($isLoggedIn): ?>
                <!-- Logged In User Section -->
                <div class="notification-bell" id="notificationBell">
                    <i class="fas fa-bell"></i>

                    <div class="notification-dropdown" id="notificationDropdown" style="display:none;">
                        <div class="notification-dropdown-header">
                            <span>Notifications</span>
                        </div>
                        <div class="notification-dropdown-list" id="notificationList" style="max-height: 270px; overflow-y: auto;">
                            <div class="notification-empty">Loading...</div>
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
            
            <?php if ($isLoggedIn): ?>
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
    
    if (profileAvatar && profileDropdown) {
        profileAvatar.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
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

    // Notifications bell dropdown
    const notificationBell = document.getElementById('notificationBell');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');

    const NOTIFICATIONS_API = '/2nd-Year-Group-Project/FixLanka/api/user-notifications.php';

    function setBadgeCount(count) {
        if (!notificationBadge) return;
        const safeCount = Number.isFinite(count) ? count : 0;
        if (safeCount > 0) {
            notificationBadge.textContent = String(safeCount);
            notificationBadge.style.display = 'inline-block';
        } else {
            notificationBadge.textContent = '0';
            notificationBadge.style.display = 'none';
        }
    }

    function formatNotificationDate(value) {
        if (!value) return '';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return String(value);
        return d.toLocaleString();
    }

    async function fetchJson(url) {
        const res = await fetch(url, { credentials: 'same-origin' });
        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error('Invalid JSON from notifications API');
        }
        if (!res.ok || data?.success === false) {
            const message = data?.message || `Request failed (${res.status})`;
            throw new Error(message);
        }
        return data;
    }

    async function refreshNotificationCount() {
        if (!notificationBell) return;
        try {
            const data = await fetchJson(`${NOTIFICATIONS_API}?action=count`);
            setBadgeCount(parseInt(data.count, 10) || 0);
        } catch (e) {
            setBadgeCount(0);
        }
    }

    function renderNotifications(notifications) {
        if (!notificationList) return;
        if (!Array.isArray(notifications) || notifications.length === 0) {
            notificationList.innerHTML = '<div class="notification-empty">No notifications</div>';
            return;
        }

        notificationList.innerHTML = notifications.map(n => {
            const title = (n?.title ?? 'Notification');
            const message = (n?.message ?? '');
            const createdAt = formatNotificationDate(n?.created_at);
            return `
                <div class="notification-item">
                    <div class="notification-title">${escapeHtml(title)}</div>
                    <div class="notification-message">${escapeHtml(message)}</div>
                    <div class="notification-meta">${escapeHtml(createdAt)}</div>
                </div>
            `;
        }).join('');
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    async function loadNotificationsList() {
        if (!notificationList) return;
        notificationList.innerHTML = '<div class="notification-empty">Loading...</div>';
        try {
            const data = await fetchJson(`${NOTIFICATIONS_API}?action=list&limit=8`);
            renderNotifications(data.notifications);
            refreshNotificationCount();
        } catch (e) {
            notificationList.innerHTML = '<div class="notification-empty">Failed to load notifications</div>';
        }
    }

    if (notificationBell && notificationDropdown) {
        refreshNotificationCount();

        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            if (profileDropdown) {
                profileDropdown.classList.remove('show');
            }
            const isOpen = notificationDropdown.style.display !== 'none';
            notificationDropdown.style.display = isOpen ? 'none' : 'block';
            if (!isOpen) {
                loadNotificationsList();
            }
        });

        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target)) {
                notificationDropdown.style.display = 'none';
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                notificationDropdown.style.display = 'none';
            }
        });
    }
});
</script>