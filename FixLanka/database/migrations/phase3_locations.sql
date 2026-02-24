-- Phase 3 Migration: Location Normalization

-- 1. Create central locations table for addresses + district
CREATE TABLE `location` (
  `location_id` INT(11) NOT NULL AUTO_INCREMENT,
  `address` TEXT DEFAULT NULL,
  `district` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Create service areas table for providers offering services in multiple districts
CREATE TABLE `service_area` (
  `area_id` INT(11) NOT NULL AUTO_INCREMENT,
  `owner_id` INT(11) NOT NULL,
  `owner_type` ENUM('company', 'repairer') NOT NULL,
  `district` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`area_id`),
  KEY `idx_owner` (`owner_id`, `owner_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Add location_id to tables that had address/district
ALTER TABLE `user` ADD COLUMN `location_id` INT(11) DEFAULT NULL AFTER `password`;
ALTER TABLE `company` ADD COLUMN `location_id` INT(11) DEFAULT NULL AFTER `tax_id`;
ALTER TABLE `jobrequest` ADD COLUMN `location_id` INT(11) DEFAULT NULL AFTER `status`;

-- 4. Add Constraints
ALTER TABLE `user` ADD CONSTRAINT `fk_user_location` FOREIGN KEY (`location_id`) REFERENCES `location`(`location_id`) ON DELETE SET NULL;
ALTER TABLE `company` ADD CONSTRAINT `fk_company_location` FOREIGN KEY (`location_id`) REFERENCES `location`(`location_id`) ON DELETE SET NULL;
ALTER TABLE `jobrequest` ADD CONSTRAINT `fk_jobrequest_location` FOREIGN KEY (`location_id`) REFERENCES `location`(`location_id`) ON DELETE SET NULL;
