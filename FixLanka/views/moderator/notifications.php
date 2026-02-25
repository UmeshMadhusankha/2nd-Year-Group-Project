<?php
// notifications.php - Pure PHP MVC - ABSOLUTE PATHS (100% WORKING)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/NotificationModel.php';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $model = new NotificationModel($pdo);
        
        switch ($action) {
            case 'add':
                $title = trim($_POST['title'] ?? '');
                $message = trim($_POST['message'] ?? '');
                $recipients = trim($_POST['recipients'] ?? '');
                
                if (empty($title) || empty($message) || empty($recipients)) {
                    $_SESSION['error_message'] = 'All fields required';
                    break;
                }
                
                $recipientTypeMap = ['all' => 'all', 'providers' => 'repairer', 'customers' => 'user', 'companies' => 'company'];
                $recipient_type = $recipientTypeMap[$recipients] ?? 'all';
                
                $model->createNotification($title, $message, $recipient_type, 'sent');
                $_SESSION['success_message'] = 'Notification sent successfully!';
                break;
                
            case 'update':
                $notification_id = (int)($_POST['notification_id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                $message = trim($_POST['message'] ?? '');
                $recipients = trim($_POST['recipients'] ?? '');
                
                if (!$notification_id || empty($title) || empty($message) || empty($recipients)) {
                    $_SESSION['error_message'] = 'All fields required';
                    break;
                }
                
                $recipientTypeMap = ['all' => 'all', 'providers' => 'repairer', 'customers' => 'user', 'companies' => 'company'];
                $recipient_type = $recipientTypeMap[$recipients] ?? 'all';
                
                $model->updateNotification($notification_id, $title, $message, $recipient_type);
                $_SESSION['success_message'] = 'Notification updated successfully!';
                break;
                
            case 'delete':
                $notification_id = (int)($_POST['notification_id'] ?? 0);
                
                if (!$notification_id) {
                    $_SESSION['error_message'] = 'ID required';
                    break;
                }
                
                $rowsAffected = $model->deleteNotification($notification_id);
                
                if ($rowsAffected > 0) {
                    $_SESSION['success_message'] = 'Notification deleted successfully!';
                } else {
                    $_SESSION['error_message'] = 'Notification not found';
                }
                break;
        }
        
        // ABSOLUTE PATH redirect - NO 404 ERRORS
        header('Location: /2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php');
        exit;
        
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        $_SESSION['error_message'] = 'An error occurred. Please try again.';
        header('Location: /2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php');
        exit;
    }
}

// Fetch data
try {
    $model = new NotificationModel($pdo);
    $recentNotifications = $model->getRecentNotifications(5);
    $stats = $model->getNotificationStats();
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $recentNotifications = [];
    $stats = ['total' => 0, 'sent' => 0, 'pending' => 0, 'failed' => 0];
}

$basePath = '';
$currentPath = '/2nd-Year-Group-Project/FixLanka/moderator-notifications';

$successMessage = $_SESSION['success_message'] ?? '';
$errorMessage = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

$pageTitle = 'Notifications Dashboard';
$pageDescription = 'Send notifications and manage communication';

