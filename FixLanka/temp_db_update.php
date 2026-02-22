<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\temp_db_update.php

require_once __DIR__ . '/config/database.php';

try {
    echo "Applying Phase 7 Schema...\n";
    
    // SQL Content
    $sql = "
    -- Phase 7: Milestone Management Schema Enhancements

    -- Add columns to contract_milestone for tracking completion and approval
    -- We'll check if exists first? Or just execute and catch error.
    -- Since we can't easily do IF NOT EXISTS for columns in MySQL in one go without procedure,
    -- we rely on catch block.
    
    ALTER TABLE contract_milestone
    ADD COLUMN completed_at DATETIME NULL,
    ADD COLUMN approved_at DATETIME NULL,
    ADD COLUMN proof_files TEXT NULL,
    ADD COLUMN comments TEXT NULL;
    ";
    
    $pdo->exec($sql);
    
    echo "Phase 7 Schema applied successfully.\n";

} catch (PDOException $e) {
    echo "Error applying schema: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Columns already exist. Proceeding.\n";
    }
}
?>
