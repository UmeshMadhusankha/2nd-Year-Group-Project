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
                INNER JOIN Company comp ON p.company_id = comp.company_id";
        
        if ($companyId !== null) {
            $query .= " WHERE comp.company_id = :company_id";
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
                    p.title as project_title,
                    p.description as project_description,
                    p.project_type,
                    p.location,
                    p.budget,
                    p.final_cost,
                    p.progress,
                    p.status as project_status,
                    p.start_date as project_start_date,
                    p.end_date as project_end_date,
                    u.user_id as customer_id,
                    u.f_name as customer_fname,
                    u.l_name as customer_lname,
                    u.email as customer_email,
                    u.address as customer_address,
                    u.district as customer_district,
                    comp.company_id,
                    comp.name as company_name
                FROM Contract c
                INNER JOIN Project p ON c.project_id = p.project_id
                INNER JOIN User u ON p.customer_id = u.user_id
                INNER JOIN Company comp ON p.company_id = comp.company_id
                WHERE c.contract_id = :contract_id";
        
        if ($companyId !== null) {
            $query .= " AND comp.company_id = :company_id";
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
        $query = "SELECT * FROM Milestone 
                  WHERE contract_id = :contract_id 
                  ORDER BY due_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':contract_id', $contractId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        
        $allowedFields = ['milestone_plan', 'total_budget', 'start_date', 'end_date', 
                          'end_date', 'terms_conditions', 'status'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $sets[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        if (empty($sets)) {
            return false;
        }
        
        $query .= implode(', ', $sets);
        $query .= " WHERE contract_id = :contract_id";
        $params[':contract_id'] = $contractId;
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
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
