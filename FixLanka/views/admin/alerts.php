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
require_once __DIR__ . '/../../controllers/AdminAlertController.php';

$basePath = '';
$currentPath = 'alerts';
$currentUserRole = strtolower((string)($_SESSION['user_role'] ?? 'admin'));

// ✅ VIEW ONLY FETCHES DATA - NO POST HANDLING
try {
    $controller = new AdminAlertController();
    
    // ✅ ONLY GET DATA FOR DISPLAY (View's job)
    $recentAlerts = $controller->getRecentAlerts();
    $statistics = $controller->getStatistics();
    
} catch (Exception $e) {
    error_log("Alert Page Error: " . $e->getMessage());
    $recentAlerts = [];
    $statistics = ['total' => 0, 'active' => 0, 'inactive' => 0];
}

// Get success/error messages from session
$message = $_SESSION['alert_message'] ?? '';
$messageType = $_SESSION['alert_type'] ?? '';
unset($_SESSION['alert_message'], $_SESSION['alert_type']);

// Alert Templates (static data for view)
$alertTemplates = [
    [
        'title' => 'Maintenance Notice',
        'priority' => 'high',
        'message' => 'System maintenance scheduled for [DATE] from [TIME] to [TIME]. Services may be temporarily unavailable.'
    ],
    [
        'title' => 'New Feature Announcement',
        'priority' => 'medium',
        'message' => 'We\'re excited to announce a new feature: [FEATURE_NAME]. Check it out in your dashboard!'
    ],
    [
        'title' => 'Policy Update',
        'priority' => 'medium',
        'message' => 'Important updates to our terms of service. Please review the changes in your account settings.'
    ],
    [
        'title' => 'Security Alert',
        'priority' => 'high',
        'message' => 'We\'ve detected unusual activity. Please verify your account security settings immediately.'
    ],
    [
        'title' => 'Payment Reminder',
        'priority' => 'medium',
        'message' => 'Your subscription payment is due soon. Update your payment method to avoid service interruption.'
    ],
    [
        'title' => 'Welcome Message',
        'priority' => 'low',
        'message' => 'Welcome to FixLanka! Get started by completing your profile and exploring our services.'
    ]
];

// Get page title and description
$pageTitle = 'Notifications - FixLanka Admin';
$pageDescription = 'Broadcast important messages to users across the platform';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/alerts.css?v=<?php echo time(); ?>">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
</head>

