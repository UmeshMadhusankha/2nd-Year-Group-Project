<?php
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/config/database.php';
$stmt = $pdo->query('SELECT project_id, title, status, progress FROM Project ORDER BY project_id DESC LIMIT 5');
$projects = $stmt->fetchAll();
print_r($projects);
