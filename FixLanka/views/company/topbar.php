<?php
/**
 * Company topbar wrapper -> shared topbar
 */

if (!isset($userData)) {
    require_once __DIR__ . '/../../config/session.php';
    $userData = getUserData();
}

require_once __DIR__ . '/../shared/topbar.php';

$companyName = $userData['company_name'] ?? $userData['name'] ?? 'Company';
$userEmail = $userData['email'] ?? 'user@example.com';
$profilePhoto = $userData['profile_photo'] ?? '/2nd-Year-Group-Project/FixLanka/assets/images/user.png';

if (isset($currentPage) && !empty($currentPage)) {
    // keep provided value
} elseif (isset($_GET['page']) && !empty($_GET['page'])) {
    $currentPage = $_GET['page'];
} else {
    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
}

if ($currentPage === 'index' || $currentPage === '' || $currentPage === 'topbar') {
    $currentPage = 'dashboard';
}

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

$currentPageInfo = $pageInfo[$currentPage] ?? $pageInfo['dashboard'];

renderSharedTopbar([
    'role' => 'company',
    'userId' => (int)($userData['id'] ?? 0),
    'userName' => $companyName,
    'userEmail' => $userEmail,
    'avatarUrl' => $profilePhoto,
    'pageTitle' => $currentPageInfo['title'],
    'pageSlogan' => $currentPageInfo['slogan'],
    'searchPlaceholder' => 'Search...',
    'searchEndpoint' => '/2nd-Year-Group-Project/FixLanka/api/global-search.php',
    'searchCategories' => [
        ['value' => 'all', 'label' => 'All'],
        ['value' => 'projects', 'label' => 'Projects'],
        ['value' => 'requests', 'label' => 'Requests'],
        ['value' => 'workforce', 'label' => 'Workforce'],
        ['value' => 'payments', 'label' => 'Payments']
    ],
    'notificationsPageUrl' => '/2nd-Year-Group-Project/FixLanka/views/company/notifications.php',
    'profileLinks' => [
        ['href' => '/2nd-Year-Group-Project/FixLanka/views/company/profile.php', 'label' => 'My Profile', 'icon' => 'fas fa-user'],
        ['href' => '/2nd-Year-Group-Project/FixLanka/views/company/settings.php', 'label' => 'Settings', 'icon' => 'fas fa-cog'],
        ['href' => '/2nd-Year-Group-Project/FixLanka/views/company/support.php', 'label' => 'Help & Support', 'icon' => 'fas fa-question-circle']
    ],
    'quickSearchLinks' => [
        ['title' => 'Dashboard', 'subtitle' => 'Overview', 'url' => '/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php', 'icon' => 'fa-home'],
        ['title' => 'Projects', 'subtitle' => 'Manage projects', 'url' => '/2nd-Year-Group-Project/FixLanka/views/company/projects.php', 'icon' => 'fa-project-diagram'],
        ['title' => 'Workforce', 'subtitle' => 'Team and freelancers', 'url' => '/2nd-Year-Group-Project/FixLanka/views/company/workforce.php', 'icon' => 'fa-users'],
        ['title' => 'Repair Requests', 'subtitle' => 'Customer requests', 'url' => '/2nd-Year-Group-Project/FixLanka/views/company/repair-requests.php', 'icon' => 'fa-tools'],
        ['title' => 'Contracts', 'subtitle' => 'Agreements', 'url' => '/2nd-Year-Group-Project/FixLanka/views/company/contracts.php', 'icon' => 'fa-file-contract'],
        ['title' => 'Payments', 'subtitle' => 'Finance and reports', 'url' => '/2nd-Year-Group-Project/FixLanka/views/company/payments.php', 'icon' => 'fa-file-invoice-dollar']
    ]
]);
