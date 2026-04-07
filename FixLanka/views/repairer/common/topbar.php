<!-- Header/Topbar Component -->
<?php
// Load profile picture for the topbar avatar
$topbarAvatarUrl = '/2nd-Year-Group-Project/FixLanka/assets/images/user.png';
if (isset($_SESSION['user_id'])) {
    try {
        require_once __DIR__ . '/../../../config/database.php';
        $avatarStmt = $pdo->prepare("SELECT profilePicture FROM repairer WHERE repairer_id = ? LIMIT 1");
        $avatarStmt->execute([(int)$_SESSION['user_id']]);
        $avatarRow = $avatarStmt->fetch(PDO::FETCH_ASSOC);
        if ($avatarRow && !empty($avatarRow['profilePicture'])) {
            $topbarAvatarUrl = '/2nd-Year-Group-Project/FixLanka/' . $avatarRow['profilePicture'];
        }
    } catch (Exception $e) {
        // Silently fall back to default avatar
    }
}
?>
<header class="header">
    <div class="header-left">
        <label for="sidebar-toggle" class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </label>
        <div class="logo">
            <img src="/2nd-Year-Group-Project/FixLanka/assets/images/fixlanka.png" alt="FixLanka" class="logo-image">
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
            <span class="notification-badge" id="notificationBadge">0</span>
            <div class="notification-dropdown">
                <div class="notification-dropdown-header">
                    <h4 class="notification-dropdown-title">Notifications</h4>
                    <a href="#" class="mark-all-read">Mark all as read</a>
                </div>
                <ul class="notification-dropdown-list" id="notificationList">
                </ul>
                <div class="notification-dropdown-footer">
                    <a href="#" class="view-all-notifications"></a>
                </div>
            </div>
        </div>
        <div class="profile-menu">
            <img src="<?php echo htmlspecialchars($topbarAvatarUrl); ?>" alt="Profile" class="profile-avatar">
            <div class="profile-dropdown">
                <div class="profile-dropdown-header">
                    <h4 class="profile-dropdown-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></h4>
                    <p class="profile-dropdown-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
                </div>
                <ul class="profile-dropdown-menu">
                    <li class="profile-dropdown-item">
                        <a href="/2nd-Year-Group-Project/FixLanka/repairer-profile" class="profile-dropdown-link">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li class="profile-dropdown-item">
                        <a href="/2nd-Year-Group-Project/FixLanka/repairer-settings" class="profile-dropdown-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="profile-dropdown-item">
                        <a href="/2nd-Year-Group-Project/FixLanka/repairer-upgrade" class="profile-dropdown-link">
                            <i class="fas fa-crown"></i>
                            <span>Upgrade</span>
                        </a>
                    </li>
                    <li class="profile-dropdown-divider"></li>
                    <li class="profile-dropdown-item">
                        <a href="/2nd-Year-Group-Project/FixLanka/logout" class="profile-dropdown-link logout" data-action="logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
