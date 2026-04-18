-- ============================================================
-- Migration: Company Quotation (Units + Pricing + Work Schedule)
-- Adds unit-label fields (per hour/per m²/per unit), pricing/payment fields,
-- and Phase-1 work schedule fields to `companyquotation`.
--
-- Safe to run multiple times:
-- - Uses INFORMATION_SCHEMA checks + dynamic SQL to only add missing columns/indexes
-- - Only expands ENUMs when the column is already an ENUM
--
-- Target DB: fix_lanka (MariaDB/MySQL)
-- ============================================================

-- Make sure you are using the correct database
-- USE `fix_lanka`;

SET @db := DATABASE();

-- -------------------------------
-- 1) Add missing columns
-- -------------------------------

-- company_id (newer schemas store the company actor here)
SET @col_exists := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'company_id'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `company_id` INT(11) NULL AFTER `user_id`',
  'SELECT "companyquotation.company_id already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Unit labels
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'labor_unit_label'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `labor_unit_label` VARCHAR(50) NULL DEFAULT NULL COMMENT ''e.g. per hour, per m2, per unit'' AFTER `additional_terms`',
  'SELECT "companyquotation.labor_unit_label already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'material_unit_label'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `material_unit_label` VARCHAR(50) NULL DEFAULT NULL COMMENT ''e.g. per m2, per unit'' AFTER `labor_unit_label`',
  'SELECT "companyquotation.material_unit_label already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Budget flexibility + pricing/payment structure
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'budget_type'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `budget_type` ENUM(''fixed'',''flexible'') NULL DEFAULT ''fixed'' COMMENT ''Fixed or Flexible (±10%)'' AFTER `material_unit_label`',
  'SELECT "companyquotation.budget_type already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'budget_min'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `budget_min` DECIMAL(10,2) NULL DEFAULT NULL COMMENT ''Minimum budget for flexible pricing'' AFTER `budget_type`',
  'SELECT "companyquotation.budget_min already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'budget_max'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `budget_max` DECIMAL(10,2) NULL DEFAULT NULL COMMENT ''Maximum budget for flexible pricing'' AFTER `budget_min`',
  'SELECT "companyquotation.budget_max already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'payment_method'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `payment_method` ENUM(''full_upfront'',''milestone_based'',''50_50'',''30_70'',''completion'',''time_and_material'') NULL DEFAULT ''full_upfront'' COMMENT ''Payment method selected'' AFTER `budget_max`',
  'SELECT "companyquotation.payment_method already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'pricing_type'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `pricing_type` ENUM(''fixed_price'',''time_and_material'') NULL DEFAULT ''fixed_price'' COMMENT ''Pricing structure type'' AFTER `payment_method`',
  'SELECT "companyquotation.pricing_type already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'hourly_rate'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `hourly_rate` DECIMAL(10,2) NULL DEFAULT NULL COMMENT ''Hourly rate for Time & Material'' AFTER `pricing_type`',
  'SELECT "companyquotation.hourly_rate already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'spending_cap_multiplier'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `spending_cap_multiplier` DECIMAL(3,2) NULL DEFAULT 1.10 COMMENT ''Spending cap multiplier for T&M (default 1.10 = 110%)'' AFTER `hourly_rate`',
  'SELECT "companyquotation.spending_cap_multiplier already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Work schedule fields (Phase 1)
SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'work_schedule_type'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `work_schedule_type` ENUM(''all_days'',''weekdays_only'',''weekends_included'',''custom'') NOT NULL DEFAULT ''weekdays_only'' AFTER `estimated_duration`',
  'SELECT "companyquotation.work_schedule_type already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'working_days_per_week'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `working_days_per_week` TINYINT(1) NULL DEFAULT NULL AFTER `work_schedule_type`',
  'SELECT "companyquotation.working_days_per_week already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'daily_work_hours'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `daily_work_hours` DECIMAL(4,2) NULL DEFAULT NULL AFTER `working_days_per_week`',
  'SELECT "companyquotation.daily_work_hours already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'work_start_time'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `work_start_time` TIME NULL DEFAULT NULL AFTER `daily_work_hours`',
  'SELECT "companyquotation.work_start_time already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'work_end_time'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `work_end_time` TIME NULL DEFAULT NULL AFTER `work_start_time`',
  'SELECT "companyquotation.work_end_time already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'break_duration'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `break_duration` DECIMAL(3,2) NULL DEFAULT NULL AFTER `work_end_time`',
  'SELECT "companyquotation.break_duration already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'custom_schedule_json'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `custom_schedule_json` TEXT NULL DEFAULT NULL AFTER `break_duration`',
  'SELECT "companyquotation.custom_schedule_json already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'public_holidays_excluded'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `public_holidays_excluded` BOOLEAN NULL DEFAULT TRUE AFTER `custom_schedule_json`',
  'SELECT "companyquotation.public_holidays_excluded already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'estimated_calendar_days'
);
SET @sql := IF(
  @col_exists = 0,
  'ALTER TABLE `companyquotation` ADD COLUMN `estimated_calendar_days` INT NULL DEFAULT NULL AFTER `public_holidays_excluded`',
  'SELECT "companyquotation.estimated_calendar_days already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -------------------------------
-- 2) Expand ENUMs (only if they are already ENUM)
--    (This prevents breaking DBs where these columns are VARCHAR)
-- -------------------------------

