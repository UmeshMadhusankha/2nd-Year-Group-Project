/**
 * Common JavaScript Functions
 * Shared functionality across all pages
 */

/**
 * Initialize common functionality
 */
document.addEventListener('DOMContentLoaded', function () {
    initializeSidebar();
    initializeSearch();
    initializeNotifications();
    initializeProfileMenu();
});

/**
 * Sidebar Navigation Handler
 */
function initializeSidebar() {
    const navLinks = document.querySelectorAll('.sidebar .nav-link');

    navLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            // If it's a hash link (internal page section), prevent default
            const href = this.getAttribute('href');
            if (href && href.startsWith('#')) {
                e.preventDefault();
            }

            // Remove active class from all nav items
            const allNavItems = document.querySelectorAll('.sidebar .nav-item');
            allNavItems.forEach(function (item) {
                item.classList.remove('active');
            });

            // Add active class to clicked nav item
            this.parentElement.classList.add('active');
        });
    });
}

/**
 * Search functionality
 */
function initializeSearch() {
    const searchInput = document.querySelector('.search-box input');

    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            const query = e.target.value.trim();

            if (query.length > 2) {
                // Debounce search
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            }
        });

        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = e.target.value.trim();
                if (query) {
                    performSearch(query);
                }
            }
        });
    }
}

/**
 * Perform search operation
 */
function performSearch(query) {

    // In a real application, this would make an API call
    // For now, just show a simple message
    showSearchResults(query);
}

/**
 * Show search results (placeholder)
 */
function showSearchResults(query) {
    // This would typically show a dropdown or navigate to search results page
    // For now, just log to console

}

/**
 * Notification bell functionality
 */
function initializeNotifications() {
    const notificationBell = document.querySelector('.notification-bell');

    if (notificationBell) {
        notificationBell.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            toggleNotificationDropdown();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!notificationBell.contains(e.target)) {
                closeNotificationDropdown();
            }
        });

        // Close dropdown when pressing Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeNotificationDropdown();
            }
        });

        // Handle notification item clicks
        const notificationItems = notificationBell.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.addEventListener('click', function (e) {
                e.stopPropagation();
                handleNotificationClick(this);
            });
        });

        // Handle mark all as read
        const markAllRead = notificationBell.querySelector('.mark-all-read');
        if (markAllRead) {
            markAllRead.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                markAllNotificationsAsRead();
            });
        }
    }
}

/**
 * Toggle notification dropdown
 */
function toggleNotificationDropdown() {
    const notificationBell = document.querySelector('.notification-bell');
    const profileMenu = document.querySelector('.profile-menu');

    if (notificationBell) {
        const isActive = notificationBell.classList.contains('active');

        // Close profile menu if open
        if (profileMenu) {
            profileMenu.classList.remove('active');
        }

        if (isActive) {
            closeNotificationDropdown();
        } else {
            openNotificationDropdown();
        }
    }
}

/**
 * Open notification dropdown
 */
function openNotificationDropdown() {
    const notificationBell = document.querySelector('.notification-bell');
    if (notificationBell) {
        notificationBell.classList.add('active');

    }
}

/**
 * Close notification dropdown
 */
function closeNotificationDropdown() {
    const notificationBell = document.querySelector('.notification-bell');
    if (notificationBell) {
        notificationBell.classList.remove('active');

    }
}

/**
 * Handle notification item click
 */
function handleNotificationClick(notificationItem) {
    // Mark notification as read
    notificationItem.classList.remove('unread');

    // Update badge count
    updateNotificationBadge();

    // Get notification details and perform action
    const title = notificationItem.querySelector('.notification-title')?.textContent;

    // You can add navigation or modal display here
    // For example:
    // window.location.href = '/notifications/detail?id=' + notificationId;
}

/**
 * Mark all notifications as read
 */
function markAllNotificationsAsRead() {
    const notificationItems = document.querySelectorAll('.notification-item.unread');
    notificationItems.forEach(item => {
        item.classList.remove('unread');
    });

    // Update badge count
    updateNotificationBadge();


}

/**
 * Update notification badge count
 */
function updateNotificationBadge() {
    const badge = document.querySelector('.notification-badge');
    const unreadCount = document.querySelectorAll('.notification-item.unread').length;

    if (badge) {
        if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }
}

/**
 * Profile menu functionality
 */
