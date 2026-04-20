-- Auto-generated schema-only union of fix_lanka.sql + fix_lanka_extra.sql
-- Contains CREATE TABLE + ALTER TABLE (indexes/auto-increment/FKs). No INSERT data.
-- NOTE: 1 column-definition conflicts exist across dumps; CREATE TABLE taken from fix_lanka_extra.sql where available.

DROP DATABASE IF EXISTS fix_lanka;
CREATE DATABASE fix_lanka;
USE fix_lanka;

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';
SET FOREIGN_KEY_CHECKS=0;

-- Table `account_moderation_cases`
CREATE TABLE `account_moderation_cases` (
  `case_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL COMMENT 'User, Repairer, or Company ID',
  `target_type` enum('User','Repairer','Company') NOT NULL,
  `admin_username` varchar(100) DEFAULT NULL COMMENT 'Admin who performed action',
  `action_type` enum('BAN','SUSPEND','RESTORE','WARNING') NOT NULL,
  `reason` text NOT NULL,
  `notes` text DEFAULT NULL,
  `duration_days` int(11) DEFAULT NULL COMMENT 'NULL for permanent ban',
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL COMMENT 'NULL for permanent ban',
  `status_before` enum('ACTIVE','SUSPENDED','BANNED') NOT NULL,
  `status_after` enum('ACTIVE','SUSPENDED','BANNED') NOT NULL,
  `is_permanent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `account_moderation_log`
CREATE TABLE `account_moderation_log` (
  `log_id` int(11) NOT NULL,
  `account_type` varchar(30) NOT NULL,
  `account_id` int(11) NOT NULL,
  `action_type` enum('suspend','ban','restore') NOT NULL,
  `reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `suspended_until` datetime DEFAULT NULL,
  `acted_by_role` varchar(30) DEFAULT NULL,
  `acted_by_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `account_moderation_status`
CREATE TABLE `account_moderation_status` (
  `status_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `account_type` enum('User','Repairer','Company') NOT NULL,
  `account_status` enum('ACTIVE','SUSPENDED','BANNED') DEFAULT 'ACTIVE',
  `banned_permanent` tinyint(1) DEFAULT 0,
  `suspended_until` datetime DEFAULT NULL,
  `moderation_reason` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `activitylog`
CREATE TABLE `activitylog` (
  `activity_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `admin`
CREATE TABLE `admin` (
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `adminalert`
CREATE TABLE `adminalert` (
  `alert_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `target_role` enum('User','Repairer','Company','Moderator','All') NOT NULL DEFAULT 'All',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `created_by` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `admin_notifications`
CREATE TABLE `admin_notifications` (
  `notification_id` int(11) NOT NULL,
  `admin_username` varchar(100) NOT NULL,
  `notification_type` enum('ACCOUNT_BANNED','ACCOUNT_SUSPENDED','ACCOUNT_RESTORED','STATUS_CHANGED') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `account_id` int(11) NOT NULL,
  `account_type` enum('User','Repairer','Company') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `admin_override_history`
CREATE TABLE `admin_override_history` (
  `override_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `admin_username` varchar(100) NOT NULL,
  `previous_status` enum('pending','approved','rejected','scheduled','active','paused','inactive','suspended') NOT NULL,
  `new_status` enum('pending','approved','rejected','scheduled','active','paused','inactive','suspended') NOT NULL,
  `override_reason` text NOT NULL,
  `previous_moderator_id` int(11) DEFAULT NULL,
  `override_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `adreport`
CREATE TABLE `adreport` (
  `report_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `reporter_type` enum('user','repairer','company') NOT NULL DEFAULT 'user',
  `reporter_id` int(11) DEFAULT NULL,
  `issue_type` enum('inappropriate_content','misleading_information','spam','privacy_violation','copyright_infringement','fraud','other') NOT NULL DEFAULT 'other',
  `description` text NOT NULL,
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'low',
  `status` enum('pending','investigating','resolved','dismissed','escalated') NOT NULL DEFAULT 'pending',
  `moderator_notes` text DEFAULT NULL,
  `evidence` varchar(500) DEFAULT NULL,
  `handled_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `adrotationsettings`
CREATE TABLE `adrotationsettings` (
  `setting_id` int(11) NOT NULL,
  `banner_seconds` int(11) NOT NULL DEFAULT 30,
  `featured_seconds` int(11) NOT NULL DEFAULT 60,
  `sponsored_seconds` int(11) NOT NULL DEFAULT 90,
  `banner_capacity` int(11) NOT NULL DEFAULT 5,
  `featured_capacity` int(11) NOT NULL DEFAULT 3,
  `sponsored_capacity` int(11) NOT NULL DEFAULT 8,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `adschedule`
CREATE TABLE `adschedule` (
  `schedule_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `advertisement`
CREATE TABLE `advertisement` (
  `ad_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `provider_type` enum('company','repairer') NOT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `type` enum('banner','featured','sponsored') NOT NULL DEFAULT 'banner',
  `budget` decimal(10,2) DEFAULT 0.00,
  `image_url` varchar(255) DEFAULT NULL,
  `target_audience` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('pending','approved','rejected','scheduled','active','paused','inactive','suspended','expired') NOT NULL DEFAULT 'pending',
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `clicks` int(11) DEFAULT 0,
  `impressions` int(11) DEFAULT 0,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `moderator_notes` text DEFAULT NULL,
  `admin_reviewed_by` varchar(50) DEFAULT NULL,
  `admin_reviewed_at` timestamp NULL DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `override_reason` text DEFAULT NULL,
  `is_override` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `ad_reports`
CREATE TABLE `ad_reports` (
  `report_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `reporter_type` enum('user','repairer','company','moderator') NOT NULL DEFAULT 'user',
  `report_category` enum('inappropriate_content','misleading_information','spam','copyright_violation','offensive_material','false_advertising','broken_link','poor_quality','other') NOT NULL,
  `description` text NOT NULL,
  `severity` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status` enum('pending','investigating','resolved','dismissed','escalated') NOT NULL DEFAULT 'pending',
  `assigned_to` int(11) DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `ad_schedules`
CREATE TABLE `ad_schedules` (
  `schedule_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `placement` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time DEFAULT '00:00:00',
  `end_time` time DEFAULT '23:59:59',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` varchar(20) DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `ad_status_history`
CREATE TABLE `ad_status_history` (
  `history_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `old_status` varchar(20) DEFAULT NULL,
  `new_status` varchar(20) NOT NULL,
  `changed_by_role` enum('moderator','admin','system') NOT NULL,
  `changed_by_id` varchar(50) NOT NULL,
  `reason` text DEFAULT NULL,
  `is_override` tinyint(1) DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `billinghistory`
CREATE TABLE `billinghistory` (
  `invoice_id` varchar(50) NOT NULL,
  `company_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('paid','pending','failed') DEFAULT 'pending',
  `download_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `category`
CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `chatmessage`
CREATE TABLE `chatmessage` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `firebase_chat_id` varchar(255) DEFAULT NULL,
  `last_message` text DEFAULT NULL,
  `last_message_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `company`
CREATE TABLE `company` (
  `company_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `business_type` varchar(255) DEFAULT NULL,
  `registration_no` varchar(100) NOT NULL,
  `tax_id` varchar(100) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `address` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `contact_no` varchar(20) NOT NULL,
  `districts` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `date_of_joined` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  `logo` varchar(255) DEFAULT NULL,
  `established_year` int(11) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `alternate_phone` varchar(20) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `skills` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `companyemployee`
CREATE TABLE `companyemployee` (
  `employee_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `repairer_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `specialty` varchar(100) NOT NULL,
  `hourly_rate` decimal(10,2) DEFAULT 0.00,
  `rating` decimal(3,2) DEFAULT 0.00,
  `status` enum('active','inactive','on_leave') DEFAULT 'active',
  `hire_date` date DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `certification_details` text DEFAULT NULL,
  `profile_photo` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `companyjobpost`
CREATE TABLE `companyjobpost` (
  `posting_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `employment_type` enum('freelance','contract','part-time','project-based') NOT NULL,
  `related_project_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `min_experience` enum('entry','junior','mid','senior','expert') NOT NULL,
  `priority_level` enum('low','medium','high','urgent') DEFAULT 'medium',
  `min_budget` decimal(10,2) NOT NULL,
  `max_budget` decimal(10,2) NOT NULL,
  `application_deadline` date DEFAULT NULL,
  `required_skills` text DEFAULT NULL,
  `location` varchar(500) NOT NULL,
  `location_requirements` varchar(500) DEFAULT NULL,
  `status` enum('draft','open','closed','filled') DEFAULT 'draft',
  `notify_repairers` tinyint(1) DEFAULT 1,
  `allow_direct_applications` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `posted_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `closed_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `companyquotation`
CREATE TABLE `companyquotation` (
  `quotation_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL COMMENT 'Company providing quotation',
  `user_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `labor_cost` decimal(10,2) NOT NULL,
  `material_cost` decimal(10,2) NOT NULL,
  `transport_cost` decimal(10,2) DEFAULT 0.00,
  `other_charges` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `completion_date` date NOT NULL,
  `estimated_duration` int(11) NOT NULL,
  `work_schedule_type` enum('all_days','weekdays_only','weekends_included','custom') NOT NULL DEFAULT 'weekdays_only' COMMENT 'Working days pattern: all days (7/week), weekdays only (Mon-Fri), weekends included (Mon-Sat), or custom',
  `working_days_per_week` tinyint(1) DEFAULT NULL COMMENT 'Number of working days per week (1-7)',
  `daily_work_hours` decimal(4,2) DEFAULT NULL COMMENT 'Working hours per day (e.g., 8.00, 6.50, 10.00)',
  `work_start_time` time DEFAULT NULL COMMENT 'Daily work start time (e.g., 08:00:00)',
  `work_end_time` time DEFAULT NULL COMMENT 'Daily work end time (e.g., 17:00:00)',
  `break_duration` decimal(3,2) DEFAULT NULL COMMENT 'Break hours per day (e.g., 1.00)',
  `custom_schedule_json` text DEFAULT NULL COMMENT 'JSON for custom day-by-day schedules',
  `public_holidays_excluded` tinyint(1) DEFAULT 1 COMMENT 'Whether to exclude public holidays from work days',
  `estimated_calendar_days` int(11) DEFAULT NULL COMMENT 'Total calendar days including non-working days',
  `custom_schedule_details` text DEFAULT NULL COMMENT 'Additional schedule notes (breaks, specific days, shift information, etc.)',
  `total_work_hours` decimal(10,2) DEFAULT NULL COMMENT 'Total estimated work hours for entire project (auto-calculated)',
  `overtime_available` tinyint(1) DEFAULT 0 COMMENT 'Whether overtime work is possible',
  `overtime_rate` decimal(10,2) DEFAULT NULL COMMENT 'Hourly rate for overtime work (LKR per hour)',
  `payment_terms` varchar(100) DEFAULT NULL,
  `warranty_period` varchar(50) DEFAULT NULL,
  `labor_unit_label` varchar(50) DEFAULT NULL,
  `material_unit_label` varchar(50) DEFAULT NULL,
  `additional_terms` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected','successful','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `budget_type` enum('fixed','flexible') DEFAULT 'fixed' COMMENT 'Fixed or Flexible (±10%)',
  `budget_min` decimal(10,2) DEFAULT NULL COMMENT 'Minimum budget for flexible pricing',
  `budget_max` decimal(10,2) DEFAULT NULL COMMENT 'Maximum budget for flexible pricing',
  `payment_method` enum('full_upfront','milestone_based','50_50','30_70','completion','time_and_material') DEFAULT 'full_upfront' COMMENT 'Payment method selected',
  `pricing_type` enum('fixed_price','time_and_material') DEFAULT 'fixed_price' COMMENT 'Pricing structure type',
  `hourly_rate` decimal(10,2) DEFAULT NULL COMMENT 'Hourly rate for Time & Material',
  `spending_cap_multiplier` decimal(3,2) DEFAULT 1.10 COMMENT 'Spending cap multiplier for T&M (default 1.10 = 110%)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `companysettings`
CREATE TABLE `companysettings` (
  `setting_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `email_repair_requests` tinyint(1) DEFAULT 1,
  `email_project_updates` tinyint(1) DEFAULT 1,
  `email_payments` tinyint(1) DEFAULT 1,
  `email_team_activity` tinyint(1) DEFAULT 0,
  `email_messages` tinyint(1) DEFAULT 1,
  `push_desktop` tinyint(1) DEFAULT 1,
  `push_mobile` tinyint(1) DEFAULT 0,
  `quiet_hours_start` time DEFAULT '22:00:00',
  `quiet_hours_end` time DEFAULT '08:00:00',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `company_employees`
CREATE TABLE `company_employees` (
  `employee_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','freelance') NOT NULL DEFAULT 'freelance',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `hired_date` date NOT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `company_jobpost`
CREATE TABLE `company_jobpost` (
  `posting_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','freelance') NOT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL,
  `min_experience` int(11) NOT NULL DEFAULT 0,
  `min_budget` decimal(10,2) NOT NULL,
  `max_budget` decimal(10,2) NOT NULL,
  `location` varchar(255) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `status` enum('draft','open','closed','filled') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `company_subscriptions`
CREATE TABLE `company_subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `plan_name` enum('free','basic','professional','enterprise') DEFAULT 'free',
  `plan_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `billing_period` enum('monthly','yearly') DEFAULT 'monthly',
  `status` enum('active','cancelled','expired','trial') DEFAULT 'trial',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `next_billing_date` date DEFAULT NULL,
  `auto_renew` tinyint(1) DEFAULT 1,
  `trial_ends_at` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `contract`
CREATE TABLE `contract` (
  `contract_id` int(11) NOT NULL,
  `contract_number` varchar(50) DEFAULT NULL,
  `quotation_id` int(11) DEFAULT NULL COMMENT 'Link to accepted quotation',
  `company_id` int(11) NOT NULL COMMENT 'Company providing service',
  `customer_id` int(11) NOT NULL COMMENT 'Customer receiving service',
  `job_request_id` int(11) DEFAULT NULL COMMENT 'Original job request',
  `project_id` int(11) NOT NULL,
  `project_title` varchar(255) DEFAULT NULL,
  `project_reference` varchar(100) DEFAULT NULL,
  `project_location` text DEFAULT NULL,
  `project_description` text DEFAULT NULL,
  `scope_description` text DEFAULT NULL,
  `scope_inclusions` text DEFAULT NULL,
  `scope_exclusions` text DEFAULT NULL,
  `scope_standards` text DEFAULT NULL,
  `materials_responsibility` enum('company','client','shared') DEFAULT 'company',
  `milestone_plan` tinyint(1) NOT NULL DEFAULT 1,
  `total_milestones` int(11) DEFAULT 0 COMMENT 'Total milestone count',
  `completed_milestones` int(11) DEFAULT 0 COMMENT 'Completed count',
  `progress_percentage` int(11) DEFAULT 0 COMMENT 'Overall progress',
  `auto_generated` tinyint(1) DEFAULT 1 COMMENT 'Auto-created from quotation',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `total_budget` decimal(12,2) NOT NULL,
  `budget_type` enum('fixed','time_based','flexible') DEFAULT 'fixed',
  `budget_flexibility_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Flexibility percentage (e.g., 10.00 for ??10%)',
  `budget_min` decimal(12,2) DEFAULT NULL COMMENT 'Minimum for flexible budget',
  `budget_max` decimal(12,2) DEFAULT NULL COMMENT 'Maximum for flexible budget',
  `tax_inclusive` tinyint(1) DEFAULT 1,
  `payment_method` enum('full_upfront','milestone_based','50_50','30_70','completion') DEFAULT 'milestone_based' COMMENT 'Payment structure',
  `advance_payment_pct` decimal(5,2) DEFAULT 0.00,
  `pricing_type` enum('fixed_price','time_and_material') DEFAULT 'fixed_price' COMMENT 'Pricing model',
  `hourly_rate` decimal(10,2) DEFAULT NULL COMMENT 'For T&M contracts',
  `spending_cap` decimal(12,2) DEFAULT NULL COMMENT 'Maximum spending for T&M',
  `late_payment_penalty` text DEFAULT NULL,
  `pause_work_clause` tinyint(1) DEFAULT 1,
  `time_extension_clause` tinyint(1) DEFAULT 1,
  `variation_clause` tinyint(1) DEFAULT 1,
  `communication_channel` varchar(100) DEFAULT 'system',
  `dispute_resolution` text DEFAULT NULL,
  `actual_hours` decimal(10,2) DEFAULT 0.00 COMMENT 'Tracked hours for T&M',
  `actual_cost` decimal(12,2) DEFAULT 0.00 COMMENT 'Current cost for T&M',
  `amount_paid` decimal(12,2) DEFAULT 0.00 COMMENT 'Total paid so far',
  `amount_pending` decimal(12,2) DEFAULT 0.00 COMMENT 'Remaining payment',
  `payment_status` enum('pending','partial','completed','overdue') DEFAULT 'pending' COMMENT 'Payment tracking',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `terms_accepted` tinyint(1) DEFAULT 0,
  `terms_accepted_at` datetime DEFAULT NULL,
  `contract_date` date NOT NULL,
  `user_signature` varchar(255) NOT NULL,
  `company_signature` varchar(255) NOT NULL,
  `customer_signature` text DEFAULT NULL,
  `signed_at` datetime DEFAULT NULL,
  `labor_cost` decimal(12,2) DEFAULT NULL,
  `material_cost` decimal(12,2) DEFAULT NULL,
  `transport_cost` decimal(12,2) DEFAULT NULL,
  `other_charges` decimal(12,2) DEFAULT NULL,
  `labor_unit_label` varchar(50) DEFAULT NULL,
  `material_unit_label` varchar(50) DEFAULT NULL,
  `undo_deadline` datetime DEFAULT NULL COMMENT '24 hours from acceptance for undo window',
  `undo_available` tinyint(1) DEFAULT 1 COMMENT 'Flag to indicate if undo is still possible',
  `undo_requested` tinyint(1) DEFAULT 0 COMMENT 'Whether customer requested undo',
  `chat_active` tinyint(1) DEFAULT 0 COMMENT 'Whether chat is active for this contract',
  `escrow_enabled` tinyint(1) DEFAULT 1 COMMENT 'Whether escrow service is enabled',
  `chat_activated_at` datetime DEFAULT NULL COMMENT 'When chat was activated',
  `escrow_account_id` int(11) DEFAULT NULL COMMENT 'Foreign key to escrow_accounts table',
  `upfront_payment_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Upfront payment percentage (50.00 or 30.00)',
  `upfront_payment_amount` decimal(10,2) DEFAULT NULL COMMENT 'Calculated upfront payment amount',
  `upfront_payment_received` tinyint(1) DEFAULT 0 COMMENT 'Whether upfront payment has been received',
  `work_verified_started` tinyint(1) DEFAULT 0 COMMENT 'Whether customer verified work has started',
  `quality_guarantee_end_date` date DEFAULT NULL COMMENT '7 days after completion for quality guarantee',
  `terms_conditions` text DEFAULT NULL,
  `status` enum('draft','sent','active','in_progress','milestone_pending','completed','terminated','disputed') DEFAULT 'draft',
  `sent_to_customer` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `customer_response` enum('pending','accepted','rejected','negotiating') DEFAULT 'pending',
  `customer_response_at` timestamp NULL DEFAULT NULL,
  `locked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `contract_audit_log`
CREATE TABLE `contract_audit_log` (
  `log_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `milestone_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'created, signed, milestone_submitted, approved, disputed, etc.',
  `performed_by` int(11) NOT NULL,
  `user_role` varchar(50) DEFAULT NULL COMMENT 'company, customer, admin',
  `details` text DEFAULT NULL COMMENT 'JSON object with additional information',
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `contract_budget_adjustments`
CREATE TABLE `contract_budget_adjustments` (
  `adjustment_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `requested_by` enum('company','customer') NOT NULL,
  `requester_id` int(11) NOT NULL,
  `requester_name` varchar(255) DEFAULT NULL,
  `adjustment_type` enum('increase','decrease') NOT NULL,
  `original_amount` decimal(10,2) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `adjustment_amount` decimal(10,2) NOT NULL COMMENT 'Difference (positive or negative)',
  `adjustment_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Percentage change',
  `reason` text NOT NULL,
  `justification` text NOT NULL COMMENT 'Detailed justification',
  `supporting_documents` text DEFAULT NULL COMMENT 'JSON array of document paths',
  `justification_documents` text DEFAULT NULL COMMENT 'JSON array of document URLs',
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_by_name` varchar(255) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `requested_at` datetime NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores budget adjustment requests for flexible contracts';

-- Table `contract_change_requests`
CREATE TABLE `contract_change_requests` (
  `change_request_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `requested_by_customer_id` int(11) NOT NULL,
  `request_text` text NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `responded_by_company_id` int(11) DEFAULT NULL,
  `response_note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `responded_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `contract_chats`
CREATE TABLE `contract_chats` (
  `chat_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `sender_type` enum('company','customer') NOT NULL,
  `message_type` enum('text','system') NOT NULL DEFAULT 'text',
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `attachment_type` enum('text','image','document','pdf') DEFAULT 'text',
  `attachment_url` varchar(500) DEFAULT NULL,
  `attachment_filename` varchar(255) DEFAULT NULL,
  `attachment_size` int(11) DEFAULT NULL COMMENT 'File size in bytes',
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores chat messages between company and customer for contracts';

-- Table `contract_dispute`
CREATE TABLE `contract_dispute` (
  `dispute_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `milestone_id` int(11) DEFAULT NULL,
  `raised_by` int(11) NOT NULL,
  `raised_by_role` enum('company','customer') NOT NULL,
  `reason` text NOT NULL,
  `evidence` text DEFAULT NULL COMMENT 'JSON array of evidence files',
  `status` enum('open','under_review','resolved','closed') DEFAULT 'open',
  `priority` enum('low','medium','high','critical') DEFAULT 'medium',
  `resolution` text DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL COMMENT 'Admin user who resolved',
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `contract_document`
CREATE TABLE `contract_document` (
  `document_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `milestone_id` int(11) DEFAULT NULL,
  `document_type` enum('contract_pdf','sow','invoice','receipt','proof_of_work','other') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) DEFAULT NULL COMMENT 'Size in bytes',
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_by_role` enum('company','customer','admin') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `contract_draft`
CREATE TABLE `contract_draft` (
  `draft_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `quotation_id` int(11) DEFAULT NULL,
  `form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`form_data`)),
  `current_step` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `contract_invoices`
CREATE TABLE `contract_invoices` (
  `invoice_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `invoice_type` enum('milestone','upfront','final','time_material','adjustment') NOT NULL,
  `milestone_id` int(11) DEFAULT NULL COMMENT 'Foreign key to milestone if applicable',
  `amount` decimal(10,2) NOT NULL COMMENT 'Base amount before tax',
  `tax_percentage` decimal(5,2) DEFAULT 0.00 COMMENT 'Tax percentage (e.g., 18.00)',
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL COMMENT 'amount + tax',
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `payment_status` enum('pending','paid','overdue','cancelled','refunded') DEFAULT 'pending',
  `paid_date` datetime DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'bank_transfer, card, cash, etc.',
  `payment_reference` varchar(100) DEFAULT NULL COMMENT 'Transaction reference number',
  `payment_receipt_url` varchar(500) DEFAULT NULL,
  `pdf_path` varchar(500) DEFAULT NULL COMMENT 'Path to generated invoice PDF',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores invoices for contract payments';

-- Table `contract_milestone`
CREATE TABLE `contract_milestone` (
  `milestone_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `milestone_number` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','in_progress','submitted','under_review','approved','rejected','disputed','paid','completed') DEFAULT 'pending',
  `escrow_held` decimal(12,2) DEFAULT 0.00,
  `payment_released` decimal(12,2) DEFAULT 0.00,
  `payment_released_at` datetime DEFAULT NULL,
  `proof_of_work` text DEFAULT NULL COMMENT 'JSON array of uploaded files',
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_comments` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `proof_files` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `unit_label` varchar(50) DEFAULT NULL COMMENT 'e.g. hours, sqft, units',
  `unit_rate` decimal(12,2) DEFAULT NULL COMMENT 'Price per unit',
  `estimated_quantity` decimal(10,2) DEFAULT NULL COMMENT 'Quantity from quotation',
  `actual_quantity` decimal(10,2) DEFAULT NULL COMMENT 'Actual units submitted',
  `actual_amount` decimal(12,2) DEFAULT NULL COMMENT 'actual_quantity * unit_rate'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `contract_notifications`
CREATE TABLE `contract_notifications` (
  `notification_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `recipient_type` enum('company','customer') NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `notification_type` varchar(50) NOT NULL COMMENT 'undo_reminder, milestone_approved, payment_received, chat_message, etc.',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `action_url` varchar(500) DEFAULT NULL COMMENT 'URL to navigate when notification clicked',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores notifications for contract events';

-- Table `contract_payment_history`
CREATE TABLE `contract_payment_history` (
  `payment_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `milestone_id` int(11) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_type` enum('escrow_deposit','milestone_release','refund','penalty','bonus') NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed','refunded') DEFAULT 'pending',
  `paid_by` int(11) DEFAULT NULL COMMENT 'Customer',
  `paid_to` int(11) DEFAULT NULL COMMENT 'Company',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `contract_timeline`
CREATE TABLE `contract_timeline` (
  `timeline_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `event_type` varchar(50) NOT NULL COMMENT 'created, sent, accepted, milestone_submitted, milestone_approved, payment_received, work_started, work_completed, disputed, etc.',
  `event_title` varchar(255) NOT NULL,
  `event_description` text DEFAULT NULL,
  `actor_type` enum('system','company','customer') NOT NULL,
  `actor_id` int(11) DEFAULT NULL COMMENT 'User ID of actor (NULL for system events)',
  `actor_name` varchar(255) DEFAULT NULL COMMENT 'Name of actor for display',
  `metadata_json` text DEFAULT NULL COMMENT 'Additional event data in JSON format',
  `is_milestone` tinyint(1) DEFAULT 0 COMMENT 'Whether event is a milestone event',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores timeline of all events for each contract';

-- Table `contract_time_logs`
CREATE TABLE `contract_time_logs` (
  `log_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_duration` decimal(3,2) DEFAULT 0.00 COMMENT 'Break hours (e.g., 1.00)',
  `total_hours` decimal(4,2) NOT NULL COMMENT 'Net working hours',
  `hourly_rate` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL COMMENT 'hours ?? rate',
  `work_description` text NOT NULL,
  `work_location` varchar(255) DEFAULT NULL,
  `submitted_by` int(11) NOT NULL COMMENT 'Company user ID who submitted',
  `submitted_at` datetime NOT NULL,
  `customer_approved` tinyint(1) DEFAULT 0,
  `customer_approval_date` datetime DEFAULT NULL,
  `customer_rejection_reason` text DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL COMMENT 'Foreign key to contract_invoices',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores time logs for Time & Material payment contracts';

-- Table `directjobrequest`
CREATE TABLE `directjobrequest` (
  `request_id` int(11) NOT NULL,
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
  `photos` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `directrequestquotes`
CREATE TABLE `directrequestquotes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `provider_type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `escrow_accounts`
CREATE TABLE `escrow_accounts` (
  `escrow_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL COMMENT 'Total contract value',
  `held_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Currently held in escrow',
  `released_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Released to company',
  `refunded_amount` decimal(10,2) DEFAULT 0.00 COMMENT 'Refunded to customer',
  `status` enum('active','completed','refunded','disputed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores escrow account information for secure payments';

-- Table `escrow_transaction`
CREATE TABLE `escrow_transaction` (
  `transaction_id` int(11) NOT NULL,
  `wallet_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` enum('deposit','release','hold','refund','service_fee') NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'completed',
  `related_contract_id` int(11) DEFAULT NULL,
  `related_milestone_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `escrow_wallet`
CREATE TABLE `escrow_wallet` (
  `wallet_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `feedback`
CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `given_by` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comments` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `financialreport`
CREATE TABLE `financialreport` (
  `report_id` int(11) NOT NULL,
  `month` date NOT NULL,
  `active_subscriptions` int(11) DEFAULT 0,
  `generated_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `financialreporttransaction`
CREATE TABLE `financialreporttransaction` (
  `transaction_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `transaction_type` enum('Payment','Commission','Withdrawal','Subscription','Advertisement','Refund') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `reference_number` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `status` enum('Completed','Pending','Failed','Cancelled') DEFAULT 'Completed',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `freelancer_assignments`
CREATE TABLE `freelancer_assignments` (
  `assignment_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `contract_id` int(11) DEFAULT NULL,
  `pricing_model` enum('hourly','fixed') NOT NULL DEFAULT 'hourly',
  `rate_or_price` decimal(10,2) NOT NULL,
  `estimated_hours` decimal(5,2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `deadline_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('offered','accepted','declined','in_progress','completed','cancelled') DEFAULT 'offered',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `issuenotification`
CREATE TABLE `issuenotification` (
  `notification_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `recipient_type` enum('reporter','target') NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status_change` varchar(100) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `issuereport`
CREATE TABLE `issuereport` (
  `issue_id` int(11) NOT NULL,
  `reportedBy_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `target_type` enum('user','repairer','company') NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('pending','investigating','resolved','escalated','closed') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_internal_notes` text DEFAULT NULL COMMENT 'Internal admin notes - not visible to users'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `issuestatushistory`
CREATE TABLE `issuestatushistory` (
  `history_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `old_status` enum('pending','investigating','resolved','escalated','closed') NOT NULL,
  `new_status` enum('pending','investigating','resolved','escalated','closed') NOT NULL,
  `changed_by` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `job`
CREATE TABLE `job` (
  `job_id` int(11) NOT NULL,
  `job_request_id` int(11) NOT NULL,
  `fixer_id` int(11) NOT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `completionDate` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `jobrequest`
CREATE TABLE `jobrequest` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','accepted','in_progress','completed','cancelled') DEFAULT 'pending',
  `location_id` int(11) DEFAULT NULL,
  `district` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `service_provider_type` varchar(50) NOT NULL,
  `urgency` enum('medium','urgent') DEFAULT 'medium',
  `finish_date` date NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `photos` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `job_collaboration`
CREATE TABLE `job_collaboration` (
  `collaboration_id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `job_collaboration_event`
CREATE TABLE `job_collaboration_event` (
  `event_id` int(11) NOT NULL,
  `collaboration_id` int(11) NOT NULL,
  `actor_id` int(11) NOT NULL,
  `actor_role` enum('user','repairer','company') NOT NULL,
  `event_type` enum('note','price_proposed','price_accepted','price_rejected','completed_marked','payment_confirmed','rating_submitted','phase_changed','system') NOT NULL,
  `message` text DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `meta_json` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `job_collaboration_rating`
CREATE TABLE `job_collaboration_rating` (
  `rating_id` int(11) NOT NULL,
  `collaboration_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewer_role` enum('user','repairer','company') NOT NULL,
  `target_id` int(11) NOT NULL,
  `target_role` enum('user','repairer','company') NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `location_id` int(11) NOT NULL,
  `address` text DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `milestone`
CREATE TABLE `milestone` (
  `milestone_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `agreements` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed','overdue') DEFAULT 'pending',
  `submitted_by_company` tinyint(1) DEFAULT 0 COMMENT 'Whether company submitted milestone plan',
  `submitted_date` datetime DEFAULT NULL COMMENT 'When company submitted milestone',
  `customer_approved` tinyint(1) DEFAULT 0 COMMENT 'Whether customer approved milestone',
  `customer_approval_date` datetime DEFAULT NULL COMMENT 'When customer approved milestone',
  `customer_rejection_reason` text DEFAULT NULL COMMENT 'Reason if customer rejected milestone',
  `work_started` tinyint(1) DEFAULT 0 COMMENT 'Whether work on milestone has started',
  `work_start_date` datetime DEFAULT NULL COMMENT 'When work started on milestone',
  `work_completed` tinyint(1) DEFAULT 0 COMMENT 'Whether work on milestone is completed',
  `work_completion_date` datetime DEFAULT NULL COMMENT 'When work was completed on milestone',
  `customer_verification_requested` tinyint(1) DEFAULT 0 COMMENT 'Whether company requested customer verification',
  `customer_verified` tinyint(1) DEFAULT 0 COMMENT 'Whether customer verified completed work',
  `customer_verification_date` datetime DEFAULT NULL COMMENT 'When customer verified work',
  `is_active` tinyint(1) DEFAULT 0,
  `deliverables` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `approval_status` enum('pending','submitted','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `milestonepayment`
CREATE TABLE `milestonepayment` (
  `payment_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `milestone_id` int(11) DEFAULT NULL,
  `payment_type` enum('upfront','milestone','final','weekly','material') NOT NULL DEFAULT 'milestone',
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `method` enum('credit_card','debit_card','bank_transfer','check','cash') NOT NULL,
  `status` enum('pending','paid','held_escrow','released','refunded','completed','cancelled') DEFAULT 'pending',
  `payment_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Percentage of total contract value',
  `description` text DEFAULT NULL,
  `escrow_enabled` tinyint(1) DEFAULT 0,
  `paid_to_escrow_at` datetime DEFAULT NULL,
  `escrow_released_at` datetime DEFAULT NULL,
  `escrow_refunded_at` datetime DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `paid_by` int(11) DEFAULT NULL COMMENT 'User ID of payer',
  `paid_to` int(11) DEFAULT NULL COMMENT 'User ID of payee',
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `moderator`
CREATE TABLE `moderator` (
  `moderator_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `assigned_section` varchar(100) DEFAULT NULL,
  `status` enum('active','suspended') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `moderatormsg`
CREATE TABLE `moderatormsg` (
  `msg_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  `date_sent` timestamp NOT NULL DEFAULT current_timestamp(),
  `response` text DEFAULT NULL,
  `date_resolved` timestamp NULL DEFAULT NULL,
  `status` enum('open','pending','resolved','closed') DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `moderator_activity`
CREATE TABLE `moderator_activity` (
  `activity_id` int(11) NOT NULL,
  `moderator_id` int(11) NOT NULL DEFAULT 1,
  `activity_type` enum('ad_approved','ad_rejected','ad_activated','user_banned','content_updated','payment_verified','user_registered','report_resolved') NOT NULL,
  `target_id` int(11) NOT NULL,
  `target_title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `moderator_management_log`
CREATE TABLE `moderator_management_log` (
  `log_id` int(11) NOT NULL,
  `admin_username` varchar(100) NOT NULL,
  `moderator_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `notification`
CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `recipient_type` enum('all','user','repairer','company') NOT NULL DEFAULT 'all',
  `status` enum('sent','pending','failed') DEFAULT 'sent',
  `send_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by_id` int(11) DEFAULT NULL,
  `created_by_role` enum('admin','moderator','company','repairer','user') DEFAULT NULL,
  `created_by_name` varchar(255) DEFAULT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `payment`
CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `job_request_id` int(11) NOT NULL,
  `paymentType` enum('credit_card','debit_card','cash','bank_transfer') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paymentDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `payment_methods`
CREATE TABLE `payment_methods` (
  `payment_method_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `card_type` enum('visa','mastercard','amex','discover') NOT NULL,
  `last_four_digits` char(4) NOT NULL,
  `card_holder_name` varchar(100) NOT NULL,
  `expiry_month` char(2) NOT NULL,
  `expiry_year` char(4) NOT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `placement_limits`
CREATE TABLE `placement_limits` (
  `placement_type` varchar(50) NOT NULL,
  `max_slots_per_day` int(11) NOT NULL DEFAULT 5,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `project`
CREATE TABLE `project` (
  `project_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `project_type` varchar(100) DEFAULT NULL,
  `location` text NOT NULL,
  `budget` decimal(12,2) DEFAULT NULL,
  `final_cost` decimal(12,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `attachment` varchar(500) DEFAULT NULL,
  `status` enum('planned','in_progress','completed','cancelled','on_hold') DEFAULT 'planned',
  `progress` int(11) DEFAULT 0 CHECK (`progress` >= 0 and `progress` <= 100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `promotion`
CREATE TABLE `promotion` (
  `promotion_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `quote_negotiation`
CREATE TABLE `quote_negotiation` (
  `negotiation_id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `repairer`
CREATE TABLE `repairer` (
  `repairer_id` int(11) NOT NULL,
  `f_name` varchar(100) NOT NULL,
  `l_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `profilePicture` varchar(500) DEFAULT NULL,
  `ratings` decimal(3,2) DEFAULT 0.00,
  `completedJobsCount` int(11) DEFAULT 0,
  `districts` text DEFAULT NULL,
  `availability` enum('available','busy','unavailable') DEFAULT 'available',
  `dateJoined` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `account_status` enum('ACTIVE','SUSPENDED','BANNED') DEFAULT 'ACTIVE',
  `banned_permanent` tinyint(1) DEFAULT 0,
  `suspended_until` datetime DEFAULT NULL,
  `moderation_reason` text DEFAULT NULL,
  `skills` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `repairerapplication`
CREATE TABLE `repairerapplication` (
  `app_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `posting_id` int(11) NOT NULL,
  `date_applied` timestamp NOT NULL DEFAULT current_timestamp(),
  `app_status` enum('pending','reviewed','accepted','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `repairerassignment`
CREATE TABLE `repairerassignment` (
  `assignment_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `assigned_date` date DEFAULT curdate(),
  `status` enum('assigned','active','completed','removed') DEFAULT 'assigned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `repairerquote`
CREATE TABLE `repairerquote` (
  `quote_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `quoteAmount` decimal(10,2) NOT NULL,
  `estimatedDays` int(11) NOT NULL DEFAULT 1,
  `warrantyPeriod` int(11) DEFAULT 0,
  `validUntil` date DEFAULT NULL,
  `materialsIncluded` tinyint(1) DEFAULT 1,
  `message` text DEFAULT NULL,
  `status` enum('pending','accepted','completed','rejected','expired') DEFAULT 'pending',
  `dateSubmitted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `repairer_applications`
CREATE TABLE `repairer_applications` (
  `application_id` int(11) NOT NULL,
  `job_posting_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `expected_rate` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','reviewed','interview','approved','rejected') DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `applied_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `report`
CREATE TABLE `report` (
  `report_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','investigating','resolved') DEFAULT 'pending',
  `resolve_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `review`
CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `service_provider_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comments` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `service_area`
CREATE TABLE `service_area` (
  `area_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `owner_type` enum('company','repairer') NOT NULL,
  `district` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `staffsummary`
CREATE TABLE `staffsummary` (
  `staff_summary_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `specialty` varchar(100) NOT NULL,
  `total_count` int(11) DEFAULT 0,
  `active_count` int(11) DEFAULT 0,
  `inactive_count` int(11) DEFAULT 0,
  `avg_rating` decimal(3,2) DEFAULT 0.00,
  `avg_hourly_rate` decimal(10,2) DEFAULT 0.00,
  `min_hourly_rate` decimal(10,2) DEFAULT 0.00,
  `max_hourly_rate` decimal(10,2) DEFAULT 0.00,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `staticcontent`
CREATE TABLE `staticcontent` (
  `content_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `body` text NOT NULL,
  `status` enum('Draft','Published') DEFAULT 'Published',
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `content_type` enum('terms','privacy','faq','about','help','contact','how_it_works','services','why_choose','support','landing_hero') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `supportticket`
CREATE TABLE `supportticket` (
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('user','repairer','company') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` enum('payment','technical','account','feature','billing','other') NOT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') DEFAULT 'open',
  `urgency` enum('can-wait','soon','asap') DEFAULT 'soon',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `project_id` int(11) DEFAULT NULL,
  `attachment` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `support_attachments`
CREATE TABLE `support_attachments` (
  `attachment_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL COMMENT 'Size in bytes',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `support_categories`
CREATE TABLE `support_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `support_responses`
CREATE TABLE `support_responses` (
  `response_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `responder_type` enum('user','admin','moderator','system') NOT NULL,
  `responder_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_internal_note` tinyint(1) DEFAULT 0 COMMENT 'Notes only visible to admins',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `support_tickets`
CREATE TABLE `support_tickets` (
  `ticket_id` int(11) NOT NULL,
  `ticket_number` varchar(50) NOT NULL,
  `user_type` enum('user','company','repairer') NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `category` enum('payment','technical','account','feature','billing','other') NOT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('open','in-progress','pending','resolved','closed') NOT NULL DEFAULT 'open',
  `description` text NOT NULL,
  `steps_to_reproduce` text DEFAULT NULL,
  `urgency` enum('can-wait','soon','asap') DEFAULT 'soon',
  `affected_users` varchar(255) DEFAULT NULL COMMENT 'Comma-separated affected groups',
  `related_project_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL COMMENT 'Admin/Moderator ID',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `system_activity_logs`
CREATE TABLE `system_activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `user_role` enum('admin','moderator','system') NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `system_audit_log`
CREATE TABLE `system_audit_log` (
  `audit_id` bigint(20) NOT NULL,
  `request_id` char(36) DEFAULT NULL,
  `occurred_at` datetime NOT NULL DEFAULT current_timestamp(),
  `actor_user_id` int(11) DEFAULT NULL,
  `actor_role` varchar(50) DEFAULT NULL,
  `action` varchar(150) NOT NULL,
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` varchar(64) DEFAULT NULL,
  `http_method` varchar(10) DEFAULT NULL,
  `endpoint` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `status_code` int(11) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `ticketmessage`
CREATE TABLE `ticketmessage` (
  `message_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('user','repairer','company','admin','moderator') NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `user`
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `f_name` varchar(100) NOT NULL,
  `l_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `profilePicture` varchar(500) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `account_status` enum('ACTIVE','SUSPENDED','BANNED') DEFAULT 'ACTIVE',
  `banned_permanent` tinyint(1) DEFAULT 0,
  `suspended_until` datetime DEFAULT NULL,
  `moderation_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `user_sessions`
CREATE TABLE `user_sessions` (
  `session_id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_role` enum('user','repairer','company','admin','moderator') NOT NULL,
  `device_type` varchar(100) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_current` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table `action_undo`
CREATE TABLE `action_undo` (
  `undo_id` int(11) NOT NULL,
  `entity_type` varchar(32) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `action_key` varchar(64) NOT NULL,
  `meta_json` longtext DEFAULT NULL,
  `undo_until` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `used_at` datetime DEFAULT NULL,
  `used_by` int(11) DEFAULT NULL,
  `used_role` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `project_staff_requirements`
CREATE TABLE `project_staff_requirements` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `specialty` varchar(100) NOT NULL,
  `required_count` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table `st`
CREATE TABLE `st` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `Age` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Post-creates: indexes, auto-increment, and constraints
ALTER TABLE `account_moderation_cases`
  ADD PRIMARY KEY (`case_id`),
  ADD KEY `admin_username` (`admin_username`),
  ADD KEY `idx_target` (`target_id`,`target_type`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_status` (`status_after`);
ALTER TABLE `account_moderation_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_account_lookup` (`account_type`,`account_id`,`created_at`),
  ADD KEY `idx_action_type` (`action_type`);
ALTER TABLE `account_moderation_status`
  ADD PRIMARY KEY (`status_id`),
  ADD UNIQUE KEY `unique_account` (`account_id`,`account_type`),
  ADD KEY `idx_status` (`account_status`),
  ADD KEY `idx_suspended_until` (`suspended_until`),
  ADD KEY `idx_account` (`account_id`,`account_type`);
ALTER TABLE `activitylog`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_timestamp` (`timestamp`);
ALTER TABLE `admin`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `email` (`email`);
ALTER TABLE `adminalert`
  ADD PRIMARY KEY (`alert_id`),
  ADD KEY `idx_target_role` (`target_role`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `created_by` (`created_by`);
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_admin` (`admin_username`),
  ADD KEY `idx_read` (`is_read`),
  ADD KEY `idx_created` (`created_at`);
ALTER TABLE `admin_override_history`
  ADD PRIMARY KEY (`override_id`),
  ADD KEY `idx_ad_overrides` (`ad_id`),
  ADD KEY `idx_admin_actions` (`admin_username`),
  ADD KEY `idx_timestamp` (`override_timestamp`);
ALTER TABLE `adreport`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_ad` (`ad_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`);
ALTER TABLE `adrotationsettings`
  ADD PRIMARY KEY (`setting_id`);
ALTER TABLE `adschedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `idx_ad` (`ad_id`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);
ALTER TABLE `advertisement`
  ADD PRIMARY KEY (`ad_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_provider` (`provider_type`,`provider_id`),
  ADD KEY `idx_submission` (`submission_date`),
  ADD KEY `fk_ad_category` (`category_id`),
  ADD KEY `fk_ad_admin_reviewer` (`admin_reviewed_by`);
ALTER TABLE `ad_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `resolved_by` (`resolved_by`),
  ADD KEY `idx_ad` (`ad_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_reporter` (`reporter_id`,`reporter_type`),
  ADD KEY `idx_assigned` (`assigned_to`),
  ADD KEY `idx_created` (`created_at`);
ALTER TABLE `ad_schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `ad_id` (`ad_id`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);
ALTER TABLE `ad_status_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_ad_id` (`ad_id`),
  ADD KEY `idx_date` (`created_at`);
ALTER TABLE `billinghistory`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `idx_company_date` (`company_id`,`date`);
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);
ALTER TABLE `chatmessage`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_repairer` (`repairer_id`);
ALTER TABLE `company`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `registration_no` (`registration_no`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `fk_company_location` (`location_id`);
ALTER TABLE `companyemployee`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `repairer_id` (`repairer_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_specialty` (`specialty`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`);
ALTER TABLE `companyjobpost`
  ADD PRIMARY KEY (`posting_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_posted_date` (`posted_date`),
  ADD KEY `idx_deadline` (`application_deadline`);
ALTER TABLE `companyquotation`
  ADD PRIMARY KEY (`quotation_id`),
  ADD KEY `idx_request` (`request_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_budget_type` (`budget_type`),
  ADD KEY `idx_payment_method` (`payment_method`),
  ADD KEY `idx_pricing_type` (`pricing_type`),
  ADD KEY `idx_company_quote` (`company_id`),
  ADD KEY `idx_schedule_type` (`work_schedule_type`),
  ADD KEY `idx_work_schedule` (`work_schedule_type`,`working_days_per_week`);
ALTER TABLE `companysettings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `unique_company` (`company_id`);
ALTER TABLE `company_employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `unique_company_repairer` (`company_id`,`repairer_id`),
  ADD KEY `repairer_id` (`repairer_id`);
ALTER TABLE `company_jobpost`
  ADD PRIMARY KEY (`posting_id`),
  ADD KEY `company_id` (`company_id`);
ALTER TABLE `company_subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_status` (`status`);
ALTER TABLE `contract`
  ADD PRIMARY KEY (`contract_id`),
  ADD UNIQUE KEY `contract_number` (`contract_number`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_quotation_phase2` (`quotation_id`),
  ADD KEY `idx_company_phase2` (`company_id`),
  ADD KEY `idx_customer_phase2` (`customer_id`),
  ADD KEY `idx_job_request_phase2` (`job_request_id`),
  ADD KEY `idx_payment_status_phase2` (`payment_status`),
  ADD KEY `idx_undo_deadline` (`undo_deadline`),
  ADD KEY `idx_chat_active` (`chat_active`),
  ADD KEY `idx_payment_method` (`payment_method`),
  ADD KEY `idx_budget_type` (`budget_type`),
  ADD KEY `fk_contract_escrow` (`escrow_account_id`);
ALTER TABLE `contract_audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `performed_by` (`performed_by`),
  ADD KEY `idx_contract_id` (`contract_id`),
  ADD KEY `idx_milestone_id` (`milestone_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);
ALTER TABLE `contract_budget_adjustments`
  ADD PRIMARY KEY (`adjustment_id`),
  ADD KEY `idx_contract_status` (`contract_id`,`status`),
  ADD KEY `idx_requester` (`requested_by`,`requester_id`),
  ADD KEY `idx_status` (`status`);
ALTER TABLE `contract_change_requests`
  ADD PRIMARY KEY (`change_request_id`),
  ADD KEY `idx_contract_status` (`contract_id`,`status`),
  ADD KEY `idx_created_at` (`created_at`);
ALTER TABLE `contract_chats`
  ADD PRIMARY KEY (`chat_id`),
  ADD KEY `idx_contract_sender` (`contract_id`,`sender_type`),
  ADD KEY `idx_unread` (`is_read`,`created_at`),
  ADD KEY `idx_created` (`created_at`);
ALTER TABLE `contract_dispute`
  ADD PRIMARY KEY (`dispute_id`),
  ADD KEY `raised_by` (`raised_by`),
  ADD KEY `resolved_by` (`resolved_by`),
  ADD KEY `idx_contract_id` (`contract_id`),
  ADD KEY `idx_milestone_id` (`milestone_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);
ALTER TABLE `contract_document`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_contract_id` (`contract_id`),
  ADD KEY `idx_milestone_id` (`milestone_id`),
  ADD KEY `idx_document_type` (`document_type`);
ALTER TABLE `contract_draft`
  ADD PRIMARY KEY (`draft_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_quotation` (`quotation_id`);
ALTER TABLE `contract_invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `milestone_id` (`milestone_id`),
  ADD KEY `idx_contract_status` (`contract_id`,`payment_status`),
  ADD KEY `idx_invoice_number` (`invoice_number`),
  ADD KEY `idx_due_date` (`due_date`,`payment_status`),
  ADD KEY `idx_type` (`invoice_type`);
ALTER TABLE `contract_milestone`
  ADD PRIMARY KEY (`milestone_id`),
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `idx_contract_id` (`contract_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`);
ALTER TABLE `contract_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `contract_id` (`contract_id`),
  ADD KEY `idx_recipient` (`recipient_type`,`recipient_id`,`is_read`),
  ADD KEY `idx_type` (`notification_type`),
  ADD KEY `idx_priority` (`priority`,`is_read`),
  ADD KEY `idx_created` (`created_at`);
ALTER TABLE `contract_payment_history`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `paid_by` (`paid_by`),
  ADD KEY `paid_to` (`paid_to`),
  ADD KEY `idx_contract_id` (`contract_id`),
  ADD KEY `idx_milestone_id` (`milestone_id`),
  ADD KEY `idx_payment_type` (`payment_type`),
  ADD KEY `idx_status` (`status`);
ALTER TABLE `contract_timeline`
  ADD PRIMARY KEY (`timeline_id`),
  ADD KEY `idx_contract_time` (`contract_id`,`created_at`),
  ADD KEY `idx_event_type` (`event_type`),
  ADD KEY `idx_actor` (`actor_type`,`actor_id`);
ALTER TABLE `contract_time_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_contract_date` (`contract_id`,`work_date`),
  ADD KEY `idx_approval` (`customer_approved`),
  ADD KEY `idx_invoice` (`invoice_id`);
ALTER TABLE `directjobrequest`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_djr_user` (`user_id`),
  ADD KEY `idx_djr_category` (`category_id`),
  ADD KEY `idx_djr_provider` (`provider_id`,`provider_type`),
  ADD KEY `idx_djr_status` (`status`),
  ADD KEY `idx_djr_created` (`date_created`);
ALTER TABLE `directrequestquotes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drq_user` (`user_id`),
  ADD KEY `idx_drq_request` (`request_id`),
  ADD KEY `idx_drq_provider` (`provider_id`,`provider_type`);
ALTER TABLE `escrow_accounts`
  ADD PRIMARY KEY (`escrow_id`),
  ADD UNIQUE KEY `contract_id` (`contract_id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_account_number` (`account_number`);
ALTER TABLE `escrow_transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `idx_wallet` (`wallet_id`),
  ADD KEY `idx_contract` (`related_contract_id`);
ALTER TABLE `escrow_wallet`
  ADD PRIMARY KEY (`wallet_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `company_id` (`company_id`);
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `given_by` (`given_by`),
  ADD KEY `idx_project` (`project_id`);
ALTER TABLE `financialreport`
  ADD PRIMARY KEY (`report_id`),
  ADD UNIQUE KEY `month` (`month`),
  ADD KEY `idx_month` (`month`),
  ADD KEY `generated_by` (`generated_by`);
ALTER TABLE `financialreporttransaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `idx_report` (`report_id`),
  ADD KEY `idx_type` (`transaction_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_date` (`transaction_date`);
ALTER TABLE `freelancer_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `repairer_id` (`repairer_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `contract_id` (`contract_id`);
ALTER TABLE `issuenotification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_issue` (`issue_id`),
  ADD KEY `idx_recipient` (`recipient_type`,`recipient_id`),
  ADD KEY `idx_read` (`is_read`);
ALTER TABLE `issuereport`
  ADD PRIMARY KEY (`issue_id`),
  ADD KEY `idx_reporter` (`reportedBy_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`);
ALTER TABLE `issuestatushistory`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_issue` (`issue_id`),
  ADD KEY `idx_date` (`changed_at`);
ALTER TABLE `job`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `job_request_id` (`job_request_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_fixer` (`fixer_id`);
ALTER TABLE `jobrequest`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`dateCreated`),
  ADD KEY `idx_district` (`district`),
  ADD KEY `fk_jobrequest_location` (`location_id`);
ALTER TABLE `job_collaboration`
  ADD PRIMARY KEY (`collaboration_id`),
  ADD UNIQUE KEY `uniq_request_type` (`request_id`,`request_type`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_provider` (`provider_id`,`provider_role`),
  ADD KEY `idx_phase` (`current_phase`),
  ADD KEY `idx_quote_lookup` (`quote_id`,`quote_source`);
ALTER TABLE `job_collaboration_event`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `idx_collab_created` (`collaboration_id`,`created_at`),
  ADD KEY `idx_actor` (`actor_id`,`actor_role`);
ALTER TABLE `job_collaboration_rating`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `uniq_collab_reviewer` (`collaboration_id`,`reviewer_id`,`reviewer_role`),
  ADD KEY `idx_target` (`target_id`,`target_role`);
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`);
ALTER TABLE `milestone`
  ADD PRIMARY KEY (`milestone_id`),
  ADD KEY `idx_contract` (`contract_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_workflow` (`customer_approved`,`work_started`,`work_completed`,`customer_verified`);
ALTER TABLE `milestonepayment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_milestone` (`milestone_id`),
  ADD KEY `idx_status` (`status`);
ALTER TABLE `moderator`
  ADD PRIMARY KEY (`moderator_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_moderator_status` (`status`),
  ADD KEY `idx_moderator_deleted` (`deleted_at`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_username` (`username`);
ALTER TABLE `moderatormsg`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `idx_repairer` (`repairer_id`),
  ADD KEY `idx_status` (`status`);
ALTER TABLE `moderator_activity`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `idx_moderator` (`moderator_id`),
  ADD KEY `idx_type` (`activity_type`),
  ADD KEY `idx_created` (`created_at`);
ALTER TABLE `moderator_management_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_admin` (`admin_username`),
  ADD KEY `idx_moderator` (`moderator_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_date` (`created_at`);
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_recipient` (`recipient_type`),
  ADD KEY `idx_send_date` (`send_date`);
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_job_request` (`job_request_id`),
  ADD KEY `idx_status` (`status`);
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`payment_method_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_primary` (`is_primary`),
  ADD KEY `idx_active` (`is_active`);
ALTER TABLE `placement_limits`
  ADD PRIMARY KEY (`placement_type`);
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_status` (`status`);
ALTER TABLE `promotion`
  ADD PRIMARY KEY (`promotion_id`),
  ADD KEY `idx_repairer` (`repairer_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);
ALTER TABLE `quote_negotiation`
  ADD PRIMARY KEY (`negotiation_id`),
  ADD KEY `idx_quote` (`quote_id`,`quote_source`),
  ADD KEY `idx_request` (`request_id`,`request_type`),
  ADD KEY `idx_sender` (`sender_id`,`sender_role`),
  ADD KEY `idx_receiver` (`receiver_id`,`receiver_role`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);
ALTER TABLE `repairer`
  ADD PRIMARY KEY (`repairer_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_ratings` (`ratings`),
  ADD KEY `idx_account_status` (`account_status`),
  ADD KEY `idx_suspended_until` (`suspended_until`);
ALTER TABLE `repairerapplication`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `idx_repairer` (`repairer_id`),
  ADD KEY `idx_posting` (`posting_id`),
  ADD KEY `idx_status` (`app_status`);
ALTER TABLE `repairerassignment`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_repairer` (`repairer_id`);
ALTER TABLE `repairerquote`
  ADD PRIMARY KEY (`quote_id`),
  ADD KEY `idx_request` (`request_id`),
  ADD KEY `idx_repairer` (`repairer_id`),
  ADD KEY `idx_status` (`status`);
ALTER TABLE `repairer_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `job_posting_id` (`job_posting_id`),
  ADD KEY `repairer_id` (`repairer_id`);
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_status` (`status`);
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `idx_job` (`job_id`),
  ADD KEY `idx_provider` (`service_provider_id`);
ALTER TABLE `service_area`
  ADD PRIMARY KEY (`area_id`),
  ADD KEY `idx_owner` (`owner_id`,`owner_type`);
ALTER TABLE `staffsummary`
  ADD PRIMARY KEY (`staff_summary_id`),
  ADD UNIQUE KEY `unique_company_specialty` (`company_id`,`specialty`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_specialty` (`specialty`);
ALTER TABLE `staticcontent`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `idx_type` (`content_type`),
  ADD KEY `idx_status` (`status`);
ALTER TABLE `supportticket`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `idx_user` (`user_id`,`user_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_project` (`project_id`);
ALTER TABLE `support_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `idx_ticket` (`ticket_id`);
ALTER TABLE `support_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_slug` (`category_slug`);
ALTER TABLE `support_responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `idx_ticket` (`ticket_id`),
  ADD KEY `idx_created` (`created_at`);
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `idx_user` (`user_type`,`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_created` (`created_at`);
ALTER TABLE `system_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_type` (`activity_type`);
ALTER TABLE `system_audit_log`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `idx_audit_actor_time` (`actor_user_id`,`occurred_at`),
  ADD KEY `idx_audit_action_time` (`action`,`occurred_at`),
  ADD KEY `idx_audit_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_audit_request` (`request_id`);
ALTER TABLE `ticketmessage`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_ticket` (`ticket_id`);
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_user` (`user_id`,`user_role`),
  ADD KEY `idx_last_activity` (`last_activity`);
ALTER TABLE `account_moderation_cases`
  MODIFY `case_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `account_moderation_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `account_moderation_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `activitylog`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `adminalert`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
ALTER TABLE `admin_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `admin_override_history`
  MODIFY `override_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `adreport`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `adrotationsettings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `adschedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `advertisement`
  MODIFY `ad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
ALTER TABLE `ad_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `ad_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `ad_status_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
ALTER TABLE `chatmessage`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000;
ALTER TABLE `companyemployee`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `companyjobpost`
  MODIFY `posting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `companyquotation`
  MODIFY `quotation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
ALTER TABLE `companysettings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `company_employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `company_jobpost`
  MODIFY `posting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `company_subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `contract`
  MODIFY `contract_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
ALTER TABLE `contract_audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `contract_budget_adjustments`
  MODIFY `adjustment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
ALTER TABLE `contract_change_requests`
  MODIFY `change_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `contract_chats`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
ALTER TABLE `contract_dispute`
  MODIFY `dispute_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `contract_document`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `contract_draft`
  MODIFY `draft_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `contract_invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `contract_milestone`
  MODIFY `milestone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
ALTER TABLE `contract_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
ALTER TABLE `contract_payment_history`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `contract_timeline`
  MODIFY `timeline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
ALTER TABLE `contract_time_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `directjobrequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `directrequestquotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `escrow_accounts`
  MODIFY `escrow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `escrow_transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
ALTER TABLE `escrow_wallet`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `financialreport`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `financialreporttransaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
ALTER TABLE `freelancer_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `issuenotification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `issuereport`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
ALTER TABLE `issuestatushistory`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `job`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `jobrequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
ALTER TABLE `job_collaboration`
  MODIFY `collaboration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `job_collaboration_event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;
ALTER TABLE `job_collaboration_rating`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
ALTER TABLE `milestone`
  MODIFY `milestone_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `milestonepayment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `moderator`
  MODIFY `moderator_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
ALTER TABLE `moderatormsg`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `moderator_activity`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
ALTER TABLE `moderator_management_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `payment_methods`
  MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `project`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
ALTER TABLE `promotion`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `quote_negotiation`
  MODIFY `negotiation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `repairer`
  MODIFY `repairer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;
ALTER TABLE `repairerapplication`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `repairerassignment`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `repairerquote`
  MODIFY `quote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
ALTER TABLE `repairer_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `service_area`
  MODIFY `area_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `staffsummary`
  MODIFY `staff_summary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `staticcontent`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
ALTER TABLE `supportticket`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `support_attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `support_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `support_responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `support_tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `system_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `system_audit_log`
  MODIFY `audit_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1005;
ALTER TABLE `ticketmessage`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9999;
ALTER TABLE `adminalert`
  ADD CONSTRAINT `adminalert_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin` (`username`) ON DELETE CASCADE;
ALTER TABLE `admin_override_history`
  ADD CONSTRAINT `admin_override_history_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_override_history_ibfk_2` FOREIGN KEY (`admin_username`) REFERENCES `admin` (`username`) ON DELETE CASCADE;
ALTER TABLE `adreport`
  ADD CONSTRAINT `adreport_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE;
ALTER TABLE `adschedule`
  ADD CONSTRAINT `adschedule_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE;
ALTER TABLE `advertisement`
  ADD CONSTRAINT `fk_ad_admin_reviewer` FOREIGN KEY (`admin_reviewed_by`) REFERENCES `admin` (`username`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ad_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `ad_reports`
  ADD CONSTRAINT `ad_reports_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ad_reports_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `moderator` (`moderator_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ad_reports_ibfk_3` FOREIGN KEY (`resolved_by`) REFERENCES `moderator` (`moderator_id`) ON DELETE SET NULL;
ALTER TABLE `ad_schedules`
  ADD CONSTRAINT `ad_schedules_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE;
ALTER TABLE `ad_status_history`
  ADD CONSTRAINT `ad_status_history_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE;
ALTER TABLE `billinghistory`
  ADD CONSTRAINT `billinghistory_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;
ALTER TABLE `chatmessage`
  ADD CONSTRAINT `chatmessage_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chatmessage_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;
ALTER TABLE `company`
  ADD CONSTRAINT `fk_company_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL;
ALTER TABLE `companyemployee`
  ADD CONSTRAINT `companyemployee_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `companyemployee_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE SET NULL;
ALTER TABLE `companyjobpost`
  ADD CONSTRAINT `companyjobpost_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;
ALTER TABLE `companyquotation`
  ADD CONSTRAINT `companyquotation_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `companyquotation_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `companyquotation_ibfk_company` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;
ALTER TABLE `companysettings`
  ADD CONSTRAINT `companysettings_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;
ALTER TABLE `company_employees`
  ADD CONSTRAINT `company_employees_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_employees_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;
ALTER TABLE `company_jobpost`
  ADD CONSTRAINT `company_jobpost_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;
ALTER TABLE `company_subscriptions`
  ADD CONSTRAINT `company_subscriptions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;
ALTER TABLE `contract`
  ADD CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_contract_escrow` FOREIGN KEY (`escrow_account_id`) REFERENCES `escrow_accounts` (`escrow_id`) ON DELETE SET NULL;
ALTER TABLE `contract_audit_log`
  ADD CONSTRAINT `contract_audit_log_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_audit_log_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `contract_milestone` (`milestone_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_audit_log_ibfk_3` FOREIGN KEY (`performed_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
ALTER TABLE `contract_budget_adjustments`
  ADD CONSTRAINT `contract_budget_adjustments_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;
ALTER TABLE `contract_change_requests`
  ADD CONSTRAINT `fk_ccr_contract` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;
ALTER TABLE `contract_chats`
  ADD CONSTRAINT `contract_chats_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;
ALTER TABLE `contract_dispute`
  ADD CONSTRAINT `contract_dispute_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_dispute_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `contract_milestone` (`milestone_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_dispute_ibfk_3` FOREIGN KEY (`raised_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_dispute_ibfk_4` FOREIGN KEY (`resolved_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;
ALTER TABLE `contract_document`
  ADD CONSTRAINT `contract_document_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_document_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `contract_milestone` (`milestone_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_document_ibfk_3` FOREIGN KEY (`uploaded_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
ALTER TABLE `contract_invoices`
  ADD CONSTRAINT `contract_invoices_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_invoices_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `milestone` (`milestone_id`) ON DELETE SET NULL;
ALTER TABLE `contract_milestone`
  ADD CONSTRAINT `contract_milestone_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_milestone_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;
ALTER TABLE `contract_notifications`
  ADD CONSTRAINT `contract_notifications_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;
ALTER TABLE `contract_payment_history`
  ADD CONSTRAINT `contract_payment_history_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_payment_history_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `contract_milestone` (`milestone_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contract_payment_history_ibfk_3` FOREIGN KEY (`paid_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contract_payment_history_ibfk_4` FOREIGN KEY (`paid_to`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;
ALTER TABLE `contract_timeline`
  ADD CONSTRAINT `contract_timeline_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;
ALTER TABLE `contract_time_logs`
  ADD CONSTRAINT `contract_time_logs_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_timelog_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `contract_invoices` (`invoice_id`) ON DELETE SET NULL;
ALTER TABLE `directjobrequest`
  ADD CONSTRAINT `directjobrequest_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `directjobrequest_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);
ALTER TABLE `directrequestquotes`
  ADD CONSTRAINT `directrequestquotes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `directrequestquotes_ibfk_2` FOREIGN KEY (`request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE;
ALTER TABLE `escrow_accounts`
  ADD CONSTRAINT `escrow_accounts_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;
ALTER TABLE `escrow_transaction`
  ADD CONSTRAINT `escrow_txn_wallet_fk` FOREIGN KEY (`wallet_id`) REFERENCES `escrow_wallet` (`wallet_id`) ON DELETE CASCADE;
ALTER TABLE `escrow_wallet`
  ADD CONSTRAINT `escrow_wallet_company_fk` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`),
  ADD CONSTRAINT `escrow_wallet_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`given_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
ALTER TABLE `financialreport`
  ADD CONSTRAINT `financialreport_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `moderator` (`moderator_id`) ON DELETE SET NULL;
ALTER TABLE `financialreporttransaction`
  ADD CONSTRAINT `financialreporttransaction_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `financialreport` (`report_id`) ON DELETE CASCADE;
ALTER TABLE `freelancer_assignments`
  ADD CONSTRAINT `freelancer_assignments_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `freelancer_assignments_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `freelancer_assignments_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `freelancer_assignments_ibfk_4` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE SET NULL;
ALTER TABLE `issuenotification`
  ADD CONSTRAINT `issuenotification_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `issuereport` (`issue_id`) ON DELETE CASCADE;
ALTER TABLE `issuereport`
  ADD CONSTRAINT `issuereport_ibfk_1` FOREIGN KEY (`reportedBy_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
ALTER TABLE `issuestatushistory`
  ADD CONSTRAINT `issuestatushistory_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `issuereport` (`issue_id`) ON DELETE CASCADE;
ALTER TABLE `job`
  ADD CONSTRAINT `job_ibfk_1` FOREIGN KEY (`job_request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_ibfk_2` FOREIGN KEY (`fixer_id`) REFERENCES `repairer` (`repairer_id`);
ALTER TABLE `jobrequest`
  ADD CONSTRAINT `fk_jobrequest_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `jobrequest_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jobrequest_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);
ALTER TABLE `job_collaboration`
  ADD CONSTRAINT `job_collaboration_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
ALTER TABLE `job_collaboration_event`
  ADD CONSTRAINT `job_collaboration_event_ibfk_1` FOREIGN KEY (`collaboration_id`) REFERENCES `job_collaboration` (`collaboration_id`) ON DELETE CASCADE;
ALTER TABLE `job_collaboration_rating`
  ADD CONSTRAINT `job_collaboration_rating_ibfk_1` FOREIGN KEY (`collaboration_id`) REFERENCES `job_collaboration` (`collaboration_id`) ON DELETE CASCADE;
ALTER TABLE `milestone`
  ADD CONSTRAINT `milestone_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;
ALTER TABLE `milestonepayment`
  ADD CONSTRAINT `milestonepayment_ibfk_1` FOREIGN KEY (`milestone_id`) REFERENCES `milestone` (`milestone_id`) ON DELETE CASCADE;
ALTER TABLE `moderatormsg`
  ADD CONSTRAINT `moderatormsg_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`job_request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE;
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `payment_methods_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `user` (`user_id`);
ALTER TABLE `promotion`
  ADD CONSTRAINT `promotion_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;
ALTER TABLE `repairer`
  ADD CONSTRAINT `repairer_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL;
ALTER TABLE `repairerapplication`
  ADD CONSTRAINT `repairerapplication_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repairerapplication_ibfk_2` FOREIGN KEY (`posting_id`) REFERENCES `companyjobpost` (`posting_id`) ON DELETE CASCADE;
ALTER TABLE `repairerassignment`
  ADD CONSTRAINT `repairerassignment_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repairerassignment_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;
ALTER TABLE `repairerquote`
  ADD CONSTRAINT `repairerquote_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repairerquote_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;
ALTER TABLE `repairer_applications`
  ADD CONSTRAINT `repairer_applications_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `job` (`job_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`service_provider_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;
ALTER TABLE `staffsummary`
  ADD CONSTRAINT `staffsummary_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;
ALTER TABLE `supportticket`
  ADD CONSTRAINT `supportticket_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE SET NULL;
ALTER TABLE `support_attachments`
  ADD CONSTRAINT `support_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`ticket_id`) ON DELETE CASCADE;
ALTER TABLE `support_responses`
  ADD CONSTRAINT `support_responses_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`ticket_id`) ON DELETE CASCADE;
ALTER TABLE `ticketmessage`
  ADD CONSTRAINT `ticketmessage_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `supportticket` (`ticket_id`) ON DELETE CASCADE;
ALTER TABLE `action_undo`
  ADD PRIMARY KEY (`undo_id`),
  ADD KEY `idx_entity_action` (`entity_type`,`entity_id`,`action_key`),
  ADD KEY `idx_undo_until` (`undo_until`),
  ADD KEY `idx_used` (`used`,`undo_until`);
ALTER TABLE `project_staff_requirements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_project_specialty` (`project_id`,`specialty`),
  ADD KEY `idx_company_specialty` (`company_id`,`specialty`);
ALTER TABLE `st`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `action_undo`
  MODIFY `undo_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `advertisement`
  MODIFY `ad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
ALTER TABLE `company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9999;
ALTER TABLE `companyquotation`
  MODIFY `quotation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
ALTER TABLE `contract`
  MODIFY `contract_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
ALTER TABLE `contract_audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `contract_change_requests`
  MODIFY `change_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `contract_chats`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
ALTER TABLE `contract_milestone`
  MODIFY `milestone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;
ALTER TABLE `directjobrequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `directrequestquotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `jobrequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;
ALTER TABLE `project`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
ALTER TABLE `project_staff_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `repairer`
  MODIFY `repairer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;
ALTER TABLE `repairerquote`
  MODIFY `quote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
ALTER TABLE `service_area`
  MODIFY `area_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `st`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `system_audit_log`
  MODIFY `audit_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1964;
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9998;
ALTER TABLE `project_staff_requirements`
  ADD CONSTRAINT `fk_psr_company` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_psr_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE;
ALTER TABLE `contract_milestone` ADD COLUMN `actual_unit_rate` decimal(12,2) DEFAULT NULL COMMENT 'Actual unit rate submitted by company (e.g., material price variation)';
ALTER TABLE `contract_milestone` ADD COLUMN `is_non_paying` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 if this milestone is non-paying (inspection / no measurable units)';
ALTER TABLE `contract_milestone` ADD COLUMN `actual_labor_quantity` decimal(10,2) DEFAULT NULL COMMENT 'Actual labour units submitted by company';
ALTER TABLE `contract_milestone` ADD COLUMN `actual_material_quantity` decimal(10,2) DEFAULT NULL COMMENT 'Actual material units submitted by company';
ALTER TABLE `contract_milestone` ADD COLUMN `actual_material_unit_rate` decimal(12,2) DEFAULT NULL COMMENT 'Actual material unit rate submitted by company (optional variation)';
ALTER TABLE `contract_milestone` ADD COLUMN `actual_extra_amount` decimal(12,2) DEFAULT NULL COMMENT 'Additional amount outside labour/material for this milestone';

SET FOREIGN_KEY_CHECKS=1;


-- Repairer settings table
CREATE TABLE `repairersettings` (
  `setting_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `email_job_requests` tinyint(1) DEFAULT 1,
  `email_quote_responses` tinyint(1) DEFAULT 1,
  `email_payment_notifications` tinyint(1) DEFAULT 1,
  `email_reviews_ratings` tinyint(1) DEFAULT 1,
  `email_weekly_summary` tinyint(1) DEFAULT 0,
  `push_browser_notifications` tinyint(1) DEFAULT 0,
  `push_sound_alerts` tinyint(1) DEFAULT 1,
  `privacy_profile_visibility` tinyint(1) DEFAULT 1,
  `privacy_show_contact` tinyint(1) DEFAULT 0,
  `privacy_location_sharing` tinyint(1) DEFAULT 1,
  `security_login_alerts` tinyint(1) DEFAULT 1,
  `security_session_timeout` varchar(20) DEFAULT '30 minutes',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `repairersettings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `unique_repairer` (`repairer_id`);

ALTER TABLE `repairersettings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `repairersettings`
  ADD CONSTRAINT `repairersettings_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;
