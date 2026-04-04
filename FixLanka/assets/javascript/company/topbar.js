/**
 * Topbar JavaScript - Dynamic Page Title & Profile Dropdown
 * Handles page title updates, profile dropdown, search, and notifications
 */

// Configuration object for all pages
const PAGE_INFO = {
    'dashboard': {
        title: 'Dashboard',
        slogan: 'Welcome back, let\'s see what\'s happening today'
    },
    'index': {
        title: 'Dashboard',
        slogan: 'Welcome back, let\'s see what\'s happening today'
    },
    'projects': {
        title: 'Projects',
        slogan: 'Manage and track your repair projects efficiently'
    },
    'workforce': {
        title: 'Workforce',
        slogan: 'Manage your team and freelancers all in one place'
    },
    'repair-requests': {
        title: 'Repair Requests',
        slogan: 'Handle customer requests and schedule repairs'
    },
    'requests': {
        title: 'Repair Requests',
        slogan: 'Handle customer requests and schedule repairs'
    },
    'support': {
        title: 'Help & Support',
        slogan: 'Get help and manage customer support tickets'
    },
    'contracts': {
        title: 'Contracts',
        slogan: 'Manage agreements and legal documents'
    },
    'payments': {
        title: 'Payments',
        slogan: 'Track payments and financial transactions'
    },
    'advertisements': {
        title: 'Advertisement Management',
        slogan: 'Promote your services to a wider audience'
    },
    'profile': {
        title: 'My Profile',
        slogan: 'Manage your personal and company information'
    },
    'reviews': {
        title: 'Reviews & Feedback',
        slogan: 'Monitor and manage customer feedback for your company'
    },
    'settings': {
        title: 'Settings',
        slogan: 'Configure your account and system preferences'
    }
};

// Initialize all topbar functionality
function initializeTopbar() {
    // Prevent multiple initializations
    if (window.topbarInitialized) {
        return;
    }
    window.topbarInitialized = true;

    // Get current page from URL
    updatePageHeaderFromURL();
    initSearch();

    // Initialize notifications with retry
    setTimeout(function () {
        initNotifications();
    }, 100);

    // Initialize profile dropdown with retry
    setTimeout(function () {
        initProfileDropdown();
    }, 200);

    attachSidebarLinkListeners();

    // Listen for page changes (for SPA-like navigation)
    window.addEventListener('popstate', function () {
        updatePageHeaderFromURL();
    });
}

/**
 * Get page name from URL
 * @returns {string} Page name
 */
function getPageNameFromURL() {
    const currentPath = window.location.pathname;
    let pageName = currentPath.split('/').pop().replace('.php', '') || 'dashboard';

    // Handle cases where filename might be index
    if (pageName === 'index' || pageName === '') {
        pageName = 'dashboard';
    }

    return pageName;
}

/**
 * Update page title and slogan based on current page
 * @param {string} pageName - The page name (e.g., 'dashboard', 'projects')
 */
function updatePageHeader(pageName) {
    const pageTitle = document.getElementById('page-title');
    const pageSlogan = document.getElementById('page-slogan');

    // Get page info or use dashboard as default
    const info = PAGE_INFO[pageName] || PAGE_INFO['dashboard'];

    if (pageTitle) {
        pageTitle.textContent = info.title;
        // Add fade-in animation
        pageTitle.style.opacity = '0';
        setTimeout(() => {
            pageTitle.style.transition = 'opacity 0.3s ease';
            pageTitle.style.opacity = '1';
        }, 50);
    }

    if (pageSlogan) {
        pageSlogan.textContent = info.slogan;
        // Add fade-in animation
        pageSlogan.style.opacity = '0';
        setTimeout(() => {
            pageSlogan.style.transition = 'opacity 0.3s ease';
            pageSlogan.style.opacity = '1';
        }, 100);
    }

    // Update browser tab title
    document.title = `${info.title} - FixLanka Company Dashboard`;
}

/**
 * Update page header from current URL
 */
function updatePageHeaderFromURL() {
    const pageName = getPageNameFromURL();
    updatePageHeader(pageName);
}

/**
 * Update page header when sidebar link is clicked
 * @param {string} pageName - The page name to navigate to
 */
