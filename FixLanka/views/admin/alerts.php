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
require_once '../../includes/admin-modarator/auth.php';
require_once '../../includes/admin-modarator/mock-data.php';

// // Check if user is logged in and get user info
// $isLoggedIn = isLoggedIn();
// $user = $isLoggedIn ? getCurrentUser() : null;
// requireRole("admin", $basePath);
// $user = getCurrentUser();

$basePath = '';
$currentPath = 'alerts';
$message = '';

$recentAlerts = [
    [
        'id' => 1,
        'message' => 'System maintenance scheduled for August 5th, 10 PM – 12 AM',
        'recipients' => 'All Users',
        'priority' => 'high',
        'sentAt' => '2024-07-25 14:30',
        'status' => 'Sent'
    ],
    [
        'id' => 2,
        'message' => 'New feature: Real-time chat with service providers now available',
        'recipients' => 'Customers',
        'priority' => 'medium',
        'sentAt' => '2024-07-24 09:15',
        'status' => 'Sent'
    ],
    [
        'id' => 3,
        'message' => 'Commission rate update effective from next month',
        'recipients' => 'Service Providers',
        'priority' => 'high',
        'sentAt' => '2024-07-23 16:45',
        'status' => 'Sent'
    ]
];

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
            <link rel="stylesheet" href="../../assets/css/admin/alerts.css">


            <?php renderPageHeader($basePath, 'Send Alerts', 'Broadcast important messages to users across the platform'); ?>

            <main style="margin-top: 5rem;" class="dashboard-content">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-foreground">Send Alerts</h2>
                        <p class="text-muted-foreground">Broadcast important messages to users across the platform</p>
                    </div>

                    <?php if ($message): ?>
                        <div class="bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <div class="alerts-compose-grid">
                        <div class="alerts-compose-card">
                            <div class="alerts-compose-header">
                                <h3 class="text-lg font-medium w-full text-card-foreground flex items-center gap-2">
                                    <i data-lucide="send" class="h-5 w-5"></i>
                                    Compose Alert
                                </h3>
                                <p class="text-sm text-muted-foreground w-full">Send notifications to specific user groups</p>

                                <form method="POST" class="alerts-compose-form w-full">
                                    <input type="hidden" name="action" value="send_alert">

                                    <div class="alerts-form-group">
                                        <label class="alerts-form-label">Recipients</label>
                                        <select name="recipients">
                                            <option value="all">All Users (150)</option>
                                            <option value="providers">Service Providers (45)</option>
                                            <option value="companies">Companies (25)</option>
                                            <option value="customers">Customers (80)</option>
                                        </select>
                                    </div>

                                    <div class="alerts-form-group">
                                        <label class="alerts-form-label">Priority</label>
                                        <select name="priority">
                                            <option value="low">Low Priority</option>
                                            <option value="medium" selected>Medium Priority</option>
                                            <option value="high">High Priority</option>
                                        </select>
                                    </div>

                                    <div class="alerts-form-group">
                                        <label class="alerts-form-label">Message</label>
                                        <textarea
                                            name="message"
                                            rows="5"
                                            placeholder="Enter your alert message here..."
                                            required></textarea>
                                    </div>

                                    <button type="submit" class="alerts-send-btn">
                                        <i data-lucide="send" class="mr-2 h-4 w-4"></i>
                                        Send Alert
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="alerts-compose-card">
                            <div class="alerts-compose-header">
                                <h3 class="text-lg font-medium text-card-foreground flex items-center gap-2">
                                    <i data-lucide="alert-triangle" class="h-5 w-5"></i>
                                    Recent Alerts
                                </h3>
                                <p class="text-sm text-muted-foreground">Previously sent notifications and their status</p>

                                <div class="alerts-recent-list">
                                    <?php
                                    $priorityVariants = [
                                        'low' => 'secondary',
                                        'medium' => 'outline',
                                        'high' => 'destructive'
                                    ];

                                    foreach ($recentAlerts as $alert): ?>
                                        <div class="alerts-recent-item">
                                            <div class="alerts-recent-header">
                                                <p class="alerts-recent-message"><?php echo htmlspecialchars($alert['message']); ?></p>
                                                <?php renderBadge(ucfirst($alert['priority']), $priorityVariants[$alert['priority']]); ?>
                                            </div>
                                            <div class="alerts-recent-meta">
                                                <span>To: <?php echo $alert['recipients']; ?></span>
                                                <span><?php echo $alert['sentAt']; ?></span>
                                            </div>
                                            <div class="alerts-recent-footer">
                                                <?php renderBadge($alert['status'], 'outline'); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-card-foreground">Alert Templates</h3>
                            <p class="text-sm text-muted-foreground">Quick templates for common alert messages</p>

                            <div class="alerts-templates-grid">
                                <?php
                                $templates = [
                                    [
                                        'title' => 'Maintenance Notice',
                                        'message' => 'System maintenance scheduled for [DATE] from [TIME] to [TIME]. Services may be temporarily unavailable.',
                                        'priority' => 'high'
                                    ],
                                    [
                                        'title' => 'New Feature Announcement',
                                        'message' => 'We\'re excited to announce a new feature: [FEATURE_NAME]. Check it out in your dashboard!',
                                        'priority' => 'medium'
                                    ],
                                    [
                                        'title' => 'Policy Update',
                                        'message' => 'Important updates to our terms of service. Please review the changes in your account settings.',
                                        'priority' => 'medium'
                                    ],
                                    [
                                        'title' => 'Security Alert',
                                        'message' => 'We\'ve detected unusual activity. Please verify your account security settings immediately.',
                                        'priority' => 'high'
                                    ],
                                    [
                                        'title' => 'Payment Reminder',
                                        'message' => 'Your subscription payment is due soon. Update your payment method to avoid service interruption.',
                                        'priority' => 'medium'
                                    ],
                                    [
                                        'title' => 'Welcome Message',
                                        'message' => 'Welcome to FixLanka! Get started by completing your profile and exploring our services.',
                                        'priority' => 'low'
                                    ]
                                ];

                                foreach ($templates as $template):
                                ?>
                                    <div class="alerts-template-card">
                                        <div class="alerts-template-header">
                                            <h4 class="alerts-template-title"><?php echo $template['title']; ?></h4>
                                            <?php renderBadge(ucfirst($template['priority']), $priorityVariants[$template['priority']]); ?>
                                        </div>
                                        <p class="alerts-template-message"><?php echo $template['message']; ?></p>
                                        <button onclick="useTemplate('<?php echo addslashes($template['message']); ?>')" class="alerts-template-btn">
                                            Use Template
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <script>
                lucide.createIcons();

                function useTemplate(message) {
                    document.querySelector('textarea[name="message"]').value = message;
                }
            </script>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="../../assets/javascript/admin-moderator/common.js"></script>
</body>

</html>
