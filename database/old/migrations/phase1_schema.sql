-- Phase 1: Work Schedule Specifications
-- Adds transparency to quotation work arrangements

-- Add work schedule columns to companyquotation
ALTER TABLE `companyquotation`
ADD COLUMN `work_schedule_type` ENUM('all_days', 'weekdays_only', 'weekends_included', 'custom') 
    NOT NULL DEFAULT 'weekdays_only' 
    COMMENT 'Working days pattern' 
    AFTER `estimated_duration`,
ADD COLUMN `working_days_per_week` TINYINT(1) NULL 
    COMMENT 'Number of working days per week (5, 6, or 7)' 
    AFTER `work_schedule_type`,
ADD COLUMN `daily_work_hours` DECIMAL(4,2) NULL 
    COMMENT 'Working hours per day (e.g., 8.00, 6.50)' 
    AFTER `working_days_per_week`,
ADD COLUMN `work_start_time` TIME NULL 
    COMMENT 'Daily work start time (e.g., 08:00:00)' 
    AFTER `daily_work_hours`,
ADD COLUMN `work_end_time` TIME NULL 
    COMMENT 'Daily work end time (e.g., 17:00:00)' 
    AFTER `work_start_time`,
ADD COLUMN `custom_schedule_details` TEXT NULL 
    COMMENT 'Additional schedule notes (breaks, specific days, etc.)' 
    AFTER `work_end_time`,
ADD COLUMN `total_work_hours` DECIMAL(10,2) NULL 
    COMMENT 'Total estimated work hours for entire project' 
    AFTER `custom_schedule_details`,
ADD COLUMN `overtime_available` BOOLEAN DEFAULT FALSE 
    COMMENT 'Whether overtime work is possible' 
    AFTER `total_work_hours`,
ADD COLUMN `overtime_rate` DECIMAL(10,2) NULL 
    COMMENT 'Hourly rate for overtime (if applicable)' 
    AFTER `overtime_available`;

-- Set default values for existing quotations
UPDATE `companyquotation` 
SET 
    work_schedule_type = 'weekdays_only',
    working_days_per_week = 5,
    daily_work_hours = 8.00,
    work_start_time = '08:00:00',
    work_end_time = '17:00:00',
    overtime_available = FALSE
WHERE work_schedule_type IS NULL;

-- Add index for filtering by schedule type
ALTER TABLE `companyquotation`
ADD INDEX `idx_schedule_type` (`work_schedule_type`);
