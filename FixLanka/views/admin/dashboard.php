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
$currentPath = '/2nd-Year-Group-Project/FixLanka/admin-dashboard';

// Get page title and description from variables or use defaults
$pageTitle = 'Admin Dashboard - FixLanka';
$pageDescription = 'Overview of system activity and quick access to management tools';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/dashboard.css?v=<?php echo time(); ?>">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
</head>

<body class="bg-foreground text-background">
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Admin Dashboard', 'Overview of system activity and quick access to management tools'); ?>

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
                    <h3 class="section-title">
                        <i data-lucide="zap"></i>
                        Quick Actions
                    </h3>
                    <div class="quick-actions-grid">
                        <a href="/2nd-Year-Group-Project/FixLanka/views/admin/moderators.php" class="action-card action-blue">
                            <div class="action-icon">
                                <i data-lucide="user-cog"></i>
                            </div>
                            <div class="action-content">
                                <h4>Moderator Management</h4>
                                <p>Manage moderators</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/admin/account-moderation.php" class="action-card action-green">
                            <div class="action-icon">
                                <i data-lucide="shield-check"></i>
                            </div>
                            <div class="action-content">
                                <h4>Account Moderation</h4>
                                <p>Moderate user accounts</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/admin/alerts.php" class="action-card action-red">
                            <div class="action-icon">
                                <i data-lucide="bell-ring"></i>
                            </div>
                            <div class="action-content">
                                <h4>Send Alerts</h4>
                                <p>Send system notifications</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/admin/issues.php" class="action-card action-orange">
                            <div class="action-icon">
                                <i data-lucide="alert-circle"></i>
                            </div>
                            <div class="action-content">
                                <h4>Issues & Reports</h4>
                                <p>View reported issues</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/admin/analytics.php" class="action-card action-purple">
                            <div class="action-icon">
                                <i data-lucide="bar-chart-3"></i>
                            </div>
                            <div class="action-content">
                                <h4>Analytics</h4>
                                <p>View platform analytics</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/admin/ads.php" class="action-card action-teal">
                            <div class="action-icon">
                                <i data-lucide="megaphone"></i>
                            </div>
                            <div class="action-content">
                                <h4>Advertisement Review</h4>
                                <p>Review pending ads</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/admin/finance.php" class="action-card action-indigo">
                            <div class="action-icon">
                                <i data-lucide="dollar-sign"></i>
                            </div>
                            <div class="action-content">
                                <h4>Financial Overview</h4>
                                <p>Revenue & transactions</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Two Column Layout -->
                <div class="two-column-grid">
                    <!-- Activity Overview -->
                    <div class="section-card">
                        <h3 class="section-title">
                            <i data-lucide="activity"></i>
                            Activity Overview
                        </h3>
                        <div class="activity-overview-list">
                            <div class="activity-overview-item" data-color="blue">
                                <div class="activity-overview-header">
                                    <span class="activity-overview-label">User Registrations</span>
                                    <span class="activity-overview-value"><?php echo $activityOverview['user_registrations']; ?>%</span>
                                </div>
                                <div class="activity-overview-bar">
                                    <div class="activity-overview-fill" style="width: <?php echo $activityOverview['user_registrations']; ?>%;"></div>
                                </div>
                            </div>

                            <div class="activity-overview-item" data-color="green">
                                <div class="activity-overview-header">
                                    <span class="activity-overview-label">Ad Approvals</span>
                                    <span class="activity-overview-value"><?php echo $activityOverview['ad_approvals']; ?>%</span>
                                </div>
                                <div class="activity-overview-bar">
                                    <div class="activity-overview-fill" style="width: <?php echo $activityOverview['ad_approvals']; ?>%;"></div>
                                </div>
                            </div>

                            <div class="activity-overview-item" data-color="orange">
                                <div class="activity-overview-header">
                                    <span class="activity-overview-label">Revenue Growth</span>
                                    <span class="activity-overview-value"><?php echo $activityOverview['revenue_growth']; ?>%</span>
                                </div>
                                <div class="activity-overview-bar">
                                    <div class="activity-overview-fill" style="width: <?php echo $activityOverview['revenue_growth']; ?>%;"></div>
                                </div>
                            </div>

                            <div class="activity-overview-item" data-color="purple">
                                <div class="activity-overview-header">
                                    <span class="activity-overview-label">System Performance</span>
                                    <span class="activity-overview-value"><?php echo $activityOverview['system_performance']; ?>%</span>
                                </div>
                                <div class="activity-overview-bar">
                                    <div class="activity-overview-fill" style="width: <?php echo $activityOverview['system_performance']; ?>%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="section-card">
                        <h3 class="section-title">
                            <i data-lucide="clock"></i>
                            Recent Activity
                        </h3>
                        <div class="recent-activity-list">
                            <?php foreach ($recentActivity as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon activity-icon-<?php echo $activity['type']; ?>">
                                        <i data-lucide="<?php echo $activity['icon']; ?>"></i>
                                    </div>
                                    <div class="activity-details">
                                        <div class="activity-text"><?php echo htmlspecialchars($activity['message']); ?></div>
                                        <div class="activity-time"><?php echo $activity['time']; ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="section-card">
                    <h3 class="section-title">
                        <i data-lucide="shield"></i>
                        System Status
                    </h3>
                    <div class="system-status-grid">
                        <div class="status-item status-<?php echo $systemStatus['server']['color']; ?>">
                            <div class="status-icon">
                                <i data-lucide="server"></i>
                            </div>
                            <div class="status-content">
                                <span class="status-label">Server Status</span>
                                <span class="status-value"><?php echo $systemStatus['server']['status']; ?></span>
                                <span class="status-detail"><?php echo $systemStatus['server']['uptime']; ?> uptime</span>
                            </div>
                        </div>

                        <div class="status-item status-<?php echo $systemStatus['database']['color']; ?>">
                            <div class="status-icon">
                                <i data-lucide="database"></i>
                            </div>
                            <div class="status-content">
                                <span class="status-label">Database</span>
                                <span class="status-value"><?php echo $systemStatus['database']['status']; ?></span>
                                <span class="status-detail"><?php echo $systemStatus['database']['response_time']; ?> response</span>
                            </div>
                        </div>

                        <div class="status-item status-<?php echo $systemStatus['alerts']['color']; ?>">
                            <div class="status-icon">
                                <i data-lucide="alert-triangle"></i>
                            </div>
                            <div class="status-content">
                                <span class="status-label">System Alerts</span>
                                <span class="status-value"><?php echo $systemStatus['alerts']['count']; ?> notifications</span>
                                <span class="status-detail">Pending review</span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>

</html>