<?php
/**
 * CompanyQuotation Model
 * Handles all CRUD operations for company quotations
 */

class CompanyQuotation {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Create a new company quotation
     * @param array $data Quotation data
     * @return int|false The quotation ID if successful, false otherwise
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO CompanyQuotation (
                request_id, user_id, title, description,
                labor_cost, material_cost, transport_cost, other_charges,
                total_amount, start_date, completion_date, estimated_duration,
                payment_terms, warranty_period, additional_terms, status
            ) VALUES (
                :request_id, :user_id, :title, :description,
                :labor_cost, :material_cost, :transport_cost, :other_charges,
                :total_amount, :start_date, :completion_date, :estimated_duration,
                :payment_terms, :warranty_period, :additional_terms, :status
            )";
            
            $stmt = $this->pdo->prepare($sql);
            
            $status = $data['status'] ?? 'pending';
            
            $stmt->execute([
                ':request_id' => $data['request_id'],
                ':user_id' => $data['user_id'],
                ':title' => $data['title'],
                ':description' => $data['description'] ?? null,
                ':labor_cost' => $data['labor_cost'],
                ':material_cost' => $data['material_cost'],
                ':transport_cost' => $data['transport_cost'] ?? 0.00,
                ':other_charges' => $data['other_charges'] ?? 0.00,
                ':total_amount' => $data['total_amount'],
                ':start_date' => $data['start_date'],
                ':completion_date' => $data['completion_date'],
                ':estimated_duration' => $data['estimated_duration'],
                ':payment_terms' => $data['payment_terms'] ?? null,
                ':warranty_period' => $data['warranty_period'] ?? null,
                ':additional_terms' => $data['additional_terms'] ?? null,
                ':status' => $status
            ]);
            
            return $this->pdo->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("Error creating company quotation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all quotations with optional filters
     * @param array $filters Optional filters (company_id, request_id, status)
     * @return array Array of quotations
     */
    public function getAll($filters = []) {
        try {
            $sql = "SELECT 
                cq.*,
                jr.title as job_title,
                jr.description as job_description,
                jr.district,
                jr.address,
                jr.finish_date,
                jr.urgency,
                jr.service_provider_type,
                c.name as category_name,
                u.f_name as customer_fname,
                u.l_name as customer_lname,
                u.email as customer_email
            FROM CompanyQuotation cq
            INNER JOIN JobRequest jr ON cq.request_id = jr.request_id
            INNER JOIN User u ON jr.user_id = u.user_id
            LEFT JOIN Category c ON jr.category_id = c.category_id
            WHERE 1=1";
            
            $params = [];
            
            if (!empty($filters['quotation_id'])) {
                $sql .= " AND cq.quotation_id = :quotation_id";
                $params[':quotation_id'] = $filters['quotation_id'];
            }
            
            if (!empty($filters['request_id'])) {
                $sql .= " AND cq.request_id = :request_id";
                $params[':request_id'] = $filters['request_id'];
            }
            
            if (!empty($filters['user_id'])) {
                $sql .= " AND cq.user_id = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }
            
            if (!empty($filters['status'])) {
                $sql .= " AND cq.status = :status";
                $params[':status'] = $filters['status'];
            }
            
            $sql .= " ORDER BY cq.created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error fetching company quotations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get a single quotation by ID
     * @param int $quotation_id
     * @return array|false Quotation data or false if not found
     */
    public function getById($quotation_id) {
        try {
            $sql = "SELECT 
                cq.*,
                jr.title as job_title,
                jr.description as job_description,
                jr.district,
                jr.address,
                jr.finish_date,
                jr.urgency,
                jr.service_provider_type,
                c.name as category_name,
                u.f_name as customer_fname,
                u.l_name as customer_lname,
                u.email as customer_email
            FROM CompanyQuotation cq
            INNER JOIN JobRequest jr ON cq.request_id = jr.request_id
            INNER JOIN User u ON jr.user_id = u.user_id
            LEFT JOIN Category c ON jr.category_id = c.category_id
            WHERE cq.quotation_id = :quotation_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':quotation_id' => $quotation_id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error fetching quotation by ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing quotation
     * @param int $quotation_id
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update($quotation_id, $data) {
        try {
            // First check if quotation exists and is in pending status
            $existing = $this->getById($quotation_id);
            if (!$existing || $existing['status'] !== 'pending') {
                return false;
            }
            
            $sql = "UPDATE CompanyQuotation SET
                title = :title,
                description = :description,
                labor_cost = :labor_cost,
                material_cost = :material_cost,
                transport_cost = :transport_cost,
                other_charges = :other_charges,
                total_amount = :total_amount,
                start_date = :start_date,
                completion_date = :completion_date,
                estimated_duration = :estimated_duration,
                payment_terms = :payment_terms,
                warranty_period = :warranty_period,
                additional_terms = :additional_terms
            WHERE quotation_id = :quotation_id AND status = 'pending'";
            
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([
                ':quotation_id' => $quotation_id,
                ':title' => $data['title'],
                ':description' => $data['description'] ?? null,
                ':labor_cost' => $data['labor_cost'],
                ':material_cost' => $data['material_cost'],
                ':transport_cost' => $data['transport_cost'] ?? 0.00,
                ':other_charges' => $data['other_charges'] ?? 0.00,
                ':total_amount' => $data['total_amount'],
                ':start_date' => $data['start_date'],
                ':completion_date' => $data['completion_date'],
                ':estimated_duration' => $data['estimated_duration'],
                ':payment_terms' => $data['payment_terms'] ?? null,
                ':warranty_period' => $data['warranty_period'] ?? null,
                ':additional_terms' => $data['additional_terms'] ?? null
            ]);
            
        } catch (PDOException $e) {
            error_log("Error updating quotation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a quotation (only if pending)
     * @param int $quotation_id
     * @return bool Success status
     */
    public function delete($quotation_id) {
        try {
            // Only allow deletion of pending quotations
            $sql = "DELETE FROM CompanyQuotation 
                    WHERE quotation_id = :quotation_id 
                    AND status = 'pending'";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':quotation_id' => $quotation_id]);
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Error deleting quotation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a quotation already exists for a request
     * @param int $request_id
     * @return bool
     */
    public function hasQuotationForRequest($request_id) {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM CompanyQuotation 
                    WHERE request_id = :request_id 
                    AND status = 'pending'";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':request_id' => $request_id]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("Error checking existing quotation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update quotation status
     * @param int $quotation_id
     * @param string $status New status
     * @return bool Success status
     */
    public function updateStatus($quotation_id, $status) {
        try {
            $validStatuses = ['pending', 'accepted', 'rejected', 'successful'];
            if (!in_array($status, $validStatuses)) {
                return false;
            }
            
            $sql = "UPDATE CompanyQuotation 
                    SET status = :status 
                    WHERE quotation_id = :quotation_id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':quotation_id' => $quotation_id,
                ':status' => $status
            ]);
            
        } catch (PDOException $e) {
            error_log("Error updating quotation status: " . $e->getMessage());
            return false;
        }
    }
}