require_once __DIR__ . '/_components/notifications/mock-data.php';
$templates = getNotificationTemplates() ?? [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/notifications.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/moderators.css">
</head>

<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Notifications', 'Send notifications and manage communication with advanced filtering'); ?>

            <?php if ($successMessage): ?>
            <div id="successAlert" class="fixed top-4 right-4 z-50 max-w-md">
                <div class="bg-green-500/10 border border-green-500 text-green-500 px-4 py-3 rounded-lg shadow-lg">
                    <div class="flex items-start">
                        <i data-lucide="check-circle" class="h-5 w-5 mr-2 flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="font-medium">Success</p>
                            <p class="text-sm mt-1"><?php echo htmlspecialchars($successMessage); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
            <div id="errorAlert" class="fixed top-4 right-4 z-50 max-w-md">
                <div class="bg-red-500/10 border border-red-500 text-red-500 px-4 py-3 rounded-lg shadow-lg">
                    <div class="flex items-start">
                        <i data-lucide="alert-circle" class="h-5 w-5 mr-2 flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="font-medium">Error</p>
                            <p class="text-sm mt-1"><?php echo htmlspecialchars($errorMessage); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <main style="margin-top: 5rem;" class="notifications-content">
                <div class="space-y-6">
                    <!-- Page Header -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight text-foreground">Notifications Dashboard</h2>
                            <p class="text-muted-foreground">Send notifications and manage user communication with powerful filtering and sorting</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/all_notifications.php" class="btn btn-outline" style="text-decoration: none;">
                                <i data-lucide="list" class="mr-2 h-4 w-4"></i>
                                View All Notifications
                            </a>
                            <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php" class="btn btn-secondary" style="text-decoration: none;">
                                <i data-lucide="refresh-cw" class="mr-2 h-4 w-4"></i>
                                Refresh
                            </a>
                        </div>
                    </div>

                    <!-- Stats Cards (Matching Advertisement Review Style) -->
                    <div class="stats-grid-new">
                        <!-- Total Sent Card (Blue) -->
                        <div class="stat-card-new stat-card-blue">
                            <div class="stat-card-icon">
                                <i data-lucide="send" class="h-8 w-8"></i>
                            </div>
                            <div class="stat-card-content">
                                <div class="stat-card-number"><?php echo $stats['total'] ?? 0; ?></div>
                                <div class="stat-card-label">Total Sent</div>
                                <div class="stat-card-sublabel">All submissions</div>
                            </div>
                        </div>

                        <!-- Sent Card (Green) -->
                        <div class="stat-card-new stat-card-green">
                            <div class="stat-card-icon">
                                <i data-lucide="check-circle" class="h-8 w-8"></i>
                            </div>
                            <div class="stat-card-content">
                                <div class="stat-card-number"><?php echo $stats['sent'] ?? 0; ?></div>
                                <div class="stat-card-label">Sent</div>
                                <div class="stat-card-sublabel">Currently active</div>
                            </div>
                        </div>

                        <!-- Pending Card (Orange) -->
                        <div class="stat-card-new stat-card-orange">
                            <div class="stat-card-icon">
                                <i data-lucide="clock" class="h-8 w-8"></i>
                            </div>
                            <div class="stat-card-content">
                                <div class="stat-card-number"><?php echo $stats['pending'] ?? 0; ?></div>
                                <div class="stat-card-label">Pending</div>
                                <div class="stat-card-sublabel">Awaiting approval</div>
                            </div>
                        </div>

                        <!-- Failed Card (Red) -->
                        <div class="stat-card-new stat-card-red">
                            <div class="stat-card-icon">
                                <i data-lucide="x-circle" class="h-8 w-8"></i>
                            </div>
                            <div class="stat-card-content">
                                <div class="stat-card-number"><?php echo $stats['failed'] ?? 0; ?></div>
                                <div class="stat-card-label">Failed</div>
                                <div class="stat-card-sublabel">Not approved</div>
                            </div>
                        </div>
                    </div>

                    <div class="notifications-grid">
                        <!-- Send Form -->
                        <div class="send-notification-card">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-foreground flex items-center gap-2">
                                    <i data-lucide="send" class="h-5 w-5"></i>
                                    Send Notification
                                </h3>
                                <p class="text-sm text-muted-foreground">Broadcast messages to users</p>
                                
                                <form method="POST" action="/2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php" class="notification-form">
                                    <input type="hidden" name="action" value="add">
                                    
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
                                        <label class="block text-sm font-medium text-foreground">Title</label>
                                        <input type="text" name="title" placeholder="Notification title..." required class="form-input mt-1">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-foreground">Message</label>
                                        <textarea name="message" rows="4" placeholder="Enter your notification message..." required class="form-textarea mt-1"></textarea>
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary flex-1">
                                            <i data-lucide="send" class="mr-2 h-4 w-4"></i>
                                            Send Now
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Recent Notifications -->
                        <div class="recent-notifications-card">
                            <div class="p-6">
                                <div class="flex items-center justify-between w-full mb-4">
                                    <div>
                                        <h3 class="text-lg font-medium text-foreground flex items-center gap-2">
                                            <i data-lucide="bell" class="h-5 w-5"></i>
                                            Recent Notifications
                                        </h3>
                                        <p class="text-sm text-muted-foreground">Latest 5 notifications with performance metrics</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <?php if (empty($recentNotifications)): ?>
                                        <div class="text-center py-8 text-muted-foreground">
                                            <i data-lucide="inbox" class="h-12 w-12 mx-auto mb-2 opacity-50"></i>
                                            <p>No notifications yet</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($recentNotifications as $n): 
                                            $recipientDisplay = ['all' => 'All Users', 'repairer' => 'Service Providers', 'user' => 'Customers', 'company' => 'Companies'];
                                            $displayRecipient = $recipientDisplay[$n['recipient_type']] ?? $n['recipient_type'];
                                            
                                            $statusClass = 'badge-sent';
                                            if ($n['status'] === 'pending') $statusClass = 'badge-pending';
                                            if ($n['status'] === 'failed') $statusClass = 'badge-failed';
                                        ?>
                                        <div class="notification-item">
                                            <div class="notification-header">
                                                <div class="flex-1">
                                                    <p class="text-sm font-semibold text-foreground"><?php echo htmlspecialchars($n['title']); ?></p>
                                                    <p class="text-sm text-muted-foreground leading-relaxed mt-1"><?php echo htmlspecialchars($n['message']); ?></p>
                                                </div>
                                                <span class="badge <?php echo $statusClass; ?>">
                                                    <?php echo ucfirst($n['status']); ?>
                                                </span>
                                            </div>
                                            
                                            <div class="notification-meta">
                                                <div class="flex items-center space-x-4">
                                                    <span>To: <?php echo htmlspecialchars($displayRecipient); ?></span>
                                                    <span><?php echo date('M d, Y H:i', strtotime($n['send_date'])); ?></span>
                                                </div>
                                            </div>
                                            
                                            <div class="notification-footer">
                                                <div class="flex items-center space-x-2">
                                                    <button type="button" onclick='openEditModal(<?php echo json_encode($n, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' class="notification-action-btn edit-btn">
                                                        <i data-lucide="edit" class="h-3 w-3"></i>
                                                        Edit
                                                    </button>
                                                    
                                                    <button type="button" onclick="openDeleteModal(<?php echo $n['notification_id']; ?>)" class="notification-action-btn delete-btn">
                                                        <i data-lucide="trash-2" class="h-3 w-3"></i>
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Templates -->
                    <div class="templates-section">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-foreground">Quick Templates</h3>
                            <p class="text-sm text-muted-foreground">Pre-built templates for common notifications</p>

                            <div class="templates-grid">
                                <?php
                                $priorityVariants = [
                                    'low' => 'badge-low',
                                    'medium' => 'badge-medium',
                                    'high' => 'badge-high'
                                ];

                                foreach (array_slice($templates, 0, 6) as $template):
                                    $priorityClass = $priorityVariants[$template['priority']] ?? 'badge-low';
                                ?>
                                <div class="template-card">
                                    <div class="template-header">
                                        <span class="badge <?php echo $priorityClass; ?>">
                                            <?php echo ucfirst($template['priority']); ?>
                                        </span>
                                    </div>
                                    <h4 class="text-sm font-medium text-foreground"><?php echo htmlspecialchars($template['title']); ?></h4>
                                    <p class="text-xs text-muted-foreground"><?php echo htmlspecialchars($template['message']); ?></p>
                                    <button type="button" onclick='useTemplate(<?php echo json_encode($template, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' class="template-btn">
                                        Use Template
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Edit Modal -->
            <div id="editNotificationModal" class="modal-overlay" style="display: none;">
                <div class="moderators-modal-content">
                    <div class="moderators-modal-header">
                        <h3 class="text-lg font-medium text-card-foreground">Edit Notification</h3>
                        <button type="button" onclick="closeEditModal()" class="text-muted-foreground hover:text-foreground">
                            <i data-lucide="x" class="h-5 w-5"></i>
                        </button>
                    </div>
                    
                    <form method="POST" action="/2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php" class="moderators-modal-form">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="editNotificationId" name="notification_id">

                        <div class="moderators-form-group">
                            <label class="moderators-form-label">Recipients</label>
                            <select id="editRecipients" name="recipients" class="form-select" required>
                                <option value="all">All Users</option>
                                <option value="providers">Service Providers</option>
                                <option value="customers">Customers</option>
                                <option value="companies">Companies</option>
                            </select>
                        </div>

                        <div class="moderators-form-group">
                            <label class="moderators-form-label">Title</label>
                            <input type="text" id="editTitle" name="title" class="form-input" required>
                        </div>

                        <div class="moderators-form-group">
                            <label class="moderators-form-label">Message</label>
                            <textarea id="editMessage" name="message" rows="4" class="form-textarea" required></textarea>
                        </div>

                        <div class="moderators-form-actions">
                            <button type="button" onclick="closeEditModal()" class="moderators-cancel-btn">Cancel</button>
                            <button type="submit" class="moderators-save-btn">Update</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Modal -->
            <div id="deleteNotificationModal" class="modal-overlay" style="display: none;">
                <div class="moderators-modal-content" style="max-width: 400px;">
                    <div class="moderators-modal-header">
                        <h3 class="text-lg font-medium text-card-foreground">Confirm Delete</h3>
                        <p class="text-sm text-muted-foreground mb-4">Are you sure you want to delete this notification?</p>
                    </div>
                    
                    <form method="POST" action="/2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="deleteNotificationId" name="notification_id">
                        
                        <div class="moderators-form-actions">
                            <button type="button" onclick="closeDeleteModal()" class="moderators-cancel-btn">Cancel</button>
                            <button type="submit" class="moderators-btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Template use function
        function useTemplate(template) {
            const form = document.querySelector('form[action="/2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php"]');
            if (!form) {
                console.error('Form not found');
                return;
            }
            
            const recipientsSelect = form.querySelector('select[name="recipients"]');
            const titleInput = form.querySelector('input[name="title"]');
            const messageTextarea = form.querySelector('textarea[name="message"]');
            
            if (recipientsSelect) recipientsSelect.value = template.recipients || 'all';
            if (titleInput) titleInput.value = template.title || '';
            if (messageTextarea) messageTextarea.value = template.message || '';
            
            form.scrollIntoView({ behavior: 'smooth' });
            lucide.createIcons();
        }
        
        // Edit modal functions
        function openEditModal(n) {
            const map = {'all': 'all', 'repairer': 'providers', 'user': 'customers', 'company': 'companies'};
            document.getElementById('editNotificationId').value = n.notification_id;
            document.getElementById('editTitle').value = n.title;
            document.getElementById('editMessage').value = n.message;
            document.getElementById('editRecipients').value = map[n.recipient_type] || 'all';
            document.getElementById('editNotificationModal').style.display = 'flex';
            lucide.createIcons();
        }
        
        function closeEditModal() {
            document.getElementById('editNotificationModal').style.display = 'none';
        }
        
        // Delete modal functions
        function openDeleteModal(id) {
            document.getElementById('deleteNotificationId').value = id;
            document.getElementById('deleteNotificationModal').style.display = 'flex';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteNotificationModal').style.display = 'none';
        }
        
        // Close modals when clicking outside
        window.onclick = function(e) {
            const editModal = document.getElementById('editNotificationModal');
            const deleteModal = document.getElementById('deleteNotificationModal');
            if (e.target === editModal) closeEditModal();
            if (e.target === deleteModal) closeDeleteModal();
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('#successAlert, #errorAlert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>

</html>