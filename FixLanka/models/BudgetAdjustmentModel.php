<?php

class BudgetAdjustmentModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a new budget adjustment request
     */
    public function create($data) {
        $query = "INSERT INTO contract_budget_adjustments (
                    contract_id, original_amount, requested_amount, 
                    adjustment_amount, adjustment_percentage, 
                    reason, justification, supporting_documents, 
                    status, requested_by, requested_at
                ) VALUES (
                    :contract_id, :original_amount, :requested_amount, 
                    :adjustment_amount, :adjustment_percentage, 
                    :reason, :justification, :supporting_documents, 
                    'pending', :requested_by, NOW()
                )";
        
        $stmt = $this->conn->prepare($query);
        
        // Use bindValue for stricter control
        $stmt->bindValue(':contract_id', $data['contract_id']);
        $stmt->bindValue(':original_amount', $data['original_amount']);
        $stmt->bindValue(':requested_amount', $data['requested_amount']);
        $stmt->bindValue(':adjustment_amount', $data['adjustment_amount']);
        $stmt->bindValue(':adjustment_percentage', $data['adjustment_percentage']);
        $stmt->bindValue(':reason', $data['reason']);
        $stmt->bindValue(':justification', $data['justification']);
        $stmt->bindValue(':supporting_documents', $data['supporting_documents'] ?? null);
        $stmt->bindValue(':requested_by', $data['requested_by']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Get adjustment by ID
     */
    public function getById($adjustmentId) {
        $sql = "SELECT * FROM contract_budget_adjustments WHERE adjustment_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $adjustmentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get adjustments for a contract
     */
    public function getByContractId($contractId) {
        $sql = "SELECT * FROM contract_budget_adjustments WHERE contract_id = :contract_id ORDER BY requested_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':contract_id' => $contractId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Review adjustment (Approve/Reject)
     */
    public function review($adjustmentId, $status, $reviewerId, $notes = null) {
        if (!in_array($status, ['approved', 'rejected'])) {
            return false;
        }

        $sql = "UPDATE contract_budget_adjustments SET 
                    status = :status, 
                    reviewed_at = NOW(), 
                    reviewed_by = :reviewed_by, 
                    review_notes = :review_notes";
        
        if ($status === 'approved') {
            $sql .= ", approved_at = NOW() ";
        } else {
            $sql .= ", rejected_at = NOW(), rejection_reason = :rejection_reason ";
        }
        
        $sql .= " WHERE adjustment_id = :adjustment_id";
        
        $stmt = $this->conn->prepare($sql);
        $params = [
            ':status' => $status,
            ':reviewed_by' => $reviewerId,
            ':review_notes' => $notes,
            ':adjustment_id' => $adjustmentId
        ];
        
        if ($status === 'rejected') {
            $params[':rejection_reason'] = $notes;
        }
        
        $success = $stmt->execute($params);
        
        if ($success && $status === 'approved') {
            // Update Contract Total Budget
            $this->updateContractBudget($adjustmentId);
        }
        
        return $success;
    }

    /**
     * Update contract budget after approval
     */
    private function updateContractBudget($adjustmentId) {
        // Get the adjustment details
        $adjustment = $this->getById($adjustmentId);
        
        if ($adjustment) {
            $contractId = $adjustment['contract_id'];
            $newAmount = $adjustment['requested_amount'];
            
            // Update contract table
            $sql = "UPDATE contract SET total_budget = :new_amount, updated_at = NOW() WHERE contract_id = :contract_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':new_amount' => $newAmount, ':contract_id' => $contractId]);
            
            // Also update project budget if linked
            // (Assuming logic: contract update should reflect on project or vice versa, implementation plan says project budget exists)
            // Let's get project_id from contract
            $cStmt = $this->conn->prepare("SELECT project_id FROM contract WHERE contract_id = :cid");
            $cStmt->execute([':cid' => $contractId]);
            $contract = $cStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($contract && $contract['project_id']) {
                $pSql = "UPDATE project SET budget = :new_amount WHERE project_id = :pid";
                $this->conn->prepare($pSql)->execute([':new_amount' => $newAmount, ':pid' => $contract['project_id']]);
            }
        }
    }
}
