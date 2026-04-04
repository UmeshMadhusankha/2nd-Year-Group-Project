<?php
/**
 * Company Topbar Component
 * Displays header with user info from session
 */

// Get user data from session if not already available
if (!isset($userData)) {
    require_once '../../config/session.php';
    $userData = getUserData();
}

// Extract user information
$companyName = $userData['company_name'] ?? $userData['name'] ?? 'Company';
$userEmail = $userData['email'] ?? 'user@example.com';
$profilePhoto = $userData['profile_photo'] ?? '/2nd-Year-Group-Project/FixLanka/assets/images/user.png';

// Generate initials for avatar if no photo
$initials = '';
if (empty($profilePhoto) || $profilePhoto === '/2nd-Year-Group-Project/FixLanka/assets/images/user.png') {
    $nameParts = explode(' ', $companyName);
    $initials = strtoupper(substr($nameParts[0], 0, 1));
    if (count($nameParts) > 1) {
        $initials .= strtoupper(substr($nameParts[1], 0, 1));
    }
}

// Detect current page and set title/slogan server-side
// Check if page parameter is passed via GET, otherwise detect from referer or use default
if (isset($_GET['page']) && !empty($_GET['page'])) {
    $currentPage = $_GET['page'];
} else {
    // Try to detect from HTTP_REFERER
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if (!empty($referer)) {
        $refererPath = parse_url($referer, PHP_URL_PATH);
        $currentPage = basename($refererPath, '.php');
    } else {
        $currentPage = basename($_SERVER['PHP_SELF'], '.php');
    }
}

if ($currentPage === 'index' || $currentPage === '' || $currentPage === 'topbar') {
    $currentPage = 'dashboard';
}

// Page information array
$pageInfo = [
    'dashboard' => ['title' => 'Dashboard', 'slogan' => 'Welcome back, let\'s see what\'s happening today'],
    'index' => ['title' => 'Dashboard', 'slogan' => 'Welcome back, let\'s see what\'s happening today'],
    'projects' => ['title' => 'Projects', 'slogan' => 'Manage and track your repair projects efficiently'],
    'workforce' => ['title' => 'Workforce', 'slogan' => 'Manage your team and freelancers all in one place'],
    'repair-requests' => ['title' => 'Repair Requests', 'slogan' => 'Manage customer repair requests - view public opportunities and handle direct requests'],
    'requests' => ['title' => 'Repair Requests', 'slogan' => 'Manage customer repair requests - view public opportunities and handle direct requests'],
  'notifications' => ['title' => 'Notifications', 'slogan' => 'View all your company notifications'],
    'support' => ['title' => 'Help & Support', 'slogan' => 'Get help and manage customer support tickets'],
    'contracts' => ['title' => 'Contracts', 'slogan' => 'Manage agreements and legal documents'],
    'payments' => ['title' => 'Payment Reports', 'slogan' => 'Income payments and expense tracking for your repair services'],
    'advertisements' => ['title' => 'Advertisement Management', 'slogan' => 'Promote your services to a wider audience'],
    'profile' => ['title' => 'My Profile', 'slogan' => 'Manage your personal and company information'],
    'reviews' => ['title' => 'Reviews & Feedback', 'slogan' => 'Monitor and manage customer feedback for your company'],
    'settings' => ['title' => 'Settings', 'slogan' => 'Configure your account and system preferences']
];

