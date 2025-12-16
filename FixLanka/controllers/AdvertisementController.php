<?php
/**
 * AdvertisementController.php
 * FIXED - Logs moderator actions in real-time
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
     * Handle POST requests + LOG MODERATOR ACTIVITY
     */
    public function handlePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        if (!isset($_POST['action']) || !isset($_POST['ad_id'])) {
            return false;
        }

        $ad_id = intval($_POST['ad_id']);
        $action = $_POST['action'];
        
        // Get ad details BEFORE updating
        $ad = $this->model->getAdvertisementById($ad_id);
        
        // Determine new status
        $new_status = '';
        if ($action === 'approve') {
            $new_status = 'approved';
        } elseif ($action === 'reject') {
            $new_status = 'rejected';
        } elseif ($action === 'activate') {
            $new_status = 'active';
        }
        
        // Update status
        if (!empty($new_status)) {
            if ($this->model->updateStatus($ad_id, $new_status)) {
                // LOG THE ACTIVITY
                $this->logModeratorActivity($action, $ad_id, $ad['title'] ?? 'Unknown Ad');
                
                $_SESSION['message'] = "Advertisement " . ucfirst($action) . "d successfully!";
                $_SESSION['message_type'] = "success";
                return true;
            } else {
                $_SESSION['message'] = "Error updating advertisement.";
                $_SESSION['message_type'] = "error";
                return false;
            }
        }
        
        return false;
    }

    /**
     * Log moderator activity to moderator_activity table
     * FIXED: Creates PDO connection directly instead of using global
     */
    private function logModeratorActivity($action, $target_id, $target_title)
    {
        $activityMap = [
            'approve' => 'ad_approved',
            'reject' => 'ad_rejected',
            'activate' => 'ad_activated'
        ];
        
        $activity_type = $activityMap[$action] ?? 'ad_approved';
        $description = "Advertisement '{$target_title}' was {$action}d";
        
        try {
            // Create PDO connection directly
            $pdo = new PDO(
                "mysql:host=localhost;dbname=fix_lanka;charset=utf8mb4",
                "root",
                "",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            $stmt = $pdo->prepare("
                INSERT INTO moderator_activity (moderator_id, activity_type, target_id, target_title, description, created_at)
                VALUES (1, :activity_type, :target_id, :target_title, :description, NOW())
            ");
            $stmt->execute([
                'activity_type' => $activity_type,
                'target_id' => $target_id,
                'target_title' => $target_title,
                'description' => $description
            ]);
        } catch (PDOException $e) {
            error_log("Failed to log moderator activity: " . $e->getMessage());
        }
    }

    public function review($ad_id)
    {
        if (!is_numeric($ad_id) || $ad_id <= 0) {
            return null;
        }
        return $this->model->getAdvertisementById(intval($ad_id));
    }

    public function getViewData()
    {
        $filters = [
            'status' => isset($_GET['status']) && $_GET['status'] !== '' && $_GET['status'] !== 'All' ? $_GET['status'] : '',
            'type' => isset($_GET['type']) && $_GET['type'] !== '' && $_GET['type'] !== 'All' ? $_GET['type'] : '',
            'search' => isset($_GET['search']) ? trim($_GET['search']) : ''
        ];

        $stats = $this->model->getStatistics();
        $ads = $this->model->getAdvertisements($filters);

        return [
            'stats' => $stats,
            'ads' => $ads,
            'filters' => $filters
        ];
    }

    public function getMessages()
    {
        $message = '';
        $messageType = 'success';
        
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            $messageType = $_SESSION['message_type'] ?? 'success';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }

        return [
            'message' => $message,
            'type' => $messageType
        ];
    }

    public function checkTable()
    {
        return $this->model->tableExists();
    }
}