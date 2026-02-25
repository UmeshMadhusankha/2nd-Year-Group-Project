<?php
/**
 * AdReportModel.php - FIXED VERSION
 * Handles all database operations for advertisement reports
 * ✅ Updated to match actual ad_reports table structure
 */

class AdReportModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all advertisement reports with full details
     * ✅ FIXED: Uses correct column names from database
     */
    public function getAllReports($filters = [])
    {
        $sql = "SELECT 
                    r.report_id,
                    r.ad_id,
                    r.reporter_id,
                    r.reporter_type,
                    r.report_category,
                    r.description,
                    r.severity,
                    r.status as report_status,
                    r.assigned_to,
                    r.resolution_notes,
                    r.resolved_at,
                    r.resolved_by,
                    r.created_at,
                    r.updated_at,
                    a.title as ad_title,
                    a.type as ad_type,
                    a.status as ad_status,
                    a.provider_type,
                    a.provider_id,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(rep.f_name, ' ', rep.l_name)
                        ELSE 'Unknown'
                    END as company_name,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.email
                        WHEN a.provider_type = 'repairer' THEN rep.email
                        ELSE NULL
                    END as provider_email
                FROM ad_reports r
                INNER JOIN Advertisement a ON r.ad_id = a.ad_id
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer rep ON a.provider_id = rep.repairer_id AND a.provider_type = 'repairer'
                WHERE 1=1";

        $params = [];

        // Apply filters
        if (!empty($filters['status'])) {
            $sql .= " AND r.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['severity'])) {
            $sql .= " AND r.severity = :severity";
            $params[':severity'] = $filters['severity'];
        }

        if (!empty($filters['report_category'])) {
            $sql .= " AND r.report_category = :report_category";
            $params[':report_category'] = $filters['report_category'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (a.title LIKE :search OR r.description LIKE :search)";
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
     * Get single report by ID with full details
     */
    public function getReportById($report_id)
    {
        $sql = "SELECT 
                    r.*,
                    a.title as ad_title,
                    a.status as ad_status,
                    a.provider_type,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(rep.f_name, ' ', rep.l_name)
                        ELSE 'Unknown'
                    END as company_name
                FROM ad_reports r
                INNER JOIN Advertisement a ON r.ad_id = a.ad_id
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer rep ON a.provider_id = rep.repairer_id AND a.provider_type = 'repairer'
                WHERE r.report_id = :report_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':report_id', $report_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching report: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update report status and notes
     * ✅ FIXED: Uses resolution_notes, assigned_to, resolved_by
     */
    public function updateReport($report_id, $data)
    {
        // Get current report data
        $currentReport = $this->getReportById($report_id);
        if (!$currentReport) {
            return ['success' => false, 'message' => 'Report not found'];
        }

        // Validate report status transition
        $validTransitions = [
            'pending' => ['investigating', 'dismissed'],
            'investigating' => ['resolved', 'escalated', 'dismissed'],
            'resolved' => [], // Final state
            'dismissed' => [], // Final state
            'escalated' => ['resolved', 'dismissed']
        ];

        $currentStatus = $currentReport['status'];
        $newStatus = $data['status'];

        // Check if transition is valid
        if ($currentStatus !== $newStatus && 
            !in_array($newStatus, $validTransitions[$currentStatus] ?? [])) {
            return [
                'success' => false, 
                'message' => "Cannot change status from '{$currentStatus}' to '{$newStatus}'"
            ];
       }

        $sql = "UPDATE ad_reports SET
                    status = :status,
                    severity = :severity,
                    resolution_notes = :resolution_notes,
                    assigned_to = :assigned_to,
                    updated_at = CURRENT_TIMESTAMP";

        // If status is resolved, set resolved_at and resolved_by
        if ($newStatus === 'resolved') {
            $sql .= ", resolved_at = CURRENT_TIMESTAMP, resolved_by = :resolved_by";
        }

        $sql .= " WHERE report_id = :report_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':severity', $data['severity']);
            $stmt->bindParam(':resolution_notes', $data['resolution_notes']);
            $stmt->bindParam(':assigned_to', $data['moderator_id'], PDO::PARAM_INT);
            $stmt->bindParam(':report_id', $report_id, PDO::PARAM_INT);
            
            if ($newStatus === 'resolved') {
                $stmt->bindParam(':resolved_by', $data['moderator_id'], PDO::PARAM_INT);
            }
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Report updated successfully'];
            }
            return ['success' => false, 'message' => 'Failed to update report'];
        } catch (PDOException $e) {
            error_log("Error updating report: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Get current advertisement status
     */
    public function getAdStatus($ad_id)
    {
        $sql = "SELECT status FROM Advertisement WHERE ad_id = :ad_id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':ad_id', $ad_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['status'] : null;
        } catch (PDOException $e) {
            error_log("Error getting ad status: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update advertisement status with strict validation
     */
    public function updateAdStatus($ad_id, $new_status, $reason = '')
    {
        // Get current ad status
        $currentStatus = $this->getAdStatus($ad_id);
        if (!$currentStatus) {
            return ['success' => false, 'message' => 'Advertisement not found'];
        }

        // Define valid status transitions
        $validTransitions = [
            'pending' => ['approved', 'rejected'],
            'approved' => ['active', 'rejected'],
            'active' => ['inactive', 'suspended'],
            'inactive' => ['active', 'suspended'],
            'suspended' => ['deleted'],
            'rejected' => [], // FINAL - Cannot change
            'expired' => [], // FINAL - Cannot change
            'deleted' => []  // FINAL - Cannot change
        ];

        // Block changes to final states
        if (in_array($currentStatus, ['rejected', 'expired', 'deleted'])) {
            return [
                'success' => false, 
                'message' => "Cannot modify advertisement with '{$currentStatus}' status - it is final"
            ];
        }

        // Validate transition
        if (!in_array($new_status, $validTransitions[$currentStatus] ?? [])) {
            return [
                'success' => false, 
                'message' => "Invalid status transition from '{$currentStatus}' to '{$new_status}'"
            ];
        }

        $sql = "UPDATE Advertisement 
                SET status = :status, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE ad_id = :ad_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':ad_id', $ad_id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $this->logModeratorAction($ad_id, $currentStatus, $new_status, $reason);
                return ['success' => true, 'message' => "Advertisement status changed to '{$new_status}'"];
            }
            return ['success' => false, 'message' => 'Failed to update advertisement'];
        } catch (PDOException $e) {
            error_log("Error updating ad status: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Log moderator action
     */
    private function logModeratorAction($ad_id, $old_status, $new_status, $reason)
    {
        $sql = "INSERT INTO moderator_activity 
                (moderator_id, action_type, target_type, target_id, old_value, new_value, reason)
                VALUES (:moderator_id, 'status_change', 'advertisement', :ad_id, :old_status, :new_status, :reason)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $moderator_id = $_SESSION['moderator_id'] ?? 1;
            $stmt->bindParam(':moderator_id', $moderator_id, PDO::PARAM_INT);
            $stmt->bindParam(':ad_id', $ad_id, PDO::PARAM_INT);
            $stmt->bindParam(':old_status', $old_status);
            $stmt->bindParam(':new_status', $new_status);
            $stmt->bindParam(':reason', $reason);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error logging action: " . $e->getMessage());
        }
    }

    /**
     * Get statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_reports,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'investigating' THEN 1 ELSE 0 END) as investigating,
                        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                        SUM(CASE WHEN status = 'dismissed' THEN 1 ELSE 0 END) as dismissed,
                        SUM(CASE WHEN status = 'escalated' THEN 1 ELSE 0 END) as escalated,
                        SUM(CASE WHEN severity = 'critical' THEN 1 ELSE 0 END) as critical_reports
                    FROM ad_reports";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting statistics: " . $e->getMessage());
            return [
                'total_reports' => 0,
                'pending' => 0,
                'investigating' => 0,
                'resolved' => 0,
                'dismissed' => 0,
                'escalated' => 0,
                'critical_reports' => 0
            ];
        }
    }
}