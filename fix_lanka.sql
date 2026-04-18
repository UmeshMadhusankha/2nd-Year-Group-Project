-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 10, 2026 at 08:33 PM
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
-- Table structure for table `account_moderation_cases`
--

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

--
-- Dumping data for table `account_moderation_cases`
--

INSERT INTO `account_moderation_cases` (`case_id`, `target_id`, `target_type`, `admin_username`, `action_type`, `reason`, `notes`, `duration_days`, `start_date`, `end_date`, `status_before`, `status_after`, `is_permanent`, `created_at`, `updated_at`) VALUES
(2, 2, 'User', 'admin', 'SUSPEND', 'Community guideline violations', NULL, 14, '2026-02-13 11:41:22', '2026-02-27 11:41:22', 'ACTIVE', 'SUSPENDED', 0, '2026-02-13 06:11:22', '2026-02-13 06:11:22'),
(3, 3, 'User', 'admin', 'SUSPEND', 'Spam activity', NULL, 3, '2026-02-13 11:41:22', '2026-02-16 11:41:22', 'ACTIVE', 'SUSPENDED', 0, '2026-02-13 06:11:22', '2026-02-13 06:11:22'),
(4, 1, 'Company', 'admin', 'BAN', 'Fraudulent business operations', NULL, NULL, '2026-02-13 11:41:22', NULL, 'ACTIVE', 'BANNED', 1, '2026-02-13 06:11:22', '2026-02-13 06:11:22');

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

--
-- Dumping data for table `account_moderation_log`
--

INSERT INTO `account_moderation_log` (`log_id`, `account_type`, `account_id`, `action_type`, `reason`, `notes`, `suspended_until`, `acted_by_role`, `acted_by_id`, `created_at`) VALUES
(1, 'company', 9998, 'restore', NULL, NULL, NULL, 'admin', NULL, '2026-04-08 11:14:23'),
(2, 'company', 9998, 'suspend', 'They are black and they are ugly', 'hee', '2026-04-09 13:14:51', 'admin', NULL, '2026-04-08 11:14:51');

-- --------------------------------------------------------

--
-- Table structure for table `account_moderation_status`
--

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

--
-- Dumping data for table `account_moderation_status`
--

INSERT INTO `account_moderation_status` (`status_id`, `account_id`, `account_type`, `account_status`, `banned_permanent`, `suspended_until`, `moderation_reason`, `last_updated`, `updated_by`) VALUES
(1, 100, 'Repairer', 'SUSPENDED', 0, '2026-02-17 00:51:54', 'Customer complaints received', '2026-02-09 19:21:54', 'admin'),
(2, 100, 'Company', 'BANNED', 1, NULL, 'Terms of service violation', '2026-02-09 19:21:54', 'admin'),
(3, 2, 'User', 'SUSPENDED', 0, '2026-02-27 11:41:22', 'Repeated violations of community guidelines', '2026-02-13 06:11:22', 'admin'),
(4, 3, 'User', 'SUSPENDED', 0, '2026-02-16 11:41:22', 'Spam reporting', '2026-02-13 06:11:22', 'admin'),
(5, 1, 'Company', 'BANNED', 1, NULL, 'Fraudulent business practices detected', '2026-02-13 06:11:22', 'admin');

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

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username`, `password`, `email`, `created_at`) VALUES
('admin', '$2y$10$KUm9be7qV1jVqPTXgnvqwuQaolTUdmHvowXtKKUGWy.vUnsZUg.pG', 'admin@fixlanka.com', '2026-01-03 09:19:02');

-- --------------------------------------------------------

--
-- Table structure for table `adminalert`
--

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

--
-- Dumping data for table `adminalert`
--

INSERT INTO `adminalert` (`alert_id`, `title`, `message`, `target_role`, `status`, `priority`, `created_by`, `created_at`, `updated_at`) VALUES
(22, 'New Feature Announcement', 'We\'re excited to announce a new feature: [FEATURE_NAME]. Check it out in your dashboard!', 'All', 'active', 'medium', 'admin', '2026-02-19 18:44:34', '2026-02-19 18:44:34'),
(23, 'Welcome Message', 'Welcome to FixLanka! Get started by completing your profile and exploring our services.', 'All', 'active', 'low', 'admin', '2026-02-19 18:44:38', '2026-02-19 18:44:38');

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

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

--
-- Dumping data for table `admin_notifications`
--

INSERT INTO `admin_notifications` (`notification_id`, `admin_username`, `notification_type`, `title`, `message`, `account_id`, `account_type`, `is_read`, `created_at`) VALUES
(1, 'admin', '', 'User Suspended', 'User Jane Smith (ID: 2) has been suspended for 14 days', 2, 'User', 0, '2026-02-13 06:11:22'),
(2, 'admin', '', 'User Suspended', 'User Bob Johnson (ID: 3) has been suspended for 3 days', 3, 'User', 0, '2026-02-13 06:11:22'),
(3, 'admin', '', 'Company Banned', 'Company BuildPro Solutions (ID: 1) has been permanently banned', 1, 'Company', 0, '2026-02-13 06:11:22');

-- --------------------------------------------------------

--
-- Table structure for table `admin_override_history`
--

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
(1, 30, 60, 90, 5, 3, 8, NULL, '2026-04-03 10:03:58');

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

--
-- Dumping data for table `adschedule`
--

INSERT INTO `adschedule` (`schedule_id`, `ad_id`, `start_date`, `end_date`, `start_time`, `end_time`) VALUES
(1, 22, '2026-04-01', '2026-04-04', '17:00:00', '18:59:00'),
(2, 23, '2026-04-01', '2026-04-08', '21:00:00', '23:59:00');

-- --------------------------------------------------------

--
-- Table structure for table `advertisement`
--

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

--
-- Dumping data for table `advertisement`
--

INSERT INTO `advertisement` (`ad_id`, `provider_id`, `provider_type`, `contact_email`, `contact_phone`, `title`, `description`, `category_id`, `type`, `budget`, `image_url`, `target_audience`, `start_date`, `end_date`, `status`, `submission_date`, `clicks`, `impressions`, `reviewed_by`, `reviewed_at`, `moderator_notes`, `admin_reviewed_by`, `admin_reviewed_at`, `admin_notes`, `override_reason`, `is_override`) VALUES
(22, 9997, 'company', NULL, NULL, 'My Signature', 'This is my signature', 1, 'banner', 4900.00, NULL, 'all', '2026-04-01', '2026-04-04', 'expired', '2026-04-01 10:20:05', 0, 0, 1, '2026-04-02 13:26:57', '', NULL, NULL, NULL, NULL, 0),
(23, 9997, 'company', NULL, NULL, 'Login Page we made', 'You can see what we are making', 1, 'banner', 4900.00, '/2nd-Year-Group-Project/FixLanka/uploads/advertisements/1775040838_Body.png', 'all', '2026-04-01', '2026-04-08', 'expired', '2026-04-01 10:53:58', 0, 0, 1, '2026-04-01 10:54:38', '', NULL, NULL, NULL, NULL, 0),
(24, 9997, 'company', NULL, NULL, 'ajhvcvbzmcv zmnc', 'ajvacjkckajsvckjavkcjvskjcvakvckavfckjvck', 1, 'banner', 3500.00, '/2nd-Year-Group-Project/FixLanka/uploads/advertisements/1775068392_WhatsApp Image 2026-02-06 at 1.04.07 PM.jpeg', 'all', '2026-04-04', '2026-04-10', 'approved', '2026-04-01 18:33:12', 0, 0, 1, '2026-04-02 13:26:44', '', NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `ad_reports`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `ad_schedules`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `ad_status_history`
--

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

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `name`, `created_at`) VALUES
(1, 'Plumbing', '2026-01-03 09:19:02'),
(2, 'Electrical', '2026-01-03 09:19:02'),
(3, 'HVAC', '2026-01-03 09:19:02'),
(4, 'Cleaning', '2026-01-03 09:19:02'),
(5, 'Carpentry', '2026-01-03 09:19:02'),
(6, 'Painting', '2026-01-03 09:19:02'),
(7, 'Appliance Repair', '2026-01-03 09:19:02'),
(8, 'Roofing', '2026-01-03 09:19:02'),
(9, 'Landscaping', '2026-01-03 09:19:02'),
(10, 'Pest Control', '2026-01-03 09:19:02'),
(11, 'Home Security', '2026-01-03 09:19:02'),
(12, 'Interior Design', '2026-01-03 09:19:02'),
(13, 'Flooring', '2026-01-03 09:19:02'),
(14, 'Masonry', '2026-01-03 09:19:02'),
(15, 'Welding', '2026-01-03 09:19:02'),
(16, 'Glass & Mirror', '2026-01-03 09:19:02'),
(17, 'Tile Work', '2026-01-03 09:19:02'),
(18, 'Drywall', '2026-01-03 09:19:02'),
(19, 'Insulation', '2026-01-03 09:19:02'),
(20, 'Window Installation', '2026-01-03 09:19:02'),
(21, 'Hotel Service', '2026-04-07 07:16:09');

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
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`company_id`, `name`, `business_type`, `registration_no`, `tax_id`, `location_id`, `address`, `email`, `website`, `contact_no`, `districts`, `password`, `description`, `rating`, `date_of_joined`, `is_deleted`) VALUES
(9996, 'Clip Craft', 'Carpentry', '1234', '', NULL, 'aihiuabicubaic', 'company@gmail.com', '', '0789242696', 'Colombo,Kalutara', '$2y$10$kAgHXAmcmPBrrMWj9UK1GuA43PxxUF.8kl2yXQ52xtMpff2iJj3Fy', 'fklgasdgiagfioqdf', 0.00, '2026-03-04 07:59:20', 0),
(9997, 'UCSC', 'Pest Control', '45678', '', 1, '', 'ucsc@gmail.com', '', '0712345678', NULL, '$2y$10$u2BENEdMnE9cINpDyvLUve7FqE..4iR21ie4OGjYrHlqcmaZoNLU2', 'aiuacigaciubacvjkabcjbcjbjcbajbcjsabvc', 0.00, '2026-03-04 12:18:40', 0),
(9998, 'Test Company', 'Plumbing,Cleaning', 'REG12345', '', 5, '', 'Password123!testcompany@fixlanka.com', '', '0112345678', NULL, '$2y$10$uDKQntBsubbfs.vJpG0ZVenZiZIRxVeMJRamvCWpAdBmJ1yXvi3om', 'We provide top-notch plumbing services.', 0.00, '2026-03-08 07:54:27', 1);

-- --------------------------------------------------------

--
-- Table structure for table `companyemployee`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `companyjobpost`
--

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

--
-- Dumping data for table `companyjobpost`
--

INSERT INTO `companyjobpost` (`posting_id`, `company_id`, `title`, `category`, `employment_type`, `related_project_id`, `description`, `min_experience`, `priority_level`, `min_budget`, `max_budget`, `application_deadline`, `required_skills`, `location`, `location_requirements`, `status`, `notify_repairers`, `allow_direct_applications`, `created_by`, `posted_date`, `updated_at`, `closed_date`) VALUES
(1, 9997, 'Student Engineer', 'hvac', 'freelance', NULL, 'Need to be cool', 'entry', 'medium', 3000.00, 4000.00, '2026-04-11', 'xnfdjtgxxxfgth', 'colombo', 'colombo', 'open', 1, 1, NULL, '2026-04-06 12:22:41', '2026-04-06 12:22:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `companyquotation`
--

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
  `status` enum('pending','accepted','rejected','successful') DEFAULT 'pending',
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

--
-- Dumping data for table `companyquotation`
--

INSERT INTO `companyquotation` (`quotation_id`, `request_id`, `company_id`, `user_id`, `title`, `description`, `labor_cost`, `material_cost`, `transport_cost`, `other_charges`, `total_amount`, `start_date`, `completion_date`, `estimated_duration`, `work_schedule_type`, `working_days_per_week`, `daily_work_hours`, `work_start_time`, `work_end_time`, `break_duration`, `custom_schedule_json`, `public_holidays_excluded`, `estimated_calendar_days`, `custom_schedule_details`, `total_work_hours`, `overtime_available`, `overtime_rate`, `payment_terms`, `warranty_period`, `labor_unit_label`, `material_unit_label`, `additional_terms`, `status`, `created_at`, `updated_at`, `budget_type`, `budget_min`, `budget_max`, `payment_method`, `pricing_type`, `hourly_rate`, `spending_cap_multiplier`) VALUES
(46, 39, 9997, 9996, 'Need To Paint My House', 'Based on your request:\n\nI need to fully colorash my house and also the roof\n\nWe will provide the following services: We will fully colorwash your house with aftercare\n', 500.00, 250.00, 0.00, 0.00, 750.00, '2026-04-13', '2026-04-20', 0, 'weekdays_only', 5, 8.00, '08:00:00', '17:00:00', NULL, NULL, 1, NULL, NULL, NULL, 0, NULL, 'Milestone-based Payment - Payment released at project milestones', '6_months', NULL, NULL, '', 'accepted', '2026-04-10 14:55:53', '2026-04-10 16:40:24', 'fixed', NULL, NULL, 'milestone_based', 'fixed_price', NULL, 1.50);

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

--
-- Dumping data for table `company_employees`
--

INSERT INTO `company_employees` (`employee_id`, `company_id`, `repairer_id`, `job_title`, `employment_type`, `status`, `hired_date`, `hourly_rate`, `notes`, `created_at`, `updated_at`) VALUES
(3, 9997, 107, 'Freelancer', 'freelance', 'inactive', '2026-04-06', 3500.00, 'Offboard reason: You are dull', '2026-04-06 12:31:43', '2026-04-08 08:09:15');

-- --------------------------------------------------------

--
-- Table structure for table `company_jobpost`
--

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

--
-- Dumping data for table `company_jobpost`
--

INSERT INTO `company_jobpost` (`posting_id`, `company_id`, `title`, `category`, `category_id`, `employment_type`, `description`, `requirements`, `min_experience`, `min_budget`, `max_budget`, `location`, `location_id`, `status`, `created_at`, `updated_at`) VALUES
(3, 9997, 'Senior Cleaner', 'roofing', NULL, 'freelance', 'Need to clean roofs', NULL, 0, 2000.00, 2500.00, 'colombo', NULL, 'open', '2026-04-06 06:30:38', '2026-04-06 06:30:38'),
(4, 9997, 'Student Engineer', 'electrical', NULL, 'freelance', 'Need an electrical engineer for build a house', NULL, 1, 5000.00, 6000.00, 'colombo', NULL, 'open', '2026-04-06 10:58:13', '2026-04-06 10:58:13');

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

--
-- Dumping data for table `company_subscriptions`
--

INSERT INTO `company_subscriptions` (`subscription_id`, `company_id`, `plan_name`, `plan_price`, `billing_period`, `status`, `start_date`, `end_date`, `next_billing_date`, `auto_renew`, `trial_ends_at`, `created_at`, `updated_at`) VALUES
(1, 9997, 'free', 0.00, 'monthly', 'trial', '2026-04-01', NULL, '2026-04-15', 1, '2026-04-15', '2026-04-01 13:05:13', '2026-04-01 13:05:13');

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `contract_audit_log`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `contract_change_requests`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `contract_chats`
--

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
  `actual_amount` decimal(12,2) DEFAULT NULL COMMENT 'actual_quantity * unit_rate',
  `is_non_paying` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 if this milestone is non-paying (inspection / no measurable units)',
  `actual_labor_quantity` decimal(10,2) DEFAULT NULL COMMENT 'Actual labour units submitted by company',
  `actual_material_quantity` decimal(10,2) DEFAULT NULL COMMENT 'Actual material units submitted by company',
  `actual_material_unit_rate` decimal(12,2) DEFAULT NULL COMMENT 'Actual material unit rate submitted by company (optional variation)',
  `actual_extra_amount` decimal(12,2) DEFAULT NULL COMMENT 'Additional amount outside labour/material for this milestone'
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
  `notification_type` varchar(50) NOT NULL COMMENT 'undo_reminder, milestone_approved, payment_received, chat_message, etc.',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `action_url` varchar(500) DEFAULT NULL COMMENT 'URL to navigate when notification clicked',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores notifications for contract events';

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

-- --------------------------------------------------------

--
-- Table structure for table `directjobrequest`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `directrequestquotes`
--

CREATE TABLE `directrequestquotes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `provider_type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `escrow_accounts`
--

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

--
-- Dumping data for table `escrow_transaction`
--

INSERT INTO `escrow_transaction` (`transaction_id`, `wallet_id`, `amount`, `type`, `status`, `related_contract_id`, `related_milestone_id`, `description`, `created_at`) VALUES
(20, 15, 150000.00, 'deposit', 'completed', NULL, NULL, 'Initial deposit for Contract #36', '2026-04-10 12:21:32'),
(21, 15, -150000.00, 'hold', 'completed', NULL, 33, 'Held for Milestone #33', '2026-04-10 12:21:32'),
(22, 15, 249000.00, 'deposit', 'completed', NULL, NULL, 'Initial deposit for Contract #35', '2026-04-10 12:24:41'),
(23, 15, -249000.00, 'hold', 'completed', NULL, 30, 'Held for Milestone #30', '2026-04-10 12:24:41');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `escrow_wallet`
--

INSERT INTO `escrow_wallet` (`wallet_id`, `user_id`, `company_id`, `balance`, `created_at`, `updated_at`) VALUES
(15, 9996, NULL, 0.00, '2026-04-10 12:21:32', '2026-04-10 12:24:41');

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
  `active_subscriptions` int(11) DEFAULT 0,
  `generated_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financialreport`
--

INSERT INTO `financialreport` (`report_id`, `month`, `active_subscriptions`, `generated_by`, `notes`, `generated_at`, `updated_at`) VALUES
(1, '2025-12-01', 145, NULL, 'December 2025 Monthly Report', '2026-01-03 09:19:02', '2026-01-03 09:19:02'),
(5, '2026-02-01', 48, 1, NULL, '2026-02-10 16:20:20', '2026-02-10 16:20:20');

-- --------------------------------------------------------

--
-- Table structure for table `financialreporttransaction`
--

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

--
-- Dumping data for table `financialreporttransaction`
--

INSERT INTO `financialreporttransaction` (`transaction_id`, `report_id`, `transaction_type`, `amount`, `description`, `reference_number`, `reference_id`, `reference_type`, `status`, `transaction_date`, `created_at`) VALUES
(1, 1, 'Payment', 15000.00, 'Plumbing service', NULL, 1, 'JobRequest', 'Completed', '2026-01-01 09:19:02', '2026-01-03 09:19:02'),
(2, 1, 'Commission', 2250.00, 'Commission - Job #1', NULL, 1, 'Payment', 'Completed', '2026-01-01 09:19:02', '2026-01-03 09:19:02'),
(3, 1, 'Subscription', 5000.00, 'Repairer subscription', NULL, 1, 'Repairer', 'Completed', '2026-01-02 09:19:02', '2026-01-03 09:19:02'),
(4, 1, 'Advertisement', 28000.00, 'Sponsored ad', NULL, 1, 'Advertisement', 'Completed', '2025-12-27 09:19:02', '2026-01-03 09:19:02'),
(5, 5, 'Payment', 150000.00, 'Plumbing service', 'TXN-2024-10-001', NULL, NULL, 'Completed', '2026-01-01 18:30:00', '2026-02-10 16:20:20'),
(6, 5, 'Commission', 22500.00, 'Commission - Job #1', 'TXN-2024-10-002', NULL, NULL, 'Completed', '2026-01-01 18:30:00', '2026-02-10 16:20:20'),
(7, 5, 'Subscription', 5000.00, 'Repairer subscription', 'TXN-2024-10-003', NULL, NULL, 'Completed', '2026-01-02 18:30:00', '2026-02-10 16:20:20'),
(8, 5, 'Advertisement', 280000.00, 'Sponsored ad', 'TXN-2024-10-004', NULL, NULL, 'Completed', '2025-12-27 18:30:00', '2026-02-10 16:20:20'),
(9, 5, 'Payment', 85000.00, 'Electrical repair', 'TXN-2024-10-005', NULL, NULL, 'Completed', '2026-01-04 18:30:00', '2026-02-10 16:20:20'),
(10, 5, 'Withdrawal', 25000.00, 'Withdrawal request', 'TXN-2024-10-006', NULL, NULL, 'Pending', '2026-01-05 18:30:00', '2026-02-10 16:20:20'),
(11, 5, 'Commission', 12750.00, 'Commission - Job #2', 'TXN-2024-10-007', NULL, NULL, 'Completed', '2026-01-06 18:30:00', '2026-02-10 16:20:20'),
(12, 5, 'Payment', 120000.00, 'Painting service', 'TXN-2024-10-008', NULL, NULL, 'Completed', '2026-01-07 18:30:00', '2026-02-10 16:20:20'),
(13, 5, 'Payment', 200000.00, 'Kitchen renovation', 'TXN-2024-10-009', NULL, NULL, 'Completed', '2026-01-09 18:30:00', '2026-02-10 16:20:20'),
(14, 5, 'Commission', 30000.00, 'Commission - Job #3', 'TXN-2024-10-010', NULL, NULL, 'Completed', '2026-01-09 18:30:00', '2026-02-10 16:20:20'),
(15, 5, 'Advertisement', 180000.00, 'Premium ad', 'TXN-2024-10-011', NULL, NULL, 'Completed', '2026-01-11 18:30:00', '2026-02-10 16:20:20'),
(16, 5, 'Subscription', 5000.00, 'Company subscription', 'TXN-2024-10-012', NULL, NULL, 'Completed', '2026-01-14 18:30:00', '2026-02-10 16:20:20'),
(17, 5, 'Withdrawal', 180000.00, 'Company withdrawal', 'TXN-2024-10-013', NULL, NULL, 'Pending', '2026-01-17 18:30:00', '2026-02-10 16:20:20'),
(18, 5, 'Payment', 750000.00, 'Commercial plumbing', 'TXN-2024-10-014', NULL, NULL, 'Completed', '2026-01-19 18:30:00', '2026-02-10 16:20:20'),
(19, 5, 'Payment', 135000.00, 'HVAC installation', 'TXN-2024-10-015', NULL, NULL, 'Completed', '2026-01-21 18:30:00', '2026-02-10 16:20:20');

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
-- Table structure for table `issuenotification`
--

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
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('pending','investigating','resolved','escalated','closed') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_internal_notes` text DEFAULT NULL COMMENT 'Internal admin notes - not visible to users'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issuereport`
--

INSERT INTO `issuereport` (`issue_id`, `reportedBy_id`, `target_id`, `target_type`, `description`, `priority`, `status`, `admin_notes`, `date`, `updated_at`, `admin_internal_notes`) VALUES
(15, 9997, 107, 'repairer', 'Issue about notification: Employment ended\n\nNotification Details:\n- Title: Employment ended\n- Created by: UCSC\n- Time: 4/8/2026, 1:39:16 PM\n\nOriginal message:\nUCSC has ended your employment. Reason: You are dull\n\nIssue:jbbjbjkb', 'medium', 'pending', NULL, '2026-04-08 10:09:30', '2026-04-08 10:09:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `issuestatushistory`
--

CREATE TABLE `issuestatushistory` (
  `history_id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `old_status` enum('pending','investigating','resolved','escalated','closed') NOT NULL,
  `new_status` enum('pending','investigating','resolved','escalated','closed') NOT NULL,
  `changed_by` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
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
  `location_id` int(11) DEFAULT NULL,
  `district` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `service_provider_type` varchar(50) NOT NULL,
  `urgency` enum('medium','urgent') DEFAULT 'medium',
  `finish_date` date NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `photos` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobrequest`
--

INSERT INTO `jobrequest` (`request_id`, `user_id`, `category_id`, `title`, `description`, `status`, `location_id`, `district`, `address`, `service_provider_type`, `urgency`, `finish_date`, `dateCreated`, `photos`) VALUES
(39, 9996, 6, 'Need To Paint My House', 'I need to fully colorash my house and also the roof', 'accepted', 11, '', '', 'company', 'medium', '2026-04-20', '2026-04-10 14:54:32', NULL);

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

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`location_id`, `address`, `district`, `created_at`) VALUES
(1, 'Colombo 07', 'Colombo', '2026-03-04 12:18:40'),
(2, 'Manakkulama', NULL, '2026-03-04 12:24:24'),
(3, 'ghkkkjk', NULL, '2026-03-04 12:25:08'),
(4, 'ghkkkjk', NULL, '2026-03-04 12:25:35'),
(5, '123 Company St, Colombo', 'Colombo', '2026-03-08 07:54:27'),
(6, 'akabkbskjbkjsbc', 'Kilinochchi', '2026-04-03 20:16:14'),
(7, 'colombo 07', 'Colombo', '2026-04-10 08:57:21'),
(8, 'Kaluthara 07', 'Kalutara', '2026-04-10 11:14:17'),
(9, 'esgsg', 'Mullaitivu', '2026-04-10 11:26:38'),
(10, 'dvmjbgzucg', 'Batticaloa', '2026-04-10 12:25:52'),
(11, 'address2', 'Colombo', '2026-04-10 14:54:32');

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

-- --------------------------------------------------------

--
-- Table structure for table `milestonepayment`
--

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

--
-- Dumping data for table `milestonepayment`
--

INSERT INTO `milestonepayment` (`payment_id`, `contract_id`, `milestone_id`, `payment_type`, `amount`, `payment_date`, `method`, `status`, `payment_percentage`, `description`, `escrow_enabled`, `paid_to_escrow_at`, `escrow_released_at`, `escrow_refunded_at`, `transaction_id`, `paid_by`, `paid_to`, `paid_at`, `created_at`) VALUES
(1, 3, NULL, 'milestone', 500.00, '2026-02-14 16:32:46', 'credit_card', '', 50.00, 'Test Escrow Payment', 1, '2026-02-14 22:02:46', NULL, NULL, 'TXN_1771086766', 1, 2, NULL, '2026-02-14 22:02:46'),
(2, 3, NULL, 'milestone', 500.00, '2026-02-14 16:33:33', 'credit_card', 'released', 50.00, 'Test Escrow Payment', 1, '2026-02-14 22:03:33', '2026-02-14 22:03:33', NULL, 'TXN_1771086813', 1, 2, NULL, '2026-02-14 22:03:33');

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
  `status` enum('active','suspended') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `moderator`
--

INSERT INTO `moderator` (`moderator_id`, `username`, `password`, `email`, `assigned_section`, `status`, `created_at`, `deleted_at`, `last_login`) VALUES
(1, 'moderator1', '$2y$10$Y46plNaUtsL3opLdRcdbO.yN76rIrDi5TpvT2XsL/bJAiSYfyZhCG', 'mod1@fixlanka.com', 'User Reports', '', '2026-02-01 16:52:39', NULL, NULL),
(9, 'Ama', '$2y$10$kq0p2oMk1g8KyADoycHm2e8lvXrT9jJA3I4j23IF61tEKU7c17rGC', 'ama002@gmail.com', 'Financial Reports', 'active', '2026-01-18 14:29:36', NULL, NULL),
(18, 'Selina_Cruize', '$2y$12$ZF/3FX1rwKJvTYDqdDPFse6ESHPQf0jTBKZv.BLCoi7GGho//SoMm', 'selinacruize07@gmail.com', 'Content Moderation', 'active', '2026-02-17 17:24:42', NULL, NULL),
(19, 'Vindya_Gomez', '$2y$12$pz9oC.WDjZApDLzjuiAN3OM44Beq7AwvMayU8wcSyouv4edJe2N.y', 'vindyagomez05@gmail.com', 'Content Moderation', 'active', '2026-02-17 17:25:50', NULL, NULL),
(20, 'Don_Ahamed', '$2y$12$Y8MpAODLOQ.NcjneBsGdweMgfXb9LtYUv/QcSpcu6xssGezXXP5B.', 'donahamed002@gmail.com', 'User Reports', 'active', '2026-02-17 17:37:31', NULL, NULL),
(21, 'Amal', '$2y$12$SpioSbVj/8dRJq22Gits6OJdIytzKLuhMB.4B3mdsv/ucZS4ZBXEW', 'amal04@gmail.com', 'Content Moderation', 'active', '2026-02-18 06:18:58', NULL, NULL),
(24, 'testmod2', '$2y$12$nTnhG3Eyz/yo/t20wtemqesOS8yTc8aV2atS9hQe4WLxlHdCnO01y', 'testmod1_updated@fixlanka.com', 'Content Moderation', '', '2026-02-18 15:34:34', NULL, NULL),
(25, 'Nimali_Herath', '$2y$12$QWCqA3k/VHmjCVS1F2/ij.PZgSPfvn8jQQ6.vpgvsb8VwzrMbMhHK', 'nimaliherath00@gmail.com', 'Financial Reports', 'active', '2026-02-18 16:39:58', NULL, NULL);

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
-- Table structure for table `moderator_activity`
--

CREATE TABLE `moderator_activity` (
  `activity_id` int(11) NOT NULL,
  `moderator_id` int(11) NOT NULL DEFAULT 1,
  `activity_type` enum('ad_approved','ad_rejected','ad_activated','user_banned','content_updated','payment_verified','user_registered','report_resolved') NOT NULL,
  `target_id` int(11) NOT NULL,
  `target_title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `moderator_activity`
