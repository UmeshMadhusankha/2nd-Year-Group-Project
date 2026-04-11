<?php
require_once '../config/database.php';
$stmt = $pdo->query('SELECT company_id, user_id FROM company ORDER BY company_id DESC LIMIT 5');
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt2 = $pdo->query('SELECT user_id, email FROM user ORDER BY user_id DESC LIMIT 5');
$users = $stmt2->fetchAll(PDO::FETCH_ASSOC);

echo "Last 5 Companies:\n";
print_r($companies);
echo "\nLast 5 Users:\n";
print_r($users);
