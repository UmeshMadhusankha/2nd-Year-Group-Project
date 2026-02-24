<?php
require_once 'config/database.php';

try {
    $stmt = $pdo->query("DESCRIBE contract");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Columns in contract:\n";
    foreach ($columns as $col) {
        echo "- $col\n";
    }
    
    $required = [
        'undo_deadline', 'undo_available', 'chat_active', 'escrow_enabled'
    ];
    
    $missing = array_diff($required, $columns);
    
    if (empty($missing)) {
        echo "\nSUCCESS: All required columns are present.\n";
    } else {
        echo "\nFAILURE: Missing columns:\n";
        print_r($missing);
        
        // Suggest fix query
        echo "\nSuggested Fix:\n";
        foreach ($missing as $col) {
            echo "ALTER TABLE contract ADD COLUMN $col ...;\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
