<?php

class ContractModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all contracts with project and customer details
     */
    public function getAll($companyId = null) {
        $query = "SELECT 
                    c.contract_id,
                    c.project_id,
                    c.contract_number,
                    c.total_budget,
                    c.start_date,
                    c.end_date,
                    c.contract_date,
                    c.status as contract_status,
                    c.milestone_plan,
                    c.payment_method,
                    c.budget_type,
                    c.sent_to_customer,
                    c.sent_at,
                    c.customer_response,
                    c.customer_response_at,
                    c.terms_accepted,
                    c.project_title,
                    c.project_description,
                    c.project_location as location,
                    c.progress_percentage as progress,
                    u.f_name as customer_fname,
                    u.l_name as customer_lname,
                    u.email as customer_email,
                    c.company_id,
                    comp.name as company_name
                FROM contract c
                LEFT JOIN user u ON c.customer_id = u.user_id
                LEFT JOIN company comp ON c.company_id = comp.company_id";
        
        if ($companyId !== null) {
            $query .= " WHERE c.company_id = :company_id";
        }
        
        $query .= " ORDER BY c.contract_date DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($companyId !== null) {
            $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get contract by ID with all details
     */
    public function getById($contractId, $companyId = null) {
        $query = "SELECT 
                    c.*,
                    u.user_id as customer_id,
                    u.f_name as customer_fname,
                    u.l_name as customer_lname,
                    u.email as customer_email,
                    u.address as customer_address,
                    u.district as customer_district,
                    comp.company_id,
                    comp.name as company_name,
                    comp.registration_no as company_registration_no,
                    comp.address as company_address,
                    comp.contact_no as company_contact,
                    comp.email as company_email
                FROM contract c
                LEFT JOIN user u ON c.customer_id = u.user_id
                LEFT JOIN company comp ON c.company_id = comp.company_id
                WHERE c.contract_id = :contract_id";
        
        if ($companyId !== null) {
            $query .= " AND c.company_id = :company_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':contract_id', $contractId, PDO::PARAM_INT);
        
        if ($companyId !== null) {
            $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get milestones for a contract
     */
    public function getMilestones($contractId) {
        // Try contract_milestone first (new table)
        $query = "SELECT milestone_id, contract_id, milestone_number, title, 
                         description, due_date, amount as payment_amount, 
                         percentage as payment_percentage, status
                  FROM contract_milestone 
                  WHERE contract_id = :contract_id 
                  ORDER BY milestone_number ASC, due_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':contract_id', $contractId, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fallback to legacy Milestone table if empty
        if (empty($results)) {
            try {
                $query2 = "SELECT * FROM Milestone WHERE contract_id = :contract_id ORDER BY due_date ASC";
                $stmt2 = $this->conn->prepare($query2);
                $stmt2->bindParam(':contract_id', $contractId, PDO::PARAM_INT);
                $stmt2->execute();
                $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) { /* table may not exist */ }
        }
        
        return $results;
    }

    /**
     * Get contract statistics for a company
     */
    public function getStats($companyId) {
        $query = "SELECT 
                    COUNT(c.contract_id) as total_contracts,
                    SUM(CASE WHEN c.status = 'active' THEN 1 ELSE 0 END) as active_contracts,
                    SUM(CASE WHEN c.status = 'completed' THEN 1 ELSE 0 END) as completed_contracts,
                    SUM(CASE WHEN c.status = 'draft' THEN 1 ELSE 0 END) as draft_contracts,
                    SUM(CASE WHEN c.status = 'terminated' THEN 1 ELSE 0 END) as terminated_contracts,
                    SUM(c.total_budget) as total_value,
                    AVG(p.progress) as avg_progress
                FROM Contract c
                INNER JOIN Project p ON c.project_id = p.project_id
                WHERE p.company_id = :company_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new contract
     */
    public function create($data) {
        $query = "INSERT INTO Contract 
                  (project_id, milestone_plan, total_budget, start_date, end_date, 
                   contract_date, user_signature, company_signature, terms_conditions, status)
                  VALUES 
                  (:project_id, :milestone_plan, :total_budget, :start_date, :end_date,
                   :contract_date, :user_signature, :company_signature, :terms_conditions, :status)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':project_id', $data['project_id']);
        $stmt->bindParam(':milestone_plan', $data['milestone_plan']);
        $stmt->bindParam(':total_budget', $data['total_budget']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':contract_date', $data['contract_date']);
        $stmt->bindParam(':user_signature', $data['user_signature']);
        $stmt->bindParam(':company_signature', $data['company_signature']);
        $stmt->bindParam(':terms_conditions', $data['terms_conditions']);
        $stmt->bindParam(':status', $data['status']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Mark contract as sent to customer
     */
    public function markAsSent($contractId, $companyId) {
        $query = "UPDATE Contract 
                  SET sent_to_customer = 1, 
                      sent_at = NOW(),
                      status = 'sent'
                  WHERE contract_id = :contract_id
                  AND company_id = :company_id
                  AND sent_to_customer = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':contract_id', $contractId, PDO::PARAM_INT);
        $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Update contract
     */
    public function update($contractId, $data, $companyId = null) {
        // First verify the contract belongs to the company
        if ($companyId !== null) {
            $contract = $this->getById($contractId, $companyId);
            if (!$contract) {
                return false;
            }
        }
        
        $query = "UPDATE Contract SET ";
        $params = [];
        $sets = [];
        
        $allowedFields = [
            'project_title', 'project_reference', 'project_location', 'project_description',
            'scope_description', 'scope_inclusions', 'scope_exclusions', 'scope_standards',
            'materials_responsibility',
            'milestone_plan', 'total_budget', 'budget_type', 'budget_min', 'budget_max',
            'tax_inclusive', 'payment_method', 'pricing_type', 'hourly_rate', 'spending_cap',
            'advance_payment_pct',
            'late_payment_penalty', 'pause_work_clause', 'time_extension_clause',
            'variation_clause',
            'communication_channel', 'dispute_resolution',
            'start_date', 'end_date',
            'terms_conditions', 'status'
        ];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $sets[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        if (empty($sets)) {
            return false;
        }
        
        // Always update the updated_at timestamp
        $sets[] = "updated_at = NOW()";
        
        $query .= implode(', ', $sets);
        $query .= " WHERE contract_id = :contract_id";
        $params[':contract_id'] = $contractId;
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }
    
    /**
     * Update milestones for a contract (delete old, insert new)
     */
    public function updateMilestones($contractId, $milestones) {
        // Delete existing milestones
        $delStmt = $this->conn->prepare("DELETE FROM contract_milestone WHERE contract_id = :cid");
        $delStmt->execute([':cid' => $contractId]);
        
        // Also try legacy Milestone table
        $delStmt2 = $this->conn->prepare("DELETE FROM Milestone WHERE contract_id = :cid");
        try { $delStmt2->execute([':cid' => $contractId]); } catch (Exception $e) { /* table may not exist */ }
        
        // Insert new milestones
        if (!empty($milestones)) {
            $insertQuery = "INSERT INTO contract_milestone 
                (contract_id, milestone_number, title, description, due_date, amount, percentage, status) 
                VALUES (:cid, :num, :title, :desc, :date, :amount, :pct, 'pending')";
            $insertStmt = $this->conn->prepare($insertQuery);
            
            foreach ($milestones as $i => $ms) {
                $insertStmt->execute([
                    ':cid' => $contractId,
                    ':num' => $i + 1,
                    ':title' => $ms['title'] ?? $ms['milestone_name'] ?? 'Milestone ' . ($i + 1),
                    ':desc' => $ms['description'] ?? '',
                    ':date' => $ms['due_date'] ?? null,
                    ':amount' => $ms['amount'] ?? 0,
                    ':pct' => $ms['percentage'] ?? 0
                ]);
            }
            
            // Update total_milestones count
            $countStmt = $this->conn->prepare("UPDATE Contract SET total_milestones = :cnt WHERE contract_id = :cid");
            $countStmt->execute([':cnt' => count($milestones), ':cid' => $contractId]);
        }
        
        return true;
    }

    /**
     * Delete contract
     */
    public function delete($contractId, $companyId = null) {
        // First verify the contract belongs to the company
        if ($companyId !== null) {
            $contract = $this->getById($contractId, $companyId);
            if (!$contract) {
                return false;
            }
        }
        
        $query = "DELETE FROM Contract WHERE contract_id = :contract_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':contract_id', $contractId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * ===================================================================
     * PHASE 2: CREATE CONTRACT FROM ACCEPTED QUOTATION
     * ===================================================================
     * Automatically generates a contract when a quotation is accepted
     * Includes all Phase 1 business logic (budget flexibility, payment methods)
     */
    public function createFromQuotation($quotationId) {
        try {
            // 1. Get quotation details with all Phase 1 fields
            $quotationQuery = "SELECT 
                q.*,
                jr.user_id,
                jr.title as request_title,
                jr.description as request_description,
                jr.address,
                jr.district,
                jr.category_id,
                c.company_id,
                c.name as company_name
            FROM companyquotation q
            INNER JOIN jobrequest jr ON q.request_id = jr.request_id
            INNER JOIN company c ON q.company_id = c.company_id
            WHERE q.quotation_id = :quotation_id
            AND q.status = 'accepted'";
            
            $stmt = $this->conn->prepare($quotationQuery);
            $stmt->bindParam(':quotation_id', $quotationId, PDO::PARAM_INT);
            $stmt->execute();
            $quotation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$quotation) {
                throw new Exception("Quotation not found or not accepted");
            }
            
            // 2. Check if contract already exists for this quotation
            $checkQuery = "SELECT contract_id FROM contract WHERE quotation_id = :quotation_id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':quotation_id', $quotationId, PDO::PARAM_INT);
            $checkStmt->execute();
            
            if ($checkStmt->fetch()) {
                throw new Exception("Contract already exists for this quotation");
            }
            
            // 3. Create project first (if not exists)
            $projectQuery = "INSERT INTO project 
                (company_id, customer_id, title, description, location, budget, 
                 start_date, end_date, status, progress)
                VALUES 
                (:company_id, :customer_id, :title, :description, :location, :budget,
                 :start_date, :end_date, 'planned', 0)";
            
            $projStmt = $this->conn->prepare($projectQuery);
            $projStmt->execute([
                ':company_id' => $quotation['company_id'],
                ':customer_id' => $quotation['user_id'],
                ':title' => $quotation['title'],
                ':description' => $quotation['description'],
                ':location' => $quotation['address'] . ', ' . $quotation['district'],
                ':budget' => $quotation['total_amount'],
                ':start_date' => $quotation['start_date'],
                ':end_date' => $quotation['completion_date']
            ]);
            
            $projectId = $this->conn->lastInsertId();
            
            // 4. Calculate budget values based on Phase 1 logic
            $budgetType = $quotation['budget_type'] ?? 'fixed';
            $budgetMin = $quotation['budget_min'] ?? $quotation['total_amount'];
            $budgetMax = $quotation['budget_max'] ?? $quotation['total_amount'];
            $totalBudget = $budgetType === 'flexible' ? $budgetMax : $quotation['total_amount'];
            
            // 5. Calculate spending cap for T&M
            $pricingType = $quotation['pricing_type'] ?? 'fixed_price';
            $spendingCap = null;
            if ($pricingType === 'time_and_material') {
                $multiplier = $quotation['spending_cap_multiplier'] ?? 1.10;
                $spendingCap = $quotation['total_amount'] * $multiplier;
            }
            
            // 6. Determine payment method
            $paymentMethod = $quotation['payment_method'] ?? 'milestone_based';
            $milestoneplan = ($paymentMethod === 'milestone_based') ? 1 : 0;
            
            // 7. Create contract with Phase 1 + Phase 2 fields
            $contractQuery = "INSERT INTO contract 
                (quotation_id, company_id, customer_id, job_request_id, project_id,
                 milestone_plan, total_budget, budget_type, budget_min, budget_max,
                 payment_method, pricing_type, hourly_rate, spending_cap,
                 start_date, end_date, contract_date, 
                 user_signature, company_signature, status,
                 auto_generated, amount_pending, payment_status, progress_percentage)
                VALUES 
                (:quotation_id, :company_id, :customer_id, :job_request_id, :project_id,
                 :milestone_plan, :total_budget, :budget_type, :budget_min, :budget_max,
                 :payment_method, :pricing_type, :hourly_rate, :spending_cap,
                 :start_date, :end_date, :contract_date,
                 :user_signature, :company_signature, :status,
                 1, :amount_pending, 'pending', 0)";
            
            $contractStmt = $this->conn->prepare($contractQuery);
            $contractStmt->execute([
                ':quotation_id' => $quotationId,
                ':company_id' => $quotation['company_id'],
                ':customer_id' => $quotation['user_id'],
                ':job_request_id' => $quotation['request_id'],
                ':project_id' => $projectId,
                ':milestone_plan' => $milestoneplan,
                ':total_budget' => $totalBudget,
                ':budget_type' => $budgetType,
                ':budget_min' => $budgetMin,
                ':budget_max' => $budgetMax,
                ':payment_method' => $paymentMethod,
                ':pricing_type' => $pricingType,
                ':hourly_rate' => $quotation['hourly_rate'],
                ':spending_cap' => $spendingCap,
                ':start_date' => $quotation['start_date'],
                ':end_date' => $quotation['completion_date'],
                ':contract_date' => date('Y-m-d'),
                ':user_signature' => 'PENDING',
                ':company_signature' => 'AUTO_GENERATED',
                ':status' => 'draft',
                ':amount_pending' => $totalBudget
            ]);
            
            $contractId = $this->conn->lastInsertId();
            
            // 8. Update quotation status
            $updateQuotQuery = "UPDATE companyquotation SET status = 'successful' WHERE quotation_id = :quotation_id";
            $updateStmt = $this->conn->prepare($updateQuotQuery);
            $updateStmt->execute([':quotation_id' => $quotationId]);
            
            return [
                'success' => true,
                'contract_id' => $contractId,
                'project_id' => $projectId,
                'payment_method' => $paymentMethod,
                'budget_type' => $budgetType
            ];
            
        } catch (Exception $e) {
            error_log("Contract creation error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get contracts by quotation ID
     */
    public function getByQuotationId($quotationId) {
        $query = "SELECT c.*, 
                    p.title as project_title,
                    u.f_name, u.l_name, u.email,
                    comp.name as company_name
                FROM contract c
                LEFT JOIN project p ON c.project_id = p.project_id
                LEFT JOIN user u ON c.customer_id = u.user_id
                LEFT JOIN company comp ON c.company_id = comp.company_id
                WHERE c.quotation_id = :quotation_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quotation_id', $quotationId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Filter contracts by status
     */
    public function filterByStatus($status, $companyId = null) {
        $query = "SELECT 
                    c.contract_id,
                    c.project_id,
                    c.total_budget,
                    c.start_date,
                    c.end_date,
                    c.contract_date,
                    c.status as contract_status,
                    c.milestone_plan,
                    p.title as project_title,
                    p.description as project_description,
                    p.project_type,
                    p.location,
                    p.progress,
                    p.status as project_status,
                    u.f_name as customer_fname,
                    u.l_name as customer_lname,
                    u.email as customer_email,
                    comp.company_id,
                    comp.name as company_name
                FROM Contract c
                INNER JOIN Project p ON c.project_id = p.project_id
                INNER JOIN User u ON p.customer_id = u.user_id
                INNER JOIN Company comp ON p.company_id = comp.company_id
                WHERE c.status = :status";
        
        if ($companyId !== null) {
            $query .= " AND comp.company_id = :company_id";
        }
        
        $query .= " ORDER BY c.contract_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        
        if ($companyId !== null) {
            $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
