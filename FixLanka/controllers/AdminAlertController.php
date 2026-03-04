<?php
/**
 * AdminAlertController.php
 * Handles HTTP requests for Admin Alert operations
 * Follows strict MVC pattern - NO direct database access, only Model calls
 */

require_once __DIR__ . '/../models/AdminAlertModel.php';

class AdminAlertController {
    private $model;
    private $currentAdmin;
    
    /**
     * Constructor - Initialize model and check authentication
     */
    public function __construct() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if admin is logged in
        $this->checkAuthentication();
        
        // Initialize model
        try {
            $this->model = new AdminAlertModel();
        } catch (Exception $e) {
            $this->handleError("System initialization failed. Please try again.");
        }
    }
    
    /**
     * Check if user is authenticated as admin
     */
    private function checkAuthentication() {
        // TODO: Replace with your actual admin authentication check
        // For now, assuming admin session is set
        if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
            $_SESSION['admin'] = 'admin'; // Default for testing
        }
        
        $this->currentAdmin = $_SESSION['admin'];
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
        
        // Create alert
        $alertData = [
            'title' => $title,
            'message' => $message,
            'target_role' => $targetRole,
            'priority' => $priority,
            'status' => 'active',
            'created_by' => $this->currentAdmin
        ];
        
        $alertId = $this->model->createAlert($alertData);
        
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
        
        if (isset($_POST['target_role'])) {
            $validRoles = ['User', 'Repairer', 'Company', 'Moderator', 'All'];
            if (in_array($_POST['target_role'], $validRoles)) {
                $updateData['target_role'] = $_POST['target_role'];
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
        
        $result = $this->model->updateAlert($alertId, $updateData);
        
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
        
        $result = $this->model->deleteAlert($alertId);
        
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
        
        $result = $this->model->toggleAlertStatus($alertId);
        
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
        
        $alert = $this->model->getAlertById($alertId);
        
        if ($alert) {
            // Store in session for display
            $_SESSION['current_alert'] = $alert;
            $this->redirect();
        } else {
            $this->redirect('error', 'Alert not found');
        }
    }
    
    /**
     * List all alerts
     */
    private function listAlerts() {
        $alerts = $this->model->getAllAlerts();
        $_SESSION['all_alerts'] = $alerts;
        
        // If this is called directly, redirect to view
        $this->redirect();
    }
    
    /**
     * Get recent alerts for display
     * @return array Recent alerts
     */
    public function getRecentAlerts() {
        return $this->model->getRecentAlerts(5);
    }
    
    /**
     * Get all alerts for display
     * @return array All alerts
     */
    public function getAllAlerts() {
        return $this->model->getAllAlerts();
    }
    
    /**
     * Get alert statistics
     * @return array Statistics
     */
    public function getStatistics() {
        return $this->model->getAlertStatistics();
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