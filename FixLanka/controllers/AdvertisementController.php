<?php
/**
 * AdvertisementController.php
 * ✅ 3-STATUS SYSTEM: pending, approved, rejected
 * ✅ XAMPP CRASH-PROOF
 * Version: 2.0.0
 */

class AdvertisementController
{
    private $model;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        require_once __DIR__ . '/../models/AdvertisementModel.php';
        $this->model = new AdvertisementModel($this->conn);
    }

    /**
     * Get current moderator ID
     */
    private function getCurrentModeratorId()
    {
        // TODO: Replace with session-based authentication
        $moderatorId = 1;
        
        if (!$this->model->moderatorExists($moderatorId)) {
            error_log("CRITICAL: Moderator ID {$moderatorId} does not exist!");
            return null;
        }
        
        return $moderatorId;
    }

    /**
     * Handle POST requests (approve/reject only)
     */
    public function handlePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        if (!isset($_POST['action']) || !isset($_POST['ad_id'])) {
            $_SESSION['message'] = "❌ Invalid request.";
            $_SESSION['message_type'] = "error";
            return false;
        }

        $ad_id = intval($_POST['ad_id']);
        $action = $_POST['action'];
        
        // Get moderator ID
        $moderatorId = $this->getCurrentModeratorId();
        
        if ($moderatorId === null) {
            $_SESSION['message'] = "❌ CRITICAL ERROR: No valid moderator found. Please run create_database.sql.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        // Get current ad status
        $ad = $this->model->getAdvertisementById($ad_id);
        
        if (!$ad) {
            $_SESSION['message'] = "❌ Advertisement not found.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        $currentStatus = strtolower($ad['status']);
        
        // RULE: Rejected/Approved ads are FINAL
        if ($currentStatus === 'rejected') {
            $_SESSION['message'] = "🚫 BLOCKED: Rejected ads are FINAL and cannot be modified.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        if ($currentStatus === 'approved') {
            $_SESSION['message'] = "🚫 BLOCKED: Approved ads cannot be changed. Use the scheduling page instead.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        // Map action to status (only approve/reject allowed)
        $statusMap = [
            'approve' => 'approved',
            'reject' => 'rejected'
        ];
        
        if (!isset($statusMap[$action])) {
            $_SESSION['message'] = "❌ Invalid action: '{$action}'. Only 'approve' or 'reject' allowed.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        $newStatus = $statusMap[$action];
        
        // Attempt update
        $result = $this->model->updateStatus($ad_id, $newStatus, $moderatorId);
        
        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = $result['success'] ? "success" : "error";
        
        return $result['success'];
    }

    public function review($ad_id)
    {
        return $this->model->getAdvertisementById(intval($ad_id));
    }

    public function getViewData()
    {
        $filters = [
            'status' => $_GET['status'] ?? '',
            'type' => $_GET['type'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        return [
            'stats' => $this->model->getStatistics(),
            'ads' => $this->model->getAdvertisements($filters),
            'filters' => $filters
        ];
    }

    public function getMessages()
    {
        $message = $_SESSION['message'] ?? '';
        $type = $_SESSION['message_type'] ?? 'success';
        unset($_SESSION['message'], $_SESSION['message_type']);
        return ['message' => $message, 'type' => $type];
    }

    public function checkTable()
    {
        return $this->model->tableExists();
    }
    
    /**
     * Get allowed UI buttons (only for pending ads)
     */
    public function getAllowedActions($advertisement)
    {
        $status = strtolower($advertisement['status'] ?? '');
        
        if ($status === 'pending') {
            return ['approve', 'reject'];
        }
        
        return []; // No actions for approved/rejected
    }

    /**
     * Get status explanation
     */
    public function getStatusExplanation($status)
    {
        $explanations = [
            'pending' => 'This advertisement is awaiting review.',
            'approved' => 'This advertisement has been approved and can be scheduled.',
            'rejected' => 'This advertisement has been rejected and cannot be modified.'
        ];
        
        return $explanations[strtolower($status)] ?? 'Unknown status.';
    }
}