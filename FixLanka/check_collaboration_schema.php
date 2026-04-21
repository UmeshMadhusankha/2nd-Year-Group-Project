<?php
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/config/database.php';
$stmt = $pdo->query("DESCRIBE job_collaboration");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "{$row['Field']} - {$row['Type']}\n";
}
echo "\nDESCRIBE job_collaboration_rating\n";
$stmt = $pdo->query("DESCRIBE job_collaboration_rating");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "{$row['Field']} - {$row['Type']}\n";
}
