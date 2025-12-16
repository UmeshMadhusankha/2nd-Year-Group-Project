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
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/ModeratorDashboardController.php';

// Initialize Controller
global $pdo;
$controller = new ModeratorDashboardController($pdo);

// Get dashboard data
$dashboardData = $controller->getDashboardData();
$dashboardStats = $controller->getFormattedStats();
$recentActivity = $dashboardData['recentActivity'];
$activityOverview = $dashboardData['activityOverview'];
$systemStatus = $dashboardData['systemStatus'];

$message = '';
$basePath = '';
$currentPath = '/2nd-Year-Group-Project/FixLanka/moderator-dashboard';

// Get page title and description from variables or use defaults
$pageTitle = 'Moderator Dashboard - FixLanka';
$pageDescription = 'Overview of system activity and quick access to management tools';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/dashboard.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-foreground text-background">
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Moderator Dashboard', 'Overview of system activity and quick access to management tools'); ?>

            <main class="main-content">
                <?php if ($message): ?>
                    <div class="alert-message">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card" data-color="blue">
                        <div class="stat-card-inner">
                            <div class="stat-info">
                                <h4>Total Users</h4>
                                <div class="stat-value"><?php echo number_format($dashboardStats['totalUsers']); ?></div>
                                <div class="stat-change">+<?php echo $dashboardStats['newUsersToday']; ?> today</div>
                            </div>
                            <div class="stat-icon">
                                <i data-lucide="users"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card" data-color="green">
                        <div class="stat-card-inner">
                            <div class="stat-info">
                                <h4>Active Ads</h4>
                                <div class="stat-value"><?php echo $dashboardStats['activeAds']; ?></div>
                                <div class="stat-change"><?php echo $dashboardStats['adsApprovedToday']; ?> approved today</div>
                            </div>
                            <div class="stat-icon">
                                <i data-lucide="megaphone"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card" data-color="yellow">
                        <div class="stat-card-inner">
                            <div class="stat-info">
                                <h4>Pending Reviews</h4>
                                <div class="stat-value"><?php echo $dashboardStats['pendingReviews']; ?></div>
                                <div class="stat-change"><?php echo $dashboardStats['reportsToday']; ?> reports today</div>
                            </div>
                            <div class="stat-icon">
                                <i data-lucide="clock"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card" data-color="purple">
                        <div class="stat-card-inner">
                            <div class="stat-info">
                                <h4>Revenue</h4>
                                <div class="stat-value">LKR <?php echo number_format($dashboardStats['totalRevenue'], 2); ?></div>
                                <div class="stat-change">This month</div>
                            </div>
                            <div class="stat-icon">
                                <i data-lucide="dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="section-card">
                    <h3 class="section-title">Quick Actions</h3>
                    <div class="quick-actions-grid">
                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/ads.php" class="action-card action-blue">
                            <div class="action-icon">
                                <i data-lucide="megaphone"></i>
                            </div>
                            <div class="action-content">
                                <h4>Advertisement Review</h4>
                                <p><?php echo $dashboardStats['pendingReviews']; ?> pending reviews</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/ad-reports.php" class="action-card action-green">
                            <div class="action-icon">
                                <i data-lucide="flag"></i>
                            </div>
                            <div class="action-content">
                                <h4>Ad Reports</h4>
                                <p><?php echo number_format($dashboardStats['reportsToday']); ?> reports today</p>
                            </div>
                        </a>

                        <!-- NEW: Ad Scheduling Button -->
                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/ad-schedules.php" class="action-card action-red">
                            <div class="action-icon">
                                <i data-lucide="calendar-clock"></i>
                            </div>
                            <div class="action-content">
                                <h4>Ad Scheduling</h4>
                                <p>Manage ad placements</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/finance.php" class="action-card action-purple">
                            <div class="action-icon">
                                <i data-lucide="dollar-sign"></i>
                            </div>
                            <div class="action-content">
                                <h4>Financial Reports</h4>
                                <p>Revenue & analytics</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php" class="action-card action-orange">
                            <div class="action-icon">
                                <i data-lucide="bell"></i>
                            </div>
                            <div class="action-content">
                                <h4>Notifications</h4>
                                <p>System alerts & messages</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/static-content.php" class="action-card action-teal">
                            <div class="action-icon">
                                <i data-lucide="file-text"></i>
                            </div>
                            <div class="action-content">
                                <h4>Static Management</h4>
                                <p>Manage site content</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Two Column Layout -->
                <div class="two-column-grid">
                    <!-- Activity Overview -->
                    <div class="section-card">
                        <h3 class="section-title">Activity Overview</h3>
                        <div class="activity-list">
                            <div class="activity-row">
                                <span class="activity-label">User Registrations</span>
                                <div class="activity-progress">
                                    <div class="progress-bar">
                                        <div class="progress-fill bg-blue" style="width: <?php echo round($activityOverview['user_registrations']); ?>%"></div>
                                    </div>
                                    <span class="activity-percent"><?php echo round($activityOverview['user_registrations']); ?>%</span>
                                </div>
                            </div>

                            <div class="activity-row">
                                <span class="activity-label">Ad Approvals</span>
                                <div class="activity-progress">
                                    <div class="progress-bar">
                                        <div class="progress-fill bg-green" style="width: <?php echo $activityOverview['ad_approvals']; ?>%"></div>
                                    </div>
                                    <span class="activity-percent"><?php echo $activityOverview['ad_approvals']; ?>%</span>
                                </div>
                            </div>

                            <div class="activity-row">
                                <span class="activity-label">Revenue Growth</span>
                                <div class="activity-progress">
                                    <div class="progress-bar">
                                        <div class="progress-fill bg-purple" style="width: <?php echo $activityOverview['revenue_growth']; ?>%"></div>
                                    </div>
                                    <span class="activity-percent"><?php echo $activityOverview['revenue_growth']; ?>%</span>
                                </div>
                            </div>

                            <div class="activity-row">
                                <span class="activity-label">System Performance</span>
                                <div class="activity-progress">
                                    <div class="progress-bar">
                                        <div class="progress-fill bg-teal" style="width: <?php echo $activityOverview['system_performance']; ?>%"></div>
                                    </div>
                                    <span class="activity-percent"><?php echo $activityOverview['system_performance']; ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="section-card">
                        <h3 class="section-title">Recent Activity</h3>
                        <div class="recent-activity-list">
                            <?php foreach ($recentActivity as $activity): ?>
                                <div class="recent-activity-item">
                                    <?php
                                    // Map activity type to icon color
                                    $iconClass = 'activity-icon-gray';
                                    switch ($activity['type']) {
                                        case 'ad_approved':
                                            $iconClass = 'activity-icon-green';
                                            $iconName = 'check-circle';
                                            break;
                                        case 'ad_rejected':
                                        case 'user_banned':
                                            $iconClass = 'activity-icon-red';
                                            $iconName = 'x-circle';
                                            break;
                                        case 'payment_received':
                                            $iconClass = 'activity-icon-purple';
                                            $iconName = 'dollar-sign';
                                            break;
                                        case 'user_registered':
                                            $iconClass = 'activity-icon-blue';
                                            $iconName = 'user-plus';
                                            break;
                                        case 'report_submitted':
                                            $iconClass = 'activity-icon-yellow';
                                            $iconName = 'flag';
                                            break;
                                        default:
                                            $iconName = 'activity';
                                    }
                                    ?>
                                    <div class="activity-icon-wrapper <?php echo $iconClass; ?>">
                                        <i data-lucide="<?php echo $iconName; ?>"></i>
                                    </div>
                                    <div class="activity-details">
                                        <p class="activity-message"><?php echo htmlspecialchars($activity['message']); ?></p>
                                        <p class="activity-time"><?php echo htmlspecialchars($activity['time']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="section-card">
                    <h3 class="section-title">System Status</h3>
                    <div class="system-status-grid">
                        <div class="status-item status-<?php echo $systemStatus['server']['color']; ?>">
                            <i data-lucide="server"></i>
                            <div class="status-content">
                                <p class="status-label">Server Status</p>
                                <p class="status-value"><?php echo $systemStatus['server']['status']; ?> - <?php echo $systemStatus['server']['uptime']; ?> uptime</p>
                            </div>
                        </div>

                        <div class="status-item status-<?php echo $systemStatus['database']['color']; ?>">
                            <i data-lucide="database"></i>
                            <div class="status-content">
                                <p class="status-label">Database</p>
                                <p class="status-value"><?php echo $systemStatus['database']['status']; ?> - <?php echo $systemStatus['database']['response_time']; ?> response</p>
                            </div>
                        </div>

                        <div class="status-item status-<?php echo $systemStatus['alerts']['color']; ?>">
                            <i data-lucide="alert-triangle"></i>
                            <div class="status-content">
                                <p class="status-label">Alerts</p>
                                <p class="status-value"><?php echo $systemStatus['alerts']['message']; ?></p>
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