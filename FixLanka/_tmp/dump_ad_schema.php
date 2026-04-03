<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/plain; charset=utf-8');

echo "DATABASE(): ";
try {
    $db = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo ($db ?: 'NULL') . "\n\n";
} catch (Throwable $e) {
    echo "(error: {$e->getMessage()})\n\n";
}

echo "COLUMNS (advertisement)\n";
echo str_repeat('-', 60) . "\n";

$stmt = $pdo->query(
    "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA
     FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'advertisement'
     ORDER BY ORDINAL_POSITION"
);
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $default = $row['COLUMN_DEFAULT'];
    $defaultStr = ($default === null) ? 'NULL' : (string)$default;
    echo sprintf(
        "%s\t%s\tNULLABLE=%s\tDEFAULT=%s\t%s\n",
        $row['COLUMN_NAME'],
        $row['COLUMN_TYPE'],
        $row['IS_NULLABLE'],
        $defaultStr,
        $row['EXTRA']
    );
}

echo "\nSHOW CREATE TABLE advertisement\n";
echo str_repeat('-', 60) . "\n";

try {
    $create = $pdo->query('SHOW CREATE TABLE advertisement')->fetch(PDO::FETCH_ASSOC);
    if ($create) {
        // MySQL returns keys: 'Table' and 'Create Table'
        $sql = $create['Create Table'] ?? implode("\n", $create);
        echo $sql . "\n";
    } else {
        echo "(no result)\n";
    }
} catch (Throwable $e) {
    echo "(error: {$e->getMessage()})\n";
}
