<?php
require_once __DIR__ . '/FixLanka/config/database.php';
session_start();

echo "Session Data:\n";
print_r($_SESSION);

echo "\nAdmin Table:\n";
try {
    $stmt = $pdo->query("SELECT username, email FROM admin");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($admins);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
