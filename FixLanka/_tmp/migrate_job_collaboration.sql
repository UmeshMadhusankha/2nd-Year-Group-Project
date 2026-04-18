-- Job Collaboration workflow migration
-- Run this against an existing fix_lanka database.

CREATE TABLE IF NOT EXISTS `job_collaboration` (
  `collaboration_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `request_type` enum('regular','direct') NOT NULL DEFAULT 'regular',
  `quote_id` int(11) NOT NULL,
  `quote_source` enum('repairer','company') NOT NULL,
  `user_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `provider_role` enum('repairer','company') NOT NULL,
  `base_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `agreed_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `current_phase` enum('negotiation','in_progress','completion_verification','payment_verification','review','completed','cancelled') NOT NULL DEFAULT 'in_progress',
  `pending_price` decimal(10,2) DEFAULT NULL,
  `pending_price_actor_role` enum('user','repairer','company') DEFAULT NULL,
  `pending_price_note` text DEFAULT NULL,
  `user_completed_at` datetime DEFAULT NULL,
  `provider_completed_at` datetime DEFAULT NULL,
  `user_payment_confirmed_at` datetime DEFAULT NULL,
  `provider_payment_confirmed_at` datetime DEFAULT NULL,
  `user_rated_at` datetime DEFAULT NULL,
  `provider_rated_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`collaboration_id`),
  UNIQUE KEY `uniq_request_type` (`request_id`,`request_type`),
  KEY `idx_user` (`user_id`),
  KEY `idx_provider` (`provider_id`,`provider_role`),
  KEY `idx_phase` (`current_phase`),
  KEY `idx_quote_lookup` (`quote_id`,`quote_source`),
  CONSTRAINT `job_collaboration_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `job_collaboration_event` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `collaboration_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `actor_role` enum('user','repairer','company') NOT NULL,
  `event_type` enum('note','price_proposed','price_accepted','price_rejected','completed_marked','payment_confirmed','rating_submitted','phase_changed','system') NOT NULL,
  `message` text DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `meta_json` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`event_id`),
  KEY `idx_collab_created` (`collaboration_id`,`created_at`),
  KEY `idx_actor` (`actor_id`,`actor_role`),
  CONSTRAINT `job_collaboration_event_ibfk_1` FOREIGN KEY (`collaboration_id`) REFERENCES `job_collaboration` (`collaboration_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `job_collaboration_rating` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `collaboration_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewer_role` enum('user','repairer','company') NOT NULL,
  `target_id` int(11) NOT NULL,
  `target_role` enum('user','repairer','company') NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`rating_id`),
  UNIQUE KEY `uniq_collab_reviewer` (`collaboration_id`,`reviewer_id`,`reviewer_role`),
  KEY `idx_target` (`target_id`,`target_role`),
  CONSTRAINT `job_collaboration_rating_ibfk_1` FOREIGN KEY (`collaboration_id`) REFERENCES `job_collaboration` (`collaboration_id`) ON DELETE CASCADE,
  CONSTRAINT `job_collaboration_rating_chk_1` CHECK (`rating` >= 1 and `rating` <= 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
