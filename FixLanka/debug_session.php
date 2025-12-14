<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'session_active' => session_status() === PHP_SESSION_ACTIVE,
    'user_id' => $_SESSION['user_id'] ?? null,
    'user_role' => $_SESSION['user_role'] ?? null,
    'session_data' => $_SESSION
], JSON_PRETTY_PRINT);
