-- =====================================================================
-- CONSOLIDATED MIGRATION: All Phase Changes
-- Safe to run on existing database — uses IF NOT EXISTS / column checks
-- Run this via apply_all_migrations.php for proper error handling
-- =====================================================================

-- =====================================================================
-- PHASE 1: Work Schedule columns on companyquotation
-- =====================================================================
-- These use ALTER TABLE ... ADD COLUMN. If columns already exist, they will error.
-- The PHP runner handles this gracefully.

ALTER TABLE `companyquotation`
ADD COLUMN `work_schedule_type` ENUM('all_days', 'weekdays_only', 'weekends_included', 'custom') 
    NOT NULL DEFAULT 'weekdays_only' AFTER `estimated_duration`;

ALTER TABLE `companyquotation`
ADD COLUMN `working_days_per_week` TINYINT(1) NULL AFTER `work_schedule_type`;

ALTER TABLE `companyquotation`
ADD COLUMN `daily_work_hours` DECIMAL(4,2) NULL AFTER `working_days_per_week`;

ALTER TABLE `companyquotation`
ADD COLUMN `work_start_time` TIME NULL AFTER `daily_work_hours`;

ALTER TABLE `companyquotation`
ADD COLUMN `work_end_time` TIME NULL AFTER `work_start_time`;

ALTER TABLE `companyquotation`
ADD COLUMN `break_duration` DECIMAL(3,2) NULL AFTER `work_end_time`;

ALTER TABLE `companyquotation`
ADD COLUMN `custom_schedule_json` TEXT NULL AFTER `break_duration`;

ALTER TABLE `companyquotation`
ADD COLUMN `public_holidays_excluded` BOOLEAN DEFAULT TRUE AFTER `custom_schedule_json`;

ALTER TABLE `companyquotation`
ADD COLUMN `estimated_calendar_days` INT NULL AFTER `public_holidays_excluded`;

-- =====================================================================
-- PHASE 1: Contract core enhancement columns
-- =====================================================================

ALTER TABLE `contract`
ADD COLUMN `budget_flexibility_percentage` DECIMAL(5,2) NULL AFTER `budget_type`;

ALTER TABLE `contract`
ADD COLUMN `undo_deadline` DATETIME NULL AFTER `signed_at`;

ALTER TABLE `contract`
ADD COLUMN `undo_requested` BOOLEAN DEFAULT FALSE AFTER `undo_deadline`;

ALTER TABLE `contract`
ADD COLUMN `chat_active` BOOLEAN DEFAULT FALSE AFTER `undo_requested`;

ALTER TABLE `contract`
ADD COLUMN `chat_activated_at` DATETIME NULL AFTER `chat_active`;

ALTER TABLE `contract`
ADD COLUMN `escrow_account_id` INT NULL AFTER `chat_activated_at`;

ALTER TABLE `contract`
ADD COLUMN `upfront_payment_percentage` DECIMAL(5,2) NULL AFTER `escrow_account_id`;

ALTER TABLE `contract`
ADD COLUMN `upfront_payment_amount` DECIMAL(10,2) NULL AFTER `upfront_payment_percentage`;

ALTER TABLE `contract`
ADD COLUMN `upfront_payment_received` BOOLEAN DEFAULT FALSE AFTER `upfront_payment_amount`;

ALTER TABLE `contract`
ADD COLUMN `work_verified_started` BOOLEAN DEFAULT FALSE AFTER `upfront_payment_received`;

ALTER TABLE `contract`
ADD COLUMN `quality_guarantee_end_date` DATE NULL AFTER `work_verified_started`;

ALTER TABLE `contract`
ADD COLUMN `escrow_enabled` TINYINT(1) DEFAULT 0 AFTER `payment_method`;

-- =====================================================================
-- PHASE 1: Milestone workflow columns (legacy table)
-- =====================================================================

ALTER TABLE `milestone`
ADD COLUMN `submitted_by_company` BOOLEAN DEFAULT FALSE AFTER `status`;

ALTER TABLE `milestone`
ADD COLUMN `submitted_date` DATETIME NULL AFTER `submitted_by_company`;

ALTER TABLE `milestone`
ADD COLUMN `customer_approved` BOOLEAN DEFAULT FALSE AFTER `submitted_date`;

ALTER TABLE `milestone`
ADD COLUMN `customer_approval_date` DATETIME NULL AFTER `customer_approved`;

ALTER TABLE `milestone`
ADD COLUMN `customer_rejection_reason` TEXT NULL AFTER `customer_approval_date`;

