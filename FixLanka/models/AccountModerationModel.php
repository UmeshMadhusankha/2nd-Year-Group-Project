<?php
/**
 * AccountModerationModel.php
 * Pure database layer - NO business logic
 */

class AccountModerationModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get statistics for dashboard cards
     */
    public function getStatistics()
    {
        try {
            $totalUsers = $this->pdo->query("SELECT COUNT(*) FROM User")->fetchColumn();
            $totalRepairers = $this->pdo->query("SELECT COUNT(*) FROM Repairer")->fetchColumn();
            $totalCompanies = $this->pdo->query("SELECT COUNT(*) FROM Company")->fetchColumn();
            $total = $totalUsers + $totalRepairers + $totalCompanies;

            $suspended = $this->pdo->query("SELECT COUNT(*) FROM account_moderation_status WHERE account_status = 'SUSPENDED'")->fetchColumn();
            $banned = $this->pdo->query("SELECT COUNT(*) FROM account_moderation_status WHERE account_status = 'BANNED'")->fetchColumn();
            $active = $total - $suspended - $banned;

            return [
                'total' => intval($total),
                'active' => intval($active),
                'suspended' => intval($suspended),
                'banned' => intval($banned)
            ];
        } catch (Exception $e) {
            error_log("Statistics Error: " . $e->getMessage());
            return ['total' => 0, 'active' => 0, 'suspended' => 0, 'banned' => 0];
        }
    }

    /**
     * Get all accounts with filters and pagination
     */
    public function getAllAccounts($search = '', $status = '', $role = '', $sort = 'newest', $limit = 10, $offset = 0)
    {
        try {
            $accounts = [];

            // Fetch Users
            if (empty($role) || $role === 'User') {
                $accounts = array_merge($accounts, $this->fetchUsers($search, $status, $sort));
            }

            // Fetch Repairers
            if (empty($role) || $role === 'Repairer') {
                $accounts = array_merge($accounts, $this->fetchRepairers($search, $status, $sort));
            }

            // Fetch Companies
            if (empty($role) || $role === 'Company') {
                $accounts = array_merge($accounts, $this->fetchCompanies($search, $status, $sort));
            }

            // Sort
            usort($accounts, function($a, $b) use ($sort) {
                $dateA = strtotime($a['updated_at']);
                $dateB = strtotime($b['updated_at']);
                return $sort === 'newest' ? ($dateB - $dateA) : ($dateA - $dateB);
            });

            $totalCount = count($accounts);
            $accounts = array_slice($accounts, $offset, $limit);

            return [
                'accounts' => $accounts,
                'total' => $totalCount
            ];
            
        } catch (Exception $e) {
            error_log("getAllAccounts Error: " . $e->getMessage());
            return ['accounts' => [], 'total' => 0];
        }
    }

    private function fetchUsers($search, $status, $sort)
    {
        $sql = "SELECT 
                    u.user_id as account_id,
                    CONCAT(u.f_name, ' ', u.l_name) as name,
                    u.email,
                    u.phone,
                    'User' as account_type,
                    COALESCE(ams.account_status, 'ACTIVE') as account_status,
                    COALESCE(ams.banned_permanent, 0) as banned_permanent,
                    ams.suspended_until,
                    ams.moderation_reason,
                    COALESCE(ams.last_updated, u.created_at) as updated_at
                FROM User u
                LEFT JOIN account_moderation_status ams 
                    ON u.user_id = ams.account_id AND ams.account_type = 'User'
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (CONCAT(u.f_name, ' ', u.l_name) LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if (!empty($status)) {
            if ($status === 'ACTIVE') {
                $sql .= " AND COALESCE(ams.account_status, 'ACTIVE') = 'ACTIVE'";
            } else {
                $sql .= " AND ams.account_status = ?";
                $params[] = $status;
            }
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function fetchRepairers($search, $status, $sort)
    {
        $sql = "SELECT 
                    r.repairer_id as account_id,
                    CONCAT(r.f_name, ' ', r.l_name) as name,
                    r.email,
                    r.phoneNumber,
                    'Repairer' as account_type,
                    COALESCE(ams.account_status, 'ACTIVE') as account_status,
                    COALESCE(ams.banned_permanent, 0) as banned_permanent,
                    ams.suspended_until,
                    ams.moderation_reason,
                    COALESCE(ams.last_updated, r.created_at) as updated_at
                FROM Repairer r
                LEFT JOIN account_moderation_status ams 
                    ON r.repairer_id = ams.account_id AND ams.account_type = 'Repairer'
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (CONCAT(r.f_name, ' ', r.l_name) LIKE ? OR r.email LIKE ? OR r.phoneNumber LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if (!empty($status)) {
            if ($status === 'ACTIVE') {
                $sql .= " AND COALESCE(ams.account_status, 'ACTIVE') = 'ACTIVE'";
            } else {
                $sql .= " AND ams.account_status = ?";
                $params[] = $status;
            }
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function fetchCompanies($search, $status, $sort)
    {
        $sql = "SELECT 
                    c.company_id as account_id,
                    c.name as name,
                    c.email,
                    c.contact_no,
                    'Company' as account_type,
                    COALESCE(ams.account_status, 'ACTIVE') as account_status,
                    COALESCE(ams.banned_permanent, 0) as banned_permanent,
                    ams.suspended_until,
                    ams.moderation_reason,
                    COALESCE(ams.last_updated, c.created_at) as updated_at
                FROM Company c
                LEFT JOIN account_moderation_status ams 
                    ON c.company_id = ams.account_id AND ams.account_type = 'Company'
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (c.name LIKE ? OR c.email LIKE ? OR c.contact_no LIKE ?)";
            $searchParam = "%$search%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if (!empty($status)) {
            if ($status === 'ACTIVE') {
                $sql .= " AND COALESCE(ams.account_status, 'ACTIVE') = 'ACTIVE'";
            } else {
                $sql .= " AND ams.account_status = ?";
                $params[] = $status;
            }
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get single account by ID and type
     */
    public function getAccountById($id, $type)
    {
        $tableName = $this->getTableName($type);
        $idColumn = $this->getIdColumn($type);
        $nameColumn = ($type === 'Company') ? 'name' : "CONCAT(f_name, ' ', l_name)";

        $sql = "SELECT 
                    t.{$idColumn} as account_id,
                    {$nameColumn} as name,
                    t.email,
                    t.phone,
                    '{$type}' as account_type,
                    COALESCE(ams.account_status, 'ACTIVE') as account_status,
                    COALESCE(ams.banned_permanent, 0) as banned_permanent,
                    ams.suspended_until,
                    ams.moderation_reason,
                    COALESCE(ams.last_updated, t.created_at) as updated_at
                FROM {$tableName} t
                LEFT JOIN account_moderation_status ams 
                    ON t.{$idColumn} = ams.account_id AND ams.account_type = ?
                WHERE t.{$idColumn} = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$type, $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ban account permanently
     */
    public function banAccount($id, $type, $reason, $notes, $adminUsername)
    {
        $sql = "INSERT INTO account_moderation_status 
                    (account_id, account_type, account_status, banned_permanent, suspended_until, moderation_reason, updated_by)
                VALUES (?, ?, 'BANNED', 1, NULL, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    account_status = 'BANNED',
                    banned_permanent = 1,
                    suspended_until = NULL,
                    moderation_reason = VALUES(moderation_reason),
                    updated_by = VALUES(updated_by),
                    last_updated = CURRENT_TIMESTAMP";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id, $type, $reason, $adminUsername]);
    }

    /**
     * Suspend account for duration
     */
    public function suspendAccount($id, $type, $reason, $notes, $endDate, $adminUsername)
    {
        $sql = "INSERT INTO account_moderation_status 
                    (account_id, account_type, account_status, banned_permanent, suspended_until, moderation_reason, updated_by)
                VALUES (?, ?, 'SUSPENDED', 0, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    account_status = 'SUSPENDED',
                    banned_permanent = 0,
                    suspended_until = VALUES(suspended_until),
                    moderation_reason = VALUES(moderation_reason),
                    updated_by = VALUES(updated_by),
                    last_updated = CURRENT_TIMESTAMP";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id, $type, $endDate, $reason, $adminUsername]);
    }

    /**
     * Restore account to ACTIVE
     */
    public function restoreAccount($id, $type, $adminUsername)
    {
        $sql = "INSERT INTO account_moderation_status 
                    (account_id, account_type, account_status, banned_permanent, suspended_until, moderation_reason, updated_by)
                VALUES (?, ?, 'ACTIVE', 0, NULL, NULL, ?)
                ON DUPLICATE KEY UPDATE 
                    account_status = 'ACTIVE',
                    banned_permanent = 0,
                    suspended_until = NULL,
                    moderation_reason = NULL,
                    updated_by = VALUES(updated_by),
                    last_updated = CURRENT_TIMESTAMP";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id, $type, $adminUsername]);
    }

    /**
     * Log moderation case with notes
     */
    public function logModerationCase($targetId, $targetType, $adminUsername, $actionType, $reason, $notes, $durationDays, $startDate, $endDate, $statusBefore, $statusAfter, $isPermanent)
    {
        $sql = "INSERT INTO account_moderation_cases 
                    (target_id, target_type, admin_username, action_type, reason, notes, duration_days, start_date, end_date, status_before, status_after, is_permanent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $targetId, $targetType, $adminUsername, $actionType, $reason, $notes,
            $durationDays, $startDate, $endDate, $statusBefore, $statusAfter, $isPermanent
        ]);

        return $this->pdo->lastInsertId();
    }

    /**
     * Create admin notification
     */
    public function createAdminNotification($adminUsername, $type, $title, $message, $accountId, $accountType)
    {
        $sql = "INSERT INTO admin_notifications 
                    (admin_username, notification_type, title, message, account_id, account_type)
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$adminUsername, $type, $title, $message, $accountId, $accountType]);
    }

    /**
     * Check if permanently banned
     */
    public function isPermanentlyBanned($id, $type)
    {
        $sql = "SELECT banned_permanent 
                FROM account_moderation_status 
                WHERE account_id = ? AND account_type = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $type]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result && $result['banned_permanent'] == 1);
    }

    /**
     * Auto-restore expired suspensions
     */
    public function autoRestoreExpiredSuspensions()
    {
        $now = date('Y-m-d H:i:s');
        
        $sql = "UPDATE account_moderation_status 
                SET account_status = 'ACTIVE', 
                    suspended_until = NULL, 
                    last_updated = CURRENT_TIMESTAMP
                WHERE account_status = 'SUSPENDED' 
                    AND suspended_until IS NOT NULL 
                    AND suspended_until <= ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$now]);
    }

    /**
     * Get all accounts for dropdown
     */
    public function getAllAccountsForDropdown()
    {
        $accounts = [];

        $sql = "SELECT user_id as id, CONCAT(f_name, ' ', l_name) as name, email, 'User' as type FROM User ORDER BY f_name";
        $accounts = array_merge($accounts, $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));

        $sql = "SELECT repairer_id as id, CONCAT(f_name, ' ', l_name) as name, email, 'Repairer' as type FROM Repairer ORDER BY f_name";
        $accounts = array_merge($accounts, $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));

        $sql = "SELECT company_id as id, name, email, 'Company' as type FROM Company ORDER BY name";
        $accounts = array_merge($accounts, $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC));

        return $accounts;
    }

    private function getTableName($type)
    {
        switch ($type) {
            case 'User': return 'User';
            case 'Repairer': return 'Repairer';
            case 'Company': return 'Company';
            default: throw new Exception("Invalid account type");
        }
    }

    private function getIdColumn($type)
    {
        switch ($type) {
            case 'User': return 'user_id';
            case 'Repairer': return 'repairer_id';
            case 'Company': return 'company_id';
            default: throw new Exception("Invalid account type");
        }
    }
}
?>