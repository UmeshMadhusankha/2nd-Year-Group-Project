<!-- Header/Topbar Component -->
<header class="header">
  <div class="header-left">
    <label for="sidebar-toggle" class="sidebar-toggle">
      <i class="fas fa-bars"></i>
    </label>
    <div class="logo">
      <img src="../../assets/images/fixlanka.png" alt="FixLanka" class="logo-image">
    </div>
    <div class="page-info">
      <h1 class="page-title" id="page-title">Dashboard</h1>
      <p class="page-slogan" id="page-slogan">Welcome to FixLanka</p>
    </div>
  </div>

  <div class="header-right">
    <div class="search-box">
      <i class="fas fa-search"></i>
      <input type="text" placeholder="Search requests, repairers, projects...">
    </div>

    <div class="notification-bell">
      <i class="fas fa-bell"></i>
      <span class="notification-badge">3</span>
    </div>

    <!-- Profile Dropdown -->
    <div class="profile-menu" id="profileMenu">
      <div class="profile-trigger">
        <img src="../../assets/images/user.png" alt="Admin" class="profile-avatar">
        <div class="profile-status-indicator"></div>
      </div>
      <div class="dropdown-menu" id="profileDropdown">
        <div class="dropdown-header">
          <img src="../../assets/images/user.png" alt="Admin" class="dropdown-avatar">
          <div class="dropdown-user-info">
            <h4>John Doe</h4>
            <p>john.doe@fixlanka.com</p>
          </div>
        </div>
        <div class="dropdown-divider"></div>
        <a href="profile.php" class="dropdown-item">
          <i class="fas fa-user"></i>
          <span>My Profile</span>
        </a>
        <a href="settings.php" class="dropdown-item">
          <i class="fas fa-cog"></i>
          <span>Settings</span>
        </a>
        <a href="support.php" class="dropdown-item">
          <i class="fas fa-question-circle"></i>
          <span>Help & Support</span>
        </a>
        <div class="dropdown-divider"></div>
        <a href="../../index.php" class="dropdown-item logout" onclick="return confirm('Are you sure you want to logout?');">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </div>
    </div>
  </div>
</header>

<!-- Initialize page title IMMEDIATELY with inline script -->
<script>
  // This runs immediately, before any external scripts
  (function() {
    try {
      // Get current page from URL
      const currentPath = window.location.pathname;
      const fileName = currentPath.split('/').pop().replace('.php', '');
      const pageName = fileName || 'dashboard';
      
      // Page information mapping
      const pageData = {
        'dashboard': { title: 'Dashboard', slogan: 'Welcome back, let\'s see what\'s happening today' },
        'repair-requests': { title: 'Repair Requests', slogan: 'Manage customer repair requests - view opportunities and handle requests' },
        'projects': { title: 'Projects', slogan: 'Manage and track your repair projects efficiently' },
        'workforce': { title: 'Workforce', slogan: 'Manage your team and freelancers all in one place' },
        'payments': { title: 'Payments', slogan: 'Track payments and financial transactions' },
        'contracts': { title: 'Contracts', slogan: 'Manage agreements and legal documents' },
        'advertisements': { title: 'Advertisements', slogan: 'Create and manage your business advertisements' },
        'support': { title: 'Help & Support', slogan: 'Get help and manage customer support tickets' },
        'settings': { title: 'Settings', slogan: 'Configure your account and system preferences' },
        'profile': { title: 'My Profile', slogan: 'Manage your personal and company information' },
        'reviews': { title: 'Reviews & Feedback', slogan: 'Monitor customer feedback for your company' },
        'feedback': { title: 'Feedback', slogan: 'View and respond to customer feedback' }
      };
      
      // Get page info or use default
      const info = pageData[pageName] || { title: 'Dashboard', slogan: 'FixLanka Company Dashboard' };
      
      // Update title and slogan immediately
      const titleElement = document.getElementById('page-title');
      const sloganElement = document.getElementById('page-slogan');
      
      if (titleElement) {
        titleElement.textContent = info.title;
      }
      
      if (sloganElement) {
        sloganElement.textContent = info.slogan;
      }
      
      // Update browser tab title
      document.title = info.title + ' - FixLanka Company Dashboard';
      
      console.log('✅ Page title set to:', info.title);
    } catch (error) {
      console.error('Error setting page title:', error);
    }
  })();
</script>

<!-- Load Topbar JavaScript -->
<script src="../../assets/javascript/company/topbar.js"></script>