// Get current page info or default to dashboard
$currentPageInfo = $pageInfo[$currentPage] ?? $pageInfo['dashboard'];
$pageTitle = $currentPageInfo['title'];
$pageSlogan = $currentPageInfo['slogan'];
?>
<!-- Header/Topbar Component -->
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
      <p class="page-slogan" id="page-slogan"><?php echo htmlspecialchars($pageSlogan); ?></p>
    </div>
  </div>

  <div class="header-right">
    <div class="search-box">
      <div class="custom-select-wrapper" id="searchCategoryWrapper">
        <div class="custom-select-trigger">
            <span id="selectedCategoryText">All</span>
            <i class="fas fa-chevron-down category-icon"></i>
        </div>
        <div class="custom-options">
            <span class="custom-option selected" data-value="all">All</span>
            <span class="custom-option" data-value="projects">Projects</span>
            <span class="custom-option" data-value="requests">Requests</span>
            <span class="custom-option" data-value="workforce">Workforce</span>
            <span class="custom-option" data-value="payments">Payments</span>
        </div>
        <input type="hidden" id="searchCategory" value="all">
      </div>
      <div class="search-divider"></div>
      <input type="text" id="globalSearchInput" placeholder="Search...">
      <div id="searchResults" class="search-results-dropdown"></div>
    </div>

    <div class="notification-bell" id="notificationBell">
      <i class="fas fa-bell"></i>
      <span class="notification-badge" id="notificationCount">0</span>
    </div>

    <!-- Profile Dropdown -->
    <div class="profile-menu" id="profileMenu">
      <div class="profile-trigger">
        <?php if (!empty($profilePhoto) && $profilePhoto !== '/2nd-Year-Group-Project/FixLanka/assets/images/user.png'): ?>
          <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="Profile" class="profile-avatar">
        <?php elseif (!empty($initials)): ?>
          <div class="profile-avatar profile-avatar-initials"><?php echo $initials; ?></div>
        <?php else: ?>
          <img src="/2nd-Year-Group-Project/FixLanka/assets/images/user.png" alt="Profile" class="profile-avatar">
        <?php endif; ?>
        <div class="profile-status-indicator"></div>
      </div>
      <div class="dropdown-menu" id="profileDropdown">
        <div class="dropdown-header">
          <?php if (!empty($profilePhoto) && $profilePhoto !== '/2nd-Year-Group-Project/FixLanka/assets/images/user.png'): ?>
            <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="Profile" class="dropdown-avatar">
          <?php elseif (!empty($initials)): ?>
            <div class="dropdown-avatar dropdown-avatar-initials"><?php echo $initials; ?></div>
          <?php else: ?>
            <img src="/2nd-Year-Group-Project/FixLanka/assets/images/user.png" alt="Profile" class="dropdown-avatar">
          <?php endif; ?>
          <div class="dropdown-user-info">
            <h4><?php echo htmlspecialchars($companyName); ?></h4>
            <p><?php echo htmlspecialchars($userEmail); ?></p>
          </div>
        </div>
        <div class="dropdown-divider"></div>
        <a href="/2nd-Year-Group-Project/FixLanka/views/company/profile.php" class="dropdown-item">
          <i class="fas fa-user"></i>
          <span>My Profile</span>
        </a>
        <a href="/2nd-Year-Group-Project/FixLanka/views/company/settings.php" class="dropdown-item">
          <i class="fas fa-cog"></i>
          <span>Settings</span>
        </a>
        <a href="/2nd-Year-Group-Project/FixLanka/views/company/support.php" class="dropdown-item">
          <i class="fas fa-question-circle"></i>
          <span>Help & Support</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="/2nd-Year-Group-Project/FixLanka/logout" class="dropdown-item logout" onclick="return confirm('Are you sure you want to logout?');">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </div>
    </div>
  </div>
</header>

<!-- Load Topbar JavaScript -->
<script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/topbar.js"></script>
<script>
// Immediately initialize notification bell after topbar loads
(function() {
    // Define global company ID for external scripts
    window.CURRENT_COMPANY_ID = <?php echo json_encode($userData['id'] ?? 0); ?>;
    
    console.log('🔄 Topbar inline script executing...');
    
    // Check if topbar.js has already loaded
    if (typeof window.initializeTopbar === 'function') {
        window.initializeTopbar();
    } else {
        // If not, wait for DOMContentLoaded which is handled in topbar.js
        console.log('⏳ Waiting for topbar.js to initialize...');
    }
})();

// Load notification count
document.addEventListener('DOMContentLoaded', function() {
    if (typeof loadNotificationCount === 'function') {
        loadNotificationCount();
    }
});

async function loadNotificationCount() {
    try {
        const companyId = <?php echo json_encode($userData['id'] ?? 0); ?>;
        if (!companyId) return;
        
        // Use the global function if available
        if (typeof window.getNotificationCount === 'function') {
            const count = await window.getNotificationCount();
            const badge = document.getElementById('notificationCount');
            if (count > 0) {
                badge.textContent = count > 9 ? '9+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error loading notification count:', error);
    }
}
</script>
