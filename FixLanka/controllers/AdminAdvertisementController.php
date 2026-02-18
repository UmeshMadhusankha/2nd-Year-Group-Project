<?php
/**
 * AdminAdvertisementController.php
 * ✅ COMPLETE ADMIN CONTROL with Override Capability
 * ✅ XAMPP CRASH-PROOF with validation
 * ✅ Full Status Management with History
 * Version: 1.0.0
 */

class AdminAdvertisementController
{
    private $model;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        require_once __DIR__ . '/../models/AdminAdvertisementModel.php';
        $this->model = new AdminAdvertisementModel($this->conn);
    }

    /**
     * ✅ Get current admin username from session
     */
    private function getCurrentAdminUsername()
    {
        // TODO: Replace with proper session-based authentication
        // For now, using a default admin username
        $adminUsername = 'admin';  // Change this to use $_SESSION['admin_username'] in production
        
        if (!$this->model->adminExists($adminUsername)) {
            error_log("CRITICAL: Admin username '{$adminUsername}' does not exist!");
            return null;
        }
        
        return $adminUsername;
    }

    /**
     * ✅ Handle POST requests (approve/reject/suspend/activate/override)
     */
    public function handlePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        if (!isset($_POST['action']) || !isset($_POST['ad_id'])) {
            $_SESSION['message'] = "❌ Invalid request: Missing required parameters.";
            $_SESSION['message_type'] = "error";
            return false;
        }

        $ad_id = intval($_POST['ad_id']);
        $action = trim($_POST['action']);
        $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
        $isOverride = isset($_POST['is_override']) && $_POST['is_override'] === 'true';
        
        // Get admin username
        $adminUsername = $this->getCurrentAdminUsername();
        
        if ($adminUsername === null) {
            $_SESSION['message'] = "❌ CRITICAL ERROR: No valid admin found. Please check admin authentication.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        // Get current advertisement
        $ad = $this->model->getAdvertisementById($ad_id);
        
        if (!$ad) {
            $_SESSION['message'] = "❌ Advertisement #" . $ad_id . " not found.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        $currentStatus = strtolower($ad['status']);
        
        // ✅ VALIDATION: Check if override is required but not provided
        if (($currentStatus === 'rejected' || $currentStatus === 'suspended') && !$isOverride) {
            $_SESSION['message'] = "🚫 This advertisement is {$currentStatus}. You must use the Override action to change it.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        // ✅ VALIDATION: Require reason for override actions
        if ($isOverride && empty($reason)) {
            $_SESSION['message'] = "❌ Override actions require a reason. Please provide an explanation.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        // Map action to status
        $statusMap = [
            'approve' => 'approved',
            'reject' => 'rejected',
            'schedule' => 'scheduled',
            'activate' => 'active',
            'pause' => 'paused',
            'deactivate' => 'inactive',
            'suspend' => 'suspended',
            'override_approve' => 'approved',
            'override_pending' => 'pending'
        ];
        
        if (!isset($statusMap[$action])) {
            $_SESSION['message'] = "❌ Invalid action: '{$action}'.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        $newStatus = $statusMap[$action];
        
        // ✅ BUSINESS RULE: Don't allow pointless updates
        if ($currentStatus === $newStatus && !$isOverride) {
            $_SESSION['message'] = "ℹ️ Advertisement is already {$currentStatus}. No change needed.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        // Attempt update
        $result = $this->model->updateStatus($ad_id, $newStatus, $adminUsername, $reason, $isOverride);
        
        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = $result['success'] ? "success" : "error";
        
        return $result['success'];
    }

    /**
     * ✅ Get advertisement for review modal
     */
    public function review($ad_id)
    {
        return $this->model->getAdvertisementById(intval($ad_id));
    }

    /**
     * ✅ Get all view data (ads, stats, filters, pagination)
     */
    public function getViewData()
    {
        $filters = [
            'status' => $_GET['status'] ?? '',
            'type' => $_GET['type'] ?? '',
            'search' => $_GET['search'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'moderator' => $_GET['moderator'] ?? '',
            'sort' => $_GET['sort'] ?? 'newest'
        ];

        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = 50;

        return [
            'stats' => $this->model->getStatistics(),
            'ads' => $this->model->getAdvertisements($filters, $page, $perPage),
            'filters' => $filters,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $this->model->getTotalCount($filters)
            ],
            'moderators' => $this->model->getAllModerators()
        ];
    }

    /**
     * ✅ Get status history for an advertisement
     */
    public function getStatusHistory($ad_id)
    {
        return $this->model->getStatusHistory(intval($ad_id));
    }

    /**
     * ✅ Get messages from session
     */
    public function getMessages()
    {
        $message = $_SESSION['message'] ?? '';
        $type = $_SESSION['message_type'] ?? 'success';
        unset($_SESSION['message'], $_SESSION['message_type']);
        return ['message' => $message, 'type' => $type];
    }

    /**
     * ✅ Check if tables exist
     */
    public function checkTables()
    {
        $adTableExists = $this->model->tableExists();
        $historyTableExists = $this->model->historyTableExists();
        
        if (!$adTableExists) {
            return [
                'success' => false,
                'message' => '⛔ Advertisement table does not exist. Please run create_database.sql.'
            ];
        }
        
        if (!$historyTableExists) {
            return [
                'success' => false,
                'message' => '⛔ ad_status_history table does not exist. Please run admin_ads_schema_update.sql.'
            ];
        }
        
        return ['success' => true];
    }
    
    /**
     * ✅ Get allowed actions for current status (for UI buttons)
     */
    public function getAllowedActions($advertisement)
    {
        $status = strtolower($advertisement['status'] ?? '');
        
        $actions = [];
        
        switch ($status) {
            case 'pending':
                $actions = ['approve', 'reject'];
                break;
            case 'approved':
                $actions = ['schedule', 'activate', 'reject', 'suspend'];
                break;
            case 'scheduled':
                $actions = ['activate', 'deactivate', 'suspend'];
                break;
            case 'active':
                $actions = ['pause', 'deactivate', 'suspend'];
                break;
            case 'paused':
                $actions = ['activate', 'deactivate', 'suspend'];
                break;
            case 'inactive':
                $actions = ['approve', 'activate', 'suspend'];
                break;
            case 'rejected':
                $actions = []; // Requires override
                break;
            case 'suspended':
                $actions = []; // Requires override
                break;
        }
        
        return $actions;
    }

    /**
     * ✅ Check if override is required for status change
     */
    public function requiresOverride($currentStatus)
    {
        return in_array(strtolower($currentStatus), ['rejected', 'suspended']);
    }

    /**
     * ✅ Get status badge HTML class
     */
    public function getStatusBadgeClass($status)
    {
        $classes = [
            'pending' => 'status-pending',
            'approved' => 'status-approved',
            'rejected' => 'status-rejected',
            'scheduled' => 'status-scheduled',
            'active' => 'status-active',
            'paused' => 'status-paused',
            'inactive' => 'status-inactive',
            'suspended' => 'status-suspended'
        ];
        
        return $classes[strtolower($status)] ?? 'status-unknown';
    }
}