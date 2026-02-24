<?php
require_once 'config/database.php';

echo "Starting Safe Phase 4 Migration (Payment Terms)...\n";

try {
    // 1. Analyze MilestonePayment Table
    $stmt = $pdo->query("DESCRIBE milestonepayment");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $colsToAdd = [
        'contract_id' => "INT NOT NULL AFTER payment_id",
        'payment_type' => "ENUM('upfront', 'milestone', 'final', 'weekly', 'material') NOT NULL DEFAULT 'milestone' AFTER milestone_id",
        'payment_percentage' => "DECIMAL(5,2) NULL COMMENT 'Percentage of total contract value'",
        'description' => "TEXT NULL",
        // status might exist, check type later or just rely on existing
        'escrow_enabled' => "BOOLEAN DEFAULT FALSE",
        'paid_to_escrow_at' => "DATETIME NULL",
        'escrow_released_at' => "DATETIME NULL",
        'escrow_refunded_at' => "DATETIME NULL",
        'transaction_id' => "VARCHAR(100) NULL",
        'paid_by' => "INT NULL COMMENT 'User ID of payer'",
        'paid_to' => "INT NULL COMMENT 'User ID of payee'",
        'paid_at' => "DATETIME NULL",
        'created_at' => "DATETIME DEFAULT CURRENT_TIMESTAMP"
    ];
    
    foreach ($colsToAdd as $colName => $def) {
        if (!in_array($colName, $columns)) {
            echo "Adding column '$colName' to milestonepayment...\n";
            // Remove comment for SQL execution if it causes issues, but MySQL usually accepts it
            $pdo->exec("ALTER TABLE milestonepayment ADD COLUMN $colName $def");
        } else {
            echo "Column '$colName' already exists in milestonepayment.\n";
        }
    }
    
    // 2. Analyze Milestone Table (Enhancements)
    $stmt = $pdo->query("DESCRIBE milestone");
    $mColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $mColsToAdd = [
        'is_active' => "BOOLEAN DEFAULT FALSE",
        'deliverables' => "TEXT NULL",
        'submitted_at' => "DATETIME NULL",
        'reviewed_at' => "DATETIME NULL",
        'approval_status' => "ENUM('pending', 'submitted', 'approved', 'rejected') DEFAULT 'pending'"
    ];
    
    foreach ($mColsToAdd as $colName => $def) {
        if (!in_array($colName, $mColumns)) {
            echo "Adding column '$colName' to milestone...\n";
            $pdo->exec("ALTER TABLE milestone ADD COLUMN $colName $def");
        } else {
            echo "Column '$colName' already exists in milestone.\n";
        }
    }
    
    echo "Phase 4 Migration completed successfully!\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
