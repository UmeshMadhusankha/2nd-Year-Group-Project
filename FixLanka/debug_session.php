<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'user_id' => $_SESSION['user_id'] ?? 'not set',
    'user_role' => $_SESSION['user_role'] ?? 'not set',
    'all_session' => $_SESSION
]);
