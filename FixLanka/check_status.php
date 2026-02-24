<?php
require_once 'config/database.php';
try {
    $stmt = $pdo->query("SELECT status FROM JobRequest LIMIT 1");
    echo "Status column exists";
} catch (PDOException $e) {
    echo "Status column MISSING: " . $e->getMessage();
}
