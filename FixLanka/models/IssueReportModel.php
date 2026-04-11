<?php
/**
 * IssueReportModel.php
 * Database operations for Issues & Reports
 * MVC Architecture - Model Layer Only (Pure Database Logic)
 * 
 * @version 2.2 - PRODUCTION READY (FIXED Missing Target Name)
 * @safety Uses PDO prepared statements, transactions, and error handling
 */

class IssueReportModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // =====================================================
    // QUERY METHODS
    // =====================================================

    /**
     * Get all issues with filters
     * ✅ FIXED: Added target name retrieval
     */
    public function getAllIssues($statusFilter = '', $priorityFilter = '', $search = '')
    {
        $sql = "SELECT 
                    ir.issue_id,
                    ir.reportedBy_id,
                    ir.target_id,
                    ir.target_type,
                    ir.description,
                    ir.priority,
                    ir.status,
                    ir.admin_notes,
                    ir.admin_internal_notes,
                    ir.date as created_at,
                    ir.updated_at,
                    CONCAT(u.f_name, ' ', u.l_name) as reporter_name,
                    u.email as reporter_email,
                    -- ✅ GET TARGET NAME based on target_type
                    CASE 
                        WHEN ir.target_type = 'company' THEN tc.name
                        WHEN ir.target_type = 'repairer' THEN CONCAT(tr.f_name, ' ', tr.l_name)
                        ELSE 'Unknown'
                    END as target_name
                FROM IssueReport ir
                LEFT JOIN User u ON ir.reportedBy_id = u.user_id
                -- ✅ JOIN for Company targets
                LEFT JOIN Company tc ON ir.target_id = tc.company_id AND ir.target_type = 'company'
                -- ✅ JOIN for Repairer targets
                LEFT JOIN Repairer tr ON ir.target_id = tr.repairer_id AND ir.target_type = 'repairer'
                WHERE 1=1";

        $params = [];

        if (!empty($statusFilter)) {
            $sql .= " AND ir.status = :status";
            $params[':status'] = $statusFilter;
        }

        if (!empty($priorityFilter)) {
            $sql .= " AND ir.priority = :priority";
            $params[':priority'] = $priorityFilter;
        }

        if (!empty($search)) {
            $sql .= " AND (ir.description LIKE :search 
                      OR CONCAT(u.f_name, ' ', u.l_name) LIKE :search 
                      OR u.email LIKE :search
                      OR tc.name LIKE :search
                      OR CONCAT(tr.f_name, ' ', tr.l_name) LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY 
                    CASE ir.priority 
                        WHEN 'high' THEN 1 
                        WHEN 'medium' THEN 2 
                        WHEN 'low' THEN 3 
                    END,
                    ir.date DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Model Error - getAllIssues: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get issue by ID with full details
     * ✅ FIXED: Added target name retrieval
     */
    public function getIssueById($issueId)
    {
        $sql = "SELECT 
                    ir.*,
                    CONCAT(u.f_name, ' ', u.l_name) as reporter_name,
                    u.email as reporter_email,
                    u.phone as reporter_phone,
                    -- ✅ GET TARGET NAME
                    CASE 
                        WHEN ir.target_type = 'company' THEN tc.name
                        WHEN ir.target_type = 'repairer' THEN CONCAT(tr.f_name, ' ', tr.l_name)
                        ELSE 'Unknown'
                    END as target_name
                FROM IssueReport ir
                LEFT JOIN User u ON ir.reportedBy_id = u.user_id
                LEFT JOIN Company tc ON ir.target_id = tc.company_id AND ir.target_type = 'company'
                LEFT JOIN Repairer tr ON ir.target_id = tr.repairer_id AND ir.target_type = 'repairer'
                WHERE ir.issue_id = :issue_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':issue_id' => $issueId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Model Error - getIssueById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get statistics for dashboard cards
     */
    public function getStatistics()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'investigating' THEN 1 ELSE 0 END) as investigating,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved
                FROM IssueReport";

        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Model Error - getStatistics: " . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'investigating' => 0,
                'resolved' => 0
            ];
        }
    }

    // =====================================================
    // UPDATE METHODS
    // =====================================================

    /**
     * Update issue (status, priority, notes)
     */
    public function updateIssue($issueId, $status, $priority, $adminNotes, $internalNotes)
    {
        $sql = "UPDATE IssueReport 
                SET status = :status, 
                    priority = :priority,
                    admin_notes = :admin_notes,
                    admin_internal_notes = :internal_notes,
                    updated_at = CURRENT_TIMESTAMP
                WHERE issue_id = :issue_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':status' => $status,
                ':priority' => $priority,
                ':admin_notes' => $adminNotes,
                ':internal_notes' => $internalNotes,
                ':issue_id' => $issueId
            ]);
        } catch (PDOException $e) {
            error_log("Model Error - updateIssue: " . $e->getMessage());
            throw $e;
        }
    }

    // =====================================================
    // STATUS HISTORY METHODS
    // =====================================================

    /**
     * Log status change to history table
     */
    public function logStatusHistory($issueId, $oldStatus, $newStatus, $changedBy, $changeReason)
    {
        $sql = "INSERT INTO IssueStatusHistory 
                (issue_id, old_status, new_status, changed_by, change_reason, changed_at) 
                VALUES (:issue_id, :old_status, :new_status, :changed_by, :change_reason, NOW())";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':issue_id' => $issueId,
                ':old_status' => $oldStatus,
                ':new_status' => $newStatus,
                ':changed_by' => $changedBy,
                ':change_reason' => $changeReason
            ]);
        } catch (PDOException $e) {
            error_log("Model Error - logStatusHistory: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get status history for an issue
     */
    public function getStatusHistory($issueId)
    {
        $sql = "SELECT 
                    history_id,
                    old_status,
                    new_status,
                    changed_by,
                    change_reason as notes,
                    changed_at
                FROM IssueStatusHistory 
                WHERE issue_id = :issue_id 
                ORDER BY changed_at DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':issue_id' => $issueId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Model Error - getStatusHistory: " . $e->getMessage());
            return [];
        }
    }

    // =====================================================
    // NOTIFICATION METHODS
    // =====================================================

    /**
     * Create notification for issue status change
     */
    public function createNotification($issueId, $recipientType, $recipientId, $message, $statusChange)
    {
        $sql = "INSERT INTO IssueNotification 
                (issue_id, recipient_type, recipient_id, message, status_change, created_at) 
                VALUES (:issue_id, :recipient_type, :recipient_id, :message, :status_change, NOW())";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':issue_id' => $issueId,
                ':recipient_type' => $recipientType,
                ':recipient_id' => $recipientId,
                ':message' => $message,
                ':status_change' => $statusChange
            ]);
        } catch (PDOException $e) {
            error_log("Model Error - createNotification: " . $e->getMessage());
            throw $e;
        }
    }

    // =====================================================
    // DELETE METHODS
    // =====================================================

    /**
     * Delete an issue (with cascade to history and notifications)
     */
    public function deleteIssue($issueId)
    {
        $sql = "DELETE FROM IssueReport WHERE issue_id = :issue_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':issue_id' => $issueId]);
        } catch (PDOException $e) {
            error_log("Model Error - deleteIssue: " . $e->getMessage());
            throw $e;
        }
    }

    // =====================================================
    // TRANSACTION CONTROL
    // =====================================================

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollback()
    {
        return $this->pdo->rollBack();
    }

    private function columnExists(string $table, string $column): bool
    {
        try {
            $stmt = $this->pdo->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column LIMIT 1");
            $stmt->execute([
                ':table' => $table,
                ':column' => $column
            ]);
            return (bool)$stmt->fetchColumn();
        } catch (Throwable $e) {
            return false;
        }
    }

    private function getEnumValues(string $table, string $column): array
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column LIMIT 1");
            $stmt->execute([
                ':table' => $table,
                ':column' => $column
            ]);
            $type = (string)$stmt->fetchColumn();
            if ($type === '' || stripos($type, 'enum(') !== 0) {
                return [];
            }

            $inside = substr($type, 5, -1);
            $parts = str_getcsv($inside, ',', "'");
            return array_values(array_filter(array_map(static function ($v) {
                return trim((string)$v);
            }, $parts)));
        } catch (Throwable $e) {
            return [];
        }
    }

    private function ensureReporterUserId(int $repairerId, string $email, string $name): int
    {
        $fallbackId = max(1, $repairerId);

        if ($email !== '') {
            try {
                $lookup = $this->pdo->prepare("SELECT user_id FROM User WHERE LOWER(email) = LOWER(:email) LIMIT 1");
                $lookup->execute([':email' => $email]);
                $existing = (int)($lookup->fetchColumn() ?: 0);
                if ($existing > 0) {
                    return $existing;
                }

                $parts = preg_split('/\s+/', trim($name));
                $firstName = trim((string)($parts[0] ?? 'Repairer'));
                $lastName = trim((string)(count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : 'Reporter'));
                if ($firstName === '') $firstName = 'Repairer';
                if ($lastName === '') $lastName = 'Reporter';

                $passwordHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT);
                $insert = $this->pdo->prepare("INSERT INTO User (f_name, l_name, email, password) VALUES (:f_name, :l_name, :email, :password)");
                $insert->execute([
                    ':f_name' => $firstName,
                    ':l_name' => $lastName,
                    ':email' => $email,
                    ':password' => $passwordHash
                ]);

                $created = (int)$this->pdo->lastInsertId();
                if ($created > 0) {
                    return $created;
                }
            } catch (Throwable $e) {
                try {
                    $retry = $this->pdo->prepare("SELECT user_id FROM User WHERE LOWER(email) = LOWER(:email) LIMIT 1");
                    $retry->execute([':email' => $email]);
                    $found = (int)($retry->fetchColumn() ?: 0);
                    if ($found > 0) {
                        return $found;
                    }
                } catch (Throwable $ignored) {
                }
            }
        }

        return $fallbackId;
    }

    public function createIssueFromRepairer(int $repairerId, string $subject, string $message, string $reporterEmail = '', string $reporterName = ''): int
    {
        $repairerId = (int)$repairerId;
        if ($repairerId <= 0) {
            throw new InvalidArgumentException('Invalid repairer id');
        }

        $subject = trim($subject);
        $message = trim($message);
        if ($subject === '' || $message === '') {
            throw new InvalidArgumentException('Subject and message are required');
        }

        $reporterUserId = $this->ensureReporterUserId($repairerId, trim($reporterEmail), trim($reporterName));
        $description = $subject . "\n\n" . $message;

        $columns = ['reportedBy_id', 'target_id', 'target_type', 'description'];
        $placeholders = [':reported_by_id', ':target_id', ':target_type', ':description'];
        $params = [
            ':reported_by_id' => $reporterUserId,
            ':target_id' => $repairerId,
            ':target_type' => 'repairer',
            ':description' => $description,
        ];

        if ($this->columnExists('IssueReport', 'priority')) {
            $columns[] = 'priority';
            $placeholders[] = ':priority';
            $params[':priority'] = 'medium';
        }

        if ($this->columnExists('IssueReport', 'status')) {
            $statusValues = array_map('strtolower', $this->getEnumValues('IssueReport', 'status'));
            $status = in_array('pending', $statusValues, true) ? 'pending' : 'open';
            $columns[] = 'status';
            $placeholders[] = ':status';
            $params[':status'] = $status;
        }

        $sql = "INSERT INTO IssueReport (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int)$this->pdo->lastInsertId();
    }

    public function getIssuesForRepairer(int $repairerId): array
    {
        $repairerId = (int)$repairerId;
        if ($repairerId <= 0) {
            return [];
        }

        $hasUpdatedAt = $this->columnExists('IssueReport', 'updated_at');

        $sql = "SELECT issue_id, description, status, date AS created_at"
            . ($hasUpdatedAt ? ", updated_at" : "")
            . " FROM IssueReport WHERE target_type = :target_type AND target_id = :repairer_id ORDER BY issue_id DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':target_type' => 'repairer',
                ':repairer_id' => $repairerId,
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            error_log('Model Error - getIssuesForRepairer: ' . $e->getMessage());
            return [];
        }
    }
}