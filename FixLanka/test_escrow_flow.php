<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\test_escrow_flow.php

require_once 'config/database.php';
require_once 'models/ContractModel.php';
require_once 'models/EscrowModel.php';

echo "<h1>Escrow Flow Test</h1>";
echo "<pre>";

try {
    $contractModel = new ContractModel($pdo);
    $escrowModel = new EscrowModel($pdo);

    // 1. Setup Data
    echo "1. Setting up Test Data... ";
    
    // Cleanup
    $pdo->exec("DELETE FROM escrow_transaction");
    $pdo->exec("DELETE FROM escrow_wallet");
    $pdo->exec("DELETE FROM contract_milestone WHERE title = 'TEST_ESCROW_MS'");
    $pdo->exec("DELETE FROM contract WHERE project_id IN (SELECT project_id FROM project WHERE title = 'TEST_ESCROW_PROJ')");
    $pdo->exec("DELETE FROM project WHERE title = 'TEST_ESCROW_PROJ'");
    $pdo->exec("DELETE FROM company WHERE company_id = 8882");
    $pdo->exec("DELETE FROM user WHERE user_id IN (8881, 8882)"); // IDs 888x for Escrow Test
    
    // Create Users
    $pdo->exec("INSERT INTO user (user_id, f_name, l_name, email, password) VALUES (8881, 'Escrow', 'Cust', 'ecust@test.com', 'pass')"); // Customer
    $pdo->exec("INSERT INTO user (user_id, f_name, l_name, email, password) VALUES (8882, 'Escrow', 'Comp', 'ecomp@test.com', 'pass')"); // Company User
    
    // Create Company
    $pdo->exec("INSERT INTO company (company_id, name, email, districts, registration_no, address, contact_no, password) 
                VALUES (8882, 'Escrow Co', 'ecomp@test.com', 'Colombo', 'REG8882', 'Addr', '123', 'pass')");

    // Create Project
    $pdo->exec("INSERT INTO project (company_id, customer_id, title, status, location) VALUES (8882, 8881, 'TEST_ESCROW_PROJ', 'planned', 'Col')");
    $pid = $pdo->lastInsertId();
    
    // Create Contract (Escrow Enabled)
    $pdo->prepare("INSERT INTO contract (project_id, company_id, customer_id, total_budget, status, contract_date, start_date, user_signature, company_signature, escrow_enabled) 
                   VALUES (?, 8882, 8881, 1000, 'active', NOW(), NOW(), 'u', 'c', 1)")->execute([$pid]);
    $cid = $pdo->lastInsertId();
    
    // Create Milestone
    $pdo->prepare("INSERT INTO contract_milestone (contract_id, milestone_number, title, amount, status, escrow_held) VALUES (?, 1, 'TEST_ESCROW_MS', 500, 'pending', 0.00)")->execute([$cid]);
    $mid = $pdo->lastInsertId();
    
    echo "[OK] Contract: $cid, Milestone: $mid<br>";

    // 2. Deposit Funds (Customer)
    echo "2. Depositing 1000 to Customer Wallet... ";
    if ($escrowModel->deposit(8881, 1000.00, 'Initial Deposit')) echo "[PASS]<br>"; else echo "[FAIL]<br>";
    
    $w = $escrowModel->getWallet(8881);
    echo "   Customer Balance: " . $w['balance'] . " (Expected: 1000.00)<br>";

    // 3. Fund Milestone (Hold)
    echo "3. Holding 500 for Milestone... ";
    if ($escrowModel->holdFundsForMilestone(8881, $mid, 500.00)) echo "[PASS]<br>"; else echo "[FAIL]<br>";
    
    $w = $escrowModel->getWallet(8881);
    echo "   Customer Balance: " . $w['balance'] . " (Expected: 500.00)<br>";
    
    $m = $pdo->query("SELECT escrow_held FROM contract_milestone WHERE milestone_id = $mid")->fetch(PDO::FETCH_ASSOC);
    echo "   Milestone Held: " . $m['escrow_held'] . " (Expected: 500.00)<br>";

    // 4. Mark Submitted
    echo "4. Marking Submitted... ";
    $contractModel->markMilestoneCompleted($mid, "link", "done");
    echo "[DONE]<br>";

    // 5. Approve & Release
    echo "5. Approving (Should release funds)... ";
    if ($contractModel->approveMilestone($mid)) echo "[PASS]<br>"; else echo "[FAIL]<br>";

    // 6. Verify Release
    $m = $pdo->query("SELECT escrow_held, status FROM contract_milestone WHERE milestone_id = $mid")->fetch(PDO::FETCH_ASSOC);
    echo "   Milestone Status: " . $m['status'] . "<br>";
    echo "   Milestone Held: " . $m['escrow_held'] . " (Expected: 0.00)<br>";
    
    $wc = $escrowModel->getWallet(8882); // Company User ID 8882
    echo "   Company Balance: " . $wc['balance'] . " (Expected: 500.00)<br>";
    
    // 7. Check Transactions
    echo "7. Transaction Log:<br>";
    $txns = $pdo->query("SELECT * FROM escrow_transaction ORDER BY transaction_id ASC")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($txns as $t) {
        echo "   ID: {$t['transaction_id']}, Type: {$t['type']}, Amount: {$t['amount']}, Wallet: {$t['wallet_id']}<br>";
    }

    // Cleanup
    echo "8. Cleaning up... ";
    // Optional: Leave data for inspection if needed, or delete.
    // $pdo->exec("DELETE FROM contract_milestone WHERE milestone_id = $mid");
    // $pdo->exec("DELETE FROM contract WHERE contract_id = $cid");
    // ...
    echo "[DONE]<br>";

} catch (Exception $e) {
    echo "\n[ERROR] " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
?>
