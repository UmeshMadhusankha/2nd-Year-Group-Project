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
        'role' => 'moderator',
        'userId' => (int)($user['id'] ?? ($_SESSION['user_id'] ?? 0)),
        'userName' => $userName,
        'userEmail' => $userEmail,
        'avatarUrl' => $userAvatar,
        'pageTitle' => $pageTitle,
        'pageSlogan' => $pageSubtitle,
        'searchPlaceholder' => $searchPlaceholder,
        'searchCategories' => [
            ['value' => 'all', 'label' => 'All'],
            ['value' => 'ads', 'label' => 'Ads'],
            ['value' => 'reports', 'label' => 'Reports'],
            ['value' => 'accounts', 'label' => 'Accounts'],
            ['value' => 'notifications', 'label' => 'Notifications']
        ],
        'notificationsPageUrl' => '/2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php',
        'profileLinks' => [
            ['href' => '/2nd-Year-Group-Project/FixLanka/moderator-dashboard', 'label' => 'My Profile', 'icon' => 'fas fa-user'],
            ['href' => '/2nd-Year-Group-Project/FixLanka/moderator-dashboard', 'label' => 'Settings', 'icon' => 'fas fa-cog']
        ],
        'quickSearchLinks' => [
            ['title' => 'Moderator Dashboard', 'subtitle' => 'Activity overview', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-dashboard', 'icon' => 'fa-gauge-high'],
            ['title' => 'Advertisement Review', 'subtitle' => 'Review submissions', 'url' => '/2nd-Year-Group-Project/FixLanka/views/moderator/ads.php', 'icon' => 'fa-rectangle-ad'],
            ['title' => 'Ad Scheduling', 'subtitle' => 'Schedule ad runs', 'url' => '/2nd-Year-Group-Project/FixLanka/views/moderator/ad-schedule.php', 'icon' => 'fa-calendar-days'],
            ['title' => 'Account Moderation', 'subtitle' => 'Moderate accounts', 'url' => '/2nd-Year-Group-Project/FixLanka/views/moderator/account-moderation.php', 'icon' => 'fa-user-shield'],
            ['title' => 'Financial Reports', 'subtitle' => 'Revenue reports', 'url' => '/2nd-Year-Group-Project/FixLanka/views/moderator/finance.php', 'icon' => 'fa-chart-column'],
            ['title' => 'Notifications', 'subtitle' => 'Manage notifications', 'url' => '/2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php', 'icon' => 'fa-bell']
        ]
    ]);
}
