<?php
/**
 * AdminAdvertisementModel.php
 * ✅ COMPLETE STATUS SYSTEM: pending, approved, rejected, scheduled, active, paused, inactive, suspended
 * ✅ XAMPP CRASH-PROOF with full error handling
 * ✅ Admin Override Capability with History Tracking
 * Version: 1.0.0
 */

class AdminAdvertisementModel
{
    private $conn;
    private $lastError = '';

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * ✅ Get last error message
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * ✅ Check if admin exists in database
     */
    public function adminExists($username)
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM Admin WHERE username = ?");
            if (!$stmt) {
                $this->lastError = "Prepare failed: " . $this->conn->error;
                return false;
            }
            
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return ($row['count'] > 0);
        } catch (Exception $e) {
            $this->lastError = "Admin check failed: " . $e->getMessage();
            error_log($this->lastError);
            return false;
        }
    }

    /**
     * ✅ Check if moderator exists in database
     */
    public function moderatorExists($moderatorId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM Moderator WHERE moderator_id = ?");
            if (!$stmt) {
                $this->lastError = "Prepare failed: " . $this->conn->error;
                return false;
            }
            
            $stmt->bind_param("i", $moderatorId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return ($row['count'] > 0);
        } catch (Exception $e) {
            $this->lastError = "Moderator check failed: " . $e->getMessage();
            error_log($this->lastError);
            return false;
        }
    }

    /**
     * ✅ Get comprehensive statistics (all statuses)
     */
    public function getStatistics()
    {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'scheduled' => 0,
            'active' => 0,
            'paused' => 0,
            'inactive' => 0,
            'suspended' => 0
        ];

        try {
            // Use single query for better performance
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                        SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled,
                        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN status = 'paused' THEN 1 ELSE 0 END) as paused,
                        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive,
                        SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) as suspended
                    FROM Advertisement";
            
            $result = $this->conn->query($sql);
            
            if ($result && $row = $result->fetch_assoc()) {
                $stats = array_map('intval', $row);
            }
        } catch (Exception $e) {
            $this->lastError = "Statistics query failed: " . $e->getMessage();
            error_log($this->lastError);
        }

        return $stats;
    }

    /**
     * ✅ Get advertisements with filters, pagination, and moderator info
     */
    public function getAdvertisements($filters = [], $page = 1, $perPage = 50)
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT 
                    a.ad_id,
                    a.provider_id,
                    a.provider_type,
                    a.title,
                    a.description,
                    a.type,
                    a.budget,
                    a.status,
                    a.submission_date,
                    a.reviewed_by,
                    a.reviewed_at,
                    a.moderator_notes,
                    a.admin_notes,
                    a.admin_reviewed_by,
                    a.admin_reviewed_at,
                    a.override_reason,
                    a.image_url,
                    a.clicks,
                    a.impressions,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown'
                    END as company_name,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.email
                        WHEN a.provider_type = 'repairer' THEN r.email
                        ELSE NULL
                    END as provider_email,
                    CONCAT(m.f_name, ' ', m.l_name) as moderator_name,
                    m.email as moderator_email,
                    adm.username as admin_username
                FROM Advertisement a
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                LEFT JOIN Moderator m ON a.reviewed_by = m.moderator_id
                LEFT JOIN Admin adm ON a.admin_reviewed_by = adm.username
                WHERE 1=1";

        $params = array();
        $types = "";

        // Status filter
        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        // Type filter
        if (!empty($filters['type'])) {
            $sql .= " AND a.type = ?";
            $params[] = $filters['type'];
            $types .= "s";
        }

        // Search filter
        if (!empty($filters['search'])) {
            $sql .= " AND (a.title LIKE ? OR c.name LIKE ? OR CONCAT(r.f_name, ' ', r.l_name) LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }

        // Date filter
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(a.submission_date) >= ?";
            $params[] = $filters['date_from'];
            $types .= "s";
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(a.submission_date) <= ?";
            $params[] = $filters['date_to'];
            $types .= "s";
        }

        // Moderator filter
        if (!empty($filters['moderator'])) {
            $sql .= " AND a.reviewed_by = ?";
            $params[] = $filters['moderator'];
            $types .= "i";
        }

        // Sorting
        $sortOrder = $filters['sort'] ?? 'newest';
        switch ($sortOrder) {
            case 'oldest':
                $sql .= " ORDER BY a.submission_date ASC";
                break;
            case 'budget_high':
                $sql .= " ORDER BY a.budget DESC";
                break;
            case 'budget_low':
                $sql .= " ORDER BY a.budget ASC";
                break;
            default:
                $sql .= " ORDER BY a.submission_date DESC";
        }

        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        $types .= "ii";

        try {
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                $this->lastError = "Prepare failed: " . $this->conn->error;
                error_log($this->lastError);
                return [];
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();

            $ads = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $ads[] = $row;
                }
            }
            
            $stmt->close();
            return $ads;
            
        } catch (Exception $e) {
            $this->lastError = "Get advertisements failed: " . $e->getMessage();
            error_log($this->lastError);
            return [];
        }
    }

    /**
     * ✅ Get total count for pagination
     */
    public function getTotalCount($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM Advertisement a WHERE 1=1";
        
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
            $sql .= " AND (a.title LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $types .= "s";
        }

        try {
            if (!empty($params)) {
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $result = $this->conn->query($sql);
            }

            if ($result && $row = $result->fetch_assoc()) {
                return (int)$row['total'];
            }
            
            return 0;
        } catch (Exception $e) {
            $this->lastError = "Count query failed: " . $e->getMessage();
            error_log($this->lastError);
            return 0;
        }
    }

    /**
     * ✅ ADMIN STATUS TRANSITION RULES (More permissive than moderator)
     * 
     * STANDARD FLOW:
     * pending → approved/rejected (Moderator)
     * approved → scheduled/active (Admin)
     * active → paused/inactive/suspended (Admin)
     * 
     * ADMIN OVERRIDE ALLOWED:
     * rejected → approved (with override flag)
     * Any status → suspended (emergency action)
     */
    public function isValidStatusTransition($currentStatus, $newStatus, $isOverride = false)
    {
        $currentStatus = strtolower($currentStatus);
        $newStatus = strtolower($newStatus);
        
        // Same status = no change needed
        if ($currentStatus === $newStatus) {
            return false;
        }

        // ✅ ADMIN OVERRIDE: Allow rejected → other statuses with override flag
        if ($isOverride) {
            $overrideAllowed = [
                'rejected' => ['pending', 'approved'],
                'suspended' => ['pending', 'approved', 'active', 'inactive']
            ];
            
            if (isset($overrideAllowed[$currentStatus]) && 
                in_array($newStatus, $overrideAllowed[$currentStatus])) {
                return true;
            }
        }

        // ✅ STANDARD TRANSITIONS (No override needed)
        $allowedTransitions = [
            'pending' => ['approved', 'rejected'],
            'approved' => ['scheduled', 'active', 'rejected'],
            'rejected' => [],  // Immutable without override
            'scheduled' => ['active', 'inactive'],
            'active' => ['paused', 'inactive', 'suspended'],
            'paused' => ['active', 'inactive'],
            'inactive' => ['approved', 'active'],
            'suspended' => []  // Requires override to change
        ];

        if (!isset($allowedTransitions[$currentStatus])) {
            return false;
        }

        // ✅ EMERGENCY: Any status can be suspended by admin
        if ($newStatus === 'suspended') {
            return true;
        }

        return in_array($newStatus, $allowedTransitions[$currentStatus]);
    }

    /**
     * ✅ Update advertisement status with full tracking
     */
    public function updateStatus($ad_id, $newStatus, $adminUsername, $reason = '', $isOverride = false)
    {
        // Validate admin exists
        if (!$this->adminExists($adminUsername)) {
            return [
                'success' => false,
                'message' => '❌ CRITICAL ERROR: Admin username "' . htmlspecialchars($adminUsername) . '" does not exist.'
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
        if (!$this->isValidStatusTransition($currentStatus, $newStatus, $isOverride)) {
            $message = "🚫 Cannot change status from '{$currentStatus}' to '{$newStatus}'.";
            
            if ($currentStatus === 'rejected' && !$isOverride) {
                $message .= " Rejected ads require admin override to change.";
            } elseif ($currentStatus === 'suspended' && !$isOverride) {
                $message .= " Suspended ads require admin override to restore.";
            } else {
                $message .= " Invalid transition.";
            }
            
            return [
                'success' => false,
                'message' => $message
            ];
        }

        // Update with transaction
        try {
            $this->conn->begin_transaction();

            // Update advertisement status
            $updateSql = "UPDATE Advertisement 
                         SET status = ?, 
                             admin_reviewed_by = ?, 
                             admin_reviewed_at = NOW(),
                             admin_notes = CASE 
                                 WHEN admin_notes IS NULL THEN ?
                                 ELSE CONCAT(admin_notes, '\n--- ', NOW(), ' ---\n', ?)
                             END" .
                         ($isOverride ? ", override_reason = ?" : "") . "
                         WHERE ad_id = ?";
            
            $stmt = $this->conn->prepare($updateSql);
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            if ($isOverride) {
                $stmt->bind_param("sssssi", $newStatus, $adminUsername, $reason, $reason, $reason, $ad_id);
            } else {
                $stmt->bind_param("ssssi", $newStatus, $adminUsername, $reason, $reason, $ad_id);
            }
            
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception("No rows updated. Advertisement may have been modified.");
            }

            // Log status history
            $this->logStatusHistory($ad_id, $currentStatus, $newStatus, 'admin', $adminUsername, $reason, $isOverride);

            $this->conn->commit();

            $actionVerb = $this->getStatusActionVerb($newStatus);
            return [
                'success' => true,
                'message' => "✅ Advertisement {$actionVerb} successfully!" . ($isOverride ? " (Admin Override)" : "")
            ];

        } catch (Exception $e) {
            $this->conn->rollback();
            $this->lastError = "Status update failed: " . $e->getMessage();
            error_log($this->lastError);
            return [
                'success' => false,
                'message' => '❌ Database error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ✅ Log status change to history table
     */
    private function logStatusHistory($ad_id, $oldStatus, $newStatus, $role, $userId, $reason, $isOverride)
    {
        try {
            $sql = "INSERT INTO ad_status_history 
                    (ad_id, old_status, new_status, changed_by_role, changed_by_id, reason, is_override, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("History log prepare failed: " . $this->conn->error);
                return false;
            }

            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            $overrideFlag = $isOverride ? 1 : 0;
            
            $stmt->bind_param("isssssiss", 
                $ad_id, 
                $oldStatus, 
                $newStatus, 
                $role, 
                $userId, 
                $reason, 
                $overrideFlag, 
                $ipAddress, 
                $userAgent
            );
            
            $stmt->execute();
            $stmt->close();
            
            return true;
        } catch (Exception $e) {
            error_log("History logging failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ Get status action verb for messages
     */
    private function getStatusActionVerb($status)
    {
        $verbs = [
            'approved' => 'approved',
            'rejected' => 'rejected',
            'scheduled' => 'scheduled',
            'active' => 'activated',
            'paused' => 'paused',
            'inactive' => 'deactivated',
            'suspended' => 'suspended'
        ];
        
        return $verbs[strtolower($status)] ?? 'updated';
    }

    /**
     * ✅ Get single advertisement by ID with full details
     */
    public function getAdvertisementById($ad_id)
    {
        $sql = "SELECT 
                    a.*,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown'
                    END as company_name,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.email
                        WHEN a.provider_type = 'repairer' THEN r.email
                        ELSE NULL
                    END as provider_email,
                    CONCAT(m.f_name, ' ', m.l_name) as moderator_name,
                    m.email as moderator_email,
                    adm.username as admin_username,
                    adm.email as admin_email
                FROM Advertisement a
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                LEFT JOIN Moderator m ON a.reviewed_by = m.moderator_id
                LEFT JOIN Admin adm ON a.admin_reviewed_by = adm.username
                WHERE a.ad_id = ?
                LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                $this->lastError = "Prepare failed: " . $this->conn->error;
                return null;
            }
            
            $stmt->bind_param("i", $ad_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $ad = $result->fetch_assoc();
            $stmt->close();
            
            return $ad;
        } catch (Exception $e) {
            $this->lastError = "Get advertisement by ID failed: " . $e->getMessage();
            error_log($this->lastError);
            return null;
        }
    }

    /**
     * ✅ Get status history for an advertisement
     */
    public function getStatusHistory($ad_id)
    {
        $sql = "SELECT 
                    h.*,
                    CASE 
                        WHEN h.changed_by_role = 'admin' THEN ad.username
                        WHEN h.changed_by_role = 'moderator' THEN CONCAT(m.f_name, ' ', m.l_name)
                        ELSE 'System'
                    END as changer_name
                FROM ad_status_history h
                LEFT JOIN Admin ad ON h.changed_by_role = 'admin' AND h.changed_by_id = ad.username
                LEFT JOIN Moderator m ON h.changed_by_role = 'moderator' AND h.changed_by_id = m.moderator_id
                WHERE h.ad_id = ?
                ORDER BY h.changed_at DESC
                LIMIT 100";
        
        try {
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                $this->lastError = "Prepare failed: " . $this->conn->error;
                return [];
            }
            
            $stmt->bind_param("i", $ad_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $history = [];
            while ($row = $result->fetch_assoc()) {
                $history[] = $row;
            }
            
            $stmt->close();
            return $history;
        } catch (Exception $e) {
            $this->lastError = "Get history failed: " . $e->getMessage();
            error_log($this->lastError);
            return [];
        }
    }

    /**
     * ✅ Get list of all moderators for filter dropdown
     */
    public function getAllModerators()
    {
        try {
            $sql = "SELECT moderator_id, CONCAT(f_name, ' ', l_name) as name, email 
                    FROM Moderator 
                    ORDER BY f_name ASC";
            
            $result = $this->conn->query($sql);
            
            $moderators = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $moderators[] = $row;
                }
            }
            
            return $moderators;
        } catch (Exception $e) {
            error_log("Get moderators failed: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ Check if table exists
     */
    public function tableExists()
    {
        try {
            $table_check = $this->conn->query("SHOW TABLES LIKE 'Advertisement'");
            return $table_check && $table_check->num_rows > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * ✅ Check if history table exists
     */
    public function historyTableExists()
    {
        try {
            $table_check = $this->conn->query("SHOW TABLES LIKE 'ad_status_history'");
            return $table_check && $table_check->num_rows > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}