function initializeProfileMenu() {
    const profileMenu = document.querySelector('.profile-menu');

    if (profileMenu) {
        profileMenu.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            toggleProfileMenu();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!profileMenu.contains(e.target)) {
                closeProfileMenu();
            }
        });

        // Close dropdown when pressing Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeProfileMenu();
            }
        });

        // Handle dropdown link clicks
        const dropdownLinks = profileMenu.querySelectorAll('.profile-dropdown-link');
        dropdownLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.stopPropagation();
                handleProfileMenuAction(this.getAttribute('data-action'));
            });
        });
    }
}

/**
 * Toggle profile menu dropdown
 */
function toggleProfileMenu() {
    const profileMenu = document.querySelector('.profile-menu');
    const notificationBell = document.querySelector('.notification-bell');

    if (profileMenu) {
        const isActive = profileMenu.classList.contains('active');

        // Close notification dropdown if open
        if (notificationBell) {
            notificationBell.classList.remove('active');
        }

        if (isActive) {
            closeProfileMenu();
        } else {
            openProfileMenu();
        }
    }
}

/**
 * Open profile menu dropdown
 */
function openProfileMenu() {
    const profileMenu = document.querySelector('.profile-menu');
    if (profileMenu) {
        profileMenu.classList.add('active');
    }
}

/**
 * Close profile menu dropdown
 */
function closeProfileMenu() {
    const profileMenu = document.querySelector('.profile-menu');
    if (profileMenu) {
        profileMenu.classList.remove('active');
    }
}

/**
 * Handle profile menu actions
 */
function handleProfileMenuAction(action) {
    switch (action) {
        case 'profile':
            window.location.href = 'profile.php';
            break;
        case 'settings':
            window.location.href = 'settings.php';
            break;
        case 'upgrade':
            window.location.href = 'upgrade.php';
            break;
        case 'support':
            window.location.href = 'support.php';
            break;
        case 'logout':
            handleLogout();
            break;
        default:

    }

    closeProfileMenu();
}

/**
 * Handle logout functionality
 */
function handleLogout() {
    if (confirm('Are you sure you want to logout?')) {
        // Clear any stored user data
        localStorage.removeItem('user_session');
        sessionStorage.clear();

        // Redirect to login page
        window.location.href = '../login.php';
    }
}

