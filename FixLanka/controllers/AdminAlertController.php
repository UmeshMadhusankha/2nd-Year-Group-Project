<?php
/**
 * AdminAlertController.php
 * Handles HTTP requests for Admin Alert operations
 * Follows strict MVC pattern - NO direct database access, only Model calls
 */

require_once __DIR__ . '/../config/databse.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../includes/admin-modarator/auth.php';

class AdminAlertController {
    private $model;
    private array $actor = [];
    
    /**
     * Constructor - Initialize model and check authentication
     */
    public function __construct() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Ensure user is authenticated as admin
        requireRole('admin');
        
        $this->checkAuthentication();
        
        // Initialize model
        try {
            global $pdo;
            $this->model = new NotificationModel($pdo);
        } catch (Exception $e) {
            $this->handleError("System initialization failed. Please try again.");
        }
    }
    
    /**
     * Check if user is authenticated as admin
     */
    private function checkAuthentication() {
        $user = function_exists('getCurrentUser') ? getCurrentUser() : null;

        if ($user) {
            $this->actor = [
                'id' => (int)($user['id'] ?? 0),
                'role' => (string)($user['role'] ?? 'admin'),
                'name' => (string)($user['name'] ?? 'Admin'),
            ];
            return;
        }

        $this->actor = [
            'id' => (int)($_SESSION['user_id'] ?? 0),
            'role' => (string)($_SESSION['user_role'] ?? 'admin'),
            'name' => (string)($_SESSION['user_name'] ?? 'Admin'),
        ];
    }

    private function roleToRecipientType(string $targetRole): string {
        $map = [
            'all' => 'all',
            'user' => 'user',
            'repairer' => 'repairer',
            'company' => 'company',
            'moderator' => 'all',
        ];
        $key = strtolower(trim($targetRole));
        return $map[$key] ?? 'all';
    }

    private function recipientTypeToRole(string $recipientType): string {
        $map = [
            'all' => 'All',
            'user' => 'User',
            'repairer' => 'Repairer',
            'company' => 'Company',
        ];
        $key = strtolower(trim($recipientType));
        return $map[$key] ?? 'All';
    }

    private function mapNotificationToAlert(array $row): array {
        $createdBy = trim((string)($row['created_by_name'] ?? ''));
        if ($createdBy === '') {
            $createdByRole = strtolower((string)($row['created_by_role'] ?? ''));
            $createdBy = $createdByRole === 'moderator' ? 'Moderator' : ($createdByRole === 'admin' ? 'Admin' : 'System');
        }

        return [
            'alert_id' => (int)($row['notification_id'] ?? 0),
            'notification_id' => (int)($row['notification_id'] ?? 0),
            'title' => (string)($row['title'] ?? ''),
            'message' => (string)($row['message'] ?? ''),
            'target_role' => $this->recipientTypeToRole((string)($row['recipient_type'] ?? 'all')),
            'recipient_type' => (string)($row['recipient_type'] ?? 'all'),
            'priority' => 'medium',
            'status' => (string)($row['status'] ?? 'sent'),
            'created_by' => $createdBy,
            'created_by_role' => (string)($row['created_by_role'] ?? ''),
            'created_at' => (string)($row['created_at'] ?? ($row['send_date'] ?? '')),
        ];
    }
    
    /**
     * Main entry point - Route requests based on action
     */
    public function handleRequest() {
        $action = $_POST['action'] ?? $_GET['action'] ?? 'list';
        
        try {
            switch ($action) {
                case 'create':
                    $this->createAlert();
                    break;
                
                case 'update':
                    $this->updateAlert();
                    break;
                
                case 'delete':
                    $this->deleteAlert();
                    break;
                
                case 'toggle_status':
                    $this->toggleStatus();
                    break;
                
                case 'get':
                    $this->getAlert();
                    break;
                
                case 'list':
                default:
                    $this->listAlerts();
                    break;
            }
        } catch (Exception $e) {
            error_log("AdminAlertController Error: " . $e->getMessage());
            $this->handleError("An unexpected error occurred. Please try again.");
        }
    }
    
    /**
     * Create a new alert
     */
    private function createAlert() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('error', 'Invalid request method');
            return;
        }
        
        // Server-side validation
        $errors = [];
        
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $targetRole = $_POST['target_role'] ?? 'All';
        $priority = $_POST['priority'] ?? 'medium';
        
        if (empty($title)) {
            $errors[] = "Title is required";
        } elseif (strlen($title) > 255) {
            $errors[] = "Title must be less than 255 characters";
        }
        
        if (empty($message)) {
            $errors[] = "Message is required";
        } elseif (strlen($message) > 5000) {
            $errors[] = "Message is too long (max 5000 characters)";
        }
        
        $validRoles = ['User', 'Repairer', 'Company', 'Moderator', 'All'];
        if (!in_array($targetRole, $validRoles)) {
            $targetRole = 'All';
        }
        
        $validPriorities = ['low', 'medium', 'high'];
        if (!in_array($priority, $validPriorities)) {
            $priority = 'medium';
        }
        
        if (!empty($errors)) {
            $this->redirect('error', implode(', ', $errors));
            return;
        }
        
        $recipientType = $this->roleToRecipientType($targetRole);
        $alertId = $this->model->createNotification($title, $message, $recipientType, 'sent', $this->actor);
        
        if ($alertId) {
            $this->redirect('success', 'Alert sent successfully!');
        } else {
            $this->redirect('error', 'Failed to send alert. Please try again.');
        }
    }
    
    /**
     * Update an existing alert
     */
    private function updateAlert() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('error', 'Invalid request method');
            return;
        }
        
        $alertId = (int)($_POST['alert_id'] ?? 0);
        
        if ($alertId <= 0) {
            $this->redirect('error', 'Invalid alert ID');
            return;
        }
        
        // Validate input
        $updateData = [];
        
        if (isset($_POST['title'])) {
            $title = trim($_POST['title']);
            if (!empty($title) && strlen($title) <= 255) {
                $updateData['title'] = $title;
            }
        }
        
        if (isset($_POST['message'])) {
            $message = trim($_POST['message']);
            if (!empty($message) && strlen($message) <= 5000) {
                $updateData['message'] = $message;
            }
        }
        
        $targetRole = null;
        if (isset($_POST['target_role'])) {
            $validRoles = ['User', 'Repairer', 'Company', 'Moderator', 'All'];
            if (in_array($_POST['target_role'], $validRoles)) {
                $targetRole = $_POST['target_role'];
            }
        }
        
        if (isset($_POST['priority'])) {
            $validPriorities = ['low', 'medium', 'high'];
            if (in_array($_POST['priority'], $validPriorities)) {
                $updateData['priority'] = $_POST['priority'];
            }
        }
        
        if (empty($updateData)) {
            $this->redirect('error', 'No valid data to update');
            return;
        }
        
        $existing = $this->model->getNotificationById($alertId);
        if (!$existing) {
            $this->redirect('error', 'Alert not found');
            return;
        }

        $newTitle = (string)($updateData['title'] ?? $existing['title']);
        $newMessage = (string)($updateData['message'] ?? $existing['message']);
        $newRecipientType = $targetRole !== null
            ? $this->roleToRecipientType($targetRole)
            : (string)($existing['recipient_type'] ?? 'all');

        $result = $this->model->updateNotification($alertId, $newTitle, $newMessage, $newRecipientType, $this->actor);
        
        if ($result) {
            $this->redirect('success', 'Alert updated successfully!');
        } else {
            $this->redirect('error', 'Failed to update alert. Please try again.');
        }
    }
    
    /**
     * Delete an alert
     */
    private function deleteAlert() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('error', 'Invalid request method');
            return;
        }
        
        $alertId = (int)($_POST['alert_id'] ?? 0);
        
        if ($alertId <= 0) {
            $this->redirect('error', 'Invalid alert ID');
            return;
        }
        
        $result = $this->model->deleteNotification($alertId, $this->actor) > 0;
        
        if ($result) {
            $this->redirect('success', 'Alert deleted successfully!');
        } else {
            $this->redirect('error', 'Failed to delete alert. Please try again.');
        }
    }
    
    /**
     * Toggle alert status
     */
    private function toggleStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('error', 'Invalid request method');
            return;
        }
        
        $alertId = (int)($_POST['alert_id'] ?? 0);
        
        if ($alertId <= 0) {
            $this->redirect('error', 'Invalid alert ID');
            return;
        }
        
        $alert = $this->model->getNotificationById($alertId);
        if (!$alert) {
            $this->redirect('error', 'Alert not found');
            return;
        }

        $currentStatus = strtolower((string)($alert['status'] ?? 'sent'));
        $nextStatus = $currentStatus === 'pending' ? 'sent' : 'pending';
        $result = $this->model->updateNotificationStatus($alertId, $nextStatus);
        
        if ($result) {
            $this->redirect('success', 'Alert status updated!');
        } else {
            $this->redirect('error', 'Failed to update status. Please try again.');
        }
    }
    
    /**
     * Get a single alert (for AJAX-like responses without AJAX)
     */
    private function getAlert() {
        $alertId = (int)($_GET['alert_id'] ?? 0);
        
        if ($alertId <= 0) {
            $this->redirect('error', 'Invalid alert ID');
            return;
        }
        
        $alert = $this->model->getNotificationById($alertId);
        
        if ($alert) {
            // Store in session for display
            $_SESSION['current_alert'] = $this->mapNotificationToAlert($alert);
            $this->redirect();
        } else {
            $this->redirect('error', 'Alert not found');
        }
    }
    
    /**
     * List all alerts
     */
    private function listAlerts() {
        $alerts = array_map([$this, 'mapNotificationToAlert'], $this->model->getAllNotifications());
        $_SESSION['all_alerts'] = $alerts;
        
        // If this is called directly, redirect to view
        $this->redirect();
    }
    
    /**
     * Get recent alerts for display
     * @return array Recent alerts
     */
    public function getRecentAlerts() {
        $all = $this->model->getAllNotifications();
        $mapped = array_map([$this, 'mapNotificationToAlert'], $all);
        return array_slice($mapped, 0, 5);
    }
    
    /**
     * Get all alerts for display
     * @return array All alerts
     */
    public function getAllAlerts() {
        return array_map([$this, 'mapNotificationToAlert'], $this->model->getAllNotifications());
    }
    
    /**
     * Get alert statistics
     * @return array Statistics
     */
    public function getStatistics() {
        $stats = $this->model->getNotificationStats();
        return [
            'total' => (int)($stats['total'] ?? 0),
            'active' => (int)($stats['sent'] ?? 0),
            'inactive' => (int)($stats['pending'] ?? 0),
            'failed' => (int)($stats['failed'] ?? 0),
        ];
    }
    
    /**
     * Redirect helper - FIXED TO USE CORRECT PATH
     * @param string|null $messageType Message type (success/error)
     * @param string|null $message Message text
     */
    private function redirect($messageType = null, $message = null) {
        if ($messageType && $message) {
            $_SESSION['alert_message'] = $message;
            $_SESSION['alert_type'] = $messageType;
        }
        
        // Don't redirect if headers already sent
        if (headers_sent()) {
            return;
        }
        
        // FIXED: Correct path including views/admin/
        $baseUrl = '/2nd-Year-Group-Project/FixLanka/views/admin/alerts.php';
        header("Location: $baseUrl");
        exit();
    }
    
    /**
     * Handle errors safely
     * @param string $message Error message
     */
    private function handleError($message) {
        $_SESSION['alert_message'] = $message;
        $_SESSION['alert_type'] = 'error';
        
        if (!headers_sent()) {
            header("Location: /2nd-Year-Group-Project/FixLanka/views/admin/alerts.php");
            exit();
        }
    }
}
?>