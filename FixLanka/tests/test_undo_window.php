<?php
/**
 * Test Script for Phase 5: 24-Hour Undo Window
 * 
 * This script verifies:
 * 1. Undo availability immediately after contract creation
 * 2. Undo unavailability after 24 hours (simulated)
 * 3. Correct status reversion upon cancellation
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ContractModel.php';
require_once __DIR__ . '/../models/JobRequestModel.php';
require_once __DIR__ . '/../models/CompanyQuotationModel.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Phase 5: 24-Hour Undo Window Test</h1>";
echo "<pre>";

try {
    global $pdo;
    $contractModel = new ContractModel($pdo);

    // ---------------------------------------------------------
    // SETUP: Create Dummy Data
    // ---------------------------------------------------------
    echo "<h3>1. Setting up Test Data...</h3>";

    // Create a dummy user (Customer)
    $stmt = $pdo->prepare("INSERT INTO user (email, password, role, f_name, l_name) VALUES (?, ?, 'customer', 'Test', 'Customer')");
    $stmt->execute(['undo_test_' . time() . '@test.com', 'password']);
    $customerId = $pdo->lastInsertId();
    echo "Created Customer ID: $customerId\n";

    // Create a dummy company
    $stmt = $pdo->prepare("INSERT INTO user (email, password, role, name) VALUES (?, ?, 'company', 'Test Company')");
    $stmt->execute(['company_' . time() . '@test.com', 'password']);
    $companyId = $pdo->lastInsertId();
    echo "Created Company ID: $companyId\n";

    // Create a Job Request
    $stmt = $pdo->prepare("INSERT INTO jobrequest (user_id, title, description, status, district, city) VALUES (?, 'Test Job for Undo', 'Description', 'Open', 'Colombo', 'Colombo')");
    $stmt->execute([$customerId]);
    $requestId = $pdo->lastInsertId();
    echo "Created Job Request ID: $requestId (Status: Open)\n";

    // Create a Quotation
    $stmt = $pdo->prepare("INSERT INTO companyquotation (request_id, user_id, company_id, title, total_amount, status) VALUES (?, ?, ?, 'Test Quote', 5000, 'accepted')");
    $stmt->execute([$requestId, $customerId, $companyId]);
    $quotationId = $pdo->lastInsertId();
    echo "Created Quotation ID: $quotationId (Status: accepted)\n";

    // ---------------------------------------------------------
    // TEST 1: Contract Creation & Undo Availability
    // ---------------------------------------------------------
    echo "<h3>2. Testing Contract Creation & Undo Availability...</h3>";

    // Simulate Create Contract logic (usually done via Controller)
    $result = $contractModel->createFromQuotation($quotationId);
    
    if (!$result['success']) {
        throw new Exception("Failed to create contract: " . $result['message']);
    }

    $contractId = $result['contract_id'];
    echo "Created Contract ID: $contractId\n";

    // Verify Undo Status
    $status = $contractModel->checkUndoStatus($contractId);
    $contract = $contractModel->getById($contractId, $companyId);

    if ($contract['undo_available'] && $status) {
        echo "<span style='color:green'>[PASS] Undo is available immediately after creation.</span>\n";
        echo "Undo Deadline: " . $contract['undo_deadline'] . "\n";
    } else {
        echo "<span style='color:red'>[FAIL] Undo should be available.</span>\n";
    }

    // Verify Job Request Status
    $stmt = $pdo->prepare("SELECT status FROM jobrequest WHERE request_id = ?");
    $stmt->execute([$requestId]);
    $jobStatus = $stmt->fetchColumn();
    
    if ($jobStatus === 'Assigned') {
        echo "<span style='color:green'>[PASS] Job Request status updated to 'Assigned'.</span>\n";
    } else {
        echo "<span style='color:red'>[FAIL] Job Request status is '$jobStatus', expected 'Assigned'.</span>\n";
    }

    // ---------------------------------------------------------
    // TEST 2: Performing Undo
    // ---------------------------------------------------------
    echo "<h3>3. Testing Undo Functionality...</h3>";

    $undoResult = $contractModel->cancelContract($contractId, "Testing Undo Feature");

    if ($undoResult) {
        echo "<span style='color:green'>[PASS] cancelContract method returned true.</span>\n";
    } else {
        throw new Exception("cancelContract returned false");
    }

    // Verify Contract Status
    $contract = $contractModel->getById($contractId, $companyId);
    if ($contract['status'] === 'terminated' && $contract['undo_available'] == 0) {
        echo "<span style='color:green'>[PASS] Contract status is 'terminated' and undo_available is 0.</span>\n";
    } else {
        echo "<span style='color:red'>[FAIL] Contract status: " . $contract['status'] . "\n";
    }

    // Verify Job Request Reversion
    $stmt = $pdo->prepare("SELECT status FROM jobrequest WHERE request_id = ?");
    $stmt->execute([$requestId]);
    $jobStatus = $stmt->fetchColumn();
    
    if ($jobStatus === 'Open') {
        echo "<span style='color:green'>[PASS] Job Request reverted to 'Open'.</span>\n";
    } else {
        echo "<span style='color:red'>[FAIL] Job Request status is '$jobStatus', expected 'Open'.</span>\n";
    }

    // Verify Quotation Reversion
    $stmt = $pdo->prepare("SELECT status FROM companyquotation WHERE quotation_id = ?");
    $stmt->execute([$quotationId]);
    $quoteStatus = $stmt->fetchColumn();

    if ($quoteStatus === 'pending') {
        echo "<span style='color:green'>[PASS] Quotation status reverted to 'pending'.</span>\n";
    } else {
        echo "<span style='color:red'>[FAIL] Quotation status is '$quoteStatus', expected 'pending'.</span>\n";
    }

    // ---------------------------------------------------------
    // TEST 3: Expired Undo Window (Simulation)
    // ---------------------------------------------------------
    echo "<h3>4. Testing Expired Undo Window...</h3>";

    // Create another contract
    // Reset quotation status for reuse (or create new one, cleaner to create new)
    $stmt = $pdo->prepare("INSERT INTO companyquotation (request_id, user_id, company_id, title, total_amount, status) VALUES (?, ?, ?, 'Test Quote 2', 5000, 'accepted')");
    $stmt->execute([$requestId, $customerId, $companyId]);
    $quotationId2 = $pdo->lastInsertId();

    $result2 = $contractModel->createFromQuotation($quotationId2);
    $contractId2 = $result2['contract_id'];
    echo "Created Contract ID: $contractId2\n";

    // Manually update undo_deadline to the past
    $stmt = $pdo->prepare("UPDATE contract SET undo_deadline = DATE_SUB(NOW(), INTERVAL 1 HOUR) WHERE contract_id = ?");
    $stmt->execute([$contractId2]);
    echo "Simulated expiry: moved undo_deadline to 1 hour ago.\n";

    // Check availability
    $isAvailable = $contractModel->checkUndoStatus($contractId2);
    
    if (!$isAvailable) {
        echo "<span style='color:green'>[PASS] checkUndoStatus returned false for expired deadline.</span>\n";
    } else {
        echo "<span style='color:red'>[FAIL] checkUndoStatus returned true, expected false.</span>\n";
    }

    // Attempt Undo (Should Fail)
    try {
        $contractModel->cancelContract($contractId2, "Should fail");
        echo "<span style='color:red'>[FAIL] cancelContract should have thrown exception.</span>\n";
    } catch (Exception $e) {
        echo "<span style='color:green'>[PASS] Exception caught as expected: " . $e->getMessage() . "</span>\n";
    }

    // ---------------------------------------------------------
    // CLEANUP
    // ---------------------------------------------------------
    echo "<h3>5. Cleanup...</h3>";
    // Optional: Delete created data
    // $pdo->exec("DELETE FROM contract WHERE contract_id IN ($contractId, $contractId2)");
    // $pdo->exec("DELETE FROM companyquotation WHERE quotation_id IN ($quotationId, $quotationId2)");
    // $pdo->exec("DELETE FROM jobrequest WHERE request_id = $requestId");
    // $pdo->exec("DELETE FROM user WHERE user_id IN ($customerId, $companyId)");
    echo "Test data left in DB for inspection. IDs: User($customerId), Company($companyId), Request($requestId), Contracts($contractId, $contractId2)\n";

} catch (Exception $e) {
    echo "<h2 style='color:red'>CRITICAL TEST FAILURE</h2>";
    echo "Error: " . $e->getMessage();
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
