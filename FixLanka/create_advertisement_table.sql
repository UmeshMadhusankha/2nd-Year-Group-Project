-- Advertisement Tables Creation Script
-- Creates tables for managing company advertisements

-- Main advertisement table
CREATE TABLE IF NOT EXISTS `advertisement` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `company_id` INT(11) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `image_url` VARCHAR(500),
    `type` ENUM('banner', 'featured', 'sponsored', 'video', 'carousel') DEFAULT 'banner',
    `priority` INT(11) DEFAULT 0,
    `target_url` VARCHAR(500),
    `start_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `end_date` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `company_id` (`company_id`),
    KEY `type` (`type`),
    KEY `start_date` (`start_date`),
    KEY `end_date` (`end_date`),
    CONSTRAINT `fk_advertisement_company` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Advertisement schedule and performance tracking
CREATE TABLE IF NOT EXISTS `advertisement_schedule` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `advertisement_id` INT(11) NOT NULL,
    `status` ENUM('active', 'paused', 'completed') DEFAULT 'active',
    `impressions` INT(11) DEFAULT 0,
    `clicks` INT(11) DEFAULT 0,
    `click_through_rate` DECIMAL(5,2) DEFAULT 0.00,
    `budget_spent` DECIMAL(10,2) DEFAULT 0.00,
    `last_shown_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `advertisement_id` (`advertisement_id`),
    CONSTRAINT `fk_schedule_advertisement` FOREIGN KEY (`advertisement_id`) REFERENCES `advertisement` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing (using first company_id)
INSERT INTO `advertisement` (`company_id`, `title`, `description`, `type`, `priority`, `target_url`, `start_date`, `end_date`) VALUES
(1, 'Professional Plumbing Services', 'Expert plumbing solutions for your home and business', 'banner', 1, '/services/plumbing', '2024-12-01 00:00:00', '2025-01-31 23:59:59'),
(1, 'Emergency Repair Services', '24/7 emergency repair services available', 'featured', 2, '/services/emergency', '2024-12-10 00:00:00', '2025-02-10 23:59:59'),
(1, 'Holiday Special Offers', 'Get 20% off on all maintenance services', 'sponsored', 3, '/offers/holiday', '2024-12-15 00:00:00', '2024-12-31 23:59:59');

-- Insert corresponding schedule data
INSERT INTO `advertisement_schedule` (`advertisement_id`, `status`, `impressions`, `clicks`, `click_through_rate`, `budget_spent`) VALUES
(1, 'active', 1250, 87, 6.96, 450.00),
(2, 'active', 980, 65, 6.63, 350.00),
(3, 'paused', 560, 34, 6.07, 180.00);
