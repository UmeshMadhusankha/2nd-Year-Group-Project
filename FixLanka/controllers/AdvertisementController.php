<?php
/**
 * AdvertisementController.php
 * ✅ MODERATOR CONTROLLER (3-Status System)
 * ✅ Pure MVC with PDO
 * Version: 3.0.0
 */

class AdvertisementController
{
    private $model;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once __DIR__ . '/../models/AdvertisementModel.php';
        $this->model = new AdvertisementModel($this->pdo);
    }

    private function getCurrentModeratorId()
    {
        // TODO: Replace with proper session authentication
        $moderatorId = $_SESSION['moderator_id'] ?? 1;
        
        if (!$this->model->moderatorExists($moderatorId)) {
            error_log("CRITICAL: Moderator ID {$moderatorId} does not exist!");
            return null;
        }
        
        return $moderatorId;
    }

    public function handlePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        if (!isset($_POST['action']) || !isset($_POST['ad_id'])) {
            $_SESSION['message'] = "❌ Invalid request: Missing parameters.";
            $_SESSION['message_type'] = "error";
            return false;
        }

        $ad_id = intval($_POST['ad_id']);
        $action = trim($_POST['action']);
        $notes = trim($_POST['notes'] ?? '');
        
        $moderatorId = $this->getCurrentModeratorId();
        
        if ($moderatorId === null) {
            $_SESSION['message'] = "❌ CRITICAL ERROR: No valid moderator found.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        $ad = $this->model->getAdvertisementById($ad_id);
        
        if (!$ad) {
            $_SESSION['message'] = "❌ Advertisement not found.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        $statusMap = [
            'approve' => 'approved',
            'reject' => 'rejected'
        ];
        
        if (!isset($statusMap[$action])) {
            $_SESSION['message'] = "❌ Invalid action: '{$action}'.";
            $_SESSION['message_type'] = "error";
            return false;
        }
        
        $newStatus = $statusMap[$action];
        
        $result = $this->model->updateStatus($ad_id, $newStatus, $moderatorId, $notes);
        
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

    public function getAllowedActions($advertisement)
    {
        $status = strtolower($advertisement['status'] ?? '');
        
        if ($status === 'pending') {
            return ['approve', 'reject'];
        }
        
        return [];
    }

    public function getStatusExplanation($status)
    {
        $explanations = [
            'pending' => 'This advertisement is awaiting review.',
            'approved' => 'This advertisement has been approved. Only admin can modify.',
            'rejected' => 'This advertisement has been rejected. Only admin can override.'
        ];
        
        return $explanations[strtolower($status)] ?? 'Unknown status.';
    }
}