<?php
require_once '../config/database.php';
$stmt = $pdo->query('SHOW CREATE TABLE companyquotation');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo '<pre>' . htmlspecialchars($row['Create Table']) . '</pre>';
