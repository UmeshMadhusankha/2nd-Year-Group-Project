-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2026 at 03:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fix_lanka`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_moderation_log`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `activitylog`
--

CREATE TABLE `activitylog` (
  `activity_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `adreport`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `adrotationsettings`
--

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

--
-- Dumping data for table `adrotationsettings`
--

INSERT INTO `adrotationsettings` (`setting_id`, `banner_seconds`, `featured_seconds`, `sponsored_seconds`, `banner_capacity`, `featured_capacity`, `sponsored_capacity`, `updated_by`, `updated_at`) VALUES
(1, 30, 60, 90, 5, 3, 8, NULL, '2026-04-09 12:07:43');

-- --------------------------------------------------------

--
-- Table structure for table `adschedule`
--

CREATE TABLE `adschedule` (
  `schedule_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `advertisement`
--

CREATE TABLE `advertisement` (
  `ad_id` int(11) NOT NULL,
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
  `status` enum('pending','approved','scheduled','active','paused','inactive','suspended','expired','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `billinghistory`
--

CREATE TABLE `billinghistory` (
  `invoice_id` varchar(50) NOT NULL,
  `company_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('paid','pending','failed') DEFAULT 'pending',
  `download_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chatmessage`
--

CREATE TABLE `chatmessage` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `firebase_chat_id` varchar(255) DEFAULT NULL,
  `last_message` text DEFAULT NULL,
  `last_message_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `company_id` int(11) NOT NULL,
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
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companyjobpost`
--

CREATE TABLE `companyjobpost` (
  `posting_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','freelance') NOT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companyquotation`
--

CREATE TABLE `companyquotation` (
  `quotation_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
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
  `work_schedule_type` enum('all_days','weekdays_only','weekends_included','custom') NOT NULL DEFAULT 'weekdays_only',
  `working_days_per_week` tinyint(1) DEFAULT NULL,
  `daily_work_hours` decimal(4,2) DEFAULT NULL,
  `work_start_time` time DEFAULT NULL,
  `work_end_time` time DEFAULT NULL,
  `break_duration` decimal(3,2) DEFAULT NULL,
  `custom_schedule_json` text DEFAULT NULL,
  `public_holidays_excluded` tinyint(1) DEFAULT 1,
  `estimated_calendar_days` int(11) DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `warranty_period` varchar(50) DEFAULT NULL,
  `additional_terms` text DEFAULT NULL,
  `budget_type` enum('fixed','flexible') DEFAULT 'fixed' COMMENT 'Fixed or Flexible (±10%)',
  `budget_min` decimal(10,2) DEFAULT NULL COMMENT 'Minimum budget for flexible pricing',
  `budget_max` decimal(10,2) DEFAULT NULL COMMENT 'Maximum budget for flexible pricing',
  `payment_method` enum('full_upfront','milestone_based','50_50','30_70','completion') DEFAULT 'full_upfront' COMMENT 'Payment method selected',
  `pricing_type` enum('fixed_price','time_and_material') DEFAULT 'fixed_price' COMMENT 'Pricing structure type',
  `hourly_rate` decimal(10,2) DEFAULT NULL COMMENT 'Hourly rate for Time & Material',
  `spending_cap_multiplier` decimal(3,2) DEFAULT 1.10 COMMENT 'Spending cap multiplier for T&M (default 1.10 = 110%)',
  `status` enum('pending','accepted','rejected','successful') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companysettings`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `company_employees`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `company_subscriptions`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--

CREATE TABLE `contract` (
  `contract_id` int(11) NOT NULL,
  `contract_number` varchar(50) DEFAULT NULL,
  `quotation_id` int(11) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `job_request_id` int(11) DEFAULT NULL,
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
  `total_milestones` int(11) DEFAULT 0,
  `completed_milestones` int(11) DEFAULT 0,
  `progress_percentage` int(11) DEFAULT 0,
  `auto_generated` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `total_budget` decimal(12,2) NOT NULL,
  `budget_type` enum('fixed','time_based','flexible') DEFAULT 'fixed',
  `budget_flexibility_percentage` decimal(5,2) DEFAULT NULL,
  `budget_min` decimal(12,2) DEFAULT NULL,
  `budget_max` decimal(12,2) DEFAULT NULL,
  `tax_inclusive` tinyint(1) DEFAULT 1,
  `payment_method` enum('full_upfront','milestone_based','50_50','30_70','completion') DEFAULT 'milestone_based',
  `escrow_enabled` tinyint(1) DEFAULT 0,
  `advance_payment_pct` decimal(5,2) DEFAULT 0.00,
  `pricing_type` enum('fixed_price','time_and_material') DEFAULT 'fixed_price',
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `spending_cap` decimal(12,2) DEFAULT NULL,
  `late_payment_penalty` text DEFAULT NULL,
  `pause_work_clause` tinyint(1) DEFAULT 1,
  `time_extension_clause` tinyint(1) DEFAULT 1,
  `variation_clause` tinyint(1) DEFAULT 1,
  `communication_channel` varchar(100) DEFAULT 'system',
  `dispute_resolution` text DEFAULT NULL,
  `actual_hours` decimal(10,2) DEFAULT 0.00,
  `actual_cost` decimal(12,2) DEFAULT 0.00,
  `amount_paid` decimal(12,2) DEFAULT 0.00,
  `amount_pending` decimal(12,2) DEFAULT 0.00,
  `payment_status` enum('pending','partial','completed','overdue') DEFAULT 'pending',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `terms_accepted` tinyint(1) DEFAULT 0,
  `terms_accepted_at` datetime DEFAULT NULL,
  `contract_date` date NOT NULL,
  `user_signature` varchar(255) NOT NULL,
  `company_signature` varchar(255) NOT NULL,
  `customer_signature` text DEFAULT NULL,
  `signed_at` datetime DEFAULT NULL,
  `undo_deadline` datetime DEFAULT NULL,
  `undo_requested` tinyint(1) DEFAULT 0,
  `chat_active` tinyint(1) DEFAULT 0,
  `chat_activated_at` datetime DEFAULT NULL,
  `escrow_account_id` int(11) DEFAULT NULL,
  `upfront_payment_percentage` decimal(5,2) DEFAULT NULL,
  `upfront_payment_amount` decimal(10,2) DEFAULT NULL,
  `upfront_payment_received` tinyint(1) DEFAULT 0,
  `work_verified_started` tinyint(1) DEFAULT 0,
  `quality_guarantee_end_date` date DEFAULT NULL,
  `terms_conditions` text DEFAULT NULL,
  `status` enum('draft','pending_signature','active','in_progress','milestone_pending','completed','terminated','disputed') DEFAULT 'draft',
  `sent_to_customer` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `customer_response` enum('pending','accepted','rejected','negotiating') DEFAULT 'pending',
  `customer_response_at` timestamp NULL DEFAULT NULL,
  `locked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_audit_log`
--

CREATE TABLE `contract_audit_log` (
  `log_id` int(11) NOT NULL,
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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_budget_adjustments`
--

CREATE TABLE `contract_budget_adjustments` (
  `adjustment_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `requested_by` enum('company','customer') NOT NULL,
  `requester_id` int(11) NOT NULL,
  `requester_name` varchar(255) DEFAULT NULL,
  `adjustment_type` enum('increase','decrease') NOT NULL,
  `original_amount` decimal(10,2) NOT NULL,
  `requested_amount` decimal(10,2) NOT NULL,
  `adjustment_amount` decimal(10,2) NOT NULL,
  `adjustment_percentage` decimal(5,2) DEFAULT NULL,
  `reason` text NOT NULL,
  `justification_documents` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_by_name` varchar(255) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_chats`
--

CREATE TABLE `contract_chats` (
  `chat_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `sender_type` enum('company','customer') NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `attachment_type` enum('text','image','document','pdf') DEFAULT 'text',
  `attachment_url` varchar(500) DEFAULT NULL,
  `attachment_filename` varchar(255) DEFAULT NULL,
  `attachment_size` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_dispute`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `contract_document`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `contract_draft`
--

CREATE TABLE `contract_draft` (
  `draft_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `quotation_id` int(11) DEFAULT NULL,
  `form_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`form_data`)),
  `current_step` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_invoices`
--

CREATE TABLE `contract_invoices` (
  `invoice_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `invoice_type` enum('milestone','upfront','final','time_material','adjustment') NOT NULL,
  `milestone_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `tax_percentage` decimal(5,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `payment_status` enum('pending','paid','overdue','cancelled','refunded') DEFAULT 'pending',
  `paid_date` datetime DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_receipt_url` varchar(500) DEFAULT NULL,
  `pdf_path` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_milestone`
--

CREATE TABLE `contract_milestone` (
  `milestone_id` int(11) NOT NULL,
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
  `completed_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `proof_files` text DEFAULT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_notifications`
--

CREATE TABLE `contract_notifications` (
  `notification_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `recipient_type` enum('company','customer') NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `notification_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_payment_history`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `contract_timeline`
--

CREATE TABLE `contract_timeline` (
  `timeline_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_description` text DEFAULT NULL,
  `actor_type` enum('system','company','customer') NOT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `actor_name` varchar(255) DEFAULT NULL,
  `metadata_json` text DEFAULT NULL,
  `is_milestone` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_time_logs`
--

CREATE TABLE `contract_time_logs` (
  `log_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_duration` decimal(3,2) DEFAULT 0.00,
  `total_hours` decimal(4,2) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `work_description` text NOT NULL,
  `work_location` varchar(255) DEFAULT NULL,
  `submitted_by` int(11) NOT NULL,
  `submitted_at` datetime NOT NULL,
  `customer_approved` tinyint(1) DEFAULT 0,
  `customer_approval_date` datetime DEFAULT NULL,
  `customer_rejection_reason` text DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `escrow_accounts`
--

CREATE TABLE `escrow_accounts` (
  `escrow_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `held_amount` decimal(10,2) DEFAULT 0.00,
  `released_amount` decimal(10,2) DEFAULT 0.00,
  `refunded_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('active','completed','refunded','disputed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `escrow_transaction`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `escrow_wallet`
--

CREATE TABLE `escrow_wallet` (
  `wallet_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `given_by` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comments` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financialreport`
--

CREATE TABLE `financialreport` (
  `report_id` int(11) NOT NULL,
  `month` date NOT NULL,
  `revenue` decimal(12,2) DEFAULT 0.00,
  `pending_withdrawals` decimal(12,2) DEFAULT 0.00,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `freelancer_assignments`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `issuereport`
--

CREATE TABLE `issuereport` (
  `issue_id` int(11) NOT NULL,
  `reportedBy_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `target_type` enum('user','repairer','company') NOT NULL,
  `description` text NOT NULL,
  `status` enum('open','investigating','resolved','closed') DEFAULT 'open',
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job`
--

CREATE TABLE `job` (
  `job_id` int(11) NOT NULL,
  `job_request_id` int(11) NOT NULL,
  `fixer_id` int(11) NOT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `completionDate` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobrequest`
--

CREATE TABLE `jobrequest` (
  `request_id` int(11) NOT NULL,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `photos` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `milestone`
--

CREATE TABLE `milestone` (
  `milestone_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `agreements` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed','overdue') DEFAULT 'pending',
  `submitted_by_company` tinyint(1) DEFAULT 0,
  `submitted_date` datetime DEFAULT NULL,
  `customer_approved` tinyint(1) DEFAULT 0,
  `customer_approval_date` datetime DEFAULT NULL,
  `customer_rejection_reason` text DEFAULT NULL,
  `work_started` tinyint(1) DEFAULT 0,
  `work_start_date` datetime DEFAULT NULL,
  `work_completed` tinyint(1) DEFAULT 0,
  `work_completion_date` datetime DEFAULT NULL,
  `customer_verification_requested` tinyint(1) DEFAULT 0,
  `customer_verified` tinyint(1) DEFAULT 0,
  `customer_verification_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `milestonepayment`
--

CREATE TABLE `milestonepayment` (
  `payment_id` int(11) NOT NULL,
  `milestone_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `method` enum('credit_card','debit_card','bank_transfer','check','cash') NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `moderator`
--

CREATE TABLE `moderator` (
  `moderator_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `assigned_section` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `moderatormsg`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `send_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `recipient_type` enum('user','repairer','company','all') NOT NULL,
  `status` enum('sent','pending','failed') DEFAULT 'pending',
  `created_by_id` int(11) DEFAULT NULL,
  `created_by_role` enum('admin','moderator','company','repairer','user') DEFAULT NULL,
  `created_by_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `job_request_id` int(11) NOT NULL,
  `paymentType` enum('credit_card','debit_card','cash','bank_transfer') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paymentDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `project_employee_assignments`
--

CREATE TABLE `project_employee_assignments` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotion`
--

CREATE TABLE `promotion` (
  `promotion_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `plan_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repairer`
--

CREATE TABLE `repairer` (
  `repairer_id` int(11) NOT NULL,
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
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repairerquote`
--

CREATE TABLE `repairerquote` (
  `quote_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `quoteAmount` decimal(10,2) NOT NULL,
  `estimatedDays` int(11) NOT NULL DEFAULT 1,
  `warrantyPeriod` int(11) DEFAULT 0,
  `validUntil` date NOT NULL,
  `materialsIncluded` tinyint(1) DEFAULT 1,
  `message` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected','expired') DEFAULT 'pending',
  `dateSubmitted` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repairer_applications`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `repairer_work_history`
--

CREATE TABLE `repairer_work_history` (
  `history_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `hours_worked` decimal(6,2) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT NULL,
  `attachments_json` text DEFAULT NULL,
  `source` enum('manual','platform') NOT NULL DEFAULT 'manual',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `report_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','investigating','resolved') DEFAULT 'pending',
  `resolve_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `service_provider_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comments` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staffsummary`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `staticcontent`
--

CREATE TABLE `staticcontent` (
  `content_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `body` text NOT NULL,
  `status` enum('Draft','Published') NOT NULL DEFAULT 'Published',
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `content_type` enum('terms','privacy','faq','about','help','contact','how_it_works','services','why_choose','support') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supportticket`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `support_attachments`
--

CREATE TABLE `support_attachments` (
  `attachment_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL COMMENT 'Size in bytes',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_categories`
--

CREATE TABLE `support_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_responses`
--

CREATE TABLE `support_responses` (
  `response_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `responder_type` enum('user','admin','moderator','system') NOT NULL,
  `responder_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_internal_note` tinyint(1) DEFAULT 0 COMMENT 'Notes only visible to admins',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `system_audit_log`
--

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

--
-- Dumping data for table `system_audit_log`
--

INSERT INTO `system_audit_log` (`audit_id`, `request_id`, `occurred_at`, `actor_user_id`, `actor_role`, `action`, `entity_type`, `entity_id`, `http_method`, `endpoint`, `ip_address`, `user_agent`, `status_code`, `details`) VALUES
(1, 'bff9006c-d5c6-458a-be93-6b48fdb467f2', '2026-04-09 17:46:22', 6, 'user', 'auth.logout', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"user\"}'),
(2, 'ae7b87ae-2b1f-4b59-9963-d14adbc7c3ff', '2026-04-09 17:46:44', NULL, NULL, 'auth.login_failed', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 401, '{\"email_hash\":\"48f9c221a2eab7f2192a6bb1967fe4cfdcfa047eea56d61c8bd48a956a2be379\"}'),
(3, 'c5874f71-e1f5-4217-beb5-d212f8bfc9e6', '2026-04-09 17:47:09', NULL, NULL, 'auth.login_failed', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 401, '{\"email_hash\":\"48f9c221a2eab7f2192a6bb1967fe4cfdcfa047eea56d61c8bd48a956a2be379\"}'),
(4, '28284a94-d024-4cb6-aca2-a0d4950483c3', '2026-04-09 17:48:06', 1, 'user', 'auth.register', 'user', '1', 'POST', '/2nd-Year-Group-Project/FixLanka/register', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 201, '{\"role\":\"user\"}');

--
-- Triggers `system_audit_log`
--
DELIMITER $$
CREATE TRIGGER `system_audit_log_no_delete` BEFORE DELETE ON `system_audit_log` FOR EACH ROW BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'system_audit_log is append-only (deletes are not allowed)';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `system_audit_log_no_update` BEFORE UPDATE ON `system_audit_log` FOR EACH ROW BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'system_audit_log is append-only (updates are not allowed)';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ticketmessage`
--

CREATE TABLE `ticketmessage` (
  `message_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('user','repairer','company','admin','moderator') NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `f_name` varchar(100) NOT NULL,
  `l_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(500) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `f_name`, `l_name`, `email`, `password`, `profile_picture`, `address`, `district`, `created_at`, `updated_at`, `is_deleted`) VALUES
(1, 'umesh', 'yapa', 'umeshyapa@gmail.com', '$2y$10$31r1kqzw2L1md/0EZe8yAeSv30bk7gkA808fsUeBGPBh8CzX20Er.', NULL, 'reid avenue, colombo 7', NULL, '2026-04-09 12:18:06', '2026-04-09 12:18:06', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

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

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`session_id`, `user_id`, `user_role`, `device_type`, `browser`, `os`, `ip_address`, `user_agent`, `last_activity`, `is_current`) VALUES
('kvms47qkg5egjd62q3s5ul2nea', 6, 'user', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-09 12:16:21', 1),
('v6gcucftu3rpj1p1f6l9elh09c', 1, 'user', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-10 15:02:26', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_moderation_log`
--
ALTER TABLE `account_moderation_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_account_lookup` (`account_type`,`account_id`,`created_at`),
  ADD KEY `idx_action_type` (`action_type`);

--
-- Indexes for table `activitylog`
--
ALTER TABLE `activitylog`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_timestamp` (`timestamp`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `adreport`
--
ALTER TABLE `adreport`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_ad` (`ad_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`);

--
-- Indexes for table `adrotationsettings`
--
ALTER TABLE `adrotationsettings`
  ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `adschedule`
--
ALTER TABLE `adschedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `idx_ad` (`ad_id`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

--
-- Indexes for table `advertisement`
--
ALTER TABLE `advertisement`
  ADD PRIMARY KEY (`ad_id`),
  ADD KEY `idx_provider` (`provider_id`,`provider_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

--
-- Indexes for table `billinghistory`
--
ALTER TABLE `billinghistory`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `idx_company_date` (`company_id`,`date`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `chatmessage`
--
ALTER TABLE `chatmessage`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_repairer` (`repairer_id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `registration_no` (`registration_no`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `companyjobpost`
--
ALTER TABLE `companyjobpost`
  ADD PRIMARY KEY (`posting_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `companyquotation`
--
ALTER TABLE `companyquotation`
  ADD PRIMARY KEY (`quotation_id`),
  ADD KEY `idx_request` (`request_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_budget_type` (`budget_type`),
  ADD KEY `idx_payment_method` (`payment_method`),
  ADD KEY `idx_pricing_type` (`pricing_type`),
  ADD KEY `idx_work_schedule` (`work_schedule_type`,`working_days_per_week`);

--
-- Indexes for table `companysettings`
--
ALTER TABLE `companysettings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `unique_company` (`company_id`);

--
-- Indexes for table `company_employees`
--
ALTER TABLE `company_employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `unique_company_repairer` (`company_id`,`repairer_id`),
  ADD KEY `repairer_id` (`repairer_id`);

--
-- Indexes for table `company_subscriptions`
--
ALTER TABLE `company_subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `contract`
--
ALTER TABLE `contract`
  ADD PRIMARY KEY (`contract_id`),
  ADD UNIQUE KEY `idx_contract_number` (`contract_number`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_quotation` (`quotation_id`),
  ADD KEY `idx_job_request` (`job_request_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `fk_contract_escrow` (`escrow_account_id`),
  ADD KEY `idx_undo_deadline` (`undo_deadline`),
  ADD KEY `idx_chat_active` (`chat_active`);

--
-- Indexes for table `contract_audit_log`
--
ALTER TABLE `contract_audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_contract` (`contract_id`),
  ADD KEY `idx_milestone` (`milestone_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_performed_by` (`performed_by`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `contract_budget_adjustments`
--
ALTER TABLE `contract_budget_adjustments`
  ADD PRIMARY KEY (`adjustment_id`),
  ADD KEY `idx_contract_status` (`contract_id`,`status`),
  ADD KEY `idx_requester` (`requested_by`,`requester_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `contract_chats`
--
ALTER TABLE `contract_chats`
  ADD PRIMARY KEY (`chat_id`),
  ADD KEY `idx_contract_sender` (`contract_id`,`sender_type`),
  ADD KEY `idx_unread` (`is_read`,`created_at`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `contract_dispute`
--
ALTER TABLE `contract_dispute`
  ADD PRIMARY KEY (`dispute_id`),
  ADD KEY `raised_by` (`raised_by`),
  ADD KEY `resolved_by` (`resolved_by`),
  ADD KEY `idx_contract_id` (`contract_id`),
  ADD KEY `idx_milestone_id` (`milestone_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `contract_document`
--
ALTER TABLE `contract_document`
  ADD PRIMARY KEY (`document_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_contract_id` (`contract_id`),
  ADD KEY `idx_milestone_id` (`milestone_id`),
  ADD KEY `idx_document_type` (`document_type`);

--
-- Indexes for table `contract_draft`
--
ALTER TABLE `contract_draft`
  ADD PRIMARY KEY (`draft_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_quotation` (`quotation_id`);

--
-- Indexes for table `contract_invoices`
--
ALTER TABLE `contract_invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `milestone_id` (`milestone_id`),
  ADD KEY `idx_contract_status` (`contract_id`,`payment_status`),
  ADD KEY `idx_invoice_number` (`invoice_number`),
  ADD KEY `idx_due_date` (`due_date`,`payment_status`),
  ADD KEY `idx_type` (`invoice_type`);

--
-- Indexes for table `contract_milestone`
--
ALTER TABLE `contract_milestone`
  ADD PRIMARY KEY (`milestone_id`),
  ADD KEY `idx_contract` (`contract_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_reviewed_by` (`reviewed_by`);

--
-- Indexes for table `contract_notifications`
--
ALTER TABLE `contract_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `contract_id` (`contract_id`),
  ADD KEY `idx_recipient` (`recipient_type`,`recipient_id`,`is_read`),
  ADD KEY `idx_type` (`notification_type`),
  ADD KEY `idx_priority` (`priority`,`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `contract_payment_history`
--
ALTER TABLE `contract_payment_history`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `paid_by` (`paid_by`),
  ADD KEY `paid_to` (`paid_to`),
  ADD KEY `idx_contract_id` (`contract_id`),
  ADD KEY `idx_milestone_id` (`milestone_id`),
  ADD KEY `idx_payment_type` (`payment_type`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `contract_timeline`
--
ALTER TABLE `contract_timeline`
  ADD PRIMARY KEY (`timeline_id`),
  ADD KEY `idx_contract_time` (`contract_id`,`created_at`),
  ADD KEY `idx_event_type` (`event_type`),
  ADD KEY `idx_actor` (`actor_type`,`actor_id`);

--
-- Indexes for table `contract_time_logs`
--
ALTER TABLE `contract_time_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_contract_date` (`contract_id`,`work_date`),
  ADD KEY `idx_approval` (`customer_approved`),
  ADD KEY `idx_invoice` (`invoice_id`);

--
-- Indexes for table `escrow_accounts`
--
ALTER TABLE `escrow_accounts`
  ADD PRIMARY KEY (`escrow_id`),
  ADD UNIQUE KEY `contract_id` (`contract_id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_account_number` (`account_number`);

--
-- Indexes for table `escrow_transaction`
--
ALTER TABLE `escrow_transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `idx_wallet` (`wallet_id`),
  ADD KEY `idx_contract` (`related_contract_id`);

--
-- Indexes for table `escrow_wallet`
--
ALTER TABLE `escrow_wallet`
  ADD PRIMARY KEY (`wallet_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `company_id` (`company_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `given_by` (`given_by`),
  ADD KEY `idx_project` (`project_id`);

--
-- Indexes for table `financialreport`
--
ALTER TABLE `financialreport`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_month` (`month`);

--
-- Indexes for table `freelancer_assignments`
--
ALTER TABLE `freelancer_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `repairer_id` (`repairer_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `contract_id` (`contract_id`);

--
-- Indexes for table `issuereport`
--
ALTER TABLE `issuereport`
  ADD PRIMARY KEY (`issue_id`),
  ADD KEY `idx_reporter` (`reportedBy_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `job`
--
ALTER TABLE `job`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `job_request_id` (`job_request_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_fixer` (`fixer_id`);

--
-- Indexes for table `jobrequest`
--
ALTER TABLE `jobrequest`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_district` (`district`);

--
-- Indexes for table `milestone`
--
ALTER TABLE `milestone`
  ADD PRIMARY KEY (`milestone_id`),
  ADD KEY `idx_contract` (`contract_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_workflow` (`customer_approved`,`work_started`,`work_completed`,`customer_verified`);

--
-- Indexes for table `milestonepayment`
--
ALTER TABLE `milestonepayment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_milestone` (`milestone_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `moderator`
--
ALTER TABLE `moderator`
  ADD PRIMARY KEY (`moderator_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `moderatormsg`
--
ALTER TABLE `moderatormsg`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `idx_repairer` (`repairer_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `idx_job_request` (`job_request_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`payment_method_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_primary` (`is_primary`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `project_employee_assignments`
--
ALTER TABLE `project_employee_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_project_employee` (`project_id`,`employee_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `promotion`
--
ALTER TABLE `promotion`
  ADD PRIMARY KEY (`promotion_id`),
  ADD KEY `idx_repairer` (`repairer_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

--
-- Indexes for table `repairer`
--
ALTER TABLE `repairer`
  ADD PRIMARY KEY (`repairer_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_ratings` (`ratings`);

--
-- Indexes for table `repairerquote`
--
ALTER TABLE `repairerquote`
  ADD PRIMARY KEY (`quote_id`),
  ADD KEY `idx_request` (`request_id`),
  ADD KEY `idx_repairer` (`repairer_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `repairer_applications`
--
ALTER TABLE `repairer_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD UNIQUE KEY `uniq_posting_repairer` (`job_posting_id`,`repairer_id`),
  ADD KEY `repairer_id` (`repairer_id`);

--
-- Indexes for table `repairer_work_history`
--
ALTER TABLE `repairer_work_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_repairer` (`repairer_id`),
  ADD KEY `idx_completed` (`completed_at`),
  ADD KEY `repairer_work_history_ibfk_2` (`category_id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `idx_job` (`job_id`),
  ADD KEY `idx_provider` (`service_provider_id`);

--
-- Indexes for table `staffsummary`
--
ALTER TABLE `staffsummary`
  ADD PRIMARY KEY (`staff_summary_id`),
  ADD UNIQUE KEY `unique_company_specialty` (`company_id`,`specialty`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_specialty` (`specialty`);

--
-- Indexes for table `staticcontent`
--
ALTER TABLE `staticcontent`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `idx_type` (`content_type`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `supportticket`
--
ALTER TABLE `supportticket`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `idx_user` (`user_id`,`user_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_project` (`project_id`);

--
-- Indexes for table `support_attachments`
--
ALTER TABLE `support_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `idx_ticket` (`ticket_id`);

--
-- Indexes for table `support_categories`
--
ALTER TABLE `support_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_slug` (`category_slug`);

--
-- Indexes for table `support_responses`
--
ALTER TABLE `support_responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `idx_ticket` (`ticket_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `idx_user` (`user_type`,`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `system_audit_log`
--
ALTER TABLE `system_audit_log`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `idx_audit_actor_time` (`actor_user_id`,`occurred_at`),
  ADD KEY `idx_audit_action_time` (`action`,`occurred_at`),
  ADD KEY `idx_audit_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_audit_request` (`request_id`);

--
-- Indexes for table `ticketmessage`
--
ALTER TABLE `ticketmessage`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_ticket` (`ticket_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_user` (`user_id`,`user_role`),
  ADD KEY `idx_last_activity` (`last_activity`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_moderation_log`
--
ALTER TABLE `account_moderation_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activitylog`
--
ALTER TABLE `activitylog`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `adreport`
--
ALTER TABLE `adreport`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `adrotationsettings`
--
ALTER TABLE `adrotationsettings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `adschedule`
--
ALTER TABLE `adschedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `advertisement`
--
ALTER TABLE `advertisement`
  MODIFY `ad_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chatmessage`
--
ALTER TABLE `chatmessage`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companyjobpost`
--
ALTER TABLE `companyjobpost`
  MODIFY `posting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companyquotation`
--
ALTER TABLE `companyquotation`
  MODIFY `quotation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companysettings`
--
ALTER TABLE `companysettings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_employees`
--
ALTER TABLE `company_employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_subscriptions`
--
ALTER TABLE `company_subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract`
--
ALTER TABLE `contract`
  MODIFY `contract_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_audit_log`
--
ALTER TABLE `contract_audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_budget_adjustments`
--
ALTER TABLE `contract_budget_adjustments`
  MODIFY `adjustment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_chats`
--
ALTER TABLE `contract_chats`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_dispute`
--
ALTER TABLE `contract_dispute`
  MODIFY `dispute_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_document`
--
ALTER TABLE `contract_document`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_draft`
--
ALTER TABLE `contract_draft`
  MODIFY `draft_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_invoices`
--
ALTER TABLE `contract_invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_milestone`
--
ALTER TABLE `contract_milestone`
  MODIFY `milestone_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_notifications`
--
ALTER TABLE `contract_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_payment_history`
--
ALTER TABLE `contract_payment_history`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_timeline`
--
ALTER TABLE `contract_timeline`
  MODIFY `timeline_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_time_logs`
--
ALTER TABLE `contract_time_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `escrow_accounts`
--
ALTER TABLE `escrow_accounts`
  MODIFY `escrow_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `escrow_transaction`
--
ALTER TABLE `escrow_transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `escrow_wallet`
--
ALTER TABLE `escrow_wallet`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financialreport`
--
ALTER TABLE `financialreport`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `freelancer_assignments`
--
ALTER TABLE `freelancer_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issuereport`
--
ALTER TABLE `issuereport`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job`
--
ALTER TABLE `job`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobrequest`
--
ALTER TABLE `jobrequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `milestone`
--
ALTER TABLE `milestone`
  MODIFY `milestone_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `milestonepayment`
--
ALTER TABLE `milestonepayment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `moderator`
--
ALTER TABLE `moderator`
  MODIFY `moderator_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `moderatormsg`
--
ALTER TABLE `moderatormsg`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_employee_assignments`
--
ALTER TABLE `project_employee_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotion`
--
ALTER TABLE `promotion`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairer`
--
ALTER TABLE `repairer`
  MODIFY `repairer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairerquote`
--
ALTER TABLE `repairerquote`
  MODIFY `quote_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairer_applications`
--
ALTER TABLE `repairer_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairer_work_history`
--
ALTER TABLE `repairer_work_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staffsummary`
--
ALTER TABLE `staffsummary`
  MODIFY `staff_summary_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staticcontent`
--
ALTER TABLE `staticcontent`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supportticket`
--
ALTER TABLE `supportticket`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_attachments`
--
ALTER TABLE `support_attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_categories`
--
ALTER TABLE `support_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_responses`
--
ALTER TABLE `support_responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_audit_log`
--
ALTER TABLE `system_audit_log`
  MODIFY `audit_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ticketmessage`
--
ALTER TABLE `ticketmessage`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adreport`
--
ALTER TABLE `adreport`
  ADD CONSTRAINT `adreport_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE;

--
-- Constraints for table `adschedule`
--
ALTER TABLE `adschedule`
  ADD CONSTRAINT `adschedule_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE;

--
-- Constraints for table `billinghistory`
--
ALTER TABLE `billinghistory`
  ADD CONSTRAINT `billinghistory_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

--
-- Constraints for table `chatmessage`
--
ALTER TABLE `chatmessage`
  ADD CONSTRAINT `chatmessage_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chatmessage_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;

--
-- Constraints for table `companyjobpost`
--
ALTER TABLE `companyjobpost`
  ADD CONSTRAINT `companyjobpost_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

--
-- Constraints for table `companyquotation`
--
ALTER TABLE `companyquotation`
  ADD CONSTRAINT `companyquotation_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `companyquotation_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `companysettings`
--
ALTER TABLE `companysettings`
  ADD CONSTRAINT `companysettings_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

--
-- Constraints for table `company_employees`
--
ALTER TABLE `company_employees`
  ADD CONSTRAINT `company_employees_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `company_employees_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;

--
-- Constraints for table `company_subscriptions`
--
ALTER TABLE `company_subscriptions`
  ADD CONSTRAINT `company_subscriptions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract`
--
ALTER TABLE `contract`
  ADD CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`),
  ADD CONSTRAINT `contract_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `contract_ibfk_4` FOREIGN KEY (`quotation_id`) REFERENCES `companyquotation` (`quotation_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contract_ibfk_5` FOREIGN KEY (`job_request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_contract_escrow` FOREIGN KEY (`escrow_account_id`) REFERENCES `escrow_accounts` (`escrow_id`) ON DELETE SET NULL;

--
-- Constraints for table `contract_audit_log`
--
ALTER TABLE `contract_audit_log`
  ADD CONSTRAINT `contract_audit_log_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_budget_adjustments`
--
ALTER TABLE `contract_budget_adjustments`
  ADD CONSTRAINT `contract_budget_adjustments_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_chats`
--
ALTER TABLE `contract_chats`
  ADD CONSTRAINT `contract_chats_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_dispute`
--
ALTER TABLE `contract_dispute`
  ADD CONSTRAINT `contract_dispute_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_dispute_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `contract_milestone` (`milestone_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_dispute_ibfk_3` FOREIGN KEY (`raised_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_dispute_ibfk_4` FOREIGN KEY (`resolved_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `contract_document`
--
ALTER TABLE `contract_document`
  ADD CONSTRAINT `contract_document_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_document_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `contract_milestone` (`milestone_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_document_ibfk_3` FOREIGN KEY (`uploaded_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_invoices`
--
ALTER TABLE `contract_invoices`
  ADD CONSTRAINT `contract_invoices_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_invoices_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `milestone` (`milestone_id`) ON DELETE SET NULL;

--
-- Constraints for table `contract_milestone`
--
ALTER TABLE `contract_milestone`
  ADD CONSTRAINT `contract_milestone_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_notifications`
--
ALTER TABLE `contract_notifications`
  ADD CONSTRAINT `contract_notifications_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_payment_history`
--
ALTER TABLE `contract_payment_history`
  ADD CONSTRAINT `contract_payment_history_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_payment_history_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `contract_milestone` (`milestone_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contract_payment_history_ibfk_3` FOREIGN KEY (`paid_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contract_payment_history_ibfk_4` FOREIGN KEY (`paid_to`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `contract_timeline`
--
ALTER TABLE `contract_timeline`
  ADD CONSTRAINT `contract_timeline_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_time_logs`
--
ALTER TABLE `contract_time_logs`
  ADD CONSTRAINT `contract_time_logs_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_timelog_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `contract_invoices` (`invoice_id`) ON DELETE SET NULL;

--
-- Constraints for table `escrow_accounts`
--
ALTER TABLE `escrow_accounts`
  ADD CONSTRAINT `escrow_accounts_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `escrow_transaction`
--
ALTER TABLE `escrow_transaction`
  ADD CONSTRAINT `escrow_txn_wallet_fk` FOREIGN KEY (`wallet_id`) REFERENCES `escrow_wallet` (`wallet_id`) ON DELETE CASCADE;

--
-- Constraints for table `escrow_wallet`
--
ALTER TABLE `escrow_wallet`
  ADD CONSTRAINT `escrow_wallet_company_fk` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`),
  ADD CONSTRAINT `escrow_wallet_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`given_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `freelancer_assignments`
--
ALTER TABLE `freelancer_assignments`
  ADD CONSTRAINT `freelancer_assignments_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `freelancer_assignments_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `freelancer_assignments_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `freelancer_assignments_ibfk_4` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE SET NULL;

--
-- Constraints for table `issuereport`
--
ALTER TABLE `issuereport`
  ADD CONSTRAINT `issuereport_ibfk_1` FOREIGN KEY (`reportedBy_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `job`
--
ALTER TABLE `job`
  ADD CONSTRAINT `job_ibfk_1` FOREIGN KEY (`job_request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_ibfk_2` FOREIGN KEY (`fixer_id`) REFERENCES `repairer` (`repairer_id`);

--
-- Constraints for table `jobrequest`
--
ALTER TABLE `jobrequest`
  ADD CONSTRAINT `jobrequest_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jobrequest_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Constraints for table `milestone`
--
ALTER TABLE `milestone`
  ADD CONSTRAINT `milestone_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `milestonepayment`
--
ALTER TABLE `milestonepayment`
  ADD CONSTRAINT `milestonepayment_ibfk_1` FOREIGN KEY (`milestone_id`) REFERENCES `milestone` (`milestone_id`) ON DELETE CASCADE;

--
-- Constraints for table `moderatormsg`
--
ALTER TABLE `moderatormsg`
  ADD CONSTRAINT `moderatormsg_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`job_request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `payment_methods_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `project_employee_assignments`
--
ALTER TABLE `project_employee_assignments`
  ADD CONSTRAINT `project_employee_assignments_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_employee_assignments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `company_employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `promotion`
--
ALTER TABLE `promotion`
  ADD CONSTRAINT `promotion_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;

--
-- Constraints for table `repairer`
--
ALTER TABLE `repairer`
  ADD CONSTRAINT `repairer_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `repairerquote`
--
ALTER TABLE `repairerquote`
  ADD CONSTRAINT `repairerquote_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repairerquote_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;

--
-- Constraints for table `repairer_applications`
--
ALTER TABLE `repairer_applications`
  ADD CONSTRAINT `repairer_applications_ibfk_1` FOREIGN KEY (`job_posting_id`) REFERENCES `companyjobpost` (`posting_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repairer_applications_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;

--
-- Constraints for table `repairer_work_history`
--
ALTER TABLE `repairer_work_history`
  ADD CONSTRAINT `repairer_work_history_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repairer_work_history_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `job` (`job_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`service_provider_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;

--
-- Constraints for table `staffsummary`
--
ALTER TABLE `staffsummary`
  ADD CONSTRAINT `staffsummary_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

--
-- Constraints for table `supportticket`
--
ALTER TABLE `supportticket`
  ADD CONSTRAINT `supportticket_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE SET NULL;

--
-- Constraints for table `support_attachments`
--
ALTER TABLE `support_attachments`
  ADD CONSTRAINT `support_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `support_responses`
--
ALTER TABLE `support_responses`
  ADD CONSTRAINT `support_responses_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `ticketmessage`
--
ALTER TABLE `ticketmessage`
  ADD CONSTRAINT `ticketmessage_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `supportticket` (`ticket_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
