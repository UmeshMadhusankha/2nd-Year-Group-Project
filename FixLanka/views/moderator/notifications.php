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

require_once __DIR__ . '/_components/notifications/NotificationCard.php';
require_once __DIR__ . '/_components/notifications/NotificationForm.php';
require_once __DIR__ . '/_components/notifications/NotificationFilters.php';
require_once __DIR__ . '/_components/notifications/NotificationStats.php';
require_once __DIR__ . '/_components/notifications/Modals.php';
require_once __DIR__ . '/_components/notifications/mock-data.php';


// Check if user is logged in and get user info
// $isLoggedIn = isLoggedIn();
// $user = $isLoggedIn ? getCurrentUser() : null;
// requireRole("moderator", $basePath);
// $user = getCurrentUser();

$notifications = [];
$stats = [];
$templates = getNotificationTemplates() ?? [];
$isLoading = true;

// Get mock notifications data
$notificationsData = getEnhancedMockNotifications();

$message = '';
$basePath = '';
$currentPath = 'notifications';

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
            <?php renderPageHeader($basePath, 'Notifications', 'Send notifications and manage communication with advanced filtering'); ?>
            <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/notifications.css">
            <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/modals.css">

            <div id="loadingOverlay" class="fixed inset-0 bg-background/80 backdrop-blur-sm z-50 flex items-center justify-center">
                <div class="flex flex-col items-center space-y-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-fixlanka-primary"></div>
                    <p class="text-foreground font-medium">Loading notifications...</p>
                </div>
            </div>

            <div id="errorAlert" class="hidden fixed top-4 right-4 z-50 max-w-md">
                <div class="bg-red-500/10 border border-red-500 text-red-500 px-4 py-3 rounded-lg shadow-lg">
                    <div class="flex items-start">
                        <i data-lucide="alert-circle" class="h-5 w-5 mr-2 flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="font-medium">Error</p>
                            <p id="errorMessage" class="text-sm mt-1"></p>
                        </div>
                        <button onclick="closeError()" class="ml-4 text-red-500 hover:text-red-600">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div id="successAlert" class="hidden fixed top-4 right-4 z-50 max-w-md">
                <div class="bg-green-500/10 border border-green-500 text-green-500 px-4 py-3 rounded-lg shadow-lg">
                    <div class="flex items-start">
                        <i data-lucide="check-circle" class="h-5 w-5 mr-2 flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="font-medium">Success</p>
                            <p id="successMessage" class="text-sm mt-1"></p>
                        </div>
                        <button onclick="closeSuccess()" class="ml-4 text-green-500 hover:text-green-600">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
            </div>

            <main style="margin-top: 5rem;" class="notifications-content">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight text-foreground">Notifications Dashboard</h2>
                            <p class="text-muted-foreground">Send notifications and manage user communication with powerful filtering and sorting</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="<?= $basePath ?>/moderator/notifications/all" class="btn btn-outline">
                                <i data-lucide="list" class="mr-2 h-4 w-4"></i>
                                View All Notifications
                            </a>
                            <button onclick="refreshNotifications()" class="btn btn-secondary" id="refreshBtn">
                                <i data-lucide="refresh-cw" class="mr-2 h-4 w-4"></i>
                                Refresh
                            </button>
                        </div>
                    </div>

                    <div id="statsContainer">
                    </div>

                    <div class="notifications-grid">
                        <div class="send-notification-card">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-foreground flex items-center gap-2">
                                    <i data-lucide="send" class="h-5 w-5"></i>
                                    Send Notification
                                </h3>
                                <p class="text-sm text-muted-foreground">Broadcast messages to users</p>

                                <form id="notificationForm" class="notification-form">
                                    <input type="hidden" name="action" value="send_notification">

                                    <div>
                                        <label class="block text-sm font-medium text-foreground">Recipients</label>
                                        <select name="recipients" class="form-select mt-1" required>
                                            <option value="all">All Users</option>
                                            <option value="providers">Service Providers</option>
                                            <option value="customers">Customers</option>
                                            <option value="companies">Companies</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-foreground">Priority</label>
                                        <select name="priority" class="form-select mt-1" required>
                                            <option value="low">Low Priority</option>
                                            <option value="medium" selected>Medium Priority</option>
                                            <option value="high">High Priority</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-foreground">Title</label>
                                        <input type="text" name="title" placeholder="Notification title..." required class="form-input mt-1">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-foreground">Message</label>
                                        <textarea name="message" rows="4" placeholder="Enter your notification message..." required class="form-textarea mt-1"></textarea>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="checkbox" name="schedule" id="schedule" class="h-4 w-4 text-fixlanka-primary focus:ring-fixlanka-primary border-border rounded">
                                        <label for="schedule" class="ml-2 block text-sm text-foreground">Schedule for later</label>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" name="send_type" value="send" class="btn btn-primary flex-1" id="sendBtn">
                                            <i data-lucide="send" class="mr-2 h-4 w-4"></i>
                                            <span id="sendBtnText">Send Now</span>
                                        </button>
                                        <button type="submit" name="send_type" value="draft" class="btn btn-secondary flex-1" id="draftBtn">
                                            <i data-lucide="save" class="mr-2 h-4 w-4"></i>
                                            <span id="draftBtnText">Save Draft</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="recent-notifications-card">
                            <div class="p-6">
                                <div class="flex items-center justify-between w-full mb-4">
                                    <div>
                                        <h3 class="text-lg font-medium text-foreground flex items-center gap-2">
                                            <i data-lucide="history" class="h-5 w-5"></i>
                                            Recent Notifications
                                        </h3>
                                        <p class="text-sm text-muted-foreground">Latest 5 notifications with performance metrics</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button onclick="toggleNotificationView('list')" id="listViewBtn" class="btn btn-sm btn-primary">
                                            <i data-lucide="list" class="h-4 w-4"></i>
                                        </button>
                                        <button onclick="toggleNotificationView('grid')" id="gridViewBtn" class="btn btn-sm btn-secondary">
                                            <i data-lucide="grid-3x3" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                </div>

                                <div id="notificationsLoader" class="space-y-4">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <div class="animate-pulse">
                                            <div class="bg-muted/30 rounded-lg border p-4">
                                                <div class="h-4 bg-muted rounded w-3/4 mb-3"></div>
                                                <div class="h-3 bg-muted rounded w-1/2 mb-2"></div>
                                                <div class="h-3 bg-muted rounded w-1/4"></div>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>

                                <div id="notificationsList" class="space-y-4" style="display: none;">
                                </div>

                                <div id="notificationsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4" style="display: none;">
                                </div>

                                <div class="mt-4 text-center">
                                    <a href="<?= $basePath ?>/moderator/notifications/all" class="btn btn-outline">
                                        <i data-lucide="arrow-right" class="mr-2 h-4 w-4"></i>
                                        View All Notifications
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="templates-section">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-foreground">Quick Templates</h3>
                            <p class="text-sm text-muted-foreground">Pre-built templates for common notifications</p>

                            <div class="templates-grid">
                                <?php
                                $priorityVariants = [
                                    'low' => 'secondary',
                                    'medium' => 'outline',
                                    'high' => 'destructive'
                                ];

                                foreach (array_slice($templates, 0, 6) as $template):
                                ?>
                                    <div class="template-card">
                                        <div class="template-header">
                                            <h4 class="font-medium text-foreground"><?php echo htmlspecialchars($template['title']); ?></h4>
                                            <?php renderBadge(ucfirst($template['priority']), $priorityVariants[$template['priority']]); ?>
                                        </div>
                                        <p class="text-sm text-muted-foreground"><?php echo htmlspecialchars(substr($template['message'], 0, 100) . '...'); ?></p>
                                        <button onclick='useTemplate(<?php echo htmlspecialchars(json_encode($template)); ?>)' class="template-btn">
                                            Use Template
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <?php
            renderNotificationDetailsModal();
            renderEditNotificationModal();
            renderVideoNotificationModal();
            renderDeleteConfirmModal();
            ?>

            <script>
                window.basePath = "<?php echo $basePath; ?>";
                // Embed mock notifications data directly
                window.allNotifications = <?= json_encode($notificationsData) ?>;
            </script>

            <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/moderator/notifications/modalFunctions.js"></script>
            <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/moderator/notifications/notifications.js"></script>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>

</body>

</html>

