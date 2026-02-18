<?php
/**
 * AdvertisementModel.php
 * ✅ 3-STATUS SYSTEM: pending, approved, rejected
 * ✅ XAMPP CRASH-PROOF with proper error handling
 * Version: 2.0.0
 */

class AdvertisementModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * ✅ Check if moderator exists in database
     */
    public function moderatorExists($moderatorId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM Moderator WHERE moderator_id = ?");
            $stmt->bind_param("i", $moderatorId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return ($row['count'] > 0);
        } catch (Exception $e) {
            error_log("Moderator check failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get statistics (3 statuses only)
     */
    public function getStatistics()
    {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
        ];

        try {
            $total_result = $this->conn->query("SELECT COUNT(*) as total FROM Advertisement");
            if ($total_result) $stats['total'] = $total_result->fetch_assoc()['total'];

            $pending_result = $this->conn->query("SELECT COUNT(*) as total FROM Advertisement WHERE status = 'pending'");
            if ($pending_result) $stats['pending'] = $pending_result->fetch_assoc()['total'];

            $approved_result = $this->conn->query("SELECT COUNT(*) as total FROM Advertisement WHERE status = 'approved'");
            if ($approved_result) $stats['approved'] = $approved_result->fetch_assoc()['total'];

            $rejected_result = $this->conn->query("SELECT COUNT(*) as total FROM Advertisement WHERE status = 'rejected'");
            if ($rejected_result) $stats['rejected'] = $rejected_result->fetch_assoc()['total'];
        } catch (Exception $e) {
            error_log("Statistics query failed: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get advertisements with filters
     */
    public function getAdvertisements($filters = [])
    {
        $sql = "SELECT 
                    a.ad_id,
                    a.provider_id,
                    a.provider_type,
                    a.title,
                    a.type,
                    a.budget,
                    a.status,
                    a.submission_date,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown'
                    END as company_name
                FROM Advertisement a
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                WHERE 1=1";

        $params = array();
        $types = "";

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        if (!empty($filters['type'])) {
            $sql .= " AND a.type = ?";
            $params[] = $filters['type'];
            $types .= "s";
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (a.title LIKE ? OR c.name LIKE ? OR CONCAT(r.f_name, ' ', r.l_name) LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }

        $sql .= " ORDER BY a.submission_date DESC LIMIT 100";

        try {
            if (!empty($params)) {
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $this->conn->query($sql);
            }

            $ads = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $ads[] = $row;
                }
            }
            return $ads;
        } catch (Exception $e) {
            error_log("Get advertisements failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ SIMPLIFIED: 3-status lifecycle validation
     * 
     * LIFECYCLE FLOW:
     * Pending → Approved ✅
     * Pending → Rejected ✅ (FINAL, immutable)
     * 
     * BLOCKED TRANSITIONS:
     * - Rejected → Anything ❌ (FINAL)
     * - Approved → Rejected ❌ (cannot reject after approval)
     * - Approved → Pending ❌ (cannot go backward)
     */
    public function isValidStatusTransition($currentStatus, $newStatus)
    {
        $currentStatus = strtolower($currentStatus);
        $newStatus = strtolower($newStatus);
        
        // ❌ RULE 1: Rejected is FINAL (immutable)
        if ($currentStatus === 'rejected') {
            return false;
        }

        // ✅ RULE 2: Define strict allowed transitions
        $allowedTransitions = [
            'pending' => ['approved', 'rejected'],  // Pending can go to approved or rejected
            'approved' => []                        // Approved is final (cannot change)
        ];

        if (!isset($allowedTransitions[$currentStatus])) {
            return false;
        }

        return in_array($newStatus, $allowedTransitions[$currentStatus]);
    }

    /**
     * ✅ SIMPLIFIED: Update advertisement status
     */
    public function updateStatus($ad_id, $newStatus, $moderatorId)
    {
        // Validate moderator exists
        if (!$this->moderatorExists($moderatorId)) {
            return [
                'success' => false,
                'message' => '❌ CRITICAL ERROR: Moderator ID ' . $moderatorId . ' does not exist. Please run create_database.sql.'
            ];
        }

        // Get current advertisement
        $ad = $this->getAdvertisementById($ad_id);
        
        if (!$ad) {
            return [
                'success' => false,
                'message' => '❌ Advertisement not found.'
            ];
        }

        $currentStatus = strtolower($ad['status']);
        $newStatus = strtolower($newStatus);

        // Validate transition
        if (!$this->isValidStatusTransition($currentStatus, $newStatus)) {
            return [
                'success' => false,
                'message' => "🚫 Cannot change status from '{$currentStatus}' to '{$newStatus}'. " .
                            ($currentStatus === 'rejected' ? "Rejected ads are FINAL." : 
                            ($currentStatus === 'approved' ? "Approved ads cannot be changed." : "Invalid transition."))
            ];
        }

        // Update with transaction
        try {
            $this->conn->begin_transaction();

            // Update advertisement status
            $stmt = $this->conn->prepare("UPDATE Advertisement SET status = ? WHERE ad_id = ?");
            $stmt->bind_param("si", $newStatus, $ad_id);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception("No rows updated. Advertisement may have been modified by another user.");
            }

            // Log moderator activity
            $activity_type = ($newStatus === 'approved') ? 'ad_approved' : 'ad_rejected';
            $description = "Advertisement '{$ad['title']}' was " . ($newStatus === 'approved' ? 'approved' : 'rejected');

            $log_stmt = $this->conn->prepare(
                "INSERT INTO moderator_activity 
                (moderator_id, activity_type, target_id, target_title, description) 
                VALUES (?, ?, ?, ?, ?)"
            );
            $log_stmt->bind_param("isiss", $moderatorId, $activity_type, $ad_id, $ad['title'], $description);
            $log_stmt->execute();

            $this->conn->commit();

            return [
                'success' => true,
                'message' => "✅ Advertisement " . ($newStatus === 'approved' ? 'approved' : 'rejected') . " successfully!"
            ];

        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Status update failed: " . $e->getMessage());
            return [
                'success' => false,
                'message' => '❌ Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get single advertisement by ID
     */
    public function getAdvertisementById($ad_id)
    {
        $sql = "SELECT 
                    a.*,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown'
                    END as company_name
                FROM Advertisement a
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                WHERE a.ad_id = ?
                LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $ad_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $ad = $result->fetch_assoc();
            $stmt->close();
            
            return $ad;
        } catch (Exception $e) {
            error_log("Get advertisement by ID failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if table exists
     */
    public function tableExists()
    {
        try {
            $table_check = $this->conn->query("SHOW TABLES LIKE 'Advertisement'");
            return $table_check->num_rows > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}