<?php
/**
 * Payment Controller
 * Phase 4 - Feature 2: Payment Gateway Integration
 * 
 * Handles all payment processing operations
 */

require_once __DIR__ . '/../config/payment.php';

class PaymentController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // ========================================
    // PAYMENT INTENT CREATION (Task 2.5)
    // ========================================
    
    /**
     * Create payment intent for Stripe
     * This prepares the payment before charging the customer
     */
    public function createPaymentIntent($contract_id, $amount, $description = '') {
        try {
            // Validate amount
            $validation = validatePaymentAmount($amount);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Fetch contract details
            $stmt = $this->pdo->prepare("
                SELECT c.*, u.email as customer_email, u.name as customer_name
                FROM contract c
                JOIN users u ON c.customer_id = u.user_id
                WHERE c.contract_id = ?
            ");
            $stmt->execute([$contract_id]);
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$contract) {
                return ['success' => false, 'message' => 'Contract not found'];
            }
            
            // Calculate fees
            $fees = calculatePaymentFees($amount);
            
            // Generate payment reference
            $paymentRef = generatePaymentReference('PAY');
            
            // In real implementation, call Stripe API here
            // For now, simulate payment intent creation
            $paymentIntentId = 'pi_test_' . uniqid();
            
            // Store pending payment in database
            $stmt = $this->pdo->prepare("
                INSERT INTO contract_payments (
                    contract_id,
                    payment_type,
                    amount,
                    platform_fee,
                    gateway_fee,
                    net_amount,
                    payment_reference,
                    payment_intent_id,
                    status,
                    description,
                    created_at
                ) VALUES (?, 'upfront', ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())
            ");
            
            $stmt->execute([
                $contract_id,
                $amount,
                $fees['platform_fee'],
                $fees['gateway_fee'],
                $fees['net_amount'],
                $paymentRef,
                $paymentIntentId,
                $description
            ]);
            
            $paymentId = $this->pdo->lastInsertId();
            
            // Log activity
            logPaymentActivity(
                $contract['customer_id'],
                'create_payment_intent',
                $amount,
                'pending',
                ['contract_id' => $contract_id, 'payment_ref' => $paymentRef]
            );
            
            return [
                'success' => true,
                'payment_intent_id' => $paymentIntentId,
                'payment_id' => $paymentId,
                'payment_reference' => $paymentRef,
                'amount' => $amount,
                'fees_breakdown' => $fees,
                'client_secret' => 'secret_' . uniqid(), // For Stripe.js
                'message' => 'Payment intent created successfully'
            ];
            
        } catch (Exception $e) {
            error_log("Create payment intent error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create payment intent'];
        }
    }
    
    // ========================================
    // PROCESS PAYMENT (Task 2.4)
    // ========================================
    
    /**
     * Process actual payment
     */
    public function processPayment($payment_id, $payment_method_details = []) {
        try {
            $this->pdo->beginTransaction();
            
            // Fetch payment record
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.customer_id, c.company_id
                FROM contract_payments p
                JOIN contract c ON p.contract_id = c.contract_id
                WHERE p.payment_id = ?
            ");
            $stmt->execute([$payment_id]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$payment) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Payment not found'];
            }
            
            if ($payment['status'] !== 'pending') {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Payment already processed'];
            }
            
            // In real implementation, charge via Stripe API here
            // For now, simulate successful payment
            $transactionId = 'txn_' . uniqid();
            
            // Update payment record
            $stmt = $this->pdo->prepare("
                UPDATE contract_payments 
                SET status = 'completed',
                    transaction_id = ?,
                    payment_method = ?,
                    completed_at = NOW(),
                    updated_at = NOW()
                WHERE payment_id = ?
            ");
            
            $stmt->execute([
                $transactionId,
                $payment_method_details['type'] ?? 'card',
                $payment_id
            ]);
            
            // Deposit to escrow
            $this->depositToEscrow($payment['contract_id'], $payment['net_amount'], $payment_id);
            
            // Log activity
            logPaymentActivity(
                $payment['customer_id'],
                'process_payment',
                $payment['amount'],
                'completed',
                ['payment_id' => $payment_id, 'transaction_id' => $transactionId]
            );
            
            // Send notification to company
            $this->createNotification(
                $payment['contract_id'],
                'payment_received',
                'Payment of ' . formatPaymentAmount($payment['amount']) . ' received',
                'company'
            );
            
            // Add timeline event
            $this->addTimelineEvent(
                $payment['contract_id'],
                'payment_received',
                'Payment received: ' . formatPaymentAmount($payment['amount'])
            );
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'payment_id' => $payment_id,
                'transaction_id' => $transactionId,
                'amount' => $payment['amount'],
                'message' => 'Payment processed successfully'
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Process payment error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Payment processing failed'];
        }
    }
    
    // ========================================
    // ESCROW MANAGEMENT (Task 2.9, 2.10)
    // ========================================
    
    /**
     * Deposit payment to escrow
     */
    public function depositToEscrow($contract_id, $amount, $payment_id) {
        try {
            // Check if escrow account exists
            $stmt = $this->pdo->prepare("
                SELECT escrow_id FROM escrow_accounts 
                WHERE contract_id = ?
            ");
            $stmt->execute([$contract_id]);
            $escrow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$escrow) {
                // Create escrow account
                $stmt = $this->pdo->prepare("
                    INSERT INTO escrow_accounts (
                        contract_id,
                        balance,
                        status,
                        created_at
                    ) VALUES (?, ?, 'active', NOW())
                ");
                $stmt->execute([$contract_id, $amount]);
                $escrowId = $this->pdo->lastInsertId();
            } else {
                // Update existing escrow
                $stmt = $this->pdo->prepare("
                    UPDATE escrow_accounts 
                    SET balance = balance + ?,
                        updated_at = NOW()
                    WHERE escrow_id = ?
                ");
                $stmt->execute([$amount, $escrow['escrow_id']]);
                $escrowId = $escrow['escrow_id'];
            }
            
            // Log escrow transaction
            $stmt = $this->pdo->prepare("
                INSERT INTO escrow_transactions (
                    escrow_id,
                    payment_id,
                    transaction_type,
                    amount,
                    balance_after,
                    created_at
                ) VALUES (?, ?, 'deposit', ?, 
                    (SELECT balance FROM escrow_accounts WHERE escrow_id = ?),
                    NOW())
            ");
            $stmt->execute([$escrowId, $payment_id, $amount, $escrowId]);
            
            // Add timeline event
            $this->addTimelineEvent(
                $contract_id,
                'escrow_deposit',
                'Payment deposited to escrow: ' . formatPaymentAmount($amount)
            );
            
            return ['success' => true, 'escrow_id' => $escrowId];
            
        } catch (Exception $e) {
            error_log("Deposit to escrow error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Escrow deposit failed'];
        }
    }
    
    /**
     * Release payment from escrow
     */
    public function releaseFromEscrow($contract_id, $amount, $reason = '') {
        try {
            $this->pdo->beginTransaction();
            
            // Fetch escrow account
            $stmt = $this->pdo->prepare("
                SELECT e.*, c.company_id 
                FROM escrow_accounts e
                JOIN contract c ON e.contract_id = c.contract_id
                WHERE e.contract_id = ?
            ");
            $stmt->execute([$contract_id]);
            $escrow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$escrow) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Escrow account not found'];
            }
            
            if ($escrow['balance'] < $amount) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Insufficient escrow balance'];
            }
            
            // Update escrow balance
            $stmt = $this->pdo->prepare("
                UPDATE escrow_accounts 
                SET balance = balance - ?,
                    updated_at = NOW()
                WHERE escrow_id = ?
            ");
            $stmt->execute([$amount, $escrow['escrow_id']]);
            
            // Log escrow transaction
            $stmt = $this->pdo->prepare("
                INSERT INTO escrow_transactions (
                    escrow_id,
                    transaction_type,
                    amount,
                    balance_after,
                    reason,
                    created_at
                ) VALUES (?, 'release', ?, 
                    (SELECT balance FROM escrow_accounts WHERE escrow_id = ?),
                    ?, NOW())
            ");
            $stmt->execute([$escrow['escrow_id'], $amount, $escrow['escrow_id'], $reason]);
            
            // In real implementation, transfer to company's account via Stripe
            // For now, just log it
            
            // Add timeline event
            $this->addTimelineEvent(
                $contract_id,
                'escrow_release',
                'Payment released from escrow: ' . formatPaymentAmount($amount) . ($reason ? ' - ' . $reason : '')
            );
            
            // Notify company
            $this->createNotification(
                $contract_id,
                'escrow_released',
                'Payment of ' . formatPaymentAmount($amount) . ' has been released from escrow',
                'company'
            );
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'amount_released' => $amount,
                'remaining_balance' => $escrow['balance'] - $amount,
                'message' => 'Payment released successfully'
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Release from escrow error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Escrow release failed'];
        }
    }
    
    // ========================================
    // REFUND PROCESSING (Task 2.7)
    // ========================================
    
    /**
     * Process refund
     */
    public function refundPayment($payment_id, $amount = null, $reason = '') {
        try {
            $this->pdo->beginTransaction();
            
            // Fetch payment
            $stmt = $this->pdo->prepare("
                SELECT p.*, c.customer_id 
                FROM contract_payments p
                JOIN contract c ON p.contract_id = c.contract_id
                WHERE p.payment_id = ?
            ");
            $stmt->execute([$payment_id]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$payment) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Payment not found'];
            }
            
            if ($payment['status'] !== 'completed') {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Can only refund completed payments'];
            }
            
            // Default to full refund
            $refundAmount = $amount ?? $payment['amount'];
            
            if ($refundAmount > $payment['amount']) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Refund amount exceeds original payment'];
            }
            
            // In real implementation, process refund via Stripe API
            $refundId = 'rfnd_' . uniqid();
            
            // Update payment record
            $stmt = $this->pdo->prepare("
                UPDATE contract_payments 
                SET status = 'refunded',
                    refund_id = ?,
                    refund_amount = ?,
                    refund_reason = ?,
                    refunded_at = NOW(),
                    updated_at = NOW()
                WHERE payment_id = ?
            ");
            $stmt->execute([$refundId, $refundAmount, $reason, $payment_id]);
            
            // Remove from escrow if still there
            $escrowBalance = $this->getEscrowBalance($payment['contract_id']);
            if ($escrowBalance >= $refundAmount) {
                $this->releaseFromEscrow($payment['contract_id'], $refundAmount, 'Refund: ' . $reason);
            }
            
            // Log activity
            logPaymentActivity(
                $payment['customer_id'],
                'refund_payment',
                $refundAmount,
                'refunded',
                ['payment_id' => $payment_id, 'refund_id' => $refundId, 'reason' => $reason]
            );
            
            // Notify customer
            $this->createNotification(
                $payment['contract_id'],
                'payment_refunded',
                'Refund of ' . formatPaymentAmount($refundAmount) . ' has been processed',
                'customer'
            );
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'refund_id' => $refundId,
                'refund_amount' => $refundAmount,
                'message' => 'Refund processed successfully'
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Refund payment error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Refund processing failed'];
        }
    }
    
    // ========================================
    // HELPER METHODS
    // ========================================
    
    /**
     * Get escrow balance for contract
     */
    private function getEscrowBalance($contract_id) {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(balance, 0) as balance 
            FROM escrow_accounts 
            WHERE contract_id = ?
        ");
        $stmt->execute([$contract_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['balance'] : 0;
    }
    
    /**
     * Add timeline event
     */
    private function addTimelineEvent($contract_id, $event_type, $description) {
        $stmt = $this->pdo->prepare("
            INSERT INTO contract_timeline (
                contract_id, event_type, description, created_at
            ) VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$contract_id, $event_type, $description]);
    }
    
    /**
     * Create notification
     */
    private function createNotification($contract_id, $type, $message, $recipient_role) {
        $stmt = $this->pdo->prepare("
            INSERT INTO notifications (
                contract_id, type, message, recipient_role, created_at
            ) VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$contract_id, $type, $message, $recipient_role]);
    }
    
    /**
     * Get payment history for contract
     */
    public function getPaymentHistory($contract_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM contract_payments 
                WHERE contract_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$contract_id]);
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'payments' => $payments
            ];
            
        } catch (Exception $e) {
            error_log("Get payment history error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to fetch payment history'];
        }
    }
}
?>