--

INSERT INTO `moderator_activity` (`activity_id`, `moderator_id`, `activity_type`, `target_id`, `target_title`, `description`, `created_at`) VALUES
(1, 1, 'ad_approved', 1, 'Door & Window Installation', 'Advertisement approved', '2026-01-03 09:16:02'),
(2, 1, 'payment_verified', 1, 'Payment LKR 15,000', 'Payment verified', '2026-01-01 09:19:02'),
(3, 1, 'user_registered', 1, 'New user account', 'User registered', '2025-12-29 09:19:02'),
(4, 1, 'report_resolved', 1, 'User complaint resolved', 'Issue report closed', '2025-12-27 09:19:02'),
(5, 1, 'ad_rejected', 6, 'Expert Cleaning Services ', 'Advertisement \'Expert Cleaning Services \' was rejectd', '2026-01-03 10:31:02'),
(6, 1, 'ad_approved', 5, 'Happy Customer Constructions', 'Advertisement \'Happy Customer Constructions\' was approved', '2026-01-03 10:35:31'),
(7, 1, '', 4, 'About FixLanka', 'Static content \'About FixLanka\' was unpublished', '2026-01-04 04:23:07'),
(8, 1, 'ad_approved', 2, 'Premium Home Construction Services', 'Advertisement \'Premium Home Construction Services\' was approved', '2026-01-04 17:28:28'),
(9, 1, 'ad_approved', 4, 'High Quality Plumbing Services ', 'Advertisement \'High Quality Plumbing Services \' was approved', '2026-01-07 04:02:35'),
(10, 1, 'ad_rejected', 7, 'Nimal Constructions ', 'Advertisement \'Nimal Constructions \' was rejectd', '2026-01-07 04:23:23'),
(11, 1, 'ad_approved', 5, 'Happy Customer Constructions', 'Advertisement \'Happy Customer Constructions\' was approved', '2026-01-07 06:57:21'),
(12, 1, 'ad_approved', 4, 'High Quality Plumbing Services ', 'Advertisement \'High Quality Plumbing Services \' was approved', '2026-01-07 10:12:05'),
(13, 1, '', 5, 'Help Center', 'Static content \'Help Center\' was unpublished', '2026-01-13 16:15:25'),
(14, 1, '', 3, 'Frequently Asked Questions', 'Static content \'Frequently Asked Questions\' was unpublished', '2026-01-14 12:01:23'),
(15, 1, '', 5, 'Help Center', 'Static content \'Help Center\' was unpublished', '2026-01-14 12:01:26'),
(16, 1, '', 4, 'About FixLanka', 'Static content \'About FixLanka\' was unpublished', '2026-01-18 14:32:00'),
(17, 1, '', 3, 'Frequently Asked Questions', 'Static content \'Frequently Asked Questions\' was published', '2026-02-01 16:31:57'),
(18, 1, 'ad_approved', 4, 'High Quality Plumbing Services ', 'Advertisement \'High Quality Plumbing Services \' was approved', '2026-02-01 16:52:54'),
(19, 1, 'ad_rejected', 1, 'New Year Construction Packages', 'Advertisement \'New Year Construction Packages\' was rejectd', '2026-02-01 16:53:33'),
(20, 1, 'ad_approved', 2, 'Premium Home Construction Services', 'Advertisement \'Premium Home Construction Services\' was approved', '2026-02-03 17:50:33'),
(21, 1, '', 4, 'High Quality Plumbing Services ', 'Advertisement \'High Quality Plumbing Services \' was paused', '2026-02-03 18:06:08'),
(22, 1, '', 5, 'Happy Customer Constructions', 'Advertisement \'Happy Customer Constructions\' was paused', '2026-02-03 18:06:14'),
(23, 1, 'ad_approved', 9, 'Good Plumbing ', 'Advertisement \'Good Plumbing \' was approved', '2026-02-03 18:08:25'),
(24, 1, 'ad_approved', 8, 'H&Q Constructions ', 'Advertisement \'H&Q Constructions \' was approved', '2026-02-03 18:08:29'),
(25, 1, '', 5, 'Help Center', 'Static content \'Help Center\' was published', '2026-02-03 18:28:02'),
(26, 1, '', 5, 'Happy Customer Constructions', 'Advertisement \'Happy Customer Constructions\' was resumed', '2026-02-07 03:47:00'),
(27, 1, '', 4, 'High Quality Plumbing Services ', 'Advertisement \'High Quality Plumbing Services \' was resumed', '2026-02-07 03:47:05'),
(28, 1, 'ad_approved', 10, 'S&S Korean Constructions ', 'Advertisement \'S&S Korean Constructions \' was approved', '2026-02-07 05:07:03'),
(29, 1, 'ad_rejected', 11, 'best quality plumbing ', 'Advertisement \'best quality plumbing \' was rejected', '2026-02-09 12:49:51'),
(30, 1, 'ad_approved', 12, 'Quality ABC Constructions ', 'Advertisement \'Quality ABC Constructions \' was approved', '2026-02-10 12:49:38'),
(31, 1, 'ad_approved', 13, 'Leel Plumbers', 'Advertisement \'Leel Plumbers\' was approved', '2026-02-14 06:53:44'),
(32, 1, '', 5, 'Help Center', 'Static content \'Help Center\' was unpublished', '2026-02-14 07:44:21'),
(33, 1, 'ad_approved', 16, 'ABC Holdings ', 'Advertisement \'ABC Holdings \' was approved', '2026-02-17 17:59:42'),
(34, 1, 'ad_rejected', 17, 'P&A Constructions ', 'Advertisement \'P&A Constructions \' was rejected', '2026-02-18 06:04:10'),
(35, 1, '', 5, 'Help Center', 'Static content \'Help Center\' was published', '2026-02-18 06:05:29'),
(36, 1, '', 2, 'Privacy Policy', 'Static content \'Privacy Policy\' was unpublished', '2026-02-18 14:00:28'),
(37, 1, '', 1, 'Terms of Service', 'Static content \'Terms of Service\' was published', '2026-02-18 14:00:37'),
(38, 1, '', 2, 'Privacy Policy', 'Static content \'Privacy Policy\' was published', '2026-02-18 14:00:52'),
(39, 1, '', 1, 'Terms of Service', 'Static content \'Terms of Service\' was unpublished', '2026-02-18 14:00:54'),
(40, 1, '', 2, 'Privacy Policy', 'Static content \'Privacy Policy\' was unpublished', '2026-02-18 14:00:55'),
(41, 1, '', 3, 'Frequently Asked Questions', 'Static content \'Frequently Asked Questions\' was unpublished', '2026-02-18 14:00:57'),
(42, 1, '', 2, 'Privacy Policy', 'Static content \'Privacy Policy\' was published', '2026-02-18 14:01:18'),
(43, 1, '', 3, 'Frequently Asked Questions', 'Static content \'Frequently Asked Questions\' was published', '2026-02-19 18:15:07');

-- --------------------------------------------------------

