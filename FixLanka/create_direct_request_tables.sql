-- =====================================================
-- Direct Request Tables
-- Creates: directjobrequest, directrequestquotes
-- =====================================================

USE fix_lanka;

-- -----------------------------------------------------
-- Table: directjobrequest
-- Note: category FK points to `category` table (existing schema)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `directjobrequest` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `provider_type` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `district` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `finish_date` date NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `photos` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  KEY `idx_djr_user` (`user_id`),
  KEY `idx_djr_category` (`category_id`),
  KEY `idx_djr_provider` (`provider_id`, `provider_type`),
  KEY `idx_djr_status` (`status`),
  KEY `idx_djr_created` (`date_created`),
  CONSTRAINT `directjobrequest_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `directjobrequest_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table: directrequestquotes
-- request_id FK follows requested mapping -> jobrequest(request_id)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `directrequestquotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `provider_type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_drq_user` (`user_id`),
  KEY `idx_drq_request` (`request_id`),
  KEY `idx_drq_provider` (`provider_id`, `provider_type`),
  CONSTRAINT `directrequestquotes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `directrequestquotes_ibfk_2` FOREIGN KEY (`request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