/**
 * Utility function to show toast messages
 */
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.textContent = message;

    const colors = {
        success: 'var(--success-color)',
        warning: 'var(--warning-color)',
        error: 'var(--danger-color)',
        info: 'var(--info-color)'
    };

    toast.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${colors[type] || colors.success};
        color: var(--text-white);
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: var(--shadow-lg);
        z-index: 9999;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        transform: translateX(100%);
        max-width: 300px;
    `;

    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);

    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

/**
 * Utility function to format currency
 */
function formatCurrency(amount) {
    return `LKR ${amount.toLocaleString()}`;
}

/**
 * Utility function to format dates
 */
function formatDate(date) {
    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    };
    return new Date(date).toLocaleDateString('en-US', options);
}

/**
 * Utility function to calculate relative time
 */
function getRelativeTime(date) {
    const now = new Date();
    const past = new Date(date);
    const diffInSeconds = Math.floor((now - past) / 1000);

    if (diffInSeconds < 60) {
        return 'Just now';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} hour${hours > 1 ? 's' : ''} ago`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} day${days > 1 ? 's' : ''} ago`;
    }
}

/**
 * Handle responsive sidebar toggle
 */
function handleSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const body = document.body;

    if (sidebarToggle) {
        sidebarToggle.addEventListener('change', function () {
            if (this.checked) {
                body.classList.add('sidebar-open');
            } else {
                body.classList.remove('sidebar-open');
            }
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (e) {
            const sidebar = document.querySelector('.sidebar');
            const toggleButton = document.querySelector('.sidebar-toggle');

            if (window.innerWidth <= 768 &&
                sidebarToggle.checked &&
                !sidebar.contains(e.target) &&
                !toggleButton.contains(e.target)) {
                sidebarToggle.checked = false;
                body.classList.remove('sidebar-open');
            }
        });
    }
}

// Initialize sidebar toggle handling
document.addEventListener('DOMContentLoaded', handleSidebarToggle);

/**
 * Global Alert/Confirmation System
 */

window.showAlert = function (message, type = 'info', title = 'Fix Lanka') {
    return new Promise((resolve) => {
        let overlay = document.getElementById('globalAlertOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'globalAlertOverlay';
            overlay.className = 'confirm-modal-overlay';
            document.body.appendChild(overlay);
        }

        const icons = {
            info: 'fas fa-info-circle',
            success: 'fas fa-check-circle',
            warning: 'fas fa-exclamation-triangle',
            danger: 'fas fa-exclamation-circle'
        };

        const iconClass = type === 'info' ? 'info' : (type === 'success' ? 'success' : (type === 'danger' ? 'danger' : 'warning'));

        overlay.innerHTML = `
            <div class="confirm-modal">
                <div class="confirm-header">
                    <div class="confirm-icon ${iconClass}">
                        <i class="${icons[type] || icons.info}"></i>
                    </div>
                    <h3 class="confirm-title">${title}</h3>
                    <p class="confirm-message">${message}</p>
                </div>
                <div class="confirm-footer">
                    <button class="btn-primary" id="globalAlertOkBtn" style="flex:1">OK</button>
                </div>
            </div>
        `;

        overlay.classList.add('show');

        overlay.querySelector('#globalAlertOkBtn').onclick = () => {
            overlay.classList.remove('show');
            resolve(true);
        };
    });
};

window.showConfirm = function (message, options = {}) {
    const {
        title = 'Confirmation',
        confirmText = 'Confirm',
        cancelText = 'Cancel',
        type = 'question', // question, danger, warning
        icon = 'fas fa-question-circle'
    } = options;

    return new Promise((resolve) => {
        let overlay = document.getElementById('globalConfirmOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'globalConfirmOverlay';
            overlay.className = 'confirm-modal-overlay';
            document.body.appendChild(overlay);
        }

        const iconClass = type;
        const btnClass = type === 'danger' ? 'btn-danger' : 'btn-primary';

        overlay.innerHTML = `
            <div class="confirm-modal">
                <div class="confirm-header">
                    <div class="confirm-icon ${iconClass}">
                        <i class="${icon}"></i>
                    </div>
                    <h3 class="confirm-title">${title}</h3>
                    <p class="confirm-message">${message}</p>
                </div>
                <div class="confirm-footer">
                    <button class="btn-cancel" id="globalConfirmCancelBtn">${cancelText}</button>
                    <button class="${btnClass}" id="globalConfirmSubmitBtn">${confirmText}</button>
                </div>
            </div>
        `;

        overlay.classList.add('show');

        const close = (result) => {
            overlay.classList.remove('show');
            resolve(result);
        };

        overlay.querySelector('#globalConfirmCancelBtn').onclick = () => close(false);
        overlay.querySelector('#globalConfirmSubmitBtn').onclick = () => close(true);
    });
};

window.showPrompt = function (message, defaultValue = '', options = {}) {
    const {
        title = 'Input Required',
        confirmText = 'Submit',
        cancelText = 'Cancel',
        placeholder = 'Enter your response...',
        type = 'info',
        icon = 'fas fa-pen'
    } = options;

    return new Promise((resolve) => {
        let overlay = document.getElementById('globalPromptOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'globalPromptOverlay';
            overlay.className = 'confirm-modal-overlay';
            document.body.appendChild(overlay);
        }

        const iconClass = type;

        overlay.innerHTML = `
            <div class="confirm-modal">
                <div class="confirm-header">
                    <div class="confirm-icon ${iconClass}">
                        <i class="${icon}"></i>
                    </div>
                    <h3 class="confirm-title">${title}</h3>
                    <p class="confirm-message">${message}</p>
                    <div style="padding: 15px 24px 0;">
                        <textarea id="globalPromptInput" 
                                  style="width:100%; padding:12px; border-radius:10px; border:1px solid #ddd; outline:none; font-family:inherit; min-height:80px;"
                                  placeholder="${placeholder}">${defaultValue}</textarea>
                    </div>
                </div>
                <div class="confirm-footer">
                    <button class="btn-cancel" id="globalPromptCancelBtn">${cancelText}</button>
                    <button class="btn-primary" id="globalPromptSubmitBtn">${confirmText}</button>
                </div>
            </div>
        `;

        overlay.classList.add('show');

        const input = overlay.querySelector('#globalPromptInput');
        input.focus();

        const close = (result) => {
            overlay.classList.remove('show');
            resolve(result);
        };

        overlay.querySelector('#globalPromptCancelBtn').onclick = () => close(null);
        overlay.querySelector('#globalPromptSubmitBtn').onclick = () => close(input.value);
    });
};