ALTER TABLE `milestone`
ADD COLUMN `work_started` BOOLEAN DEFAULT FALSE AFTER `customer_rejection_reason`;

ALTER TABLE `milestone`
ADD COLUMN `work_start_date` DATETIME NULL AFTER `work_started`;

ALTER TABLE `milestone`
ADD COLUMN `work_completed` BOOLEAN DEFAULT FALSE AFTER `work_start_date`;

ALTER TABLE `milestone`
ADD COLUMN `work_completion_date` DATETIME NULL AFTER `work_completed`;

ALTER TABLE `milestone`
ADD COLUMN `customer_verification_requested` BOOLEAN DEFAULT FALSE AFTER `work_completion_date`;

ALTER TABLE `milestone`
ADD COLUMN `customer_verified` BOOLEAN DEFAULT FALSE AFTER `customer_verification_requested`;

ALTER TABLE `milestone`
ADD COLUMN `customer_verification_date` DATETIME NULL AFTER `customer_verified`;

-- =====================================================================
-- PHASE 3: Budget Adjustments Table
-- =====================================================================

CREATE TABLE IF NOT EXISTS `contract_budget_adjustments` (
  `adjustment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `contract_id` INT NOT NULL,
  `original_amount` DECIMAL(12,2) NOT NULL,
  `requested_amount` DECIMAL(12,2) NOT NULL,
  `adjustment_amount` DECIMAL(12,2) NOT NULL,
  `adjustment_percentage` DECIMAL(5,2) NOT NULL,
  `reason` TEXT NOT NULL,
  `justification` TEXT NOT NULL,
  `supporting_documents` TEXT NULL,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `requested_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `requested_by` INT NOT NULL,
  `reviewed_at` DATETIME NULL,
  `reviewed_by` INT NULL,
  `review_notes` TEXT NULL,
  `approved_at` DATETIME NULL,
  `rejected_at` DATETIME NULL,
  `rejection_reason` TEXT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
  INDEX `idx_contract` (`contract_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- PHASE 6: Contract Chats Table
-- =====================================================================

CREATE TABLE IF NOT EXISTS `contract_chats` (
    `chat_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NOT NULL,
    `sender_type` ENUM('company', 'customer') NOT NULL,
    `sender_id` INT NOT NULL,
    `message` TEXT NOT NULL,
    `attachment_type` ENUM('text', 'image', 'document', 'pdf') DEFAULT 'text',
    `attachment_url` VARCHAR(500) NULL,
    `attachment_filename` VARCHAR(255) NULL,
    `attachment_size` INT NULL,
    `is_read` BOOLEAN DEFAULT FALSE,
    `read_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    INDEX `idx_contract_sender` (`contract_id`, `sender_type`),
    INDEX `idx_unread` (`is_read`, `created_at`),
    INDEX `idx_created` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- PHASE 7: Milestone Enhancements (contract_milestone table)
-- =====================================================================

ALTER TABLE `contract_milestone`
ADD COLUMN `completed_at` DATETIME NULL AFTER `updated_at`;

ALTER TABLE `contract_milestone`
ADD COLUMN `approved_at` DATETIME NULL AFTER `completed_at`;

ALTER TABLE `contract_milestone`
ADD COLUMN `proof_files` TEXT NULL AFTER `approved_at`;

ALTER TABLE `contract_milestone`
ADD COLUMN `comments` TEXT NULL AFTER `proof_files`;

-- =====================================================================
-- PHASE 8: Escrow System
-- =====================================================================

CREATE TABLE IF NOT EXISTS `escrow_wallet` (
    `wallet_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `company_id` int(11) DEFAULT NULL,
    `balance` decimal(15,2) DEFAULT 0.00,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`wallet_id`),
    UNIQUE KEY `user_id` (`user_id`),
    UNIQUE KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `escrow_transaction` (
    `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
    `wallet_id` int(11) NOT NULL,
    `amount` decimal(15,2) NOT NULL,
    `type` enum('deposit', 'release', 'hold', 'refund', 'service_fee') NOT NULL,
    `status` enum('pending', 'completed', 'failed') DEFAULT 'completed',
    `related_contract_id` int(11) DEFAULT NULL,
    `related_milestone_id` int(11) DEFAULT NULL,
    `description` varchar(255) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`transaction_id`),
    KEY `idx_wallet` (`wallet_id`),
    KEY `idx_contract` (`related_contract_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `escrow_accounts` (
    `escrow_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT UNIQUE NOT NULL,
    `account_number` VARCHAR(50) UNIQUE NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `held_amount` DECIMAL(10,2) DEFAULT 0,
    `released_amount` DECIMAL(10,2) DEFAULT 0,
    `refunded_amount` DECIMAL(10,2) DEFAULT 0,
    `status` ENUM('active', 'completed', 'refunded', 'disputed') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    INDEX `idx_status` (`status`),
    INDEX `idx_account_number` (`account_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- PHASE 9: Contract Notifications Table
-- =====================================================================

CREATE TABLE IF NOT EXISTS `contract_notifications` (
    `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NOT NULL,
    `recipient_type` ENUM('company', 'customer') NOT NULL,
    `recipient_id` INT NOT NULL,
    `notification_type` VARCHAR(50) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `action_url` VARCHAR(500) NULL,
    `priority` ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    `is_read` BOOLEAN DEFAULT FALSE,
    `read_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    INDEX `idx_recipient` (`recipient_type`, `recipient_id`, `is_read`),
    INDEX `idx_type` (`notification_type`),
    INDEX `idx_priority` (`priority`, `is_read`),
    INDEX `idx_created` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- Supporting Tables (Timeline, Time Logs, Invoices)
-- =====================================================================

CREATE TABLE IF NOT EXISTS `contract_timeline` (
    `timeline_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NOT NULL,
    `event_type` VARCHAR(50) NOT NULL,
    `event_title` VARCHAR(255) NOT NULL,
    `event_description` TEXT NULL,
    `actor_type` ENUM('system', 'company', 'customer') NOT NULL,
    `actor_id` INT NULL,
    `actor_name` VARCHAR(255) NULL,
    `metadata_json` TEXT NULL,
    `is_milestone` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    INDEX `idx_contract_time` (`contract_id`, `created_at` DESC),
    INDEX `idx_event_type` (`event_type`),
    INDEX `idx_actor` (`actor_type`, `actor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contract_time_logs` (
    `log_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NOT NULL,
    `work_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `break_duration` DECIMAL(3,2) DEFAULT 0,
    `total_hours` DECIMAL(4,2) NOT NULL,
    `hourly_rate` DECIMAL(10,2) NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `work_description` TEXT NOT NULL,
    `work_location` VARCHAR(255) NULL,
    `submitted_by` INT NOT NULL,
    `submitted_at` DATETIME NOT NULL,
    `customer_approved` BOOLEAN DEFAULT FALSE,
    `customer_approval_date` DATETIME NULL,
    `customer_rejection_reason` TEXT NULL,
    `invoice_id` INT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    INDEX `idx_contract_date` (`contract_id`, `work_date` DESC),
    INDEX `idx_approval` (`customer_approved`),
    INDEX `idx_invoice` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contract_invoices` (
    `invoice_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NOT NULL,
    `invoice_number` VARCHAR(50) UNIQUE NOT NULL,
    `invoice_type` ENUM('milestone', 'upfront', 'final', 'time_material', 'adjustment') NOT NULL,
    `milestone_id` INT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `tax_percentage` DECIMAL(5,2) DEFAULT 0,
    `tax_amount` DECIMAL(10,2) DEFAULT 0,
    `total_amount` DECIMAL(10,2) NOT NULL,
    `issue_date` DATE NOT NULL,
    `due_date` DATE NOT NULL,
    `payment_status` ENUM('pending', 'paid', 'overdue', 'cancelled', 'refunded') DEFAULT 'pending',
    `paid_date` DATETIME NULL,
    `payment_method` VARCHAR(50) NULL,
    `payment_reference` VARCHAR(100) NULL,
    `payment_receipt_url` VARCHAR(500) NULL,
    `pdf_path` VARCHAR(500) NULL,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    INDEX `idx_contract_status` (`contract_id`, `payment_status`),
    INDEX `idx_invoice_number` (`invoice_number`),
    INDEX `idx_due_date` (`due_date`, `payment_status`),
    INDEX `idx_type` (`invoice_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- Indexes for performance
-- =====================================================================

-- These may already exist, the PHP runner handles duplicates gracefully
ALTER TABLE `companyquotation`
ADD INDEX `idx_work_schedule` (`work_schedule_type`, `working_days_per_week`);

ALTER TABLE `contract`
ADD INDEX `idx_undo_deadline` (`undo_deadline`);

ALTER TABLE `contract`
ADD INDEX `idx_chat_active` (`chat_active`);
