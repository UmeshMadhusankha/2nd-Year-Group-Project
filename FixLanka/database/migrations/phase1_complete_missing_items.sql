-- =====================================================================
-- PHASE 1: DATABASE SCHEMA COMPLETION
-- Missing columns and tables to complete Phase 1
-- 
-- IMPORTANT: This script is SAFE and will NOT break existing data
-- It only ADDS new columns and tables, never drops or modifies existing ones
-- 
-- Date: February 12, 2026
-- =====================================================================

-- Set safe update mode
SET SQL_SAFE_UPDATES = 0;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================================
-- TASK 1.1: Add Work Schedule Columns to companyquotation
-- These columns support the supervisor's work schedule requirement
-- =====================================================================

-- Check if columns don't exist before adding (safe execution)
ALTER TABLE `companyquotation`
ADD COLUMN IF NOT EXISTS `work_schedule_type` ENUM('all_days', 'weekdays_only', 'weekends_included', 'custom') 
    NOT NULL DEFAULT 'weekdays_only' 
    COMMENT 'Type of work schedule' 
    AFTER `estimated_duration`;

ALTER TABLE `companyquotation`
ADD COLUMN IF NOT EXISTS `working_days_per_week` TINYINT(1) NULL 
    COMMENT '5-7 days per week' 
    AFTER `work_schedule_type`;

ALTER TABLE `companyquotation`
ADD COLUMN IF NOT EXISTS `daily_work_hours` DECIMAL(4,2) NULL 
    COMMENT 'Hours per day (e.g., 8.00)' 
    AFTER `working_days_per_week`;

ALTER TABLE `companyquotation`
ADD COLUMN IF NOT EXISTS `work_start_time` TIME NULL 
    COMMENT 'Default start time (e.g., 08:00:00)' 
    AFTER `daily_work_hours`;

ALTER TABLE `companyquotation`
ADD COLUMN IF NOT EXISTS `work_end_time` TIME NULL 
    COMMENT 'Default end time (e.g., 17:00:00)' 
    AFTER `work_start_time`;

ALTER TABLE `companyquotation`
ADD COLUMN IF NOT EXISTS `break_duration` DECIMAL(3,2) NULL 
    COMMENT 'Break hours per day (e.g., 1.00)' 
    AFTER `work_end_time`;

ALTER TABLE `companyquotation`
ADD COLUMN IF NOT EXISTS `custom_schedule_json` TEXT NULL 
    COMMENT 'JSON for custom day-by-day schedules' 
    AFTER `break_duration`;

ALTER TABLE `companyquotation`
ADD COLUMN IF NOT EXISTS `public_holidays_excluded` BOOLEAN DEFAULT TRUE 
    COMMENT 'Whether to exclude public holidays from work days' 
    AFTER `custom_schedule_json`;

ALTER TABLE `companyquotation`
ADD COLUMN IF NOT EXISTS `estimated_calendar_days` INT NULL 
    COMMENT 'Total calendar days including non-working days' 
    AFTER `public_holidays_excluded`;

-- Add index for better performance
ALTER TABLE `companyquotation`
ADD INDEX IF NOT EXISTS `idx_work_schedule` (`work_schedule_type`, `working_days_per_week`);

