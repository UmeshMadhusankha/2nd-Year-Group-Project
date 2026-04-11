-- Database Schema for Home Repair Service Platform
-- Matches Live Database as of 2026-01-18

-- IMPORTANT
-- This file DROPS and RE-CREATES the `fix_lanka` database.
-- If you only want to apply recent additions (like `system_audit_log`) to an existing DB,
-- DO NOT run the whole file. Instead, run the "MIGRATION ONLY" block at the bottom.

DROP DATABASE IF EXISTS fix_lanka;
CREATE DATABASE fix_lanka;
USE fix_lanka;

-- User Table
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `f_name` varchar(100) NOT NULL,
  `l_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profilePicture` varchar(500) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Activity Log
CREATE TABLE `activitylog` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`activity_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Admin
CREATE TABLE `admin` (
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Advertisement
CREATE TABLE `advertisement` (
  `ad_id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `provider_type` enum('repairer','company') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `view_count` int(11) DEFAULT 0,
  `click_count` int(11) DEFAULT 0,
  `type` enum('banner','featured','sponsored') NOT NULL,
  `budget` decimal(10,2) NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('pending','approved','scheduled','active','paused','inactive','suspended','expired','rejected') DEFAULT 'pending',
  PRIMARY KEY (`ad_id`),
  KEY `idx_provider` (`provider_id`,`provider_type`),
  KEY `idx_status` (`status`),
  KEY `idx_dates` (`start_date`,`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Ad Schedule
CREATE TABLE `adschedule` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  PRIMARY KEY (`schedule_id`),
  KEY `idx_ad` (`ad_id`),
  KEY `idx_dates` (`start_date`,`end_date`),
  CONSTRAINT `adschedule_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Advertisement Reports (User/Provider reports against ads)
CREATE TABLE `adreport` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `reporter_type` enum('user','repairer','company') NOT NULL DEFAULT 'user',
  `reporter_id` int(11) DEFAULT NULL,
  `issue_type` enum(
    'inappropriate_content',
    'misleading_information',
    'spam',
    'privacy_violation',
    'copyright_infringement',
    'fraud',
    'other'
  ) NOT NULL DEFAULT 'other',
  `description` text NOT NULL,
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'low',
  `status` enum('pending','investigating','resolved','dismissed','escalated') NOT NULL DEFAULT 'pending',
  `moderator_notes` text DEFAULT NULL,
  `evidence` varchar(500) DEFAULT NULL,
  `handled_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`report_id`),
  KEY `idx_ad` (`ad_id`),
  KEY `idx_status` (`status`),
  KEY `idx_priority` (`priority`),
  CONSTRAINT `adreport_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Ad Rotation Settings (Moderator controlled)
CREATE TABLE `adrotationsettings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_seconds` int(11) NOT NULL DEFAULT 30,
  `featured_seconds` int(11) NOT NULL DEFAULT 60,
  `sponsored_seconds` int(11) NOT NULL DEFAULT 90,
  `banner_capacity` int(11) NOT NULL DEFAULT 5,
  `featured_capacity` int(11) NOT NULL DEFAULT 3,
  `sponsored_capacity` int(11) NOT NULL DEFAULT 8,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `adrotationsettings` (
  banner_seconds, featured_seconds, sponsored_seconds,
  banner_capacity, featured_capacity, sponsored_capacity
) VALUES (30, 60, 90, 5, 3, 8);

-- Company
CREATE TABLE `company` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `business_type` varchar(255) DEFAULT NULL,
  `registration_no` varchar(100) NOT NULL,
  `tax_id` varchar(100) DEFAULT NULL,
  `address` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `contact_no` varchar(20) NOT NULL,
  `districts` text DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `date_of_joined` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`company_id`),
  UNIQUE KEY `registration_no` (`registration_no`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Company Settings
CREATE TABLE `companysettings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `unique_company` (`company_id`),
  CONSTRAINT `companysettings_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- User Sessions (Session Management)
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
  `is_current` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`session_id`),
  KEY `idx_user` (`user_id`,`user_role`),
  KEY `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Company Subscriptions
CREATE TABLE `company_subscriptions` (
  `subscription_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`subscription_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `company_subscriptions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Payment Methods