function setPageTitle(pageName) {
    updatePageHeader(pageName);
}

/**
 * Attach listeners to sidebar links to update page title
 * This function listens for clicks on sidebar navigation links
 */
function attachSidebarLinkListeners() {
    // Listen for all links that might navigate to different pages
    document.addEventListener('click', function (e) {
        const link = e.target.closest('a[href*=".php"]');

        if (link) {
            const href = link.getAttribute('href');
            // Extract page name from href
            const pageName = href.split('/').pop().replace('.php', '');

            if (pageName && PAGE_INFO[pageName]) {
                // Update page title immediately (before navigation)
                updatePageHeader(pageName);
            }
        }
    });
}

/**
 * Initialize Search Functionality
 */
function initSearch() {
    const searchInput = document.getElementById('globalSearchInput');
    const searchWrapper = document.getElementById('searchCategoryWrapper');
    const searchTrigger = document.querySelector('.custom-select-trigger');
    const customOptions = document.querySelectorAll('.custom-option');
    const hiddenInput = document.getElementById('searchCategory');
    const selectedText = document.getElementById('selectedCategoryText');
    const searchResults = document.getElementById('searchResults');

    if (!searchInput) return;

    let debounceTimer;

    // Custom Dropdown Logic
    if (searchWrapper && searchTrigger) {
        // Toggle Dropdown
        searchTrigger.addEventListener('click', function (e) {
            e.stopPropagation();
            searchWrapper.classList.toggle('open');
        });

        // Select Option
        customOptions.forEach(option => {
            option.addEventListener('click', function (e) {
                e.stopPropagation();
                const value = this.getAttribute('data-value');
                const text = this.textContent;

                // Update UI
                selectedText.textContent = text;
                hiddenInput.value = value;

                // Update Active Class
                customOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');

                // Close Dropdown
                searchWrapper.classList.remove('open');

                // Trigger Search if input has value
                if (searchInput.value.trim().length >= 2) {
                    performGlobalSearch(searchInput.value.trim(), value);
                }
            });
        });

        // Close on click outside
        document.addEventListener('click', function (e) {
            if (!searchWrapper.contains(e.target)) {
                searchWrapper.classList.remove('open');
            }
        });
    }

    // Search Input Handler
    searchInput.addEventListener('input', function (e) {
        const query = e.target.value.trim();
        const category = hiddenInput ? hiddenInput.value : 'all';

        clearTimeout(debounceTimer);

        if (query.length < 2) {
            if (searchResults) searchResults.classList.remove('active');
            return;
        }

        debounceTimer = setTimeout(() => {
            performGlobalSearch(query, category);
        }, 300);
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.search-box')) {
            if (searchResults) searchResults.classList.remove('active');
        }
    });
}

/**
 * Perform Global Search via API
 */
