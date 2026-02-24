-- Phase 2: Contract Creation System
-- Enhancements for 24-hour undo window and chat activation

-- Add columns to contract table
ALTER TABLE `contract`
ADD COLUMN `undo_deadline` DATETIME NULL 
    COMMENT 'Time until which the contract can be undone (24h from acceptance)' 
    AFTER `contract_date`,
ADD COLUMN `undo_available` BOOLEAN DEFAULT TRUE 
    COMMENT 'Flag to indicate if undo is still possible' 
    AFTER `undo_deadline`,
ADD COLUMN `chat_active` BOOLEAN DEFAULT FALSE 
    COMMENT 'Whether the dedicated chat room is active' 
    AFTER `undo_available`,
ADD COLUMN `escrow_enabled` BOOLEAN DEFAULT TRUE 
    COMMENT 'Whether escrow service is enabled for this contract' 
    AFTER `chat_active`;

-- Create Contract Chat table (for Phase 3, but setting up schema now as per plan)
CREATE TABLE IF NOT EXISTS `contract_chats` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add indexes
ALTER TABLE `contract`
ADD INDEX `idx_undo_deadline` (`undo_deadline`),
ADD INDEX `idx_chat_active` (`chat_active`);
