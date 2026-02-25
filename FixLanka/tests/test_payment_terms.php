<?php
// Test Script for Payment Terms (Phase 4)
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/PaymentModel.php';

echo "Starting Payment Terms Test...\n";

try {
    // 1. Setup Data (Contract ID is needed)
    // We can just use a dummy contract ID since foreign keys checks might be relevant but we can insert if needed.
    // Let's rely on existing contract from previous tests or insert a quick one.
    
    // Check if any contract exists
    $stmt = $pdo->query("SELECT contract_id FROM contract LIMIT 1");
    $contractId = $stmt->fetchColumn();
    
    if (!$contractId) {
        // Create dummy contract
        $pdo->exec("INSERT INTO contract (request_id, quotation_id, customer_id, company_id, budget_amount, status, created_at) VALUES (1, 1, 1, 1, 1000, 'active', NOW())");
        $contractId = $pdo->lastInsertId();
        echo "Created Dummy Contract ID: $contractId\n";
    } else {
        echo "Using Existing Contract ID: $contractId\n";
    }
    
    // 2. Create Payment (Held in Escrow)
    $paymentModel = new PaymentModel($pdo);
    
    $paymentData = [
        'contract_id' => $contractId,
        'amount' => 500.00,
        'payment_type' => 'milestone',
        'payment_percentage' => 50.00,
        'description' => 'Test Escrow Payment',
        'status' => 'held_escrow',
        'escrow_enabled' => 1,
        'paid_by' => 1,
        'paid_to' => 2,
        'transaction_id' => 'TXN_' . time()
    ];
    
    $paymentId = $paymentModel->create($paymentData);
    if (!$paymentId) throw new Exception("Failed to create payment");
    
    echo "Payment Created: ID $paymentId (Type: milestone, Status: held_escrow)\n";
    
    // 3. Verify Escrow Status
    $payment = $paymentModel->getById($paymentId);
    if ($payment['status'] !== 'held_escrow') throw new Exception("Status mismatch. Expected held_escrow");
    if (empty($payment['paid_to_escrow_at'])) throw new Exception("paid_to_escrow_at should look set");
    
    // 4. Release Escrow
    $success = $paymentModel->updateEscrowStatus($paymentId, 'released');
    if (!$success) throw new Exception("Failed to release escrow");
    
    echo "Escrow Released.\n";
    
    // 5. Verify Released Status
    $payment = $paymentModel->getById($paymentId);
    if ($payment['status'] !== 'released') throw new Exception("Status mismatch. Expected released");
    if (empty($payment['escrow_released_at'])) throw new Exception("escrow_released_at should be set");
    
    echo "SUCCESS: Payment Terms Verified.\n";

} catch (Exception $e) {
    echo "TEST FAILED: " . $e->getMessage() . "\n";
    // Check for foreign key error
    if (strpos($e->getMessage(), 'Foreign key constraint fails') !== false) {
        echo "Hint: Make sure contract/milestone foreign keys exist.\n";
    }
    exit(1);
}
