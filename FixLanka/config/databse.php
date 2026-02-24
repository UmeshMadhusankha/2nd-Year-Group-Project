<?php
    $host = 'localhost';
    $dbname = 'fix_lanka'; // Create this database in phpMyAdmin
    $username = 'root';
    $password = ''; // Default XAMPP password is empty

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }