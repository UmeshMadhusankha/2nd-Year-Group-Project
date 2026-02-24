<?php
require_once 'config/database.php';

try {
    echo "--- Checking Contract ID 1 ---\n";
    $stmt = $pdo->prepare("SELECT contract_id, payment_method, total_budget, milestone_plan FROM Contract WHERE contract_id = 1");
    $stmt->execute();
    $contract = $stmt->fetch(PDO::FETCH_ASSOC);
    
    print_r($contract);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
