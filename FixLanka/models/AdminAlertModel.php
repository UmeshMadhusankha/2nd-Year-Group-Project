<?php
/**
 * AdminAlertModel.php
 * Handles all database operations for Admin Alerts
 * Follows strict MVC pattern - NO HTML, only database operations
 */

class AdminAlertModel {
    private $db;
    
    /**
     * Constructor - Initialize database connection
     */
    public function __construct() {
        try {
            // Database configuration
            $host = 'localhost';
            $dbname = 'fix_lanka';
            $username = 'root';
            $password = '';
            
            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $this->db = new PDO($dsn, $username, $password, $options);
            
        } catch (PDOException $e) {
            error_log("AdminAlertModel DB Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed. Please check your database configuration.");
        }
    }
    
    /**
     * Get all alerts with optional filtering
     * @param string|null $status Filter by status (active/inactive/null for all)
     * @param string|null $targetRole Filter by target role
     * @return array List of alerts
     */
    public function getAllAlerts($status = null, $targetRole = null) {
        try {
            $sql = "SELECT * FROM AdminAlert WHERE 1=1";
            $params = [];
            
            if ($status !== null) {
                $sql .= " AND status = :status";
                $params[':status'] = $status;
            }
            
            if ($targetRole !== null) {
                $sql .= " AND target_role = :target_role";
                $params[':target_role'] = $targetRole;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get All Alerts Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get a single alert by ID
     * @param int $alertId Alert ID
     * @return array|null Alert data or null if not found
     */
    public function getAlertById($alertId) {
        try {
            if (!is_numeric($alertId) || $alertId <= 0) {
                return null;
            }
            
            $stmt = $this->db->prepare("SELECT * FROM AdminAlert WHERE alert_id = :alert_id LIMIT 1");
            $stmt->execute([':alert_id' => $alertId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result : null;
        } catch (PDOException $e) {
            error_log("Get Alert By ID Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new alert
     * @param array $data Alert data (title, message, target_role, priority, created_by)
     * @return int|false Alert ID if successful, false otherwise
     */
    public function createAlert($data) {
        try {
            // Validate required fields
            if (empty($data['title']) || empty($data['message']) || empty($data['created_by'])) {
                throw new Exception("Required fields missing");
            }
            
            // Set defaults
            $targetRole = isset($data['target_role']) ? $data['target_role'] : 'All';
            $priority = isset($data['priority']) ? $data['priority'] : 'medium';
            $status = isset($data['status']) ? $data['status'] : 'active';
            
            // Validate enum values
            $validRoles = ['User', 'Repairer', 'Company', 'Moderator', 'All'];
            $validPriorities = ['low', 'medium', 'high'];
            $validStatuses = ['active', 'inactive'];
            
            if (!in_array($targetRole, $validRoles)) {
                $targetRole = 'All';
            }
            if (!in_array($priority, $validPriorities)) {
                $priority = 'medium';
            }
            if (!in_array($status, $validStatuses)) {
                $status = 'active';
            }
            
            $sql = "INSERT INTO AdminAlert (title, message, target_role, priority, status, created_by) 
                    VALUES (:title, :message, :target_role, :priority, :status, :created_by)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':title' => trim($data['title']),
                ':message' => trim($data['message']),
                ':target_role' => $targetRole,
                ':priority' => $priority,
                ':status' => $status,
                ':created_by' => $data['created_by']
            ]);
            
            return $result ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("Create Alert Error: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Create Alert Validation Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing alert
     * @param int $alertId Alert ID
     * @param array $data Alert data to update
     * @return bool True if successful, false otherwise
     */
    public function updateAlert($alertId, $data) {
        try {
            if (!is_numeric($alertId) || $alertId <= 0) {
                return false;
            }
            
            // Check if alert exists
            $existing = $this->getAlertById($alertId);
            if (!$existing) {
                return false;
            }
            
            // Build update query dynamically based on provided data
            $updateFields = [];
            $params = [':alert_id' => $alertId];
            
            if (isset($data['title'])) {
                $updateFields[] = "title = :title";
                $params[':title'] = trim($data['title']);
            }
            
            if (isset($data['message'])) {
                $updateFields[] = "message = :message";
                $params[':message'] = trim($data['message']);
            }
            
            if (isset($data['target_role'])) {
                $validRoles = ['User', 'Repairer', 'Company', 'Moderator', 'All'];
                if (in_array($data['target_role'], $validRoles)) {
                    $updateFields[] = "target_role = :target_role";
                    $params[':target_role'] = $data['target_role'];
                }
            }
            
            if (isset($data['priority'])) {
                $validPriorities = ['low', 'medium', 'high'];
                if (in_array($data['priority'], $validPriorities)) {
                    $updateFields[] = "priority = :priority";
                    $params[':priority'] = $data['priority'];
                }
            }
            
            if (isset($data['status'])) {
                $validStatuses = ['active', 'inactive'];
                if (in_array($data['status'], $validStatuses)) {
                    $updateFields[] = "status = :status";
                    $params[':status'] = $data['status'];
                }
            }
            
            if (empty($updateFields)) {
                return false;
            }
            
            $sql = "UPDATE AdminAlert SET " . implode(', ', $updateFields) . " WHERE alert_id = :alert_id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Update Alert Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete an alert permanently
     * @param int $alertId Alert ID
     * @return bool True if successful, false otherwise
     */
    public function deleteAlert($alertId) {
        try {
            if (!is_numeric($alertId) || $alertId <= 0) {
                return false;
            }
            
            $stmt = $this->db->prepare("DELETE FROM AdminAlert WHERE alert_id = :alert_id");
            return $stmt->execute([':alert_id' => $alertId]);
        } catch (PDOException $e) {
            error_log("Delete Alert Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get alert statistics
     * @return array Statistics data
     */
    public function getAlertStatistics() {
        try {
            $stats = [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'by_role' => [],
                'by_priority' => []
            ];
            
            // Total and status counts
            $stmt = $this->db->query("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive
                FROM AdminAlert");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $stats['total'] = (int)$result['total'];
                $stats['active'] = (int)$result['active'];
                $stats['inactive'] = (int)$result['inactive'];
            }
            
            // By role
            $stmt = $this->db->query("SELECT target_role, COUNT(*) as count FROM AdminAlert GROUP BY target_role");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats['by_role'][$row['target_role']] = (int)$row['count'];
            }
            
            // By priority
            $stmt = $this->db->query("SELECT priority, COUNT(*) as count FROM AdminAlert GROUP BY priority");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats['by_priority'][$row['priority']] = (int)$row['count'];
            }
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Get Alert Statistics Error: " . $e->getMessage());
            return [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'by_role' => [],
                'by_priority' => []
            ];
        }
    }
    
    /**
     * Get recent alerts (last N alerts)
     * @param int $limit Number of alerts to retrieve
     * @return array List of recent alerts
     */
    public function getRecentAlerts($limit = 5) {
        try {
            $limit = (int)$limit;
            if ($limit <= 0) $limit = 5;
            
            $stmt = $this->db->prepare("SELECT * FROM AdminAlert ORDER BY created_at DESC LIMIT :limit");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Recent Alerts Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Toggle alert status (active <-> inactive)
     * @param int $alertId Alert ID
     * @return bool True if successful, false otherwise
     */
    public function toggleAlertStatus($alertId) {
        try {
            if (!is_numeric($alertId) || $alertId <= 0) {
                return false;
            }
            
            $stmt = $this->db->prepare("UPDATE AdminAlert SET status = IF(status = 'active', 'inactive', 'active') WHERE alert_id = :alert_id");
            return $stmt->execute([':alert_id' => $alertId]);
        } catch (PDOException $e) {
            error_log("Toggle Alert Status Error: " . $e->getMessage());
            return false;
        }
    }
}
?>