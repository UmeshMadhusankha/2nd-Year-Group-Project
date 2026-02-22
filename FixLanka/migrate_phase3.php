<?php
require_once 'config/database.php';

echo "Starting Phase 3 Migration (Budget Flexibility)...\n";

try {
    $sql = file_get_contents(__DIR__ . '/database/migrations/phase3_schema.sql');
    
    // Split by semicolon to handle multiple statements if any (though file currently has one)
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            $pdo->exec($stmt);
        }
    }
    
    echo "Phase 3 Migration completed successfully!\n";
    echo "Table 'contract_budget_adjustments' created/checked.\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
