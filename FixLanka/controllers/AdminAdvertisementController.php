<?php
/**
 * AdminAdvertisementController.php
 * ✅ ADMIN CONTROLLER (8-Status System with Override)
 * ✅ Pure MVC with PDO
 * Version: 3.0.0
 */

class AdminAdvertisementController
{
    private $model;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once __DIR__ . '/../models/AdminAdvertisementModel.php';
        $this->model = new AdminAdvertisementModel($this->pdo);
    }

    private function getCurrentAdminUsername()
    {
        // TODO: Replace with proper session authentication
        $adminUsername = $_SESSION['admin_username'] ?? 'admin';
        
        if (!$this->model->adminExists($adminUsername)) {
            error_log("CRITICAL: Admin '{$adminUsername}' does not exist!");
            return null;
        }
        
        return $adminUsername;
    }

    public function handlePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        if (!isset($_POST['action']) || !isset($_POST['ad_id'])) {
            $_SESSION['admin_message'] = "❌ Invalid request: Missing required parameters.";
            $_SESSION['admin_message_type'] = "error";
            return false;
        }

        $ad_id = intval($_POST['ad_id']);
        $action = trim($_POST['action']);
        $reason = trim($_POST['reason'] ?? '');
        $isOverride = isset($_POST['is_override']) && $_POST['is_override'] === 'true';
        
        $adminUsername = $this->getCurrentAdminUsername();
        
        if ($adminUsername === null) {
            $_SESSION['admin_message'] = "❌ CRITICAL ERROR: No valid admin found.";
            $_SESSION['admin_message_type'] = "error";
            return false;
        }
        
        $ad = $this->model->getAdvertisementById($ad_id);
        
        if (!$ad) {
            $_SESSION['admin_message'] = "❌ Advertisement #" . $ad_id . " not found.";
            $_SESSION['admin_message_type'] = "error";
            return false;
        }
        
        $statusMap = [
            'approve' => 'approved',
            'reject' => 'rejected',
            'suspend' => 'suspended',
            'activate' => 'active',
            'schedule' => 'scheduled',
            'pause' => 'paused',
            'deactivate' => 'inactive'
        ];
        
        if (!isset($statusMap[$action])) {
            $_SESSION['admin_message'] = "❌ Invalid action: '{$action}'.";
            $_SESSION['admin_message_type'] = "error";
            return false;
        }
        
        $newStatus = $statusMap[$action];
        
        $result = $this->model->updateStatus($ad_id, $newStatus, $adminUsername, $reason, $isOverride);
        
        $_SESSION['admin_message'] = $result['message'];
        $_SESSION['admin_message_type'] = $result['success'] ? "success" : "error";
        
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
            'search' => $_GET['search'] ?? '',
            'moderator' => $_GET['moderator'] ?? ''
        ];

        return [
            'stats' => $this->model->getAdminStatistics(),
            'ads' => $this->model->getAdvertisements($filters, 200),
            'filters' => $filters,
            'moderators' => $this->model->getAllModerators()
        ];
    }

    public function getStatusHistory($ad_id)
    {
        return $this->model->getStatusHistory(intval($ad_id));
    }

    public function getOverrideHistory($ad_id)
    {
        return $this->model->getOverrideHistory(intval($ad_id));
    }

    public function getMessages()
    {
        $message = $_SESSION['admin_message'] ?? '';
        $type = $_SESSION['admin_message_type'] ?? 'success';
        unset($_SESSION['admin_message'], $_SESSION['admin_message_type']);
        return ['message' => $message, 'type' => $type];
    }

    public function checkTables()
    {
        return $this->model->tableExists();
    }

    public function getAllowedActions($advertisement)
    {
        $status = strtolower($advertisement['status'] ?? '');
        
        $actions = [];
        
        switch ($status) {
            case 'pending':
                $actions = ['approve', 'reject', 'suspend'];
                break;
            case 'approved':
                $actions = ['schedule', 'activate', 'reject', 'suspend'];
                break;
            case 'rejected':
                $actions = ['override_approve'];
                break;
            case 'scheduled':
                $actions = ['activate', 'pause', 'deactivate', 'suspend'];
                break;
            case 'active':
                $actions = ['pause', 'deactivate', 'suspend'];
                break;
            case 'paused':
                $actions = ['activate', 'deactivate', 'suspend'];
                break;
            case 'inactive':
                $actions = ['activate', 'schedule', 'suspend'];
                break;
            case 'suspended':
                $actions = ['override_activate'];
                break;
        }
        
        return $actions;
    }

    public function requiresOverride($currentStatus)
    {
        return in_array(strtolower($currentStatus), ['rejected', 'suspended']);
    }
}