--
-- Table structure for table `moderator_management_log`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

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

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notification_id`, `title`, `message`, `recipient_type`, `status`, `send_date`, `created_at`, `created_by_id`, `created_by_role`, `created_by_name`, `recipient_id`, `is_read`) VALUES
(14, 'Service Update', 'Important updates to our service offerings. Check out the new features available in your dashboard.', 'all', 'sent', '2026-02-15 14:49:17', '2026-02-15 14:49:17', NULL, NULL, NULL, NULL, 1),
(15, 'Maintenance Notice', 'Scheduled maintenance on [DATE] from [TIME] to [TIME]. Services may be temporarily unavailable.', 'all', 'sent', '2026-02-15 14:49:27', '2026-02-15 14:49:27', NULL, NULL, NULL, NULL, 1),
(16, 'Security Alert', 'We\'ve detected unusual activity on your account. Please verify your security settings.', 'all', 'sent', '2026-02-19 18:46:13', '2026-02-19 18:46:13', NULL, NULL, NULL, NULL, 1),
(17, 'Hello!', 'Welcome To Fixlanka', 'company', 'sent', '2026-04-07 12:04:58', '2026-04-07 12:04:58', NULL, NULL, NULL, NULL, 1),
(18, 'Hello', 'Welcome to Fixlanka', 'all', 'sent', '2026-04-07 12:05:25', '2026-04-07 12:05:25', NULL, NULL, NULL, NULL, 1),
(19, 'Hi!!!!', 'iluhfbaskfbkasbia', 'company', 'sent', '2026-04-08 05:50:49', '2026-04-08 05:50:49', NULL, NULL, NULL, NULL, 1),
(20, 'Service Update', 'Important updates to our service offerings. Check out the new features available in your dashboard.', 'all', 'sent', '2026-04-08 05:54:11', '2026-04-08 05:54:11', NULL, NULL, NULL, NULL, 1),
(21, 'New Feature', 'We\'ve added a new feature to improve your experience. Learn more about [FEATURE_NAME] in your account.', 'all', 'sent', '2026-04-08 05:54:45', '2026-04-08 05:54:45', NULL, NULL, NULL, NULL, 1),
(22, 'Byee', 'All please logout', 'all', 'sent', '2026-04-08 06:33:12', '2026-04-08 06:33:12', 0, 'admin', 'admin', NULL, 1),
(23, 'Hello', 'Please work well', 'repairer', 'sent', '2026-04-08 06:41:15', '2026-04-08 06:41:15', 1, 'moderator', 'moderator1', NULL, 0),
(24, 'Employment ended', 'UCSC has ended your employment. Reason: You are dull', 'repairer', 'sent', '2026-04-08 08:09:16', '2026-04-08 08:09:16', 9997, 'company', 'UCSC', 107, 1),
(25, 'Contract change request', 'Customer requested adjustments for Test Sink Repair.', 'company', 'sent', '2026-04-08 18:09:58', '2026-04-08 18:09:58', 9996, 'user', 'Customer', 9997, 1),
(26, 'Contract change request', 'Customer requested adjustments for Test Sink Repair.', 'company', 'sent', '2026-04-08 19:51:35', '2026-04-08 19:51:35', 9996, 'user', 'Customer', 9997, 1),
(27, 'Contract change request updated', 'Company accepted your change request for Test Sink Repair.', 'user', 'sent', '2026-04-08 20:11:29', '2026-04-08 20:11:29', 9997, 'company', 'Company', 9996, 0),
(28, 'Contract accepted', 'Customer accepted the contract for Test Sink Repair.', 'company', 'sent', '2026-04-08 20:18:02', '2026-04-08 20:18:02', 9996, 'user', 'Customer', 9997, 1),
(29, 'Contract accepted', 'You accepted the contract for Test Sink Repair.', 'user', 'sent', '2026-04-08 20:18:02', '2026-04-08 20:18:02', 9996, 'user', 'Customer', 9996, 0),
(30, 'Quotation accepted', 'Your quotation for request #35 was accepted.', 'company', 'sent', '2026-04-10 09:02:46', '2026-04-10 09:02:46', NULL, 'user', 'Customer', 9997, 1),
(31, 'Quotation rejected', 'Your quotation for request #35 was rejected.', 'repairer', 'sent', '2026-04-10 09:02:46', '2026-04-10 09:02:46', NULL, 'user', 'Customer', 107, 0),
(32, 'Contract change request', 'Customer requested adjustments for I need to fix my door.', 'company', 'sent', '2026-04-10 09:19:25', '2026-04-10 09:19:25', 9996, 'user', 'Customer', 9997, 1),
(33, 'Contract change request', 'Customer requested adjustments for I need to fix my door.', 'company', 'sent', '2026-04-10 09:25:02', '2026-04-10 09:25:02', 9996, 'user', 'Customer', 9997, 1),
(34, 'Contract change request updated', 'Company rejected your change request for I need to fix my door.', 'user', 'sent', '2026-04-10 09:32:18', '2026-04-10 09:32:18', 9997, 'company', 'Company', 9996, 0),
(35, 'Contract change request updated', 'Company accepted your change request for I need to fix my door.', 'user', 'sent', '2026-04-10 09:32:21', '2026-04-10 09:32:21', 9997, 'company', 'Company', 9996, 0),
(36, 'Contract accepted', 'Customer accepted the contract for I need to fix my door.', 'company', 'sent', '2026-04-10 10:21:33', '2026-04-10 10:21:33', 9996, 'user', 'Customer', 9997, 1),
(37, 'Contract accepted', 'You accepted the contract for I need to fix my door.', 'user', 'sent', '2026-04-10 10:21:33', '2026-04-10 10:21:33', 9996, 'user', 'Customer', 9996, 0),
(38, 'Quotation accepted', 'Your quotation for request #36 was accepted.', 'company', 'sent', '2026-04-10 11:16:33', '2026-04-10 11:16:33', NULL, 'user', 'Customer', 9997, 1),
(39, 'Quotation accepted', 'Your quotation for request #37 was accepted.', 'company', 'sent', '2026-04-10 11:29:11', '2026-04-10 11:29:11', NULL, 'user', 'Customer', 9997, 1),
(40, 'Contract accepted & paid', 'Customer accepted and paid upfront for Paint the house. You can now start the job.', 'company', 'sent', '2026-04-10 12:21:32', '2026-04-10 12:21:32', 9996, 'user', 'Customer', 9997, 1),
(41, 'Contract accepted & paid', 'Customer accepted and paid upfront for I need to paint my house. You can now start the job.', 'company', 'sent', '2026-04-10 12:24:42', '2026-04-10 12:24:42', 9996, 'user', 'Customer', 9997, 1),
(42, 'Quotation accepted', 'Your quotation for request #39 was accepted.', 'company', 'sent', '2026-04-10 16:40:24', '2026-04-10 16:40:24', NULL, 'user', 'Customer', 9997, 0);

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

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`payment_method_id`, `company_id`, `card_type`, `last_four_digits`, `card_holder_name`, `expiry_month`, `expiry_year`, `billing_address`, `is_primary`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 9997, 'mastercard', '0202', 'Dilanka', '03', '2034', 'NO:115, Kumbukanda, Mahailuppallama', 1, 1, '2026-04-01 18:32:30', '2026-04-01 18:32:30');

-- --------------------------------------------------------

--
-- Table structure for table `placement_limits`
--

CREATE TABLE `placement_limits` (
  `placement_type` varchar(50) NOT NULL,
  `max_slots_per_day` int(11) NOT NULL DEFAULT 5,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `placement_limits`
--

INSERT INTO `placement_limits` (`placement_type`, `max_slots_per_day`, `description`, `created_at`) VALUES
('banner', 10, 'Maximum 10 banner ads per day', '2026-02-03 17:09:34'),
('featured', 5, 'Maximum 5 featured ads per day', '2026-02-03 17:09:34'),
('sponsored', 8, 'Maximum 8 sponsored ads per day', '2026-02-03 17:09:34');

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

--
-- Dumping data for table `repairer`
--

INSERT INTO `repairer` (`repairer_id`, `f_name`, `l_name`, `email`, `password`, `phone`, `phoneNumber`, `about`, `profilePicture`, `ratings`, `completedJobsCount`, `districts`, `availability`, `dateJoined`, `category_id`, `account_status`, `banned_permanent`, `suspended_until`, `moderation_reason`, `skills`) VALUES
(107, 'Gayan', 'Anuradha', 'nimesha@gmail.com', '$2y$10$nIk5VLNtOpkqECaUNAASd.cgeoBamWbCY9.ikK/XmHbR5BKwHs9WG', NULL, '0701597534', 'kjrwbjbfkjbeibwikjad', NULL, 0.00, 0, 'Colombo, Kalutara', 'available', '2026-03-02 17:00:51', 12, 'ACTIVE', 0, NULL, NULL, 'clean'),
(108, 'Dilanka', 'Supun', 'dilanka@gmail.com', '$2y$10$cIb4c.VZO5NnmTmhFfmpO.mJj24uAnOCX22GX8MjZV3.y69xhdn.a', NULL, '0743588367', 'skbvfdbvkjbds', NULL, 0.00, 0, 'Colombo', 'available', '2026-04-07 05:39:07', 18, 'ACTIVE', 0, NULL, NULL, NULL),
(109, 'repairer', 'repairer', 're@gmail.com', '$2y$10$e6TDVhecT/jU0NjYNOGeueag4otPR4hGPixvHIpr9bQXYJziaPJgG', NULL, '22385', 'sdaszgszv', NULL, 0.00, 0, 'Colombo', 'available', '2026-04-07 06:03:09', 9, 'ACTIVE', 0, NULL, NULL, NULL),
(110, 'Dilanka', 'Supun', 'dilankasupun333@gmail.com', '$2y$10$ttLLy2IhBAZiLqq9jfsurOSdJm.GVQNIciLyhoYf5mkh6dN5xgnQG', NULL, 'ucsc@gmail.com', 'sajbk', NULL, 0.00, 0, 'Galle', 'available', '2026-04-07 07:16:09', 21, 'ACTIVE', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `repairerapplication`
--

CREATE TABLE `repairerapplication` (
  `app_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `posting_id` int(11) NOT NULL,
  `date_applied` timestamp NOT NULL DEFAULT current_timestamp(),
  `app_status` enum('pending','reviewed','accepted','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repairerassignment`
--

CREATE TABLE `repairerassignment` (
  `assignment_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `assigned_date` date DEFAULT curdate(),
  `status` enum('assigned','active','completed','removed') DEFAULT 'assigned'
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
  `validUntil` date DEFAULT NULL,
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

--
-- Dumping data for table `repairer_applications`
--

INSERT INTO `repairer_applications` (`application_id`, `job_posting_id`, `repairer_id`, `cover_letter`, `expected_rate`, `status`, `rejection_reason`, `applied_date`, `updated_at`) VALUES
(5, 3, 107, 'I\'m Cleaning perfectly', 2250.00, 'pending', NULL, '2026-04-06 06:39:51', '2026-04-06 06:39:51'),
(8, 1, 107, 'hgccgcjhchjchujkc', 3500.00, 'rejected', 'You are dull', '2026-04-06 12:28:32', '2026-04-08 08:16:00');

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
-- Table structure for table `service_area`
--

CREATE TABLE `service_area` (
  `area_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `owner_type` enum('company','repairer') NOT NULL,
  `district` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_area`
--

INSERT INTO `service_area` (`area_id`, `owner_id`, `owner_type`, `district`) VALUES
(1, 9997, 'company', 'Colombo'),
(2, 9997, 'company', 'Kalutara'),
(3, 9998, 'company', 'Colombo');

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

--
-- Dumping data for table `staffsummary`
--

INSERT INTO `staffsummary` (`staff_summary_id`, `company_id`, `specialty`, `total_count`, `active_count`, `inactive_count`, `avg_rating`, `avg_hourly_rate`, `min_hourly_rate`, `max_hourly_rate`, `last_update`) VALUES
(2, 9997, 'Painter', 10, 10, 0, 0.00, 2500.00, 2500.00, 2500.00, '2026-04-07 06:56:08'),
(3, 9997, 'HVAC Technician', 5, 5, 0, 0.00, 2500.00, 2500.00, 2500.00, '2026-04-07 06:56:08');

-- --------------------------------------------------------

--
-- Table structure for table `staticcontent`
--

CREATE TABLE `staticcontent` (
  `content_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `body` text NOT NULL,
  `status` enum('Draft','Published') DEFAULT 'Published',
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `content_type` enum('terms','privacy','faq','about','help','contact','how_it_works','services','why_choose','support','landing_hero') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staticcontent`
--

INSERT INTO `staticcontent` (`content_id`, `title`, `description`, `body`, `status`, `last_update`, `content_type`) VALUES
(1, 'Terms of Service', 'Legal terms and conditions for using FixLanka platform', 'Welcome to FixLanka! By accessing and using our platform, you agree to be bound by these Terms of Service.\r\n\r\n1. ACCEPTANCE OF TERMS\r\nBy creating an account or using our services, you accept these terms in full. If you disagree with any part of these terms, you must not use our platform.\r\n\r\n2. USER ACCOUNTS\r\n- You must be at least 18 years old to register\r\n- You are responsible for maintaining the confidentiality of your account\r\n- You must provide accurate and complete information\r\n- One person may not maintain multiple accounts\r\n\r\n3. SERVICE PROVIDER RESPONSIBILITIES\r\nService providers (repairers and companies) must:\r\n- Provide accurate credentials and qualifications\r\n- Deliver services as described and quoted\r\n- Maintain professional conduct at all times\r\n- Comply with all local laws and regulations\r\n\r\n4. PAYMENT TERMS\r\n- All payments must be processed through our secure platform\r\n- Service providers will receive payment after successful job completion\r\n- FixLanka charges a 10% platform fee on all transactions\r\n- Refunds are subject to our refund policy\r\n\r\n5. LIABILITY\r\n- FixLanka acts as a marketplace platform only\r\n- We are not responsible for the quality of services provided\r\n- Users engage with service providers at their own risk\r\n- We recommend verifying credentials and reviews before hiring\r\n\r\n6. INTELLECTUAL PROPERTY\r\nAll content on FixLanka, including logos, text, and graphics, is owned by FixLanka and protected by copyright laws.\r\n\r\n7. TERMINATION\r\nWe reserve the right to suspend or terminate accounts that violate these terms or engage in fraudulent activity.\r\n\r\n8. CHANGES TO TERMS\r\nWe may modify these terms at any time. Continued use of the platform constitutes acceptance of modified terminologies.\r\n\r\nLast updated: January 2026', 'Draft', '2026-04-08 12:08:43', 'terms'),
(2, 'Privacy Policy', 'How we collect, use, and protect your personal information', 'At FixLanka, we take your privacy seriously. This Privacy Policy explains how we collect, use, and safeguard your information.\r\n\r\n1. INFORMATION WE COLLECT\r\n\r\nPersonal Information:\r\n- Name, email address, phone number\r\n- Physical address for service delivery\r\n- Payment information (processed securely)\r\n- Profile photos and identification documents (for service providers)\r\n\r\nUsage Information:\r\n- Pages visited and features used\r\n- Search queries and preferences\r\n- Device information and IP address\r\n- Cookies and similar technologies\r\n\r\n2. HOW WE USE YOUR INFORMATION\r\n\r\nWe use your information to:\r\n- Provide and improve our services\r\n- Connect users with service providers\r\n- Process payments and transactions\r\n- Send notifications and updates\r\n- Prevent fraud and ensure platform security\r\n- Comply with legal obligations\r\n\r\n3. INFORMATION SHARING\r\n\r\nWe do not sell your personal information. We may share data with:\r\n- Service providers you choose to work with\r\n- Payment processors for transactions\r\n- Law enforcement when legally required\r\n- Third-party analytics services (anonymized data)\r\n\r\n4. DATA SECURITY\r\n\r\nWe implement industry-standard security measures:\r\n- Encrypted data transmission (SSL/TLS)\r\n- Secure password storage with hashing\r\n- Regular security audits\r\n- Access controls and monitoring\r\n- Secure payment processing through trusted partners\r\n\r\n5. YOUR RIGHTS\r\n\r\nYou have the right to:\r\n- Access your personal data\r\n- Correct inaccurate information\r\n- Request data deletion (subject to legal requirements)\r\n- Opt-out of marketing communications\r\n- Export your data\r\n\r\n6. COOKIES\r\n\r\nWe use cookies to:\r\n- Keep you logged in\r\n- Remember your preferences\r\n- Analyze platform usage\r\n- Improve user experience\r\n\r\nYou can control cookies through your browser settings.\r\n\r\n7. DATA RETENTION\r\n\r\nWe retain your information as long as your account is active or as needed to provide services. After account deletion, we may retain certain data for legal and security purposes.\r\n\r\n8. CHILDREN\'S PRIVACY\r\n\r\nOur services are not intended for users under 18. We do not knowingly collect information from children.\r\n\r\n9. INTERNATIONAL USERS\r\n\r\nYour information may be stored and processed in Sri Lanka or other countries where we operate.\r\n\r\n10. CHANGES TO POLICY\r\n\r\nWe may update this policy periodically. We will notify you of significant changes via email or platform notification.\r\n\r\nContact us at privacy@fixlanka.lk for any privacy concerns.\r\n\r\nLast updated: January 2026', 'Published', '2026-02-18 14:01:18', 'privacy'),
(3, 'Frequently Asked Questions', 'Common questions and answers about FixLanka services', 'FREQUENTLY ASKED QUESTIONS\r\n\r\n=== FOR USERS ===\r\n\r\nQ: How do I post a job request?\r\nA: Log in to your account, click \"Post a Job\" from your dashboard, fill in the job details including category, location, urgency, and upload photos if needed. Submit the request and wait for quotes from service providers.\r\n\r\nQ: How long does it take to receive quotes?\r\nA: Typically, you will start receiving quotes within 1-2 hours. Urgent requests often get faster responses.\r\n\r\nQ: How do I choose the best service provider?\r\nA: Review their profile, ratings, completed jobs, and the quotes they provide. Read reviews from previous customers. Compare prices and estimated completion times.\r\n\r\nQ: Is payment secure?\r\nA: Yes! All payments are processed through our secure platform using encrypted payment gateways. Your financial information is never stored on our servers.\r\n\r\nQ: What if I\'m not satisfied with the service?\r\nA: Contact our support team immediately. We have a dispute resolution process and may offer refunds or arrange for corrective work depending on the situation.\r\n\r\nQ: Can I cancel a job request?\r\nA: Yes, you can cancel before accepting a quote without penalty. After accepting a quote, cancellation terms apply and may incur fees.\r\n\r\n=== FOR SERVICE PROVIDERS ===\r\n\r\nQ: How do I become a service provider on FixLanka?\r\nA: Click \"Join as Service Provider\" and complete the registration form. Provide your credentials, qualifications, and work experience. Our team will verify your information within 24-48 hours.\r\n\r\nQ: What are the fees?\r\nA: FixLanka charges a 10% platform fee on completed jobs. There are no upfront costs or subscription fees. You only pay when you earn.\r\n\r\nQ: How do I get paid?\r\nA: After completing a job and receiving customer approval, payment is processed within 3-5 business days to your registered bank account.\r\n\r\nQ: Can I work in multiple districts?\r\nA: Yes! During registration, you can select all districts where you offer services.\r\n\r\nQ: How does the rating system work?\r\nA: Customers rate your service after job completion on a 5-star scale. Maintaining high ratings increases your visibility and job opportunities.\r\n\r\nQ: What if a customer doesn\'t pay?\r\nA: Our platform requires payment confirmation before job completion. Contact support if you encounter payment issues.\r\n\r\n=== TECHNICAL QUESTIONS ===\r\n\r\nQ: Which browsers are supported?\r\nA: FixLanka works best on Chrome, Firefox, Safari, and Edge (latest versions).\r\n\r\nQ: Is there a mobile app?\r\nA: Currently, we offer a mobile-responsive website. A dedicated mobile app is coming soon!\r\n\r\nQ: How do I reset my password?\r\nA: Click \"Forgot Password\" on the login page and follow the instructions sent to your email.\r\n\r\nQ: Why can\'t I upload photos?\r\nA: Ensure your images are in JPG, PNG, or JPEG format and under 5MB each. Check your internet connection.\r\n\r\nQ: Why can\'t I upload photos?\r\nA: Ensure your images are in JPG, PNG, or JPEG format and under 5MB each. Check your internet connection.\r\n\r\n=== CONTACT US ===\r\n\r\nStill have questions? Reach us at:\r\n- Email: support@fixlanka.lk\r\n- Phone: +94 11 234 5678\r\n- Live Chat: Available Mon-Fri, 9 AM - 6 PM\r\n\r\nLast updated: January 2026', 'Published', '2026-04-08 12:41:08', 'faq'),
(4, 'About FixLanka', 'Learn about our mission, vision, and the team behind FixLanka', 'ABOUT FIXLANKA\r\n\r\n=== OUR STORY ===\r\n\r\nFixLanka was founded in 2024 with a simple mission: to make home repair and maintenance services accessible, reliable, and affordable for everyone in Sri Lanka.\r\n\r\nWe recognized that finding trustworthy, skilled professionals for home repairs was a major challenge. Customers struggled to find reliable service providers, while skilled repairers and companies had difficulty reaching potential clients.\r\n\r\nFixLanka bridges this gap by creating a transparent, efficient marketplace that connects homeowners with verified, qualified service providers across Sri Lanka.\r\n\r\n=== OUR MISSION ===\r\n\r\nTo revolutionize the home services industry in Sri Lanka by:\r\n- Providing easy access to qualified professionals\r\n- Ensuring transparency and trust through verified profiles and reviews\r\n- Offering fair pricing and secure payment processing\r\n- Supporting local businesses and skilled workers\r\n- Delivering exceptional customer service\r\n\r\n=== OUR VISION ===\r\n\r\nTo become Sri Lanka\'s most trusted and comprehensive home services platform, expanding our services across all districts and becoming the go-to solution for every household repair and maintenance need.\r\n\r\n=== WHAT WE OFFER ===\r\n\r\nFor Homeowners:\r\n- Quick and easy job posting\r\n- Access to verified service providers\r\n- Competitive quotes from multiple professionals\r\n- Secure payment processing\r\n- Quality assurance and customer support\r\n\r\nFor Service Providers:\r\n- Increased visibility and job opportunities\r\n- Direct access to customers\r\n- Fair and transparent pricing\r\n- Timely payments\r\n- Business growth support\r\n\r\n=== OUR CATEGORIES ===\r\n\r\nWe cover all major home service needs:\r\n- Plumbing repairs and installations\r\n- Electrical work and wiring\r\n- Carpentry and furniture\r\n- Painting and decorating\r\n- HVAC services\r\n- Appliance repair\r\n- Roofing and waterproofing\r\n- Masonry and construction\r\n- Landscaping and gardening\r\n- And many more!\r\n\r\n=== OUR VALUES ===\r\n\r\nTRUST: We verify all service providers and maintain strict quality standards.\r\n\r\nTRANSPARENCY: Clear pricing, honest reviews, and open communication.\r\n\r\nQUALITY: We partner only with skilled, professional service providers.\r\n\r\nINNOVATION: Continuously improving our platform with new features and technologies.\r\n\r\nCUSTOMER FIRST: Your satisfaction is our top priority.\r\n\r\n=== COVERAGE ===\r\n\r\nWe currently serve customers across all 25 districts of Sri Lanka, with growing networks of service providers in:\r\n- Colombo, Gampaha, Kalutara\r\n- Kandy, Matale, Nuwara Eliya\r\n- Galle, Matara, Hambantota\r\n- Jaffna, Kilinochchi, Mannar\r\n- And all other districts\r\n\r\n=== OUR TEAM ===\r\n\r\nFixLanka is powered by a dedicated team of technology experts, customer service professionals, and industry specialists committed to transforming the home services sector.\r\n\r\n=== JOIN US ===\r\n\r\nWhether you\'re a homeowner seeking quality services or a skilled professional looking to grow your business, FixLanka is here for you.\r\n\r\nJoin thousands of satisfied customers and service providers who trust FixLanka for their home service needs.\r\n\r\nContact Us:\r\n- Email: info@fixlanka.lk\r\n- Phone: +94 11 234 5678\r\n- Address: 123 Galle Road, Colombo 03, Sri Lanka\r\n\r\nLast updated: January 2026', 'Published', '2026-02-18 08:12:45', 'about'),
(5, 'Help Center', 'Comprehensive guide to using FixLanka platform', 'FIXLANKA HELP CENTER\r\n\r\nWelcome to the FixLanka Help Center! Find step-by-step guides and answers to common questions.\r\n\r\n=== GETTING STARTED ===\r\n\r\nCREATING AN ACCOUNT\r\n1. Click \"Sign Up\" on the homepage\r\n2. Choose account type (User or Service Provider)\r\n3. Fill in required information\r\n4. Verify your email address\r\n5. Complete your profile\r\n\r\nNAVIGATING THE PLATFORM\r\n- Dashboard: Your central hub for all activities\r\n- Job Requests: View and manage your service requests\r\n- Messages: Communicate with service providers/customers\r\n- Profile: Update your information and settings\r\n- Notifications: Stay updated on important activities\r\n\r\n=== FOR USERS ===\r\n\r\nHOW TO POST A JOB REQUEST\r\n1. Log in to your account\r\n2. Click \"Post a Job\" button\r\n3. Select service category\r\n4. Enter job title and detailed description\r\n5. Add your location (district and address)\r\n6. Set urgency level (medium or urgent)\r\n7. Upload photos (optional but recommended)\r\n8. Set expected completion date\r\n9. Choose provider type (individual, company, or both)\r\n10. Review and submit\r\n\r\nRECEIVING AND COMPARING QUOTES\r\n- Service providers will send quotes within hours\r\n- Review each quote carefully\r\n- Check provider profiles, ratings, and reviews\r\n- Compare pricing and estimated completion time\r\n- Ask questions through the messaging system\r\n- Accept the quote that best meets your needs\r\n\r\nPAYMENT PROCESS\r\n1. Accept a quote from your preferred provider\r\n2. Job status changes to \"In Progress\"\r\n3. Service provider completes the work\r\n4. Review and approve the completed work\r\n5. Process payment through secure gateway\r\n6. Rate and review the service provider\r\n\r\nMANAGING YOUR JOBS\r\n- Track job status in real-time\r\n- Communicate with service providers\r\n- Upload additional photos or details\r\n- Request updates or modifications\r\n- Mark jobs as completed\r\n- Report issues if needed\r\n\r\n=== FOR SERVICE PROVIDERS ===\r\n\r\nSETTING UP YOUR PROFILE\r\n1. Complete all required fields\r\n2. Add professional profile photo\r\n3. Write detailed \"About\" section\r\n4. List your skills and qualifications\r\n5. Specify service districts\r\n6. Set your availability status\r\n7. Upload certificates/licenses (if applicable)\r\n\r\nFINDING AND BIDDING ON JOBS\r\n1. Browse available job requests in your category\r\n2. Filter by location and urgency\r\n3. Review job details carefully\r\n4. Submit competitive quotes with clear pricing\r\n5. Include estimated completion time\r\n6. Add a professional message explaining your approach\r\n\r\nWINNING JOBS\r\n- Respond quickly to new requests\r\n- Offer competitive pricing\r\n- Maintain high ratings and positive reviews\r\n- Provide detailed, professional quotes\r\n- Build a strong profile with completed jobs\r\n\r\nCOMPLETING JOBS\r\n1. Confirm job details with customer\r\n2. Schedule work at convenient time\r\n3. Arrive on time and work professionally\r\n4. Update customer on progress\r\n5. Complete work to high standards\r\n6. Request customer approval\r\n7. Receive payment through platform\r\n\r\nGROWING YOUR BUSINESS\r\n- Maintain excellent service quality\r\n- Respond promptly to inquiries\r\n- Keep your profile updated\r\n- Earn positive reviews\r\n- Consider promotion packages for increased visibility\r\n\r\n=== PAYMENT AND BILLING ===\r\n\r\nPAYMENT METHODS\r\n- Credit/Debit Cards (Visa, Mastercard)\r\n- Online Banking\r\n- Mobile Wallets\r\n- Bank Transfer\r\n\r\nSECURITY\r\n- All transactions are encrypted\r\n- We never store full card details\r\n- PCI-DSS compliant payment processing\r\n- Secure authentication protocols\r\n\r\nREFUND POLICY\r\n- Full refund if work not started\r\n- Partial refund for incomplete work\r\n- Quality issues reviewed case-by-case\r\n- Disputes handled by support team\r\n\r\n=== SAFETY AND SECURITY ===\r\n\r\nSTAYING SAFE\r\n- Verify service provider credentials\r\n- Check ratings and reviews\r\n- Communicate through platform messaging\r\n- Keep payment records\r\n- Report suspicious activity immediately\r\n\r\nPRIVACY PROTECTION\r\n- Your personal data is encrypted\r\n- We never sell your information\r\n- Control your privacy settings\r\n- Review our Privacy Policy for details\r\n\r\n=== ACCOUNT MANAGEMENT ===\r\n\r\nUPDATING YOUR PROFILE\r\n1. Go to Profile Settings\r\n2. Click \"Edit Profile\"\r\n3. Update desired information\r\n4. Save changes\r\n\r\nCHANGING PASSWORD\r\n1. Go to Account Settings\r\n2. Click \"Change Password\"\r\n3. Enter current password\r\n4. Enter new password (min 8 characters)\r\n5. Confirm new password\r\n6. Save changes\r\n\r\nNOTIFICATION PREFERENCES\r\n- Email notifications\r\n- SMS alerts (if enabled)\r\n- In-app notifications\r\n- Customize frequency and types\r\n\r\n=== TROUBLESHOOTING ===\r\n\r\nCAN\'T LOG IN?\r\n- Check email and password\r\n- Use \"Forgot Password\" to reset\r\n- Clear browser cache and cookies\r\n- Try different browser\r\n- Contact support if issue persists\r\n\r\nUPLOAD ISSUES?\r\n- Check file size (max 5MB per photo)\r\n- Use supported formats (JPG, PNG, JPEG)\r\n- Check internet connection\r\n- Try reducing image resolution\r\n\r\nPAYMENT FAILED?\r\n- Verify card details and limits\r\n- Check internet connection\r\n- Try alternative payment method\r\n- Contact your bank\r\n- Reach out to our support team\r\n\r\n=== CONTACT SUPPORT ===\r\n\r\nNeed more help? We\'re here for you!\r\n\r\nEmail Support: support@fixlanka.lk\r\nResponse time: Within 24 hours\r\n\r\nPhone Support: +94 11 234 5678\r\nAvailable: Mon-Fri, 9 AM - 6 PM\r\n\r\nLive Chat: Available on website\r\nStatus: Online during business hours\r\n\r\nAddress: 123 Galle Road, Colombo 03, Sri Lanka\r\n\r\n=== FEEDBACK ===\r\n\r\nWe value your feedback! Help us improve:\r\n- Rate your experience\r\n- Suggest new features\r\n- Report bugs\r\n- Share your success stories\r\n\r\nEmail: feedback@fixlanka.lk\r\n\r\nLast updated: January 2026', 'Published', '2026-02-18 06:05:29', 'help'),
(7, 'How It Works', 'Step-by-step explanation of platform workflow.', '<div class=\"about-section\">\r\n    <div class=\"about-content\">\r\n        <h2 class=\"about-title\">About Fix Lanka</h2>\r\n        <p class=\"about-text\">\r\n            <strong>Fix Lanka</strong> is your trusted platform connecting customers with verified,\r\n            professional service providers across Sri Lanka. We understand that finding reliable\r\n            professionals for home and business needs can be challenging and time-consuming.\r\n        </p>\r\n        <p class=\"about-text\">\r\n            Our mission is to simplify this process by creating a seamless marketplace where\r\n            quality service providers and customers meet. Whether you need a plumber, electrician,\r\n            cleaner, carpenter, or any other professional service, Fix Lanka makes it easy to\r\n            find, compare, and hire the right expert for your needs.\r\n        </p>\r\n        <p class=\"about-text\">\r\n            We carefully verify all service providers on our platform, ensuring they meet our\r\n            high standards for professionalism, reliability, and quality. With transparent pricing,\r\n            real customer reviews, and 24/7 support, Fix Lanka is committed to delivering\r\n            exceptional service experiences every time.\r\n        </p>\r\n    </div>\r\n</div>\r\n\r\n<div class=\"steps-section\">\r\n    <h2 class=\"section-title\">Getting Started is Easy</h2>\r\n    <div class=\"steps-grid\">\r\n        <div class=\"step-card\">\r\n            <div class=\"step-number\">1</div>\r\n            <div class=\"step-icon\"><i class=\"fas fa-user-plus\"></i></div>\r\n            <h3 class=\"step-title\">Create Your Account</h3>\r\n            <p class=\"step-description\">Sign up for free in just a few minutes. Provide basic information to create your profile and start exploring our network of professional service providers.</p>\r\n        </div>\r\n        <div class=\"step-card\">\r\n            <div class=\"step-number\">2</div>\r\n            <div class=\"step-icon\"><i class=\"fas fa-clipboard-list\"></i></div>\r\n            <h3 class=\"step-title\">Post Your Job Request</h3>\r\n            <p class=\"step-description\">Describe your service needs in detail. Include the type of service, location, timeline, and any specific requirements. You can also upload photos to help service providers understand your needs better.</p>\r\n        </div>\r\n        <div class=\"step-card\">\r\n            <div class=\"step-number\">3</div>\r\n            <div class=\"step-icon\"><i class=\"fas fa-search\"></i></div>\r\n            <h3 class=\"step-title\">Browse &amp; Compare</h3>\r\n            <p class=\"step-description\">Receive quotes from multiple verified service providers. Review their profiles, ratings, past work, and customer reviews. Compare prices and service offerings to make an informed decision.</p>\r\n        </div>\r\n        <div class=\"step-card\">\r\n            <div class=\"step-number\">4</div>\r\n            <div class=\"step-icon\"><i class=\"fas fa-handshake\"></i></div>\r\n            <h3 class=\"step-title\">Choose Your Provider</h3>\r\n            <p class=\"step-description\">Select the service provider that best fits your needs and budget. Contact them directly through our platform to discuss details, schedule the service, and confirm the booking.</p>\r\n        </div>\r\n        <div class=\"step-card\">\r\n            <div class=\"step-number\">5</div>\r\n            <div class=\"step-icon\"><i class=\"fas fa-tools\"></i></div>\r\n            <h3 class=\"step-title\">Get Service Done</h3>\r\n            <p class=\"step-description\">The service provider completes the work according to your agreement. Track the progress through our platform and communicate directly with your provider for any updates or changes.</p>\r\n        </div>\r\n        <div class=\"step-card\">\r\n            <div class=\"step-number\">6</div>\r\n            <div class=\"step-icon\"><i class=\"fas fa-star\"></i></div>\r\n            <h3 class=\"step-title\">Rate &amp; Review</h3>\r\n            <p class=\"step-description\">Once the job is complete, rate your experience and leave a review. Your feedback helps maintain service quality and assists other customers in making informed decisions.</p>\r\n        </div>\r\n    </div>\r\n</div>\r\n\r\n<div class=\"providers-section\">\r\n    <div class=\"providers-content\">\r\n        <h2 class=\"providers-title\"><i class=\"fas fa-briefcase\"></i> Are You a Service Provider?</h2>\r\n        <p class=\"providers-text\">Join Fix Lanka\'s growing network of professional service providers. Reach thousands of potential customers, grow your business, and manage your bookings efficiently through our platform.</p>\r\n        <div class=\"providers-benefits\">\r\n            <div class=\"benefit-item\"><i class=\"fas fa-users\"></i><span>Access to Customers</span></div>\r\n            <div class=\"benefit-item\"><i class=\"fas fa-calendar-check\"></i><span>Manage Bookings</span></div>\r\n            <div class=\"benefit-item\"><i class=\"fas fa-chart-line\"></i><span>Grow Your Business</span></div>\r\n            <div class=\"benefit-item\"><i class=\"fas fa-shield-alt\"></i><span>Verified Badge</span></div>\r\n        </div>\r\n    </div>\r\n</div>', 'Published', '2026-04-08 12:41:08', 'how_it_works'),
(8, 'Services', 'Overview of available service categories.', '<div class=\"service-card\">\r\n    <div class=\"service-icon plumbing\"><i class=\"fas fa-faucet\"></i></div>\r\n    <h3 class=\"service-title\">Plumbing</h3>\r\n    <p class=\"service-description\">Expert plumbing services including pipe repairs, leak fixes, drain cleaning, water heater installation, and bathroom/kitchen plumbing solutions.</p>\r\n    <ul class=\"service-features\">\r\n        <li><i class=\"fas fa-check\"></i> Emergency repairs</li>\r\n        <li><i class=\"fas fa-check\"></i> Pipe installation</li>\r\n        <li><i class=\"fas fa-check\"></i> Leak detection</li>\r\n        <li><i class=\"fas fa-check\"></i> Drain cleaning</li>\r\n    </ul>\r\n</div>\r\n\r\n<div class=\"service-card\">\r\n    <div class=\"service-icon electrical\"><i class=\"fas fa-bolt\"></i></div>\r\n    <h3 class=\"service-title\">Electrical</h3>\r\n    <p class=\"service-description\">Professional electrical services including wiring, lighting installation, circuit repairs, electrical panel upgrades, and safety inspections.</p>\r\n    <ul class=\"service-features\">\r\n        <li><i class=\"fas fa-check\"></i> Wiring &amp; rewiring</li>\r\n        <li><i class=\"fas fa-check\"></i> Lighting installation</li>\r\n        <li><i class=\"fas fa-check\"></i> Panel upgrades</li>\r\n        <li><i class=\"fas fa-check\"></i> Safety inspections</li>\r\n    </ul>\r\n</div>\r\n\r\n<div class=\"service-card\">\r\n    <div class=\"service-icon hvac\"><i class=\"fas fa-wind\"></i></div>\r\n    <h3 class=\"service-title\">HVAC</h3>\r\n    <p class=\"service-description\">Complete HVAC services including air conditioning installation, heating system repairs, ventilation solutions, and regular maintenance.</p>\r\n    <ul class=\"service-features\">\r\n        <li><i class=\"fas fa-check\"></i> AC installation</li>\r\n        <li><i class=\"fas fa-check\"></i> Heating repairs</li>\r\n        <li><i class=\"fas fa-check\"></i> Ventilation</li>\r\n        <li><i class=\"fas fa-check\"></i> Regular maintenance</li>\r\n    </ul>\r\n</div>\r\n\r\n<div class=\"service-card\">\r\n    <div class=\"service-icon cleaning\"><i class=\"fas fa-broom\"></i></div>\r\n    <h3 class=\"service-title\">Cleaning</h3>\r\n    <p class=\"service-description\">Professional cleaning services for homes and offices including deep cleaning, regular maintenance, carpet cleaning, and specialized sanitization.</p>\r\n    <ul class=\"service-features\">\r\n        <li><i class=\"fas fa-check\"></i> Deep cleaning</li>\r\n        <li><i class=\"fas fa-check\"></i> Regular maintenance</li>\r\n        <li><i class=\"fas fa-check\"></i> Carpet cleaning</li>\r\n        <li><i class=\"fas fa-check\"></i> Sanitization</li>\r\n    </ul>\r\n</div>\r\n\r\n<div class=\"service-card\">\r\n    <div class=\"service-icon carpentry\"><i class=\"fas fa-hammer\"></i></div>\r\n    <h3 class=\"service-title\">Carpentry</h3>\r\n    <p class=\"service-description\">Skilled carpentry services including custom furniture, cabinet installation, door and window repairs, and wooden structure construction.</p>\r\n    <ul class=\"service-features\">\r\n        <li><i class=\"fas fa-check\"></i> Custom furniture</li>\r\n        <li><i class=\"fas fa-check\"></i> Cabinet installation</li>\r\n        <li><i class=\"fas fa-check\"></i> Door &amp; window repairs</li>\r\n        <li><i class=\"fas fa-check\"></i> Wood structures</li>\r\n    </ul>\r\n</div>\r\n\r\n<div class=\"service-card\">\r\n    <div class=\"service-icon painting\"><i class=\"fas fa-paint-roller\"></i></div>\r\n    <h3 class=\"service-title\">Painting</h3>\r\n    <p class=\"service-description\">Professional painting services for interior and exterior spaces including wall preparation, color consultation, and specialty finishes.</p>\r\n    <ul class=\"service-features\">\r\n        <li><i class=\"fas fa-check\"></i> Interior painting</li>\r\n        <li><i class=\"fas fa-check\"></i> Exterior painting</li>\r\n        <li><i class=\"fas fa-check\"></i> Color consultation</li>\r\n        <li><i class=\"fas fa-check\"></i> Specialty finishes</li>\r\n    </ul>\r\n</div>', 'Published', '2026-04-08 12:41:08', 'services'),
(9, 'Why Choose Fix Lanka?', 'Trust and value proposition section.', '<div class=\"features-grid\">\r\n    <div class=\"feature-item\">\r\n        <div class=\"feature-icon\"><i class=\"fas fa-user-shield\"></i></div>\r\n        <h4>Verified Professionals</h4>\r\n        <p>All service providers are thoroughly vetted and verified for quality assurance</p>\r\n    </div>\r\n    <div class=\"feature-item\">\r\n        <div class=\"feature-icon\"><i class=\"fas fa-clock\"></i></div>\r\n        <h4>24/7 Availability</h4>\r\n        <p>Round-the-clock support and emergency services when you need them most</p>\r\n    </div>\r\n    <div class=\"feature-item\">\r\n        <div class=\"feature-icon\"><i class=\"fas fa-star\"></i></div>\r\n        <h4>Quality Guaranteed</h4>\r\n        <p>Satisfaction guaranteed with our quality assurance and service standards</p>\r\n    </div>\r\n    <div class=\"feature-item\">\r\n        <div class=\"feature-icon\"><i class=\"fas fa-money-bill-wave\"></i></div>\r\n        <h4>Competitive Pricing</h4>\r\n        <p>Transparent pricing with no hidden charges and competitive rates</p>\r\n    </div>\r\n</div>', 'Published', '2026-04-08 12:41:08', 'why_choose'),
(10, 'Contact Us', 'Support contact details and communication channels.', '<div class=\"info-card\">\r\n    <div class=\"info-icon\"><i class=\"fas fa-envelope\"></i></div>\r\n    <h3>Email Us</h3>\r\n    <p>support@fixlanka.lk</p>\r\n    <small>We\'ll respond within 24 hours</small>\r\n</div>\r\n\r\n<div class=\"info-card\">\r\n    <div class=\"info-icon\"><i class=\"fas fa-phone\"></i></div>\r\n    <h3>Call Us</h3>\r\n    <p>+94 11 234 5678</p>\r\n    <small>Mon-Fri, 9AM - 6PM</small>\r\n</div>\r\n\r\n<div class=\"info-card\">\r\n    <div class=\"info-icon\"><i class=\"fas fa-map-marker-alt\"></i></div>\r\n    <h3>Visit Us</h3>\r\n    <p>Colombo, Sri Lanka</p>\r\n    <small>Main Office</small>\r\n</div>', 'Published', '2026-04-08 12:41:08', 'contact'),
(11, 'Support', 'Support page guidance and help resources.', '<div class=\"quick-links-section\">\r\n    <h2 class=\"section-title\">Quick Access</h2>\r\n    <div class=\"quick-links-grid\">\r\n        <a href=\"/2nd-Year-Group-Project/FixLanka/views/user/help-center.php\" class=\"quick-link-card\">\r\n            <div class=\"link-icon help\"><i class=\"fas fa-question-circle\"></i></div>\r\n            <h3>Help Center</h3>\r\n            <p>Browse frequently asked questions and find answers</p>\r\n        </a>\r\n        <a href=\"/2nd-Year-Group-Project/FixLanka/views/user/contact-us.php\" class=\"quick-link-card\">\r\n            <div class=\"link-icon contact\"><i class=\"fas fa-envelope\"></i></div>\r\n            <h3>Contact Us</h3>\r\n            <p>Get in touch with our support team directly</p>\r\n        </a>\r\n        <a href=\"/2nd-Year-Group-Project/FixLanka/views/user/terms-of-service.php\" class=\"quick-link-card\">\r\n            <div class=\"link-icon terms\"><i class=\"fas fa-file-contract\"></i></div>\r\n            <h3>Terms of Service</h3>\r\n            <p>Read our terms and conditions for using Fix Lanka</p>\r\n        </a>\r\n    </div>\r\n</div>\r\n\r\n<div class=\"contact-methods-section\">\r\n    <h2 class=\"section-title\">Get in Touch</h2>\r\n    <div class=\"contact-grid\">\r\n        <div class=\"contact-method\">\r\n            <div class=\"method-icon email\"><i class=\"fas fa-envelope\"></i></div>\r\n            <h3>Email Support</h3>\r\n            <p class=\"method-value\">support@fixlanka.lk</p>\r\n            <p class=\"method-description\">Response within 24 hours</p>\r\n        </div>\r\n        <div class=\"contact-method\">\r\n            <div class=\"method-icon phone\"><i class=\"fas fa-phone-alt\"></i></div>\r\n            <h3>Phone Support</h3>\r\n            <p class=\"method-value\">+94 11 234 5678</p>\r\n            <p class=\"method-description\">Mon - Fri, 9:00 AM - 6:00 PM</p>\r\n        </div>\r\n        <div class=\"contact-method\">\r\n            <div class=\"method-icon location\"><i class=\"fas fa-map-marker-alt\"></i></div>\r\n            <h3>Office Location</h3>\r\n            <p class=\"method-value\">Colombo, Sri Lanka</p>\r\n            <p class=\"method-description\">Visit us for in-person support</p>\r\n        </div>\r\n    </div>\r\n</div>\r\n\r\n<div class=\"support-topics-section\">\r\n    <h2 class=\"section-title\">Popular Topics</h2>\r\n    <div class=\"topics-grid\">\r\n        <div class=\"topic-card\">\r\n            <div class=\"topic-icon\"><i class=\"fas fa-user-circle\"></i></div>\r\n            <h4>Account Management</h4>\r\n            <ul class=\"topic-list\">\r\n                <li>Creating an account</li>\r\n                <li>Profile settings</li>\r\n                <li>Password recovery</li>\r\n                <li>Account verification</li>\r\n            </ul>\r\n        </div>\r\n        <div class=\"topic-card\">\r\n            <div class=\"topic-icon\"><i class=\"fas fa-tasks\"></i></div>\r\n            <h4>Job Requests</h4>\r\n            <ul class=\"topic-list\">\r\n                <li>Posting a job</li>\r\n                <li>Editing requests</li>\r\n                <li>Cancellation policy</li>\r\n                <li>Request tracking</li>\r\n            </ul>\r\n        </div>\r\n        <div class=\"topic-card\">\r\n            <div class=\"topic-icon\"><i class=\"fas fa-credit-card\"></i></div>\r\n            <h4>Payments &amp; Billing</h4>\r\n            <ul class=\"topic-list\">\r\n                <li>Payment methods</li>\r\n                <li>Pricing information</li>\r\n                <li>Refund policy</li>\r\n                <li>Invoice queries</li>\r\n            </ul>\r\n        </div>\r\n        <div class=\"topic-card\">\r\n            <div class=\"topic-icon\"><i class=\"fas fa-shield-alt\"></i></div>\r\n            <h4>Safety &amp; Security</h4>\r\n            <ul class=\"topic-list\">\r\n                <li>Verified providers</li>\r\n                <li>Data protection</li>\r\n                <li>Reporting issues</li>\r\n                <li>Trust &amp; safety</li>\r\n            </ul>\r\n        </div>\r\n    </div>\r\n</div>\r\n\r\n<div class=\"emergency-banner\">\r\n    <div class=\"emergency-content\">\r\n        <div class=\"emergency-icon\"><i class=\"fas fa-exclamation-triangle\"></i></div>\r\n        <div class=\"emergency-text\">\r\n            <h3>Need Urgent Help?</h3>\r\n            <p>For emergency support or urgent issues, please call our hotline immediately</p>\r\n        </div>\r\n        <a href=\"tel:+94112345678\" class=\"emergency-button\"><i class=\"fas fa-phone\"></i> Call Now</a>\r\n    </div>\r\n</div>', 'Published', '2026-04-08 12:41:08', 'support'),
(12, 'Landing Page Header', 'Hero title and subtitle shown on the landing page.', '{\"title\":\"Find Trusted Service Professionals Near You\",\"subtitle\":\"Connect with verified local experts for all your home and business needs\"}', 'Published', '2026-04-08 14:10:05', 'landing_hero');

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
-- Table structure for table `system_activity_logs`
--

CREATE TABLE `system_activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `user_role` enum('admin','moderator','system') NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_activity_logs`
--

INSERT INTO `system_activity_logs` (`id`, `user_id`, `user_role`, `activity_type`, `description`, `related_id`, `created_at`) VALUES
(1, '1', 'moderator', 'ad_rejected', 'Moderator #1 rejected advertisement: Nihal Constructions ', 19, '2026-02-20 06:48:57'),
(2, '1', 'admin', 'ad_override', 'Admin \'admin\' changed ad \'TEST - Rejected Ad for Override\' from \'rejected\' to \'approved\' (OVERRIDE)', 15, '2026-02-20 06:49:51'),
(3, '1', 'admin', 'ad_override', 'Admin \'admin\' changed ad \'Nimal Constructions\' from \'rejected\' to \'approved\' (OVERRIDE)', 7, '2026-02-20 07:08:25');

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
(1, '39f5c8be-0db3-4494-9d14-5e95193d91f1', '2026-04-07 16:18:33', 107, 'repairer', 'auth.logout', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"repairer\"}'),
(2, '36fba620-0a6a-419c-a8b4-12db1a52569c', '2026-04-07 16:18:57', 107, 'repairer', 'auth.login_success', 'repairer', '107', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"repairer\"}'),
(3, 'a9a26ae9-49da-45f5-b2fe-08c2a630fed6', '2026-04-07 16:19:54', 9997, 'company', 'api.settings.php.post', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/settings.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(4, 'da4f9eff-562f-4d59-a047-765007b0ff71', '2026-04-07 16:20:10', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(5, '53c163a6-5c4b-4bf8-aa7b-3d22ad1952fd', '2026-04-07 16:51:13', NULL, 'admin', 'auth.logout', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"admin\"}'),
(6, '916fd935-75b9-44d0-b0b9-c3de8f1131c4', '2026-04-07 16:51:29', 1, 'moderator', 'auth.login_success', 'moderator', '1', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"moderator\"}'),
(7, '0c8735bf-6c83-47c9-9f04-ada815434d77', '2026-04-07 18:37:27', 9997, 'company', 'api.settings.php.post', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/settings.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(8, 'de75d491-ac5e-4548-a29d-34917acd17dc', '2026-04-07 18:37:35', 9997, 'company', 'api.company-profile.php.get_profile', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(9, 'ce69598d-ebcc-4c63-8749-e8d33d3303ff', '2026-04-07 18:37:35', 9997, 'company', 'api.company-profile.php.get_bank_accounts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(10, 'b953cc7b-a0a0-4924-9a7a-374cd6a721de', '2026-04-07 18:47:50', 9997, 'company', 'api.company-profile.php.get_profile', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(11, '260339b7-377d-404d-8f78-6f79563d1044', '2026-04-07 18:47:50', 9997, 'company', 'api.company-profile.php.get_bank_accounts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(12, 'c427730a-d123-4767-9e7f-132c238107f4', '2026-04-07 18:47:53', 9997, 'company', 'api.company-profile.php.get_bank_accounts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(13, '2756b56d-146c-4f93-886b-67f39c25d475', '2026-04-07 18:47:53', 9997, 'company', 'api.company-profile.php.get_profile', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(14, 'b49c958c-1a01-4ebd-9c74-b2249345183c', '2026-04-07 18:47:54', 9997, 'company', 'api.company-profile.php.get_profile', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(15, '82e0d031-a709-467f-8a25-50e463e6e46c', '2026-04-07 18:47:54', 9997, 'company', 'api.company-profile.php.get_bank_accounts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(16, '6a3dfe50-e94e-451f-ad8a-4a6782f6656e', '2026-04-07 18:47:54', 9997, 'company', 'api.company-profile.php.get_profile', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(17, '561e458a-9164-44f1-80de-5b418d0cebca', '2026-04-07 18:47:54', 9997, 'company', 'api.company-profile.php.get_bank_accounts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(18, '748d9170-24d0-4a62-b5d8-a068bb605ae9', '2026-04-07 18:53:31', 9997, 'company', 'api.company-profile.php.get_profile', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(19, 'c274b8f2-e6a9-4859-91c5-59eb538abb36', '2026-04-07 18:53:31', 9997, 'company', 'api.company-profile.php.get_bank_accounts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(20, '9c1241c0-171b-45b7-9ba2-7fb70e657392', '2026-04-07 18:56:17', 9997, 'company', 'api.company-profile.php.get_bank_accounts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(21, 'e76be850-8387-4266-9e48-34e8016c4591', '2026-04-07 18:56:17', 9997, 'company', 'api.company-profile.php.get_profile', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(22, 'becb483b-e814-444e-8a6f-8efe4417a194', '2026-04-07 18:56:49', 9997, 'company', 'api.company-profile.php.get_profile', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(23, 'f1d88e3e-8de0-482e-835e-a41d6b69c130', '2026-04-07 18:56:49', 9997, 'company', 'api.company-profile.php.get_bank_accounts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(24, '41901583-bcaf-49e9-bf00-210da8825249', '2026-04-07 18:58:55', 9997, 'company', 'api.company-profile.php.get_bank_accounts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(25, '7fdeabf4-e3da-4c58-9186-fee10282ebe4', '2026-04-07 18:58:55', 9997, 'company', 'api.company-profile.php.get_profile', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/company-profile.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(26, 'fba49384-b5d4-4a56-9a54-5f95e2d8f2a3', '2026-04-07 22:57:24', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(27, '5da2a6fc-7f09-4e6b-abbe-d1c2a65ea8f7', '2026-04-08 10:21:42', 107, 'repairer', 'auth.login_success', 'repairer', '107', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"repairer\"}'),
(28, '9e51592c-069b-4b92-9f9f-04cf22442f8b', '2026-04-08 10:21:42', 107, 'repairer', 'auth.login_success', 'repairer', '107', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"repairer\"}'),
(29, '20aca1a8-0f3d-4ba9-876c-956a3adce0db', '2026-04-08 10:22:31', 9997, 'company', 'auth.login_success', 'company', '9997', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"company\"}'),
(30, 'b7d007f8-4cc3-4511-bd33-b365c3eb12b8', '2026-04-08 10:40:47', 9997, 'company', 'auth.login_success', 'company', '9997', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"company\"}'),
(31, 'b101d486-302e-4eba-979e-40e8b84f29fc', '2026-04-08 10:41:46', 107, 'repairer', 'auth.login_success', 'repairer', '107', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"repairer\"}'),
(32, '3219a126-b183-4701-9392-3b8063f9e51b', '2026-04-08 11:20:15', NULL, NULL, 'auth.login_failed', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 401, '{\"email_hash\":\"687311c006eaf507ac8f56d3f0575c232030e03c50bc7368fb443db3276310c2\"}'),
(33, '784693c9-0122-426b-bf65-10eeb65d6698', '2026-04-08 11:20:27', 1, 'moderator', 'auth.login_success', 'moderator', '1', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"moderator\"}'),
(34, 'a572a318-9f5c-4639-a101-8c8e606cfd9a', '2026-04-08 11:26:15', NULL, 'admin', 'auth.login_success', 'admin', 'admin', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"admin\"}'),
(35, 'd75ae541-c7fc-46c4-8a42-75da097488b9', '2026-04-08 12:31:49', 9997, 'company', 'api.settings.php.post', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/settings.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(36, 'bc5108cb-e61e-4071-bbe5-52fc1b5100c4', '2026-04-08 15:35:01', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(37, 'd901f16c-0e3c-4c73-99ef-314b1065c9fb', '2026-04-08 15:35:02', 9997, 'company', 'api.settings.php.post', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/settings.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(38, '924934b7-0e07-40a9-921c-77d88fcd63d9', '2026-04-08 15:39:30', 107, 'repairer', 'api.issue-reports.php.post', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/issue-reports.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(39, '841dedf5-0bd8-4868-9b42-386761b00961', '2026-04-08 15:53:07', NULL, 'admin', 'auth.login_success', 'admin', 'admin', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"admin\"}'),
(40, 'cfdd29b5-f428-406d-a4b7-248fff5cf057', '2026-04-08 16:03:32', NULL, 'admin', 'api.account-moderation.php.suspend', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/account-moderation.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[\"action\",\"account_id\",\"account_type\",\"duration\",\"custom_days\",\"reason\",\"notes\"]}'),
(41, '14d0126b-294b-4e86-9cce-ac7353806001', '2026-04-08 16:04:12', NULL, NULL, 'auth.login_failed', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 401, '{\"email_hash\":\"566ddc4dc482f499a5277500af8be0aae5132f98c2ffd4181bb20feaef38579f\"}'),
(42, '6431b44c-ca0b-4f9f-bd6e-73c9611ffdd4', '2026-04-08 16:04:25', NULL, 'admin', 'auth.login_success', 'admin', 'admin', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"admin\"}'),
(43, '7b49a879-2bdf-4996-b70b-6a30b7ec587f', '2026-04-08 16:44:23', NULL, 'admin', 'api.account-moderation.php.restore', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/account-moderation.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[\"action\",\"account_id\",\"account_type\"]}'),
(44, '0ede8aac-9e03-4166-8af3-9d4f553da279', '2026-04-08 16:44:51', NULL, 'admin', 'api.account-moderation.php.suspend', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/account-moderation.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[\"action\",\"account_id\",\"account_type\",\"duration\",\"custom_days\",\"reason\",\"notes\"]}'),
(45, '31e34bb3-1ef0-41d6-b074-50320a43add2', '2026-04-08 22:19:42', 9997, 'company', 'api.settings.php.post', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/settings.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(46, '56e0a259-5dc3-4b57-9302-161eed687040', '2026-04-08 22:21:19', 9997, 'company', 'api.settings.php.post', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/settings.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(47, '3608caff-233e-49b1-a561-a56712c82586', '2026-04-08 22:51:27', 107, 'repairer', 'auth.logout', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"repairer\"}'),
(48, 'c0ee0efa-43bd-4e5c-9d5a-4d9935514102', '2026-04-08 22:51:46', 9996, 'user', 'auth.login_success', 'user', '9996', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"user\"}'),
(49, 'fc5e4e42-a09d-42f2-aaa8-13589ba96164', '2026-04-08 23:20:55', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(50, '44f02a39-d305-4804-8548-18a3c54c390c', '2026-04-08 23:21:00', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(51, '5e896446-eed7-4e57-96d6-4e8c7fcf15f1', '2026-04-08 23:21:01', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(52, '5bd4489a-a471-4a2d-86ff-29499b5ec1d1', '2026-04-08 23:21:01', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(53, 'fd2f1c7e-9e10-4745-9eac-c996e49aac8b', '2026-04-08 23:33:35', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(54, '286f900f-e681-49f3-8331-3d1044e74458', '2026-04-08 23:33:37', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(55, '52d583dc-3872-4123-8263-5679545098a6', '2026-04-08 23:33:38', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(56, '9e35e754-c793-4f37-b43c-483e8020cb08', '2026-04-08 23:33:38', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(57, '34cba1f2-7e28-4fa7-9724-312187a08687', '2026-04-08 23:33:39', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(58, '1657f98c-4cac-42e0-b675-98341f1763f2', '2026-04-08 23:33:43', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(59, 'a2191837-e364-4583-93d3-2567910d267d', '2026-04-08 23:37:30', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(60, 'd9f04fa5-810f-4e2c-957e-477a2ecea856', '2026-04-08 23:37:30', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(61, 'f477cd07-6f61-4669-8c3c-eb7d6b98461d', '2026-04-08 23:37:31', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(62, '8e6922bf-36c1-444e-92f4-7e20b59f9424', '2026-04-08 23:38:37', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(63, '4504387d-f5f4-41c0-8059-80575fa80905', '2026-04-08 23:38:37', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(64, '0ee2e724-c198-417b-972a-735d55f0843f', '2026-04-08 23:38:42', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(65, 'e72b6286-73b5-491f-aad0-01355d839274', '2026-04-08 23:39:04', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(66, '1a6e0b5e-ccdc-412b-91b9-fe32f5cadc70', '2026-04-08 23:39:07', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(67, '6ef9bcb9-1751-40ec-a560-582b113ba8af', '2026-04-08 23:39:12', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(68, '861e32d2-e837-4a6d-94d5-8725dc9b9ee1', '2026-04-08 23:39:13', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(69, '44ff78de-8d87-4949-addf-795a60d26406', '2026-04-08 23:39:15', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(70, 'bbf91d3b-729c-4773-ae61-6a9a5c2ea03e', '2026-04-08 23:39:15', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(71, '0832714d-209f-4b8c-8891-81d81f3831d5', '2026-04-08 23:39:17', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(72, '03d00bd3-f7d6-46dd-8fbb-5aac21df9e6c', '2026-04-08 23:39:19', 9996, 'user', 'api.chat.php.post', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(73, '95ee382f-7d2d-4794-8531-9f73c6c23ddc', '2026-04-08 23:39:19', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(74, 'b1cff32d-8e26-441b-99f3-7040616d240e', '2026-04-08 23:39:20', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(75, '61ba5db4-b924-44dd-88ce-b85771044ff3', '2026-04-08 23:39:22', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(76, '7ecc7aee-881e-477c-95bb-70f9871de647', '2026-04-08 23:39:24', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(77, '0c23062b-a449-4004-97b3-561b92c5b1ef', '2026-04-08 23:39:26', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(78, '5da543c5-20d9-45c4-bdc1-18c63297545d', '2026-04-08 23:39:26', 9997, 'company', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(79, '80845ee5-6922-468c-a07f-35a118be3923', '2026-04-08 23:39:26', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(80, '13128570-a8c6-4892-8db3-1c082a3003d8', '2026-04-08 23:39:30', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(81, '5cd774bd-b971-42b5-9bb4-a557e25b70b0', '2026-04-08 23:39:33', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(82, '3aec1551-4bc3-45b2-987e-a94ba357af88', '2026-04-08 23:39:34', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(83, '6ddb9b4c-09b8-4253-bedc-7ca519eb7753', '2026-04-08 23:39:36', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(84, '3a67b7c8-884d-47b3-b5b0-a9b7365ddcc5', '2026-04-08 23:39:42', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(85, '7016dcc9-a422-42f5-b9cb-d5bce77678a8', '2026-04-08 23:39:46', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(86, '0e88883e-8636-436a-8486-c0a8fa5b09fb', '2026-04-08 23:39:51', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(87, 'ca0ff674-e749-4f03-90d2-a1e307d13761', '2026-04-08 23:39:56', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(88, '52ae9fa8-23ea-4fdf-a621-c80340247638', '2026-04-08 23:39:58', 9996, 'user', 'api.contracts.php.request_contract_change', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":{\"contract_id\":33},\"body_keys\":[\"action\",\"contract_id\",\"request_text\"]}'),
(89, '531efd35-be12-47c6-877b-00fc98252b4d', '2026-04-08 23:40:00', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(90, 'b6358ed4-cdf0-463b-9fda-a74633ead338', '2026-04-08 23:40:00', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(91, '631bbd5b-a037-4dff-89ca-43bc25b6de30', '2026-04-08 23:40:01', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(92, '8718dbc3-50c2-4cbe-82eb-615ea41be079', '2026-04-08 23:40:06', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(93, '9bc183f6-e671-4ac6-84e7-14a40c1a1854', '2026-04-08 23:40:06', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(94, 'd727c142-d153-45d9-9e07-3327ec1ca054', '2026-04-08 23:40:10', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(95, 'f0b34191-7731-4044-bc64-f9c24efc07d3', '2026-04-08 23:40:11', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(96, 'd53d7679-ad86-4ddf-a5dd-5f88e156d4ab', '2026-04-08 23:40:15', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(97, '7a9cfdca-7ac9-429a-b6dc-ff356abc9ef2', '2026-04-08 23:40:16', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(98, '14bc0d01-6b71-4295-8797-9d3361165634', '2026-04-08 23:40:20', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(99, 'eb80f269-b350-4500-9e6e-9595c125505c', '2026-04-08 23:40:26', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(100, '48134fb5-9755-438a-80f9-355a6edc3928', '2026-04-08 23:40:30', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(101, '2acd24c8-2534-4a11-9a55-3adffc419cc1', '2026-04-08 23:40:35', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(102, 'f174ed59-8ecd-415c-bb90-fbeba89b6faf', '2026-04-08 23:40:40', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(103, '49fbf202-b0eb-491d-a746-b5edbce562f7', '2026-04-08 23:40:45', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(104, '2c5ecbd3-026d-49a1-bffb-f8f16ed3952e', '2026-04-08 23:40:51', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(105, '6e941816-49d0-422c-95af-6f7e038efa8d', '2026-04-08 23:40:55', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(106, 'a6c5f0be-4857-4189-b77d-c171b291785a', '2026-04-08 23:41:00', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(107, 'ec2ad09e-2c61-4367-bddc-ca07cd4ba512', '2026-04-08 23:41:05', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(108, '7f150a1c-542b-41e4-aad2-88224135bba3', '2026-04-08 23:41:10', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(109, 'cbfe667f-7cb5-4e89-9a64-4005045ef115', '2026-04-08 23:41:16', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(110, '7e81eb76-8e9a-4a46-9e21-4b28c6bdc5ca', '2026-04-08 23:41:20', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(111, '4fa1ec6e-28ce-4c90-b13b-a2b6964bd106', '2026-04-08 23:42:13', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(112, 'e1df248f-2be9-4b75-a536-2f3887b9b3f8', '2026-04-08 23:43:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(113, '1388b0ce-988d-4bf1-a249-4338b9549556', '2026-04-08 23:44:13', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(114, 'ce80d92b-be39-4d3f-8e00-6d38e50fc0a1', '2026-04-08 23:45:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(115, 'e0db815c-9a97-4432-ad29-583841255fce', '2026-04-08 23:46:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(116, '6a38bcdf-229d-425f-93d5-4403067f7944', '2026-04-08 23:47:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(117, 'ae67fd04-de9b-4b72-8fa6-62a485a42eea', '2026-04-08 23:48:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(118, '2f33b824-b8f2-427f-aab7-b6a779ee1669', '2026-04-08 23:49:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(119, 'd1d385da-22c3-4f98-9b48-339a189be859', '2026-04-08 23:49:17', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(120, 'd4cd4e21-0b2d-4926-9f8e-269d0f415236', '2026-04-08 23:49:19', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(121, '034bb15a-faf3-4680-bec5-c08d346d8e00', '2026-04-08 23:49:21', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(122, '5ea4c5d6-ddef-4db7-9dc8-f9f762868ec2', '2026-04-08 23:49:21', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(123, '3146e303-4459-486c-9fda-e16490251d1c', '2026-04-08 23:49:21', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(124, 'b855c9ae-7005-4851-838d-627ed43b1d63', '2026-04-08 23:49:21', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(125, '4a362ad7-621d-4db3-bd52-4b172cbf540b', '2026-04-08 23:49:21', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(126, '6ee0cec5-f66e-441c-b8ac-c481ebb54427', '2026-04-08 23:49:22', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(127, 'f5736b9d-78e4-4e79-8898-7b99235e71f4', '2026-04-08 23:49:22', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}');
INSERT INTO `system_audit_log` (`audit_id`, `request_id`, `occurred_at`, `actor_user_id`, `actor_role`, `action`, `entity_type`, `entity_id`, `http_method`, `endpoint`, `ip_address`, `user_agent`, `status_code`, `details`) VALUES
(128, '4d3f8f25-13de-4aca-aaf4-c632b7a3b50f', '2026-04-08 23:57:17', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(129, 'c7e54b55-4f14-4580-88ea-b241510ea20c', '2026-04-08 23:57:20', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(130, 'e05f993c-1f0d-44a6-b2c1-670b8aec0f10', '2026-04-08 23:57:26', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(131, '71742a85-14c3-4f0f-b849-143aadc326da', '2026-04-08 23:57:32', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(132, '4d452266-feb7-4173-88f5-b173b64ee7c9', '2026-04-09 00:10:31', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(133, 'ed237c6f-8b97-4e11-89ff-705b43a8cc5e', '2026-04-09 00:12:07', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(134, '271d9155-2614-45bc-81e1-f5d1a5cd4005', '2026-04-09 00:12:14', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(135, 'fe4637e6-ebb1-47c9-84a6-3c74e9ad21b7', '2026-04-09 00:12:15', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(136, '5a44f706-c309-4b18-abf0-4ee8387db248', '2026-04-09 00:14:03', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(137, '571625de-2994-478c-915f-ddab3d240010', '2026-04-09 00:21:20', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(138, 'ba03bc8d-6258-4139-867e-aa91a65b72c2', '2026-04-09 00:28:48', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(139, 'e36f92f9-bb08-4aec-b48d-691696de317a', '2026-04-09 00:28:51', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(140, 'f4b3b8d3-b4f2-44c5-aac9-7aa217809858', '2026-04-09 00:32:06', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(141, '9defed34-927d-4e78-ad53-332117f90bf6', '2026-04-09 00:32:10', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(142, 'dbddbcb6-cb6e-4e90-a540-5602e21b6820', '2026-04-09 00:32:21', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(143, '2107afb7-abb7-43fd-8f99-d64ae193325c', '2026-04-09 00:32:42', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(144, '7951685c-2ebc-43dd-a2f3-09afa6dd0859', '2026-04-09 00:32:48', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(145, '9117464c-c0e0-45b5-acd3-81092eaff648', '2026-04-09 00:32:48', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(146, '1092c0bc-549d-475b-a099-7d15352ffd39', '2026-04-09 00:42:10', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(147, '90c7113e-ddc6-461d-aefa-28f1a56adc57', '2026-04-09 00:42:13', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(148, '1f5a20c3-ac62-4f46-bd0d-f1eb5fdbf430', '2026-04-09 00:42:15', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(149, '8fe3ed08-1bef-4439-930b-6d3892f6de06', '2026-04-09 00:59:13', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(150, '0868080e-f239-49e6-89a9-9b9ccd2358da', '2026-04-09 00:59:15', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(151, '2605a3b6-ce20-46f8-bb6c-265e16691d0a', '2026-04-09 00:59:15', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(152, 'd0357054-ad1e-4b42-843c-d32ffbc9d80a', '2026-04-09 00:59:15', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(153, '0f8f9fa7-6e04-4ec1-9c89-9d3d9ce2a6e2', '2026-04-09 00:59:16', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(154, 'b26b7308-2062-49f5-83bc-c81771ba4d37', '2026-04-09 00:59:16', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(155, 'e77cd73b-e2cc-4a7d-9e6e-0acb0f8b7fd0', '2026-04-09 00:59:17', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(156, 'f23d0210-38a4-48a9-a769-3168d3ca0f59', '2026-04-09 01:14:11', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(157, '3826d481-85d1-4a8e-9987-37fe552ed266', '2026-04-09 01:14:13', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(158, 'b9d46660-c20d-4a56-80c9-9257b6a5a787', '2026-04-09 01:14:30', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(159, 'e53a1044-f299-4b4f-8fce-aee382c5818d', '2026-04-09 01:14:32', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(160, '72481227-e3aa-4a1e-93a7-358c700f99ea', '2026-04-09 01:14:42', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(161, '06c735c1-92e5-4539-a53e-0cdf1ff7b456', '2026-04-09 01:14:44', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(162, 'ad14704a-ffd2-4ecd-8476-3496ede9dec6', '2026-04-09 01:18:18', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(163, 'b4af4702-d85b-4bac-9c69-333a3d1c9ff3', '2026-04-09 01:18:20', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(164, 'f347730c-1b4e-4272-abf8-87bcd621cfe4', '2026-04-09 01:19:15', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(165, 'd9c8b740-242f-4e20-9a2e-0da7a6c7b423', '2026-04-09 01:19:35', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(166, 'a150f3d0-30f3-4ac3-87f9-333cb524a0b5', '2026-04-09 01:19:41', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(167, '379ea860-3e48-4601-bbf8-76037db1f624', '2026-04-09 01:19:41', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(168, '0afdbdb9-683a-4924-8fa6-222fdd04a18c', '2026-04-09 01:21:35', 9996, 'user', 'api.contracts.php.request_contract_change', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":{\"contract_id\":33},\"body_keys\":[\"action\",\"contract_id\",\"request_text\",\"proposed_changes\"]}'),
(169, 'f7e84835-3fb3-4f4a-be4d-adb5a2357f8e', '2026-04-09 01:21:38', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(170, '986a169d-8d39-40ed-acfb-6a59665c5e22', '2026-04-09 01:21:38', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(171, 'e1887233-c8f8-4423-8b34-89fbc62a5368', '2026-04-09 01:21:40', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(172, '6163f12c-d4d3-4558-9e40-e523cd3515cf', '2026-04-09 01:21:40', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(173, '8340e93d-c6ae-48f3-8de3-bd8aabe44f80', '2026-04-09 01:21:41', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(174, '318f5a18-4725-41ae-82fa-160f112d54d7', '2026-04-09 01:21:41', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(175, '7e08bde4-4c63-4342-8bda-1a46cc5b09e6', '2026-04-09 01:21:42', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(176, 'a90d8555-94ab-4e15-b0fe-30162c299530', '2026-04-09 01:21:42', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(177, '599ae643-aaef-4559-8de4-391b1450afda', '2026-04-09 01:21:42', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(178, '95518c61-deb8-4b02-b838-1bc7b706e40e', '2026-04-09 01:21:42', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(179, '53981fd4-e041-46d5-91d5-4f392d553547', '2026-04-09 01:21:46', 9996, 'user', 'api.contracts.php.get_contract_change_preview', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(180, 'e44781af-01d7-4832-92a6-f6e42c5e8d85', '2026-04-09 01:21:47', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(181, '13f122dd-a4de-4a35-b05e-bccb23acdc4f', '2026-04-09 01:21:52', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(182, 'de28f2d1-e255-42e7-8dd8-0710dce2a8f3', '2026-04-09 01:21:52', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(183, 'fd1c24a0-3dc9-42e8-ac42-acdc828abd39', '2026-04-09 01:21:55', 9997, 'company', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(184, 'fb2ffe58-cf62-4644-89e0-e9cf142af2be', '2026-04-09 01:21:55', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(185, '4fcf455a-ed65-48d4-b36a-58b9d0185d00', '2026-04-09 01:21:57', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(186, '4c17ccbd-cbe3-468a-be6e-6664679cb7f8', '2026-04-09 01:22:00', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(187, '4cf1a930-7137-46b0-9f5e-33e1b751fa35', '2026-04-09 01:22:02', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(188, '049fec82-7b42-45cf-a895-dd2de31f7cd0', '2026-04-09 01:22:05', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(189, '28e71ec7-ec1c-466c-9165-eca303f93629', '2026-04-09 01:22:08', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(190, 'f499ac22-c8f8-47af-b842-bf2ffeb75875', '2026-04-09 01:22:10', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(191, 'c9b16e3f-ca8a-4425-a2ee-39c899b0234d', '2026-04-09 01:22:13', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(192, 'd2bf6fe9-7fc4-42df-8714-f0a5968817ed', '2026-04-09 01:22:15', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(193, '315ae774-8b83-47dd-bd4c-978812de7d04', '2026-04-09 01:22:17', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(194, 'faa5c98a-916c-4b90-b495-fb0d31031ed4', '2026-04-09 01:22:20', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(195, '627dd934-175e-4295-a55b-6dcddeb835a5', '2026-04-09 01:22:23', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(196, '72181de4-987d-4275-80d7-153514774526', '2026-04-09 01:22:25', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(197, 'c3edf08f-3b59-41e1-932d-b8dd0a332c45', '2026-04-09 01:22:27', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(198, '97354748-e451-4a11-93a3-20df3ef4bfba', '2026-04-09 01:22:30', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(199, '1413f23c-4add-4543-b10a-f000fd47c49d', '2026-04-09 01:22:32', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(200, '42075580-704e-4e1f-99fb-43284fb7d16f', '2026-04-09 01:22:35', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(201, 'b65b0a89-d184-49ba-b96d-c814b5df20b1', '2026-04-09 01:22:37', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(202, '8f425121-fbba-4beb-9955-861cf3a7cf97', '2026-04-09 01:22:40', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(203, '2c16faf3-ba24-4d09-b535-ebade60666a3', '2026-04-09 01:22:42', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(204, '7ce1996b-58d8-40d9-84d2-d098e846d361', '2026-04-09 01:22:45', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(205, 'a4de06d1-136f-407e-ae68-e16c319d46fb', '2026-04-09 01:22:47', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(206, '9f78d9d9-be08-4229-b68f-dae4bc8a48b4', '2026-04-09 01:22:50', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(207, 'af889123-ea05-4c96-90c3-d2bd0ca0aedd', '2026-04-09 01:22:56', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(208, '11d2e4b5-d04d-48f0-8d10-ae68f5044a98', '2026-04-09 01:22:57', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(209, '72d316be-6d77-4444-ab4b-35550b5a51d5', '2026-04-09 01:22:59', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(210, 'd3c50fa3-5135-441f-afe7-d5a1a153d9b8', '2026-04-09 01:22:59', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(211, 'a1e7f255-0e22-4620-9e20-7a3bdffb8b5e', '2026-04-09 01:23:04', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(212, 'ffacfabf-1716-4c50-a965-00519303ce2b', '2026-04-09 01:23:10', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(213, '0c3e5142-e779-44fe-9b6d-7cfd6dd74a91', '2026-04-09 01:23:15', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(214, 'a2e879ec-96a7-4278-b4ee-b6b93029dcd4', '2026-04-09 01:23:20', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(215, '6964afb6-e75a-4e68-8f51-850ffb9e1d3a', '2026-04-09 01:23:25', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(216, 'ca1867ee-b146-4e92-801d-83a045b1aef0', '2026-04-09 01:23:30', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(217, '6b9d523d-2969-437a-a663-2ceb54ecf36d', '2026-04-09 01:23:35', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(218, 'abdec3af-da91-4170-83ed-8df305607f16', '2026-04-09 01:23:40', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(219, '4d4d0cff-22e4-479a-b3cb-630705014f88', '2026-04-09 01:23:45', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(220, '74803bcb-f12a-4fcc-8e9a-686113845f8b', '2026-04-09 01:23:50', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(221, 'bce38958-a6ce-4f11-94e1-45a0da8ad67c', '2026-04-09 01:23:55', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(222, '39a597ac-b57a-4905-a39b-4db0de6a7864', '2026-04-09 01:24:00', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(223, '81ac1b6a-309b-4c88-9f99-98c3d6383813', '2026-04-09 01:24:05', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(224, '187e3b4e-0c09-4804-951e-11ee78ecd8da', '2026-04-09 01:24:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(225, 'f30252a6-ff91-48b2-89ce-af76a14d4b6a', '2026-04-09 01:25:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(226, '248f7723-9dba-4bfc-a096-4bf3d0710c6f', '2026-04-09 01:26:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(227, '5bb41607-dfc6-41f6-b2f5-cdf9a2dc2bd6', '2026-04-09 01:27:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(228, '1f5f67ea-065c-479f-ae57-8926e80a7b93', '2026-04-09 01:28:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(229, 'b16180ac-efd0-4af2-a64b-6af8de10798d', '2026-04-09 01:29:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(230, 'f6495d8f-fe92-497a-8a73-92df8a33e792', '2026-04-09 01:30:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(231, 'd19fe8b9-58e9-4d01-b060-d241de69b67f', '2026-04-09 01:31:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(232, '4a19979d-bd4f-4163-a849-4766d6b30cc2', '2026-04-09 01:32:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(233, '01797766-4f15-4d5f-b897-4dc0d93516fc', '2026-04-09 01:33:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(234, '26261126-2d65-41e2-a300-663da97e2a7a', '2026-04-09 01:34:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(235, '5787b27e-1b5a-4a74-a67a-ffdc869dfa4f', '2026-04-09 01:34:57', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(236, 'dc7e572e-ccb8-4803-a4c5-ec521bc1a4e4', '2026-04-09 01:34:59', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(237, '6075d95a-79f1-4ba7-982c-5b2864a2d89c', '2026-04-09 01:35:03', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(238, '93c45616-2b91-452f-874d-d933b5af635d', '2026-04-09 01:35:05', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(239, '932393d1-da86-4d80-91ed-5013578b402e', '2026-04-09 01:35:10', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(240, '04094951-efc3-4fe5-9533-254a78124621', '2026-04-09 01:35:15', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(241, '988299fe-8c57-45d2-9ba4-bbf10fd1723f', '2026-04-09 01:35:17', 9997, 'company', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(242, '0f74cc57-e3cf-4076-a49a-53efa7e01a8e', '2026-04-09 01:35:17', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(243, '4f9cb9bf-c066-43b5-a360-a4eb66a96f8f', '2026-04-09 01:35:20', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(244, 'cbd7f7cc-cd5f-489f-9832-43a5c86a9d3e', '2026-04-09 01:35:22', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(245, '902913c2-1365-4639-aff2-2cfeee6ff564', '2026-04-09 01:35:23', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(246, '82b00acc-a9ff-4407-a4cc-72b1c10eba21', '2026-04-09 01:35:25', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(247, 'c9011aa4-176c-4a49-9ce9-27bf48f52ee1', '2026-04-09 01:35:26', 9997, 'company', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(248, '1a901203-1d28-465a-b925-a3ac7d96bf05', '2026-04-09 01:35:26', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(249, 'c87573c1-597e-4b34-ae2d-e46a1100a74c', '2026-04-09 01:35:28', 9997, 'company', 'api.contracts.php.get_contract_change_preview', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(250, '0fee1cdf-0315-4ea7-83dd-53f74c6b0b6c', '2026-04-09 01:35:30', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(251, '49213bca-3fcb-4365-8db7-f05abc59a94d', '2026-04-09 01:35:31', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}');
INSERT INTO `system_audit_log` (`audit_id`, `request_id`, `occurred_at`, `actor_user_id`, `actor_role`, `action`, `entity_type`, `entity_id`, `http_method`, `endpoint`, `ip_address`, `user_agent`, `status_code`, `details`) VALUES
(252, '6bb8b04e-dca4-46f2-96c8-67b5503f10bd', '2026-04-09 01:35:35', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(253, '95140bb6-a401-48ee-a3d0-91e1bed69a08', '2026-04-09 01:35:36', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(254, '2d3b9c39-e2c0-46b2-9c8c-35a373c8c34b', '2026-04-09 01:35:40', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(255, 'ae540dce-2228-4989-bfb7-ff06393d3175', '2026-04-09 01:35:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(256, 'd7ca6efb-cb71-4f9a-b837-387a380a473a', '2026-04-09 01:35:45', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(257, 'b7e8b38e-3385-4197-a863-0575d3112a91', '2026-04-09 01:35:46', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(258, '67ab1a13-aabe-4677-bc24-a9634fe2c921', '2026-04-09 01:35:50', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(259, '11fd8151-be08-43d2-86b3-db676b8bb894', '2026-04-09 01:35:51', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(260, '33d0078f-f332-44c9-82ee-f20a291cef6f', '2026-04-09 01:35:55', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(261, '68f0da3c-319f-440a-bd62-39026da54653', '2026-04-09 01:35:56', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(262, '64287b16-d875-4af3-922c-0147d2308d23', '2026-04-09 01:36:01', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(263, 'bafcbf55-336e-47bb-87d3-cb8f4c467a08', '2026-04-09 01:36:06', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(264, 'f7257ad5-3b97-4404-830a-04b699c5080d', '2026-04-09 01:36:11', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(265, '0e5eb4bb-6b7e-4028-af65-e488d00943c5', '2026-04-09 01:36:12', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(266, '74dd582b-71c8-4085-93b3-24dbd5c96f39', '2026-04-09 01:36:16', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(267, 'ef5a79f1-af60-498a-b3ca-f1087c9b3325', '2026-04-09 01:36:21', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(268, 'd644af34-884c-4f9d-a3db-cd04fe0a705a', '2026-04-09 01:36:26', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(269, 'b7d4e83f-3b02-429d-b2d2-41d7503caa73', '2026-04-09 01:36:32', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(270, 'e9c725ec-7a5e-47cf-a71a-11b35b5d6045', '2026-04-09 01:36:36', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(271, '6565f054-3367-40b8-bcb7-7e8a6fd770b6', '2026-04-09 01:36:38', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(272, 'f41025bd-3e03-4468-91db-6836b72b9f27', '2026-04-09 01:36:39', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(273, '7a861bb2-ff47-40ec-bc9a-beac941f8f97', '2026-04-09 01:36:39', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(274, '7680c599-6cf9-4a64-8166-aac4ba5115ad', '2026-04-09 01:36:42', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(275, 'd89adbe4-7877-4738-93d9-0c3131d74041', '2026-04-09 01:36:43', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(276, '9540ada2-556b-4cf7-9496-f8278a2dbc19', '2026-04-09 01:36:45', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(277, '78d5b8c6-7968-4181-b6e6-ede175aacfcd', '2026-04-09 01:36:45', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(278, 'e06b8af6-0eb0-42e7-9227-bdc6fc178a9d', '2026-04-09 01:38:51', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(279, '267e148e-4ad0-4538-9cc2-f5cb5f79721e', '2026-04-09 01:38:52', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(280, 'd0d20e3c-82a3-4661-813c-e3609b8e90bb', '2026-04-09 01:38:55', 9997, 'company', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(281, '40b2a7e6-7b6c-4dfb-8408-9cfb0d39f850', '2026-04-09 01:38:55', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(282, '80150de7-83cd-42e0-8ad6-17b2eed66cd4', '2026-04-09 01:38:57', 9997, 'company', 'api.contracts.php.get_contract_change_preview', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(283, '96150e36-1f38-4cae-8b96-48870fc041e2', '2026-04-09 01:39:00', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(284, '6faf7427-a252-45c8-baa5-2d06bd5637b1', '2026-04-09 01:39:04', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(285, 'a0eeca80-54d8-4c77-99d5-3e638ed8e9d3', '2026-04-09 01:41:26', 9997, 'company', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(286, '19ed5144-a5d4-4eb1-b528-a73ef62e49f4', '2026-04-09 01:41:26', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(287, '282b7b00-cc21-4653-97b6-129e7ef6906a', '2026-04-09 01:41:29', 9997, 'company', 'api.contracts.php.respond_contract_change', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[\"action\",\"change_request_id\",\"decision\",\"response_note\"]}'),
(288, '6ef84860-bca0-403c-8035-940551bbfc86', '2026-04-09 01:41:29', 9997, 'company', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(289, '2669a261-8c98-43b9-9219-6534076aec67', '2026-04-09 01:41:29', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(290, '606d1165-8551-4edc-af05-4bc3ad3848f0', '2026-04-09 01:41:30', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(291, '8b0d5f4c-46d5-4796-94dd-f7ca5d901277', '2026-04-09 01:41:36', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(292, 'f137b2a6-9709-4a26-b2ed-244591a8ef39', '2026-04-09 01:41:39', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(293, '7474f6a9-3e8e-43eb-beca-9fabd3ac2fcb', '2026-04-09 01:42:24', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(294, '2a08822f-df91-44cd-bd76-027fccb511ea', '2026-04-09 01:42:31', 9996, 'user', 'api.contracts.php.respond', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":{\"contract_id\":33},\"body_keys\":[\"action\",\"contract_id\",\"response\",\"esign_consent\"]}'),
(295, 'e1016704-440a-4ad7-86e3-6640151335e9', '2026-04-09 01:47:57', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(296, '7cd0b61b-c0d4-4801-85ba-f2f580a807f7', '2026-04-09 01:47:59', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(297, '0257f0f6-90ac-44e0-bcf1-dcbf1c1f3848', '2026-04-09 01:48:02', 9996, 'user', 'api.contracts.php.respond', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":{\"contract_id\":33},\"body_keys\":[\"action\",\"contract_id\",\"response\",\"esign_consent\"]}'),
(298, '404c0799-8b02-4006-8e8a-b35d2ccd6f28', '2026-04-09 01:48:04', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(299, '0ab44462-a2af-44b1-a8b7-0820b6b401d5', '2026-04-09 01:48:23', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(300, 'c807b5a4-0d3a-44a3-8fdb-c4545afd85ec', '2026-04-09 01:48:37', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"33\"},\"body_allow\":[],\"body_keys\":[]}'),
(301, 'a595f0b5-1099-46d6-86e1-f862087b8c13', '2026-04-09 01:48:52', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(302, '3590ba5f-a12a-47ac-8540-fdf18a1cb103', '2026-04-09 21:00:42', 9997, 'company', 'auth.login_success', 'company', '9997', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"company\"}'),
(303, '055c3db1-1ece-4c24-9d7d-3c26ad1c5779', '2026-04-09 21:00:48', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(304, 'f297a3f9-08c7-4b30-b10c-d01d788d83fb', '2026-04-09 21:00:48', 9997, 'company', 'api.settings.php.post', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/settings.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(305, '32725e4c-063c-4942-b633-331d7c8e2b21', '2026-04-09 21:00:51', 9997, 'company', 'api.settings.php.post', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/settings.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(306, '7b312956-ca7e-4820-b2cf-c93d846c310e', '2026-04-09 21:00:53', 9997, 'company', 'api.settings.php.post', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/settings.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(307, 'e8218512-25b1-4175-93cd-32eb4945a1b7', '2026-04-09 21:00:57', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(308, '2f2bfa41-2d83-4979-a3c6-c978a886758b', '2026-04-10 11:25:10', 9997, 'company', 'auth.login_success', 'company', '9997', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"company\"}'),
(309, 'c525ddae-f80b-45f7-95b0-2091d7fda496', '2026-04-10 11:33:34', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(310, '393a3721-a62a-4397-be39-383af489468b', '2026-04-10 11:33:40', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(311, '90bf973f-0872-4d69-816b-e511aa23e561', '2026-04-10 11:43:09', 9997, 'company', 'auth.login_success', 'company', '9997', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"company\"}'),
(312, '560510d8-138c-4ff0-8d18-fb81b0a55e4b', '2026-04-10 11:43:53', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(313, '326f542d-31a7-4e07-92b8-2a9ea5fc56d4', '2026-04-10 11:44:04', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(314, 'c31829d9-6939-46d2-bbc7-046d331da296', '2026-04-10 11:56:28', 9997, 'company', 'api.projects.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(315, 'd71cfa71-ea85-4f31-a124-dc7d7bf8377a', '2026-04-10 11:56:31', 9997, 'company', 'api.projects.php.list_startable_contracts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(316, 'e594a45a-7cdd-45bc-9e28-ecf12a0bbcbd', '2026-04-10 11:56:42', 9997, 'company', 'api.projects.php.delete', NULL, NULL, 'DELETE', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"project_id\":\"28\"},\"body_allow\":[],\"body_keys\":[]}'),
(317, '7bae1e9c-1619-4665-b785-ee2fb5e8386b', '2026-04-10 11:56:42', 9997, 'company', 'api.projects.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(318, '18d28952-c31e-4064-9145-762694904127', '2026-04-10 11:56:46', 9997, 'company', 'api.projects.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(319, '39830db0-913c-4edf-a124-1b0e336ec95d', '2026-04-10 11:56:48', 9997, 'company', 'api.projects.php.list_startable_contracts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(320, 'd93c3562-6fcd-4b62-9591-2d0a2d45a739', '2026-04-10 11:57:15', 9997, 'company', 'api.projects.php.list_startable_contracts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(321, 'b6c54116-1ee2-49de-ae35-28f7220452de', '2026-04-10 12:25:15', NULL, NULL, 'api.projects.php.list_startable_contracts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8115', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(322, 'f32f641f-9e13-4190-a1a5-8958f1b44e08', '2026-04-10 12:28:37', NULL, NULL, 'api.projects.php.list_startable_contracts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8115', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(323, '8e048422-57ec-4f5a-b99c-dcafacecbae5', '2026-04-10 12:29:42', NULL, NULL, 'api.projects.php.list_startable_contracts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8115', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(324, '098f70b0-dded-412b-9e3d-442ea1189d29', '2026-04-10 12:35:23', NULL, NULL, 'api.projects.php.list_startable_contracts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.8115', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(325, '17b4555c-4bb8-4002-8c22-6e3f093c2450', '2026-04-10 12:39:55', 9997, 'company', 'api.projects.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(326, '467135fe-e780-4f12-ade6-05f42a3c7af2', '2026-04-10 12:39:57', 9997, 'company', 'api.projects.php.list_startable_contracts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(327, '120e3618-68fd-4ce8-a5d0-1cdf4fa8827f', '2026-04-10 12:40:04', 9997, 'company', 'api.projects.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(328, '9108249b-8434-4d3c-af3d-41534b5c0888', '2026-04-10 12:40:06', 9997, 'company', 'api.projects.php.list_startable_contracts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(329, '003dbbbc-ce48-4056-93f9-c70621ae4ea0', '2026-04-10 12:50:26', 9997, 'company', 'api.projects.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(330, '08ae7438-4330-4c40-8055-dfbf53449449', '2026-04-10 12:50:28', 9997, 'company', 'api.projects.php.list_startable_contracts', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/projects.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(331, '0195d6bc-c53d-4139-baa1-178545b20b4b', '2026-04-10 12:54:39', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(332, '19cda8f7-a047-4b79-9d67-3879365f97a6', '2026-04-10 13:05:15', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(333, '9eed0c15-a145-46f8-af27-8504da0e3e0b', '2026-04-10 13:05:32', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(334, 'a0f53ccc-109a-41ff-8ee8-f2fae9e724b0', '2026-04-10 13:24:20', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(335, '750ace58-9c76-4488-bf81-3098d37c3367', '2026-04-10 13:24:22', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(336, '5fb34cee-5d4f-4ed2-8c26-c3992fb1820f', '2026-04-10 13:24:22', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(337, 'e2f322ce-0faa-4cd9-b18f-8d46c993f78f', '2026-04-10 13:24:23', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(338, 'b0fad303-2b0d-43a3-9d57-535e4d88bf9a', '2026-04-10 13:24:25', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(339, '9af6ad8a-989b-43b0-b086-4e6032a38251', '2026-04-10 14:16:36', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(340, '3642245e-f447-4b2e-b170-86cff492577d', '2026-04-10 14:16:37', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(341, 'daed984f-4802-4bbe-8eb7-e7221ae14914', '2026-04-10 14:16:38', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(342, '9c3e1c40-5af8-4623-bea7-ed07e89a1354', '2026-04-10 14:16:41', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(343, 'ca9205bb-e8b6-4714-a26c-837834f0aceb', '2026-04-10 14:16:42', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(344, '030e514e-847e-4a19-a94b-2feffdc0c095', '2026-04-10 14:16:43', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(345, 'd0a2ad63-af32-4b7a-b397-b75e8087b20c', '2026-04-10 14:16:43', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(346, '8cc44207-3780-4a1f-9511-7e357951303b', '2026-04-10 14:16:43', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(347, 'c7629cfe-513a-47af-bfd7-da638ef8b330', '2026-04-10 14:16:48', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(348, 'b5f5468c-3032-4547-a5cb-e22db540a138', '2026-04-10 14:16:48', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(349, '1cfde46c-7d17-43b0-a088-57427fc124b0', '2026-04-10 14:16:48', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(350, '314b818f-8c98-4b21-9950-613351b0c00e', '2026-04-10 14:16:49', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(351, '03e542b3-5154-4343-ab5c-fe79c97c1f46', '2026-04-10 14:16:49', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(352, '60ab77f1-182e-435b-af99-957d6f51dd35', '2026-04-10 14:18:25', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(353, '95a10142-0ecc-494e-96fb-2b96422f7195', '2026-04-10 14:18:26', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(354, '6ffcc782-c030-4be6-8e53-3e09ecc88d56', '2026-04-10 14:18:32', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(355, '868207a6-f4f1-40d9-8a49-4d913647c14b', '2026-04-10 14:18:32', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(356, '61a059aa-f4ab-44c1-8910-1abd3dcb2f0c', '2026-04-10 14:18:37', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(357, '7574393c-07ec-4638-a2c9-773acc106017', '2026-04-10 14:18:37', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(358, 'b0ec2261-1a56-4e35-9a6e-72b9cdf840af', '2026-04-10 14:18:57', 9996, 'user', 'auth.login_success', 'user', '9996', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"user\"}'),
(359, '41bc18a9-59f9-4702-90a3-b66c9264058b', '2026-04-10 14:19:01', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(360, '5ec4686c-9d06-4306-b80a-bd9323537840', '2026-04-10 14:19:27', 9996, 'user', 'api.user-quotes.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"request_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(361, '1c1e6a90-11f6-4831-9a73-05172d70cdfd', '2026-04-10 14:23:22', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(362, 'd22cbec8-b917-45c4-a4d8-06314eb77b70', '2026-04-10 14:23:22', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(363, 'd91bd2fa-5e66-4c3a-8f4d-afdef7336a6f', '2026-04-10 14:23:22', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(364, '6cfa2219-235c-4e1a-9c4b-bc521805fd4c', '2026-04-10 14:23:23', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(365, '3441ff48-ad45-4d25-a863-d370d9826129', '2026-04-10 14:23:25', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(366, '4588678e-e144-45c8-9be8-1c9fb9d3899c', '2026-04-10 14:23:25', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(367, '390cbfe0-c17a-4d94-aa3c-703f3fe6d1a6', '2026-04-10 14:23:48', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(368, 'ade065ce-041f-4017-97f6-fce31876c96d', '2026-04-10 14:23:54', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(369, '1517107a-7b75-4b8d-82f6-c595a4a10efe', '2026-04-10 14:23:54', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(370, 'b332d547-e050-4dc4-9c78-9ee1eb08f7b8', '2026-04-10 14:27:57', 107, 'repairer', 'auth.login_success', 'repairer', '107', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"repairer\"}'),
(371, '0e6119cd-2872-4134-b768-b91c10b3e068', '2026-04-10 14:32:32', 9996, 'user', 'api.user-quotes.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"request_id\":\"35\"},\"body_allow\":[],\"body_keys\":[]}'),
(372, 'a0dadf5d-d3ba-44f9-92e3-9a6bb54d6131', '2026-04-10 14:32:46', 9996, 'user', 'api.user-quotes.php.respond', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(373, 'b398b1e1-cc79-40dc-8223-74a475e38c0b', '2026-04-10 14:33:21', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(374, '4e07d7d0-8247-405e-9a13-b2eb945a0ce4', '2026-04-10 14:33:22', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(375, 'd02071e5-b96b-429a-b474-7af09e5616cc', '2026-04-10 14:33:23', 9997, 'company', 'api.contracts.php.getAcceptedQuotations', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(376, '3febb36b-866d-4962-ad0f-30e8ecbd75ab', '2026-04-10 14:43:43', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(377, '00a8406e-04e4-45f3-95b1-05b090e24f7a', '2026-04-10 14:43:43', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}');
INSERT INTO `system_audit_log` (`audit_id`, `request_id`, `occurred_at`, `actor_user_id`, `actor_role`, `action`, `entity_type`, `entity_id`, `http_method`, `endpoint`, `ip_address`, `user_agent`, `status_code`, `details`) VALUES
(378, 'a831a42f-d97e-4caa-b18c-3218ac3e5eea', '2026-04-10 14:43:45', 9997, 'company', 'api.contracts.php.getAcceptedQuotations', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(379, '1c749d80-0c94-46a2-88d0-2d5b2b4e3347', '2026-04-10 14:46:09', 9997, 'company', 'api.contracts.php.create', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":{\"quotation_id\":\"43\",\"request_id\":\"35\",\"job_request_id\":\"35\"},\"body_keys\":[\"quotation_id\",\"request_id\",\"job_request_id\",\"customer_id\",\"contract_date\",\"project_title\",\"project_reference\",\"project_location\",\"project_type\",\"project_description\",\"scope_description\",\"scope_inclusions\",\"scope_exclusions\",\"scope_standards\",\"materials_responsibility\",\"start_date\",\"end_date\",\"estimated_duration\",\"total_budget\",\"budget_type\",\"budget_min\",\"budget_max\",\"tax_inclusive\",\"payment_method\",\"pricing_type\",\"hourly_rate\",\"spending_cap\",\"late_payment_penalty\",\"pause_work_clause\",\"time_extension_clause\",\"variation_clause\",\"communication_channel\",\"dispute_resolution\",\"warranty_period\",\"payment_terms\",\"additional_terms\",\"milestones\",\"send_to_customer\"]}'),
(380, '31eeabff-ee3f-407d-8f9a-dcbd17ca9e1a', '2026-04-10 14:46:10', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(381, 'a03e4c2d-333a-42ac-9a73-652432529237', '2026-04-10 14:46:32', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(382, '98fc5900-8ec1-4e0f-9756-5454c97fcf7c', '2026-04-10 14:48:53', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(383, 'c93cb874-3923-4cb5-90cf-1989af90f242', '2026-04-10 14:49:25', 9996, 'user', 'api.contracts.php.request_contract_change', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":{\"contract_id\":34},\"body_keys\":[\"action\",\"contract_id\",\"request_text\",\"proposed_changes\"]}'),
(384, 'd475e574-67f9-4098-a91f-7d8294e1d129', '2026-04-10 14:49:29', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(385, '9aebdd5a-5fc8-4c1f-8e83-ca3cb2100df7', '2026-04-10 14:49:29', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(386, '104f318a-c106-4e53-98b3-8b78d3292118', '2026-04-10 14:49:34', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(387, '80d076c2-59bf-45af-9362-feb01bdeaccc', '2026-04-10 14:49:40', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(388, '7ae7c1bc-c4bc-44a3-b919-28d728def088', '2026-04-10 14:49:45', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(389, '6374f30f-448c-49e8-8cff-ec5b76112a54', '2026-04-10 14:49:49', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(390, 'c8e9ee29-0a3e-4e3b-8669-2c321228bf68', '2026-04-10 14:49:50', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(391, '3e19d583-efc4-49dc-9dc6-4e93f9ef296c', '2026-04-10 14:49:55', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(392, 'c9f69924-8a33-4245-a8a3-73876319f15d', '2026-04-10 14:50:00', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(393, 'b80ff315-bcd2-4c6f-8994-b9b223840676', '2026-04-10 14:50:05', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(394, '48ff38e9-4720-4ecc-a86c-71705c82b449', '2026-04-10 14:50:10', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(395, '2608acb6-6b6a-492a-819a-8c732d570fa3', '2026-04-10 14:50:15', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(396, '5078547e-8f8e-4061-8c72-fb8a63aa5dbd', '2026-04-10 14:50:20', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(397, 'b4077fde-e75a-42d7-b7ee-25ed5df5b341', '2026-04-10 14:50:25', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(398, 'a5660932-b965-44c1-aece-ad445270ae8c', '2026-04-10 14:50:30', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(399, '33c43390-3ca9-4c5e-b94b-36d8bd2ca7bb', '2026-04-10 14:50:41', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(400, 'c9659a25-0efe-4888-bc5c-a942ee9354b2', '2026-04-10 14:51:41', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(401, '96a61ef1-d28d-4b22-a170-5403a280b9f5', '2026-04-10 14:51:44', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(402, '4c1c7203-7829-4741-b4b9-384cc4715c59', '2026-04-10 14:51:46', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(403, 'a9abff92-9623-49a6-8515-3000f90a57dd', '2026-04-10 14:51:50', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(404, '4b24f65e-8d72-40f1-9450-0343491fe14e', '2026-04-10 14:51:52', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(405, '9489385d-0894-4050-ad96-c5314ef8d1a0', '2026-04-10 14:54:21', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(406, 'afa7d9fd-e2a1-4803-a717-bbc859a351ca', '2026-04-10 14:54:25', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(407, '57ed51f8-104b-4f5e-a40b-ab8fae6e29f5', '2026-04-10 14:54:30', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(408, '1287eaf3-8ca1-4a67-99c2-2146bbef128a', '2026-04-10 14:54:33', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(409, '1c4aeb4c-5bd7-4023-b87f-4646e69f8b1f', '2026-04-10 14:54:33', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(410, 'ae2c8dcd-3f7b-46ca-a7d9-5b7b593d5d1c', '2026-04-10 14:55:02', 9996, 'user', 'api.contracts.php.request_contract_change', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":{\"contract_id\":34},\"body_keys\":[\"action\",\"contract_id\",\"request_text\",\"proposed_changes\"]}'),
(411, '63dfc0ca-c613-41d3-ba7b-a047b7256a4f', '2026-04-10 14:55:04', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(412, '105b36cd-b044-4119-9569-0c3a03cf9c81', '2026-04-10 14:55:04', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(413, '814967a8-e3ac-41c0-a329-86227d275c07', '2026-04-10 14:55:10', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(414, 'a145d521-1c41-4496-b559-209dbc7ed74f', '2026-04-10 14:55:12', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(415, '934467e0-147b-4d19-a8f7-b71f1713a1b7', '2026-04-10 14:55:15', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(416, '4a42d933-5475-46b8-b3c0-aeaf1692f102', '2026-04-10 14:55:20', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(417, '9aba6687-aeab-4b5a-9f4a-0a69a6bbdc7e', '2026-04-10 14:55:25', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(418, '96098735-e382-470b-9de1-156d4d4e71fd', '2026-04-10 14:55:30', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(419, '17d6121a-010c-4492-91fd-bd24f088487e', '2026-04-10 14:55:35', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(420, '23ce7d2d-9c05-48f4-a457-d2a71916e152', '2026-04-10 14:55:40', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(421, '71d94059-767f-4be0-9e2e-cc9c2b5144ff', '2026-04-10 14:55:45', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(422, '01fe6c3a-0b47-42ee-a103-e87c0a23e4a8', '2026-04-10 14:55:50', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(423, '2ed23c25-08c2-453d-a4d4-c8e3da6cd4b6', '2026-04-10 14:55:55', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(424, 'f1d4530d-95b4-4642-bfcb-f6d68afaf22b', '2026-04-10 14:56:00', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(425, '690350f8-3b33-448f-a6ff-006ff8b17369', '2026-04-10 14:56:05', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(426, 'af9a48e2-2994-4526-aa1f-3d1f74e6e261', '2026-04-10 14:56:41', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(427, '3eaebfd4-7427-4339-a417-5f419c50f112', '2026-04-10 14:57:18', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(428, 'ceb88155-300b-4af9-8a7c-0f87a9a2a23c', '2026-04-10 14:57:21', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(429, 'bb8aaced-833e-4113-9ded-0c26bbea4094', '2026-04-10 14:57:22', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(430, 'aa79f461-2f69-4616-84d7-35e75889b079', '2026-04-10 15:01:57', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(431, 'a164e7b6-dc3c-4c94-9693-a0f9fafc0c24', '2026-04-10 15:02:02', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(432, '5045115d-ad2d-4c5e-adcc-308cd1fd2c66', '2026-04-10 15:02:05', 9997, 'company', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(433, '5f9426c1-540c-4430-8d94-247f7e4b17a3', '2026-04-10 15:02:05', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(434, '72b761a9-2db1-4b16-b3b9-7549005adeef', '2026-04-10 15:02:18', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(435, 'c7348f46-f84a-446d-834c-646987edcb27', '2026-04-10 15:02:18', 9997, 'company', 'api.contracts.php.respond_contract_change', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[\"action\",\"change_request_id\",\"decision\",\"response_note\"]}'),
(436, '3a2ba837-63dc-4f80-bc8f-d787d4adda76', '2026-04-10 15:02:18', 9997, 'company', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(437, '12125dcb-2968-43b9-a5c9-482428484359', '2026-04-10 15:02:18', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(438, '0a0f6bed-f10a-4780-a098-9a2139b1a77a', '2026-04-10 15:02:20', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(439, 'a71312ad-a19a-4b97-b164-ea74eb988492', '2026-04-10 15:02:21', 9997, 'company', 'api.contracts.php.respond_contract_change', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[\"action\",\"change_request_id\",\"decision\",\"response_note\"]}'),
(440, 'a9007798-9ee6-4a9d-b8d7-2f62319c63ea', '2026-04-10 15:02:21', 9997, 'company', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(441, 'fb215feb-4910-49e3-a19f-37f4c18ba6b1', '2026-04-10 15:02:21', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(442, '0c45e819-000a-45c4-8d83-3c2875cebbd9', '2026-04-10 15:02:25', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(443, 'aa83b7d0-7b90-46e7-b06b-9904e5e4ac23', '2026-04-10 15:02:29', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(444, 'd8586196-3855-4f90-83d2-c1b09e7188fe', '2026-04-10 15:02:31', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(445, 'a50c0987-3da0-435e-9068-0a02e4ba6875', '2026-04-10 15:02:36', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(446, 'a32380e5-99cd-4f83-8c4f-466e82accf94', '2026-04-10 15:02:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(447, 'ff0c85e9-be45-4d4a-a1a5-8bc18719bcb0', '2026-04-10 15:02:46', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(448, '9626f71c-fbb4-408e-a7d8-5558a58a887e', '2026-04-10 15:02:51', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(449, '6d92997d-7595-4246-ac97-bd65ce7855c5', '2026-04-10 15:02:56', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(450, 'a4edef34-bdd8-49dc-bff8-2c437feb9b65', '2026-04-10 15:03:01', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(451, 'efdd571b-2903-47a2-ba5d-70c9769fc7e7', '2026-04-10 15:03:06', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(452, '87fbd0ce-83e8-4378-95d9-55ee9c5e3254', '2026-04-10 15:03:11', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(453, '7e798474-9291-44bd-a51a-8051574b2a18', '2026-04-10 15:03:16', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(454, '7b8077ea-0872-4328-927a-6b147df6bb6d', '2026-04-10 15:03:21', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(455, 'e59bac88-5553-4bbe-b678-0cd2095bf2b1', '2026-04-10 15:03:26', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(456, '5be13edc-cb7b-4345-b506-e84bdc4cf8e0', '2026-04-10 15:03:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(457, 'ce9d589d-585a-41c5-a52a-32f158c3cbe4', '2026-04-10 15:04:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(458, '3c46d996-5646-43e4-876f-278ea3eb1f9b', '2026-04-10 15:05:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(459, '72c1b3b4-88fb-4858-8207-664954f5c1b3', '2026-04-10 15:06:26', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(460, '42496634-5073-48ad-87bf-c7f7cd3d7040', '2026-04-10 15:06:35', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(461, '1413a513-d80f-4c7c-80ea-1a2680ec7afb', '2026-04-10 15:06:36', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(462, '5fca4f36-2cfb-4b22-848e-45d0fea951e7', '2026-04-10 15:06:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(463, 'b0ab3489-b021-4c57-9f7f-eb98c82223bb', '2026-04-10 15:07:21', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(464, '7c45e90a-01b0-4cb7-a787-cae33d4d22aa', '2026-04-10 15:07:38', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(465, 'df07158a-5105-49f8-a673-a6cb9e5c96a8', '2026-04-10 15:07:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(466, 'c1c3ea28-776c-45d2-9c95-353ca9e12306', '2026-04-10 15:08:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(467, '6106bf15-44fc-4030-8d42-e9ff63cdbd5d', '2026-04-10 15:09:37', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(468, '626a5735-8416-4b4e-ba7c-603749d215d9', '2026-04-10 15:09:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(469, 'ef4e52f3-1164-400e-8d60-fa29afc0fea4', '2026-04-10 15:10:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(470, '7dfb7dde-57b4-4823-ac64-a15f08dbad9c', '2026-04-10 15:11:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(471, 'f61d34c1-322e-467c-a327-36da648dffd2', '2026-04-10 15:12:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(472, 'd1e9a7ca-6ce9-4dac-be66-47c63972b9c3', '2026-04-10 15:13:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(473, '95430e73-f0b1-4b5e-8a4e-dd4bf4f564fb', '2026-04-10 15:13:47', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(474, '85eb2cab-ad5b-4ba9-b607-0df85c2c73d9', '2026-04-10 15:14:41', 9997, 'company', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(475, '5893f2ee-85a5-47c0-9c5b-6cd3140286e4', '2026-04-10 15:14:49', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(476, '7862c672-c1d1-4f26-b748-2975822f42a4', '2026-04-10 15:16:06', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(477, 'b43569e4-cbe9-46b7-a68a-5d7431fa13a7', '2026-04-10 15:19:05', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(478, '74359584-0fcd-457a-a6ba-61cd77c9ebbf', '2026-04-10 15:19:14', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(479, '8a28b1d6-1100-4149-8eaa-259e543feb0f', '2026-04-10 15:19:16', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(480, 'd4e69e5b-9109-4051-8994-c3ea5181dcec', '2026-04-10 15:23:53', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(481, '969423df-ba1c-40a6-8703-6174632d1419', '2026-04-10 15:24:04', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(482, 'c504b3fc-68bf-4d23-8ab6-26957d261a5a', '2026-04-10 15:30:34', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(483, '5cb8f4b9-b37c-4ab8-ae24-39762ca1ec91', '2026-04-10 15:30:42', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(484, '5f3fc685-11e6-422c-97ea-b620cce8ad3a', '2026-04-10 15:30:54', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(485, '8380071c-b4c9-4c9c-9400-4c6a753fe0c2', '2026-04-10 15:31:29', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(486, '85cec227-8f3e-48ad-bd04-f195dda272d2', '2026-04-10 15:32:29', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(487, '72aa8d9f-e65d-4276-8cf4-c67828f027ee', '2026-04-10 15:33:16', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(488, 'd425c9f4-fb72-4fca-871e-c7930dff9d07', '2026-04-10 15:33:22', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(489, 'a3ca8de1-b091-4c66-9390-3af6d56fdc39', '2026-04-10 15:33:22', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(490, 'ce7f36d2-3c19-4ee3-9c23-ce456f412cd2', '2026-04-10 15:39:17', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(491, '3bf773c4-3e19-45de-874b-c3f710b8a0cd', '2026-04-10 15:51:23', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(492, '4573d303-9290-48c9-be67-c8e06bb7b72e', '2026-04-10 15:51:25', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(493, '237973ee-23d4-404d-b299-ff00775bdd55', '2026-04-10 15:51:27', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(494, '6ba6b29e-09ee-496d-ada2-f8bfe11435f2', '2026-04-10 15:51:33', 9996, 'user', 'api.contracts.php.respond', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":{\"contract_id\":34},\"body_keys\":[\"action\",\"contract_id\",\"response\",\"esign_consent\"]}'),
(495, 'd22e07cb-a648-422a-86f1-143af71eaec8', '2026-04-10 15:51:36', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(496, 'b1439e29-af63-4ed5-b5d5-879c5cc78126', '2026-04-10 15:51:42', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(497, '4031f82f-5fdb-4b95-8d99-5009bcab1ac4', '2026-04-10 15:53:10', 9996, 'user', 'auth.login_success', 'user', '9996', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"user\"}'),
(498, '640d1c20-05d5-4070-8de7-250651cc6788', '2026-04-10 15:55:18', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(499, '7366125f-ffe1-4ff8-88fb-65332c46ecf5', '2026-04-10 15:55:22', 9996, 'user', 'api.contracts.php.list_contract_changes', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}');
INSERT INTO `system_audit_log` (`audit_id`, `request_id`, `occurred_at`, `actor_user_id`, `actor_role`, `action`, `entity_type`, `entity_id`, `http_method`, `endpoint`, `ip_address`, `user_agent`, `status_code`, `details`) VALUES
(500, 'edb395c2-4135-48d6-9ced-d204f1de4839', '2026-04-10 15:55:22', 9996, 'user', 'api.chat.php.get_messages', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/chat.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"contract_id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(501, 'fbae1cec-c14c-4bdd-890c-b276d4421e9f', '2026-04-10 15:55:24', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(502, '36b23c15-9144-463f-b0e0-27e7a5186f1a', '2026-04-10 15:55:27', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(503, 'd2f3df71-a26c-4e5e-a509-881f2514598d', '2026-04-10 15:55:29', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(504, '6d47a492-6fd0-4b88-9faa-a333a1b583eb', '2026-04-10 15:55:29', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(505, '9882fcc8-5f84-4d4f-81f9-df637d12c915', '2026-04-10 15:56:01', 9997, 'company', 'api.settings.php.post', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/settings.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(506, '35bbc8dd-cbe7-4caa-9310-b51aba93f796', '2026-04-10 15:56:01', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(507, '1ee3a193-3cad-45d0-abff-0b7264069f9e', '2026-04-10 15:56:02', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(508, '8c923acf-6e68-429a-89cf-6f8e3e8d1961', '2026-04-10 15:57:25', 9996, 'user', 'auth.logout', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"user\"}'),
(509, '6677c8a6-084e-4e8c-935d-7c8aa60c3015', '2026-04-10 15:58:09', 107, 'repairer', 'auth.login_success', 'repairer', '107', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"repairer\"}'),
(510, 'e08c7b42-16b0-4563-afd3-cc1a906f0040', '2026-04-10 15:59:47', 107, 'repairer', 'auth.logout', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"repairer\"}'),
(511, '4724c7c0-77d5-44cf-b41b-4ad0c33590db', '2026-04-10 16:00:13', 9996, 'user', 'auth.login_success', 'user', '9996', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"user\"}'),
(512, '14f92897-c7f2-429d-8de9-c458a58689d5', '2026-04-10 16:00:48', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(513, '21901940-e44c-402f-92cb-979be3d1be6a', '2026-04-10 16:02:06', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(514, '17428945-f559-46ae-b541-0b3bec53da45', '2026-04-10 16:02:31', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(515, '91178e2f-19b8-449b-a3bf-69c0dd848554', '2026-04-10 16:03:40', 9996, 'user', 'auth.logout', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"user\"}'),
(516, '3bc94392-f2b4-4cd1-9585-bc490556c96b', '2026-04-10 16:04:11', 107, 'repairer', 'auth.login_success', 'repairer', '107', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"repairer\"}'),
(517, '7aff017d-9044-49cf-9e0b-17768f4cd575', '2026-04-10 16:04:41', 107, 'repairer', 'auth.logout', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"repairer\"}'),
(518, 'ff13ac55-594f-4e5e-a672-3157389dce5e', '2026-04-10 16:07:40', 9996, 'user', 'auth.login_success', 'user', '9996', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"user\"}'),
(519, '77b8fcf5-0baf-4dd7-bfb7-7c025caf10da', '2026-04-10 16:07:49', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(520, 'bed05069-d7da-40e5-95c3-de49a2e5a91c', '2026-04-10 16:30:54', 9996, 'user', 'auth.logout', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"user\"}'),
(521, 'df8bd035-0051-49c2-ab1a-fca1ca384d16', '2026-04-10 16:31:33', 9996, 'user', 'auth.login_success', 'user', '9996', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"user\"}'),
(522, '6d909cad-cf7e-42c8-a67b-dfc8eb9c5040', '2026-04-10 16:32:04', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(523, '0f1404af-92fc-4593-bbb6-d5fe26c52ff7', '2026-04-10 16:34:35', 9996, 'user', 'auth.logout', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/logout', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"user\"}'),
(524, '675a2a5d-42a0-495b-bda1-34885c947a55', '2026-04-10 16:35:36', NULL, NULL, 'auth.login_failed', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 401, '{\"email_hash\":\"dae9c7c55697ba170d6b494c458649bd469af525520280d0dcfc98d74d13b17e\"}'),
(525, '7e8ac3f2-3d85-41b2-8f88-a842f77436b8', '2026-04-10 16:38:05', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(526, '0edbef3f-93ed-4236-9a94-75ef93b111a1', '2026-04-10 16:46:28', 9996, 'user', 'api.user-quotes.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"request_id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(527, '2a66c2d7-e719-4352-84ee-433aafd50223', '2026-04-10 16:46:33', 9996, 'user', 'api.user-quotes.php.respond', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(528, 'd52654f1-bc57-4f72-a653-c2eb779c6517', '2026-04-10 16:46:39', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(529, 'ccc8dc82-513c-4d53-9d62-c7bf0d294728', '2026-04-10 16:46:40', 9997, 'company', 'api.contracts.php.getAcceptedQuotations', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(530, '0d04a1b4-cd87-4abb-81bf-6706634d62c4', '2026-04-10 16:46:52', 9997, 'company', 'api.contracts.php.getAcceptedQuotations', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(531, 'e73d1aed-d9f6-4d17-9b32-fc5373eb1f4a', '2026-04-10 16:47:27', 9997, 'company', 'api.contracts.php.create', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":{\"quotation_id\":\"44\",\"request_id\":\"36\",\"job_request_id\":\"36\"},\"body_keys\":[\"quotation_id\",\"request_id\",\"job_request_id\",\"customer_id\",\"contract_date\",\"project_title\",\"project_reference\",\"project_location\",\"project_type\",\"project_description\",\"scope_description\",\"scope_inclusions\",\"scope_exclusions\",\"scope_standards\",\"materials_responsibility\",\"start_date\",\"end_date\",\"estimated_duration\",\"total_budget\",\"budget_type\",\"budget_min\",\"budget_max\",\"tax_inclusive\",\"payment_method\",\"pricing_type\",\"hourly_rate\",\"spending_cap\",\"late_payment_penalty\",\"pause_work_clause\",\"time_extension_clause\",\"variation_clause\",\"communication_channel\",\"dispute_resolution\",\"warranty_period\",\"payment_terms\",\"additional_terms\",\"milestones\",\"send_to_customer\"]}'),
(532, '090fbcdf-2a21-4034-9b56-933c8185ca6f', '2026-04-10 16:47:28', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(533, '9c420ac9-b8da-47eb-a369-12d3652fca3d', '2026-04-10 16:47:34', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(534, '7d7d6c88-71ce-429b-b091-eb718ef58795', '2026-04-10 16:47:36', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"34\"},\"body_allow\":[],\"body_keys\":[]}'),
(535, 'fb14004e-de10-46e3-8612-19617b05cd9b', '2026-04-10 16:47:42', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"35\"},\"body_allow\":[],\"body_keys\":[]}'),
(536, 'd9213676-e9cf-4d38-b37e-471f7bc668ef', '2026-04-10 16:47:46', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"35\"},\"body_allow\":[],\"body_keys\":[]}'),
(537, 'bb86d030-9264-40c7-8b79-6e62254c6b2e', '2026-04-10 16:47:50', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"35\"},\"body_allow\":[],\"body_keys\":[]}'),
(538, '9341dfb4-b5eb-4dea-8e2c-7ae2e8f06aa8', '2026-04-10 16:59:07', 9996, 'user', 'api.user-quotes.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"request_id\":\"37\"},\"body_allow\":[],\"body_keys\":[]}'),
(539, '25d0a6b8-8ee0-4986-862c-8bee3470ec55', '2026-04-10 16:59:11', 9996, 'user', 'api.user-quotes.php.respond', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(540, 'baae7e45-4ce4-43ae-9fb5-118c226c790f', '2026-04-10 16:59:24', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(541, 'f7152e9a-193b-4843-b5ed-782762582e29', '2026-04-10 16:59:25', 9997, 'company', 'api.contracts.php.getAcceptedQuotations', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(542, '8735a30e-4a16-4949-9a9e-c857a339ab55', '2026-04-10 16:59:45', 9997, 'company', 'api.contracts.php.create', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":{\"quotation_id\":\"45\",\"request_id\":\"37\",\"job_request_id\":\"37\"},\"body_keys\":[\"quotation_id\",\"request_id\",\"job_request_id\",\"customer_id\",\"contract_date\",\"project_title\",\"project_reference\",\"project_location\",\"project_type\",\"project_description\",\"scope_description\",\"scope_inclusions\",\"scope_exclusions\",\"scope_standards\",\"materials_responsibility\",\"start_date\",\"end_date\",\"estimated_duration\",\"total_budget\",\"budget_type\",\"budget_min\",\"budget_max\",\"tax_inclusive\",\"payment_method\",\"pricing_type\",\"hourly_rate\",\"spending_cap\",\"late_payment_penalty\",\"pause_work_clause\",\"time_extension_clause\",\"variation_clause\",\"communication_channel\",\"dispute_resolution\",\"warranty_period\",\"payment_terms\",\"additional_terms\",\"milestones\",\"send_to_customer\"]}'),
(543, 'cce59859-5f23-4578-9101-069ecf592d9e', '2026-04-10 16:59:45', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(544, '113499c8-6e7f-4266-b608-bc218cc1a85a', '2026-04-10 16:59:52', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(545, 'b5a3b138-e243-46e2-ab6f-1ce70372cd08', '2026-04-10 16:59:54', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(546, '741415ec-b684-4f6c-a888-04e8420c4347', '2026-04-10 16:59:56', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(547, '51837c0a-9dd3-4378-9e47-40449d502a11', '2026-04-10 17:00:11', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(548, '25ae6a47-2e92-4273-b3f4-4092e7325c35', '2026-04-10 17:00:12', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(549, '35798260-7c65-4003-a745-24d0f6b20ee5', '2026-04-10 17:00:13', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(550, '3e07cf9b-8ce9-48c6-b85c-d2ca01c264e8', '2026-04-10 17:00:18', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(551, '19cc5f7c-43b5-455a-b141-3f5baf9e25c2', '2026-04-10 17:27:18', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(552, '89791eb1-1e3b-456d-8bdf-9e1fae42d86a', '2026-04-10 17:27:20', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(553, 'f8b38aa2-296b-4725-b645-61867ba7a110', '2026-04-10 17:27:21', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(554, 'f20dbb13-8a7d-446f-ba48-72cab11039de', '2026-04-10 17:30:16', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(555, '45285031-1e46-4762-b330-aff8ee7a4910', '2026-04-10 17:51:17', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(556, '097de19e-afe3-465b-ac57-8608cf88e003', '2026-04-10 17:51:19', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(557, 'd75411cf-d35b-45da-b295-8408c91ba6b3', '2026-04-10 17:51:20', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(558, 'f1688837-ef19-4a96-ba71-537056e5b507', '2026-04-10 17:51:32', 9996, 'user', 'api.contracts.php.pay_and_accept', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":{\"contract_id\":36},\"body_keys\":[\"contract_id\"]}'),
(559, 'e4e4d6ac-0335-4c64-ae3b-3685a622bf9e', '2026-04-10 17:54:25', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(560, 'bdb30ef6-fe55-4f07-97a4-a75b365498ed', '2026-04-10 17:54:27', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(561, '10b0345d-396e-4dcd-a5ac-f6608ac8b02f', '2026-04-10 17:54:30', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(562, '31d4fd14-e3b2-4359-b6de-1db1820e65b3', '2026-04-10 17:54:33', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"35\"},\"body_allow\":[],\"body_keys\":[]}'),
(563, 'bfda016b-22ea-4047-85dd-bfacbc785b3c', '2026-04-10 17:54:34', 9996, 'user', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"35\"},\"body_allow\":[],\"body_keys\":[]}'),
(564, '49ecab7c-12f0-4b29-8bc7-5b8446dd7ee6', '2026-04-10 17:54:41', 9996, 'user', 'api.contracts.php.pay_and_accept', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":{\"contract_id\":35},\"body_keys\":[\"contract_id\"]}'),
(565, 'ada2fbbb-5bfa-42d6-a365-e7b31d64c6f8', '2026-04-10 17:54:42', 9996, 'user', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(566, '6437b695-00fe-4905-940e-fd023a102b83', '2026-04-10 17:54:54', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(567, '7343b5d2-6307-4eac-b09a-5bfb5c3573a4', '2026-04-10 17:55:08', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(568, 'b6ac4a2c-8e1e-46fc-a6d8-20097b056727', '2026-04-10 18:04:05', 9997, 'company', 'auth.login_success', 'company', '9997', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 200, '{\"role\":\"company\"}'),
(569, '15655d9f-26e2-4476-9a21-48315e245854', '2026-04-10 19:16:38', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(570, 'c18f5230-2b57-4514-ad9e-f262a092a8ab', '2026-04-10 19:16:40', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"35\"},\"body_allow\":[],\"body_keys\":[]}'),
(571, '8acbefc9-a110-43eb-bc68-b64b572e26ec', '2026-04-10 20:20:57', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(572, '350b653c-9608-4521-adf4-f24e6e9d9f32', '2026-04-10 20:20:59', 9997, 'company', 'api.contracts.php.get', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"id\":\"36\"},\"body_allow\":[],\"body_keys\":[]}'),
(573, 'ec4802f1-1535-4579-9304-5528b08813b6', '2026-04-10 20:24:52', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(574, '642db7fd-d50a-44a0-836c-765b99e7ad67', '2026-04-10 20:24:52', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(575, '6e30427c-fd41-4832-a2c0-a2b6c471dd2d', '2026-04-10 20:26:06', 9996, 'user', 'api.user-quotes.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"request_id\":\"39\"},\"body_allow\":[],\"body_keys\":[]}'),
(576, 'ebef022e-02e0-407a-a78a-88848ec9f1b9', '2026-04-10 21:45:24', 9996, 'user', 'auth.login_success', 'user', '9996', 'POST', '/2nd-Year-Group-Project/FixLanka/login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 200, '{\"role\":\"user\"}'),
(577, 'bcab9ccd-d26e-4fea-9e75-6ca6b56e7b5c', '2026-04-10 21:45:55', 9996, 'user', 'api.user-quotes.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"request_id\":\"39\"},\"body_allow\":[],\"body_keys\":[]}'),
(578, 'd96f8524-c512-450e-afa0-b2b935bf1a42', '2026-04-10 21:57:39', 9996, 'user', 'api.user-quotes.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"request_id\":\"39\"},\"body_allow\":[],\"body_keys\":[]}'),
(579, '481aaa13-a5d8-4f51-a143-1250c903bcb6', '2026-04-10 21:59:47', 9996, 'user', 'api.user-quotes.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"request_id\":\"39\"},\"body_allow\":[],\"body_keys\":[]}'),
(580, 'b7a998e7-e4a8-4804-adcd-60bb4d46115a', '2026-04-10 22:04:05', 9996, 'user', 'api.user-quotes.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"request_id\":\"39\"},\"body_allow\":[],\"body_keys\":[]}'),
(581, '864ab039-1dd4-44a1-a86c-fe1a50d8bc30', '2026-04-10 22:04:28', 9996, 'user', 'api.user-quotes.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"request_id\":\"39\"},\"body_allow\":[],\"body_keys\":[]}'),
(582, 'f63becd1-51b6-4e56-b604-acf0112bbab7', '2026-04-10 22:10:09', 9996, 'user', 'api.user-quotes.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":{\"request_id\":\"39\"},\"body_allow\":[],\"body_keys\":[]}'),
(583, 'acd5fda8-9fa7-4b26-a8d4-57445e3d287d', '2026-04-10 22:10:24', 9996, 'user', 'api.user-quotes.php.respond', NULL, NULL, 'POST', '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(584, '5294ee86-9fe7-4771-8e1a-0fe36733c822', '2026-04-10 22:10:38', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(585, 'e3d59e77-610a-4e04-aaa2-6bc935604ad5', '2026-04-10 22:10:38', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(586, '79f6a949-c2ce-4171-82c4-b5e42b96ee15', '2026-04-10 22:10:40', 9997, 'company', 'api.contracts.php.getAcceptedQuotations', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(587, '95be0926-9129-4c6c-8d25-6539d46d779e', '2026-04-10 22:21:02', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(588, '8e9bc6f2-9dcc-4011-907e-ed451e967214', '2026-04-10 22:21:02', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(589, 'f28a9fea-77be-41ff-aabc-f6aa32105dfa', '2026-04-10 22:21:04', 9997, 'company', 'api.contracts.php.getAcceptedQuotations', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(590, '969fb63e-82f9-428e-87d3-b5f77348364b', '2026-04-10 22:43:43', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(591, 'e022de5f-927e-4cc1-b1bd-5241fdae7995', '2026-04-10 22:43:43', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(592, 'f92e4767-69f8-461a-802f-c12b02c494bb', '2026-04-10 22:46:26', 9997, 'company', 'api.contracts.php.getAcceptedQuotations', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(593, 'b843479a-33e6-44a0-b0b0-00fcc8ae587a', '2026-04-10 22:46:34', 9997, 'company', 'api.contracts.php.getAcceptedQuotations', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(594, '4eed5587-ea33-470e-949d-f3da4e945c5b', '2026-04-10 22:46:50', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(595, 'd3c23594-e52c-40be-873e-c6b9145e9839', '2026-04-10 22:46:50', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(596, 'cf738ff6-081f-4db2-9bb9-9884aab34dac', '2026-04-10 22:46:52', 9997, 'company', 'api.contracts.php.getAcceptedQuotations', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(597, '59b852e8-8599-4248-83b3-f6d66072c095', '2026-04-10 22:54:12', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(598, 'fb994ce1-300b-4ac9-bae2-372dbd41714a', '2026-04-10 22:54:13', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(599, 'fad7ad48-7004-4823-b8cc-344dab3e85dd', '2026-04-10 22:54:17', 9997, 'company', 'api.contracts.php.getAcceptedQuotations', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(600, '2ae3103b-bd5a-47c3-aaae-b8fdb782153d', '2026-04-10 22:54:30', 9997, 'company', 'api.contracts.php.list', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}'),
(601, '2515a5c9-169e-4cd8-b506-14172ad4d473', '2026-04-10 22:54:30', 9997, 'company', 'api.contracts.php.stats', NULL, NULL, 'GET', '/2nd-Year-Group-Project/FixLanka/api/contracts.php', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', NULL, '{\"query_allow\":[],\"body_allow\":[],\"body_keys\":[]}');

--
-- Triggers `system_audit_log`
--
DELIMITER $$
CREATE TRIGGER `system_audit_log_no_delete` BEFORE DELETE ON `system_audit_log` FOR EACH ROW BEGIN
  SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'system_audit_log is append-only (deletes are not allowed)';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `system_audit_log_no_update` BEFORE UPDATE ON `system_audit_log` FOR EACH ROW BEGIN
  SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'system_audit_log is append-only (updates are not allowed)';
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

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `f_name`, `l_name`, `email`, `password`, `phone`, `profilePicture`, `address`, `district`, `created_at`, `updated_at`, `account_status`, `banned_permanent`, `suspended_until`, `moderation_reason`) VALUES
(9995, 'Gayan', 'Anuradha', 'gayan@gmail.com', '$2y$10$AY.c2LFo8FCIFLS5jDLheuVPPfpxTQhy2JXp3ntq.ZLBKI05dWcYK', NULL, NULL, 'Manakkulama', NULL, '2026-03-03 10:08:53', '2026-03-03 10:08:53', 'ACTIVE', 0, NULL, NULL),
(9996, 'Test', 'User', 'testuser@gmail.com', '$2y$10$KDNeRGuhcQw4.jNBJ27i6e5.3uKfulcU/rWBar/ozDDtKR4bKvAqy', NULL, NULL, '123 Test St', NULL, '2026-03-03 12:16:14', '2026-03-31 09:17:08', 'ACTIVE', 0, NULL, NULL),
(9997, 'Gayan', 'Anuradha', 'nimesha@gmail.com', '$2y$10$Sfn9QNjMvIwfoZYBDEiJSOJfIDeckYPCQHBOjW8DUx7PljsdLWwU6', NULL, NULL, NULL, NULL, '2026-04-08 10:09:30', '2026-04-08 10:09:30', 'ACTIVE', 0, NULL, NULL);

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
('05sctc9vv7dhh1hmmvte9pt019', 1, 'moderator', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-04 09:00:22', 1),
('0621vm1jgmq9aipka5ggh54dbg', 9995, 'user', 'Desktop', 'Unknown', 'Unknown', '::1', 'curl/8.18.0', '2026-03-03 11:14:01', 1),
('0jr82nrht4ksm89ksr68qkfglf', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-07 12:19:33', 1),
('0pkqf0tbok9irc96i2fbft12bg', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-04 18:27:08', 1),
('0ug9n2i9uvvg07101eaio9v9vq', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-31 17:53:08', 1),
('151dgfgi8105ve44eca2l4b9hc', 9997, 'company', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-10 12:34:08', 1),
('18sddv7ahr4bpcl0epnt140c2h', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-06 06:30:55', 1),
('1nhpmdf7ls70c7flvipkmld8hc', 5, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-03 10:10:21', 1),
('1sov1b7dk4peee9uohj10b4j7n', 1, 'moderator', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-08 14:19:52', 1),
('261279h14eoa6nqa18ilalovul', 9996, 'user', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-10 11:04:35', 1),
('2foqjuln87rf4d1il4ivl7dbhp', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-08 17:21:27', 1),
('317m5qkifeh925hdeoti8mumja', 107, 'repairer', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-10 12:33:43', 1),
('36a5tu63srrpaqc3tod7k6kouq', 9996, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-10 16:50:57', 1),
('3at4c468cf9ecqh4g9ngdnqkdt', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-01 06:23:13', 1),
('3jo6oldf6v3d1aj2mppolpsrjv', 9995, 'user', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-08 09:26:15', 1),
('3ou0rf4bkmt7822r1qia1att0t', 1, 'moderator', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-01 10:54:27', 1),
('46eecogp60jbsi6lhe37ei5peq', 2, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-02 14:46:27', 1),
('4fho5idqt5f02j4c4spbt5p7qu', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-02 18:54:06', 1),
('4gvf3i0ubv574p3guub4ud9ont', 9996, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-01 06:49:12', 1),
('4mico57o5rag8jg4biuafir1m0', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-07 10:48:57', 1),
('5129bd2207ea42bc0fe7b85bda41f920', 9998, 'company', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-08 07:54:38', 1),
('51mggi7tkf1p8u5bo68jaf4e3j', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-08 20:18:11', 1),
('5kmavd7fhkk5op1kirc2vji74m', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 11:52:04', 1),
('5olmi6lr1k70165bbg8kvulovs', 9996, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-08 20:17:56', 1),
('60atqksn70e0j40a2k76p6rlg6', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-09 15:31:03', 1),
('7jvq1qcfdjvr6u72k2nnbg38ss', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-07 11:21:13', 1),
('7rlq8o6eengs2p7egqpka4e0om', 9995, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-04 08:29:35', 1),
('996jnaevma2cnn10hvdtfcbe1h', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-08 11:18:35', 1),
('9lkpcfthcsgtpc2nc0do8tkkic', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-06 08:07:46', 1),
('9p7708t3jvbri465pj0liu0ffh', 1, 'moderator', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-02 17:51:01', 1),
('9ub23fqjuan6cp7anmg349r6hc', 108, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-07 07:14:49', 1),
('a97d0242jephpvn7g7jeghvd0e', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-07 11:42:33', 1),
('aogn7f1uhaa44o2f6e863rplfd', 0, 'admin', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-02 16:54:11', 1),
('bdu031f3ck2irhijgkulv5dm1l', 9996, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-10 14:56:06', 1),
('br1649krvvtgtb0coa2v6lbt10', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-01 18:33:47', 1),
('cr796jr4d1cd8o11gmippaueh6', 9995, 'user', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-03 12:35:50', 1),
('ds583ij4a5vmu45p2vd94fs2h9', 9996, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-04 08:39:39', 1),
('e864tcp0ggpi8e1f95pjtnqgv2', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-06 12:29:10', 1),
('f2cspselpf2tphm075j07diq9e', 1, 'moderator', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 08:59:17', 1),
('fm04sbafkvm23251qlu6cqsl0s', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-08 10:33:32', 1),
('fm10cht8jvedkilcua70tg3vr0', 1, 'moderator', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-04 08:38:28', 1),
('foncegqmv7s7dj4ah531nv46no', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 10:48:21', 1),
('frlprno72tcshja545p7tltrvi', 9996, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-31 09:17:58', 1),
('g04hiidrpt5143ic2egc402lmm', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 08:59:03', 1),
('g5i2f56cotjk76gj91jp7lj7ko', 5, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-02 19:43:13', 1),
('gjts7s2lh0cprip3i6eq3m6r2g', 107, 'repairer', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 08:37:10', 1),
('hatujplvnvasgc3kkeeg67ojqv', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-08 10:15:22', 1),
('i344rbl490prjcapo15ha8lp6o', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-10 17:24:30', 1),
('jpio6k6kvcafhqj9gsn31mr193', 1, 'moderator', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-07 12:04:42', 1),
('k19ncc07fjnkhbogti0ivfrit0', 109, 'repairer', 'Desktop', 'Firefox', 'Windows 10/11', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0', '2026-04-07 06:03:09', 1),
('ko8chm4jnic6e7g7eqbufai377', 0, 'admin', 'Desktop', 'Unknown', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.7920', '2026-03-04 11:15:01', 1),
('koamd4dgce5rjbrofumkcu1ic4', 9997, 'company', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 20:32:29', 1),
('ksarbqplp2q7agbli15e959dnf', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-06 10:29:56', 1),
('kvm25528iek8b2vtbkp96cakva', 9996, 'user', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 20:16:14', 1),
('l1oltob1mcqhnhbet16dqksjlu', 9996, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-04 08:57:44', 1),
('lcfe5t4d5o4fpusf1o0vajqeee', 5, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-02 14:37:00', 1),
('lhj4li9l10n3hnqdc6vfaif9ei', 9996, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-04 12:16:53', 1),
('lrgqg2971guaufnvrmu10jbh4d', 9996, 'user', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-03 12:33:08', 1),
('mdl9uidkvrpoa9v7f282q2fioo', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-04 08:39:04', 1),
('mugvd0up8hj1cc7muqrj6ani14', 9996, 'user', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-10 10:33:40', 1),
('n8ttretkf9lpudtffnnolavhvj', 0, 'admin', 'Desktop', 'Unknown', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.7920', '2026-03-04 11:12:23', 1),
('nd15skkfh28gk8vhp8ddst2lb8', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-01 18:49:57', 1),
('noflm5baj1cq1cqtd0bfbh2794', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 11:51:46', 1),
('obqt04chr5843mpepur6d6785g', 9996, 'user', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-10 11:00:54', 1),
('oct8hk81i0gt2r7l2kre3nogd8', 0, 'admin', 'Desktop', 'Unknown', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.7920', '2026-03-04 10:43:06', 1),
('onjk7d72qdprq181k7krec2o4q', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-10 05:55:26', 1),
('ors96h706kifk1imcr626s79pq', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-04 12:23:22', 1),
('ov7m0eut0ecl56alftee1qk038', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-04 09:00:01', 1),
('p744rr39ka2iobfl8u8ega858a', 9995, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-03 10:13:14', 1),
('q2h6u6sjdlsu2sr0r3ssg4hll0', 0, 'admin', 'Desktop', 'Unknown', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.7920', '2026-03-04 10:47:47', 1),
('qcskcvkfn9qop075dabdt4d0fq', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-07 10:48:33', 1),
('r2a4f984u5p4can46q669dl1id', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-08 11:01:24', 1),
('r5qg2h3nd0vvr9bnui81osgmjp', 107, 'repairer', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-10 10:34:41', 1),
('s3bpbv4tmak2c057b0vjas8gai', 9996, 'user', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-10 10:27:25', 1),
('sa1jk8onvo5l2evko0e981l628', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-07 18:24:21', 1),
('smgsta4e3mbmh9fess7v4mtg92', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-07 12:22:29', 1),
('t0s3p1gs7dqi48la989irr28uj', 5, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-04 07:58:20', 1),
('t6uc45h70q5927nd5k3g7gr9e0', 5, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-02 16:58:58', 1),
('tbdsih89ltf5k2qj81uc3f8g8j', 0, 'admin', 'Desktop', 'Unknown', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.7920', '2026-03-04 10:44:57', 1),
('tmdooouobsu7mboun006qts495', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-07 05:38:27', 1),
('u3cmpmumi423430ecjo1tmh48j', 110, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-07 07:17:37', 1),
('u7lgltl0lcoce4lrtu49nva4ie', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-03 12:53:51', 1),
('ua5019b5r97kg84emb1v7umbdc', 107, 'repairer', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-10 10:29:47', 1),
('up03qr37harqa9cvb67o0lag5g', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-08 04:52:47', 1),
('v8560e0mojk851i0dgeb6ggh94', 0, 'admin', 'Desktop', 'Unknown', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.7920', '2026-03-04 11:12:56', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_moderation_cases`
--
ALTER TABLE `account_moderation_cases`
  ADD PRIMARY KEY (`case_id`),
  ADD KEY `admin_username` (`admin_username`),
  ADD KEY `idx_target` (`target_id`,`target_type`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_status` (`status_after`);

--
-- Indexes for table `account_moderation_log`
--
ALTER TABLE `account_moderation_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_account_lookup` (`account_type`,`account_id`,`created_at`),
  ADD KEY `idx_action_type` (`action_type`);

--
-- Indexes for table `account_moderation_status`
--
ALTER TABLE `account_moderation_status`
  ADD PRIMARY KEY (`status_id`),
  ADD UNIQUE KEY `unique_account` (`account_id`,`account_type`),
  ADD KEY `idx_status` (`account_status`),
  ADD KEY `idx_suspended_until` (`suspended_until`),
  ADD KEY `idx_account` (`account_id`,`account_type`);

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
-- Indexes for table `adminalert`
--
ALTER TABLE `adminalert`
  ADD PRIMARY KEY (`alert_id`),
  ADD KEY `idx_target_role` (`target_role`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_admin` (`admin_username`),
  ADD KEY `idx_read` (`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `admin_override_history`
--
ALTER TABLE `admin_override_history`
  ADD PRIMARY KEY (`override_id`),
  ADD KEY `idx_ad_overrides` (`ad_id`),
  ADD KEY `idx_admin_actions` (`admin_username`),
  ADD KEY `idx_timestamp` (`override_timestamp`);

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
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_provider` (`provider_type`,`provider_id`),
  ADD KEY `idx_submission` (`submission_date`),
  ADD KEY `fk_ad_category` (`category_id`),
  ADD KEY `fk_ad_admin_reviewer` (`admin_reviewed_by`);

--
-- Indexes for table `ad_reports`
--
ALTER TABLE `ad_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `resolved_by` (`resolved_by`),
  ADD KEY `idx_ad` (`ad_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_severity` (`severity`),
  ADD KEY `idx_reporter` (`reporter_id`,`reporter_type`),
  ADD KEY `idx_assigned` (`assigned_to`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `ad_schedules`
--
ALTER TABLE `ad_schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `ad_id` (`ad_id`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

--
-- Indexes for table `ad_status_history`
--
ALTER TABLE `ad_status_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_ad_id` (`ad_id`),
  ADD KEY `idx_date` (`created_at`);

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
  ADD KEY `idx_email` (`email`),
  ADD KEY `fk_company_location` (`location_id`);

--
-- Indexes for table `companyemployee`
--
ALTER TABLE `companyemployee`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `repairer_id` (`repairer_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_specialty` (`specialty`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `companyjobpost`
--
ALTER TABLE `companyjobpost`
  ADD PRIMARY KEY (`posting_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_posted_date` (`posted_date`),
  ADD KEY `idx_deadline` (`application_deadline`);

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
  ADD KEY `idx_company_quote` (`company_id`),
  ADD KEY `idx_schedule_type` (`work_schedule_type`),
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
-- Indexes for table `company_jobpost`
--
ALTER TABLE `company_jobpost`
  ADD PRIMARY KEY (`posting_id`),
  ADD KEY `company_id` (`company_id`);

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

--
-- Indexes for table `contract_audit_log`
--
ALTER TABLE `contract_audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `performed_by` (`performed_by`),
  ADD KEY `idx_contract_id` (`contract_id`),
  ADD KEY `idx_milestone_id` (`milestone_id`),
  ADD KEY `idx_action` (`action`),
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
-- Indexes for table `contract_change_requests`
--
ALTER TABLE `contract_change_requests`
  ADD PRIMARY KEY (`change_request_id`),
  ADD KEY `idx_contract_status` (`contract_id`,`status`),
  ADD KEY `idx_created_at` (`created_at`);

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
  ADD KEY `reviewed_by` (`reviewed_by`),
  ADD KEY `idx_contract_id` (`contract_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`);

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
-- Indexes for table `directjobrequest`
--
ALTER TABLE `directjobrequest`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_djr_user` (`user_id`),
  ADD KEY `idx_djr_category` (`category_id`),
  ADD KEY `idx_djr_provider` (`provider_id`,`provider_type`),
  ADD KEY `idx_djr_status` (`status`),
  ADD KEY `idx_djr_created` (`date_created`);

--
-- Indexes for table `directrequestquotes`
--
ALTER TABLE `directrequestquotes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_drq_user` (`user_id`),
  ADD KEY `idx_drq_request` (`request_id`),
  ADD KEY `idx_drq_provider` (`provider_id`,`provider_type`);

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
  ADD UNIQUE KEY `month` (`month`),
  ADD KEY `idx_month` (`month`),
  ADD KEY `generated_by` (`generated_by`);

--
-- Indexes for table `financialreporttransaction`
--
ALTER TABLE `financialreporttransaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `idx_report` (`report_id`),
  ADD KEY `idx_type` (`transaction_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_date` (`transaction_date`);

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
-- Indexes for table `issuenotification`
--
ALTER TABLE `issuenotification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_issue` (`issue_id`),
  ADD KEY `idx_recipient` (`recipient_type`,`recipient_id`),
  ADD KEY `idx_read` (`is_read`);

--
-- Indexes for table `issuereport`
--
ALTER TABLE `issuereport`
  ADD PRIMARY KEY (`issue_id`),
  ADD KEY `idx_reporter` (`reportedBy_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`);

--
-- Indexes for table `issuestatushistory`
--
ALTER TABLE `issuestatushistory`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_issue` (`issue_id`),
  ADD KEY `idx_date` (`changed_at`);

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
  ADD KEY `idx_created` (`dateCreated`),
  ADD KEY `idx_district` (`district`),
  ADD KEY `fk_jobrequest_location` (`location_id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`location_id`);

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
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_moderator_status` (`status`),
  ADD KEY `idx_moderator_deleted` (`deleted_at`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_username` (`username`);

--
-- Indexes for table `moderatormsg`
--
ALTER TABLE `moderatormsg`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `idx_repairer` (`repairer_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `moderator_activity`
--
ALTER TABLE `moderator_activity`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `idx_moderator` (`moderator_id`),
  ADD KEY `idx_type` (`activity_type`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `moderator_management_log`
--
ALTER TABLE `moderator_management_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_admin` (`admin_username`),
  ADD KEY `idx_moderator` (`moderator_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_date` (`created_at`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_recipient` (`recipient_type`),
  ADD KEY `idx_send_date` (`send_date`);

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
-- Indexes for table `placement_limits`
--
ALTER TABLE `placement_limits`
  ADD PRIMARY KEY (`placement_type`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_customer` (`customer_id`),
  ADD KEY `idx_status` (`status`);

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
  ADD KEY `idx_ratings` (`ratings`),
  ADD KEY `idx_account_status` (`account_status`),
  ADD KEY `idx_suspended_until` (`suspended_until`);

--
-- Indexes for table `repairerapplication`
--
ALTER TABLE `repairerapplication`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `idx_repairer` (`repairer_id`),
  ADD KEY `idx_posting` (`posting_id`),
  ADD KEY `idx_status` (`app_status`);

--
-- Indexes for table `repairerassignment`
--
ALTER TABLE `repairerassignment`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_repairer` (`repairer_id`);

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
  ADD KEY `job_posting_id` (`job_posting_id`),
  ADD KEY `repairer_id` (`repairer_id`);

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
-- Indexes for table `service_area`
--
ALTER TABLE `service_area`
  ADD PRIMARY KEY (`area_id`),
  ADD KEY `idx_owner` (`owner_id`,`owner_type`);

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
-- Indexes for table `system_activity_logs`
--
ALTER TABLE `system_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_type` (`activity_type`);

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
-- AUTO_INCREMENT for table `account_moderation_cases`
--
ALTER TABLE `account_moderation_cases`
  MODIFY `case_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `account_moderation_log`
--
ALTER TABLE `account_moderation_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `account_moderation_status`
--
ALTER TABLE `account_moderation_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `activitylog`
--
ALTER TABLE `activitylog`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `adminalert`
--
ALTER TABLE `adminalert`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_override_history`
--
ALTER TABLE `admin_override_history`
  MODIFY `override_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `advertisement`
--
ALTER TABLE `advertisement`
  MODIFY `ad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `ad_reports`
--
ALTER TABLE `ad_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ad_schedules`
--
ALTER TABLE `ad_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ad_status_history`
--
ALTER TABLE `ad_status_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `chatmessage`
--
ALTER TABLE `chatmessage`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9999;

--
-- AUTO_INCREMENT for table `companyemployee`
--
ALTER TABLE `companyemployee`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companyjobpost`
--
ALTER TABLE `companyjobpost`
  MODIFY `posting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `companyquotation`
--
ALTER TABLE `companyquotation`
  MODIFY `quotation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `companysettings`
--
ALTER TABLE `companysettings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_employees`
--
ALTER TABLE `company_employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `company_jobpost`
--
ALTER TABLE `company_jobpost`
  MODIFY `posting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `company_subscriptions`
--
ALTER TABLE `company_subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contract`
--
ALTER TABLE `contract`
  MODIFY `contract_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `contract_audit_log`
--
ALTER TABLE `contract_audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contract_budget_adjustments`
--
ALTER TABLE `contract_budget_adjustments`
  MODIFY `adjustment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contract_change_requests`
--
ALTER TABLE `contract_change_requests`
  MODIFY `change_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contract_chats`
--
ALTER TABLE `contract_chats`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
  MODIFY `draft_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contract_invoices`
--
ALTER TABLE `contract_invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contract_milestone`
--
ALTER TABLE `contract_milestone`
  MODIFY `milestone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `contract_notifications`
--
ALTER TABLE `contract_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `contract_payment_history`
--
ALTER TABLE `contract_payment_history`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contract_timeline`
--
ALTER TABLE `contract_timeline`
  MODIFY `timeline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `contract_time_logs`
--
ALTER TABLE `contract_time_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `directjobrequest`
--
ALTER TABLE `directjobrequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `directrequestquotes`
--
ALTER TABLE `directrequestquotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `escrow_accounts`
--
ALTER TABLE `escrow_accounts`
  MODIFY `escrow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `escrow_transaction`
--
ALTER TABLE `escrow_transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `escrow_wallet`
--
ALTER TABLE `escrow_wallet`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financialreport`
--
ALTER TABLE `financialreport`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `financialreporttransaction`
--
ALTER TABLE `financialreporttransaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `freelancer_assignments`
--
ALTER TABLE `freelancer_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `issuenotification`
--
ALTER TABLE `issuenotification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issuereport`
--
ALTER TABLE `issuereport`
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `issuestatushistory`
--
ALTER TABLE `issuestatushistory`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job`
--
ALTER TABLE `job`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobrequest`
--
ALTER TABLE `jobrequest`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `milestone`
--
ALTER TABLE `milestone`
  MODIFY `milestone_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `milestonepayment`
--
ALTER TABLE `milestonepayment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `moderator`
--
ALTER TABLE `moderator`
  MODIFY `moderator_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `moderatormsg`
--
ALTER TABLE `moderatormsg`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `moderator_activity`
--
ALTER TABLE `moderator_activity`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `moderator_management_log`
--
ALTER TABLE `moderator_management_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `promotion`
--
ALTER TABLE `promotion`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairer`
--
ALTER TABLE `repairer`
  MODIFY `repairer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `repairerapplication`
--
ALTER TABLE `repairerapplication`
  MODIFY `app_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairerassignment`
--
ALTER TABLE `repairerassignment`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairerquote`
--
ALTER TABLE `repairerquote`
  MODIFY `quote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `repairer_applications`
--
ALTER TABLE `repairer_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- AUTO_INCREMENT for table `service_area`
--
ALTER TABLE `service_area`
  MODIFY `area_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `staffsummary`
--
ALTER TABLE `staffsummary`
  MODIFY `staff_summary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `staticcontent`
--
ALTER TABLE `staticcontent`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- AUTO_INCREMENT for table `system_activity_logs`
--
ALTER TABLE `system_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_audit_log`
--
ALTER TABLE `system_audit_log`
  MODIFY `audit_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=602;

--
-- AUTO_INCREMENT for table `ticketmessage`
--
ALTER TABLE `ticketmessage`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9998;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adminalert`
--
ALTER TABLE `adminalert`
  ADD CONSTRAINT `adminalert_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin` (`username`) ON DELETE CASCADE;

--
-- Constraints for table `admin_override_history`
--
ALTER TABLE `admin_override_history`
  ADD CONSTRAINT `admin_override_history_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_override_history_ibfk_2` FOREIGN KEY (`admin_username`) REFERENCES `admin` (`username`) ON DELETE CASCADE;

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
-- Constraints for table `advertisement`
--
ALTER TABLE `advertisement`
  ADD CONSTRAINT `fk_ad_admin_reviewer` FOREIGN KEY (`admin_reviewed_by`) REFERENCES `admin` (`username`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ad_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ad_reports`
--
ALTER TABLE `ad_reports`
  ADD CONSTRAINT `ad_reports_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ad_reports_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `moderator` (`moderator_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ad_reports_ibfk_3` FOREIGN KEY (`resolved_by`) REFERENCES `moderator` (`moderator_id`) ON DELETE SET NULL;

--
-- Constraints for table `ad_schedules`
--
ALTER TABLE `ad_schedules`
  ADD CONSTRAINT `ad_schedules_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE;

--
-- Constraints for table `ad_status_history`
--
ALTER TABLE `ad_status_history`
  ADD CONSTRAINT `ad_status_history_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE;

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
-- Constraints for table `company`
--
ALTER TABLE `company`
  ADD CONSTRAINT `fk_company_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL;

--
-- Constraints for table `companyemployee`
--
ALTER TABLE `companyemployee`
  ADD CONSTRAINT `companyemployee_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `companyemployee_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `companyquotation_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `companyquotation_ibfk_company` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

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
-- Constraints for table `company_jobpost`
--
ALTER TABLE `company_jobpost`
  ADD CONSTRAINT `company_jobpost_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_contract_escrow` FOREIGN KEY (`escrow_account_id`) REFERENCES `escrow_accounts` (`escrow_id`) ON DELETE SET NULL;

--
-- Constraints for table `contract_audit_log`
--
ALTER TABLE `contract_audit_log`
  ADD CONSTRAINT `contract_audit_log_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_audit_log_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `contract_milestone` (`milestone_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_audit_log_ibfk_3` FOREIGN KEY (`performed_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_budget_adjustments`
--
ALTER TABLE `contract_budget_adjustments`
  ADD CONSTRAINT `contract_budget_adjustments_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_change_requests`
--
ALTER TABLE `contract_change_requests`
  ADD CONSTRAINT `fk_ccr_contract` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `contract_milestone_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contract_milestone_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL;

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
-- Constraints for table `directjobrequest`
--
ALTER TABLE `directjobrequest`
  ADD CONSTRAINT `directjobrequest_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `directjobrequest_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Constraints for table `directrequestquotes`
--
ALTER TABLE `directrequestquotes`
  ADD CONSTRAINT `directrequestquotes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `directrequestquotes_ibfk_2` FOREIGN KEY (`request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE;

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
-- Constraints for table `financialreport`
--
ALTER TABLE `financialreport`
  ADD CONSTRAINT `financialreport_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `moderator` (`moderator_id`) ON DELETE SET NULL;

--
-- Constraints for table `financialreporttransaction`
--
ALTER TABLE `financialreporttransaction`
  ADD CONSTRAINT `financialreporttransaction_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `financialreport` (`report_id`) ON DELETE CASCADE;

--
-- Constraints for table `freelancer_assignments`
--
ALTER TABLE `freelancer_assignments`
  ADD CONSTRAINT `freelancer_assignments_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `freelancer_assignments_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `freelancer_assignments_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `freelancer_assignments_ibfk_4` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE SET NULL;

--
-- Constraints for table `issuenotification`
--
ALTER TABLE `issuenotification`
  ADD CONSTRAINT `issuenotification_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `issuereport` (`issue_id`) ON DELETE CASCADE;

--
-- Constraints for table `issuereport`
--
ALTER TABLE `issuereport`
  ADD CONSTRAINT `issuereport_ibfk_1` FOREIGN KEY (`reportedBy_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `issuestatushistory`
--
ALTER TABLE `issuestatushistory`
  ADD CONSTRAINT `issuestatushistory_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `issuereport` (`issue_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_jobrequest_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE SET NULL,
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
-- Constraints for table `repairerapplication`
--
ALTER TABLE `repairerapplication`
  ADD CONSTRAINT `repairerapplication_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repairerapplication_ibfk_2` FOREIGN KEY (`posting_id`) REFERENCES `companyjobpost` (`posting_id`) ON DELETE CASCADE;

--
-- Constraints for table `repairerassignment`
--
ALTER TABLE `repairerassignment`
  ADD CONSTRAINT `repairerassignment_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repairerassignment_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `repairer_applications_ibfk_2` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;

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
