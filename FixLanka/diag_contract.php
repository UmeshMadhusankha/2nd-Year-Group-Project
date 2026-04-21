<?php
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/config/database.php';
$stmt = $pdo->prepare("SELECT * FROM contract WHERE project_id = 46");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\nChecking JobRequest #52 directly:\n";
$stmt = $pdo->prepare("SELECT * FROM jobrequest WHERE request_id = 52");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
