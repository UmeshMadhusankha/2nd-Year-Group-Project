<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\apply_phase8_v2.php

require_once 'config/database.php';

try {
    echo "Applying Phase 8 Schema V2...\n";
    
    // Drop old tables to start fresh (since it's a new feature and we are dev)
    $pdo->exec("DROP TABLE IF EXISTS escrow_transaction");
    $pdo->exec("DROP TABLE IF EXISTS escrow_wallet");
    
    $sql = "
    CREATE TABLE IF NOT EXISTS `escrow_wallet` (
        `wallet_id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) DEFAULT NULL,
        `company_id` int(11) DEFAULT NULL,
        `balance` decimal(15,2) DEFAULT 0.00,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`wallet_id`),
        UNIQUE KEY `user_id` (`user_id`),
        UNIQUE KEY `company_id` (`company_id`),
        CONSTRAINT `escrow_wallet_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
        CONSTRAINT `escrow_wallet_company_fk` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE
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
    
    echo "Phase 8 Schema V2 applied successfully.\n";

} catch (PDOException $e) {
    echo "Error applying schema: " . $e->getMessage() . "\n";
}
?>
