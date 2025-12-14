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
    'support' => ['title' => 'Help & Support', 'slogan' => 'Get help and manage customer support tickets'],
    'contracts' => ['title' => 'Contracts', 'slogan' => 'Manage agreements and legal documents'],
    'payments' => ['title' => 'Payment Reports', 'slogan' => 'Income payments and expense tracking for your repair services'],
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
      <i class="fas fa-search"></i>
      <input type="text" placeholder="Search requests, repairers, projects...">
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
    console.log('🔄 Topbar inline script executing...');
    
    // Wait for topbar.js to load, then initialize
    function initializeNotificationBell() {
        const bell = document.getElementById('notificationBell') || document.querySelector('.notification-bell');
        
        if (!bell) {
            console.error('❌ Notification bell not found in DOM');
            return;
        }
        
        console.log('✅ Notification bell found, attaching click handler...');
        
        // Remove any existing listeners
        const newBell = bell.cloneNode(true);
        bell.parentNode.replaceChild(newBell, bell);
        
        // Attach click handler
        newBell.addEventListener('click', function(e) {
            e.stopPropagation();
            console.log('🔔 Bell clicked!');
            
            if (typeof toggleNotificationDropdown === 'function') {
                toggleNotificationDropdown();
            } else {
                console.error('❌ toggleNotificationDropdown not available yet, retrying...');
                setTimeout(function() {
                    if (typeof toggleNotificationDropdown === 'function') {
                        toggleNotificationDropdown();
                    } else {
                        alert('Notification system is still loading. Please try again in a moment.');
                    }
                }, 500);
            }
        });
        
        console.log('✅ Click handler attached successfully');
    }
    
    // Try immediately
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeNotificationBell);
    } else {
        initializeNotificationBell();
    }
    
    // Also try after a short delay to ensure everything is loaded
    setTimeout(initializeNotificationBell, 100);
})();

// Load notification count
document.addEventListener('DOMContentLoaded', function() {
    loadNotificationCount();
});

async function loadNotificationCount() {
    try {
        const companyId = <?php echo json_encode($userData['id'] ?? 0); ?>;
        if (!companyId) return;
        
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/notifications.php?action=count&user_id=${companyId}&user_type=company`);
        const data = await response.json();
        
        if (data.success && data.count > 0) {
            document.getElementById('notificationCount').textContent = data.count;
            document.getElementById('notificationCount').style.display = 'flex';
        } else {
            document.getElementById('notificationCount').style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading notification count:', error);
        document.getElementById('notificationCount').style.display = 'none';
    }
}
</script>