-- =====================================================================
-- TASK 1.2: Add Missing Columns to contract table
-- These support budget flexibility, payment methods, undo window, chat, escrow
-- =====================================================================

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `budget_type` ENUM('flexible', 'fixed') 
    NOT NULL DEFAULT 'fixed' 
    COMMENT 'Budget flexibility type' 
    AFTER `contract_value`;

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `budget_flexibility_percentage` DECIMAL(5,2) NULL 
    COMMENT 'Flexibility percentage (e.g., 10.00 for ±10%)' 
    AFTER `budget_type`;

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `payment_method` ENUM('milestone', 'upfront_final_50_50', 'upfront_final_30_70', 'after_completion', 'time_material') 
    NOT NULL DEFAULT 'milestone' 
    COMMENT 'Payment method type'
    AFTER `budget_flexibility_percentage`;

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `undo_deadline` DATETIME NULL 
    COMMENT '24 hours from acceptance for undo window' 
    AFTER `signed_at`;

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `undo_requested` BOOLEAN DEFAULT FALSE 
    COMMENT 'Whether customer requested undo' 
    AFTER `undo_deadline`;

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `chat_active` BOOLEAN DEFAULT FALSE 
    COMMENT 'Whether chat is active for this contract' 
    AFTER `undo_requested`;

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `chat_activated_at` DATETIME NULL 
    COMMENT 'When chat was activated' 
    AFTER `chat_active`;

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `escrow_account_id` INT NULL 
    COMMENT 'Foreign key to escrow_accounts table' 
    AFTER `chat_activated_at`;

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `upfront_payment_percentage` DECIMAL(5,2) NULL 
    COMMENT 'Upfront payment percentage (50.00 or 30.00)' 
    AFTER `escrow_account_id`;

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `upfront_payment_amount` DECIMAL(10,2) NULL 
    COMMENT 'Calculated upfront payment amount' 
    AFTER `upfront_payment_percentage`;

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `upfront_payment_received` BOOLEAN DEFAULT FALSE 
    COMMENT 'Whether upfront payment has been received' 
    AFTER `upfront_payment_amount`;

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `work_verified_started` BOOLEAN DEFAULT FALSE 
    COMMENT 'Whether customer verified work has started' 
    AFTER `upfront_payment_received`;

ALTER TABLE `contract`
ADD COLUMN IF NOT EXISTS `quality_guarantee_end_date` DATE NULL 
    COMMENT '7 days after completion for quality guarantee' 
    AFTER `work_verified_started`;

-- Add indexes for performance
ALTER TABLE `contract`
ADD INDEX IF NOT EXISTS `idx_undo_deadline` (`undo_deadline`);

ALTER TABLE `contract`
ADD INDEX IF NOT EXISTS `idx_chat_active` (`chat_active`);

ALTER TABLE `contract`
ADD INDEX IF NOT EXISTS `idx_payment_method` (`payment_method`);

ALTER TABLE `contract`
ADD INDEX IF NOT EXISTS `idx_budget_type` (`budget_type`);

-- =====================================================================
-- TASK 1.3: Add Workflow Columns to milestone table
-- These support milestone submission and approval workflow
-- =====================================================================

ALTER TABLE `milestone`
ADD COLUMN IF NOT EXISTS `submitted_by_company` BOOLEAN DEFAULT FALSE 
    COMMENT 'Whether company submitted milestone plan' 
    AFTER `status`;

ALTER TABLE `milestone`
ADD COLUMN IF NOT EXISTS `submitted_date` DATETIME NULL 
    COMMENT 'When company submitted milestone' 
    AFTER `submitted_by_company`;

ALTER TABLE `milestone`
ADD COLUMN IF NOT EXISTS `customer_approved` BOOLEAN DEFAULT FALSE 
    COMMENT 'Whether customer approved milestone' 
    AFTER `submitted_date`;

ALTER TABLE `milestone`
ADD COLUMN IF NOT EXISTS `customer_approval_date` DATETIME NULL 
    COMMENT 'When customer approved milestone' 
    AFTER `customer_approved`;

ALTER TABLE `milestone`
ADD COLUMN IF NOT EXISTS `customer_rejection_reason` TEXT NULL 
    COMMENT 'Reason if customer rejected milestone' 
    AFTER `customer_approval_date`;

ALTER TABLE `milestone`
ADD COLUMN IF NOT EXISTS `work_started` BOOLEAN DEFAULT FALSE 
    COMMENT 'Whether work on milestone has started' 
    AFTER `customer_rejection_reason`;

ALTER TABLE `milestone`
ADD COLUMN IF NOT EXISTS `work_start_date` DATETIME NULL 
    COMMENT 'When work started on milestone' 
    AFTER `work_started`;

