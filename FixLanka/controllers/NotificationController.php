<?php
// NotificationController.php - Business logic layer for notification operations
// Handles validation and coordinates between API and Model

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class NotificationController
{
    private $model;

    public function __construct()
    {
        global $pdo;
        $this->model = new NotificationModel($pdo);
    }

    /**
     * Get all notifications
     */
    public function getAllNotifications()
    {
        try {
            $notifications = $this->model->getAllNotifications();
            $this->jsonResponse(['success' => true, 'data' => $notifications]);
        } catch (PDOException $e) {
            error_log("Error fetching notifications: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to fetch notifications'], 500);
        }
    }

    /**
     * Get recent notifications
     */
    public function getRecentNotifications()
    {
        try {
            // error_log("[CONTROLLER] getRecentNotifications called");
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
            $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
            $user_type = isset($_GET['user_type']) ? $_GET['user_type'] : 'all';

            // Map frontend user types to DB enum types if needed
            // (Assuming frontend sends 'company', 'repairer', 'user' matching DB)
            
            $notifications = $this->model->getRecentNotifications($user_id, $user_type, $limit);
            // error_log("[CONTROLLER] Fetched " . count($notifications) . " notifications");
            $this->jsonResponse(['success' => true, 'notifications' => $notifications]); // topbar.js expects 'notifications' key
        } catch (PDOException $e) {
            error_log("[CONTROLLER ERROR] " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to fetch notifications'], 500);
        } catch (Exception $e) {
            error_log("[CONTROLLER ERROR] " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Unexpected error'], 500);
        }
    }

    /**
     * Get notification count
     */
    /**
     * Get notification count
     */
    public function getNotificationCount()
    {
        try {
            $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
            $user_type = $_GET['user_type'] ?? 'all';
            
            // Map frontend type to DB type if needed (assuming 1:1 for now)
            // But Controller logic previously mapped 'customer' -> 'user'
            $recipientTypeMap = [
                'repairer' => 'repairer',
                'customer' => 'user', 
                'company' => 'company',
                'all' => 'all'
            ];
            
            $recipient_type = $recipientTypeMap[$user_type] ?? $user_type;
            
            $count = $this->model->getNotificationCount($user_id, $recipient_type);
            
            $this->jsonResponse(['success' => true, 'count' => $count]);
        } catch (PDOException $e) {
            error_log("Error counting notifications: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to count notifications'], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats()
    {
        try {
            $stats = $this->model->getNotificationStats();
            $this->jsonResponse(['success' => true, 'data' => $stats]);
        } catch (PDOException $e) {
            error_log("Error fetching notification stats: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to fetch statistics'], 500);
        }
    }

    /**
     * Add new notification
     */
    public function addNotification()
    {
        // Get and sanitize input
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $recipients = trim($_POST['recipients'] ?? '');

        // Debug logging
        error_log("ADD Notification - Title: $title, Recipients: $recipients");

        // Validation
        if (empty($title) || empty($message) || empty($recipients)) {
            $this->jsonResponse(['success' => false, 'message' => 'All fields are required'], 400);
            return;
        }

        // Map recipient types from form to database enum
        $recipientTypeMap = [
            'all' => 'all',
            'providers' => 'repairer',
            'customers' => 'user',
            'companies' => 'company'
        ];

        $recipient_type = $recipientTypeMap[$recipients] ?? 'all';

        try {
            // Create notification with 'sent' status (sent immediately)
            $notification_id = $this->model->createNotification($title, $message, $recipient_type, 'sent');

            // Fetch the newly created notification
            $notification = $this->model->getNotificationById($notification_id);

            $this->jsonResponse([
                'success' => true, 
                'message' => 'Notification sent successfully', 
                'data' => $notification
            ]);
        } catch (PDOException $e) {
            error_log("Error adding notification: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to send notification'], 500);
        }
    }

    /**
     * Update notification
     */
    public function updateNotification()
    {
        // Get and sanitize input
        $notification_id = (int)($_POST['notification_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $recipients = trim($_POST['recipients'] ?? '');

        // Debug logging
        error_log("UPDATE Notification - ID: $notification_id, Title: $title, Recipients: $recipients");

        // Validation
        if (!$notification_id || empty($title) || empty($message) || empty($recipients)) {
            $this->jsonResponse(['success' => false, 'message' => 'All fields are required'], 400);
            return;
        }

        // Map recipient types
        $recipientTypeMap = [
            'all' => 'all',
            'providers' => 'repairer',
            'customers' => 'user',
            'companies' => 'company'
        ];

        $recipient_type = $recipientTypeMap[$recipients] ?? 'all';

        try {
            // Update notification
            $this->model->updateNotification($notification_id, $title, $message, $recipient_type);

            // Fetch updated notification
            $notification = $this->model->getNotificationById($notification_id);

            $this->jsonResponse([
                'success' => true, 
                'message' => 'Notification updated successfully', 
                'data' => $notification
            ]);
        } catch (PDOException $e) {
            error_log("Error updating notification: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to update notification'], 500);
        }
    }

    /**
     * Delete notification
     */
    public function deleteNotification()
    {
        $notification_id = (int)($_POST['notification_id'] ?? 0);

        // Debug logging
        error_log("DELETE Notification - ID: $notification_id");

        // Validation
        if (!$notification_id) {
            $this->jsonResponse(['success' => false, 'message' => 'Notification ID is required'], 400);
            return;
        }

        try {
            $rowsAffected = $this->model->deleteNotification($notification_id);

            if ($rowsAffected > 0) {
                $this->jsonResponse(['success' => true, 'message' => 'Notification deleted successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Notification not found'], 404);
            }
        } catch (PDOException $e) {
            error_log("Error deleting notification: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to delete notification'], 500);
        }
    }

    /**
     * Helper function to send JSON response
     */
    private function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
