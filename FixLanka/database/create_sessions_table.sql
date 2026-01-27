-- Session Management Table
-- This table tracks all active user sessions for security purposes

CREATE TABLE IF NOT EXISTS `user_sessions` (
  `session_id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_role` enum('company','customer','repairer','admin') NOT NULL,
  `device_type` varchar(100) DEFAULT 'Unknown',
  `browser` varchar(100) DEFAULT 'Unknown',
  `os` varchar(100) DEFAULT 'Unknown',
  `ip_address` varchar(45) DEFAULT NULL,
  `location` varchar(255) DEFAULT 'Unknown',
  `user_agent` text DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_current` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`session_id`),
  KEY `idx_user` (`user_id`, `user_role`),
  KEY `idx_last_activity` (`last_activity`),
  KEY `idx_user_active` (`user_id`, `user_role`, `last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Clean up old sessions (optional - run manually or via cron)
-- DELETE FROM user_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- View all active sessions for a specific company
-- SELECT * FROM user_sessions WHERE user_id = 1 AND user_role = 'company' ORDER BY last_activity DESC;

-- Count sessions per user
-- SELECT user_id, user_role, COUNT(*) as session_count FROM user_sessions GROUP BY user_id, user_role;
