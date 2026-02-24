<?php
require_once 'config/database.php';

echo "Updating Payment Status Enum...\n";

try {
    // Check current column type if possible, or just force update
    // We want to add new statuses to the ENUM
    $sql = "ALTER TABLE milestonepayment MODIFY COLUMN status ENUM('pending', 'paid', 'held_escrow', 'released', 'refunded', 'completed', 'cancelled') DEFAULT 'pending'";
    $pdo->exec($sql);
    
    echo "Payment Status Enum Updated.\n";

} catch (PDOException $e) {
    echo "Update failed: " . $e->getMessage() . "\n";
    exit(1);
}
