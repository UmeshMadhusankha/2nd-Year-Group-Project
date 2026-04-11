<?php
require 'config/database.php';

try {
    $contractId = 36;
    
    // BEFORE
    $stmt = $pdo->prepare('SELECT milestone_id, milestone_number, amount FROM contract_milestone WHERE contract_id = ?');
    $stmt->execute([$contractId]);
    $before = $stmt->fetchAll();
    
    // UPDATE
    $pdo->exec("UPDATE contract_milestone SET amount = 150000.00 WHERE contract_id = $contractId AND milestone_number = 1");
    $pdo->exec("UPDATE contract_milestone SET amount = 350000.00 WHERE contract_id = $contractId AND milestone_number = 2");
    
    // AFTER
    $stmt = $pdo->prepare('SELECT milestone_id, milestone_number, amount FROM contract_milestone WHERE contract_id = ?');
    $stmt->execute([$contractId]);
    $after = $stmt->fetchAll();

    header('Content-Type: text/plain');
    echo "BEFORE:\n";
    print_r($before);
    echo "\nAFTER:\n";
    print_r($after);

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
