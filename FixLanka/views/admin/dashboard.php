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

$basePath = '';
$currentPath = 'dashboard';
$message = '';

$pageTitle = $title ?? 'Admin Dashboard';
$pageDescription = $description ?? 'Full Access Of the website!';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/dashboard.css?v=<?php echo time(); ?>">
</head>

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
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
                                <!-- PENDING WITHDRAWALS - FIXED BUTTON -->
                                <div class="pt-4">
                                    <div class="flex items-center justify-between w-full">
                                        <span class="text-sm font-medium text-foreground">Pending Withdrawals</span>
                                        <button class="badge badge-outline withdrawal-badge" onclick="showWithdrawalPopup(<?php echo $mockFinancials['pendingWithdrawals']; ?>)">
                                            LKR <?php echo number_format($mockFinancials['pendingWithdrawals'] / 1000); ?>K
                                        </button>
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

                    <!-- QUICK ACTIONS - FIXED NAVIGATION -->
                    <div class="dashboard-quick-actions">
                        <div class="card">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-foreground flex items-center gap-2">
                                    <i data-lucide="zap" class="h-5 w-5 text-fixlanka-primary"></i>
                                    Quick Actions
                                </h3>
                                <div class="mt-4 action-buttons-grid">
                                    <!-- Row 1 -->
                                    <button onclick="navigateOrShowComingSoon('moderator-management')" class="action-btn">
                                        <i data-lucide="users-cog"></i>
                                        <span>Moderator Management</span>
                                    </button>
                                    <button onclick="navigateOrShowComingSoon('account-moderation')" class="action-btn">
                                        <i data-lucide="shield-check"></i>
                                        <span>Account Moderation</span>
                                    </button>
                                    <!-- Row 2 -->
                                    <button onclick="navigateOrShowComingSoon('send-alerts')" class="action-btn">
                                        <i data-lucide="bell"></i>
                                        <span>Send Alerts</span>
                                    </button>
                                    <button onclick="navigateOrShowComingSoon('issues-reports')" class="action-btn">
                                        <i data-lucide="flag"></i>
                                        <span>Issues & Reports</span>
                                    </button>
                                    <!-- Row 3 -->
                                    <button onclick="navigateOrShowComingSoon('analytics')" class="action-btn">
                                        <i data-lucide="bar-chart-3"></i>
                                        <span>Analytics</span>
                                    </button>
                                    <button onclick="navigateOrShowComingSoon('advertisement-review')" class="action-btn">
                                        <i data-lucide="monitor"></i>
                                        <span>Advertisement Review</span>
                                    </button>
                                    <!-- Row 4 -->
                                    <button onclick="navigateOrShowComingSoon('financial-overview')" class="action-btn" style="grid-column: span 2;">
                                        <i data-lucide="dollar-sign"></i>
                                        <span>Financial Overview</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- SYSTEM HEALTH - WORKING POPUPS -->
                        <div class="card">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-foreground">System Health</h3>
                                <div class="dashboard-health-indicators mt-4">
                                    <div class="dashboard-health-item">
                                        <span class="text-sm text-foreground">Server Status</span>
                                        <button class="health-badge health-online" onclick="showHealthPopup('Server', 'Online', 'Server is running smoothly with 99.9% uptime')">
                                            Online
                                        </button>
                                    </div>
                                    <div class="dashboard-health-item">
                                        <span class="text-sm text-foreground">Database</span>
                                        <button class="health-badge health-healthy" onclick="showHealthPopup('Database', 'Healthy', 'Database connection is stable. Response time: 12ms')">
                                            Healthy
                                        </button>
                                    </div>
                                    <div class="dashboard-health-item">
                                        <span class="text-sm text-foreground">Payment Gateway</span>
                                        <button class="health-badge health-active" onclick="showHealthPopup('Payment Gateway', 'Active', 'All payment services are operational and processing transactions')">
                                            Active
                                        </button>
                                    </div>
                                    <div class="dashboard-health-item">
                                        <span class="text-sm text-foreground">Backup Status</span>
                                        <button class="health-badge health-backup" onclick="showHealthPopup('Backup', 'Last: 2h ago', 'Last successful backup: 2 hours ago. Next scheduled: In 22 hours')">
                                            Last: 2h ago
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- SYSTEM HEALTH POPUP MODAL -->
            <div id="healthPopup" class="health-popup-overlay" onclick="closeHealthPopup()">
                <div class="health-popup-content" onclick="event.stopPropagation()">
                    <div class="health-popup-header">
                        <h4 id="popupTitle">System Status</h4>
                        <button onclick="closeHealthPopup()" class="popup-close-btn">×</button>
                    </div>
                    <div class="health-popup-body">
                        <div class="popup-status-badge" id="popupStatus">Online</div>
                        <p id="popupMessage">System is running smoothly</p>
                    </div>
                    <div class="health-popup-footer">
                        <button onclick="closeHealthPopup()" class="popup-ok-btn">OK</button>
                    </div>
                </div>
            </div>

            <!-- PENDING WITHDRAWALS POPUP MODAL -->
            <div id="withdrawalPopup" class="health-popup-overlay" onclick="closeWithdrawalPopup()">
                <div class="health-popup-content" onclick="event.stopPropagation()">
                    <div class="health-popup-header">
                        <h4>Pending Withdrawals</h4>
                        <button onclick="closeWithdrawalPopup()" class="popup-close-btn">×</button>
                    </div>
                    <div class="health-popup-body">
                        <div style="text-align: center; margin-bottom: 1.5rem;">
                            <i data-lucide="wallet" style="width: 64px; height: 64px; color: #f59e0b; margin-bottom: 1rem;"></i>
                        </div>
                        <div style="background: #fffbeb; border: 2px solid #fbbf24; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                            <div style="text-align: center;">
                                <div style="font-size: 0.875rem; color: #92400e; font-weight: 600; margin-bottom: 0.5rem;">Total Pending Amount</div>
                                <div style="font-size: 2rem; font-weight: 700; color: #78350f;" id="withdrawalAmount">LKR 235,000</div>
                            </div>
                        </div>
                        <div style="text-align: left; color: #6b7280; font-size: 0.875rem; line-height: 1.6;">
                            <p style="margin-bottom: 0.75rem;"><strong>Status:</strong> Awaiting Admin Approval</p>
                            <p style="margin-bottom: 0.75rem;"><strong>Total Requests:</strong> 12 pending withdrawals</p>
                            <p style="margin-bottom: 0;"><strong>Action Required:</strong> Review and approve withdrawal requests from repairers and companies in the Financial Overview section.</p>
                        </div>
                    </div>
                    <div class="health-popup-footer">
                        <button onclick="navigateToFinancialOverview()" class="popup-ok-btn" style="background: #f59e0b; margin-right: 0.5rem;">
                            <i data-lucide="external-link" style="width: 16px; height: 16px; display: inline; margin-right: 0.25rem;"></i>
                            Go to Financial Overview
                        </button>
                        <button onclick="closeWithdrawalPopup()" class="popup-ok-btn">Close</button>
                    </div>
                </div>
            </div>

            <!-- COMING SOON POPUP MODAL -->
            <div id="comingSoonPopup" class="health-popup-overlay" onclick="closeComingSoonPopup()">
                <div class="health-popup-content" onclick="event.stopPropagation()">
                    <div class="health-popup-header">
                        <h4 id="comingSoonTitle">Page Under Construction</h4>
                        <button onclick="closeComingSoonPopup()" class="popup-close-btn">×</button>
                    </div>
                    <div class="health-popup-body">
                        <div class="construction-icon">
                            <i data-lucide="construction" style="width: 64px; height: 64px; color: #f59e0b;"></i>
                        </div>
                        <p id="comingSoonMessage" style="margin-top: 1rem;">This page is currently under development. Please check back later!</p>
                    </div>
                    <div class="health-popup-footer">
                        <button onclick="closeComingSoonPopup()" class="popup-ok-btn">OK</button>
                    </div>
                </div>
            </div>

            <script>
                // Initialize Lucide icons
                lucide.createIcons();

                // Page routes configuration - SET TO null FOR PAGES THAT DON'T EXIST YET
                const pageRoutes = {
                    // EXISTING PAGES - Full paths
                    'account-moderation': '/2nd-Year-Group-Project/FixLanka/views/admin/account-moderation.php',
                    
                    // PAGES UNDER DEVELOPMENT - Set to null to show "Coming Soon"
                    'moderator-management': null,
                    'send-alerts': null,
                    'issues-reports': null,
                    'analytics': null,
                    'advertisement-review': null,
                    'financial-overview': null
                };

                // Page display names
                const pageNames = {
                    'moderator-management': 'Moderator Management',
                    'account-moderation': 'Account Moderation',
                    'send-alerts': 'Send Alerts',
                    'issues-reports': 'Issues & Reports',
                    'analytics': 'Analytics Dashboard',
                    'advertisement-review': 'Advertisement Review',
                    'financial-overview': 'Financial Overview'
                };

                // Navigate to page or show coming soon
                function navigateOrShowComingSoon(pageName) {
                    const route = pageRoutes[pageName];
                    
                    if (route) {
                        // Page exists - navigate to it
                        window.location.href = route;
                    } else {
                        // Page doesn't exist - show coming soon
                        showComingSoonPopup(pageNames[pageName] || 'This Page');
                    }
                }

                // Show coming soon popup
                function showComingSoonPopup(pageName) {
                    document.getElementById('comingSoonTitle').textContent = pageName + ' - Coming Soon';
                    document.getElementById('comingSoonMessage').textContent = 
                        `The ${pageName} page is currently under development. Our team is working hard to bring you this feature. Please check back soon!`;
                    document.getElementById('comingSoonPopup').classList.add('active');
                    document.body.style.overflow = 'hidden';
                    
                    lucide.createIcons();
                }

                // Close coming soon popup
                function closeComingSoonPopup() {
                    document.getElementById('comingSoonPopup').classList.remove('active');
                    document.body.style.overflow = 'auto';
                }

                // System Health Popup Functions
                function showHealthPopup(title, status, message) {
                    document.getElementById('popupTitle').textContent = title;
                    document.getElementById('popupStatus').textContent = status;
                    document.getElementById('popupMessage').textContent = message;
                    document.getElementById('healthPopup').classList.add('active');
                    document.body.style.overflow = 'hidden';
                }

                function closeHealthPopup() {
                    document.getElementById('healthPopup').classList.remove('active');
                    document.body.style.overflow = 'auto';
                }

                // Pending Withdrawals Popup Functions
                function showWithdrawalPopup(amount) {
                    document.getElementById('withdrawalAmount').textContent = 'LKR ' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    document.getElementById('withdrawalPopup').classList.add('active');
                    document.body.style.overflow = 'hidden';
                    
                    lucide.createIcons();
                }

                function closeWithdrawalPopup() {
                    document.getElementById('withdrawalPopup').classList.remove('active');
                    document.body.style.overflow = 'auto';
                }

                // Navigate to Financial Overview - FIXED
                function navigateToFinancialOverview() {
                    const route = pageRoutes['financial-overview'];
                    
                    if (route) {
                        // Page exists - navigate
                        window.location.href = route;
                    } else {
                        // Page doesn't exist - close withdrawal popup and show coming soon
                        closeWithdrawalPopup();
                        setTimeout(() => {
                            showComingSoonPopup('Financial Overview');
                        }, 300);
                    }
                }

                // Close popups on Escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeHealthPopup();
                        closeComingSoonPopup();
                        closeWithdrawalPopup();
                    }
                });

                // Re-initialize Lucide icons after page load
                window.addEventListener('load', function() {
                    lucide.createIcons();
                });
            </script>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>

</html>