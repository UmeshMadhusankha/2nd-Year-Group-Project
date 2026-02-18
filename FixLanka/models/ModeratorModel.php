<?php
/**
 * ModeratorModel.php - Professional Database Layer
 * Handles ALL database operations for Moderator CRUD
 * NO business logic - pure data access only
 */

class ModeratorModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all moderators with status
     */
    public function getAllModerators()
    {
        $sql = "SELECT 
                    moderator_id, 
                    username, 
                    email, 
                    assigned_section, 
                    status,
                    last_login,
                    created_at 
                FROM Moderator 
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
        $sql = "SELECT * FROM Moderator WHERE status = 'active' ORDER BY username ASC";
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
                    assigned_section, 
                    status,
                    last_login,
                    created_at 
                FROM Moderator 
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
            $sql = "SELECT moderator_id FROM Moderator WHERE username = ? AND moderator_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username, $exclude_id]);
        } else {
            $sql = "SELECT moderator_id FROM Moderator WHERE username = ?";
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
            $sql = "SELECT moderator_id FROM Moderator WHERE email = ? AND moderator_id != ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $exclude_id]);
        } else {
            $sql = "SELECT moderator_id FROM Moderator WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
        }
        return $stmt->fetch() !== false;
    }

    /**
     * Create new moderator
     */
    public function createModerator($username, $email, $hashedPassword, $assigned_section)
    {
        $sql = "INSERT INTO Moderator (username, email, password, assigned_section, status) 
                VALUES (?, ?, ?, ?, 'active')";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$username, $email, $hashedPassword, $assigned_section]);
        
        return $this->pdo->lastInsertId();
    }

    /**
     * Update moderator WITHOUT password change
     */
    public function updateModerator($moderator_id, $email, $assigned_section)
    {
        $sql = "UPDATE Moderator 
                SET email = ?, assigned_section = ? 
                WHERE moderator_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$email, $assigned_section, $moderator_id]);
    }

    /**
     * Update moderator WITH password change
     */
    public function updateModeratorWithPassword($moderator_id, $email, $hashedPassword, $assigned_section)
    {
        $sql = "UPDATE Moderator 
                SET email = ?, password = ?, assigned_section = ? 
                WHERE moderator_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$email, $hashedPassword, $assigned_section, $moderator_id]);
    }

    /**
     * Soft delete - Set status to inactive
     */
    public function deactivateModerator($moderator_id)
    {
        $sql = "UPDATE Moderator SET status = 'inactive' WHERE moderator_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$moderator_id]);
    }

    /**
     * Reactivate moderator
     */
    public function activateModerator($moderator_id)
    {
        $sql = "UPDATE Moderator SET status = 'active' WHERE moderator_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$moderator_id]);
    }

    /**
     * Hard delete - Permanently remove moderator (use with caution)
     */
    public function deleteModerator($moderator_id)
    {
        $sql = "DELETE FROM Moderator WHERE moderator_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$moderator_id]);
        return $stmt->rowCount();
    }

    /**
     * Check if moderator can be safely deleted (foreign key checks)
     * FIXED: Advertisement table uses 'reviewed_by' NOT 'moderator_id'
     */
    public function canDeleteModerator($moderator_id)
    {
        $constraints = [];

        // Check Advertisement table - FIXED: Uses 'reviewed_by' column
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM Advertisement WHERE reviewed_by = ?");
        $stmt->execute([$moderator_id]);
        $adCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        if ($adCount > 0) {
            $constraints[] = "{$adCount} advertisement(s)";
        }

        // Check ad_schedules table
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM ad_schedules WHERE created_by = ?");
        $stmt->execute([$moderator_id]);
        $scheduleCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
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
        $sql = "UPDATE Moderator SET last_login = NOW() WHERE moderator_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$moderator_id]);
    }

    /**
     * Get moderator statistics
     */
    public function getModeratorStats()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive
                FROM Moderator";
        
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