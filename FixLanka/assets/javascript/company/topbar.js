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
    slogan: 'Manage customer repair requests - view opportunities and handle requests'
  },
  'requests': {
    title: 'Repair Requests',
    slogan: 'Manage customer repair requests - view opportunities and handle requests'
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
  'profile': {
    title: 'My Profile',
    slogan: 'Manage your personal and company information'
  },
  'reviews': {
    title: 'Reviews & Feedback',
    slogan: 'Monitor customer feedback for your company'
  },
  'settings': {
    title: 'Settings',
    slogan: 'Configure your account and system preferences'
  },
  'advertisements': {
    title: 'Advertisements',
    slogan: 'Create and manage your business advertisements'
  },
  'feedback': {
    title: 'Feedback',
    slogan: 'View and respond to customer feedback'
  }
};

// Initialize all topbar functionality
function initializeTopbar() {
  // Prevent multiple initializations
  if (window.topbarInitialized) {
    return;
  }
  window.topbarInitialized = true;

  // Update page title from URL (in case inline script didn't run)
  updatePageHeaderFromURL();
  initSearch();
  initNotifications();
  initProfileDropdown();

  console.log('✅ Topbar initialized successfully');
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
  }

  if (pageSlogan) {
    pageSlogan.textContent = info.slogan;
  }

  // Update browser tab title
  document.title = `${info.title} - FixLanka Company Dashboard`;

  console.log('📄 Page title updated to:', info.title);
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

// Export functions for global access
if (typeof window !== 'undefined') {
  window.updatePageHeader = updatePageHeader;
  window.updatePageHeaderFromURL = updatePageHeaderFromURL;
  window.setPageTitle = setPageTitle;
}

/**
 * Initialize Search Functionality
 */
function initSearch() {
  const searchBox = document.querySelector('.search-box');
  const searchInput = searchBox ? searchBox.querySelector('input[type="text"]') : null;
  
  if (!searchInput) return;
  
  // Focus effect
  searchInput.addEventListener('focus', function() {
    searchBox.classList.add('focused');
  });
  
  searchInput.addEventListener('blur', function() {
    searchBox.classList.remove('focused');
  });
  
  // Search functionality
  searchInput.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase().trim();
    
    if (searchTerm.length > 0) {
      performSearch(searchTerm);
    }
  });
  
  // Enter key to search
  searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      const searchTerm = e.target.value.toLowerCase().trim();
      if (searchTerm.length > 0) {
        performSearch(searchTerm);
        console.log('🔍 Searching for:', searchTerm);
      }
    }
  });
}

/**
 * Perform search across the system
 */
function performSearch(searchTerm) {
  const searchablePages = {
    'projects': ['project', 'repair', 'service', 'work', 'task'],
    'workforce': ['worker', 'employee', 'staff', 'team', 'repairer', 'freelancer'],
    'repair-requests': ['request', 'customer', 'repair', 'quote', 'estimate'],
    'contracts': ['contract', 'agreement', 'terms', 'legal', 'document'],
    'payments': ['payment', 'invoice', 'transaction', 'billing', 'finance'],
    'support': ['support', 'help', 'ticket', 'issue', 'problem']
  };
  
  const matches = [];
  for (const [page, keywords] of Object.entries(searchablePages)) {
    if (keywords.some(keyword => keyword.includes(searchTerm) || searchTerm.includes(keyword))) {
      matches.push(page);
    }
  }
  
  if (matches.length > 0) {
    console.log('✅ Search results:', matches);
  } else {
    console.log('❌ No results found for:', searchTerm);
  }
}

/**
 * Initialize Notifications
 */
function initNotifications() {
  const notificationBell = document.querySelector('.notification-bell');
  if (!notificationBell) return;
  
  notificationBell.addEventListener('click', function(e) {
    e.stopPropagation();
    toggleNotificationDropdown();
  });
  
  document.addEventListener('click', function(e) {
    const notificationDropdown = document.querySelector('.notification-dropdown');
    if (notificationDropdown && !notificationBell.contains(e.target)) {
      notificationDropdown.remove();
    }
  });
}

/**
 * Toggle notification dropdown
 */