<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Notifications', 'Broadcast important messages to users across the platform'); ?>

            <main class="main-content">
                <!-- Success/Error Message -->
                <?php if (!empty($message)): ?>
                <div id="alertMessage" class="alert-success" style="display: flex;">
                    <i class="fa-solid <?php echo $messageType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                    <span><?php echo htmlspecialchars($message); ?></span>
                </div>
                <?php endif; ?>

                <!-- Two Column Grid -->
                <div class="alerts-grid">
                    <!-- Send Alert Card -->
                    <div class="section-card">
                        <div class="section-header">
                                <h3 class="section-title">
                                <i class="fa-solid fa-paper-plane"></i>
                                Send Notification
                            </h3>
                            <p class="section-subtitle">Broadcast messages to users</p>
                        </div>

                        <!-- ✅ FIXED: Form submits to proper route via index.php -->
                        <form method="POST" action="/2nd-Year-Group-Project/FixLanka/admin-alerts-action" class="alert-form" onsubmit="return validateForm()">
                            <input type="hidden" name="action" value="create" id="formAction">
                            <input type="hidden" name="alert_id" value="" id="editingAlertId">
                            
                            <div class="form-group">
                                <label class="form-label" for="recipientsSelect">Recipients</label>
                                <select id="recipientsSelect" name="target_role" class="form-select" required>
                                    <option value="All">All Users</option>
                                    <option value="User">Customers Only</option>
                                    <option value="Repairer">Repairers Only</option>
                                    <option value="Company">Companies Only</option>
                                    <option value="Moderator">Moderators Only</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="titleInput">Title</label>
                                <input type="text" id="titleInput" name="title" class="form-input" placeholder="Notification title..." required maxlength="255">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="prioritySelect">Priority</label>
                                <select id="prioritySelect" name="priority" class="form-select" required>
                                    <option value="low">Low Priority</option>
                                    <option value="medium" selected>Medium Priority</option>
                                    <option value="high">High Priority</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="messageTextarea">Message</label>
                                <textarea id="messageTextarea" name="message" class="form-textarea" placeholder="Enter your notification message..." required maxlength="5000"></textarea>
                            </div>

                            <div class="form-actions">
                                <button type="submit" id="submitButton" class="btn btn-primary">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    <span id="submitButtonText">Send Now</span>
                                </button>
                                <button type="button" id="cancelButton" onclick="cancelEdit()" class="btn btn-secondary" style="display: none;">
                                    <i class="fa-solid fa-xmark"></i>
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Recent Notifications Card -->
                    <div class="section-card">
                        <div class="section-header">
                                        <h3 class="section-title">
                                <i class="fa-solid fa-clock"></i>
                                Recent Notifications
                            </h3>
                            <p class="section-subtitle">Latest sent notifications</p>
                        </div>

                        <div class="alerts-list" id="alertsList">
                            <?php if (!empty($recentAlerts)): ?>
                                        <?php foreach ($recentAlerts as $alert): ?>
                                        <?php $creatorLabel = !empty($alert['created_by']) ? htmlspecialchars((string)$alert['created_by']) : 'Admin'; ?>
                                        <?php $isAdminCreated = strtolower((string)($alert['created_by_role'] ?? '')) === 'admin'; ?>
                                        <?php $lockEdit = ($currentUserRole === 'moderator' && $isAdminCreated); ?>
                                    <div class="alert-item" id="alert-<?= $alert['alert_id'] ?>" data-alert='<?= json_encode($alert) ?>' data-lock-edit="<?= $lockEdit ? '1' : '0' ?>">
                                    <div class="alert-header">
                                        <div class="alert-message"><?= htmlspecialchars($alert['message']) ?></div>
                                    </div>
                                    <div class="alert-meta">
                                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                                            <span class="badge badge-<?= strtolower($alert['status']) ?>"><?= $alert['status'] ?></span>
                                            <span><?= htmlspecialchars($alert['target_role']) ?></span>
                                                    <span>Created by: <?= $creatorLabel ?></span>
                                        </div>
                                        <span><?= date('M j, Y', strtotime($alert['created_at'])) ?></span>
                                    </div>
                                    <div class="alert-footer">
                                        <span class="badge badge-<?= strtolower($alert['priority']) ?>"><?= ucfirst($alert['priority']) ?></span>
                                        <div class="alert-actions">
                                            <button onclick="editAlert(<?= $alert['alert_id'] ?>)" class="notification-action-btn edit-btn" <?php echo $lockEdit ? 'disabled title="Admin-created notifications cannot be edited by moderators"' : ''; ?>>
                                                <i class="fa-solid fa-pencil"></i>
                                                Edit
                                            </button>
                                            <button onclick="deleteAlert(<?= $alert['alert_id'] ?>)" class="notification-action-btn delete-btn" <?php echo $lockEdit ? 'disabled title="Admin-created notifications cannot be deleted by moderators"' : ''; ?>>
                                                <i class="fa-solid fa-trash"></i>
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="text-align: center; color: #64748b; padding: 2rem;">No alerts sent yet</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Templates Section -->
                <div class="section-card templates-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fa-solid fa-table-cells"></i>
                            Quick Templates
                        </h3>
                        <p class="section-subtitle">Pre-built templates for common notifications</p>
                    </div>

                    <div class="templates-grid">
                        <?php foreach ($alertTemplates as $template): ?>
                        <div class="template-card">
                            <div class="template-header">
                                <h4 class="template-title"><?= htmlspecialchars($template['title']) ?></h4>
                                <span class="badge badge-<?= strtolower($template['priority']) ?>"><?= ucfirst($template['priority']) ?></span>
                            </div>
                            <p class="template-message"><?= htmlspecialchars($template['message']) ?></p>
                            <button onclick="useTemplate('<?= htmlspecialchars($template['title']) ?>', '<?= addslashes($template['message']) ?>', '<?= $template['priority'] ?>')" class="template-btn">
                                Use Template
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Form validation
        function validateForm() {
            const title = document.getElementById('titleInput').value.trim();
            const message = document.getElementById('messageTextarea').value.trim();
            
            if (title.length === 0) {
                alert('Please enter a title');
                return false;
            }
            
            if (message.length === 0) {
                alert('Please enter a message');
                return false;
            }
            
            if (title.length > 255) {
                alert('Title must be less than 255 characters');
                return false;
            }
            
            if (message.length > 5000) {
                alert('Message must be less than 5000 characters');
                return false;
            }
            
            return true;
        }

        // Use Template Function
        function useTemplate(title, message, priority) {
            document.getElementById('titleInput').value = title;
            document.getElementById('messageTextarea').value = message;
            document.getElementById('prioritySelect').value = priority;
            
            // Scroll to form
            document.querySelector('.section-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Focus on message field
            setTimeout(() => {
                document.getElementById('messageTextarea').focus();
            }, 500);
        }

        // ✅ FIXED: Edit Alert Function - submits to correct route
        function editAlert(alertId) {
            const alertElement = document.getElementById('alert-' + alertId);
            if (!alertElement) return;

            if (String(alertElement.getAttribute('data-lock-edit') || '0') === '1') {
                alert('Admin-created notifications cannot be edited by moderators.');
                return;
            }
            
            const alertData = JSON.parse(alertElement.getAttribute('data-alert'));
            
            // Populate form
            document.getElementById('formAction').value = 'update';
            document.getElementById('editingAlertId').value = alertData.alert_id;
            document.getElementById('titleInput').value = alertData.title;
            document.getElementById('messageTextarea').value = alertData.message;
            document.getElementById('recipientsSelect').value = alertData.target_role;
            document.getElementById('prioritySelect').value = alertData.priority;
            
            // Update button text
            document.getElementById('submitButtonText').textContent = 'Update Alert';
            
            // Show cancel button
            document.getElementById('cancelButton').style.display = 'inline-flex';
            
            // Scroll to form
            document.querySelector('.section-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Cancel Edit Function
        function cancelEdit() {
            // Clear form
            document.getElementById('formAction').value = 'create';
            document.getElementById('editingAlertId').value = '';
            document.getElementById('titleInput').value = '';
            document.getElementById('messageTextarea').value = '';
            document.getElementById('recipientsSelect').value = 'All';
            document.getElementById('prioritySelect').value = 'medium';
            
            // Reset button text
            document.getElementById('submitButtonText').textContent = 'Send Now';
            
            // Hide cancel button
            document.getElementById('cancelButton').style.display = 'none';
        }

        // ✅ FIXED: Delete Alert Function - submits to correct route
        function deleteAlert(alertId) {
            const alertElement = document.getElementById('alert-' + alertId);
            if (alertElement && String(alertElement.getAttribute('data-lock-edit') || '0') === '1') {
                alert('Admin-created notifications cannot be deleted by moderators.');
                return;
            }

            if (!confirm('Are you sure you want to delete this alert? This action cannot be undone.')) {
                return;
            }
            
            // Create form and submit to proper route
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/2nd-Year-Group-Project/FixLanka/admin-alerts-action'; // ✅ Proper route
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'alert_id';
            idInput.value = alertId;
            
            form.appendChild(actionInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Auto-hide success/error messages after 5 seconds
        setTimeout(() => {
            const alertMessage = document.getElementById('alertMessage');
            if (alertMessage) {
                alertMessage.style.opacity = '0';
                alertMessage.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    alertMessage.style.display = 'none';
                }, 300);
            }
        }, 5000);
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>

</html>