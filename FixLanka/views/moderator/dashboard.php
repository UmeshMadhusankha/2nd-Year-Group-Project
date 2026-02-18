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
                                <i class="fa-solid fa-users"></i>
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
                                <i class="fa-solid fa-bullhorn"></i>
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
                                <i class="fa-solid fa-clock"></i>
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
                                <i class="fa-solid fa-dollar-sign"></i>
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
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                            <div class="action-content">
                                <h4>Advertisement Review</h4>
                                <p><?php echo $dashboardStats['pendingReviews']; ?> pending reviews</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/ad-reports.php" class="action-card action-green">
                            <div class="action-icon">
                                <i class="fa-solid fa-flag"></i>
                            </div>
                            <div class="action-content">
                                <h4>Ad Reports</h4>
                                <p><?php echo $dashboardStats['reportsToday']; ?> reports today</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/ad-schedule.php" class="action-card action-red">
                            <div class="action-icon">
                                <i class="fa-solid fa-calendar"></i>
                            </div>
                            <div class="action-content">
                                <h4>Ad Scheduling</h4>
                                <p>Manage ad placements</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/finance.php" class="action-card action-purple">
                            <div class="action-icon">
                                <i class="fa-solid fa-dollar-sign"></i>
                            </div>
                            <div class="action-content">
                                <h4>Financial Reports</h4>
                                <p>Revenue & analytics</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php" class="action-card action-orange">
                            <div class="action-icon">
                                <i class="fa-solid fa-bell"></i>
                            </div>
                            <div class="action-content">
                                <h4>Notifications</h4>
                                <p>System alerts & messages</p>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/static-content.php" class="action-card action-teal">
                            <div class="action-icon">
                                <i class="fa-solid fa-file-lines"></i>
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
                        <h3 class="section-title">
                            <i class="fa-solid fa-chart-line"></i>
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
                            <i class="fa-solid fa-clock"></i>
                            Recent Activity
                        </h3>
                        <div class="recent-activity-list">
                            <?php 
                            // Icon mapping for dynamic activity icons
                            $iconMap = [
                                'user' => 'fa-user',
                                'users' => 'fa-users',
                                'megaphone' => 'fa-bullhorn',
                                'check-circle' => 'fa-circle-check',
                                'flag' => 'fa-flag',
                                'dollar-sign' => 'fa-dollar-sign',
                                'alert-triangle' => 'fa-triangle-exclamation',
                                'file' => 'fa-file',
                                'calendar' => 'fa-calendar',
                                'bell' => 'fa-bell'
                            ];
                            
                            foreach ($recentActivity as $activity): 
                                $faIcon = $iconMap[$activity['icon']] ?? 'fa-circle';
                            ?>
                                <div class="activity-item">
                                    <div class="activity-icon activity-icon-<?php echo $activity['type']; ?>">
                                        <i class="fa-solid <?php echo $faIcon; ?>"></i>
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
                        <i class="fa-solid fa-shield"></i>
                        System Status
                    </h3>
                    <div class="system-status-grid">
                        <div class="status-item status-<?php echo $systemStatus['server']['color']; ?>">
                            <div class="status-icon">
                                <i class="fa-solid fa-server"></i>
                            </div>
                            <div class="status-content">
                                <span class="status-label">Server Status</span>
                                <span class="status-value"><?php echo $systemStatus['server']['status']; ?></span>
                                <span class="status-detail"><?php echo $systemStatus['server']['uptime']; ?> uptime</span>
                            </div>
                        </div>

                        <div class="status-item status-<?php echo $systemStatus['database']['color']; ?>">
                            <div class="status-icon">
                                <i class="fa-solid fa-database"></i>
                            </div>
                            <div class="status-content">
                                <span class="status-label">Database</span>
                                <span class="status-value"><?php echo $systemStatus['database']['status']; ?></span>
                                <span class="status-detail"><?php echo $systemStatus['database']['response_time']; ?> response</span>
                            </div>
                        </div>

                        <div class="status-item status-<?php echo $systemStatus['alerts']['color']; ?>">
                            <div class="status-icon">
                                <i class="fa-solid fa-triangle-exclamation"></i>
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