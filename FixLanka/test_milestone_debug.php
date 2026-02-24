<?php
require_once 'config/database.php';

echo "<pre>";

function check($pdo, $table, $idCol, $idVal) {
    $c = $pdo->query("SELECT count(*) FROM $table WHERE $idCol = $idVal")->fetchColumn();
    echo "Check $table ($idVal): " . ($c > 0 ? "EXISTS ($c)" : "MISSING") . "<br>";
}

try {
    // 1. User 9992
    echo "Inserting User 9992...<br>";
    $pdo->exec("DELETE FROM user WHERE user_id = 9992"); // Clean first
    $pdo->exec("INSERT INTO user (user_id, f_name, l_name, email, password, role) VALUES (9992, 'Test', 'Company', 'testcomp9992@example.com', 'pass', 'company')");
    check($pdo, 'user', 'user_id', 9992);

    // 2. Company 9992
    echo "Inserting Company 9992...<br>";
    $pdo->exec("DELETE FROM company WHERE company_id = 9992"); // Clean first
    $pdo->exec("INSERT INTO company (company_id, user_id, name, email, district, city, registration_no, address, contact_no) 
                VALUES (9992, 9992, 'Test Co', 'testcomp9992@example.com', 'Colombo', 'Colombo', 'REG9992', 'Addr', '123')");
    check($pdo, 'company', 'company_id', 9992);

    // 3. User 9991 (Customer)
    echo "Inserting User 9991...<br>";
    $pdo->exec("DELETE FROM user WHERE user_id = 9991"); // Clean first
    $pdo->exec("INSERT INTO user (user_id, f_name, l_name, email, password, role) VALUES (9991, 'Test', 'Customer', 'testcust9991@example.com', 'pass', 'customer')");
    check($pdo, 'user', 'user_id', 9991);

    // 4. Project
    echo "Inserting Project...<br>";
    $pdo->exec("INSERT INTO project (company_id, customer_id, title, status, location) VALUES (9992, 9991, 'TEST_MS_PROJ', 'planned', 'Col')");
    $pid = $pdo->lastInsertId();
    echo "Project Created ID: $pid<br>";

    // 5. Contract
    echo "Inserting Contract...<br>";
    $pdo->prepare("INSERT INTO contract (project_id, company_id, customer_id, total_budget, status, contract_date, start_date, user_signature, company_signature) 
                   VALUES (?, 9992, 9991, 1000, 'active', NOW(), NOW(), 'u', 'c')")->execute([$pid]);
    $cid = $pdo->lastInsertId();
    echo "Contract Created ID: $cid<br>";

    // 6. Milestone
    echo "Inserting Milestone...<br>";
    $pdo->prepare("INSERT INTO contract_milestone (contract_id, milestone_number, title, amount, status) VALUES (?, 1, 'TEST_MS_1', 500, 'pending')")->execute([$cid]);
    $mid = $pdo->lastInsertId();
    echo "Milestone Created ID: $mid<br>";

    // 7. Verify Workflow
    require_once 'models/ContractModel.php';
    $model = new ContractModel($pdo);
    
    echo "Marking Completed...<br>";
    if($model->markMilestoneCompleted($mid)) echo "Marked.<br>"; else echo "Mark Failed.<br>";
    
    echo "Approving...<br>";
    if($model->approveMilestone($mid)) echo "Approved.<br>"; else echo "Approve Failed.<br>";

} catch (Exception $e) {
    echo "<br>[ERROR] " . $e->getMessage() . "<br>";
    echo $e->getTraceAsString();
}

echo "</pre>";
?>
