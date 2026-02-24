<?php
require_once 'config/database.php';
echo "<pre>";
$stmt = $pdo->query("SHOW CREATE TABLE contract_milestone");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo htmlspecialchars($row['Create Table']);
echo "</pre>";
?>