async function performGlobalSearch(query, category) {
    const searchResults = document.getElementById('searchResults');
    if (!searchResults) return;

    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/global-search.php?q=${encodeURIComponent(query)}&category=${encodeURIComponent(category)}`);
        const data = await response.json();

        if (data.success && data.results.length > 0) {
            const html = data.results.map(item => `
        <a href="${item.url}" class="search-result-item">
          <div class="result-icon">
            <i class="fas ${item.icon || 'fa-search'}"></i>
          </div>
          <div class="result-content">
            <h4>${item.title}</h4>
            <p>${item.subtitle || ''}</p>
          </div>
        </a>
      `).join('');
            searchResults.innerHTML = html;
            searchResults.classList.add('active');
        } else {
            searchResults.innerHTML = '<div class="no-results">No results found</div>';
            searchResults.classList.add('active');
        }

    } catch (error) {
        console.error('Search error:', error);
        searchResults.innerHTML = '<div class="no-results">Error searching</div>';
        searchResults.classList.add('active');
    }
}

/**
 * Initialize Notifications
 */
function initNotifications() {
    const notificationBell = document.querySelector('.notification-bell');
    if (!notificationBell) {
        console.error('⚠️ Notification bell element not found!');
        // Try to find it after a delay (in case topbar loads late)
        setTimeout(function () {
            const bellRetry = document.querySelector('.notification-bell');
            if (bellRetry) {
                initNotifications();
            } else {
                console.error('⚠️ Notification bell still not found after retry');
            }
        }, 500);
        return;
    }

    notificationBell.addEventListener('click', function (e) {
        e.stopPropagation();
        toggleNotificationDropdown();
    });

    document.addEventListener('click', function (e) {
        const notificationDropdown = document.querySelector('.notification-dropdown');
        if (notificationDropdown && !notificationBell.contains(e.target)) {
            notificationDropdown.remove();
        }
    });
}

/**
 * Toggle notification dropdown
 */
async function toggleNotificationDropdown() {
    const existingDropdown = document.querySelector('.notification-dropdown');
    if (existingDropdown) {
        existingDropdown.remove();
        return;
    }

    // Get company ID from window or PHP
    const companyId = window.CURRENT_COMPANY_ID || 0;

    const dropdown = document.createElement('div');
    dropdown.className = 'notification-dropdown';
    dropdown.innerHTML = `
    <div class="notification-header">
      <h4>Notifications</h4>
      <button class="mark-all-read">Mark all as read</button>
    </div>
    <div class="notification-list">
      <div class="notification-loading">
        <i class="fas fa-spinner fa-spin"></i> Loading notifications...
      </div>
    </div>
    <div class="notification-footer">
            <a href="/2nd-Year-Group-Project/FixLanka/views/company/notifications.php">View All Notifications</a>
    </div>
  `;

    // Get notification bell position for better dropdown placement
    const notificationBell = document.querySelector('.notification-bell');
    const bellRect = notificationBell ? notificationBell.getBoundingClientRect() : null;

    // Apply positioning via inline style, but leave visual styling to CSS
    dropdown.style.top = `${bellRect ? bellRect.bottom + 10 : 60}px`;
    dropdown.style.right = `${bellRect ? window.innerWidth - bellRect.right : 80}px`;

    // Add to DOM first to transition it
    document.body.appendChild(dropdown);

    // Trigger animation
    requestAnimationFrame(() => {
        dropdown.classList.add('active');
    });

    // Load notifications from API
    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/notifications.php?action=list&user_id=${companyId}&user_type=company&limit=5`);
        const data = await response.json();

        const notificationList = dropdown.querySelector('.notification-list');

        if (data.success && data.notifications && data.notifications.length > 0) {
            notificationList.innerHTML = data.notifications.map(notif => `
        <div class="notification-item ${notif.is_read == 0 ? 'unread' : ''} ${notif.type}">
          <div class="notification-icon">
            <i class="fas ${getNotificationIcon(notif.type)}"></i>
          </div>
          <div class="notification-content">
            <h5>${escapeHtml(notif.title)}</h5>
            <p>${escapeHtml(notif.message)}</p>
            <span class="notification-time">${formatTimeAgo(notif.created_at)}</span>
          </div>
        </div>
      `).join('');
        } else {
            notificationList.innerHTML = `
        <div class="notification-empty">
          <i class="fas fa-bell-slash"></i>
          <p>No notifications yet</p>
        </div>
      `;
        }
    } catch (error) {
        console.error('⚠️ Error loading notifications:', error);
        const notificationList = dropdown.querySelector('.notification-list');
        notificationList.innerHTML = `
      <div class="notification-empty">
        <i class="fas fa-exclamation-triangle"></i>
        <p>Failed to load notifications</p>
      </div>
    `;
    }

    const markAllBtn = dropdown.querySelector('.mark-all-read');
    markAllBtn.addEventListener('click', async function () {
        try {
            const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/notifications.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'mark_all_read',
                    user_id: companyId,
                    user_type: 'company'
                })
            });

            const data = await response.json();
            if (data.success) {
                const unreadItems = dropdown.querySelectorAll('.notification-item.unread');
                unreadItems.forEach(item => item.classList.remove('unread'));
                updateNotificationBadge(0);
            }
        } catch (error) {
            console.error('Error marking notifications as read:', error);
        }
    });
}

/**
 * Get notification icon based on type
 */
