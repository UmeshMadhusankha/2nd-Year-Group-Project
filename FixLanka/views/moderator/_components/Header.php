<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../includes/admin-modarator/auth.php';

function renderPageHeader($basePath, $title, $pageSubtitle = null, $currentPage = null, $searchPlaceholder = 'Search...')
{
    $user = getCurrentUser();
    $userName = $user ? htmlspecialchars($user['name']) : 'Guest';
    $userEmail = $user ? htmlspecialchars($user['email'] ?? 'guest@fixlanka.com') : 'guest@fixlanka.com';
    $userAvatar = $user['avatar'] ?? '../../assets/images/admin-moderator/placeholder-user.jpg'; // default avatar

    // Default values
    $pageSubtitle = $pageSubtitle ?: 'Welcome to FixLanka';
    $pageTitle = $title ?: 'Dashboard';

    echo '<!-- Header/Topbar Component -->';
    echo '<header class="header">';

    // Left side (logo, menu, title)
    echo '<div class="header-left">';
    echo '<label for="sidebar-toggle" class="sidebar-toggle"><i class="fas fa-bars"></i></label>';
    echo '<div class="logo">';
    echo '<img src="/2nd-Year-Group-Project/FixLanka/assets/images/admin-moderator/logo.jpg" alt="FixLanka" class="logo-image">';
    echo '</div>';
    echo '<div class="page-info">';
    echo '<h1 class="page-title">' . $pageTitle . '</h1>';
    echo '<p class="page-subtitle">' . $pageSubtitle . '</p>';
    echo '</div>';
    echo '</div>';

    // Right side (search, notifications, profile)
    echo '<div class="header-right">';
    echo '<div class="search-box">';
    echo '<i class="fas fa-search"></i>';
    echo '<input type="text" placeholder="' . htmlspecialchars($searchPlaceholder) . '">';
    echo '</div>';

    echo '<div class="notification-bell">';
    echo '<i class="fas fa-bell"></i>';
    echo '<span class="notification-badge">3</span>';
    echo '</div>';

    echo '<div class="profile-menu">';
    echo '<img src="' . htmlspecialchars($userAvatar) . '" alt="' . $userName . '" class="profile-avatar">';
    echo '<div class="profile-dropdown">';
    echo '<div class="profile-dropdown-header">';
    echo '<h4 class="profile-dropdown-name">' . $userName . '</h4>';
    echo '<p class="profile-dropdown-email">' . $userEmail . '</p>';
    echo '</div>';
    echo '<ul class="profile-dropdown-menu">';
    echo '<li class="profile-dropdown-item"><a href="/2nd-Year-Group-Project/FixLanka/moderator-dashboard" class="profile-dropdown-link"><i class="fas fa-user"></i><span>My Profile</span></a></li>';
    echo '<li class="profile-dropdown-item"><a href="/2nd-Year-Group-Project/FixLanka/moderator-dashboard" class="profile-dropdown-link"><i class="fas fa-cog"></i><span>Settings</span></a></li>';
    echo '<li class="profile-dropdown-divider"></li>';
    echo '<li class="profile-dropdown-item"><a href="/2nd-Year-Group-Project/FixLanka/logout" class="profile-dropdown-link logout" data-action="logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>';
    echo '</ul>';
    echo '</div>';
    echo '</div>'; // profile-menu
    echo '</div>'; // header-right

    echo '</header>';
}
