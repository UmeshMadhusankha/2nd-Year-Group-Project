-- Quote negotiation workflow migration
-- Run this against existing fix_lanka databases.

CREATE TABLE IF NOT EXISTS `quote_negotiation` (
  `negotiation_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `request_type` enum('regular','direct') NOT NULL DEFAULT 'regular',
  `quote_id` int(11) NOT NULL,
  `quote_source` enum('repairer','company') NOT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `job_description` text DEFAULT NULL,
  `listed_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `proposed_price` decimal(10,2) NOT NULL,
  `message` text DEFAULT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_role` enum('user','repairer','company') NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `receiver_role` enum('user','repairer','company') NOT NULL,
  `status` enum('pending','accepted','rejected','countered','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`negotiation_id`),
  KEY `idx_quote` (`quote_id`,`quote_source`),
  KEY `idx_request` (`request_id`,`request_type`),
  KEY `idx_sender` (`sender_id`,`sender_role`),
  KEY `idx_receiver` (`receiver_id`,`receiver_role`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
