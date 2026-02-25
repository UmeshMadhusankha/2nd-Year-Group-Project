<?php

class PaymentModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create a payment record
     */
    public function create($data) {
        $sql = "INSERT INTO milestonepayment (
                    contract_id, milestone_id, amount, payment_type, 
                    payment_percentage, description, status, 
                    escrow_enabled, paid_by, paid_to, paid_at, 
                    transaction_id, created_at
                ) VALUES (
                    :contract_id, :milestone_id, :amount, :payment_type, 
                    :payment_percentage, :description, :status, 
                    :escrow_enabled, :paid_by, :paid_to, :paid_at, 
                    :transaction_id, NOW()
                )";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':contract_id', $data['contract_id']);
        $stmt->bindValue(':milestone_id', $data['milestone_id'] ?? null);
        $stmt->bindValue(':amount', $data['amount']);
        $stmt->bindValue(':payment_type', $data['payment_type'] ?? 'milestone');
        $stmt->bindValue(':payment_percentage', $data['payment_percentage'] ?? null);
        $stmt->bindValue(':description', $data['description'] ?? null);
        $stmt->bindValue(':status', $data['status'] ?? 'pending');
        $stmt->bindValue(':escrow_enabled', $data['escrow_enabled'] ?? 0);
        $stmt->bindValue(':paid_by', $data['paid_by'] ?? null);
        $stmt->bindValue(':paid_to', $data['paid_to'] ?? null);
        $stmt->bindValue(':paid_at', $data['paid_at'] ?? null); // If paid immediately
        $stmt->bindValue(':transaction_id', $data['transaction_id'] ?? null);
        
        if ($stmt->execute()) {
            $paymentId = $this->conn->lastInsertId();
            
            // If escrow enabled and status is 'held_escrow', set paid_to_escrow_at
            if (($data['escrow_enabled'] ?? 0) && ($data['status'] ?? '') === 'held_escrow') {
                $this->updateEscrowStatus($paymentId, 'held');
            }
            
            return $paymentId;
        }
        return false;
    }

    /**
     * Get payment by ID
     */
    public function getById($paymentId) {
        $sql = "SELECT * FROM milestonepayment WHERE payment_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $paymentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get payments for a contract
     */
    public function getByContractId($contractId) {
        $sql = "SELECT * FROM milestonepayment WHERE contract_id = :contract_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':contract_id' => $contractId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update Escrow Status
     */
    public function updateEscrowStatus($paymentId, $action) {
        $sql = "";
        $params = [':id' => $paymentId];
        
        if ($action === 'held') {
            $sql = "UPDATE milestonepayment SET paid_to_escrow_at = NOW(), status = 'held_escrow' WHERE payment_id = :id";
        } elseif ($action === 'released') {
            $sql = "UPDATE milestonepayment SET escrow_released_at = NOW(), status = 'released' WHERE payment_id = :id";
        } elseif ($action === 'refunded') {
            $sql = "UPDATE milestonepayment SET escrow_refunded_at = NOW(), status = 'refunded' WHERE payment_id = :id";
        }
        
        if ($sql) {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        }
        return false;
    }
}
