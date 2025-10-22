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
// requireRole("admin", $basePath);
// $user = getCurrentUser();

$basePath = '';
$currentPath = 'dashboard';
$message = '';

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

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/dashboard.css">

            <?php renderPageHeader($basePath, 'Admin Dashboard', 'Full Access Of the website!'); ?>

            <main style="margin-top: 5rem;" class="main-content">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-foreground">Dashboard Overview</h2>
                        <p class="text-muted-foreground">Welcome to the FixLanka admin panel. Here's what's happening today.</p>
                    </div>

                    <div class="dashboard-grid grid-cols-4">
                        <?php
                        renderCard('Total Users', number_format($mockAnalytics['totalUsers']), '+12% from last month', 'users', 'text-fixlanka-primary');
                        renderCard('Service Providers', number_format($mockAnalytics['serviceProviders']), 'Active providers', 'building-2', 'text-fixlanka-highlight');
                        renderCard('Active Ads', $mockAnalytics['activeAds'], 'Currently running', 'monitor', 'text-fixlanka-primary');
                        renderCard('Monthly Revenue', 'LKR ' . number_format($mockFinancials['monthlyRevenue'] / 1000000, 1) . 'M', '+15.2% growth', 'dollar-sign', 'text-fixlanka-highlight');
                        ?>
                    </div>

                    <div class="dashboard-activity-grid">
                        <div class="dashboard-system-overview">
                            <h3 class="text-lg font-medium text-foreground">System Overview</h3>
                            <p class="text-sm text-muted-foreground">Key metrics and performance indicators</p>

                            <div class="mt-6 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium text-foreground">User Engagement</p>
                                        <div class="flex items-center space-x-2">
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: <?php echo $mockAnalytics['userEngagement']; ?>%"></div>
                                            </div>
                                            <span class="text-sm text-muted-foreground"><?php echo $mockAnalytics['userEngagement']; ?>%</span>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium text-foreground">Revenue Growth</p>
                                        <div class="flex items-center space-x-2">
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: <?php echo $mockAnalytics['revenueGrowth'] * 5; ?>%"></div>
                                            </div>
                                            <span class="text-sm text-muted-foreground">+<?php echo $mockAnalytics['revenueGrowth']; ?>%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-4">
                                    <div class="flex items-center justify-between w-full">
                                        <span class="text-sm font-medium text-foreground">Pending Withdrawals</span>
                                        <?php renderBadge('LKR ' . number_format($mockFinancials['pendingWithdrawals'] / 1000) . 'K', 'outline'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-recent-activity">
                            <h3 class="text-lg font-medium text-foreground">Recent Activity</h3>
                            <p class="text-sm text-muted-foreground">Latest system activities and updates</p>

                            <div class="mt-6 space-y-4">
                                <?php
                                $recentActivity = [
                                    ['action' => 'New service provider registered', 'time' => '2 minutes ago', 'type' => 'user'],
                                    ['action' => 'Advertisement approved', 'time' => '15 minutes ago', 'type' => 'ad'],
                                    ['action' => 'Issue reported and resolved', 'time' => '1 hour ago', 'type' => 'issue'],
                                    ['action' => 'Financial report generated', 'time' => '2 hours ago', 'type' => 'finance']
                                ];

                                foreach ($recentActivity as $activity) {
                                    $iconMap = [
                                        'user' => 'users',
                                        'ad' => 'monitor',
                                        'issue' => 'alert-triangle',
                                        'finance' => 'dollar-sign'
                                    ];
                                    $colorMap = [
                                        'user' => 'text-fixlanka-primary',
                                        'ad' => 'text-fixlanka-primary',
                                        'issue' => 'text-fixlanka-error',
                                        'finance' => 'text-fixlanka-highlight'
                                    ];

                                    echo '<div class="dashboard-activity-item">';
                                    echo '<div class="flex-shrink-0">';
                                    echo '<i data-lucide="' . $iconMap[$activity['type']] . '" class="h-4 w-4 ' . $colorMap[$activity['type']] . '"></i>';
                                    echo '</div>';
                                    echo '<div class="flex-1 min-w-0">';
                                    echo '<p class="text-sm font-medium text-foreground">' . $activity['action'] . '</p>';
                                    echo '<p class="text-xs text-muted-foreground">' . $activity['time'] . '</p>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-quick-actions">
                        <div class="card">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-foreground flex items-center gap-2">
                                    <i data-lucide="trending-up" class="h-5 w-5 text-fixlanka-primary"></i>
                                    Quick Actions
                                </h3>
                                <div class="mt-4 grid grid-cols-2 gap-2">
                                    <?php
                                    renderBadge('Send Alert', 'secondary');
                                    renderBadge('Review Ads', 'secondary');
                                    renderBadge('User Management', 'secondary');
                                    renderBadge('Generate Report', 'secondary');
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-foreground">System Health</h3>
                                <div class="dashboard-health-indicators mt-4">
                                    <div class="dashboard-health-item">
                                        <span class="text-sm text-foreground">Server Status</span>
                                        <?php renderBadge('Online', 'default'); ?>
                                    </div>
                                    <div class="dashboard-health-item">
                                        <span class="text-sm text-foreground">Database</span>
                                        <?php renderBadge('Healthy', 'default'); ?>
                                    </div>
                                    <div class="dashboard-health-item">
                                        <span class="text-sm text-foreground">Payment Gateway</span>
                                        <?php renderBadge('Active', 'default'); ?>
                                    </div>
                                    <div class="dashboard-health-item">
                                        <span class="text-sm text-foreground">Backup Status</span>
                                        <?php renderBadge('Last: 2h ago', 'outline'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <script>
                lucide.createIcons();
            </script>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>

</html>