CREATE TABLE `payment_methods` (
  `payment_method_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`payment_method_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_primary` (`is_primary`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `payment_methods_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Billing History
CREATE TABLE `billinghistory` (
  `invoice_id` varchar(50) NOT NULL,
  `company_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('paid','pending','failed') DEFAULT 'pending',
  `download_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`invoice_id`),
  KEY `idx_company_date` (`company_id`,`date`),
  CONSTRAINT `billinghistory_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Category
CREATE TABLE `category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Repairer
CREATE TABLE `repairer` (
  `repairer_id` int(11) NOT NULL AUTO_INCREMENT,
  `f_name` varchar(100) NOT NULL,
  `l_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phoneNumber` varchar(20) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `profile_picture` varchar(500) DEFAULT NULL,
  `experience_initial_years` int(11) NOT NULL DEFAULT 0,
  `experience_years` int(11) NOT NULL DEFAULT 0,
  `hourly_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ratings` decimal(3,2) DEFAULT 0.00,
  `completed_jobs_count` int(11) DEFAULT 0,
  `skills` text DEFAULT NULL,
  `districts` text DEFAULT NULL,
  `availability` enum('available','busy','unavailable') DEFAULT 'available',
  `dateJoined` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`repairer_id`),
  UNIQUE KEY `email` (`email`),
  KEY `category_id` (`category_id`),
  KEY `idx_email` (`email`),
  KEY `idx_ratings` (`ratings`),
  CONSTRAINT `repairer_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Chat Message
CREATE TABLE `chatmessage` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `firebase_chat_id` varchar(255) DEFAULT NULL,
  `last_message` text DEFAULT NULL,
  `last_message_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`session_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_repairer` (`repairer_id`),
  CONSTRAINT `chatmessage_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `chatmessage_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Company Employee
CREATE TABLE `companyemployee` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`employee_id`),
  KEY `repairer_id` (`repairer_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_specialty` (`specialty`),
  KEY `idx_status` (`status`),
  KEY `idx_rating` (`rating`),
  CONSTRAINT `companyemployee_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  CONSTRAINT `companyemployee_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Company Job Post
CREATE TABLE `companyjobpost` (
  `posting_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `closed_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`posting_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_status` (`status`),
  KEY `idx_category` (`category`),
  KEY `idx_posted_date` (`posted_date`),
  KEY `idx_deadline` (`application_deadline`),
  CONSTRAINT `companyjobpost_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Job Request
CREATE TABLE `jobrequest` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','accepted','in_progress','completed','cancelled') DEFAULT 'pending',
  `district` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `service_provider_type` varchar(50) NOT NULL,
  `urgency` enum('medium','urgent') DEFAULT 'medium',
  `finish_date` date NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `photos` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  KEY `category_id` (`category_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`dateCreated`),
  KEY `idx_district` (`district`),
  CONSTRAINT `jobrequest_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `jobrequest_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Company Quotation
CREATE TABLE `companyquotation` (
  `quotation_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
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
  `payment_terms` varchar(100) DEFAULT NULL,
  `warranty_period` varchar(50) DEFAULT NULL,
  `additional_terms` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected','successful') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`quotation_id`),
  KEY `idx_request` (`request_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `companyquotation_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE,
  CONSTRAINT `companyquotation_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Project
CREATE TABLE `project` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `progress` int(11) DEFAULT 0 CHECK (`progress` >= 0 and `progress` <= 100),
  PRIMARY KEY (`project_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `project_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  CONSTRAINT `project_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Contract
CREATE TABLE `contract` (
  `contract_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `milestone_plan` tinyint(1) NOT NULL DEFAULT 1,
  `total_budget` decimal(12,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `contract_date` date NOT NULL,
  `user_signature` varchar(255) NOT NULL,
  `company_signature` varchar(255) NOT NULL,
  `terms_conditions` text DEFAULT NULL,
  `status` enum('draft','active','completed','terminated') DEFAULT 'draft',
  PRIMARY KEY (`contract_id`),
  KEY `idx_project` (`project_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Contract Milestones (Enhanced - linked to contract)
CREATE TABLE `contract_milestone` (
  `milestone_id` int(11) NOT NULL AUTO_INCREMENT,
  `contract_id` int(11) NOT NULL,
  `milestone_number` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','in_progress','submitted','under_review','approved','rejected','disputed','paid') DEFAULT 'pending',
  `escrow_held` decimal(12,2) DEFAULT 0.00,
  `payment_released` decimal(12,2) DEFAULT 0.00,
  `payment_released_at` datetime DEFAULT NULL,
  `proof_of_work` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_comments` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`milestone_id`),
  KEY `idx_contract` (`contract_id`),
  KEY `idx_status` (`status`),
  KEY `idx_due_date` (`due_date`),
  KEY `idx_reviewed_by` (`reviewed_by`),
  CONSTRAINT `contract_milestone_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Contract Audit Log (change tracking)
CREATE TABLE `contract_audit_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `contract_id` int(11) NOT NULL,
  `milestone_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `performed_by` int(11) NOT NULL,
  `user_role` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `idx_contract` (`contract_id`),
  KEY `idx_milestone` (`milestone_id`),
  KEY `idx_action` (`action`),
  KEY `idx_performed_by` (`performed_by`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `contract_audit_log_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Contract Draft (auto-save for contract creation form)
CREATE TABLE `contract_draft` (
  `draft_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `quotation_id` int(11) DEFAULT NULL,
  `form_data` json NOT NULL,
  `current_step` int(11) DEFAULT 1,
  `created_at` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`draft_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_quotation` (`quotation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Contract Dispute
CREATE TABLE `contract_dispute` (
  `dispute_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`dispute_id`),
  KEY `raised_by` (`raised_by`),
  KEY `resolved_by` (`resolved_by`),
  KEY `idx_contract_id` (`contract_id`),
  KEY `idx_milestone_id` (`milestone_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `contract_dispute_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  CONSTRAINT `contract_dispute_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `contract_milestone` (`milestone_id`) ON DELETE CASCADE,
  CONSTRAINT `contract_dispute_ibfk_3` FOREIGN KEY (`raised_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `contract_dispute_ibfk_4` FOREIGN KEY (`resolved_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Contract Document
CREATE TABLE `contract_document` (
  `document_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`document_id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `idx_contract_id` (`contract_id`),
  KEY `idx_milestone_id` (`milestone_id`),
  KEY `idx_document_type` (`document_type`),
  CONSTRAINT `contract_document_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  CONSTRAINT `contract_document_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `contract_milestone` (`milestone_id`) ON DELETE CASCADE,
  CONSTRAINT `contract_document_ibfk_3` FOREIGN KEY (`uploaded_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Contract Payment History
CREATE TABLE `contract_payment_history` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `paid_by` (`paid_by`),
  KEY `paid_to` (`paid_to`),
  KEY `idx_contract_id` (`contract_id`),
  KEY `idx_milestone_id` (`milestone_id`),
  KEY `idx_payment_type` (`payment_type`),
  KEY `idx_status` (`status`),
  CONSTRAINT `contract_payment_history_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  CONSTRAINT `contract_payment_history_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `contract_milestone` (`milestone_id`) ON DELETE SET NULL,
  CONSTRAINT `contract_payment_history_ibfk_3` FOREIGN KEY (`paid_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `contract_payment_history_ibfk_4` FOREIGN KEY (`paid_to`) REFERENCES `user` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- System Audit Log (cross-module activity logging)
-- Stores high-level audit events for actions performed by any role.
CREATE TABLE IF NOT EXISTS `system_audit_log` (
  `audit_id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `details` json DEFAULT NULL,
  PRIMARY KEY (`audit_id`),
  KEY `idx_audit_actor_time` (`actor_user_id`, `occurred_at`),
  KEY `idx_audit_action_time` (`action`, `occurred_at`),
  KEY `idx_audit_entity` (`entity_type`, `entity_id`),
  KEY `idx_audit_request` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Make audit logs append-only (no UPDATE/DELETE allowed)
DROP TRIGGER IF EXISTS `system_audit_log_no_update`;
DROP TRIGGER IF EXISTS `system_audit_log_no_delete`;

DELIMITER //
CREATE TRIGGER `system_audit_log_no_update`
BEFORE UPDATE ON `system_audit_log`
FOR EACH ROW
BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'system_audit_log is append-only (updates are not allowed)';
END//

CREATE TRIGGER `system_audit_log_no_delete`
BEFORE DELETE ON `system_audit_log`
FOR EACH ROW
BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'system_audit_log is append-only (deletes are not allowed)';
END//
DELIMITER ;

-- =====================================================================
-- MIGRATION ONLY (SAFE TO RUN ON EXISTING DB)
-- Apply this block if your current database is missing `system_audit_log`.
-- =====================================================================

-- Account Moderation Log (SAFE)
-- Used by the Admin Account Moderation page to persist moderation reason and
-- suspension end date, especially for tables that don't have those columns.
CREATE TABLE IF NOT EXISTS `account_moderation_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_type` varchar(30) NOT NULL,
  `account_id` int(11) NOT NULL,
  `action_type` enum('suspend','ban','restore') NOT NULL,
  `reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `suspended_until` datetime DEFAULT NULL,
  `acted_by_role` varchar(30) DEFAULT NULL,
  `acted_by_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `idx_account_lookup` (`account_type`, `account_id`, `created_at`),
  KEY `idx_action_type` (`action_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create the table if it does not exist
CREATE TABLE IF NOT EXISTS `system_audit_log` (
  `audit_id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `details` json DEFAULT NULL,
  PRIMARY KEY (`audit_id`),
  KEY `idx_audit_actor_time` (`actor_user_id`, `occurred_at`),
  KEY `idx_audit_action_time` (`action`, `occurred_at`),
  KEY `idx_audit_entity` (`entity_type`, `entity_id`),
  KEY `idx_audit_request` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Ensure append-only triggers exist (recreate safely)
DROP TRIGGER IF EXISTS `system_audit_log_no_update`;
DROP TRIGGER IF EXISTS `system_audit_log_no_delete`;

DELIMITER //
CREATE TRIGGER `system_audit_log_no_update`
BEFORE UPDATE ON `system_audit_log`
FOR EACH ROW
BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'system_audit_log is append-only (updates are not allowed)';
END//

CREATE TRIGGER `system_audit_log_no_delete`
BEFORE DELETE ON `system_audit_log`
FOR EACH ROW
BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'system_audit_log is append-only (deletes are not allowed)';
END//
DELIMITER ;

-- Legacy Milestones (kept for backward compatibility)
CREATE TABLE `milestone` (
  `milestone_id` int(11) NOT NULL AUTO_INCREMENT,
  `contract_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `agreements` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed','overdue') DEFAULT 'pending',
  PRIMARY KEY (`milestone_id`),
  KEY `idx_contract` (`contract_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `milestone_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `milestonepayment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `milestone_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `method` enum('credit_card','debit_card','bank_transfer','check','cash') NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  PRIMARY KEY (`payment_id`),
  KEY `idx_milestone` (`milestone_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `milestonepayment_ibfk_1` FOREIGN KEY (`milestone_id`) REFERENCES `milestone` (`milestone_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Feedback
CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `given_by` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comments` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`feedback_id`),
  KEY `given_by` (`given_by`),
  KEY `idx_project` (`project_id`),
  CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`given_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Financial Report
CREATE TABLE `financialreport` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `month` date NOT NULL,
  `revenue` decimal(12,2) DEFAULT 0.00,
  `pending_withdrawals` decimal(12,2) DEFAULT 0.00,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`report_id`),
  KEY `idx_month` (`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Issue Report
CREATE TABLE `issuereport` (
  `issue_id` int(11) NOT NULL AUTO_INCREMENT,
  `reportedBy_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `target_type` enum('user','repairer','company') NOT NULL,
  `description` text NOT NULL,
  `status` enum('open','investigating','resolved','closed') DEFAULT 'open',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`issue_id`),
  KEY `idx_reporter` (`reportedBy_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `issuereport_ibfk_1` FOREIGN KEY (`reportedBy_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Job
CREATE TABLE `job` (
  `job_id` int(11) NOT NULL AUTO_INCREMENT,
  `job_request_id` int(11) NOT NULL,
  `fixer_id` int(11) NOT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `completionDate` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`job_id`),
  KEY `job_request_id` (`job_request_id`),
  KEY `idx_status` (`status`),
  KEY `idx_fixer` (`fixer_id`),
  CONSTRAINT `job_ibfk_1` FOREIGN KEY (`job_request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE,
  CONSTRAINT `job_ibfk_2` FOREIGN KEY (`fixer_id`) REFERENCES `repairer` (`repairer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Moderator
CREATE TABLE `moderator` (
  `moderator_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `assigned_section` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`moderator_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `moderatormsg` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `repairer_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  `date_sent` timestamp NOT NULL DEFAULT current_timestamp(),
  `response` text DEFAULT NULL,
  `date_resolved` timestamp NULL DEFAULT NULL,
  `status` enum('open','pending','resolved','closed') DEFAULT 'open',
  PRIMARY KEY (`msg_id`),
  KEY `idx_repairer` (`repairer_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `moderatormsg_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Notification
CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `send_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `recipient_type` enum('user','repairer','company','all') NOT NULL,
  `status` enum('sent','pending','failed') DEFAULT 'pending',
  `created_by_id` int(11) DEFAULT NULL,
  `created_by_role` enum('admin','moderator','company','repairer','user') DEFAULT NULL,
  `created_by_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Payment
CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `job_request_id` int(11) NOT NULL,
  `paymentType` enum('credit_card','debit_card','cash','bank_transfer') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paymentDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  PRIMARY KEY (`payment_id`),
  KEY `idx_job_request` (`job_request_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`job_request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Promotion
CREATE TABLE `promotion` (
  `promotion_id` int(11) NOT NULL AUTO_INCREMENT,
  `repairer_id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active',
  PRIMARY KEY (`promotion_id`),
  KEY `idx_repairer` (`repairer_id`),
  KEY `idx_status` (`status`),
  KEY `idx_dates` (`start_date`,`end_date`),
  CONSTRAINT `promotion_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Repairer Application
CREATE TABLE `repairerapplication` (
  `app_id` int(11) NOT NULL AUTO_INCREMENT,
  `repairer_id` int(11) NOT NULL,
  `posting_id` int(11) NOT NULL,
  `date_applied` timestamp NOT NULL DEFAULT current_timestamp(),
  `app_status` enum('pending','reviewed','accepted','rejected') DEFAULT 'pending',
  PRIMARY KEY (`app_id`),
  KEY `idx_repairer` (`repairer_id`),
  KEY `idx_posting` (`posting_id`),
  KEY `idx_status` (`app_status`),
  CONSTRAINT `repairerapplication_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE,
  CONSTRAINT `repairerapplication_ibfk_2` FOREIGN KEY (`posting_id`) REFERENCES `companyjobpost` (`posting_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Repairer Assignment
CREATE TABLE `repairerassignment` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `assigned_date` date DEFAULT curdate(),
  `status` enum('assigned','active','completed','removed') DEFAULT 'assigned',
  PRIMARY KEY (`assignment_id`),
  KEY `idx_project` (`project_id`),
  KEY `idx_repairer` (`repairer_id`),
  CONSTRAINT `repairerassignment_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `repairerassignment_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Repairer Quote
CREATE TABLE `repairerquote` (
  `quote_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `quoteAmount` decimal(10,2) NOT NULL,
  `estimatedDays` int(11) NOT NULL DEFAULT 1,
  `warrantyPeriod` int(11) DEFAULT 0,
  `validUntil` date NOT NULL,
  `materialsIncluded` tinyint(1) DEFAULT 1,
  `message` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected','expired') DEFAULT 'pending',
  `dateSubmitted` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`quote_id`),
  KEY `idx_request` (`request_id`),
  KEY `idx_repairer` (`repairer_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `repairerquote_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE,
  CONSTRAINT `repairerquote_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Report
CREATE TABLE `report` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `status` enum('pending','investigating','resolved') DEFAULT 'pending',
  `resolve_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`report_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Review
CREATE TABLE `review` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `service_provider_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comments` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`review_id`),
  KEY `idx_job` (`job_id`),
  KEY `idx_provider` (`service_provider_id`),
  CONSTRAINT `review_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `job` (`job_id`) ON DELETE CASCADE,
  CONSTRAINT `review_ibfk_2` FOREIGN KEY (`service_provider_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Staff Summary
CREATE TABLE `staffsummary` (
  `staff_summary_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `specialty` varchar(100) NOT NULL,
  `total_count` int(11) DEFAULT 0,
  `active_count` int(11) DEFAULT 0,
  `inactive_count` int(11) DEFAULT 0,
  `avg_rating` decimal(3,2) DEFAULT 0.00,
  `avg_hourly_rate` decimal(10,2) DEFAULT 0.00,
  `min_hourly_rate` decimal(10,2) DEFAULT 0.00,
  `max_hourly_rate` decimal(10,2) DEFAULT 0.00,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`staff_summary_id`),
  UNIQUE KEY `unique_company_specialty` (`company_id`,`specialty`),
  KEY `idx_company` (`company_id`),
  KEY `idx_specialty` (`specialty`),
  CONSTRAINT `staffsummary_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Static Content
CREATE TABLE `staticcontent` (
  `content_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `body` text NOT NULL,
  `status` enum('Draft','Published') NOT NULL DEFAULT 'Published',
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `content_type` enum('terms','privacy','faq','about','help','contact','how_it_works','services','why_choose','support') NOT NULL,
  PRIMARY KEY (`content_id`),
  KEY `idx_type` (`content_type`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Support Tickets
CREATE TABLE `support_tickets` (
  `ticket_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `closed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`ticket_id`),
  UNIQUE KEY `ticket_number` (`ticket_number`),
  KEY `idx_user` (`user_type`,`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_priority` (`priority`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Support Attachments
CREATE TABLE `support_attachments` (
  `attachment_id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL COMMENT 'Size in bytes',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`attachment_id`),
  KEY `idx_ticket` (`ticket_id`),
  CONSTRAINT `support_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`ticket_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Support Categories
CREATE TABLE `support_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `category_slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_slug` (`category_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Support Responses
CREATE TABLE `support_responses` (
  `response_id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `responder_type` enum('user','admin','moderator','system') NOT NULL,
  `responder_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_internal_note` tinyint(1) DEFAULT 0 COMMENT 'Notes only visible to admins',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`response_id`),
  KEY `idx_ticket` (`ticket_id`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `support_responses_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`ticket_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Legacy Support Ticket (if needed, otherwise can be removed if support_tickets is the primary)
CREATE TABLE `supportticket` (
  `ticket_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `attachment` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`ticket_id`),
  KEY `idx_user` (`user_id`,`user_type`),
  KEY `idx_status` (`status`),
  KEY `idx_project` (`project_id`),
  CONSTRAINT `supportticket_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ticketmessage` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('user','repairer','company','admin','moderator') NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`message_id`),
  KEY `idx_ticket` (`ticket_id`),
  CONSTRAINT `ticketmessage_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `supportticket` (`ticket_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- PHASE 1 ADDITIONS: Work Schedule, Budget, Payment, Chat, Escrow
-- Added: February 12, 2026
-- =====================================================================

-- Add work schedule columns to companyquotation
ALTER TABLE `companyquotation`
ADD COLUMN `work_schedule_type` ENUM('all_days', 'weekdays_only', 'weekends_included', 'custom') 
    NOT NULL DEFAULT 'weekdays_only' AFTER `estimated_duration`,
ADD COLUMN `working_days_per_week` TINYINT(1) NULL AFTER `work_schedule_type`,
ADD COLUMN `daily_work_hours` DECIMAL(4,2) NULL AFTER `working_days_per_week`,
ADD COLUMN `work_start_time` TIME NULL AFTER `daily_work_hours`,
ADD COLUMN `work_end_time` TIME NULL AFTER `work_start_time`,
ADD COLUMN `break_duration` DECIMAL(3,2) NULL AFTER `work_end_time`,
ADD COLUMN `custom_schedule_json` TEXT NULL AFTER `break_duration`,
ADD COLUMN `public_holidays_excluded` BOOLEAN DEFAULT TRUE AFTER `custom_schedule_json`,
ADD COLUMN `estimated_calendar_days` INT NULL AFTER `public_holidays_excluded`;

-- Add Phase 1 columns to contract table
ALTER TABLE `contract`
ADD COLUMN `budget_flexibility_percentage` DECIMAL(5,2) NULL AFTER `budget_type`,
ADD COLUMN `undo_deadline` DATETIME NULL AFTER `signed_at`,
ADD COLUMN `undo_requested` BOOLEAN DEFAULT FALSE AFTER `undo_deadline`,
ADD COLUMN `chat_active` BOOLEAN DEFAULT FALSE AFTER `undo_requested`,
ADD COLUMN `chat_activated_at` DATETIME NULL AFTER `chat_active`,
ADD COLUMN `escrow_account_id` INT NULL AFTER `chat_activated_at`,
ADD COLUMN `upfront_payment_percentage` DECIMAL(5,2) NULL AFTER `escrow_account_id`,
ADD COLUMN `upfront_payment_amount` DECIMAL(10,2) NULL AFTER `upfront_payment_percentage`,
ADD COLUMN `upfront_payment_received` BOOLEAN DEFAULT FALSE AFTER `upfront_payment_amount`,
ADD COLUMN `work_verified_started` BOOLEAN DEFAULT FALSE AFTER `upfront_payment_received`,
ADD COLUMN `quality_guarantee_end_date` DATE NULL AFTER `work_verified_started`;

-- Add workflow columns to milestone table
ALTER TABLE `milestone`
ADD COLUMN `submitted_by_company` BOOLEAN DEFAULT FALSE AFTER `status`,
ADD COLUMN `submitted_date` DATETIME NULL AFTER `submitted_by_company`,
ADD COLUMN `customer_approved` BOOLEAN DEFAULT FALSE AFTER `submitted_date`,
ADD COLUMN `customer_approval_date` DATETIME NULL AFTER `customer_approved`,
ADD COLUMN `customer_rejection_reason` TEXT NULL AFTER `customer_approval_date`,
ADD COLUMN `work_started` BOOLEAN DEFAULT FALSE AFTER `customer_rejection_reason`,
ADD COLUMN `work_start_date` DATETIME NULL AFTER `work_started`,
ADD COLUMN `work_completed` BOOLEAN DEFAULT FALSE AFTER `work_start_date`,
ADD COLUMN `work_completion_date` DATETIME NULL AFTER `work_completed`,
ADD COLUMN `customer_verification_requested` BOOLEAN DEFAULT FALSE AFTER `work_completion_date`,
ADD COLUMN `customer_verified` BOOLEAN DEFAULT FALSE AFTER `customer_verification_requested`,
ADD COLUMN `customer_verification_date` DATETIME NULL AFTER `customer_verified`;

-- Contract Chats Table
CREATE TABLE `contract_chats` (
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

-- Contract Notifications Table
CREATE TABLE `contract_notifications` (
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

-- Contract Timeline Table
CREATE TABLE `contract_timeline` (
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

-- Contract Time Logs Table
CREATE TABLE `contract_time_logs` (
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

-- Contract Invoices Table
CREATE TABLE `contract_invoices` (
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
    FOREIGN KEY (`milestone_id`) REFERENCES `milestone`(`milestone_id`) ON DELETE SET NULL,
    INDEX `idx_contract_status` (`contract_id`, `payment_status`),
    INDEX `idx_invoice_number` (`invoice_number`),
    INDEX `idx_due_date` (`due_date`, `payment_status`),
    INDEX `idx_type` (`invoice_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contract Budget Adjustments Table
CREATE TABLE `contract_budget_adjustments` (
    `adjustment_id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_id` INT NOT NULL,
    `requested_by` ENUM('company', 'customer') NOT NULL,
    `requester_id` INT NOT NULL,
    `requester_name` VARCHAR(255) NULL,
    `adjustment_type` ENUM('increase', 'decrease') NOT NULL,
    `original_amount` DECIMAL(10,2) NOT NULL,
    `requested_amount` DECIMAL(10,2) NOT NULL,
    `adjustment_amount` DECIMAL(10,2) NOT NULL,
    `adjustment_percentage` DECIMAL(5,2) NULL,
    `reason` TEXT NOT NULL,
    `justification_documents` TEXT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Escrow Accounts Table
CREATE TABLE `escrow_accounts` (
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

-- Add foreign key constraints
ALTER TABLE `contract`
ADD CONSTRAINT `fk_contract_escrow`
FOREIGN KEY (`escrow_account_id`) REFERENCES `escrow_accounts`(`escrow_id`) 
ON DELETE SET NULL;

ALTER TABLE `contract_time_logs`
ADD CONSTRAINT `fk_timelog_invoice`
FOREIGN KEY (`invoice_id`) REFERENCES `contract_invoices`(`invoice_id`) 
ON DELETE SET NULL;

-- Add indexes for performance
ALTER TABLE `companyquotation`
ADD INDEX `idx_work_schedule` (`work_schedule_type`, `working_days_per_week`);

ALTER TABLE `contract`
ADD INDEX `idx_undo_deadline` (`undo_deadline`),
ADD INDEX `idx_chat_active` (`chat_active`);

ALTER TABLE `milestone`
ADD INDEX `idx_workflow` (`customer_approved`, `work_started`, `work_completed`, `customer_verified`);

-- =====================================================================
-- PHASE 7 ADDITIONS: Milestone Management Enhancements
-- Added: February 14, 2026
-- =====================================================================

-- Add explicit completion and approval tracking for milestones
-- (Note: some overlap with existing fields, but these are used by new logic)
ALTER TABLE `contract_milestone`
ADD COLUMN `completed_at` DATETIME NULL AFTER `updated_at`,
ADD COLUMN `approved_at` DATETIME NULL AFTER `completed_at`,
ADD COLUMN `proof_files` TEXT NULL AFTER `approved_at`,
ADD COLUMN `comments` TEXT NULL AFTER `proof_files`;

-- =====================================================================
-- PHASE 8 ADDITIONS: Escrow System
-- Added: February 15, 2026
-- =====================================================================

-- Escrow Wallet: Tracks balance for Users (Customers) and Companies
CREATE TABLE IF NOT EXISTS `escrow_wallet` (
    `wallet_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `company_id` int(11) DEFAULT NULL,
    `balance` decimal(15,2) DEFAULT 0.00,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`wallet_id`),
    UNIQUE KEY `user_id` (`user_id`),
    UNIQUE KEY `company_id` (`company_id`),
    CONSTRAINT `escrow_wallet_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT,
    CONSTRAINT `escrow_wallet_company_fk` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE RESTRICT,
    CONSTRAINT `check_owner` CHECK ((`user_id` IS NOT NULL AND `company_id` IS NULL) OR (`user_id` IS NULL AND `company_id` IS NOT NULL))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Escrow Transactions: Logs history of funds
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
    KEY `idx_contract` (`related_contract_id`),
    CONSTRAINT `escrow_txn_wallet_fk` FOREIGN KEY (`wallet_id`) REFERENCES `escrow_wallet` (`wallet_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure contract table has escrow_enabled if not already
-- (Phase 2 might have added it, but good to ensure)
-- Note: 'escrow_enabled' column check can be done manually or via robust migration script.
-- For this SQL dump, we assume it's created if missing.
ALTER TABLE `contract`
ADD COLUMN `escrow_enabled` TINYINT(1) DEFAULT 0 AFTER `payment_method`;

-- =====================================================================
-- PHASE 4 ADDITIONS: Workforce System
-- =====================================================================

-- 1. Company Job Post Table
CREATE TABLE IF NOT EXISTS `companyjobpost` (
  `posting_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','freelance') NOT NULL,
  `description` text NOT NULL,
  `requirements` text,
  `min_experience` int(11) NOT NULL DEFAULT 0,
  `priority_level` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `min_budget` decimal(10,2) NOT NULL,
  `max_budget` decimal(10,2) NOT NULL,
  `location` varchar(255) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `location_requirements` text DEFAULT NULL,
  `required_skills` text DEFAULT NULL,
  `application_deadline` date DEFAULT NULL,
  `status` enum('draft','open','closed','filled') DEFAULT 'open',
  `created_at` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`posting_id`),
  FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Repairer Applications Table
CREATE TABLE IF NOT EXISTS `repairer_applications` (
  `application_id` int(11) NOT NULL AUTO_INCREMENT,
  `job_posting_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `cover_letter` text,
  `expected_rate` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','reviewed','interview','approved','rejected') DEFAULT 'pending',
  `rejection_reason` text,
  `applied_date` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`application_id`),
  UNIQUE KEY `uniq_posting_repairer` (`job_posting_id`,`repairer_id`),
  FOREIGN KEY (`job_posting_id`) REFERENCES `companyjobpost` (`posting_id`) ON DELETE CASCADE,
  FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Company Employees (Roster) Table
CREATE TABLE IF NOT EXISTS `company_employees` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `job_title` varchar(255) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','freelance') NOT NULL DEFAULT 'freelance',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `hired_date` date NOT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`employee_id`),
  FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_company_repairer` (`company_id`,`repairer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3a. Project Employee Assignments (Company staff per project)
CREATE TABLE IF NOT EXISTS `project_employee_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `assigned_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_employee` (`project_id`, `employee_id`),
  FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `company_employees` (`employee_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Freelancer Job Assignments (Job Offers) Table
CREATE TABLE IF NOT EXISTS `freelancer_assignments` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `contract_id` int(11) DEFAULT NULL,
  `pricing_model` enum('hourly','fixed') NOT NULL DEFAULT 'hourly',
  `rate_or_price` decimal(10,2) NOT NULL,
  `estimated_hours` decimal(5,2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `deadline_date` date NOT NULL,
  `notes` text,
  `status` enum('offered','accepted','declined','in_progress','completed','cancelled') DEFAULT 'offered',
  `created_at` timestamp DEFAULT current_timestamp(),
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`assignment_id`),
  FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE,
  FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE SET NULL,
  FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Repairer Work History (Portfolio)
-- Stores completed work entries for a repairer (company access is read-only via APIs)
CREATE TABLE IF NOT EXISTS `repairer_work_history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `repairer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `hours_worked` decimal(6,2) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `attachments_json` text DEFAULT NULL,
  `source` enum('manual','platform') NOT NULL DEFAULT 'manual',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`history_id`),
  KEY `idx_repairer` (`repairer_id`),
  KEY `idx_completed` (`completed_at`),
  CONSTRAINT `repairer_work_history_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE,
  CONSTRAINT `repairer_work_history_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
