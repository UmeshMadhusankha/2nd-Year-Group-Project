<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/plain');

echo "DB connected\n";

echo "Checking table: system_audit_log\n";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'system_audit_log'");
    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "SHOW TABLES result: ";
    var_export($tables);
    echo "\n";
} catch (Throwable $e) {
    echo "SHOW TABLES error: " . $e->getMessage() . "\n";
}

try {
    $stmt = $pdo->query('SELECT COUNT(*) AS c FROM system_audit_log');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "COUNT(*): " . ($row['c'] ?? 'n/a') . "\n";
} catch (Throwable $e) {
    echo "SELECT COUNT error: " . $e->getMessage() . "\n";
}
