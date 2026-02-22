<?php
require_once 'config/database.php';

echo "Starting Safe Phase 3 Fix...\n";

try {
    // Get existing columns
    $stmt = $pdo->query("DESCRIBE contract_budget_adjustments");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Define columns to check/add
    $definitions = [
        'justification' => "TEXT NOT NULL COMMENT 'Detailed justification' AFTER reason",
        'supporting_documents' => "TEXT NULL COMMENT 'JSON array of document paths' AFTER justification",
        'original_amount' => "DECIMAL(12,2) NOT NULL",
        'requested_amount' => "DECIMAL(12,2) NOT NULL",
        'adjustment_amount' => "DECIMAL(12,2) NOT NULL",
        'adjustment_percentage' => "DECIMAL(5,2) NOT NULL",
        'reason' => "TEXT NOT NULL",
        'requested_by' => "INT NOT NULL",
        'requested_at' => "DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP",
        'reviewed_at' => "DATETIME NULL",
        'reviewed_by' => "INT NULL",
        'review_notes' => "TEXT NULL",
        'approved_at' => "DATETIME NULL",
        'rejected_at' => "DATETIME NULL",
        'rejection_reason' => "TEXT NULL"
    ];
    
    foreach ($definitions as $colName => $def) {
        if (!in_array($colName, $columns)) {
            echo "Adding column '$colName'...\n";
            $sql = "ALTER TABLE contract_budget_adjustments ADD COLUMN $colName $def";
            $pdo->exec($sql);
            echo "Added '$colName'.\n";
        } else {
            echo "Column '$colName' exists.\n";
        }
    }
    
    echo "Phase 3 Fix completed successfully!\n";

} catch (PDOException $e) {
    echo "Fix failed: " . $e->getMessage() . "\n";
    exit(1);
}
