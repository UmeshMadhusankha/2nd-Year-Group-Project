-- FixLanka: System Audit Log (append-only)
-- Import this into the `fix_lanka` database (phpMyAdmin -> SQL tab).

CREATE TABLE IF NOT EXISTS `system_audit_log` (
  `audit_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `request_id` char(36) DEFAULT NULL,
  `occurred_at` datetime NOT NULL DEFAULT current_timestamp(),
  `actor_user_id` int(11) DEFAULT NULL,
  `actor_role` varchar(50) DEFAULT NULL,
  `action` varchar(150) NOT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` varchar(64) DEFAULT NULL,
  `http_method` varchar(10) DEFAULT NULL,
  `endpoint` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status_code` int(11) DEFAULT NULL,
  `details` json DEFAULT NULL,
  PRIMARY KEY (`audit_id`),
  KEY `idx_audit_actor_time` (`actor_user_id`, `occurred_at`),
  KEY `idx_audit_action_time` (`action`, `occurred_at`),
  KEY `idx_audit_entity` (`entity_type`, `entity_id`),
  KEY `idx_audit_request` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Append-only enforcement (no UPDATE/DELETE allowed)
-- NOTE: If you re-import, drop old triggers first (names must be unique per table).
DROP TRIGGER IF EXISTS `system_audit_log_no_update`;
DROP TRIGGER IF EXISTS `system_audit_log_no_delete`;

DELIMITER //
CREATE TRIGGER `system_audit_log_no_update`
BEFORE UPDATE ON `system_audit_log`
FOR EACH ROW
BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'system_audit_log is append-only (updates are not allowed)';
END//

CREATE TRIGGER `system_audit_log_no_delete`
BEFORE DELETE ON `system_audit_log`
FOR EACH ROW
BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'system_audit_log is append-only (deletes are not allowed)';
END//
DELIMITER ;
