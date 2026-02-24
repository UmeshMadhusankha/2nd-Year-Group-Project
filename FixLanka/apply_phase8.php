<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\apply_phase8.php

require_once 'config/database.php';

try {
    echo "Applying Phase 8 Schema...\n";
    
    // Read SQL file (Assuming hardcoded for simplicity in this temp script, or read from path if accessible)
    // Since direct file read might be blocked or complex with relative paths, I'll embed the SQL.
    
    $sql = "
    CREATE TABLE IF NOT EXISTS `escrow_wallet` (
        `wallet_id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `balance` decimal(15,2) DEFAULT 0.00,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`wallet_id`),
        UNIQUE KEY `user_id` (`user_id`),
        CONSTRAINT `escrow_wallet_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `escrow_transaction` (
        `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
        `wallet_id` int(11) NOT NULL,
        `amount` decimal(15,2) NOT NULL,
        `type` enum('deposit', 'release', 'hold', 'refund', 'service_fee') NOT NULL,
        `status` enum('pending', 'completed', 'failed') DEFAULT 'completed',
        `related_contract_id` int(11) DEFAULT NULL,
        `related_milestone_id` int(11) DEFAULT NULL,
        `description` varchar(255) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`transaction_id`),
        KEY `idx_wallet` (`wallet_id`),
        KEY `idx_contract` (`related_contract_id`),
        CONSTRAINT `escrow_txn_wallet_fk` FOREIGN KEY (`wallet_id`) REFERENCES `escrow_wallet` (`wallet_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    $pdo->exec($sql);
    
    echo "Phase 8 Schema applied successfully.\n";

} catch (PDOException $e) {
    echo "Error applying schema: " . $e->getMessage() . "\n";
}
?>
