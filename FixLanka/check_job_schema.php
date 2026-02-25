<?php
require_once 'config/database.php';
try {
    $stmt = $pdo->query("DESCRIBE JobRequest");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    print_r($columns);
} catch (PDOException $e) {
    echo $e->getMessage();
}
