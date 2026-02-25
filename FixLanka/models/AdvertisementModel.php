<?php
/**
 * AdvertisementModel.php
 * ✅ FIXED - Column name corrections + Activity Logging
 * Version: 3.1.0
 */

class AdvertisementModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function moderatorExists($moderatorId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM moderator WHERE moderator_id = :moderator_id");
            $stmt->execute(['moderator_id' => $moderatorId]);
            $row = $stmt->fetch();
            return ($row['count'] > 0);
        } catch (PDOException $e) {
            error_log("Moderator check failed: " . $e->getMessage());
            return false;
        }
    }

    public function getStatistics()
    {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
        ];

        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM advertisement");
            $stats['total'] = $stmt->fetch()['total'];

            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM advertisement WHERE status = 'pending'");
            $stats['pending'] = $stmt->fetch()['total'];

            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM advertisement WHERE status = 'approved'");
            $stats['approved'] = $stmt->fetch()['total'];

            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM advertisement WHERE status = 'rejected'");
            $stats['rejected'] = $stmt->fetch()['total'];

        } catch (PDOException $e) {
            error_log("Statistics query failed: " . $e->getMessage());
        }

        return $stats;
    }

    public function getAdvertisements($filters = [])
    {
        // SIMPLIFIED QUERY - No JOINs for now
        $sql = "SELECT * FROM advertisement
                WHERE 1=1";

        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['type'])) {
            $sql .= " AND type = :type";
            $params['type'] = $filters['type'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND title LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY submission_date DESC LIMIT 100";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            
            // Add derived fields (safe access for optional columns)
            foreach ($results as &$ad) {
                $ad['provider_name'] = $this->getProviderName($ad['provider_id'], $ad['provider_type']);
                $ad['moderator_name'] = $this->getModeratorName($ad['reviewed_by'] ?? null);
                $ad['moderator_email'] = $this->getModeratorEmail($ad['reviewed_by'] ?? null);
                $ad['category_name'] = $this->getCategoryName($ad['category_id'] ?? null);
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log("Get advertisements failed: " . $e->getMessage());
            return [];
        }
    }

    private function getProviderName($providerId, $providerType)
    {
        if (!$providerId) return 'Unknown';
        
        try {
            if ($providerType === 'company') {
                $stmt = $this->pdo->prepare("SELECT name FROM company WHERE company_id = :id");
            } else {
                $stmt = $this->pdo->prepare("SELECT f_name FROM repairer WHERE repairer_id = :id");
            }
            $stmt->execute(['id' => $providerId]);
            $row = $stmt->fetch();
            return $row ? ($row['name'] ?? $row['f_name'] ?? 'Unknown') : 'Unknown';
        } catch (PDOException $e) {
            return 'Unknown';
        }
    }

    private function getModeratorName($moderatorId)
    {
        if (!$moderatorId) return null;
        
        try {
            $stmt = $this->pdo->prepare("SELECT username FROM moderator WHERE moderator_id = :id");
            $stmt->execute(['id' => $moderatorId]);
            $row = $stmt->fetch();
            return $row ? $row['username'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    private function getModeratorEmail($moderatorId)
    {
        if (!$moderatorId) return null;
        
        try {
            $stmt = $this->pdo->prepare("SELECT email FROM moderator WHERE moderator_id = :id");
            $stmt->execute(['id' => $moderatorId]);
            $row = $stmt->fetch();
            return $row ? $row['email'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    private function getCategoryName($categoryId)
    {
        if (!$categoryId) return null;
        
        try {
            $stmt = $this->pdo->prepare("SELECT name FROM category WHERE category_id = :id");
            $stmt->execute(['id' => $categoryId]);
            $row = $stmt->fetch();
            return $row ? $row['name'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getAdvertisementById($ad_id)
    {
        $sql = "SELECT * FROM advertisement WHERE ad_id = :ad_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['ad_id' => $ad_id]);
            $ad = $stmt->fetch();
            
            if ($ad) {
                $ad['provider_name'] = $this->getProviderName($ad['provider_id'], $ad['provider_type']);
                $ad['moderator_name'] = $this->getModeratorName($ad['reviewed_by'] ?? null);
                $ad['moderator_email'] = $this->getModeratorEmail($ad['reviewed_by'] ?? null);
                $ad['category_name'] = $this->getCategoryName($ad['category_id'] ?? null);
            }
            
            return $ad ?: null;
        } catch (PDOException $e) {
            error_log("Get advertisement by ID failed: " . $e->getMessage());
            return null;
        }
    }

    public function isValidStatusTransition($currentStatus, $newStatus)
    {
        $currentStatus = strtolower($currentStatus);
        $newStatus = strtolower($newStatus);
        
        if ($currentStatus === 'rejected') {
            return false;
        }

        $allowedTransitions = [
            'pending' => ['approved', 'rejected'],
            'approved' => []
        ];

        if (!isset($allowedTransitions[$currentStatus])) {
            return false;
        }

        return in_array($newStatus, $allowedTransitions[$currentStatus]);
    }

    public function updateStatus($ad_id, $newStatus, $moderatorId, $notes = '')
    {
        if (!$this->moderatorExists($moderatorId)) {
            return [
                'success' => false,
                'message' => "❌ ERROR: Moderator ID {$moderatorId} does not exist in database."
            ];
        }

        $ad = $this->getAdvertisementById($ad_id);
        
        if (!$ad) {
            return [
                'success' => false,
                'message' => "❌ Advertisement #{$ad_id} not found."
            ];
        }

        $currentStatus = strtolower($ad['status']);
        $newStatus = strtolower($newStatus);

        if (!$this->isValidStatusTransition($currentStatus, $newStatus)) {
            return [
                'success' => false,
                'message' => "❌ Invalid transition: Cannot change from '{$currentStatus}' to '{$newStatus}'."
            ];
        }

        try {
            $this->pdo->beginTransaction();

            // ✅ Update advertisement table
            $stmt = $this->pdo->prepare("
                UPDATE advertisement 
                SET status = :status,
                    reviewed_by = :moderator_id,
                    reviewed_at = NOW(),
                    moderator_notes = :notes
                WHERE ad_id = :ad_id
            ");

            $stmt->execute([
                'status' => $newStatus,
                'moderator_id' => $moderatorId,
                'notes' => $notes,
                'ad_id' => $ad_id
            ]);

            // ✅ Insert into ad_status_history with CORRECT column names
            $stmt = $this->pdo->prepare("
                INSERT INTO ad_status_history 
                (ad_id, old_status, new_status, changed_by_role, changed_by_id, reason, is_override)
                VALUES (:ad_id, :old_status, :new_status, 'moderator', :moderator_id, :reason, 0)
            ");

            $stmt->execute([
                'ad_id' => $ad_id,
                'old_status' => $currentStatus,
                'new_status' => $newStatus,
                'moderator_id' => $moderatorId,
                'reason' => $notes
            ]);

            // ✅ NEW: Log activity to system_activity_logs
            require_once __DIR__ . '/ActivityLogModel.php';
            $activityLog = new ActivityLogModel($this->pdo);
            
            $activityType = ($newStatus === 'approved') ? 'ad_approved' : 'ad_rejected';
            $description = "Moderator #{$moderatorId} {$newStatus} advertisement: {$ad['title']}";
            
            $activityLog->logActivity(
                $moderatorId,
                'moderator',
                $activityType,
                $description,
                $ad_id
            );

            $this->pdo->commit();

            $actionVerb = ($newStatus === 'approved') ? 'approved' : 'rejected';
            return [
                'success' => true,
                'message' => "✅ Advertisement #{$ad_id} has been {$actionVerb}."
            ];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Update status failed: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "❌ Database error: " . $e->getMessage()
            ];
        }
    }

    public function tableExists()
    {
        try {
            $stmt = $this->pdo->query("SHOW TABLES LIKE 'advertisement'");
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}