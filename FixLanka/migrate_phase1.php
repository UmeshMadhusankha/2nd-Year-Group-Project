<?php
// Migrate Phase 1 Schema
require_once 'config/database.php';

try {
    echo "Starting Phase 1 Migration...\n";
    
    // Read SQL file
    $sqlFile = __DIR__ . '/database/migrations/phase1_schema.sql';
    if (!file_exists($sqlFile)) {
        die("Error: SQL file not found at $sqlFile\n");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split into individual queries (basic split by ;)
    // note: this is a simple splitter, might break on complex stored procs but fine for ALTER TABLE
    $queries = explode(';', $sql);
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (empty($query)) continue;
        
        echo "Executing: " . substr($query, 0, 50) . "...\n";
        $pdo->exec($query);
    }
    
    echo "Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
