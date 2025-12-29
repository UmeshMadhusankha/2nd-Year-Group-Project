<?php
// Test script to check session data
require_once '../config/session.php';

header('Content-Type: application/json');

echo json_encode([
    'session_data' => $_SESSION,
    'is_logged_in' => isLoggedIn(),
    'user_data' => getUserData(),
    'user_role' => getUserRole()
], JSON_PRETTY_PRINT);
