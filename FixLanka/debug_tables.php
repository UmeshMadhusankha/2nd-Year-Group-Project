<?php
require_once 'config/database.php';

try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h1>Database Tables</h1>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Check specific tables columns
    echo "<h2>Column Check</h2>";
    foreach (['milestone', 'contract_milestone', 'contract_milestones'] as $t) {
        if (in_array($t, $tables)) {
            echo "<h3>Table: $t</h3>";
            $stmt = $pdo->query("DESCRIBE $t");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<pre>";
            foreach ($columns as $col) {
                echo $col['Field'] . " (" . $col['Type'] . ")\n";
            }
            echo "</pre>";
        } else {
            echo "<h3>Table: $t (NOT FOUND)</h3>";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
