<?php
require_once 'config/database.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT contract_id, payment_method, total_budget, milestone_plan FROM Contract WHERE contract_id = 1");
    $stmt->execute();
    $contract = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $contract]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
