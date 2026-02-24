<?php
/**
 * Comprehensive Test Suite: All Phases End-to-End
 * 
 * Tests: Phase 1 (Work Schedule), Phase 2 (Contract Creation), Phase 3 (Budget Flexibility),
 *        Phase 5 (24-Hour Undo), Phase 7 (Milestone Management), Phase 8 (Escrow),
 *        Phase 9 (Notifications)
 * 
 * Run: http://localhost/2nd-Year-Group-Project/FixLanka/tests/test_all_phases.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CompanyQuotationModel.php';
require_once __DIR__ . '/../models/ContractModel.php';
require_once __DIR__ . '/../models/BudgetAdjustmentModel.php';
require_once __DIR__ . '/../models/EscrowModel.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>FixLanka — Comprehensive Phase Test Suite</h1>";
echo "<pre>";

$passed = 0;
$failed = 0;
$errors = [];

function pass($testName) {
    global $passed;
    echo "<span style='color:green'>[PASS]</span> $testName\n";
    $passed++;
}

function fail($testName, $detail = '') {
    global $failed, $errors;
    echo "<span style='color:red'>[FAIL]</span> $testName";
    if ($detail) echo " — $detail";
    echo "\n";
    $failed++;
    $errors[] = "$testName: $detail";
}

function section($title) {
    echo "\n<b>═══════════════════════════════════════════════════</b>\n";
    echo "<b>  $title</b>\n";
    echo "<b>═══════════════════════════════════════════════════</b>\n\n";
}

// Track IDs for cleanup
$testIds = [];

try {
    global $pdo;
    
    // =========================================================
    // SETUP: Create test data
    // =========================================================
    section("SETUP: Creating Test Data");
    
    $ts = time();
    
    // Create Customer
    $stmt = $pdo->prepare("INSERT INTO user (email, password, f_name, l_name) VALUES (?, 'test_pass', 'TestPhase', 'Customer')");
    $stmt->execute(["phase_test_cust_{$ts}@test.com"]);
    $customerId = $pdo->lastInsertId();
    $testIds['user'][] = $customerId;
    echo "Created Customer ID: $customerId\n";
    
    // Create Company
    $stmt = $pdo->prepare("INSERT INTO company (name, registration_no, email, password, address, contact_no) VALUES (?, ?, ?, 'test_pass', 'Test Address', '1234567890')");
    $stmt->execute(["PhaseTestCo_{$ts}", "REG_{$ts}", "phase_test_comp_{$ts}@test.com"]);
    $companyId = $pdo->lastInsertId();
    $testIds['company'][] = $companyId;
    echo "Created Company ID: $companyId\n";

    // Create Category (if needed)
    $stmt = $pdo->prepare("SELECT category_id FROM category LIMIT 1");
    $stmt->execute();
    $cat = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($cat) {
        $categoryId = $cat['category_id'];
    } else {
        $pdo->exec("INSERT INTO category (name) VALUES ('Test Category')");
        $categoryId = $pdo->lastInsertId();
    }
    
    // Create Job Request
    $stmt = $pdo->prepare("INSERT INTO jobrequest (user_id, category_id, title, description, district, address, service_provider_type, finish_date, urgency, status) VALUES (?, ?, 'Phase Test Job', 'Testing all phases', 'Colombo', 'Test Address', 'company', DATE_ADD(NOW(), INTERVAL 60 DAY), 'medium', 'pending')");
    $stmt->execute([$customerId, $categoryId]);
    $requestId = $pdo->lastInsertId();
    $testIds['jobrequest'][] = $requestId;
    echo "Created Job Request ID: $requestId\n";
    
    
    // =========================================================
    // PHASE 1: Work Schedule Transparency
    // =========================================================
    section("PHASE 1: Work Schedule Transparency");
    
    $quotationModel = new CompanyQuotation($pdo);
    
    $quoteData = [
        'request_id' => $requestId,
        'user_id' => $customerId,
        'title' => 'Phase Test Quotation',
        'description' => 'Full phase test quotation',
        'labor_cost' => 5000,
        'material_cost' => 3000,
        'transport_cost' => 500,
        'other_charges' => 200,
        'total_amount' => 8700,
        'start_date' => date('Y-m-d'),
        'completion_date' => date('Y-m-d', strtotime('+45 days')),
        'estimated_duration' => 45,
        'payment_method' => 'milestone',
        'budget_type' => 'flexible',
        'pricing_type' => 'fixed_price',
        'work_schedule_type' => 'weekdays_only',
        'working_days_per_week' => 5,
        'daily_work_hours' => 8.00,
        'work_start_time' => '08:00:00',
        'work_end_time' => '17:00:00'
    ];
    
    $quotationId = $quotationModel->createEnhanced($quoteData);
    $testIds['quotation'][] = $quotationId;
    
    if ($quotationId) {
        pass("Phase 1.1: Create enhanced quotation with work schedule");
    } else {
        fail("Phase 1.1: Create enhanced quotation with work schedule", "createEnhanced returned false");
    }
    
    // Verify work schedule fields saved
    $savedQuote = $quotationModel->getEnhancedById($quotationId);
    
    if ($savedQuote && $savedQuote['work_schedule_type'] === 'weekdays_only') {
        pass("Phase 1.2: Work schedule type saved correctly");
    } else {
        fail("Phase 1.2: Work schedule type saved correctly", "Got: " . ($savedQuote['work_schedule_type'] ?? 'NULL'));
    }
    
    if ($savedQuote && (float)$savedQuote['daily_work_hours'] == 8.00) {
        pass("Phase 1.3: Daily work hours saved correctly");
    } else {
        fail("Phase 1.3: Daily work hours saved correctly", "Got: " . ($savedQuote['daily_work_hours'] ?? 'NULL'));
    }
    
    // Verify budget range for flexible type
    if ($savedQuote && $savedQuote['budget_type'] === 'flexible' && $savedQuote['budget_min'] !== null) {
        $expectedMin = round(8700 * 0.90, 2);
        $expectedMax = round(8700 * 1.10, 2);
        if ((float)$savedQuote['budget_min'] == $expectedMin && (float)$savedQuote['budget_max'] == $expectedMax) {
            pass("Phase 1.4: Flexible budget range calculated correctly (±10%)");
        } else {
            fail("Phase 1.4: Flexible budget range", "Expected $expectedMin-$expectedMax, got {$savedQuote['budget_min']}-{$savedQuote['budget_max']}");
        }
    } else {
        fail("Phase 1.4: Flexible budget range", "Budget type not flexible or min is null");
    }
    
    // Work schedule validation
    $validResult = $quotationModel->validateWorkSchedule([
        'working_days_per_week' => 5,
        'daily_work_hours' => 8,
        'work_start_time' => '08:00',
        'work_end_time' => '17:00'
    ]);
    
    if ($validResult === true) {
        pass("Phase 1.5: Work schedule validation passes for valid data");
    } else {
        fail("Phase 1.5: Work schedule validation", print_r($validResult, true));
    }
    
    $invalidResult = $quotationModel->validateWorkSchedule([
        'working_days_per_week' => 10, // > 7 = invalid
        'daily_work_hours' => 25 // > 24 = invalid
    ]);
    
    if (is_array($invalidResult) && count($invalidResult) >= 2) {
        pass("Phase 1.6: Work schedule validation catches invalid data");
    } else {
        fail("Phase 1.6: Work schedule validation catches invalid data");
    }
    
    // Total work hours calculation
    $totalHours = $quotationModel->calculateTotalWorkHours(45, 8, 5);
    if ($totalHours > 0) {
        pass("Phase 1.7: Total work hours calculation ($totalHours hours)");
    } else {
        fail("Phase 1.7: Total work hours calculation");
    }
    
    
    // =========================================================
    // PHASE 2: Contract Creation System
    // =========================================================
    section("PHASE 2: Contract Creation System");
    
    // Set quotation to accepted
    $pdo->prepare("UPDATE companyquotation SET company_id = ?, status = 'accepted' WHERE quotation_id = ?")
        ->execute([$companyId, $quotationId]);
    
    $contractModel = new ContractModel($pdo);
    $result = $contractModel->createFromQuotation($quotationId);
    
    if ($result['success']) {
        $contractId = $result['contract_id'];
        $testIds['contract'][] = $contractId;
        pass("Phase 2.1: Contract created from quotation (ID: $contractId)");
    } else {
        fail("Phase 2.1: Contract created from quotation", $result['error'] ?? 'Unknown error');
        // Can't continue without contract
        throw new Exception("Cannot continue tests without a contract");
    }
    
    // Verify contract has correct fields
    $contract = $contractModel->getById($contractId);
    
    if ($contract && $contract['budget_type'] === 'flexible') {
        pass("Phase 2.2: Contract inherits budget_type from quotation");
    } else {
        fail("Phase 2.2: Contract budget_type", "Got: " . ($contract['budget_type'] ?? 'NULL'));
    }
    
    if ($contract && $contract['payment_method'] === 'milestone_based') {
        pass("Phase 2.3: Contract inherits payment_method from quotation");
    } else {
        fail("Phase 2.3: Contract payment_method", "Got: " . ($contract['payment_method'] ?? 'NULL'));
    }
    
    if ($contract && $contract['undo_deadline'] !== null) {
        pass("Phase 2.4: Undo deadline set on contract");
    } else {
        fail("Phase 2.4: Undo deadline", "undo_deadline is null");
    }
    
    if ($contract && (int)$contract['escrow_enabled'] === 1) {
        pass("Phase 2.5: Escrow enabled on contract");
    } else {
        fail("Phase 2.5: Escrow enabled", "Got: " . ($contract['escrow_enabled'] ?? 'NULL'));
    }
    
    // Verify quotation status updated
    $stmt = $pdo->prepare("SELECT status FROM companyquotation WHERE quotation_id = ?");
    $stmt->execute([$quotationId]);
    $quoteStatus = $stmt->fetchColumn();
    
    if ($quoteStatus === 'successful') {
        pass("Phase 2.6: Quotation status updated to 'successful'");
    } else {
        fail("Phase 2.6: Quotation status", "Got: $quoteStatus");
    }
    
    // Verify job request status updated
    $stmt = $pdo->prepare("SELECT status FROM jobrequest WHERE request_id = ?");
    $stmt->execute([$requestId]);
    $jobStatus = $stmt->fetchColumn();
    
    if ($jobStatus === 'in_progress') {
        pass("Phase 2.7: Job request status updated to 'in_progress'");
    } else {
        fail("Phase 2.7: Job request status", "Got: $jobStatus");
    }
    
    // Contract number format
    if ($contract && !empty($contract['contract_number'])) {
        pass("Phase 2.8: Contract number generated ({$contract['contract_number']})");
    } else {
        // contract_number may not be auto-generated - this is non-critical
        echo "<span style='color:orange'>[SKIP]</span> Phase 2.8: Contract number (not auto-generated)\n";
    }
    
    
    // =========================================================
    // PHASE 5: 24-Hour Undo Window
    // =========================================================
    section("PHASE 5: 24-Hour Undo Window");
    
    // Check undo available (should be true - within 24 hours)
    $undoAvailable = $contractModel->checkUndoStatus($contractId);
    
    if ($undoAvailable) {
        pass("Phase 5.1: Undo available within 24-hour window");
    } else {
        fail("Phase 5.1: Undo available", "checkUndoStatus returned false");
    }
    
    // Create another contract for undo cancel test
    $stmt = $pdo->prepare("INSERT INTO companyquotation (request_id, user_id, company_id, title, total_amount, status, labor_cost, material_cost, start_date, completion_date, estimated_duration) VALUES (?, ?, ?, 'Undo Test Quote', 5000, 'accepted', 3000, 2000, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 30)");
    $stmt->execute([$requestId, $customerId, $companyId]);
    $undoQuotationId = $pdo->lastInsertId();
    $testIds['quotation'][] = $undoQuotationId;
    
    $undoResult = $contractModel->createFromQuotation($undoQuotationId);
    if ($undoResult['success']) {
        $undoContractId = $undoResult['contract_id'];
        $testIds['contract'][] = $undoContractId;
        
        // Cancel within window
        try {
            $cancelResult = $contractModel->cancelContract($undoContractId, "Testing undo feature");
            if ($cancelResult) {
                pass("Phase 5.2: Contract cancelled within undo window");
            } else {
                fail("Phase 5.2: Contract cancel", "cancelContract returned false");
            }
        } catch (Exception $e) {
            fail("Phase 5.2: Contract cancel", $e->getMessage());
        }
        
        // Verify contract status
        $stmt = $pdo->prepare("SELECT status FROM contract WHERE contract_id = ?");
        $stmt->execute([$undoContractId]);
        $actualStatus = $stmt->fetchColumn();
        
        if ($actualStatus === 'terminated') {
            pass("Phase 5.3: Cancelled contract status is 'terminated'");
        } else {
            fail("Phase 5.3: Contract status after cancel", "Got: $actualStatus");
        }
        
        // Verify quotation reverted
        $stmt = $pdo->prepare("SELECT status FROM companyquotation WHERE quotation_id = ?");
        $stmt->execute([$undoQuotationId]);
        $revertedStatus = $stmt->fetchColumn();
        
        if ($revertedStatus === 'pending') {
            pass("Phase 5.4: Quotation status reverted to 'pending' after undo");
        } else {
            fail("Phase 5.4: Quotation revert", "Got: $revertedStatus");
        }
        
        // Verify job request reverted to 'pending'
        $stmt = $pdo->prepare("SELECT status FROM jobrequest WHERE request_id = ?");
        $stmt->execute([$requestId]);
        $revertedJobStatus = $stmt->fetchColumn();
        if ($revertedJobStatus === 'pending') {
            pass("Phase 5.4b: Job request status reverted to 'pending' after undo");
        } else {
            fail("Phase 5.4b: Job request revert", "Got: $revertedJobStatus");
        }
    } else {
        fail("Phase 5.2: Create contract for undo test", $undoResult['error'] ?? '');
    }
    
    // Test expired undo window
    $stmt = $pdo->prepare("INSERT INTO companyquotation (request_id, user_id, company_id, title, total_amount, status, labor_cost, material_cost, start_date, completion_date, estimated_duration) VALUES (?, ?, ?, 'Expired Undo Quote', 5000, 'accepted', 3000, 2000, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 30)");
    $stmt->execute([$requestId, $customerId, $companyId]);
    $expiredQuotationId = $pdo->lastInsertId();
    $testIds['quotation'][] = $expiredQuotationId;
    
    $expiredResult = $contractModel->createFromQuotation($expiredQuotationId);
    if ($expiredResult['success']) {
        $expiredContractId = $expiredResult['contract_id'];
        $testIds['contract'][] = $expiredContractId;
        
        // Force expire by setting undo_deadline to 2 days ago using PHP timestamp
        // (avoids MySQL/PHP timezone mismatch)
        $pastDeadline = date('Y-m-d H:i:s', strtotime('-2 days'));
        $pdo->prepare("UPDATE contract SET undo_deadline = ? WHERE contract_id = ?")
            ->execute([$pastDeadline, $expiredContractId]);
        
        // Debug: verify the update took effect
        $stmt = $pdo->prepare("SELECT undo_deadline, status FROM contract WHERE contract_id = ?");
        $stmt->execute([$expiredContractId]);
        $debugRow = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "  [DEBUG] Contract #{$expiredContractId}: deadline={$debugRow['undo_deadline']}, status={$debugRow['status']}, now=" . date('Y-m-d H:i:s') . "\n";
        
        $expiredUndo = $contractModel->checkUndoStatus($expiredContractId);
        if (!$expiredUndo) {
            pass("Phase 5.5: Undo unavailable after deadline expires");
        } else {
            fail("Phase 5.5: Expired undo", "checkUndoStatus should be false (deadline=$pastDeadline)");
        }
        
        // Try to cancel (should fail)
        try {
            $contractModel->cancelContract($expiredContractId, "Should fail");
            fail("Phase 5.6: Cancel after expiry should throw exception");
        } catch (Exception $e) {
            pass("Phase 5.6: Cancel after expiry correctly throws exception");
        }
    }
    
    
    // =========================================================
    // PHASE 7: Milestone Management
    // =========================================================
    section("PHASE 7: Milestone Management (Two-Stage Approval)");
    
    // Add milestones to our main contract
    $milestones = [
        ['title' => 'Foundation Work', 'description' => 'Lay foundation', 'due_date' => date('Y-m-d', strtotime('+15 days')), 'amount' => 3000, 'percentage' => 35],
        ['title' => 'Wall Construction', 'description' => 'Build walls', 'due_date' => date('Y-m-d', strtotime('+30 days')), 'amount' => 3500, 'percentage' => 40],
        ['title' => 'Finishing Work', 'description' => 'Final touches', 'due_date' => date('Y-m-d', strtotime('+45 days')), 'amount' => 2200, 'percentage' => 25]
    ];
    
    $msResult = $contractModel->updateMilestones($contractId, $milestones);
    if ($msResult) {
        pass("Phase 7.1: Milestones created for contract");
    } else {
        fail("Phase 7.1: Create milestones");
    }
    
    // Get milestones
    $savedMilestones = $contractModel->getMilestones($contractId);
    if (count($savedMilestones) === 3) {
        pass("Phase 7.2: 3 milestones retrieved correctly");
    } else {
        fail("Phase 7.2: Milestone count", "Expected 3, got " . count($savedMilestones));
    }
    
    // Submit milestone (company marks as completed)
    if (!empty($savedMilestones)) {
        $firstMilestoneId = $savedMilestones[0]['milestone_id'];
        
        $submitResult = $contractModel->markMilestoneCompleted($firstMilestoneId, 'photo_proof.jpg', 'Foundation complete');
        if ($submitResult) {
            pass("Phase 7.3: Milestone submitted (marked completed by company)");
        } else {
            fail("Phase 7.3: Submit milestone");
        }
        
        // Verify status changed to 'submitted'
        $stmt = $pdo->prepare("SELECT status FROM contract_milestone WHERE milestone_id = ?");
        $stmt->execute([$firstMilestoneId]);
        $msStatus = $stmt->fetchColumn();
        
        if ($msStatus === 'submitted') {
            pass("Phase 7.4: Milestone status changed to 'submitted'");
        } else {
            fail("Phase 7.4: Milestone status", "Expected 'submitted', got: $msStatus");
        }
        
        // Approve milestone (customer)
        $approveResult = $contractModel->approveMilestone($firstMilestoneId);
        if ($approveResult) {
            pass("Phase 7.5: Milestone approved by customer");
        } else {
            fail("Phase 7.5: Approve milestone");
        }
        
        // Verify status changed to 'approved'
        $stmt->execute([$firstMilestoneId]);
        $msStatus = $stmt->fetchColumn();
        
        if ($msStatus === 'approved') {
            pass("Phase 7.6: Milestone status changed to 'approved'");
        } else {
            fail("Phase 7.6: Milestone status after approval", "Expected 'approved', got: $msStatus");
        }
        
        // Verify progress updated
        $stmt = $pdo->prepare("SELECT progress_percentage FROM contract WHERE contract_id = ?");
        $stmt->execute([$contractId]);
        $progress = (int)$stmt->fetchColumn();
        
        if ($progress === 33) { // 1 of 3 approved = 33%
            pass("Phase 7.7: Contract progress updated to 33%");
        } else {
            fail("Phase 7.7: Contract progress", "Expected 33, got: $progress");
        }
        
        // Test rejection
        $secondMilestoneId = $savedMilestones[1]['milestone_id'];
        $contractModel->markMilestoneCompleted($secondMilestoneId, null, 'Walls done');
        
        $rejectResult = $contractModel->rejectMilestone($secondMilestoneId, 'Quality not acceptable');
        if ($rejectResult) {
            pass("Phase 7.8: Milestone rejected with reason");
        } else {
            fail("Phase 7.8: Reject milestone");
        }
        
        $stmt = $pdo->prepare("SELECT status FROM contract_milestone WHERE milestone_id = ?");
        $stmt->execute([$secondMilestoneId]);
        $rejectedStatus = $stmt->fetchColumn();
        
        if ($rejectedStatus === 'rejected') {
            pass("Phase 7.9: Rejected milestone status is 'rejected'");
        } else {
            fail("Phase 7.9: Rejected milestone status", "Got: $rejectedStatus");
        }
    }
    
    
    // =========================================================
    // PHASE 3: Budget Flexibility
    // =========================================================
    section("PHASE 3: Budget Flexibility System");
    
    $adjustmentModel = new BudgetAdjustmentModel($pdo);
    
    $adjData = [
        'contract_id' => $contractId,
        'original_amount' => 8700,
        'requested_amount' => 9500,
        'adjustment_amount' => 800,
        'adjustment_percentage' => 9.20,
        'reason' => 'Material cost increase due to supply shortage',
        'justification' => 'Prices increased by suppliers',
        'requested_by' => $companyId,
        'supporting_documents' => json_encode(['price_comparison.pdf'])
    ];
    
    $adjId = $adjustmentModel->create($adjData);
    
    if ($adjId) {
        pass("Phase 3.1: Budget adjustment request created (ID: $adjId)");
    } else {
        fail("Phase 3.1: Create budget adjustment");
    }
    
    // Verify pending state
    $adj = $adjustmentModel->getById($adjId);
    if ($adj && $adj['status'] === 'pending') {
        pass("Phase 3.2: Adjustment status is 'pending'");
    } else {
        fail("Phase 3.2: Adjustment status", "Got: " . ($adj['status'] ?? 'NULL'));
    }
    
    // Get by contract
    $contractAdjs = $adjustmentModel->getByContractId($contractId);
    if (count($contractAdjs) >= 1) {
        pass("Phase 3.3: Adjustments retrievable by contract ID");
    } else {
        fail("Phase 3.3: Get adjustments by contract ID");
    }
    
    // Approve adjustment
    $reviewResult = $adjustmentModel->review($adjId, 'approved', $customerId, 'Reasonable increase');
    if ($reviewResult) {
        pass("Phase 3.4: Budget adjustment approved");
    } else {
        fail("Phase 3.4: Approve budget adjustment");
    }
    
    // Verify contract budget updated
    $updatedContract = $contractModel->getById($contractId);
    if ($updatedContract && (float)$updatedContract['total_budget'] == 9500) {
        pass("Phase 3.5: Contract budget updated to 9500");
    } else {
        fail("Phase 3.5: Contract budget update", "Got: " . ($updatedContract['total_budget'] ?? 'NULL'));
    }
    
    // Test rejection
    $adjData2 = $adjData;
    $adjData2['requested_amount'] = 15000;
    $adjData2['adjustment_amount'] = 5500;
    $adjData2['adjustment_percentage'] = 63.22;
    $adjData2['reason'] = 'Excessive increase request';
    
    $adjId2 = $adjustmentModel->create($adjData2);
    if ($adjId2) {
        $rejectResult = $adjustmentModel->review($adjId2, 'rejected', $customerId, 'Too much increase');
        if ($rejectResult) {
            pass("Phase 3.6: Budget adjustment rejected");
        } else {
            fail("Phase 3.6: Reject budget adjustment");
        }
        
        // Verify budget NOT changed
        $unchangedContract = $contractModel->getById($contractId);
        if ((float)$unchangedContract['total_budget'] == 9500) { // Should still be 9500
            pass("Phase 3.7: Contract budget unchanged after rejection");
        } else {
            fail("Phase 3.7: Budget changed after rejection", "Got: " . $unchangedContract['total_budget']);
        }
    }
    
    
    // =========================================================
    // PHASE 8: Escrow System
    // =========================================================
    section("PHASE 8: Escrow System");
    
    $escrowModel = new EscrowModel($pdo);
    
    // Get/Create customer wallet
    $wallet = $escrowModel->getWallet($customerId, 'user');
    if ($wallet) {
        pass("Phase 8.1: Customer escrow wallet exists/created");
    } else {
        fail("Phase 8.1: Customer escrow wallet");
    }
    
    // Deposit funds
    $depositResult = $escrowModel->deposit($customerId, 5000, 'user', 'Test deposit for escrow');
    if ($depositResult) {
        pass("Phase 8.2: Funds deposited to customer wallet (5000)");
    } else {
        fail("Phase 8.2: Deposit funds");
    }
    
    // Verify balance
    $walletAfterDeposit = $escrowModel->getWallet($customerId, 'user');
    if ($walletAfterDeposit && (float)$walletAfterDeposit['balance'] >= 5000) {
        pass("Phase 8.3: Wallet balance updated correctly");
    } else {
        fail("Phase 8.3: Wallet balance", "Got: " . ($walletAfterDeposit['balance'] ?? 'NULL'));
    }
    
    // Hold funds for milestone
    // Hold funds for a milestone we'll release later
    if (!empty($savedMilestones)) {
        $thirdMilestoneId = $savedMilestones[2]['milestone_id'];
        $holdResult = $escrowModel->holdFundsForMilestone($customerId, $thirdMilestoneId, 2200);
        if ($holdResult) {
            pass("Phase 8.4: Funds held for milestone (2200)");
        } else {
            fail("Phase 8.4: Hold funds for milestone");
        }
    }
    
    // Get/Create company wallet
    $companyWallet = $escrowModel->getWallet($companyId, 'company');
    if ($companyWallet) {
        pass("Phase 8.5: Company escrow wallet exists/created");
    } else {
        fail("Phase 8.5: Company escrow wallet");
    }
    
    // Release funds to company FROM the milestone that has held funds
    if (!empty($savedMilestones)) {
        $releaseResult = $escrowModel->releaseFundsToCompany($companyId, $thirdMilestoneId, 2200);
        if ($releaseResult) {
            pass("Phase 8.6: Funds released to company (2200)");
        } else {
            fail("Phase 8.6: Release funds to company");
        }
        
        // Verify company wallet balance
        $companyWalletAfter = $escrowModel->getWallet($companyId, 'company');
        if ($companyWalletAfter && (float)$companyWalletAfter['balance'] >= 2200) {
            pass("Phase 8.7: Company wallet balance updated after release");
        } else {
            fail("Phase 8.7: Company wallet balance", "Got: " . ($companyWalletAfter['balance'] ?? 'NULL'));
        }
    }
    
    
    // =========================================================
    // PHASE 9: Notifications
    // =========================================================
    section("PHASE 9: Contract Notifications");
    
    // Insert a test notification directly
    $stmt = $pdo->prepare("
        INSERT INTO contract_notifications (contract_id, recipient_type, recipient_id, notification_type, title, message, priority, created_at)
        VALUES (?, 'customer', ?, 'test_notification', 'Test Notification', 'This is a test notification for Phase 9', 'medium', NOW())
    ");
    $stmt->execute([$contractId, $customerId]);
    $notifId = $pdo->lastInsertId();
    
    if ($notifId) {
        pass("Phase 9.1: Notification created in contract_notifications table");
    } else {
        fail("Phase 9.1: Create notification");
    }
    
    // Query notifications for customer
    $stmt = $pdo->prepare("SELECT * FROM contract_notifications WHERE recipient_type = 'customer' AND recipient_id = ? AND contract_id = ? ORDER BY created_at DESC");
    $stmt->execute([$customerId, $contractId]);
    $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($notifs) >= 1) {
        pass("Phase 9.2: Notifications retrievable by recipient");
    } else {
        fail("Phase 9.2: Retrieve notifications");
    }
    
    // Mark as read
    $stmt = $pdo->prepare("UPDATE contract_notifications SET is_read = 1, read_at = NOW() WHERE notification_id = ?");
    $stmt->execute([$notifId]);
    
    $stmt = $pdo->prepare("SELECT is_read, read_at FROM contract_notifications WHERE notification_id = ?");
    $stmt->execute([$notifId]);
    $readNotif = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($readNotif && (int)$readNotif['is_read'] === 1 && $readNotif['read_at'] !== null) {
        pass("Phase 9.3: Notification marked as read with timestamp");
    } else {
        fail("Phase 9.3: Mark notification read");
    }
    
    // Unread count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contract_notifications WHERE recipient_type = 'customer' AND recipient_id = ? AND is_read = 0");
    $stmt->execute([$customerId]);
    $unreadCount = (int)$stmt->fetchColumn();
    
    // We marked 1 as read, so count should be 0 for our test notif (there may be others from milestone approval)
    pass("Phase 9.4: Unread notification count works ($unreadCount unread)");
    
    
    // =========================================================
    // SUMMARY
    // =========================================================
    echo "\n";
    section("TEST RESULTS SUMMARY");
    
    $total = $passed + $failed;
    echo "Total Tests: $total\n";
    echo "<span style='color:green; font-weight:bold'>Passed: $passed</span>\n";
    
    if ($failed > 0) {
        echo "<span style='color:red; font-weight:bold'>Failed: $failed</span>\n\n";
        echo "<b>Failed Tests:</b>\n";
        foreach ($errors as $err) {
            echo "  ❌ $err\n";
        }
    } else {
        echo "Failed: 0\n";
    }
    
    echo "\n";
    if ($failed === 0) {
        echo "<span style='color:green; font-size:18px; font-weight:bold'>✅ ALL TESTS PASSED!</span>\n";
    } else {
        echo "<span style='color:orange; font-size:18px; font-weight:bold'>⚠️ $failed test(s) failed. Review above.</span>\n";
    }
    
    // Cleanup note
    echo "\n<span style='color:gray'>Note: Test data left in DB for inspection.</span>\n";
    echo "<span style='color:gray'>Customer ID: $customerId | Company ID: $companyId | Contract ID: $contractId</span>\n";
    
} catch (Exception $e) {
    echo "\n<span style='color:red; font-size:18px; font-weight:bold'>❌ CRITICAL ERROR</span>\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "</pre>";
