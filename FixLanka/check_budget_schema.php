<?php
require_once 'config/database.php';

try {
    $stmt = $pdo->query("DESCRIBE contract_budget_adjustments");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Columns in contract_budget_adjustments:\n";
    foreach ($columns as $col) {
        echo "- $col\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
