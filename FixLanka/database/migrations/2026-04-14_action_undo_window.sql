-- Short undo windows for critical actions (default 30 seconds)
-- Stores undo eligibility + metadata to revert state.

CREATE TABLE IF NOT EXISTS `action_undo` (
  `undo_id` INT NOT NULL AUTO_INCREMENT,
  `entity_type` VARCHAR(32) NOT NULL,
  `entity_id` INT NOT NULL,
  `action_key` VARCHAR(64) NOT NULL,
  `meta_json` LONGTEXT NULL,
  `undo_until` DATETIME NOT NULL,
  `used` TINYINT(1) NOT NULL DEFAULT 0,
  `used_at` DATETIME NULL,
  `used_by` INT NULL,
  `used_role` VARCHAR(20) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`undo_id`),
  KEY `idx_entity_action` (`entity_type`, `entity_id`, `action_key`),
  KEY `idx_undo_until` (`undo_until`),
  KEY `idx_used` (`used`, `undo_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
