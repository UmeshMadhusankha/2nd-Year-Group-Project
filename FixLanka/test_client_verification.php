<?php
/**
 * Test Client Verification Flow
 */

// Debug PDO drivers
if (!in_array('mysql', PDO::getAvailableDrivers())) {
    die("Error: PDO MySQL driver not found. Available drivers: " . implode(', ', PDO::getAvailableDrivers()) . "\n");
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/ProjectModel.php';
// Mock Controller if needed or include it
// require_once __DIR__ . '/controllers/ContractController.php'; 
// To avoid strict dependency on Controller (which might use $_SESSION), let's insert data directly or use a minimal stub.

echo "=== STARTING CLIENT VERIFICATION TEST ===\n";


// 1. Setup: Create Project, Contract, Milestone
echo "1. Creating Test Data...\n";
$companyId = 1;
$customerId = 2; // Assuming customer exists

// Create Project
$stmt = $pdo->prepare("INSERT INTO project (company_id, customer_id, title, location, status) VALUES (?, ?, ?, ?, 'ongoing')");
$stmt->execute([$companyId, $customerId, 'Test Verification Project ' . time(), 'Test Location']);
$projectId = $pdo->lastInsertId();

// Create Contract
$stmt = $pdo->prepare("INSERT INTO contract (project_id, company_id, customer_id, title, total_budget, payment_method, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");
$stmt->execute([$projectId, $companyId, $customerId, 'Test Contract', 5000.00, 'milestone_based', date('Y-m-d'), date('Y-m-d', strtotime('+1 month'))]);
$contractId = $pdo->lastInsertId();

// Create Milestone
$stmt = $pdo->prepare("INSERT INTO contract_milestone (contract_id, milestone_number, title, amount, status, due_date) VALUES (?, 1, 'Phase 1', 1000.00, 'pending', NOW())");
$stmt->execute([$contractId]);
$milestoneId = $pdo->lastInsertId();

// Create Escrow Account (Needed for release)
$pdo->prepare("INSERT INTO escrow_accounts (contract_id, balance, status) VALUES (?, 5000.00, 'active')")->execute([$contractId]);
$escrowId = $pdo->lastInsertId();

echo "   Project ID: $projectId, Contract ID: $contractId, Milestone ID: $milestoneId\n";
echo "   Escrow Init Balance: 5000.00\n";


// 2. Start Phase
echo "\n2. Starting Phase...\n";
$projectModel->startPhase($milestoneId);
$status = $pdo->query("SELECT status FROM contract_milestone WHERE milestone_id = $milestoneId")->fetchColumn();
echo "   Status: $status " . ($status === 'in_progress' ? "[OK]" : "[FAIL]") . "\n";

// 3. Submit Proof
echo "\n3. Submitting Proof...\n";
$projectModel->submitPhaseProof($milestoneId, 'Work done', []);
$status = $pdo->query("SELECT status FROM contract_milestone WHERE milestone_id = $milestoneId")->fetchColumn();
echo "   Status: $status " . ($status === 'submitted' ? "[OK]" : "[FAIL]") . "\n";

// 4. Client Verify - APPROVE
echo "\n4. Verifying Phase (Approve)...\n";
$res = $projectModel->verifyPhase($milestoneId, 'approve');
echo "   Result: " . json_encode($res) . "\n";

// Check Status
$status = $pdo->query("SELECT status FROM contract_milestone WHERE milestone_id = $milestoneId")->fetchColumn();
echo "   New Status: $status " . ($status === 'approved' ? "[OK]" : "[FAIL]") . "\n";

// Check Escrow
$balance = $pdo->query("SELECT balance FROM escrow_accounts WHERE contract_id = $contractId")->fetchColumn();
echo "   New Escrow Balance: $balance " . ($balance == 4000.00 ? "[OK]" : "[FAIL: Expected 4000.00]") . "\n";

// Check Transaction Log
$logCount = $pdo->query("SELECT COUNT(*) FROM escrow_transactions WHERE escrow_id = $escrowId AND transaction_type = 'release'")->fetchColumn();
echo "   Transaction Logged: " . ($logCount > 0 ? "Yes [OK]" : "No [FAIL]") . "\n";

echo "\n=== TEST COMPLETE ===\n";
?>
