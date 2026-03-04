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
}