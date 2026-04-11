<?php
require 'config/database.php';

try {
    $contractId = 36;
    $stmt = $pdo->prepare('SELECT milestone_id, title FROM contract_milestone WHERE contract_id = ?');
    $stmt->execute([$contractId]);
    $milestones = $stmt->fetchAll();

    header('Content-Type: text/plain');
    print_r($milestones);

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