function getNotificationIcon(type) {
    const icons = {
        'contract': 'fa-file-contract',
        'repair_request': 'fa-tools',
        'payment': 'fa-dollar-sign',
        'message': 'fa-envelope',
        'alert': 'fa-exclamation-circle',
        'info': 'fa-info-circle',
        'success': 'fa-check-circle'
    };
    return icons[type] || 'fa-bell';
}

/**
 * Format time ago (e.g., "5 minutes ago")
 */
function formatTimeAgo(timestamp) {
    const now = new Date();
    const time = new Date(timestamp);
    const diffMs = now - time;
    const diffSecs = Math.floor(diffMs / 1000);
    const diffMins = Math.floor(diffSecs / 60);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);

    if (diffSecs < 60) return 'Just now';
    if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
    if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
    if (diffDays < 7) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
    return time.toLocaleDateString();
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Update notification badge count
 */
function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
}

/**
 * Initialize Profile Dropdown
 */
function initProfileDropdown() {
    const profileMenu = document.getElementById('profileMenu');
    const profileDropdown = document.getElementById('profileDropdown');
    const profileTrigger = profileMenu ? profileMenu.querySelector('.profile-trigger') : null;
    let isLocked = false;
    let hoverTimeout = null;

    if (!profileMenu || !profileDropdown || !profileTrigger) {
        setTimeout(initProfileDropdown, 100);
        return;
    }

    profileMenu.addEventListener('mouseenter', function () {
        if (!isLocked) {
            clearTimeout(hoverTimeout);
            hoverTimeout = setTimeout(() => {
                profileDropdown.classList.add('show');
                profileMenu.classList.add('active');
            }, 150);
        }
    });

    profileMenu.addEventListener('mouseleave', function () {
        clearTimeout(hoverTimeout);
        if (!isLocked) {
            profileDropdown.classList.remove('show');
            profileMenu.classList.remove('active');
        }
    });

    profileTrigger.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        isLocked = !isLocked;

        if (isLocked) {
            clearTimeout(hoverTimeout);
            profileDropdown.classList.add('show', 'locked');
            profileMenu.classList.add('active', 'locked');
        } else {
            profileDropdown.classList.remove('show', 'locked');
            profileMenu.classList.remove('active', 'locked');
        }
    });

    document.addEventListener('click', function (e) {
        if (isLocked && !profileMenu.contains(e.target)) {
            isLocked = false;
            profileDropdown.classList.remove('show', 'locked');
            profileMenu.classList.remove('active', 'locked');
        }
    });

    profileDropdown.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    const dropdownItems = profileDropdown.querySelectorAll('.dropdown-item');
    dropdownItems.forEach(item => {
        item.addEventListener('click', function (e) {
            isLocked = false;
            profileDropdown.classList.remove('show', 'locked');
            profileMenu.classList.remove('active', 'locked');
        });
    });
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
    <span>${message}</span>
  `;

    toast.style.cssText = `
    position: fixed;
    top: 80px;
    right: 20px;
    padding: 16px 24px;
    background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#3b82f6'};
    color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
    z-index: 10000;
    animation: slideInRight 0.3s ease;
  `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Get notification count from API
 */
async function getNotificationCount() {
    try {
        const companyId = window.CURRENT_COMPANY_ID || 0;
        if (!companyId) return 0;

        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/notifications.php?action=count&user_id=${companyId}&user_type=company`);
        const data = await response.json();

        return data.success ? (data.count || 0) : 0;
    } catch (error) {
        console.error('Error getting notification count:', error);
        return 0;
    }
}

/**
 * Refresh notifications from server
 */
async function refreshNotifications() {
    try {
        const count = await getNotificationCount();
        updateNotificationBadge(count);

    } catch (error) {
        console.error('⚠️ Error fetching notifications:', error);
    }
}

// Export functions for external use
window.updatePageHeader = updatePageHeader;
window.updatePageHeaderFromURL = updatePageHeaderFromURL;
window.setPageTitle = setPageTitle;
window.showToast = showToast;
window.refreshNotifications = refreshNotifications;
window.getNotificationCount = getNotificationCount;
window.toggleNotificationDropdown = toggleNotificationDropdown;
window.initializeTopbar = initializeTopbar;

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTopbar);
} else {
    initializeTopbar();
}

// Auto-refresh notifications every 30 seconds
setInterval(refreshNotifications, 30000);
