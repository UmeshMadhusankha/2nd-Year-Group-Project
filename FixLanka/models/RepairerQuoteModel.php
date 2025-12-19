<?php
/**
 * RepairerQuote Model
 * Handles CRUD operations for repairer quotations
 */

class RepairerQuote {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * CREATE - Submit new quotation
     * 
     * @param array $data Quotation data
     * @return int|false Quote ID on success, false on failure
     */
    public function create($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO RepairerQuote 
                (request_id, repairer_id, quoteAmount, estimatedDays, warrantyPeriod, 
                 validUntil, materialsIncluded, message, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['request_id'],
                $data['repairer_id'],
                $data['quoteAmount'],
                $data['estimatedDays'] ?? 1,
                $data['warrantyPeriod'] ?? 0,
                $data['validUntil'],
                $data['materialsIncluded'] ?? true,
                $data['message'] ?? null,
                $data['status'] ?? 'pending'
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating repairer quote: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * READ - Get all quotations with optional filters
     * 
     * @param array $filters Optional filters (repairer_id, request_id, status, quote_id)
     * @return array Array of quotations
     */
    public function getAll($filters = []) {
        try {
            $sql = "SELECT rq.*, 
                           jr.title as job_title,
                           jr.description as job_description,
                           jr.district,
                           jr.address,
                           jr.urgency,
                           jr.finish_date,
                           jr.dateCreated as job_posted_date,
                           c.name as category_name,
                           u.f_name as customer_first_name,
                           u.l_name as customer_last_name
                    FROM RepairerQuote rq
                    LEFT JOIN JobRequest jr ON rq.request_id = jr.request_id
                    LEFT JOIN Category c ON jr.category_id = c.category_id
                    LEFT JOIN User u ON jr.user_id = u.user_id
                    WHERE 1=1";
            
            $params = [];
            
            if (isset($filters['quote_id'])) {
                $sql .= " AND rq.quote_id = ?";
                $params[] = $filters['quote_id'];
            }
            
            if (isset($filters['repairer_id'])) {
                $sql .= " AND rq.repairer_id = ?";
                $params[] = $filters['repairer_id'];
            }
            
            if (isset($filters['request_id'])) {
                $sql .= " AND rq.request_id = ?";
                $params[] = $filters['request_id'];
            }
            
            if (isset($filters['status'])) {
                $sql .= " AND rq.status = ?";
                $params[] = $filters['status'];
            }
            
            $sql .= " ORDER BY rq.dateSubmitted DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting repairer quotes: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * READ - Get single quotation by ID
     * 
     * @param int $quoteId Quote ID
     * @return array|false Quotation data or false
     */
    public function getById($quoteId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT rq.*, 
                       jr.title as job_title,
                       jr.description as job_description,
                       jr.district,
                       jr.address,
                       jr.urgency,
                       jr.finish_date,
                       jr.photos as job_photos,
                       jr.dateCreated as job_posted_date,
                       c.name as category_name,
                       u.f_name as customer_first_name,
                       u.l_name as customer_last_name,
                       u.email as customer_email
                FROM RepairerQuote rq
                LEFT JOIN JobRequest jr ON rq.request_id = jr.request_id
                LEFT JOIN Category c ON jr.category_id = c.category_id
                LEFT JOIN User u ON jr.user_id = u.user_id
                WHERE rq.quote_id = ?
            ");
            
            $stmt->execute([$quoteId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting repairer quote: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * UPDATE - Update pending quotation
     * 
     * @param int $quoteId Quote ID
     * @param int $repairerId Repairer ID (for authorization)
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update($quoteId, $repairerId, $data) {
        try {
            // First verify the quote exists, belongs to repairer, and is pending
            $stmt = $this->pdo->prepare("
                SELECT status FROM RepairerQuote 
                WHERE quote_id = ? AND repairer_id = ?
            ");
            $stmt->execute([$quoteId, $repairerId]);
            $quote = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$quote) {
                error_log("Quote not found or unauthorized access");
                return false;
            }
            
            if ($quote['status'] !== 'pending') {
                error_log("Only pending quotes can be updated");
                return false;
            }
            
            // Build update query dynamically
            $updates = [];
            $params = [];
            
            if (isset($data['quoteAmount'])) {
                $updates[] = "quoteAmount = ?";
                $params[] = $data['quoteAmount'];
            }
            
            if (isset($data['estimatedDays'])) {
                $updates[] = "estimatedDays = ?";
                $params[] = $data['estimatedDays'];
            }
            
            if (isset($data['warrantyPeriod'])) {
                $updates[] = "warrantyPeriod = ?";
                $params[] = $data['warrantyPeriod'];
            }
            
            if (isset($data['validUntil'])) {
                $updates[] = "validUntil = ?";
                $params[] = $data['validUntil'];
            }
            
            if (isset($data['materialsIncluded'])) {
                $updates[] = "materialsIncluded = ?";
                $params[] = $data['materialsIncluded'];
            }
            
            if (isset($data['message'])) {
                $updates[] = "message = ?";
                $params[] = $data['message'];
            }
            
            if (empty($updates)) {
                return false; // No fields to update
            }
            
            // Add quote_id and repairer_id to params
            $params[] = $quoteId;
            $params[] = $repairerId;
            
            $sql = "UPDATE RepairerQuote SET " . implode(", ", $updates) . 
                   " WHERE quote_id = ? AND repairer_id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log("Error updating repairer quote: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * DELETE - Delete pending quotation
     * 
     * @param int $quoteId Quote ID
     * @param int $repairerId Repairer ID (for authorization)
     * @return bool Success status
     */
    public function delete($quoteId, $repairerId) {
        try {
            // Only allow deletion of pending quotes
            $stmt = $this->pdo->prepare("
                DELETE FROM RepairerQuote 
                WHERE quote_id = ? AND repairer_id = ? AND status = 'pending'
            ");
            
            $stmt->execute([$quoteId, $repairerId]);
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Error deleting repairer quote: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if repairer has already submitted a quote for a job request
     * 
     * @param int $requestId Job request ID
     * @param int $repairerId Repairer ID
     * @return bool True if quote exists
     */
    public function hasQuoteForJob($requestId, $repairerId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM RepairerQuote 
                WHERE request_id = ? AND repairer_id = ? AND status = 'pending'
            ");
            
            $stmt->execute([$requestId, $repairerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error checking quote existence: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get count of quotes by status for a repairer
     * 
     * @param int $repairerId Repairer ID
     * @return array Status counts
     */
    public function getCountsByStatus($repairerId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT status, COUNT(*) as count 
                FROM RepairerQuote 
                WHERE repairer_id = ?
                GROUP BY status
            ");
            
            $stmt->execute([$repairerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting quote counts: " . $e->getMessage());
            return [];
        }
    }
}
?>
