<?php
// Test Script for Budget Adjustments (Phase 3)
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CompanyQuotationModel.php';
require_once __DIR__ . '/../models/ContractModel.php';
require_once __DIR__ . '/../models/BudgetAdjustmentModel.php';

echo "Starting Budget Adjustment Test...\n";

try {
    // 1. Setup Test Data (Reuse logic from contract test or create new)
    // Create Customer
    $pdo->exec("INSERT INTO User (email, password, f_name, l_name) VALUES ('test_budget_cust@example.com', 'pass', 'Budget', 'Customer') ON DUPLICATE KEY UPDATE user_id=LAST_INSERT_ID(user_id)");
    $customerId = $pdo->lastInsertId();
    
    // Create Company
    $pdo->exec("INSERT INTO Company (name, registration_no, email, password, address, contact_no) VALUES ('Budget Company', 'REG888', 'test_budget_comp@example.com', 'pass', 'Company Address', '8888888888') ON DUPLICATE KEY UPDATE company_id=LAST_INSERT_ID(company_id)");
    $companyId = $pdo->lastInsertId();
    
    // Create Job Request
    $stmt = $pdo->prepare("INSERT INTO JobRequest (user_id, category_id, title, description, district, address, service_provider_type, finish_date, urgency, status) VALUES (?, 1, 'Budget Job', 'Job for Budget', 'Colombo', 'Address', 'company', NOW(), 'medium', 'pending')");
    try {
        $stmt->execute([$customerId]);
        $requestId = $pdo->lastInsertId();
    } catch (Exception $e) {
        $pdo->exec("INSERT INTO Category (name) VALUES ('Budget Cat')");
        $catId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO JobRequest (user_id, category_id, title, description, district, address, service_provider_type, finish_date, urgency, status) VALUES (?, ?, 'Budget Job', 'Job for Budget', 'Colombo', 'Address', 'company', NOW(), 'medium', 'pending')");
        $stmt->execute([$customerId, $catId]);
        $requestId = $pdo->lastInsertId();
    }
    
    // Create Quotation
    $quotationModel = new CompanyQuotation($pdo);
    $quoteData = [
        'request_id' => $requestId,
        'user_id' => $customerId,
        'company_id' => $companyId,
        'title' => 'Budget Quote',
        'labor_cost' => 1000,
        'material_cost' => 500,
        'total_amount' => 1500,
        'start_date' => date('Y-m-d'),
        'completion_date' => date('Y-m-d', strtotime('+30 days')),
        'estimated_duration' => 30,
        'payment_method' => 'milestone',
        'budget_type' => 'flexible', // Supports adjustments
        'work_schedule_type' => 'weekdays_only',
        'working_days_per_week' => 5,
        'daily_work_hours' => 8
    ];
    $quotationId = $quotationModel->createEnhanced($quoteData);
    $pdo->exec("UPDATE companyquotation SET company_id = $companyId, status = 'accepted' WHERE quotation_id = $quotationId");
    
    // Create Contract
    $contractModel = new ContractModel($pdo);
    $result = $contractModel->createFromQuotation($quotationId);
    if (!$result['success']) throw new Exception("Contract Creation Failed: " . ($result['error'] ?? 'Unknown'));
    $contractId = $result['contract_id'];
    
    echo "Contract Created: ID $contractId (Amount: 1500)\n";
    
    // 2. Request Budget Adjustment
    $adjustmentModel = new BudgetAdjustmentModel($pdo);
    
    $adjData = [
        'contract_id' => $contractId,
        'original_amount' => 1500,
        'requested_amount' => 2000,
        'adjustment_amount' => 500,
        'adjustment_percentage' => 33.33,
        'reason' => 'Material cost increase',
        'justification' => 'Prices went up',
        'requested_by' => $companyId, // Company requests
        'supporting_documents' => json_encode(['invoice.pdf'])
    ];
    
    $adjId = $adjustmentModel->create($adjData);
    if (!$adjId) throw new Exception("Failed to create budget adjustment");
    
    echo "Adjustment Requested: ID $adjId (+500)\n";
    
    // 3. Verify Pending State
    $adj = $adjustmentModel->getById($adjId);
    if ($adj['status'] !== 'pending') throw new Exception("Adjustment status should be pending");
    
    // 4. Approve Adjustment
    $success = $adjustmentModel->review($adjId, 'approved', $customerId, 'Looks reasonable');
    if (!$success) throw new Exception("Failed to approve adjustment");
    
    echo "Adjustment Approved\n";
    
    // 5. Verify Contract Update
    $contract = $contractModel->getById($contractId);
    if ($contract['total_budget'] != 2000) {
        throw new Exception("Contract budget not updated. Expected 2000, got " . $contract['total_budget']);
    }
    
    echo "Contract Budget Updated to 2000\n";
    
    // Cleanup
    $contractModel->delete($contractId);
    $quotationModel->delete($quotationId);
    
    echo "SUCCESS: Budget Adjustment Verified.\n";

} catch (Exception $e) {
    echo "TEST FAILED: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
