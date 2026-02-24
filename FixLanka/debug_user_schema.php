<?php
require_once 'config/database.php';
echo "<pre>";
$stmt = $pdo->query("SHOW CREATE TABLE user");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo htmlspecialchars($row['Create Table']);
echo "</pre>";
?>
