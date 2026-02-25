<?php
// VIEW ALL Notifications Page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/NotificationModel.php';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $model = new NotificationModel($pdo);
        
        switch ($action) {
            case 'update':
                $notification_id = (int)($_POST['notification_id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                $message = trim($_POST['message'] ?? '');
                $recipients = trim($_POST['recipients'] ?? '');
                
                if (!$notification_id || empty($title) || empty($message) || empty($recipients)) {
                    $_SESSION['error_message'] = 'All fields required';
                    break;
                }
                
                $recipientTypeMap = [
                    'all' => 'all',
                    'providers' => 'repairer',
                    'customers' => 'user',
                    'companies' => 'company'
                ];
                $recipient_type = $recipientTypeMap[$recipients] ?? 'all';
                
                $model->updateNotification($notification_id, $title, $message, $recipient_type);
                $_SESSION['success_message'] = 'Notification updated!';
                break;
                
            case 'delete':
                $notification_id = (int)($_POST['notification_id'] ?? 0);
                
                if (!$notification_id) {
                    $_SESSION['error_message'] = 'ID required';
                    break;
                }
                
                $rowsAffected = $model->deleteNotification($notification_id);
                
                if ($rowsAffected > 0) {
                    $_SESSION['success_message'] = 'Notification deleted!';
                } else {
                    $_SESSION['error_message'] = 'Not found';
                }
                break;
        }
        
        header('Location: all_notifications.php');
        exit;
        
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        $_SESSION['error_message'] = 'Error occurred';
        header('Location: all_notifications.php');
        exit;
    }
}

// Fetch all notifications
try {
    $model = new NotificationModel($pdo);
    $allNotifications = $model->getAllNotifications();
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $allNotifications = [];
}

$basePath = '';
$currentPath = '/2nd-Year-Group-Project/FixLanka/moderator-notifications';
$pageTitle = 'All Notifications';

$successMessage = $_SESSION['success_message'] ?? '';
$errorMessage = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, '', $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/notifications.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/moderators.css">
</head>

