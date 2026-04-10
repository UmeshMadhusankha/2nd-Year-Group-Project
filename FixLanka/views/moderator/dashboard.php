<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/ModeratorDashboardController.php';

try {
    // $pdo is provided by config/database.php
    $controller = new ModeratorDashboardController($pdo);
    
    $dashboardData = $controller->getDashboardData();
    $dashboardStats = $controller->getFormattedStats();
    $recentActivity = $dashboardData['recentActivity'];
    $activityOverview = $dashboardData['activityOverview'];
} catch (Exception $e) {
    die("⛔ Database Error: " . htmlspecialchars($e->getMessage()));
}

$message = '';
$basePath = '';
$currentPath = '/2nd-Year-Group-Project/FixLanka/moderator-dashboard';
$pageTitle = 'Moderator Dashboard - FixLanka';
$pageDescription = 'Overview of system activity and quick access to management tools';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">
    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Moderator Dashboard', 'Overview of system activity and quick access to management tools'); ?>
            <main class="main-content">
                <?php if ($message): ?>
                    <div class="alert-message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <div class="stats-grid">
                    <div class="stat-card" data-color="blue">
                        <div class="stat-card-inner">
                            <div class="stat-info">
                                <h4>TOTAL USERS</h4>
                                <div class="stat-value"><?php echo number_format($dashboardStats['totalUsers']); ?></div>
                                <div class="stat-change">+<?php echo $dashboardStats['newUsersToday']; ?> today</div>
                            </div>
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                    <div class="stat-card" data-color="green">
                        <div class="stat-card-inner">
                            <div class="stat-info">
                                <h4>ACTIVE ADS</h4>
                                <div class="stat-value"><?php echo number_format($dashboardStats['activeAds']); ?></div>
                                <div class="stat-change"><?php echo $dashboardStats['adsApprovedToday']; ?> approved today</div>
                            </div>
                            <div class="stat-icon"><i class="fas fa-bullhorn"></i></div>
                        </div>
                    </div>
                    <div class="stat-card" data-color="yellow">
                        <div class="stat-card-inner">
                            <div class="stat-info">
                                <h4>PENDING REVIEWS</h4>
                                <div class="stat-value"><?php echo number_format($dashboardStats['pendingReviews']); ?></div>
                                <div class="stat-change"><?php echo $dashboardStats['reportsToday']; ?> reports today</div>
                            </div>
                            <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        </div>
                    </div>
                    <div class="stat-card" data-color="purple">
                        <div class="stat-card-inner">
                            <div class="stat-info">
                                <h4>REVENUE</h4>
                                <div class="stat-value">LKR <?php echo number_format($dashboardStats['totalRevenue'], 2); ?></div>
                                <div class="stat-change">This month</div>
                            </div>
                            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <h3 class="section-title"><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="quick-actions-grid">
                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/ads.php" class="action-card action-blue">
                            <div class="action-icon"><i class="fas fa-clipboard-check"></i></div>
                            <div class="action-content"><h4>Advertisement Review</h4><p>0 pending reviews</p></div>
                        </a>
                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/ad-reports.php" class="action-card action-green">
                            <div class="action-icon"><i class="fas fa-file-alt"></i></div>
                            <div class="action-content"><h4>Ad Reports</h4><p>0 reports today</p></div>
                        </a>
                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/ad-schedule.php" class="action-card action-red">
                            <div class="action-icon"><i class="fas fa-calendar-alt"></i></div>
                            <div class="action-content"><h4>Ad Scheduling</h4><p>Manage ad placements</p></div>
                        </a>
                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/finance.php" class="action-card action-purple">
                            <div class="action-icon"><i class="fas fa-chart-line"></i></div>
                            <div class="action-content"><h4>Financial Reports</h4><p>Revenue & analytics</p></div>
                        </a>
                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php" class="action-card action-orange">
                            <div class="action-icon"><i class="fas fa-bell"></i></div>
                            <div class="action-content"><h4>Notifications</h4><p>System alerts & messages</p></div>
                        </a>
                        <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/static-content.php" class="action-card action-teal">
                            <div class="action-icon"><i class="fas fa-edit"></i></div>
                            <div class="action-content"><h4>Static Management</h4><p>Manage site content</p></div>
                        </a>
                    </div>
                </div>

                <div class="two-column-grid">
                    <div class="section-card">
                        <h3 class="section-title"><i class="fas fa-chart-line"></i> Activity Overview</h3>
                        <div class="activity-overview-list">
                            <div class="activity-overview-item" data-color="blue">
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
                                    <div class="activity-overview-fill" style="width: <?php echo abs($activityOverview['revenue_growth']); ?>%;"></div>
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

                    <div class="section-card">
                        <h3 class="section-title"><i class="fas fa-history"></i> Recent Activity</h3>
                        <div class="recent-activity-list">
                            <?php if (!empty($recentActivity)): ?>
                                <?php foreach ($recentActivity as $activity): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon activity-icon-<?php echo htmlspecialchars($activity['activity_type']); ?>">
                                            <i class="fas fa-<?php echo htmlspecialchars($activity['icon']); ?>"></i>
                                        </div>
                                        <div class="activity-details">
                                            <div class="activity-text">
                                                <?php echo htmlspecialchars($activity['description']); ?>
                                                <span class="badge-<?php echo htmlspecialchars($activity['user_role']); ?>" style="margin-left: 8px; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; background: <?php echo $activity['user_role'] === 'admin' ? '#dc2626' : '#2563eb'; ?>; color: white;">
                                                    <?php echo strtoupper($activity['user_role']); ?>
                                                </span>
                                            </div>
                                            <div class="activity-time"><?php echo $controller->formatDateTime($activity['created_at']); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="activity-item">
                                    <div class="activity-icon activity-icon-system">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <div class="activity-details">
                                        <div class="activity-text">No recent activity available</div>
                                        <div class="activity-time"><?php echo date('d M Y, h:i A'); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>
</html>