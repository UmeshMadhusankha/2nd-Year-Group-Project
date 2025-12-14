<?php
// Quick script to find your company's user_id
require_once 'config/database.php';
require_once 'config/session.php';

echo "=== Find Your Company User ID ===\n\n";

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    echo "❌ Not logged in!\n";
    echo "Please login to your company account first.\n\n";
    
    // Show all companies in database
    $stmt = $pdo->query("SELECT user_id, username, email FROM User WHERE user_role = 'company'");
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Available Companies:\n";
    foreach ($companies as $company) {
        echo "- user_id: {$company['user_id']}, Name: {$company['username']}, Email: {$company['email']}\n";
    }
} else {
    echo "✅ You are logged in!\n\n";
    echo "Your user_id: " . $_SESSION['user_id'] . "\n";
    echo "Your role: " . ($_SESSION['user_role'] ?? 'Not set') . "\n";
    echo "Your name: " . ($_SESSION['user_name'] ?? 'Not set') . "\n\n";
    
    if ($_SESSION['user_role'] === 'company') {
        echo "✅ You are a company user!\n";
        echo "👉 Use user_id = " . $_SESSION['user_id'] . " in the SQL script\n";
    } else {
        echo "❌ You are NOT a company user!\n";
        echo "Your role is: " . $_SESSION['user_role'] . "\n";
        echo "Please login with a company account.\n";
    }
}

echo "\n=================================\n";
?>
