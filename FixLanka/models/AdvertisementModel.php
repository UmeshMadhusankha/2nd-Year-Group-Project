<?php
/**
 * AdvertisementModel.php - Model for Advertisement Management
 * Handles all database operations for Advertisement table
 */

class AdvertisementModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Get statistics for dashboard cards
     * @return array Statistics data
     */
    public function getStatistics()
    {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
        ];

        // Total Ads
        $total_sql = "SELECT COUNT(*) as total FROM Advertisement";
        $total_result = $this->conn->query($total_sql);
        if ($total_result) {
            $stats['total'] = $total_result->fetch_assoc()['total'];
        }

        // Pending Ads
        $pending_sql = "SELECT COUNT(*) as total FROM Advertisement WHERE status = 'pending'";
        $pending_result = $this->conn->query($pending_sql);
        if ($pending_result) {
            $stats['pending'] = $pending_result->fetch_assoc()['total'];
        }

        // Approved Ads
        $approved_sql = "SELECT COUNT(*) as total FROM Advertisement WHERE status = 'approved'";
        $approved_result = $this->conn->query($approved_sql);
        if ($approved_result) {
            $stats['approved'] = $approved_result->fetch_assoc()['total'];
        }

        // Rejected Ads
        $rejected_sql = "SELECT COUNT(*) as total FROM Advertisement WHERE status = 'rejected'";
        $rejected_result = $this->conn->query($rejected_sql);
        if ($rejected_result) {
            $stats['rejected'] = $rejected_result->fetch_assoc()['total'];
        }

        return $stats;
    }

    /**
     * Get advertisements with filters
     * @param array $filters Filter criteria (status, type, search)
     * @return array Array of advertisements
     */
    public function getAdvertisements($filters = [])
    {
        $sql = "SELECT 
                    a.ad_id,
                    a.provider_id,
                    a.provider_type,
                    a.title,
                    a.type,
                    a.budget,
                    a.status,
                    a.submission_date,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown'
                    END as company_name
                FROM Advertisement a
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                WHERE 1=1";

        $params = array();
        $types = "";

        // Apply status filter
        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }

        // Apply type filter
        if (!empty($filters['type'])) {
            $sql .= " AND a.type = ?";
            $params[] = $filters['type'];
            $types .= "s";
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $sql .= " AND (a.title LIKE ? OR c.name LIKE ? OR CONCAT(r.f_name, ' ', r.l_name) LIKE ?)";
            $search_param = "%" . $filters['search'] . "%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= "sss";
        }

        $sql .= " ORDER BY a.submission_date DESC";

        // Prepare and execute
        $stmt = $this->conn->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all results
        $ads = array();
        while ($row = $result->fetch_assoc()) {
            $ads[] = array(
                'id' => $row['ad_id'],
                'company' => $row['company_name'] ?? 'Unknown Provider',
                'title' => $row['title'],
                'type' => ucfirst($row['type']),
                'budget' => number_format($row['budget'], 2),
                'status' => ucfirst($row['status']),
                'submittedDate' => $row['submission_date'],
                'description' => $row['title'],
                'duration' => 'N/A',
                'views' => 0,
                'clicks' => 0
            );
        }

        $stmt->close();
        return $ads;
    }

    /**
     * Update advertisement status
     * @param int $ad_id Advertisement ID
     * @param string $status New status (approved, rejected, active)
     * @return bool Success status
     */
    public function updateStatus($ad_id, $status)
    {
        $update_sql = "UPDATE Advertisement SET status = ? WHERE ad_id = ?";
        $stmt = $this->conn->prepare($update_sql);
        $stmt->bind_param("si", $status, $ad_id);
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Get single advertisement by ID
     * @param int $ad_id Advertisement ID
     * @return array|null Advertisement data or null if not found
     */
    public function getAdvertisementById($ad_id)
    {
        $sql = "SELECT 
                    a.*,
                    CASE 
                        WHEN a.provider_type = 'company' THEN c.name
                        WHEN a.provider_type = 'repairer' THEN CONCAT(r.f_name, ' ', r.l_name)
                        ELSE 'Unknown'
                    END as company_name
                FROM Advertisement a
                LEFT JOIN Company c ON a.provider_id = c.company_id AND a.provider_type = 'company'
                LEFT JOIN Repairer r ON a.provider_id = r.repairer_id AND a.provider_type = 'repairer'
                WHERE a.ad_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $ad_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $ad = $result->fetch_assoc();
        $stmt->close();
        
        return $ad;
    }

    /**
     * Check if Advertisement table exists
     * @return bool True if table exists
     */
    public function tableExists()
    {
        $table_check = $this->conn->query("SHOW TABLES LIKE 'Advertisement'");
        return $table_check->num_rows > 0;
    }
}