<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'All Notifications', 'View and manage all'); ?>

            <?php if ($successMessage): ?>
            <div class="fixed top-4 right-4 z-50 max-w-md">
                <div class="bg-green-500/10 border border-green-500 text-green-500 px-4 py-3 rounded-lg shadow-lg">
                    <p><?php echo htmlspecialchars($successMessage); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
            <div class="fixed top-4 right-4 z-50 max-w-md">
                <div class="bg-red-500/10 border border-red-500 text-red-500 px-4 py-3 rounded-lg shadow-lg">
                    <p><?php echo htmlspecialchars($errorMessage); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <main style="margin-top: 5rem; padding: 2rem;">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold">All (<?php echo count($allNotifications); ?>)</h2>
                    <a href="notifications.php" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left h-4 w-4 mr-2"></i>
                        Back
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #f9fafb;">
                            <tr>
                                <th style="padding: 0.75rem; text-align: left;">ID</th>
                                <th style="padding: 0.75rem; text-align: left;">Title</th>
                                <th style="padding: 0.75rem; text-align: left;">Message</th>
                                <th style="padding: 0.75rem; text-align: left;">To</th>
                                <th style="padding: 0.75rem; text-align: left;">Status</th>
                                <th style="padding: 0.75rem; text-align: left;">Date</th>
                                <th style="padding: 0.75rem; text-align: left;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($allNotifications)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem;">No data</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($allNotifications as $n): 
                                    $recipientDisplay = [
                                        'all' => 'All',
                                        'repairer' => 'Providers',
                                        'user' => 'Customers',
                                        'company' => 'Companies'
                                    ];
                                    $displayRecipient = $recipientDisplay[$n['recipient_type']] ?? $n['recipient_type'];
                                    
                                    $badgeClass = 'badge-sent';
                                    if ($n['status'] === 'pending') $badgeClass = 'badge-pending';
                                    if ($n['status'] === 'failed') $badgeClass = 'badge-failed';
                                ?>
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 0.75rem;">#<?php echo $n['notification_id']; ?></td>
                                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($n['title']); ?></td>
                                    <td style="padding: 0.75rem; max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($n['message']); ?>
                                    </td>
                                    <td style="padding: 0.75rem;"><?php echo $displayRecipient; ?></td>
                                    <td style="padding: 0.75rem;">
                                        <span class="badge <?php echo $badgeClass; ?>">
                                            <?php echo ucfirst($n['status']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 0.75rem;"><?php echo date('M d, Y', strtotime($n['send_date'])); ?></td>
                                    <td style="padding: 0.75rem;">
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($n)); ?>)" style="background: none; border: none; cursor: pointer; color: #3b82f6; margin-right: 8px;">
                                            <i class="fa-solid fa-pen-to-square h-4 w-4 inline"></i>
                                        </button>
                                        <button onclick="openDeleteModal(<?php echo $n['notification_id']; ?>)" style="background: none; border: none; cursor: pointer; color: #ef4444;">
                                            <i class="fa-solid fa-trash h-4 w-4 inline"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>

            <!-- Edit Modal -->
            <div id="editModal" class="modal-overlay">
                <div class="moderators-modal-content">
                    <div class="moderators-modal-header">
                        <h3>Edit</h3>
                        <button type="button" onclick="closeEditModal()">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    
                    <form method="POST" action="all_notifications.php" class="moderators-modal-form">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" id="editId" name="notification_id">

                        <div class="moderators-form-group">
                            <label class="moderators-form-label">Recipients</label>
                            <select id="editRecipients" name="recipients" class="moderators-form-input" required>
                                <option value="all">All</option>
                                <option value="providers">Providers</option>
                                <option value="customers">Customers</option>
                                <option value="companies">Companies</option>
                            </select>
                        </div>

                        <div class="moderators-form-group">
                            <label class="moderators-form-label">Title</label>
                            <input type="text" id="editTitle" name="title" class="moderators-form-input" required>
                        </div>

                        <div class="moderators-form-group">
                            <label class="moderators-form-label">Message</label>
                            <textarea id="editMessage" name="message" rows="4" class="moderators-form-input" required></textarea>
                        </div>

                        <div class="moderators-modal-footer">
                            <button type="button" onclick="closeEditModal()" class="moderators-cancel-btn">Cancel</button>
                            <button type="submit" class="moderators-save-btn">Update</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Modal -->
            <div id="deleteModal" class="modal-overlay">
                <div class="moderators-modal-content" style="max-width: 400px;">
                    <div class="moderators-modal-header">
                        <h3>Confirm</h3>
                        <button type="button" onclick="closeDeleteModal()">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    
                    <form method="POST" action="all_notifications.php">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="deleteId" name="notification_id">
                        
                        <div class="p-6">
                            <p>Delete this notification?</p>
                        </div>
                        
                        <div class="moderators-modal-footer">
                            <button type="button" onclick="closeDeleteModal()" class="moderators-cancel-btn">Cancel</button>
                            <button type="submit" class="moderators-btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        function openEditModal(n) {
            const map = {'all': 'all', 'repairer': 'providers', 'user': 'customers', 'company': 'companies'};
            document.getElementById('editId').value = n.notification_id;
            document.getElementById('editTitle').value = n.title;
            document.getElementById('editMessage').value = n.message;
            document.getElementById('editRecipients').value = map[n.recipient_type] || 'all';
            document.getElementById('editModal').style.display = 'flex';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        function openDeleteModal(id) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteModal').style.display = 'flex';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        
        window.onclick = function(e) {
            const editModal = document.getElementById('editModal');
            const deleteModal = document.getElementById('deleteModal');
            if (e.target === editModal) closeEditModal();
            if (e.target === deleteModal) closeDeleteModal();
        }
        
        setTimeout(() => {
            document.querySelectorAll('.fixed.top-4').forEach(a => {
                a.style.opacity = '0';
                a.style.transition = 'opacity 0.5s';
                setTimeout(() => a.remove(), 500);
            });
        }, 5000);
    </script>
</body>

</html>