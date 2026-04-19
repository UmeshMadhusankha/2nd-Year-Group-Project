<?php
/**
 * ModeratorModel.php - Professional Database Layer
 * Handles ALL database operations for Moderator CRUD
 * NO business logic - pure data access only
 * 
 * ✅ FIXED: getAllModerators() now returns ALL rows regardless of status value
 *           (handles NULL, empty string, 'active', 'inactive' equally)
 */

class ModeratorModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all moderators - returns every row in the table
     * 
     * ✅ FIXED: Uses COALESCE so rows with NULL status still appear.
     *    No WHERE clause on status - admin should see ALL moderators.
     *    If your table has a deleted_at column for soft deletes, we 
     *    explicitly exclude only hard-deleted rows.
     */
    public function getAllModerators()
    {
        // COALESCE ensures NULL status is treated as 'inactive' for display,
        // but crucially the row is ALWAYS returned.
        $sql = "SELECT 
                    moderator_id, 
                    username, 
                    email, 
                    COALESCE(status, 'suspended') AS status,
                    last_login,
                    created_at 
                FROM moderator
                ORDER BY created_at DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get active moderators only
     */
    public function getActiveModerators()
    {
        $sql = "SELECT * FROM moderator WHERE status = 'active' ORDER BY username ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get moderator by ID
     */
    public function getModeratorById($moderator_id)
    {
        $sql = "SELECT 
                    moderator_id, 
                    username, 
                    email, 
                    COALESCE(status, 'suspended') AS status,
                    last_login,
                    created_at 
                FROM moderator 
                WHERE moderator_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$moderator_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if username exists (excluding specific ID for updates)
     */
    public function usernameExists($username, $exclude_id = null)
    {
        if ($exclude_id) {
            $sql = "SELECT moderator_id FROM moderator WHERE username = ? AND moderator_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username, $exclude_id]);
        } else {
            $sql = "SELECT moderator_id FROM moderator WHERE username = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username]);
        }
        return $stmt->fetch() !== false;
    }

    /**
     * Check if email exists (excluding specific ID for updates)
     */
    public function emailExists($email, $exclude_id = null)
    {
        if ($exclude_id) {
            $sql = "SELECT moderator_id FROM moderator WHERE email = ? AND moderator_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $exclude_id]);
        } else {
            $sql = "SELECT moderator_id FROM moderator WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
        }
        return $stmt->fetch() !== false;
    }

    /**
     * Create new moderator
     */
    public function createModerator($username, $email, $hashedPassword)
    {
        $sql = "INSERT INTO moderator (username, email, password, status) 
                VALUES (?, ?, ?, 'active')";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username, $email, $hashedPassword]);
        
        return $this->pdo->lastInsertId();
    }
    /**
     * Update moderator password
     */
    public function updateModeratorWithPassword($moderator_id, $email, $hashedPassword)
    {
        $sql = "UPDATE moderator 
                SET email = ?, password = ? 
                WHERE moderator_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$email, $hashedPassword, $moderator_id]);
    }


    /**
     * Soft delete - Set status to inactive
     */
    public function deactivateModerator($moderator_id)
    {
        $sql = "UPDATE moderator SET status = 'suspended' WHERE moderator_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$moderator_id]);
    }

    /**
     * Reactivate moderator
     */
    public function activateModerator($moderator_id)
    {
        $sql = "UPDATE moderator SET status = 'active' WHERE moderator_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$moderator_id]);
    }

    /**
     * Hard delete - Permanently remove moderator (use with caution)
     */
    public function deleteModerator($moderator_id)
    {
        $sql = "DELETE FROM moderator WHERE moderator_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$moderator_id]);
        return $stmt->rowCount();
    }

    /**
     * Check if moderator can be safely deleted (foreign key checks)
     */
    public function canDeleteModerator($moderator_id)
    {
        $constraints = [];

        // Check Advertisement table
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM Advertisement WHERE reviewed_by = ?");
        $stmt->execute([$moderator_id]);
        $adCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        if ($adCount > 0) {
            $constraints[] = "{$adCount} advertisement(s)";
        }

        // Check ad schedules table(s)
        // This project uses `adschedule` for moderator scheduling. Some databases may also
        // include a legacy `ad_schedules` table.
        $scheduleCount = 0;

        // Current flow: adschedule (no created_by column), infer via ads reviewed by moderator.
        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) as count\n"
                . "FROM adschedule\n"
                . "WHERE ad_id IN (SELECT ad_id FROM advertisement WHERE reviewed_by = ?)"
            );
            $stmt->execute([$moderator_id]);
            $scheduleCount += (int)($stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0);
        } catch (Exception $e) {
            // ignore
        }

        // Legacy flow: ad_schedules
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM ad_schedules WHERE created_by = ?");
            $stmt->execute([$moderator_id]);
            $scheduleCount += (int)($stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0);
        } catch (Exception $e) {
            // ignore
        }
        if ($scheduleCount > 0) {
            $constraints[] = "{$scheduleCount} ad schedule(s)";
        }

        // Check FinancialReport table
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM FinancialReport WHERE generated_by = ?");
        $stmt->execute([$moderator_id]);
        $reportCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        if ($reportCount > 0) {
            $constraints[] = "{$reportCount} financial report(s)";
        }

        // Check moderator_activity table
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM moderator_activity WHERE moderator_id = ?");
        $stmt->execute([$moderator_id]);
        $activityCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        if ($activityCount > 0) {
            $constraints[] = "{$activityCount} activity log(s)";
        }

        return [
            'can_delete' => empty($constraints),
            'constraints' => $constraints,
            'total_references' => $adCount + $scheduleCount + $reportCount + $activityCount
        ];
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin($moderator_id)
    {
        $sql = "UPDATE moderator SET last_login = NOW() WHERE moderator_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$moderator_id]);
    }

    /**
     * Get moderator statistics
     * ✅ FIXED: Counts NULL status rows as 'inactive' so total always matches getAllModerators()
     */
    public function getModeratorStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN COALESCE(status,'suspended') = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN COALESCE(status,'suspended') = 'suspended' THEN 1 ELSE 0 END) as suspended
                FROM moderator";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Log moderator management action
     */
    public function logAction($admin_username, $moderator_id, $action, $old_values = null, $new_values = null)
    {
        $sql = "INSERT INTO moderator_management_log 
                (admin_username, moderator_id, action, old_values, new_values, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $admin_username,
            $moderator_id,
            $action,
            $old_values ? json_encode($old_values) : null,
            $new_values ? json_encode($new_values) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}