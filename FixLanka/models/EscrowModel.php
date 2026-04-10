<?php
/**
 * EscrowModel handles wallet balances and transactions.
 */
class EscrowModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get or Create Wallet for User or Company
     * @param int $ownerId
     * @param string $ownerType 'user' or 'company'
     */
    public function getWallet($ownerId, $ownerType = 'user') {
        $col = ($ownerType === 'company') ? 'company_id' : 'user_id';
        
        $stmt = $this->conn->prepare("SELECT * FROM escrow_wallet WHERE $col = ?");
        $stmt->execute([$ownerId]);
        $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$wallet) {
            $stmt = $this->conn->prepare("INSERT INTO escrow_wallet ($col, balance) VALUES (?, 0.00)");
            $stmt->execute([$ownerId]);
            $wallet_id = $this->conn->lastInsertId();
            return ['wallet_id' => $wallet_id, $col => $ownerId, 'balance' => 0.00];
        }

        return $wallet;
    }

    /**
     * Deposit funds into wallet
     */
    public function deposit($ownerId, $amount, $ownerType = 'user', $description = 'Deposit') {
        $startedTransaction = false;
        try {
            if (!$this->conn->inTransaction()) {
                $this->conn->beginTransaction();
                $startedTransaction = true;
            }
            $wallet = $this->getWallet($ownerId, $ownerType);
            
            // Update Balance
            $stmt = $this->conn->prepare("UPDATE escrow_wallet SET balance = balance + ? WHERE wallet_id = ?");
            $stmt->execute([$amount, $wallet['wallet_id']]);

            // Log Transaction
            $this->logTransaction($wallet['wallet_id'], $amount, 'deposit', $description);

            if ($startedTransaction) $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($startedTransaction && $this->conn->inTransaction()) $this->conn->rollBack();
            error_log("Deposit Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hold funds for a milestone (From Customer Wallet -> Milestone Escrow)
     */
    public function holdFundsForMilestone($userId, $milestoneId, $amount) {
        $startedTransaction = false;
        try {
            if (!$this->conn->inTransaction()) {
                $this->conn->beginTransaction();
                $startedTransaction = true;
            }
            $wallet = $this->getWallet($userId, 'user');

            if ($wallet['balance'] < $amount) {
                throw new Exception("Insufficient funds.");
            }

            // Deduct from Wallet
            $stmt = $this->conn->prepare("UPDATE escrow_wallet SET balance = balance - ? WHERE wallet_id = ?");
            $stmt->execute([$amount, $wallet['wallet_id']]);

            // Add to Milestone Escrow
            $stmt = $this->conn->prepare("UPDATE contract_milestone SET escrow_held = escrow_held + ? WHERE milestone_id = ?");
            $stmt->execute([$amount, $milestoneId]);

            // Log Transaction
            $this->logTransaction($wallet['wallet_id'], -$amount, 'hold', "Held for Milestone #$milestoneId", null, $milestoneId);

            if ($startedTransaction) $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($startedTransaction && $this->conn->inTransaction()) $this->conn->rollBack();
            error_log("Hold Funds Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Release funds from milestone (Milestone Escrow -> Company Wallet)
     */
    public function releaseFundsToCompany($companyId, $milestoneId, $amount) {
        $startedTransaction = false;
        try {
            if (!$this->conn->inTransaction()) {
                $this->conn->beginTransaction();
                $startedTransaction = true;
            }
            
            // Check Milestone Held Amount
            $stmt = $this->conn->prepare("SELECT escrow_held FROM contract_milestone WHERE milestone_id = ?");
            $stmt->execute([$milestoneId]);
            $held = $stmt->fetchColumn();

            if ($held < $amount) {
                throw new Exception("Insufficient held funds in milestone.");
            }

            // Deduct from Milestone Escrow
            $stmt = $this->conn->prepare("UPDATE contract_milestone SET escrow_held = escrow_held - ?, payment_released = payment_released + ?, payment_released_at = NOW() WHERE milestone_id = ?");
            $stmt->execute([$amount, $amount, $milestoneId]);

            // Add to Company Wallet
            $wallet = $this->getWallet($companyId, 'company');
            $stmt = $this->conn->prepare("UPDATE escrow_wallet SET balance = balance + ? WHERE wallet_id = ?");
            $stmt->execute([$amount, $wallet['wallet_id']]);

            // Log Transaction
            $this->logTransaction($wallet['wallet_id'], $amount, 'release', "Released from Milestone #$milestoneId", null, $milestoneId);

            if ($startedTransaction) $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($startedTransaction && $this->conn->inTransaction()) $this->conn->rollBack();
            error_log("Release Funds Error: " . $e->getMessage());
            return false;
        }
    }

    private function logTransaction($walletId, $amount, $type, $desc, $contractId = null, $milestoneId = null) {
        $stmt = $this->conn->prepare("INSERT INTO escrow_transaction (wallet_id, amount, type, description, related_contract_id, related_milestone_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$walletId, $amount, $type, $desc, $contractId, $milestoneId]);
    }
}
?>
