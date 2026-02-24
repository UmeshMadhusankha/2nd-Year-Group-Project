<?php
// Test Script for Contract Creation (Phase 2)
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CompanyQuotationModel.php';
require_once __DIR__ . '/../models/ContractModel.php';

echo "Starting Contract Creation Test...\n";

try {
    // 1. Setup Test Data
    // Create Customer
    $pdo->exec("INSERT INTO User (email, password, f_name, l_name) VALUES ('test_contract_cust@example.com', 'pass', 'Contract', 'Customer') ON DUPLICATE KEY UPDATE user_id=LAST_INSERT_ID(user_id)");
    $customerId = $pdo->lastInsertId();
    
    // Create Company
    $pdo->exec("INSERT INTO Company (name, registration_no, email, password, address, contact_no) VALUES ('Contract Company', 'REG999', 'test_contract_comp@example.com', 'pass', 'Company Address', '9999999999') ON DUPLICATE KEY UPDATE company_id=LAST_INSERT_ID(company_id)");
    $companyId = $pdo->lastInsertId();
    
    // Create Job Request
    $stmt = $pdo->prepare("INSERT INTO JobRequest (user_id, category_id, title, description, district, address, service_provider_type, finish_date, urgency, status) VALUES (?, 1, 'Contract Job', 'Job for Contract', 'Colombo', 'Address', 'company', NOW(), 'medium', 'pending')");
    try {
        $stmt->execute([$customerId]);
        $requestId = $pdo->lastInsertId();
    } catch (Exception $e) {
        $pdo->exec("INSERT INTO Category (name) VALUES ('Contract Cat')");
        $catId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO JobRequest (user_id, category_id, title, description, district, address, service_provider_type, finish_date, urgency, status) VALUES (?, ?, 'Contract Job', 'Job for Contract', 'Colombo', 'Address', 'company', NOW(), 'medium', 'pending')");
        $stmt->execute([$customerId, $catId]);
        $requestId = $pdo->lastInsertId();
    }
    
    echo "Test Data: Cust=$customerId, Comp=$companyId, Req=$requestId\n";
    
    // 2. Create Quotation and Accept it
    $quotationModel = new CompanyQuotation($pdo);
    $quoteData = [
        'request_id' => $requestId,
        'user_id' => $customerId, // Customer ID as per previous finding
        'company_id' => $companyId, // We should try to set this if possible, OR if model doesn't support it, maybe we need to manually update?
        // Wait, CompanyQuotationModel::create uses :user_id but docblock says "company submitting".
        // In previous test we used customerId.
        // But for Contract creation, we need a valid company_id link!
        // Let's check CompanyQuotation table schema using check_db... 
        // We know it has `user_id` and `company_id`.
        // The `create` method inserts `user_id`. It does NOT insert `company_id`.
        // This is a problem for Phase 2 test if `createFromQuotation` relies on `company_id`.
        // `createFromQuotation` query: INNER JOIN company c ON q.company_id = c.company_id
        // So `q.company_id` MUST be set.
        // If `create` method doesn't set it, we must set it manually after creation.
        
        'title' => 'Contract Quote',
        'labor_cost' => 1000,
        'material_cost' => 500,
        'total_amount' => 1500,
        'start_date' => date('Y-m-d'),
        'completion_date' => date('Y-m-d', strtotime('+7 days')),
        'estimated_duration' => 7,
        'payment_method' => 'milestone',
        'budget_type' => 'fixed',
        'work_schedule_type' => 'weekdays_only',
        'working_days_per_week' => 5,
        'daily_work_hours' => 8
    ];
    
    $quotationId = $quotationModel->createEnhanced($quoteData);
    if (!$quotationId) throw new Exception("Failed to create quotation");
    
    // Manually update company_id and status to accepted
    $pdo->exec("UPDATE companyquotation SET company_id = $companyId, status = 'accepted' WHERE quotation_id = $quotationId");
    
    echo "Quotation Created & Accepted: $quotationId (CompanyID set to $companyId)\n";
    
    // 3. Create Contract
    $contractModel = new ContractModel($pdo);
    $result = $contractModel->createFromQuotation($quotationId);
    
    if (!$result['success']) {
        throw new Exception("Contract Creation Failed: " . ($result['error'] ?? 'Unknown'));
    }
    
    $contractId = $result['contract_id'];
    echo "Contract Created: ID $contractId\n";
    
    // 4. Verify Phase 2 Fields
    $contract = $contractModel->getById($contractId);
    
    // Check Undo Deadline (approx 24h)
    $deadline = strtotime($contract['undo_deadline']);
    $now = time();
    $diff = $deadline - $now;
    // Should be around 86400 seconds (24h)
    if ($diff < 86000 || $diff > 87000) {
        throw new Exception("Undo Deadline seems wrong: " . $contract['undo_deadline'] . " (Diff: $diff)");
    }
    
    // Check other flags
    if ($contract['undo_available'] != 1) throw new Exception("undo_available should be 1");
    if ($contract['chat_active'] != 0) throw new Exception("chat_active should be 0");
    if ($contract['escrow_enabled'] != 1) throw new Exception("escrow_enabled should be 1");
    
    echo "Verification Passed: Contract Fields\n";
    
    // 5. Test Helper Methods
    
    // Check Undo Status
    $status = $contractModel->checkUndoStatus($contractId);
    if (!$status) throw new Exception("checkUndoStatus should return true");
    
    // Activate Chat
    $contractModel->activateChat($contractId);
    $updated = $contractModel->getById($contractId);
    if ($updated['chat_active'] != 1) throw new Exception("chat_active should be 1 after activation");
    
    echo "Verification Passed: Helper Methods\n";
    
    // Cleanup
    $contractModel->delete($contractId);
    // Also delete quote/req manually if needed, but test DB might be persistent. Use explicit delete if crucial.
    $quotationModel->delete($quotationId);
    
    echo "Test Cleaned Up\n";
    echo "SUCCESS: All tests passed.\n";

} catch (Exception $e) {
    echo "TEST FAILED: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
