<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\test_milestone_v2.php

require_once 'config/database.php';
require_once 'models/ContractModel.php';

echo "<h1>Milestone Workflow Test V2 - VERSION 3</h1>";
echo "<pre>";

try {
    $model = new ContractModel($pdo);

    // 1. Setup Data
    echo "1. Setting up Test Data... ";
    
    // Force cleanup
    $pdo->exec("DELETE FROM contract_milestone WHERE title = 'TEST_MS_1'");
    $pdo->exec("DELETE FROM contract WHERE project_id IN (SELECT project_id FROM project WHERE title = 'TEST_MS_PROJ')");
    $pdo->exec("DELETE FROM project WHERE title = 'TEST_MS_PROJ'");
    $pdo->exec("DELETE FROM company WHERE company_id = 9992");
    $pdo->exec("DELETE FROM user WHERE user_id IN (9991, 9992)");
    
    // Create Dummy Customer (User table) - User has 'district' column but optional
    $pdo->exec("INSERT INTO user (user_id, f_name, l_name, email, password) VALUES (9991, 'Test', 'Customer', 'testcust@example.com', 'pass')");
    
    // Create Dummy Company (Company table) - Company has 'districts' column
    try {
        $pdo->exec("INSERT INTO company (company_id, name, email, districts, registration_no, address, contact_no, password) 
                    VALUES (9992, 'Test Co', 'testcomp@example.com', 'Colombo', 'REG9992', 'Addr', '123', 'pass')");
    } catch (PDOException $e) {
        echo "Error creating company: " . $e->getMessage() . "<br>";
        throw $e;
    }

    // DEBUG
    $u = $pdo->query("SELECT user_id FROM user WHERE user_id = 9991")->fetchAll();
    echo "Customer found: " . count($u) . "<br>";
    $c = $pdo->query("SELECT company_id FROM company WHERE company_id = 9992")->fetch();
    echo "Company found: " . ($c ? 'Yes' : 'No') . "<br>";

    // Insert dummy project
    $pdo->exec("INSERT INTO project (company_id, customer_id, title, status, location) VALUES (9992, 9991, 'TEST_MS_PROJ', 'planned', 'Col')");
    $pid = $pdo->lastInsertId();
    
    // Insert dummy contract
    $pdo->prepare("INSERT INTO contract (project_id, company_id, customer_id, total_budget, status, contract_date, start_date, user_signature, company_signature) 
                   VALUES (?, 9992, 9991, 1000, 'active', NOW(), NOW(), 'u', 'c')")->execute([$pid]);
    $cid = $pdo->lastInsertId();
    
    // Insert dummy milestone
    $pdo->prepare("INSERT INTO contract_milestone (contract_id, milestone_number, title, amount, status) VALUES (?, 1, 'TEST_MS_1', 500, 'pending')")->execute([$cid]);
    $mid = $pdo->lastInsertId();
    
    echo "[OK] Contract ID: $cid, Milestone ID: $mid<br>";

    // 2. Mark Completed
    echo "2. Marking Milestone Completed (Company action)... ";
    $res = $model->markMilestoneCompleted($mid, "http://proof.link", "Done");
    if ($res) echo "[PASS]<br>"; else echo "[FAIL]<br>";
    
    // Verify Status
    $m = $pdo->query("SELECT status, completed_at FROM contract_milestone WHERE milestone_id = $mid")->fetch(PDO::FETCH_ASSOC);
    echo "   Status: " . $m['status'] . "<br>";
    if ($m['status'] === 'submitted') echo "   [PASS] Status is submitted<br>"; else echo "   [FAIL] Status should be submitted (actual: " . $m['status'] . ")<br>";

    // 3. Approve
    echo "3. Approving Milestone (Customer action)... ";
    $res = $model->approveMilestone($mid);
    if ($res) echo "[PASS]<br>"; else echo "[FAIL]<br>";

    // Verify Status and Progress
    $m = $pdo->query("SELECT status, approved_at FROM contract_milestone WHERE milestone_id = $mid")->fetch(PDO::FETCH_ASSOC);
    echo "   Status: " . $m['status'] . "<br>";
    if ($m['status'] === 'approved') echo "   [PASS] Status is approved<br>"; else echo "   [FAIL] Status should be approved<br>";

    $c = $pdo->query("SELECT progress_percentage FROM contract WHERE contract_id = $cid")->fetch(PDO::FETCH_ASSOC);
    echo "   Contract Progress: " . $c['progress_percentage'] . "%<br>";
    if ($c['progress_percentage'] == 100) echo "   [PASS] Progress is 100%<br>"; else echo "   [FAIL] Progress should be 100%<br>";

    // Cleanup
    echo "4. Cleaning up... ";
    $pdo->exec("DELETE FROM contract_milestone WHERE milestone_id = $mid");
    $pdo->exec("DELETE FROM contract WHERE contract_id = $cid");
    $pdo->exec("DELETE FROM project WHERE project_id = $pid");
    // Leave Users/Company for now or delete them too
    $pdo->exec("DELETE FROM company WHERE company_id = 9992");
    $pdo->exec("DELETE FROM user WHERE user_id IN (9991, 9992)");
    
    echo "[OK]<br>";

} catch (Exception $e) {
    echo "\n[ERROR] " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
?>
