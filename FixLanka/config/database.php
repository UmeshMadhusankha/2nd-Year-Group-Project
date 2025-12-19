<?php
/**
 * Database Configuration
 * PDO connection for the FixLanka application
 */

// Database credentials
$host = 'localhost';
$dbname = 'fix_lanka';
$username = 'root';
$password = '';

try {
    // Create PDO connection
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Log error and return JSON error response
    error_log("Database Connection Error: " . $e->getMessage());
    
    // If this is an API call, return JSON
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Database connection failed. Please try again later.'
        ]);
        exit();
    }
    
    // Otherwise die with error message
    die("Database connection failed: " . $e->getMessage());
}