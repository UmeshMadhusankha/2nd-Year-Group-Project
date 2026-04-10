<?php
require 'config/database.php';

try {
    $contractId = 36;
    $stmt1 = $pdo->prepare('UPDATE contract_milestone SET amount = 150000 WHERE contract_id = ? AND milestone_number = 1');
    $stmt1->execute([$contractId]);
    
    $stmt2 = $pdo->prepare('UPDATE contract_milestone SET amount = 350000 WHERE contract_id = ? AND milestone_number = 2');
    $stmt2->execute([$contractId]);
    
    echo "SUCCESS: Updated milestones for contract $contractId.\n";
    echo "Rows affected 1: " . $stmt1->rowCount() . "\n";
    echo "Rows affected 2: " . $stmt2->rowCount() . "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
