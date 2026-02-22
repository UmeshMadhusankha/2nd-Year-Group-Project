<?php
/**
 * Phase 6: Chat System Migration
 * Enhances the existing contract_chats table with sender_type and message_type columns.
 * Safe to run multiple times (uses IF NOT EXISTS / column checks).
 */

require_once __DIR__ . '/config/database.php';

echo "=== Phase 6: Chat System Migration ===\n\n";

try {
    // 1. Ensure contract_chats table exists with enhanced schema
    $pdo->exec("CREATE TABLE IF NOT EXISTS `contract_chats` (
        `chat_id` INT(11) NOT NULL AUTO_INCREMENT,
        `contract_id` INT(11) NOT NULL,
        `sender_id` INT(11) NOT NULL,
        `sender_type` ENUM('customer','company') NOT NULL DEFAULT 'customer',
        `message_type` ENUM('text','system') NOT NULL DEFAULT 'text',
        `message` TEXT NOT NULL,
        `sent_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `is_read` TINYINT(1) DEFAULT 0,
        `read_at` DATETIME NULL,
        `attachment_url` VARCHAR(500) DEFAULT NULL,
        PRIMARY KEY (`chat_id`),
        KEY `idx_contract` (`contract_id`),
        KEY `idx_sender` (`sender_id`),
        KEY `idx_unread` (`contract_id`, `is_read`),
        KEY `idx_sent_at` (`sent_at`),
        CONSTRAINT `fk_chat_contract` FOREIGN KEY (`contract_id`)
            REFERENCES `contract` (`contract_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "[OK] Table 'contract_chats' ensured.\n";

    // 2. Add missing columns to existing table (safe — checks first)
    $stmt = $pdo->query("DESCRIBE contract_chats");
    $columns = array_column($stmt->fetchAll(), 'Field');

    $additions = [
        'sender_type' => "ENUM('customer','company') NOT NULL DEFAULT 'customer' AFTER `sender_id`",
        'message_type' => "ENUM('text','system') NOT NULL DEFAULT 'text' AFTER `sender_type`",
        'read_at'      => "DATETIME NULL AFTER `is_read`"
    ];

    foreach ($additions as $col => $def) {
        if (!in_array($col, $columns)) {
            $pdo->exec("ALTER TABLE `contract_chats` ADD COLUMN `$col` $def");
            echo "[OK] Added column '$col'.\n";
        } else {
            echo "[--] Column '$col' already exists.\n";
        }
    }

    // 3. Ensure contract table has chat_active column
    $stmt2 = $pdo->query("DESCRIBE contract");
    $contractCols = array_column($stmt2->fetchAll(), 'Field');

    if (!in_array('chat_active', $contractCols)) {
        $pdo->exec("ALTER TABLE `contract` ADD COLUMN `chat_active` BOOLEAN DEFAULT FALSE");
        echo "[OK] Added 'chat_active' to contract table.\n";
    } else {
        echo "[--] 'chat_active' already exists in contract.\n";
    }

    echo "\n=== Migration complete! ===\n";

} catch (PDOException $e) {
    echo "[ERROR] Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
