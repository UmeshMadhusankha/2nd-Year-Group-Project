<?php

class ContractModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all contracts for a specific customer (user) with company details.
     */
    public function getAllForCustomer($customerId) {
        $query = "SELECT 
                    c.contract_id,
                    c.project_id,
                    c.contract_number,
                    c.quotation_id,
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
                    c.project_location,
                    c.progress_percentage,
                    c.company_id,
                    c.chat_active,
                    comp.name as company_name,
                    COALESCE(cq.labor_cost, cq_req.labor_cost) as labor_cost,
                    COALESCE(cq.material_cost, cq_req.material_cost) as material_cost,
                    COALESCE(cq.labor_unit_label, cq_req.labor_unit_label) as labor_unit_label,
                    COALESCE(cq.material_unit_label, cq_req.material_unit_label) as material_unit_label,
                    (SELECT COUNT(*) FROM contract_chats cc
                     WHERE cc.contract_id = c.contract_id
                       AND cc.sender_type = 'company'
                       AND cc.is_read = 0) as unread_messages,
                    (
                        SELECT COUNT(*) 
                        FROM contract_milestone cm 
                        WHERE cm.contract_id = c.contract_id
                    ) as total_milestones,
                    (
                        SELECT COUNT(*) 
                        FROM contract_milestone cm 
                        WHERE cm.contract_id = c.contract_id AND cm.status = 'approved'
                    ) as completed_milestones,
                    (
                        SELECT COUNT(*) 
                        FROM contract_milestone cm 
                        WHERE cm.contract_id = c.contract_id AND cm.status = 'submitted'
                    ) as submitted_milestones
                FROM contract c
                LEFT JOIN company comp ON c.company_id = comp.company_id
                LEFT JOIN companyquotation cq ON c.quotation_id = cq.quotation_id
                LEFT JOIN companyquotation cq_req ON cq_req.quotation_id = (
                        SELECT q2.quotation_id
                        FROM companyquotation q2
                        WHERE q2.request_id = c.job_request_id
                            AND (q2.company_id = c.company_id OR q2.company_id IS NULL)
                            AND q2.status IN ('accepted', 'successful')
                        ORDER BY (q2.status = 'successful') DESC, q2.updated_at DESC, q2.created_at DESC
                        LIMIT 1
                )
                WHERE c.customer_id = :customer_id
                ORDER BY c.contract_date DESC";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':customer_id', (int)$customerId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Backward compatibility: if contract_milestone doesn't exist yet
            $fallback = "SELECT 
                    c.contract_id,
                    c.project_id,
                    c.contract_number,
                    c.quotation_id,
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
                    c.project_location,
                    c.progress_percentage,
                    c.company_id,
                    c.chat_active,
                    comp.name as company_name,
                    COALESCE(cq.labor_cost, cq_req.labor_cost) as labor_cost,
                    COALESCE(cq.material_cost, cq_req.material_cost) as material_cost,
                    COALESCE(cq.labor_unit_label, cq_req.labor_unit_label) as labor_unit_label,
                    COALESCE(cq.material_unit_label, cq_req.material_unit_label) as material_unit_label,
                    (SELECT COUNT(*) FROM contract_chats cc
                     WHERE cc.contract_id = c.contract_id
                       AND cc.sender_type = 'company'
                       AND cc.is_read = 0) as unread_messages,
                    0 as total_milestones,
                    0 as completed_milestones,
                    0 as submitted_milestones
                FROM contract c
                LEFT JOIN company comp ON c.company_id = comp.company_id
                LEFT JOIN companyquotation cq ON c.quotation_id = cq.quotation_id
                LEFT JOIN companyquotation cq_req ON cq_req.quotation_id = (
                        SELECT q2.quotation_id
                        FROM companyquotation q2
                        WHERE q2.request_id = c.job_request_id
                            AND (q2.company_id = c.company_id OR q2.company_id IS NULL)
                            AND q2.status IN ('accepted', 'successful')
                        ORDER BY (q2.status = 'successful') DESC, q2.updated_at DESC, q2.created_at DESC
                        LIMIT 1
                )
                WHERE c.customer_id = :customer_id
                ORDER BY c.contract_date DESC";

            $stmt = $this->conn->prepare($fallback);
            $stmt->bindValue(':customer_id', (int)$customerId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Get contract by ID for a specific customer (user) with all details.
     */
    public function getByIdForCustomer($contractId, $customerId) {
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
                    c_loc.address as company_address,
                    comp.contact_no as company_contact,
                    comp.email as company_email,
                    COALESCE(cq.labor_cost, cq_req.labor_cost) as labor_cost,
                    COALESCE(cq.material_cost, cq_req.material_cost) as material_cost,
                    COALESCE(cq.transport_cost, cq_req.transport_cost) as transport_cost,
                    COALESCE(cq.other_charges, cq_req.other_charges) as other_charges,
                    COALESCE(cq.labor_unit_label, cq_req.labor_unit_label) as labor_unit_label,
                    COALESCE(cq.material_unit_label, cq_req.material_unit_label) as material_unit_label
                FROM contract c
                LEFT JOIN user u ON c.customer_id = u.user_id
                LEFT JOIN company comp ON c.company_id = comp.company_id
                LEFT JOIN companyquotation cq ON c.quotation_id = cq.quotation_id
                LEFT JOIN companyquotation cq_req ON cq_req.quotation_id = (
                        SELECT q2.quotation_id
                        FROM companyquotation q2
                        WHERE q2.request_id = c.job_request_id
                            AND (q2.company_id = c.company_id OR q2.company_id IS NULL)
                            AND q2.status IN ('accepted', 'successful')
                        ORDER BY (q2.status = 'successful') DESC, q2.updated_at DESC, q2.created_at DESC
                        LIMIT 1
                )
                LEFT JOIN location c_loc ON comp.location_id = c_loc.location_id
                WHERE c.contract_id = :contract_id AND c.customer_id = :customer_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':contract_id', (int)$contractId, PDO::PARAM_INT);
        $stmt->bindValue(':customer_id', (int)$customerId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all contracts with project and customer details
     */
    public function getAll($companyId = null) {
        $query = "SELECT 
                    c.contract_id,
                    c.project_id,
                    c.contract_number,
                    c.quotation_id,
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
                    p.status as project_status,
                    p.start_date as project_start_date,
                    u.f_name as customer_fname,
                    u.l_name as customer_lname,
                    u.email as customer_email,
                    c.company_id,
                    c.chat_active,
                    comp.name as company_name,
                                        COALESCE(cq.labor_cost, cq_req.labor_cost) as labor_cost,
                                        COALESCE(cq.material_cost, cq_req.material_cost) as material_cost,
                                        COALESCE(cq.transport_cost, cq_req.transport_cost) as transport_cost,
                                        COALESCE(cq.other_charges, cq_req.other_charges) as other_charges,
                                        COALESCE(cq.labor_unit_label, cq_req.labor_unit_label) as labor_unit_label,
                                        COALESCE(cq.material_unit_label, cq_req.material_unit_label) as material_unit_label,
                    (SELECT COUNT(*) FROM contract_chats cc 
                     WHERE cc.contract_id = c.contract_id 
                       AND cc.sender_type = 'customer' 
                       AND cc.is_read = 0) as unread_messages
                FROM contract c
                LEFT JOIN project p ON c.project_id = p.project_id
                LEFT JOIN user u ON c.customer_id = u.user_id
                                LEFT JOIN company comp ON c.company_id = comp.company_id
                                LEFT JOIN companyquotation cq ON c.quotation_id = cq.quotation_id
                                LEFT JOIN companyquotation cq_req ON cq_req.quotation_id = (
                                        SELECT q2.quotation_id
                                        FROM companyquotation q2
                                        WHERE q2.request_id = c.job_request_id
                                            AND (q2.company_id = c.company_id OR q2.company_id IS NULL)
                                            AND q2.status IN ('accepted', 'successful')
                                        ORDER BY (q2.status = 'successful') DESC, q2.updated_at DESC, q2.created_at DESC
                                        LIMIT 1
                                )";
        
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
                    c_loc.address as company_address,
                    comp.contact_no as company_contact,
                    comp.email as company_email,
                    COALESCE(cq.labor_cost, cq_req.labor_cost) as labor_cost,
                    COALESCE(cq.material_cost, cq_req.material_cost) as material_cost,
                    COALESCE(cq.transport_cost, cq_req.transport_cost) as transport_cost,
                    COALESCE(cq.other_charges, cq_req.other_charges) as other_charges,
                    COALESCE(cq.labor_unit_label, cq_req.labor_unit_label) as labor_unit_label,
                    COALESCE(cq.material_unit_label, cq_req.material_unit_label) as material_unit_label,
                    (SELECT COUNT(*) FROM contract_chats cc 
                     WHERE cc.contract_id = c.contract_id 
                       AND cc.sender_type = 'customer' 
                       AND cc.is_read = 0) as unread_messages
                FROM contract c
                LEFT JOIN user u ON c.customer_id = u.user_id
                LEFT JOIN company comp ON c.company_id = comp.company_id
                LEFT JOIN companyquotation cq ON c.quotation_id = cq.quotation_id
                LEFT JOIN companyquotation cq_req ON cq_req.quotation_id = (
                        SELECT q2.quotation_id
                        FROM companyquotation q2
                        WHERE q2.request_id = c.job_request_id
                            AND (q2.company_id = c.company_id OR q2.company_id IS NULL)
                            AND q2.status IN ('accepted', 'successful')
                        ORDER BY (q2.status = 'successful') DESC, q2.updated_at DESC, q2.created_at DESC
                        LIMIT 1
                )
                LEFT JOIN location c_loc ON comp.location_id = c_loc.location_id
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
                    percentage as payment_percentage, status,
                    unit_label, unit_rate, actual_unit_rate,
                    estimated_quantity, actual_quantity, actual_amount,
                    proof_of_work, proof_files, comments,
                    submitted_at, completed_at, approved_at
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
        // Enforce platform chat only (no email communication)
        if (is_array($data)) {
            $data['communication_channel'] = 'system';
        }

        $allowedFields = [
            'project_id', 'quotation_id', 'customer_id', 'company_id', 'job_request_id',
            'project_title', 'project_reference', 'project_location', 'project_description',
            'scope_description', 'scope_inclusions', 'scope_exclusions', 'scope_standards',
            'materials_responsibility',
            'milestone_plan', 'total_budget', 'budget_type', 'budget_min', 'budget_max',
            'tax_inclusive', 'payment_method', 'pricing_type', 'hourly_rate', 'spending_cap',
            'advance_payment_pct',
            'late_payment_penalty', 'pause_work_clause', 'time_extension_clause',
            'variation_clause',
            'communication_channel', 'dispute_resolution',
            'start_date', 'end_date', 'contract_date',
            'terms_conditions', 'status', 'user_signature', 'company_signature',
            'auto_generated', 'amount_pending', 'payment_status', 'progress_percentage',
            'undo_deadline', 'undo_requested', 'chat_active', 'escrow_enabled'
        ];

        $fields = [];
        $placeholders = [];
        $params = [];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = $field;
                $placeholders[] = ":$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $query = "INSERT INTO Contract (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute($params)) {
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
                      status = 'sent',
                      chat_active = 1
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

        // Enforce platform chat only (no email communication)
        if (is_array($data)) {
            $data['communication_channel'] = 'system';
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
                (contract_id, milestone_number, title, description, due_date,
                 amount, percentage, unit_label, unit_rate, estimated_quantity, status) 
                VALUES (:cid, :num, :title, :desc, :date,
                        :amount, :pct, :unit_label, :unit_rate, :estimated_quantity, 'pending')";
            $insertStmt = $this->conn->prepare($insertQuery);
            
            foreach ($milestones as $i => $ms) {
                $insertStmt->execute([
                    ':cid'                => $contractId,
                    ':num'                => $i + 1,
                    ':title'              => $ms['title'] ?? $ms['milestone_name'] ?? 'Milestone ' . ($i + 1),
                    ':desc'               => $ms['description'] ?? '',
                    ':date'               => $ms['due_date'] ?? null,
                    ':amount'             => $ms['amount'] ?? 0,
                    ':pct'                => $ms['percentage'] ?? 0,
                    ':unit_label'         => $ms['unit_label'] ?? null,
                    ':unit_rate'          => $ms['unit_rate'] ?? null,
                    ':estimated_quantity' => $ms['estimated_quantity'] ?? null,
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
     * Delete contract and its milestones (used for cancellation)
     */
    public function deleteWithMilestones($contractId, $companyId = null) {
        if ($companyId !== null) {
            $contract = $this->getById($contractId, $companyId);
            if (!$contract) {
                return false;
            }
        }

        try {
            $this->conn->beginTransaction();

            $delMilestones = $this->conn->prepare("DELETE FROM contract_milestone WHERE contract_id = :cid");
            $delMilestones->execute([':cid' => $contractId]);

            $delContract = $this->conn->prepare("DELETE FROM Contract WHERE contract_id = :contract_id");
            $delContract->execute([':contract_id' => $contractId]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error deleting contract with milestones: " . $e->getMessage());
            return false;
        }
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
                jr_loc.address,
                jr_loc.district,
                jr.category_id,
                c.company_id,
                c.name as company_name
            FROM companyquotation q
            INNER JOIN jobrequest jr ON q.request_id = jr.request_id
            LEFT JOIN location jr_loc ON jr.location_id = jr_loc.location_id
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
            
            // 6. Determine payment method (map quotation values to contract ENUM)
            $paymentMethodMap = [
                'milestone' => 'milestone_based',
                'milestone_based' => 'milestone_based',
                'full_upfront' => 'full_upfront',
                '50_50' => '50_50',
                '30_70' => '30_70',
                'completion' => 'completion'
            ];
            $rawMethod = $quotation['payment_method'] ?? 'milestone_based';
            $paymentMethod = $paymentMethodMap[$rawMethod] ?? 'milestone_based';
            $milestoneplan = ($paymentMethod === 'milestone_based') ? 1 : 0;
            
            // 7. Create contract with Phase 1 + Phase 2 fields
            $contractQuery = "INSERT INTO contract 
                (quotation_id, company_id, customer_id, job_request_id, project_id,
                 milestone_plan, total_budget, budget_type, budget_min, budget_max,
                 payment_method, pricing_type, hourly_rate, spending_cap,
                 start_date, end_date, contract_date, 
                 user_signature, company_signature, status,
                 auto_generated, amount_pending, payment_status, progress_percentage,
                 undo_deadline, undo_requested, chat_active, escrow_enabled)
                VALUES 
                (:quotation_id, :company_id, :customer_id, :job_request_id, :project_id,
                 :milestone_plan, :total_budget, :budget_type, :budget_min, :budget_max,
                 :payment_method, :pricing_type, :hourly_rate, :spending_cap,
                 :start_date, :end_date, :contract_date,
                 :user_signature, :company_signature, :status,
                 1, :amount_pending, 'pending', 0,
                 :undo_deadline, :undo_requested, :chat_active, :escrow_enabled)";
            
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
                ':amount_pending' => $totalBudget,
                
                // Phase 2 Fields
                ':undo_deadline' => date('Y-m-d H:i:s', strtotime('+24 hours')),
                ':undo_requested' => 0,  // FALSE = undo NOT requested yet = undo IS available
                ':chat_active' => 0,
                ':escrow_enabled' => 1
            ]);
            
            $contractId = $this->conn->lastInsertId();
            
            // 8. Update quotation status
            $updateQuotQuery = "UPDATE companyquotation SET status = 'successful' WHERE quotation_id = :quotation_id";
            $updateStmt = $this->conn->prepare($updateQuotQuery);
            $updateStmt->execute([':quotation_id' => $quotationId]);

            // 9. Update Job Request status to 'in_progress'
            $updateJobQuery = "UPDATE jobrequest SET status = 'in_progress' WHERE request_id = :request_id";
            $updateJobStmt = $this->conn->prepare($updateJobQuery);
            $updateJobStmt->execute([':request_id' => $quotation['request_id']]);
            
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

    /**
     * Check if undo is available and update status
     */
    public function checkUndoStatus($contractId) {
        try {
            $query = "SELECT undo_deadline, undo_requested, status FROM contract WHERE contract_id = :contract_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':contract_id' => $contractId]);
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fallback if undo_requested column doesn't exist
            $query = "SELECT undo_deadline, status FROM contract WHERE contract_id = :contract_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':contract_id' => $contractId]);
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if (!$contract) return false;
        
        // Can't undo if already terminated
        if ($contract['status'] === 'terminated') return false;
        
        // Check deadline
        if (!$contract['undo_deadline'] || strtotime($contract['undo_deadline']) <= time()) {
            return false;
        }

        // Check if already requested (if column exists)
        if (isset($contract['undo_requested']) && $contract['undo_requested']) {
            return false;
        }
        
        return true;
    }

    /**
     * Activate chat for a contract
     */
    public function activateChat($contractId) {
        $query = "UPDATE contract SET chat_active = 1 WHERE contract_id = :contract_id";
        return $this->conn->prepare($query)->execute([':contract_id' => $contractId]);
    }

    /**
     * Cancel a contract (Undo functionality)
     * Reverts Contract, Quotation, and JobRequest statuses
     */
    public function cancelContract($contractId, $reason = '') {
        try {
            // 1. Check undo status
            if (!$this->checkUndoStatus($contractId)) {
                throw new Exception("Cancellation window has expired or is not available.");
            }

            // 2. Get contract details to find quotation and job request
            $contractQuery = "SELECT quotation_id, job_request_id FROM contract WHERE contract_id = :contract_id";
            $stmt = $this->conn->prepare($contractQuery);
            $stmt->execute([':contract_id' => $contractId]);
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$contract) {
                throw new Exception("Contract not found.");
            }

            // 3. Begin transaction
            $this->conn->beginTransaction();

            // 4. Update contract status to 'terminated'
            // setting undo_requested to 1 
            $updateContract = "UPDATE contract SET status = 'terminated', undo_requested = 1, terms_conditions = CONCAT(IFNULL(terms_conditions,''), '\n\n[CANCELLED]: ', :reason) WHERE contract_id = :contract_id";
            $stmtContract = $this->conn->prepare($updateContract);
            $stmtContract->execute([':reason' => $reason, ':contract_id' => $contractId]);

            // 5. Revert company quotation status to 'pending' (so it can be accepted again or rejected)
            $updateQuote = "UPDATE companyquotation SET status = 'pending' WHERE quotation_id = :quotation_id";
            $this->conn->prepare($updateQuote)->execute([':quotation_id' => $contract['quotation_id']]);

            // 6. Revert job request status to 'pending'
            $updateJob = "UPDATE jobrequest SET status = 'pending' WHERE request_id = :request_id";
            $this->conn->prepare($updateJob)->execute([':request_id' => $contract['job_request_id']]);

            // 7. Commit
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error cancelling contract: " . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Mark milestone as submitted by company (waiting for customer verification).
     * Accepts the actual units consumed so the billing amount can be pre-computed.
     */
    public function markMilestoneCompleted($milestoneId, $proofFiles = null, $comments = null, $actualQuantity = null, $actualUnitRate = null) {
        // Fetch unit_rate so we can calculate actual_amount
        $fetchStmt = $this->conn->prepare(
            "SELECT unit_rate, estimated_quantity FROM contract_milestone WHERE milestone_id = ?"
        );
        $fetchStmt->execute([$milestoneId]);
        $row = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        $actualAmount = null;
        if ($row && $actualQuantity !== null && $actualQuantity !== '') {
            $agreedRate = (float)($row['unit_rate'] ?? 0);
            $effectiveRate = $agreedRate;
            if ($actualUnitRate !== null && $actualUnitRate !== '' && (float)$actualUnitRate > 0) {
                $effectiveRate = (float)$actualUnitRate;
            }

            $qty  = (float)$actualQuantity;
            if ($effectiveRate > 0) {
                $actualAmount = $effectiveRate * $qty;
            }
        }

        $query = "UPDATE contract_milestone 
                  SET status = 'submitted', 
                      completed_at = NOW(), 
                      proof_files = :proof, 
                      comments = :comments,
                      actual_quantity = :actual_qty,
                      actual_unit_rate = :actual_unit_rate,
                      actual_amount   = :actual_amt
                  WHERE milestone_id = :id AND status IN ('pending', 'rejected', 'in_progress')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':proof',      $proofFiles);
        $stmt->bindValue(':comments',   $comments);
        $stmt->bindValue(':actual_qty', ($actualQuantity !== null && $actualQuantity !== '') ? (float)$actualQuantity : null);
        $stmt->bindValue(':actual_unit_rate', ($actualUnitRate !== null && $actualUnitRate !== '') ? (float)$actualUnitRate : null);
        $stmt->bindValue(':actual_amt', $actualAmount);
        $stmt->bindValue(':id',         $milestoneId);
        
        return $stmt->execute();
    }

    /**
     * Approve milestone by customer.
     * Uses actual_amount (unit_rate × actual_quantity) for billing.
     * No escrow required — payment is direct after verification.
     */
    public function approveMilestone($milestoneId) {
        try {
            $this->conn->beginTransaction();

            // 1. Fetch milestone data before approve
            $mFetch = $this->conn->prepare(
                "SELECT contract_id, actual_amount, amount FROM contract_milestone WHERE milestone_id = ?"
            );
            $mFetch->execute([$milestoneId]);
            $mData = $mFetch->fetch(PDO::FETCH_ASSOC);

            if (!$mData) {
                throw new Exception("Milestone not found.");
            }

            // 2. Update milestone status to approved
            $upd = $this->conn->prepare(
                "UPDATE contract_milestone 
                 SET status = 'approved', approved_at = NOW() 
                 WHERE milestone_id = :id AND status = 'submitted'"
            );
            $upd->bindValue(':id', $milestoneId);
            $upd->execute();

            if ($upd->rowCount() === 0) {
                $chk = $this->conn->prepare("SELECT status FROM contract_milestone WHERE milestone_id = ?");
                $chk->execute([$milestoneId]);
                if ($chk->fetchColumn() === 'approved') {
                    $this->conn->commit();
                    return true; // idempotent
                }
                throw new Exception("Milestone not found or not in submitted status.");
            }

            // 3. Update contract amount_paid / amount_pending using actual billed amount
            $contractId = $mData['contract_id'];
            // Prefer actual_amount (unit-based). Fall back to estimated amount.
            $billedAmount = ($mData['actual_amount'] !== null) ? (float)$mData['actual_amount'] : (float)$mData['amount'];

            if ($billedAmount > 0) {
                $this->conn->prepare(
                    "UPDATE contract
                     SET amount_paid    = COALESCE(amount_paid, 0) + :billed,
                         amount_pending = GREATEST(0, COALESCE(amount_pending, 0) - :billed2)
                     WHERE contract_id  = :cid"
                )->execute([':billed' => $billedAmount, ':billed2' => $billedAmount, ':cid' => $contractId]);
            }

            // 4. Recalculate progress
            $progStmt = $this->conn->prepare(
                "SELECT COUNT(*) as total,
                        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved
                 FROM contract_milestone WHERE contract_id = ?"
            );
            $progStmt->execute([$contractId]);
            $stats = $progStmt->fetch(PDO::FETCH_ASSOC);

            if ($stats && $stats['total'] > 0) {
                $newProgress = (int)round(($stats['approved'] / $stats['total']) * 100);
                $this->conn->prepare(
                    "UPDATE contract SET progress_percentage = ? WHERE contract_id = ?"
                )->execute([$newProgress, $contractId]);

                if ($newProgress >= 100) {
                    $this->conn->prepare(
                        "UPDATE contract SET status = 'completed' WHERE contract_id = ?"
                    )->execute([$contractId]);
                }
            }

            $this->conn->commit();
            return ['success' => true, 'billed_amount' => $billedAmount];

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error approving milestone: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject milestone by customer
     */
    public function rejectMilestone($milestoneId, $reason) {
        $query = "UPDATE contract_milestone 
                  SET status = 'rejected', 
                      comments = CONCAT(IFNULL(comments, ''), '\n[REJECTION]: ', :reason)
                  WHERE milestone_id = :id AND status = 'submitted'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':reason', $reason);
        $stmt->bindValue(':id', $milestoneId);
        
        return $stmt->execute();
    }
}