function toggleNotificationDropdown() {
  const existingDropdown = document.querySelector('.notification-dropdown');
  if (existingDropdown) {
    existingDropdown.remove();
    return;
  }
  
  const dropdown = document.createElement('div');
  dropdown.className = 'notification-dropdown';
  dropdown.innerHTML = `
    <div class="notification-header">
      <h4>Notifications</h4>
      <button class="mark-all-read">Mark all as read</button>
    </div>
    <div class="notification-list">
      <div class="notification-item unread">
        <div class="notification-icon">
          <i class="fas fa-file-contract"></i>
        </div>
        <div class="notification-content">
          <h5>New Contract Signed</h5>
          <p>John Perera signed the contract for Air Conditioner Repair</p>
          <span class="notification-time">5 minutes ago</span>
        </div>
      </div>
      <div class="notification-item unread">
        <div class="notification-icon">
          <i class="fas fa-tools"></i>
        </div>
        <div class="notification-content">
          <h5>New Repair Request</h5>
          <p>Customer submitted a plumbing repair request in Kandy</p>
          <span class="notification-time">1 hour ago</span>
        </div>
      </div>
      <div class="notification-item">
        <div class="notification-icon">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="notification-content">
          <h5>Payment Received</h5>
          <p>Payment of LKR 125,000 received from ABC Corporation</p>
          <span class="notification-time">3 hours ago</span>
        </div>
      </div>
    </div>
    <div class="notification-footer">
      <a href="support.php">View All Notifications</a>
    </div>
  `;
  
  dropdown.style.cssText = `
    position: absolute;
    top: 60px;
    right: 80px;
    width: 360px;
    max-height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    overflow: hidden;
    animation: slideDown 0.3s ease;
  `;
  
  document.body.appendChild(dropdown);
  
  const markAllBtn = dropdown.querySelector('.mark-all-read');
  markAllBtn.addEventListener('click', function() {
    const unreadItems = dropdown.querySelectorAll('.notification-item.unread');
    unreadItems.forEach(item => item.classList.remove('unread'));
    updateNotificationBadge(0);
  });
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

  console.log('✅ Profile dropdown initialized successfully!');

  profileMenu.addEventListener('mouseenter', function() {
    if (!isLocked) {
      clearTimeout(hoverTimeout);
      hoverTimeout = setTimeout(() => {
        profileDropdown.classList.add('show');
        profileMenu.classList.add('active');
      }, 150);
    }
  });

  profileMenu.addEventListener('mouseleave', function() {
    clearTimeout(hoverTimeout);
    if (!isLocked) {
      profileDropdown.classList.remove('show');
      profileMenu.classList.remove('active');
    }
  });

  profileTrigger.addEventListener('click', function(e) {
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

  document.addEventListener('click', function(e) {
    if (isLocked && !profileMenu.contains(e.target)) {
      isLocked = false;
      profileDropdown.classList.remove('show', 'locked');
      profileMenu.classList.remove('active', 'locked');
    }
  });

  profileDropdown.addEventListener('click', function(e) {
    e.stopPropagation();
  });

  const dropdownItems = profileDropdown.querySelectorAll('.dropdown-item');
  dropdownItems.forEach(item => {
    item.addEventListener('click', function(e) {
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
 * Get notification count
 */
function getNotificationCount() {
  return 3;
}

/**
 * Refresh notifications from server
 */
async function refreshNotifications() {
  try {
    console.log('📄 Refreshing notifications...');
    updateNotificationBadge(getNotificationCount());
  } catch (error) {
    console.error('❌ Error fetching notifications:', error);
  }
}

// Export functions for external use
window.updatePageHeader = updatePageHeader;
window.updatePageHeaderFromURL = updatePageHeaderFromURL;
window.setPageTitle = setPageTitle;
window.showToast = showToast;
window.refreshNotifications = refreshNotifications;
window.getNotificationCount = getNotificationCount;
window.initializeTopbar = initializeTopbar;

// Initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeTopbar);
} else {
  initializeTopbar();
}

// Auto-refresh notifications every 30 seconds
setInterval(refreshNotifications, 30000);

console.log('✅ Topbar fully loaded and functional!');