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
      <h1 class="page-title" id="page-title">Dashboard</h1>
      <p class="page-slogan" id="page-slogan">Welcome back, let's see what's happening today</p>
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
        <img src="/2nd-Year-Group-Project/FixLanka/assets/images/user.png" alt="Admin" class="profile-avatar">
        <div class="profile-status-indicator"></div>
      </div>
      <div class="dropdown-menu" id="profileDropdown">
        <div class="dropdown-header">
          <img src="/2nd-Year-Group-Project/FixLanka/assets/images/user.png" alt="Admin" class="dropdown-avatar">
          <div class="dropdown-user-info">
            <h4>Dilanka</h4>
            <p>dilanka@gmail.com</p>
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