-- payment_method: ensure it includes `completion` and `time_and_material`
SET @is_enum := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'payment_method' AND DATA_TYPE = 'enum'
);
SET @needs_expand := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'payment_method' AND DATA_TYPE = 'enum'
    AND (COLUMN_TYPE NOT LIKE '%completion%' OR COLUMN_TYPE NOT LIKE '%time_and_material%')
);
SET @sql := IF(
  @is_enum = 1 AND @needs_expand = 1,
  'ALTER TABLE `companyquotation` MODIFY COLUMN `payment_method` ENUM(''full_upfront'',''milestone_based'',''50_50'',''30_70'',''completion'',''time_and_material'') NULL DEFAULT ''full_upfront'' COMMENT ''Payment method selected''',
  'SELECT "companyquotation.payment_method enum expansion not needed (or not enum)"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- pricing_type: ensure it includes time_and_material
SET @is_enum := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'pricing_type' AND DATA_TYPE = 'enum'
);
SET @needs_expand := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'pricing_type' AND DATA_TYPE = 'enum'
    AND COLUMN_TYPE NOT LIKE '%time_and_material%'
);
SET @sql := IF(
  @is_enum = 1 AND @needs_expand = 1,
  'ALTER TABLE `companyquotation` MODIFY COLUMN `pricing_type` ENUM(''fixed_price'',''time_and_material'') NULL DEFAULT ''fixed_price'' COMMENT ''Pricing structure type''',
  'SELECT "companyquotation.pricing_type enum expansion not needed (or not enum)"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- budget_type: ensure it includes flexible
SET @is_enum := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'budget_type' AND DATA_TYPE = 'enum'
);
SET @needs_expand := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'budget_type' AND DATA_TYPE = 'enum'
    AND COLUMN_TYPE NOT LIKE '%flexible%'
);
SET @sql := IF(
  @is_enum = 1 AND @needs_expand = 1,
  'ALTER TABLE `companyquotation` MODIFY COLUMN `budget_type` ENUM(''fixed'',''flexible'') NULL DEFAULT ''fixed'' COMMENT ''Fixed or Flexible (±10%)''',
  'SELECT "companyquotation.budget_type enum expansion not needed (or not enum)"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -------------------------------
-- 3) Add useful indexes (only if missing)
-- -------------------------------

SET @idx_exists := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND INDEX_NAME = 'idx_budget_type'
);
SET @sql := IF(
  @idx_exists = 0,
  'ALTER TABLE `companyquotation` ADD INDEX `idx_budget_type` (`budget_type`)',
  'SELECT "idx_budget_type already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND INDEX_NAME = 'idx_payment_method'
);
SET @sql := IF(
  @idx_exists = 0,
  'ALTER TABLE `companyquotation` ADD INDEX `idx_payment_method` (`payment_method`)',
  'SELECT "idx_payment_method already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND INDEX_NAME = 'idx_pricing_type'
);
SET @sql := IF(
  @idx_exists = 0,
  'ALTER TABLE `companyquotation` ADD INDEX `idx_pricing_type` (`pricing_type`)',
  'SELECT "idx_pricing_type already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @idx_exists := (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'companyquotation' AND INDEX_NAME = 'idx_work_schedule'
);
SET @sql := IF(
  @idx_exists = 0,
  'ALTER TABLE `companyquotation` ADD INDEX `idx_work_schedule` (`work_schedule_type`, `working_days_per_week`)',
  'SELECT "idx_work_schedule already exists"'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -------------------------------
-- 4) Optional backfill for legacy data
-- -------------------------------
-- If older DBs stored the company ID in `user_id`, you likely want this.
-- Review first:
--   SELECT COUNT(*) AS missing_company_id FROM companyquotation WHERE company_id IS NULL;
-- Then run:
--   UPDATE companyquotation SET company_id = user_id WHERE company_id IS NULL;