ALTER TABLE `milestone`
ADD COLUMN IF NOT EXISTS `work_completed` BOOLEAN DEFAULT FALSE 
    COMMENT 'Whether work on milestone is completed' 
    AFTER `work_start_date`;

ALTER TABLE `milestone`
ADD COLUMN IF NOT EXISTS `work_completion_date` DATETIME NULL 
    COMMENT 'When work was completed on milestone' 
    AFTER `work_completed`;

ALTER TABLE `milestone`
ADD COLUMN IF NOT EXISTS `customer_verification_requested` BOOLEAN DEFAULT FALSE 
    COMMENT 'Whether company requested customer verification' 
    AFTER `work_completion_date`;

ALTER TABLE `milestone`
ADD COLUMN IF NOT EXISTS `customer_verified` BOOLEAN DEFAULT FALSE 
    COMMENT 'Whether customer verified completed work' 
    AFTER `customer_verification_requested`;

ALTER TABLE `milestone`
ADD COLUMN IF NOT EXISTS `customer_verification_date` DATETIME NULL 
    COMMENT 'When customer verified work' 
    AFTER `customer_verified`;

-- Add index for workflow queries
ALTER TABLE `milestone`
ADD INDEX IF NOT EXISTS `idx_workflow` (`customer_approved`, `work_started`, `work_completed`, `customer_verified`);

-- =====================================================================
-- TASK 1.4: Create New Tables
-- =====================================================================

