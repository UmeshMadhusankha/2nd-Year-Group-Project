<?php
/**
 * TEMPORARY HASH GENERATOR
 * Run this once to get hashed passwords, then delete this file
 */

echo "🔐 PASSWORD HASH GENERATOR\n\n";
echo "=" . str_repeat("=", 80) . "\n\n";

// Admin password
$adminPassword = "admin123";
$adminHash = password_hash($adminPassword, PASSWORD_DEFAULT);
echo "ADMIN PASSWORD HASH:\n";
echo "Plain text: admin123\n";
echo "Hashed: " . $adminHash . "\n\n";
echo str_repeat("-", 80) . "\n\n";

// Moderator password
$moderatorPassword = "moderator1";
$moderatorHash = password_hash($moderatorPassword, PASSWORD_DEFAULT);
echo "MODERATOR PASSWORD HASH:\n";
echo "Plain text: moderator1\n";
echo "Hashed: " . $moderatorHash . "\n\n";
echo str_repeat("=", 80) . "\n\n";

echo "📋 SQL INSERT STATEMENTS:\n\n";

echo "-- Insert Admin (username: admin, email: admin@fixlanka.com)\n";
echo "INSERT INTO Admin (username, email, password) VALUES ('admin', 'admin@fixlanka.com', '$adminHash');\n\n";

echo "-- Insert Moderator (username: moderator, email: moderator@fixlanka.com)\n";
echo "INSERT INTO Moderator (username, email, password, assigned_section) VALUES ('moderator', 'moderator@fixlanka.com', '$moderatorHash', 'General');\n\n";

echo str_repeat("=", 80) . "\n";
echo "✅ Copy the hashed passwords or SQL statements above!\n";
echo "⚠️  DELETE THIS FILE after copying the hashes!\n";
?>
