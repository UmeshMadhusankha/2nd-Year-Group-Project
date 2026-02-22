<?php
require_once 'config/database.php';

echo "Starting Safe Phase 2 Migration...\n";

try {
    // Get existing columns
    $stmt = $pdo->query("DESCRIBE contract");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Define columns to add
    $definitions = [
        'undo_deadline' => "DATETIME NULL COMMENT 'Time until which the contract can be undone' AFTER contract_date",
        'undo_available' => "BOOLEAN DEFAULT TRUE COMMENT 'Flag to indicate if undo is still possible' AFTER undo_deadline",
        'chat_active' => "BOOLEAN DEFAULT FALSE COMMENT 'Whether the dedicated chat room is active' AFTER undo_available",
        'escrow_enabled' => "BOOLEAN DEFAULT TRUE COMMENT 'Whether escrow service is enabled' AFTER chat_active"
    ];
    
    foreach ($definitions as $colName => $def) {
        if (!in_array($colName, $columns)) {
            echo "Adding column '$colName'...\n";
            $sql = "ALTER TABLE contract ADD COLUMN $colName $def";
            $pdo->exec($sql);
            echo "Added '$colName'.\n";
        } else {
            echo "Column '$colName' already exists. Skipping.\n";
        }
    }
    
    // Also create contract_chats table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS `contract_chats` (
      `chat_id` int(11) NOT NULL AUTO_INCREMENT,
      `contract_id` int(11) NOT NULL,
      `sender_id` int(11) NOT NULL,
      `message` text NOT NULL,
      `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `is_read` tinyint(1) DEFAULT 0,
      `attachment_url` varchar(500) DEFAULT NULL,
      PRIMARY KEY (`chat_id`),
      KEY `idx_contract` (`contract_id`),
      KEY `idx_sender` (`sender_id`),
      CONSTRAINT `contract_chats_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "Table 'contract_chats' checked/created.\n";
    
    echo "Migration completed successfully!\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