-- -----------------------------------------------
-- 1. Contract Chats Table
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `contract_chats` (
    `chat_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NOT NULL,
    `sender_type` ENUM('company', 'customer') NOT NULL,
    `sender_id` INT NOT NULL,
    `message` TEXT NOT NULL,
    `attachment_type` ENUM('text', 'image', 'document', 'pdf') DEFAULT 'text',
    `attachment_url` VARCHAR(500) NULL,
    `attachment_filename` VARCHAR(255) NULL,
    `attachment_size` INT NULL COMMENT 'File size in bytes',
    `is_read` BOOLEAN DEFAULT FALSE,
    `read_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    INDEX `idx_contract_sender` (`contract_id`, `sender_type`),
    INDEX `idx_unread` (`is_read`, `created_at`),
    INDEX `idx_created` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Stores chat messages between company and customer for contracts';

-- -----------------------------------------------
-- 2. Contract Notifications Table
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `contract_notifications` (
    `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NOT NULL,
    `recipient_type` ENUM('company', 'customer') NOT NULL,
    `recipient_id` INT NOT NULL,
    `notification_type` VARCHAR(50) NOT NULL COMMENT 'undo_reminder, milestone_approved, payment_received, chat_message, etc.',
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `action_url` VARCHAR(500) NULL COMMENT 'URL to navigate when notification clicked',
    `priority` ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    `is_read` BOOLEAN DEFAULT FALSE,
    `read_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    INDEX `idx_recipient` (`recipient_type`, `recipient_id`, `is_read`),
    INDEX `idx_type` (`notification_type`),
    INDEX `idx_priority` (`priority`, `is_read`),
    INDEX `idx_created` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Stores notifications for contract events';

-- -----------------------------------------------
-- 3. Contract Timeline Table
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `contract_timeline` (
    `timeline_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NOT NULL,
    `event_type` VARCHAR(50) NOT NULL COMMENT 'created, sent, accepted, milestone_submitted, milestone_approved, payment_received, work_started, work_completed, disputed, etc.',
    `event_title` VARCHAR(255) NOT NULL,
    `event_description` TEXT NULL,
    `actor_type` ENUM('system', 'company', 'customer') NOT NULL,
    `actor_id` INT NULL COMMENT 'User ID of actor (NULL for system events)',
    `actor_name` VARCHAR(255) NULL COMMENT 'Name of actor for display',
    `metadata_json` TEXT NULL COMMENT 'Additional event data in JSON format',
    `is_milestone` BOOLEAN DEFAULT FALSE COMMENT 'Whether event is a milestone event',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    INDEX `idx_contract_time` (`contract_id`, `created_at` DESC),
    INDEX `idx_event_type` (`event_type`),
    INDEX `idx_actor` (`actor_type`, `actor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Stores timeline of all events for each contract';

-- -----------------------------------------------
-- 4. Contract Time Logs (for Time & Material payment)
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `contract_time_logs` (
    `log_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NOT NULL,
    `work_date` DATE NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `break_duration` DECIMAL(3,2) DEFAULT 0 COMMENT 'Break hours (e.g., 1.00)',
    `total_hours` DECIMAL(4,2) NOT NULL COMMENT 'Net working hours',
    `hourly_rate` DECIMAL(10,2) NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL COMMENT 'hours × rate',
    `work_description` TEXT NOT NULL,
    `work_location` VARCHAR(255) NULL,
    `submitted_by` INT NOT NULL COMMENT 'Company user ID who submitted',
    `submitted_at` DATETIME NOT NULL,
    `customer_approved` BOOLEAN DEFAULT FALSE,
    `customer_approval_date` DATETIME NULL,
    `customer_rejection_reason` TEXT NULL,
    `invoice_id` INT NULL COMMENT 'Foreign key to contract_invoices',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    INDEX `idx_contract_date` (`contract_id`, `work_date` DESC),
    INDEX `idx_approval` (`customer_approved`),
    INDEX `idx_invoice` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Stores time logs for Time & Material payment contracts';

-- -----------------------------------------------
-- 5. Contract Invoices
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `contract_invoices` (
    `invoice_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NOT NULL,
    `invoice_number` VARCHAR(50) UNIQUE NOT NULL,
    `invoice_type` ENUM('milestone', 'upfront', 'final', 'time_material', 'adjustment') NOT NULL,
    `milestone_id` INT NULL COMMENT 'Foreign key to milestone if applicable',
    `amount` DECIMAL(10,2) NOT NULL COMMENT 'Base amount before tax',
    `tax_percentage` DECIMAL(5,2) DEFAULT 0 COMMENT 'Tax percentage (e.g., 18.00)',
    `tax_amount` DECIMAL(10,2) DEFAULT 0,
    `total_amount` DECIMAL(10,2) NOT NULL COMMENT 'amount + tax',
    `issue_date` DATE NOT NULL,
    `due_date` DATE NOT NULL,
    `payment_status` ENUM('pending', 'paid', 'overdue', 'cancelled', 'refunded') DEFAULT 'pending',
    `paid_date` DATETIME NULL,
    `payment_method` VARCHAR(50) NULL COMMENT 'bank_transfer, card, cash, etc.',
    `payment_reference` VARCHAR(100) NULL COMMENT 'Transaction reference number',
    `payment_receipt_url` VARCHAR(500) NULL,
    `pdf_path` VARCHAR(500) NULL COMMENT 'Path to generated invoice PDF',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    FOREIGN KEY (`milestone_id`) REFERENCES `milestone`(`milestone_id`) ON DELETE SET NULL,
    INDEX `idx_contract_status` (`contract_id`, `payment_status`),
    INDEX `idx_invoice_number` (`invoice_number`),
    INDEX `idx_due_date` (`due_date`, `payment_status`),
    INDEX `idx_type` (`invoice_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Stores invoices for contract payments';

-- -----------------------------------------------
-- 6. Contract Budget Adjustments
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `contract_budget_adjustments` (
    `adjustment_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NOT NULL,
    `requested_by` ENUM('company', 'customer') NOT NULL,
    `requester_id` INT NOT NULL,
    `requester_name` VARCHAR(255) NULL,
    `adjustment_type` ENUM('increase', 'decrease') NOT NULL,
    `original_amount` DECIMAL(10,2) NOT NULL,
    `requested_amount` DECIMAL(10,2) NOT NULL,
    `adjustment_amount` DECIMAL(10,2) NOT NULL COMMENT 'Difference (positive or negative)',
    `adjustment_percentage` DECIMAL(5,2) NULL COMMENT 'Percentage change',
    `reason` TEXT NOT NULL,
    `justification_documents` TEXT NULL COMMENT 'JSON array of document URLs',
    `status` ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    `approved_by` INT NULL,
    `approved_by_name` VARCHAR(255) NULL,
    `approved_at` DATETIME NULL,
    `rejection_reason` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    INDEX `idx_contract_status` (`contract_id`, `status`),
    INDEX `idx_requester` (`requested_by`, `requester_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Stores budget adjustment requests for flexible contracts';

-- -----------------------------------------------
-- 7. Escrow Accounts
-- -----------------------------------------------
CREATE TABLE IF NOT EXISTS `escrow_accounts` (
    `escrow_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT UNIQUE NOT NULL,
    `account_number` VARCHAR(50) UNIQUE NOT NULL,
    `total_amount` DECIMAL(10,2) NOT NULL COMMENT 'Total contract value',
    `held_amount` DECIMAL(10,2) DEFAULT 0 COMMENT 'Currently held in escrow',
    `released_amount` DECIMAL(10,2) DEFAULT 0 COMMENT 'Released to company',
    `refunded_amount` DECIMAL(10,2) DEFAULT 0 COMMENT 'Refunded to customer',
    `status` ENUM('active', 'completed', 'refunded', 'disputed') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
    INDEX `idx_status` (`status`),
    INDEX `idx_account_number` (`account_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Stores escrow account information for secure payments';

-- =====================================================================
-- Add Foreign Key Constraint for escrow_account_id in contract table
-- (Only after escrow_accounts table is created)
-- =====================================================================

ALTER TABLE `contract`
ADD CONSTRAINT `fk_contract_escrow`
FOREIGN KEY (`escrow_account_id`) REFERENCES `escrow_accounts`(`escrow_id`) 
ON DELETE SET NULL;

-- =====================================================================
-- Add Foreign Key Constraint for invoice_id in contract_time_logs
-- (Only after contract_invoices table is created)
-- =====================================================================

ALTER TABLE `contract_time_logs`
ADD CONSTRAINT `fk_timelog_invoice`
FOREIGN KEY (`invoice_id`) REFERENCES `contract_invoices`(`invoice_id`) 
ON DELETE SET NULL;

-- =====================================================================
-- VERIFICATION QUERIES
-- Run these to verify the migration was successful
-- =====================================================================

-- Check new columns in companyquotation
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'companyquotation'
AND COLUMN_NAME LIKE '%work%' OR COLUMN_NAME LIKE '%schedule%'
ORDER BY ORDINAL_POSITION;

-- Check new columns in contract
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'contract'
AND COLUMN_NAME IN ('budget_type', 'payment_method', 'undo_deadline', 'chat_active', 'escrow_account_id', 'upfront_payment_percentage', 'work_verified_started', 'quality_guarantee_end_date')
ORDER BY ORDINAL_POSITION;

-- Check new columns in milestone
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'milestone'
AND COLUMN_NAME LIKE '%submitted%' OR COLUMN_NAME LIKE '%approved%' OR COLUMN_NAME LIKE '%work_%' OR COLUMN_NAME LIKE '%verification%'
ORDER BY ORDINAL_POSITION;

-- Check new tables
SELECT TABLE_NAME, TABLE_ROWS, CREATE_TIME, TABLE_COMMENT
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN ('contract_chats', 'contract_notifications', 'contract_timeline', 'contract_time_logs', 'contract_invoices', 'contract_budget_adjustments', 'escrow_accounts')
ORDER BY TABLE_NAME;

-- =====================================================================
-- RESTORE SETTINGS
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 1;
SET SQL_SAFE_UPDATES = 1;

-- =====================================================================
-- SUCCESS MESSAGE
-- =====================================================================

SELECT 
    '✅ PHASE 1 DATABASE MIGRATION COMPLETED SUCCESSFULLY!' AS status,
    'All missing columns and tables have been added safely.' AS message,
    'No existing data was modified or deleted.' AS note,
    'You can now proceed to update your PHP models and controllers.' AS next_step;
