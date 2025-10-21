<!-- Header/Topbar Component -->
<header class="header">
    <div class="header-left">
        <label for="sidebar-toggle" class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </label>
        <div class="logo">
            <img src="../../../assets/images/fixlanka.png" alt="FixLanka" class="logo-image">
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
            <div class="notification-dropdown">
                <div class="notification-dropdown-header">
                    <h4 class="notification-dropdown-title">Notifications</h4>
                    <a href="#" class="mark-all-read">Mark all as read</a>
                </div>
                <ul class="notification-dropdown-list">
                    <li class="notification-item unread">
                        <div class="notification-icon new-job">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="notification-content">
                            <h5 class="notification-title">New Job Request</h5>
                            <p class="notification-description">Water heater repair needed in Colombo 07</p>
                            <span class="notification-time">5 minutes ago</span>
                        </div>
                    </li>
                    <li class="notification-item unread">
                        <div class="notification-icon quote-response">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="notification-content">
                            <h5 class="notification-title">Quote Accepted</h5>
                            <p class="notification-description">Customer accepted your quote for LKR 8,500</p>
                            <span class="notification-time">1 hour ago</span>
                        </div>
                    </li>
                    <li class="notification-item unread">
                        <div class="notification-icon payment">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="notification-content">
                            <h5 class="notification-title">Payment Received</h5>
                            <p class="notification-description">LKR 12,000 received for Job #1234</p>
                            <span class="notification-time">2 hours ago</span>
                        </div>
                    </li>
                    <li class="notification-item">
                        <div class="notification-icon review">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="notification-content">
                            <h5 class="notification-title">New Review</h5>
                            <p class="notification-description">You received a 5-star review from Sarah</p>
                            <span class="notification-time">5 hours ago</span>
                        </div>
                    </li>
                    <li class="notification-item">
                        <div class="notification-icon system">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="notification-content">
                            <h5 class="notification-title">System Update</h5>
                            <p class="notification-description">New features available in your dashboard</p>
                            <span class="notification-time">1 day ago</span>
                        </div>
                    </li>
                </ul>
                <div class="notification-dropdown-footer">
                    <a href="#" class="view-all-notifications"></a>
                </div>
            </div>
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
