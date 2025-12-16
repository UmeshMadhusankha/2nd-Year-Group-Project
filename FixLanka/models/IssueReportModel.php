<?php
/**
 * IssueReportModel.php
 * Handles all database operations for Issues & Reports
 * Pure PHP - No frameworks - Beginner friendly
 */

class IssueReportModel
{
    private $pdo;

    /**
     * Constructor - Connect to database
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all issues with reporter details
     * @param string $statusFilter - Filter by status (optional)
     * @param string $priorityFilter - Filter by priority (optional)
     * @param string $search - Search in description (optional)
     * @return array - List of issues
     */
    public function getAllIssues($statusFilter = '', $priorityFilter = '', $search = '')
    {
        // Start building query
        $sql = "SELECT 
                    ir.issue_id,
                    ir.reportedBy_id,
                    ir.target_id,
                    ir.target_type,
                    ir.description,
                    ir.priority,
                    ir.status,
                    ir.date as created_at,
                    ir.updated_at,
                    CONCAT(u.f_name, ' ', u.l_name) as reporter_name,
                    u.email as reporter_email
                FROM IssueReport ir
                LEFT JOIN User u ON ir.reportedBy_id = u.user_id
                WHERE 1=1";

        $params = [];

        // Add status filter
        if (!empty($statusFilter)) {
            $sql .= " AND ir.status = :status";
            $params[':status'] = $statusFilter;
        }

        // Add priority filter
        if (!empty($priorityFilter)) {
            $sql .= " AND ir.priority = :priority";
            $params[':priority'] = $priorityFilter;
        }

        // Add search filter
        if (!empty($search)) {
            $sql .= " AND (ir.description LIKE :search OR CONCAT(u.f_name, ' ', u.l_name) LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        // Order by most recent first
        $sql .= " ORDER BY ir.date DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching issues: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get issue by ID
     * @param int $issueId
     * @return array|null - Issue details or null
     */
    public function getIssueById($issueId)
    {
        $sql = "SELECT 
                    ir.*,
                    CONCAT(u.f_name, ' ', u.l_name) as reporter_name,
                    u.email as reporter_email
                FROM IssueReport ir
                LEFT JOIN User u ON ir.reportedBy_id = u.user_id
                WHERE ir.issue_id = :issue_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':issue_id' => $issueId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching issue: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get statistics for dashboard
     * @return array - Counts of issues by status
     */
    public function getStatistics()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'investigating' THEN 1 ELSE 0 END) as investigating,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'escalated' THEN 1 ELSE 0 END) as escalated
                FROM IssueReport";

        try {
            $stmt = $this->pdo->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching statistics: " . $e->getMessage());
            return [
                'total' => 0,
                'pending' => 0,
                'investigating' => 0,
                'resolved' => 0,
                'escalated' => 0
            ];
        }
    }

    /**
     * Update issue status and priority
     * @param int $issueId
     * @param string $status
     * @param string $priority
     * @return bool - Success or failure
     */
    public function updateIssue($issueId, $status, $priority)
    {
        $sql = "UPDATE IssueReport 
                SET status = :status, 
                    priority = :priority,
                    updated_at = CURRENT_TIMESTAMP
                WHERE issue_id = :issue_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':status' => $status,
                ':priority' => $priority,
                ':issue_id' => $issueId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating issue: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete an issue
     * @param int $issueId
     * @return bool - Success or failure
     */
    public function deleteIssue($issueId)
    {
        $sql = "DELETE FROM IssueReport WHERE issue_id = :issue_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':issue_id' => $issueId]);
        } catch (PDOException $e) {
            error_log("Error deleting issue: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete user account (for serious violations)
     * @param int $userId
     * @return bool - Success or failure
     */
    public function deleteUser($userId)
    {
        $sql = "DELETE FROM User WHERE user_id = :user_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
}