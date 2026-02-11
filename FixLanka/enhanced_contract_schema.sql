-- ============================================
-- ENHANCED CONTRACT SCHEMA
-- Legal Contract Creation Form (8 Sections)
-- Run this AFTER the base create_database.sql
-- ============================================

USE fix_lanka;

-- ============================================
-- 1. ALTER the contract table to add new columns
-- ============================================

-- Add new columns to existing contract table (safe - uses IF NOT EXISTS pattern)
ALTER TABLE `contract`
  ADD COLUMN IF NOT EXISTS `contract_number` VARCHAR(50) DEFAULT NULL AFTER `contract_id`,
  ADD COLUMN IF NOT EXISTS `quotation_id` INT(11) DEFAULT NULL AFTER `contract_number`,
  ADD COLUMN IF NOT EXISTS `company_id` INT(11) DEFAULT NULL AFTER `quotation_id`,
  ADD COLUMN IF NOT EXISTS `customer_id` INT(11) DEFAULT NULL AFTER `company_id`,
  ADD COLUMN IF NOT EXISTS `job_request_id` INT(11) DEFAULT NULL AFTER `customer_id`,
  
  -- Section 2: Project Overview
  ADD COLUMN IF NOT EXISTS `project_title` VARCHAR(255) DEFAULT NULL AFTER `project_id`,
  ADD COLUMN IF NOT EXISTS `project_reference` VARCHAR(100) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `project_location` TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `project_description` TEXT DEFAULT NULL,
  
  -- Section 3: Scope of Work
  ADD COLUMN IF NOT EXISTS `scope_description` TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `scope_inclusions` TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `scope_exclusions` TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `scope_standards` TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `materials_responsibility` ENUM('company','client','shared') DEFAULT 'company',
  
  -- Section 5: Pricing & Payments
  ADD COLUMN IF NOT EXISTS `budget_type` ENUM('fixed','time_based','flexible') DEFAULT 'fixed',
  ADD COLUMN IF NOT EXISTS `budget_min` DECIMAL(12,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `budget_max` DECIMAL(12,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `tax_inclusive` TINYINT(1) DEFAULT 1,
  ADD COLUMN IF NOT EXISTS `payment_method` ENUM('full_upfront','milestone_based','50_50','30_70','completion') DEFAULT 'milestone_based',
  ADD COLUMN IF NOT EXISTS `advance_payment_pct` DECIMAL(5,2) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `pricing_type` ENUM('fixed_price','time_and_material') DEFAULT 'fixed_price',
  ADD COLUMN IF NOT EXISTS `hourly_rate` DECIMAL(10,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `spending_cap` DECIMAL(12,2) DEFAULT NULL,
  
  -- Section 5: Delay Handling
  ADD COLUMN IF NOT EXISTS `late_payment_penalty` TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `pause_work_clause` TINYINT(1) DEFAULT 1,
  ADD COLUMN IF NOT EXISTS `time_extension_clause` TINYINT(1) DEFAULT 1,
  
  -- Section 6: Variations
  ADD COLUMN IF NOT EXISTS `variation_clause` TINYINT(1) DEFAULT 1,
  
  -- Section 7: Communication
  ADD COLUMN IF NOT EXISTS `communication_channel` VARCHAR(100) DEFAULT 'system',
  ADD COLUMN IF NOT EXISTS `dispute_resolution` TEXT DEFAULT NULL,
  
  -- Tracking
  ADD COLUMN IF NOT EXISTS `auto_generated` TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `amount_pending` DECIMAL(12,2) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `payment_status` ENUM('pending','partial','completed') DEFAULT 'pending',
  ADD COLUMN IF NOT EXISTS `progress_percentage` INT DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `sent_to_customer` TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `sent_at` TIMESTAMP NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `customer_response` ENUM('pending','accepted','rejected','negotiating') DEFAULT 'pending',
  ADD COLUMN IF NOT EXISTS `customer_response_at` TIMESTAMP NULL DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `locked` TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Add unique index on contract_number
-- ALTER TABLE `contract` ADD UNIQUE KEY IF NOT EXISTS `idx_contract_number` (`contract_number`);

-- ============================================
-- 2. Contract Milestones table (if not exists)
-- ============================================
CREATE TABLE IF NOT EXISTS `contract_milestone` (
  `milestone_id` INT(11) NOT NULL AUTO_INCREMENT,
  `contract_id` INT(11) NOT NULL,
  `milestone_number` INT(11) NOT NULL DEFAULT 1,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `due_date` DATE NOT NULL,
  `amount` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `percentage` DECIMAL(5,2) DEFAULT NULL,
  `status` ENUM('pending','in_progress','completed','overdue') DEFAULT 'pending',
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`milestone_id`),
  KEY `idx_contract` (`contract_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `contract_milestone_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- 3. Contract Audit Log (for tracking changes)
-- ============================================
CREATE TABLE IF NOT EXISTS `contract_audit_log` (
  `log_id` INT(11) NOT NULL AUTO_INCREMENT,
  `contract_id` INT(11) NOT NULL,
  `milestone_id` INT(11) DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL,
  `performed_by` INT(11) NOT NULL,
  `user_role` VARCHAR(50) DEFAULT NULL,
  `details` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_contract` (`contract_id`),
  KEY `idx_action` (`action`),
  CONSTRAINT `contract_audit_log_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================
-- 4. Contract Drafts (auto-save)
-- ============================================
CREATE TABLE IF NOT EXISTS `contract_draft` (
  `draft_id` INT(11) NOT NULL AUTO_INCREMENT,
  `company_id` INT(11) NOT NULL,
  `quotation_id` INT(11) DEFAULT NULL,
  `form_data` JSON NOT NULL,
  `current_step` INT(11) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`draft_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_quotation` (`quotation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SELECT 'Enhanced contract schema applied successfully!' AS status;
