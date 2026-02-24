<?php
require_once 'config/database.php';

try {
    echo "--- Milestone Table ---\n";
    $stmt = $pdo->query("DESCRIBE milestone");
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($cols as $c) echo "- $c\n";

    echo "\n--- MilestonePayment Table ---\n";
    $stmt = $pdo->query("DESCRIBE milestonepayment");
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($cols as $c) echo "- $c\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
