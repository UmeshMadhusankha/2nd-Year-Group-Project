<?php
/**
 * AdminAdvertisementModel.php
 * ✅ FIXED - Column name corrections + Activity Logging
 * Version: 3.1.0
 */

class AdminAdvertisementModel
{
    private $pdo;
    private $lastError = '';

    private function tableExistsByName(string $table): bool
    {
        try {
            $stmt = $this->pdo->prepare("SHOW TABLES LIKE :t");
            $stmt->execute([':t' => $table]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Best-effort sync for time-based statuses.
     * Keeps DB status aligned with campaign dates + schedule existence:
     * scheduled -> active on/after start_date, and -> expired after end_date.
     */
    private function syncTimeBasedStatuses(): void
    {
        if (!$this->tableExists()) {
            return;
        }

        try {
            // Expire ads after the campaign end date (regardless of scheduling).
            $this->pdo->exec(
                "UPDATE advertisement\n"
                . "SET status = 'expired'\n"
                . "WHERE end_date < CURDATE()\n"
                . "  AND status IN ('approved','scheduled','active')"
            );
        } catch (PDOException $e) {
            error_log('Time-based advertisement status sync failed: ' . $e->getMessage());
        }
    }

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function adminExists($username)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM admin WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $row = $stmt->fetch();
            return ($row['count'] > 0);
        } catch (PDOException $e) {
            $this->lastError = "Admin check failed: " . $e->getMessage();
            error_log($this->lastError);
            return false;
        }
    }

    public function getAdminStatistics()
    {
        $this->syncTimeBasedStatuses();

        $stats = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
            'scheduled' => 0,
            'active' => 0,
            'paused' => 0,
            'inactive' => 0,
            'suspended' => 0,
            'expired' => 0
        ];

        try {
            $hasSchedule = $this->tableExistsByName('adschedule');

            if ($hasSchedule) {
                $sql = "SELECT computed_status, COUNT(*) AS count\n"
                    . "FROM (\n"
                    . "  SELECT\n"
                    . "    a.ad_id,\n"
                    . "    (CASE\n"
                    . "      WHEN a.end_date < CURDATE() THEN 'expired'\n"
                    . "      WHEN LOWER(a.status) IN ('rejected') THEN 'rejected'\n"
                    . "      WHEN LOWER(a.status) IN ('pending') THEN 'pending'\n"
                    . "      WHEN LOWER(a.status) IN ('suspended') THEN 'suspended'\n"
                    . "      WHEN LOWER(a.status) IN ('paused') THEN 'paused'\n"
                    . "      WHEN LOWER(a.status) IN ('inactive') THEN 'inactive'\n"
                    . "      WHEN s.schedule_id IS NULL THEN LOWER(a.status)\n"
                    . "      ELSE\n"
                    . "        (CASE\n"
                    . "          WHEN CURDATE() < s.start_date THEN 'scheduled'\n"
                    . "          WHEN CURDATE() > s.end_date THEN 'expired'\n"
                    . "          WHEN CURTIME() BETWEEN COALESCE(s.start_time,'00:00:00') AND COALESCE(s.end_time,'23:59:59') THEN 'active'\n"
                    . "          ELSE 'scheduled'\n"
                    . "        END)\n"
                    . "    END) AS computed_status\n"
                    . "  FROM advertisement a\n"
                    . "  LEFT JOIN adschedule s\n"
                    . "    ON s.schedule_id = (\n"
                    . "      SELECT s2.schedule_id\n"
                    . "      FROM adschedule s2\n"
                    . "      WHERE s2.ad_id = a.ad_id\n"
                    . "      ORDER BY s2.schedule_id DESC\n"
                    . "      LIMIT 1\n"
                    . "    )\n"
                    . ") t\n"
                    . "GROUP BY computed_status";

                $stmt = $this->pdo->query($sql);
                while ($row = $stmt->fetch()) {
                    $status = strtolower((string)($row['computed_status'] ?? ''));
                    if ($status !== '' && isset($stats[$status])) {
                        $stats[$status] = (int)($row['count'] ?? 0);
                    }
                }
            } else {
                // Fallback: count by stored status.
                $stmt = $this->pdo->query("SELECT status, COUNT(*) as count FROM advertisement GROUP BY status");
                while ($row = $stmt->fetch()) {
                    $status = strtolower($row['status']);
                    if (isset($stats[$status])) {
                        $stats[$status] = $row['count'];
                    }
                }
            }

            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM advertisement");
            $stats['total'] = (int)($stmt->fetch()['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Admin statistics failed: " . $e->getMessage());
        }

        return $stats;
    }

    public function getAdvertisements($filters = [], $limit = 200)
    {
        $this->syncTimeBasedStatuses();

        $hasSchedule = $this->tableExistsByName('adschedule');
        $computedStatusFilter = '';

        if (!empty($filters['status']) && in_array($filters['status'], ['active', 'scheduled', 'expired'], true)) {
            $computedStatusFilter = $filters['status'];
        }

        if ($hasSchedule) {
            $sql = "SELECT 
                        a.ad_id,
                        a.title,
                        a.description,
                        a.provider_id,
                        a.provider_type,
                        a.type,
                        a.budget,
                        a.status,
                        a.submission_date,
                        a.reviewed_by,
                        a.reviewed_at,
                        a.moderator_notes,
                        a.admin_reviewed_by,
                        a.admin_reviewed_at,
                        a.admin_notes,
                        a.override_reason,
                        a.is_override,
                        a.contact_email,
                        a.contact_phone,
                        a.image_url,
                        a.category_id,
                        a.target_audience,
                        a.start_date,
                        a.end_date,
                        a.clicks,
                        a.impressions,
                        (CASE
                            WHEN a.end_date < CURDATE() THEN 'expired'
                            WHEN a.status IN ('paused','inactive','suspended','rejected') THEN a.status
                            WHEN s.schedule_id IS NULL THEN a.status
                            WHEN CURDATE() < s.start_date THEN 'scheduled'
                            WHEN CURDATE() > s.end_date THEN 'expired'
                            WHEN CURTIME() BETWEEN COALESCE(s.start_time,'00:00:00') AND COALESCE(s.end_time,'23:59:59') THEN 'active'
                            ELSE 'scheduled'
                        END) AS computed_status
                    FROM advertisement a
                    LEFT JOIN adschedule s
                      ON s.schedule_id = (
                        SELECT s2.schedule_id
                        FROM adschedule s2
                        WHERE s2.ad_id = a.ad_id
                        ORDER BY s2.schedule_id DESC
                        LIMIT 1
                      )
                    WHERE 1=1";
        } else {
            $sql = "SELECT 
                        ad_id,
                        title,
                        description,
                        provider_id,
                        provider_type,
                        type,
                        budget,
                        status,
                        submission_date,
                        reviewed_by,
                        reviewed_at,
                        moderator_notes,
                        admin_reviewed_by,
                        admin_reviewed_at,
                        admin_notes,
                        override_reason,
                        is_override,
                        contact_email,
                        contact_phone,
                        image_url,
                        category_id,
                        target_audience,
                        start_date,
                        end_date,
                        clicks,
                        impressions,
                        status AS computed_status
                    FROM advertisement 
                    WHERE 1=1";
        }

        $params = [];

        if (!empty($filters['status']) && $computedStatusFilter === '') {
            $status = strtolower(trim((string)$filters['status']));
            if ($status === 'approved') {
                // "Approved" in the UI means "reviewed/accepted" and includes
                // lifecycle states that come after approval (scheduled/active/etc.).
                $sql .= " AND LOWER(status) IN ('approved','scheduled','active','paused','inactive','suspended','expired')";
            } else {
                $sql .= " AND LOWER(status) = :status";
                $params['status'] = $status;
            }
        }

        if (!empty($filters['type'])) {
            $sql .= " AND type = :type";
            $params['type'] = $filters['type'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND title LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['moderator'])) {
            $sql .= " AND reviewed_by = :moderator";
            $params['moderator'] = $filters['moderator'];
        }

        $sql .= " ORDER BY submission_date DESC LIMIT :limit";
        $params['limit'] = $limit;

        try {
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($params as $key => $value) {
                if ($key === 'limit') {
                    $stmt->bindValue(":$key", $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue(":$key", $value);
                }
            }
            
            $stmt->execute();
            $results = $stmt->fetchAll();

            if ($computedStatusFilter !== '') {
                $results = array_values(array_filter($results, function ($row) use ($computedStatusFilter) {
                    return strtolower((string)($row['computed_status'] ?? '')) === $computedStatusFilter;
                }));
            }
            
            foreach ($results as &$ad) {
                $ad['provider_name'] = $this->getProviderName($ad['provider_id'], $ad['provider_type']);
                $ad['moderator_name'] = $this->getModeratorName($ad['reviewed_by']);
                $ad['moderator_email'] = $this->getModeratorEmail($ad['reviewed_by']);
                $ad['category_name'] = $this->getCategoryName($ad['category_id']);
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
            $table = ($providerType === 'company') ? 'company' : 'repairer';
            $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE " . ($providerType === 'company' ? 'company_id' : 'repairer_id') . " = :id");
            $stmt->execute(['id' => $providerId]);
            $row = $stmt->fetch();
            return $row ? ($row['name'] ?? $row['first_name'] ?? 'Unknown') : 'Unknown';
        } catch (PDOException $e) {
            error_log("Get provider name failed: " . $e->getMessage());
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
            error_log("Get moderator name failed: " . $e->getMessage());
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
            error_log("Get moderator email failed: " . $e->getMessage());
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
            error_log("Get category name failed: " . $e->getMessage());
            return null;
        }
    }

    public function getAdvertisementById($ad_id)
    {
        $this->syncTimeBasedStatuses();

                if ($this->tableExistsByName('adschedule')) {
                        $sql = "SELECT
                                                a.*,
                                                (CASE
                                                        WHEN a.end_date < CURDATE() THEN 'expired'
                                                        WHEN a.status IN ('paused','inactive','suspended','rejected') THEN a.status
                                                        WHEN s.schedule_id IS NULL THEN a.status
                                                        WHEN CURDATE() < s.start_date THEN 'scheduled'
                                                        WHEN CURDATE() > s.end_date THEN 'expired'
                                                        WHEN CURTIME() BETWEEN COALESCE(s.start_time,'00:00:00') AND COALESCE(s.end_time,'23:59:59') THEN 'active'
                                                        ELSE 'scheduled'
                                                END) AS computed_status
                                        FROM advertisement a
                                        LEFT JOIN adschedule s
                                            ON s.schedule_id = (
                                                SELECT s2.schedule_id
                                                FROM adschedule s2
                                                WHERE s2.ad_id = a.ad_id
                                                ORDER BY s2.schedule_id DESC
                                                LIMIT 1
                                            )
                                        WHERE a.ad_id = :ad_id";
                } else {
                        $sql = "SELECT *, status AS computed_status FROM advertisement WHERE ad_id = :ad_id";
                }

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['ad_id' => $ad_id]);
            $ad = $stmt->fetch();
            
            if ($ad) {
                $ad['provider_name'] = $this->getProviderName($ad['provider_id'], $ad['provider_type']);
                $ad['moderator_name'] = $this->getModeratorName($ad['reviewed_by']);
                $ad['moderator_email'] = $this->getModeratorEmail($ad['reviewed_by']);
                $ad['category_name'] = $this->getCategoryName($ad['category_id']);
            }
            
            return $ad ?: null;
        } catch (PDOException $e) {
            error_log("Get advertisement by ID failed: " . $e->getMessage());
            return null;
        }
    }

    public function isValidStatusTransition($currentStatus, $newStatus, $isOverride = false)
    {
        $currentStatus = strtolower($currentStatus);
        $newStatus = strtolower($newStatus);

        if ($isOverride && in_array($currentStatus, ['rejected', 'suspended'])) {
            return true;
        }

        $allowedTransitions = [
            'pending' => ['approved', 'rejected'],
            'approved' => ['scheduled', 'active', 'suspended'],
            'scheduled' => ['active', 'suspended'],
            'active' => ['paused', 'inactive', 'suspended'],
            'paused' => ['active', 'inactive', 'suspended'],
            'inactive' => ['active', 'suspended'],
            'rejected' => [],
            'suspended' => []
        ];

        if (!isset($allowedTransitions[$currentStatus])) {
            return false;
        }

        return in_array($newStatus, $allowedTransitions[$currentStatus]);
    }

    public function updateStatus($ad_id, $newStatus, $adminUsername, $reason = '', $isOverride = false)
    {
        if (!$this->adminExists($adminUsername)) {
            return [
                'success' => false,
                'message' => "❌ Admin '{$adminUsername}' does not exist."
            ];
        }

        $ad = $this->getAdvertisementById($ad_id);
        
        if (!$ad) {
            return [
                'success' => false,
                'message' => "❌ Advertisement not found."
            ];
        }

        $currentStatus = strtolower($ad['status']);
        $newStatus = strtolower($newStatus);

        if (!$this->isValidStatusTransition($currentStatus, $newStatus, $isOverride)) {
            return [
                'success' => false,
                'message' => "❌ Invalid status transition: {$currentStatus} → {$newStatus}."
            ];
        }

        try {
            $this->pdo->beginTransaction();

            // ✅ Update advertisement table
            $updateSql = "UPDATE advertisement 
                          SET status = :status,
                              admin_reviewed_by = :admin,
                              admin_reviewed_at = CURRENT_TIMESTAMP,
                              admin_notes = :notes,
                              is_override = :is_override,
                              override_reason = :override_reason
                          WHERE ad_id = :ad_id";

            $stmt = $this->pdo->prepare($updateSql);
            $stmt->execute([
                'status' => $newStatus,
                'admin' => $adminUsername,
                'notes' => $reason,
                'is_override' => $isOverride ? 1 : 0,
                'override_reason' => $isOverride ? $reason : null,
                'ad_id' => $ad_id
            ]);

            // ✅ Insert into ad_status_history with CORRECT column names
            $historySql = "INSERT INTO ad_status_history 
                          (ad_id, old_status, new_status, changed_by_role, changed_by_id, reason, is_override, created_at)
                          VALUES (:ad_id, :old_status, :new_status, 'admin', :changed_by_id, :reason, :is_override, CURRENT_TIMESTAMP)";

            $stmt = $this->pdo->prepare($historySql);
            $stmt->execute([
                'ad_id' => $ad_id,
                'old_status' => $currentStatus,
                'new_status' => $newStatus,
                'changed_by_id' => $adminUsername,
                'reason' => $reason,
                'is_override' => $isOverride ? 1 : 0
            ]);

            // ✅ NEW: Log activity to system_activity_logs
            require_once __DIR__ . '/ActivityLogModel.php';
            $activityLog = new ActivityLogModel($this->pdo);
            
            $activityType = $isOverride ? 'ad_override' : "ad_{$newStatus}";
            
            if ($isOverride) {
                $description = "Admin '{$adminUsername}' changed ad '{$ad['title']}' from '{$currentStatus}' to '{$newStatus}' (OVERRIDE)";
            } else {
                $description = "Admin '{$adminUsername}' {$newStatus} advertisement: {$ad['title']}";
            }
            
            $activityLog->logActivity(
                1,
                'admin',
                $activityType,
                $description,
                $ad_id
            );

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => "✅ Advertisement #{$ad_id} status updated to '{$newStatus}'" . ($isOverride ? ' (Admin Override)' : '') . "."
            ];

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->lastError = "Status update failed: " . $e->getMessage();
            error_log($this->lastError);
            return [
                'success' => false,
                'message' => "❌ Database error: " . $e->getMessage()
            ];
        }
    }

    public function getStatusHistory($ad_id)
    {
        $sql = "SELECT * FROM ad_status_history 
                WHERE ad_id = :ad_id 
                ORDER BY created_at DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['ad_id' => $ad_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get status history failed: " . $e->getMessage());
            return [];
        }
    }

    public function getOverrideHistory($ad_id)
    {
        $sql = "SELECT * FROM ad_status_history 
                WHERE ad_id = :ad_id AND is_override = 1
                ORDER BY created_at DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['ad_id' => $ad_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get override history failed: " . $e->getMessage());
            return [];
        }
    }

    public function getAllModerators()
    {
        try {
            $stmt = $this->pdo->query("SELECT moderator_id, username, email FROM moderator ORDER BY username");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get all moderators failed: " . $e->getMessage());
            return [];
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