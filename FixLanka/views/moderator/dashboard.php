<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include components
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../includes/admin-modarator/auth.php';
require_once __DIR__ . '/../../includes/admin-modarator/mock-data.php';

// Check if user is logged in and get user info
// $isLoggedIn = isLoggedIn();
// $user = $isLoggedIn ? getCurrentUser() : null;
// requireRole("moderator", $basePath);
// $user = getCurrentUser();


$dashboardStats = [
    'totalUsers' => 1247,
    'activeAds' => 89,
    'pendingReviews' => 23,
    'totalRevenue' => 45670,
    'newUsersToday' => 12,
    'adsApprovedToday' => 8,
    'reportsToday' => 3,
    'systemAlerts' => 2
];

$recentActivity = [
    ['type' => 'user_registered', 'message' => 'New user John Doe registered', 'time' => '2 minutes ago'],
    ['type' => 'ad_approved', 'message' => 'Advertisement "Plumbing Services" approved', 'time' => '15 minutes ago'],
    ['type' => 'report_submitted', 'message' => 'User report submitted for review', 'time' => '1 hour ago'],
    ['type' => 'payment_received', 'message' => 'Payment of $150 received', 'time' => '2 hours ago'],
    ['type' => 'user_banned', 'message' => 'User account suspended for violations', 'time' => '3 hours ago']
];

$message = '';
$basePath = '';
$currentPath = 'dashboard';

// Get page title and description from variables or use defaults
$pageTitle = $title ?? 'Advanced PHP Router';
$pageDescription = $description ?? 'A Next.js-inspired PHP routing system with advanced features';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

</head>

