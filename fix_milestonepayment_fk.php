<?php
require_once __DIR__ . '/FixLanka/config/database.php';

try {
    // 1. Drop the old constraint
    echo "Dropping old constraint milestonepayment_ibfk_1...\n";
    $pdo->exec("ALTER TABLE milestonepayment DROP FOREIGN KEY IF EXISTS milestonepayment_ibfk_1");
    echo "Done.\n";
    
    // 2. Add new constraint pointing to contract_milestone
    echo "Adding new constraint milestonepayment_ibfk_1 pointing to contract_milestone...\n";
    $pdo->exec("ALTER TABLE milestonepayment 
                ADD CONSTRAINT milestonepayment_ibfk_1 
                FOREIGN KEY (milestone_id) 
                REFERENCES contract_milestone(milestone_id) 
                ON DELETE CASCADE");
    echo "Done.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    if (strpos($e->getMessage(), "already exists") !== false) {
        echo "Constraint already exists. Skipping.\n";
    }
}
?>
