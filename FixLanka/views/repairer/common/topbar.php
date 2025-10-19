<!-- Header/Topbar Component -->
<header class="header">
    <div class="header-left">
        <label for="sidebar-toggle" class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </label>
        <div class="logo">
            <img src="../common/fixlanka.png" alt="FixLanka" class="logo-image">
        </div>
        <div class="page-info">
            <h1 class="page-title"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
            <p class="page-subtitle"><?php echo isset($pageSubtitle) ? $pageSubtitle : 'Welcome to FixLanka'; ?></p>
        </div>
    </div>
    <div class="header-right">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="<?php echo isset($searchPlaceholder) ? $searchPlaceholder : 'Search...'; ?>">
        </div>
        <div class="notification-bell">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">3</span>
        </div>
        <div class="profile-menu">
            <img src="../common/user.png" alt="Admin" class="profile-avatar">
            <div class="profile-dropdown">
                <div class="profile-dropdown-header">
                    <h4 class="profile-dropdown-name">John Doe</h4>
                    <p class="profile-dropdown-email">john.doe@fixlanka.com</p>
                </div>
                <ul class="profile-dropdown-menu">
                    <li class="profile-dropdown-item">
                        <a href="profile.php" class="profile-dropdown-link">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li class="profile-dropdown-item">
                        <a href="settings.php" class="profile-dropdown-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="profile-dropdown-item">
                        <a href="upgrade.php" class="profile-dropdown-link">
                            <i class="fas fa-crown"></i>
                            <span>Upgrade</span>
                        </a>
                    </li>
                    <li class="profile-dropdown-divider"></li>
                    <li class="profile-dropdown-item">
                        <a href="#logout" class="profile-dropdown-link logout" data-action="logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