<body class="bg-foreground text-background">
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/dashboard.css">
            <?php renderPageHeader($basePath, 'Moderator Dashboard', 'Overview of system activity and quick access to management tools'); ?>

            <main style="margin-top: 5rem;" class="main-content">
                <div class="space-y-6">
                    <?php if ($message): ?>
                        <div class="bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <div class="dashboard-stats">
                        <?php
                        renderCard('Total Users', number_format($dashboardStats['totalUsers']), '+' . $dashboardStats['newUsersToday'] . ' today', 'users', 'text-blue-600');
                        renderCard('Active Ads', $dashboardStats['activeAds'], $dashboardStats['adsApprovedToday'] . ' approved today', 'megaphone', 'text-green-600');
                        renderCard('Pending Reviews', $dashboardStats['pendingReviews'], $dashboardStats['reportsToday'] . ' reports today', 'clock', 'text-yellow-600');
                        renderCard('Revenue', '$' . number_format($dashboardStats['totalRevenue']), 'This month', 'dollar-sign', 'text-purple-600');
                        ?>
                    </div>

                    <div class="bg-card rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-foreground mb-6">Quick Actions</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <a href="ads.php" class="nav-card bg-blue-500/10 border-blue-500/20 hover:bg-blue-500/20">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-blue-500/20 rounded-lg">
                                            <i data-lucide="megaphone" class="h-6 w-6 text-blue-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-foreground">Advertisement Review</h4>
                                            <p class="text-sm text-muted-foreground"><?php echo $dashboardStats['pendingReviews']; ?> pending reviews</p>
                                        </div>
                                    </div>
                                </a>

                                <a href="account-moderation.php" class="nav-card bg-red-500/10 border-red-500/20 hover:bg-red-500/20">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-red-500/20 rounded-lg">
                                            <i data-lucide="shield-alert" class="h-6 w-6 text-red-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-foreground">Account Moderation</h4>
                                            <p class="text-sm text-muted-foreground">Manage banned accounts</p>
                                        </div>
                                    </div>
                                </a>

                                <a href="ad-reports.php" class="nav-card bg-green-500/10 border-green-500/20 hover:bg-green-500/20">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-green-500/20 rounded-lg">
                                            <i data-lucide="flag" class="h-6 w-6 text-green-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-foreground">Ad Reports</h4>
                                            <p class="text-sm text-muted-foreground"><?php echo number_format($dashboardStats['reportsToday']); ?> reports today</p>
                                        </div>
                                    </div>
                                </a>

                                <a href="finance.php" class="nav-card bg-purple-500/10 border-purple-500/20 hover:bg-purple-500/20">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-purple-500/20 rounded-lg">
                                            <i data-lucide="dollar-sign" class="h-6 w-6 text-purple-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-foreground">Financial Reports</h4>
                                            <p class="text-sm text-muted-foreground">Revenue & analytics</p>
                                        </div>
                                    </div>
                                </a>

                                <a href="notifications.php" class="nav-card bg-orange-500/10 border-orange-500/20 hover:bg-orange-500/20">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-orange-500/20 rounded-lg">
                                            <i data-lucide="bell" class="h-6 w-6 text-orange-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-foreground">Notifications</h4>
                                            <p class="text-sm text-muted-foreground">System alerts & messages</p>
                                        </div>
                                    </div>
                                </a>

                                <a href="static-content.php" class="nav-card bg-teal-500/10 border-teal-500/20 hover:bg-teal-500/20">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 bg-teal-500/20 rounded-lg">
                                            <i data-lucide="file-text" class="h-6 w-6 text-teal-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-foreground">Content Management</h4>
                                            <p class="text-sm text-muted-foreground">Manage site content</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <div class="bg-card rounded-lg border">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-foreground mb-4">Activity Overview</h3>
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-sm text-muted-foreground">User Registrations</span>
                                            <div class="flex items-center space-x-2">
                                                <div class="w-24 bg-muted rounded-full h-2">
                                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 75%"></div>
                                                </div>
                                                <span class="text-sm font-medium">75%</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-sm text-muted-foreground">Ad Approvals</span>
                                            <div class="flex items-center space-x-2">
                                                <div class="w-24 bg-muted rounded-full h-2">
                                                    <div class="bg-green-500 h-2 rounded-full" style="width: 60%"></div>
                                                </div>
                                                <span class="text-sm font-medium">60%</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-sm text-muted-foreground">Revenue Growth</span>
                                            <div class="flex items-center space-x-2">
                                                <div class="w-24 bg-muted rounded-full h-2">
                                                    <div class="bg-purple-500 h-2 rounded-full" style="width: 85%"></div>
                                                </div>
                                                <span class="text-sm font-medium">85%</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-sm text-muted-foreground">System Performance</span>
                                            <div class="flex items-center space-x-2">
                                                <div class="w-24 bg-muted rounded-full h-2">
                                                    <div class="bg-fixlanka-primary h-2 rounded-full" style="width: 92%"></div>
                                                </div>
                                                <span class="text-sm font-medium">92%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="bg-card rounded-lg border">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-foreground mb-4">Recent Activity</h3>
                                    <div class="space-y-4">
                                        <?php foreach ($recentActivity as $activity): ?>
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <?php
                                                    $iconMap = [
                                                        'user_registered' => ['user-plus', 'text-blue-500'],
                                                        'ad_approved' => ['check-circle', 'text-green-500'],
                                                        'report_submitted' => ['alert-triangle', 'text-yellow-500'],
                                                        'payment_received' => ['dollar-sign', 'text-purple-500'],
                                                        'user_banned' => ['shield-alert', 'text-red-500']
                                                    ];
                                                    $icon = $iconMap[$activity['type']] ?? ['circle', 'text-gray-500'];
                                                    ?>
                                                    <div class="p-1.5 bg-muted/50 rounded-full">
                                                        <i data-lucide="<?php echo $icon[0]; ?>" class="h-3 w-3 <?php echo $icon[1]; ?>"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm text-foreground"><?php echo htmlspecialchars($activity['message']); ?></p>
                                                    <p class="text-xs text-muted-foreground"><?php echo $activity['time']; ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-card rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-foreground mb-4">System Status</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex items-center space-x-3 p-4 bg-green-500/10 border border-green-500/20 rounded-lg">
                                    <i data-lucide="server" class="h-5 w-5 text-green-500"></i>
                                    <div>
                                        <p class="text-sm font-medium text-foreground">Server Status</p>
                                        <p class="text-xs text-green-600">Online - 99.9% uptime</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 p-4 bg-blue-500/10 border border-blue-500/20 rounded-lg">
                                    <i data-lucide="database" class="h-5 w-5 text-blue-500"></i>
                                    <div>
                                        <p class="text-sm font-medium text-foreground">Database</p>
                                        <p class="text-xs text-blue-600">Healthy - 2.3ms response</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                                    <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-500"></i>
                                    <div>
                                        <p class="text-sm font-medium text-foreground">Alerts</p>
                                        <p class="text-xs text-yellow-600"><?php echo $dashboardStats['systemAlerts']; ?> pending alerts</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>

</body>

</html>

