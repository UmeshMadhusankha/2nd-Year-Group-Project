<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../includes/admin-modarator/auth.php';
require_once __DIR__ . '/../../shared/topbar.php';

function renderPageHeader($basePath, $title, $pageSubtitle = null, $currentPage = null, $searchPlaceholder = 'Search...')
{
    $user = getCurrentUser();
    $userName = $user ? (string)$user['name'] : 'Guest';
    $userEmail = $user ? (string)($user['email'] ?? 'guest@fixlanka.com') : 'guest@fixlanka.com';
    $userAvatar = $user['avatar'] ?? '/2nd-Year-Group-Project/FixLanka/assets/images/admin-moderator/placeholder-user.jpg';

    $pageTitle = $title ?: 'Dashboard';
    $pageSubtitle = $pageSubtitle ?: 'Welcome to FixLanka';

    renderSharedTopbar([
        'role' => 'admin',
        'userId' => (int)($user['id'] ?? ($_SESSION['user_id'] ?? 0)),
        'userName' => $userName,
        'userEmail' => $userEmail,
        'avatarUrl' => $userAvatar,
        'pageTitle' => $pageTitle,
        'pageSlogan' => $pageSubtitle,
        'searchPlaceholder' => $searchPlaceholder,
        'searchCategories' => [
            ['value' => 'all', 'label' => 'All'],
            ['value' => 'users', 'label' => 'Users'],
            ['value' => 'moderators', 'label' => 'Moderators'],
            ['value' => 'ads', 'label' => 'Ads'],
            ['value' => 'finance', 'label' => 'Finance']
        ],
        'notificationsPageUrl' => '/2nd-Year-Group-Project/FixLanka/views/admin/alerts.php',
        'profileLinks' => [
            ['href' => '/2nd-Year-Group-Project/FixLanka/admin-dashboard', 'label' => 'My Profile', 'icon' => 'fas fa-user'],
            ['href' => '/2nd-Year-Group-Project/FixLanka/admin-dashboard', 'label' => 'Settings', 'icon' => 'fas fa-cog']
        ],
        'quickSearchLinks' => [
            ['title' => 'Admin Dashboard', 'subtitle' => 'System overview', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-dashboard', 'icon' => 'fa-gauge-high'],
            ['title' => 'User Management', 'subtitle' => 'Manage users', 'url' => '/2nd-Year-Group-Project/FixLanka/views/admin/users.php', 'icon' => 'fa-users'],
            ['title' => 'Moderator Management', 'subtitle' => 'Manage moderators', 'url' => '/2nd-Year-Group-Project/FixLanka/views/admin/moderators.php', 'icon' => 'fa-user-shield'],
            ['title' => 'Analytics', 'subtitle' => 'Platform insights', 'url' => '/2nd-Year-Group-Project/FixLanka/views/admin/analytics.php', 'icon' => 'fa-chart-line'],
            ['title' => 'Financial Overview', 'subtitle' => 'Revenue and transactions', 'url' => '/2nd-Year-Group-Project/FixLanka/views/admin/finance.php', 'icon' => 'fa-money-bill-trend-up'],
            ['title' => 'Send Alerts', 'subtitle' => 'Broadcast notifications', 'url' => '/2nd-Year-Group-Project/FixLanka/views/admin/alerts.php', 'icon' => 'fa-bell']
        ]
    ]);
}
