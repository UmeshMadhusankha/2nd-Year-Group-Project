<?php
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/config/database.php';
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
sort($tables);
foreach ($tables as $table) {
    echo $table . "\n";
}
