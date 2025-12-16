<?php
/**
 * AdReportModel.php
 * Handles all database operations for advertisement reports
 */

class AdReportModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all reports with filters
     */
    public function getAllReports($filters = [])
    {
        $sql = "SELECT 
                    r.*,
                    a.title as ad_title,
                    a.type as ad_type,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(rep.f_name, ' ', rep.l_name)
                        ELSE 'Unknown'
                    END as company_name
                FROM ad_reports r
                LEFT JOIN Advertisement a ON r.ad_id = a.ad_id
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer rep ON a.provider_id = rep.repairer_id AND a.provider_type = 'repairer'
                WHERE 1=1";

        $params = [];

        // Apply filters
        if (!empty($filters['status'])) {
            $sql .= " AND r.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql .= " AND r.priority = :priority";
            $params[':priority'] = $filters['priority'];
        }

        if (!empty($filters['issue_type'])) {
            $sql .= " AND r.issue_type = :issue_type";
            $params[':issue_type'] = $filters['issue_type'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (a.title LIKE :search OR r.reporter_name LIKE :search OR r.description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY r.created_at DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching reports: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update report (status, priority, moderator notes)
     */
    public function updateReport($report_id, $data)
    {
        $sql = "UPDATE ad_reports SET
                    status = :status,
                    priority = :priority,
                    moderator_notes = :moderator_notes,
                    updated_at = CURRENT_TIMESTAMP";

        // If status is resolved, set resolved_at
        if ($data['status'] === 'resolved') {
            $sql .= ", resolved_at = CURRENT_TIMESTAMP";
        }

        $sql .= " WHERE report_id = :report_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':priority', $data['priority']);
            $stmt->bindParam(':moderator_notes', $data['moderator_notes']);
            $stmt->bindParam(':report_id', $report_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating report: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            $statusCounts = $this->pdo->query("
                SELECT status, COUNT(*) as count 
                FROM ad_reports 
                GROUP BY status
            ")->fetchAll(PDO::FETCH_KEY_PAIR);

            return [
                'pending' => $statusCounts['pending'] ?? 0,
                'investigating' => $statusCounts['investigating'] ?? 0,
                'escalated' => $statusCounts['escalated'] ?? 0,
                'resolved' => $statusCounts['resolved'] ?? 0,
                'reject_report' => $statusCounts['reject_report'] ?? 0,
                'suspend_ad' => $statusCounts['suspend_ad'] ?? 0,
                'delete_ad' => $statusCounts['delete_ad'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Error getting statistics: " . $e->getMessage());
            return [
                'pending' => 0,
                'investigating' => 0,
                'escalated' => 0,
                'resolved' => 0,
                'reject_report' => 0,
                'suspend_ad' => 0,
                'delete_ad' => 0
            ];
        }
    }
}
