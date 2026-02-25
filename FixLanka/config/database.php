<?php
/**
 * Database Configuration
 * ✅ PDO Connection Helper
 * Version: 1.0.0
 */

function getDatabaseConnection()
{
    $host = 'localhost';
    $dbname = 'fix_lanka';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5
            ]
        );
        
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("Database Connection Failed: " . $e->getMessage());
        die("⛔ Database connection failed. Please check if XAMPP MySQL is running.");
    }
    
    // Otherwise die with error message
    die("Database connection failed: " . $e->getMessage());
}

