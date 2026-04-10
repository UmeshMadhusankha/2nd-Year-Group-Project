-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 07, 2026 at 08:13 AM
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
(23, 9997, 'company', NULL, NULL, 'Login Page we made', 'You can see what we are making', 1, 'banner', 4900.00, '/2nd-Year-Group-Project/FixLanka/uploads/advertisements/1775040838_Body.png', 'all', '2026-04-01', '2026-04-08', 'scheduled', '2026-04-01 10:53:58', 0, 0, 1, '2026-04-01 10:54:38', '', NULL, NULL, NULL, NULL, 0),
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
(20, 'Window Installation', '2026-01-03 09:19:02');

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
(9998, 'Test Company', 'Plumbing,Cleaning', 'REG12345', '', 5, '', 'Password123!testcompany@fixlanka.com', '', '0112345678', NULL, '$2y$10$uDKQntBsubbfs.vJpG0ZVenZiZIRxVeMJRamvCWpAdBmJ1yXvi3om', 'We provide top-notch plumbing services.', 0.00, '2026-03-08 07:54:27', 0);

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
  `additional_terms` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected','successful') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `budget_type` enum('fixed','flexible') DEFAULT 'fixed' COMMENT 'Fixed or Flexible (±10%)',
  `budget_min` decimal(10,2) DEFAULT NULL COMMENT 'Minimum budget for flexible pricing',
  `budget_max` decimal(10,2) DEFAULT NULL COMMENT 'Maximum budget for flexible pricing',
  `payment_method` enum('full_upfront','milestone_based','50_50','30_70','completion') DEFAULT 'full_upfront' COMMENT 'Payment method selected',
  `pricing_type` enum('fixed_price','time_and_material') DEFAULT 'fixed_price' COMMENT 'Pricing structure type',
  `hourly_rate` decimal(10,2) DEFAULT NULL COMMENT 'Hourly rate for Time & Material',
  `spending_cap_multiplier` decimal(3,2) DEFAULT 1.10 COMMENT 'Spending cap multiplier for T&M (default 1.10 = 110%)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companyquotation`
--

INSERT INTO `companyquotation` (`quotation_id`, `request_id`, `company_id`, `user_id`, `title`, `description`, `labor_cost`, `material_cost`, `transport_cost`, `other_charges`, `total_amount`, `start_date`, `completion_date`, `estimated_duration`, `work_schedule_type`, `working_days_per_week`, `daily_work_hours`, `work_start_time`, `work_end_time`, `break_duration`, `custom_schedule_json`, `public_holidays_excluded`, `estimated_calendar_days`, `custom_schedule_details`, `total_work_hours`, `overtime_available`, `overtime_rate`, `payment_terms`, `warranty_period`, `additional_terms`, `status`, `created_at`, `updated_at`, `budget_type`, `budget_min`, `budget_max`, `payment_method`, `pricing_type`, `hourly_rate`, `spending_cap_multiplier`) VALUES
(34, 33, 9996, 9995, 'Dye my hair', 'Based on your request:\n\ndye my hair it is now fully white\n\nWe will provide the following services:\n', 24000.00, 2500.00, 0.00, 0.00, 26500.00, '2026-03-07', '2026-03-11', 4, 'weekdays_only', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 0, NULL, '30% Advance, 70% on Completion', '6_months', '', 'rejected', '2026-03-04 08:27:58', '2026-03-08 09:26:11', 'fixed', NULL, NULL, 'full_upfront', 'fixed_price', NULL, 1.10),
(41, 33, 9997, 9995, 'Dye my hair', 'Based on your request:\n\ndye my hair it is now fully white\n\nWe will provide the following services:\n', 40000.00, 5000.00, 0.00, 0.00, 45000.00, '2026-03-11', '2026-03-14', 3, 'weekdays_only', 5, 8.00, '08:00:00', '17:00:00', NULL, NULL, 1, NULL, NULL, NULL, 0, NULL, '50% Advance, 50% on Completion', '6_months', '', 'accepted', '2026-03-08 08:11:54', '2026-03-08 09:26:11', 'fixed', NULL, NULL, '50_50', 'time_and_material', NULL, 1.50),
(42, 28, 9997, 9996, 'Test Sink Repair', 'Based on your request:\n\nFix the kitchen sink\n\nWe will provide the following services:\n', 13500.00, 5000.00, 0.00, 0.00, 18500.00, '2026-04-03', '2026-04-07', 3, 'weekdays_only', 5, 8.00, '08:00:00', '17:00:00', NULL, NULL, 1, NULL, NULL, NULL, 0, NULL, 'Milestone-based Payment - Payment released at project milestones', '6_months', 'bjhhjgkjukuuhgcj', 'successful', '2026-03-31 09:03:59', '2026-03-31 15:16:19', 'fixed', NULL, NULL, 'milestone_based', 'time_and_material', NULL, 1.50);

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
(3, 9997, 107, 'Freelancer', 'freelance', 'active', '2026-04-06', 3500.00, NULL, '2026-04-06 12:31:43', '2026-04-06 12:31:43');

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

--
-- Dumping data for table `contract`
--

INSERT INTO `contract` (`contract_id`, `contract_number`, `quotation_id`, `company_id`, `customer_id`, `job_request_id`, `project_id`, `project_title`, `project_reference`, `project_location`, `project_description`, `scope_description`, `scope_inclusions`, `scope_exclusions`, `scope_standards`, `materials_responsibility`, `milestone_plan`, `total_milestones`, `completed_milestones`, `progress_percentage`, `auto_generated`, `created_at`, `updated_at`, `total_budget`, `budget_type`, `budget_flexibility_percentage`, `budget_min`, `budget_max`, `tax_inclusive`, `payment_method`, `advance_payment_pct`, `pricing_type`, `hourly_rate`, `spending_cap`, `late_payment_penalty`, `pause_work_clause`, `time_extension_clause`, `variation_clause`, `communication_channel`, `dispute_resolution`, `actual_hours`, `actual_cost`, `amount_paid`, `amount_pending`, `payment_status`, `start_date`, `end_date`, `terms_accepted`, `terms_accepted_at`, `contract_date`, `user_signature`, `company_signature`, `customer_signature`, `signed_at`, `undo_deadline`, `undo_available`, `undo_requested`, `chat_active`, `escrow_enabled`, `chat_activated_at`, `escrow_account_id`, `upfront_payment_percentage`, `upfront_payment_amount`, `upfront_payment_received`, `work_verified_started`, `quality_guarantee_end_date`, `terms_conditions`, `status`, `sent_to_customer`, `sent_at`, `customer_response`, `customer_response_at`, `locked`) VALUES
(33, NULL, 42, 9997, 9996, 28, 28, 'Test Sink Repair', '', '123 Test Street, Colombo', 'Based on your request:\n\nFix the kitchen sink\n\nWe will provide the following services:\n', 'Based on your request:\n\nFix the kitchen sink\n\nWe will provide the following services:\n', '', '', '', 'company', 1, 3, 0, 0, 1, '2026-03-31 15:16:18', '2026-03-31 15:16:19', 18500.00, 'flexible', NULL, 16650.00, 20350.00, 1, 'milestone_based', 0.00, 'time_and_material', 0.00, 20350.00, 'Interest of 2% per month on overdue payments after a 7-day grace period.', 1, 1, 1, 'system', 'Both parties agree to attempt resolution through negotiation via the FixLanka platform before seeking external mediation or arbitration. A message log of all communications will be maintained as part of the contract record.', 0.00, 0.00, 0.00, 18500.00, 'pending', '2026-04-03', '2026-04-07', 0, NULL, '2026-03-31', 'PENDING', 'PENDING', NULL, NULL, NULL, 1, 0, 1, 1, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, 'sent', 1, '2026-03-31 15:16:19', 'pending', NULL, 0);

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
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contract_milestone`
--

INSERT INTO `contract_milestone` (`milestone_id`, `contract_id`, `milestone_number`, `title`, `description`, `due_date`, `amount`, `percentage`, `status`, `escrow_held`, `payment_released`, `payment_released_at`, `proof_of_work`, `submitted_at`, `reviewed_by`, `reviewed_at`, `review_comments`, `created_at`, `updated_at`, `completed_at`, `approved_at`, `proof_files`, `comments`) VALUES
(22, 33, 1, 'Project Commencement', 'Site preparation and initial setup', '2026-04-03', 5550.00, 30.00, 'pending', 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-31 20:46:19', '2026-03-31 20:46:19', NULL, NULL, NULL, NULL),
(23, 33, 2, 'Mid-Project Review', 'Progress inspection and quality check', '2026-04-05', 7400.00, 40.00, 'pending', 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-31 20:46:19', '2026-03-31 20:46:19', NULL, NULL, NULL, NULL),
(24, 33, 3, 'Project Handover', 'Final inspection, cleanup, and handover', '2026-04-07', 5550.00, 30.00, 'pending', 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-31 20:46:19', '2026-03-31 20:46:19', NULL, NULL, NULL, NULL);

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
(22, 9995, 5, 'Door repairing', 'Door had broken from neck need quick fix', 'pending', NULL, 'Kalutara', 'NO:115, Kumbukanda, Mahailuppallama', 'both', 'medium', '2026-03-14', '2026-03-03 10:10:14', NULL),
(28, 9996, 1, 'Test Sink Repair', 'Fix the kitchen sink', 'in_progress', NULL, 'Colombo', '123 Test Street', 'individual', 'medium', '2026-04-01', '2026-03-03 12:17:10', NULL),
(33, 9995, 6, 'Dye my hair', 'dye my hair it is now fully white', 'accepted', NULL, 'Colombo', 'umesh-job-2', 'both', 'medium', '2026-03-14', '2026-03-03 12:49:55', NULL),
(34, 9996, 4, 'hello', 'clean my ass', 'pending', 6, '', '', 'company', 'medium', '2026-04-15', '2026-04-03 20:16:14', NULL);

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
(6, 'akabkbskjbkjsbc', 'Kilinochchi', '2026-04-03 20:16:14');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notification_id`, `title`, `message`, `recipient_type`, `status`, `send_date`, `created_at`) VALUES
(14, 'Service Update', 'Important updates to our service offerings. Check out the new features available in your dashboard.', 'all', 'sent', '2026-02-15 14:49:17', '2026-02-15 14:49:17'),
(15, 'Maintenance Notice', 'Scheduled maintenance on [DATE] from [TIME] to [TIME]. Services may be temporarily unavailable.', 'all', 'sent', '2026-02-15 14:49:27', '2026-02-15 14:49:27'),
(16, 'Security Alert', 'We\'ve detected unusual activity on your account. Please verify your security settings.', 'all', 'sent', '2026-02-19 18:46:13', '2026-02-19 18:46:13');

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

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`project_id`, `company_id`, `customer_id`, `title`, `description`, `project_type`, `location`, `budget`, `final_cost`, `start_date`, `end_date`, `attachment`, `status`, `progress`) VALUES
(28, 9997, 9996, 'Test Sink Repair', 'Based on your request:\n\nFix the kitchen sink\n\nWe will provide the following services:', NULL, '123 Test Street, Colombo', 18500.00, NULL, '2026-04-03', '2026-04-07', NULL, 'planned', 0);

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
(109, 'repairer', 'repairer', 're@gmail.com', '$2y$10$e6TDVhecT/jU0NjYNOGeueag4otPR4hGPixvHIpr9bQXYJziaPJgG', NULL, '22385', 'sdaszgszv', NULL, 0.00, 0, 'Colombo', 'available', '2026-04-07 06:03:09', 9, 'ACTIVE', 0, NULL, NULL, NULL);

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
(8, 1, 107, 'hgccgcjhchjchujkc', 3500.00, 'approved', NULL, '2026-04-06 12:28:32', '2026-04-06 12:31:43');

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
  `content_type` enum('terms','privacy','faq','about','help') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staticcontent`
--

INSERT INTO `staticcontent` (`content_id`, `title`, `description`, `body`, `status`, `last_update`, `content_type`) VALUES
(1, 'Terms of Service', 'Legal terms and conditions for using FixLanka platform', 'Welcome to FixLanka! By accessing and using our platform, you agree to be bound by these Terms of Service.\r\n\r\n1. ACCEPTANCE OF TERMS\r\nBy creating an account or using our services, you accept these terms in full. If you disagree with any part of these terms, you must not use our platform.\r\n\r\n2. USER ACCOUNTS\r\n- You must be at least 18 years old to register\r\n- You are responsible for maintaining the confidentiality of your account\r\n- You must provide accurate and complete information\r\n- One person may not maintain multiple accounts\r\n\r\n3. SERVICE PROVIDER RESPONSIBILITIES\r\nService providers (repairers and companies) must:\r\n- Provide accurate credentials and qualifications\r\n- Deliver services as described and quoted\r\n- Maintain professional conduct at all times\r\n- Comply with all local laws and regulations\r\n\r\n4. PAYMENT TERMS\r\n- All payments must be processed through our secure platform\r\n- Service providers will receive payment after successful job completion\r\n- FixLanka charges a 10% platform fee on all transactions\r\n- Refunds are subject to our refund policy\r\n\r\n5. LIABILITY\r\n- FixLanka acts as a marketplace platform only\r\n- We are not responsible for the quality of services provided\r\n- Users engage with service providers at their own risk\r\n- We recommend verifying credentials and reviews before hiring\r\n\r\n6. INTELLECTUAL PROPERTY\r\nAll content on FixLanka, including logos, text, and graphics, is owned by FixLanka and protected by copyright laws.\r\n\r\n7. TERMINATION\r\nWe reserve the right to suspend or terminate accounts that violate these terms or engage in fraudulent activity.\r\n\r\n8. CHANGES TO TERMS\r\nWe may modify these terms at any time. Continued use of the platform constitutes acceptance of modified terms.\r\n\r\nLast updated: January 2026', 'Draft', '2026-02-18 14:00:54', 'terms'),
(2, 'Privacy Policy', 'How we collect, use, and protect your personal information', 'At FixLanka, we take your privacy seriously. This Privacy Policy explains how we collect, use, and safeguard your information.\r\n\r\n1. INFORMATION WE COLLECT\r\n\r\nPersonal Information:\r\n- Name, email address, phone number\r\n- Physical address for service delivery\r\n- Payment information (processed securely)\r\n- Profile photos and identification documents (for service providers)\r\n\r\nUsage Information:\r\n- Pages visited and features used\r\n- Search queries and preferences\r\n- Device information and IP address\r\n- Cookies and similar technologies\r\n\r\n2. HOW WE USE YOUR INFORMATION\r\n\r\nWe use your information to:\r\n- Provide and improve our services\r\n- Connect users with service providers\r\n- Process payments and transactions\r\n- Send notifications and updates\r\n- Prevent fraud and ensure platform security\r\n- Comply with legal obligations\r\n\r\n3. INFORMATION SHARING\r\n\r\nWe do not sell your personal information. We may share data with:\r\n- Service providers you choose to work with\r\n- Payment processors for transactions\r\n- Law enforcement when legally required\r\n- Third-party analytics services (anonymized data)\r\n\r\n4. DATA SECURITY\r\n\r\nWe implement industry-standard security measures:\r\n- Encrypted data transmission (SSL/TLS)\r\n- Secure password storage with hashing\r\n- Regular security audits\r\n- Access controls and monitoring\r\n- Secure payment processing through trusted partners\r\n\r\n5. YOUR RIGHTS\r\n\r\nYou have the right to:\r\n- Access your personal data\r\n- Correct inaccurate information\r\n- Request data deletion (subject to legal requirements)\r\n- Opt-out of marketing communications\r\n- Export your data\r\n\r\n6. COOKIES\r\n\r\nWe use cookies to:\r\n- Keep you logged in\r\n- Remember your preferences\r\n- Analyze platform usage\r\n- Improve user experience\r\n\r\nYou can control cookies through your browser settings.\r\n\r\n7. DATA RETENTION\r\n\r\nWe retain your information as long as your account is active or as needed to provide services. After account deletion, we may retain certain data for legal and security purposes.\r\n\r\n8. CHILDREN\'S PRIVACY\r\n\r\nOur services are not intended for users under 18. We do not knowingly collect information from children.\r\n\r\n9. INTERNATIONAL USERS\r\n\r\nYour information may be stored and processed in Sri Lanka or other countries where we operate.\r\n\r\n10. CHANGES TO POLICY\r\n\r\nWe may update this policy periodically. We will notify you of significant changes via email or platform notification.\r\n\r\nContact us at privacy@fixlanka.lk for any privacy concerns.\r\n\r\nLast updated: January 2026', 'Published', '2026-02-18 14:01:18', 'privacy'),
(3, 'Frequently Asked Questions', 'Common questions and answers about FixLanka services', 'FREQUENTLY ASKED QUESTIONS\r\n\r\n=== FOR USERS ===\r\n\r\nQ: How do I post a job request?\r\nA: Log in to your account, click \"Post a Job\" from your dashboard, fill in the job details including category, location, urgency, and upload photos if needed. Submit the request and wait for quotes from service providers.\r\n\r\nQ: How long does it take to receive quotes?\r\nA: Typically, you will start receiving quotes within 1-2 hours. Urgent requests often get faster responses.\r\n\r\nQ: How do I choose the best service provider?\r\nA: Review their profile, ratings, completed jobs, and the quotes they provide. Read reviews from previous customers. Compare prices and estimated completion times.\r\n\r\nQ: Is payment secure?\r\nA: Yes! All payments are processed through our secure platform using encrypted payment gateways. Your financial information is never stored on our servers.\r\n\r\nQ: What if I\'m not satisfied with the service?\r\nA: Contact our support team immediately. We have a dispute resolution process and may offer refunds or arrange for corrective work depending on the situation.\r\n\r\nQ: Can I cancel a job request?\r\nA: Yes, you can cancel before accepting a quote without penalty. After accepting a quote, cancellation terms apply and may incur fees.\r\n\r\n=== FOR SERVICE PROVIDERS ===\r\n\r\nQ: How do I become a service provider on FixLanka?\r\nA: Click \"Join as Service Provider\" and complete the registration form. Provide your credentials, qualifications, and work experience. Our team will verify your information within 24-48 hours.\r\n\r\nQ: What are the fees?\r\nA: FixLanka charges a 10% platform fee on completed jobs. There are no upfront costs or subscription fees. You only pay when you earn.\r\n\r\nQ: How do I get paid?\r\nA: After completing a job and receiving customer approval, payment is processed within 3-5 business days to your registered bank account.\r\n\r\nQ: Can I work in multiple districts?\r\nA: Yes! During registration, you can select all districts where you offer services.\r\n\r\nQ: How does the rating system work?\r\nA: Customers rate your service after job completion on a 5-star scale. Maintaining high ratings increases your visibility and job opportunities.\r\n\r\nQ: What if a customer doesn\'t pay?\r\nA: Our platform requires payment confirmation before job completion. Contact support if you encounter payment issues.\r\n\r\n=== TECHNICAL QUESTIONS ===\r\n\r\nQ: Which browsers are supported?\r\nA: FixLanka works best on Chrome, Firefox, Safari, and Edge (latest versions).\r\n\r\nQ: Is there a mobile app?\r\nA: Currently, we offer a mobile-responsive website. A dedicated mobile app is coming soon!\r\n\r\nQ: How do I reset my password?\r\nA: Click \"Forgot Password\" on the login page and follow the instructions sent to your email.\r\n\r\nQ: Why can\'t I upload photos?\r\nA: Ensure your images are in JPG, PNG, or JPEG format and under 5MB each. Check your internet connection.\r\n\r\n=== CONTACT US ===\r\n\r\nStill have questions? Reach us at:\r\n- Email: support@fixlanka.lk\r\n- Phone: +94 11 234 5678\r\n- Live Chat: Available Mon-Fri, 9 AM - 6 PM\r\n\r\nLast updated: January 2026', 'Published', '2026-02-19 18:15:07', 'faq'),
(4, 'About FixLanka', 'Learn about our mission, vision, and the team behind FixLanka', 'ABOUT FIXLANKA\r\n\r\n=== OUR STORY ===\r\n\r\nFixLanka was founded in 2024 with a simple mission: to make home repair and maintenance services accessible, reliable, and affordable for everyone in Sri Lanka.\r\n\r\nWe recognized that finding trustworthy, skilled professionals for home repairs was a major challenge. Customers struggled to find reliable service providers, while skilled repairers and companies had difficulty reaching potential clients.\r\n\r\nFixLanka bridges this gap by creating a transparent, efficient marketplace that connects homeowners with verified, qualified service providers across Sri Lanka.\r\n\r\n=== OUR MISSION ===\r\n\r\nTo revolutionize the home services industry in Sri Lanka by:\r\n- Providing easy access to qualified professionals\r\n- Ensuring transparency and trust through verified profiles and reviews\r\n- Offering fair pricing and secure payment processing\r\n- Supporting local businesses and skilled workers\r\n- Delivering exceptional customer service\r\n\r\n=== OUR VISION ===\r\n\r\nTo become Sri Lanka\'s most trusted and comprehensive home services platform, expanding our services across all districts and becoming the go-to solution for every household repair and maintenance need.\r\n\r\n=== WHAT WE OFFER ===\r\n\r\nFor Homeowners:\r\n- Quick and easy job posting\r\n- Access to verified service providers\r\n- Competitive quotes from multiple professionals\r\n- Secure payment processing\r\n- Quality assurance and customer support\r\n\r\nFor Service Providers:\r\n- Increased visibility and job opportunities\r\n- Direct access to customers\r\n- Fair and transparent pricing\r\n- Timely payments\r\n- Business growth support\r\n\r\n=== OUR CATEGORIES ===\r\n\r\nWe cover all major home service needs:\r\n- Plumbing repairs and installations\r\n- Electrical work and wiring\r\n- Carpentry and furniture\r\n- Painting and decorating\r\n- HVAC services\r\n- Appliance repair\r\n- Roofing and waterproofing\r\n- Masonry and construction\r\n- Landscaping and gardening\r\n- And many more!\r\n\r\n=== OUR VALUES ===\r\n\r\nTRUST: We verify all service providers and maintain strict quality standards.\r\n\r\nTRANSPARENCY: Clear pricing, honest reviews, and open communication.\r\n\r\nQUALITY: We partner only with skilled, professional service providers.\r\n\r\nINNOVATION: Continuously improving our platform with new features and technologies.\r\n\r\nCUSTOMER FIRST: Your satisfaction is our top priority.\r\n\r\n=== COVERAGE ===\r\n\r\nWe currently serve customers across all 25 districts of Sri Lanka, with growing networks of service providers in:\r\n- Colombo, Gampaha, Kalutara\r\n- Kandy, Matale, Nuwara Eliya\r\n- Galle, Matara, Hambantota\r\n- Jaffna, Kilinochchi, Mannar\r\n- And all other districts\r\n\r\n=== OUR TEAM ===\r\n\r\nFixLanka is powered by a dedicated team of technology experts, customer service professionals, and industry specialists committed to transforming the home services sector.\r\n\r\n=== JOIN US ===\r\n\r\nWhether you\'re a homeowner seeking quality services or a skilled professional looking to grow your business, FixLanka is here for you.\r\n\r\nJoin thousands of satisfied customers and service providers who trust FixLanka for their home service needs.\r\n\r\nContact Us:\r\n- Email: info@fixlanka.lk\r\n- Phone: +94 11 234 5678\r\n- Address: 123 Galle Road, Colombo 03, Sri Lanka\r\n\r\nLast updated: January 2026', 'Published', '2026-02-18 08:12:45', 'about'),
(5, 'Help Center', 'Comprehensive guide to using FixLanka platform', 'FIXLANKA HELP CENTER\r\n\r\nWelcome to the FixLanka Help Center! Find step-by-step guides and answers to common questions.\r\n\r\n=== GETTING STARTED ===\r\n\r\nCREATING AN ACCOUNT\r\n1. Click \"Sign Up\" on the homepage\r\n2. Choose account type (User or Service Provider)\r\n3. Fill in required information\r\n4. Verify your email address\r\n5. Complete your profile\r\n\r\nNAVIGATING THE PLATFORM\r\n- Dashboard: Your central hub for all activities\r\n- Job Requests: View and manage your service requests\r\n- Messages: Communicate with service providers/customers\r\n- Profile: Update your information and settings\r\n- Notifications: Stay updated on important activities\r\n\r\n=== FOR USERS ===\r\n\r\nHOW TO POST A JOB REQUEST\r\n1. Log in to your account\r\n2. Click \"Post a Job\" button\r\n3. Select service category\r\n4. Enter job title and detailed description\r\n5. Add your location (district and address)\r\n6. Set urgency level (medium or urgent)\r\n7. Upload photos (optional but recommended)\r\n8. Set expected completion date\r\n9. Choose provider type (individual, company, or both)\r\n10. Review and submit\r\n\r\nRECEIVING AND COMPARING QUOTES\r\n- Service providers will send quotes within hours\r\n- Review each quote carefully\r\n- Check provider profiles, ratings, and reviews\r\n- Compare pricing and estimated completion time\r\n- Ask questions through the messaging system\r\n- Accept the quote that best meets your needs\r\n\r\nPAYMENT PROCESS\r\n1. Accept a quote from your preferred provider\r\n2. Job status changes to \"In Progress\"\r\n3. Service provider completes the work\r\n4. Review and approve the completed work\r\n5. Process payment through secure gateway\r\n6. Rate and review the service provider\r\n\r\nMANAGING YOUR JOBS\r\n- Track job status in real-time\r\n- Communicate with service providers\r\n- Upload additional photos or details\r\n- Request updates or modifications\r\n- Mark jobs as completed\r\n- Report issues if needed\r\n\r\n=== FOR SERVICE PROVIDERS ===\r\n\r\nSETTING UP YOUR PROFILE\r\n1. Complete all required fields\r\n2. Add professional profile photo\r\n3. Write detailed \"About\" section\r\n4. List your skills and qualifications\r\n5. Specify service districts\r\n6. Set your availability status\r\n7. Upload certificates/licenses (if applicable)\r\n\r\nFINDING AND BIDDING ON JOBS\r\n1. Browse available job requests in your category\r\n2. Filter by location and urgency\r\n3. Review job details carefully\r\n4. Submit competitive quotes with clear pricing\r\n5. Include estimated completion time\r\n6. Add a professional message explaining your approach\r\n\r\nWINNING JOBS\r\n- Respond quickly to new requests\r\n- Offer competitive pricing\r\n- Maintain high ratings and positive reviews\r\n- Provide detailed, professional quotes\r\n- Build a strong profile with completed jobs\r\n\r\nCOMPLETING JOBS\r\n1. Confirm job details with customer\r\n2. Schedule work at convenient time\r\n3. Arrive on time and work professionally\r\n4. Update customer on progress\r\n5. Complete work to high standards\r\n6. Request customer approval\r\n7. Receive payment through platform\r\n\r\nGROWING YOUR BUSINESS\r\n- Maintain excellent service quality\r\n- Respond promptly to inquiries\r\n- Keep your profile updated\r\n- Earn positive reviews\r\n- Consider promotion packages for increased visibility\r\n\r\n=== PAYMENT AND BILLING ===\r\n\r\nPAYMENT METHODS\r\n- Credit/Debit Cards (Visa, Mastercard)\r\n- Online Banking\r\n- Mobile Wallets\r\n- Bank Transfer\r\n\r\nSECURITY\r\n- All transactions are encrypted\r\n- We never store full card details\r\n- PCI-DSS compliant payment processing\r\n- Secure authentication protocols\r\n\r\nREFUND POLICY\r\n- Full refund if work not started\r\n- Partial refund for incomplete work\r\n- Quality issues reviewed case-by-case\r\n- Disputes handled by support team\r\n\r\n=== SAFETY AND SECURITY ===\r\n\r\nSTAYING SAFE\r\n- Verify service provider credentials\r\n- Check ratings and reviews\r\n- Communicate through platform messaging\r\n- Keep payment records\r\n- Report suspicious activity immediately\r\n\r\nPRIVACY PROTECTION\r\n- Your personal data is encrypted\r\n- We never sell your information\r\n- Control your privacy settings\r\n- Review our Privacy Policy for details\r\n\r\n=== ACCOUNT MANAGEMENT ===\r\n\r\nUPDATING YOUR PROFILE\r\n1. Go to Profile Settings\r\n2. Click \"Edit Profile\"\r\n3. Update desired information\r\n4. Save changes\r\n\r\nCHANGING PASSWORD\r\n1. Go to Account Settings\r\n2. Click \"Change Password\"\r\n3. Enter current password\r\n4. Enter new password (min 8 characters)\r\n5. Confirm new password\r\n6. Save changes\r\n\r\nNOTIFICATION PREFERENCES\r\n- Email notifications\r\n- SMS alerts (if enabled)\r\n- In-app notifications\r\n- Customize frequency and types\r\n\r\n=== TROUBLESHOOTING ===\r\n\r\nCAN\'T LOG IN?\r\n- Check email and password\r\n- Use \"Forgot Password\" to reset\r\n- Clear browser cache and cookies\r\n- Try different browser\r\n- Contact support if issue persists\r\n\r\nUPLOAD ISSUES?\r\n- Check file size (max 5MB per photo)\r\n- Use supported formats (JPG, PNG, JPEG)\r\n- Check internet connection\r\n- Try reducing image resolution\r\n\r\nPAYMENT FAILED?\r\n- Verify card details and limits\r\n- Check internet connection\r\n- Try alternative payment method\r\n- Contact your bank\r\n- Reach out to our support team\r\n\r\n=== CONTACT SUPPORT ===\r\n\r\nNeed more help? We\'re here for you!\r\n\r\nEmail Support: support@fixlanka.lk\r\nResponse time: Within 24 hours\r\n\r\nPhone Support: +94 11 234 5678\r\nAvailable: Mon-Fri, 9 AM - 6 PM\r\n\r\nLive Chat: Available on website\r\nStatus: Online during business hours\r\n\r\nAddress: 123 Galle Road, Colombo 03, Sri Lanka\r\n\r\n=== FEEDBACK ===\r\n\r\nWe value your feedback! Help us improve:\r\n- Rate your experience\r\n- Suggest new features\r\n- Report bugs\r\n- Share your success stories\r\n\r\nEmail: feedback@fixlanka.lk\r\n\r\nLast updated: January 2026', 'Published', '2026-02-18 06:05:29', 'help');

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
(9996, 'Test', 'User', 'testuser@gmail.com', '$2y$10$KDNeRGuhcQw4.jNBJ27i6e5.3uKfulcU/rWBar/ozDDtKR4bKvAqy', NULL, NULL, '123 Test St', NULL, '2026-03-03 12:16:14', '2026-03-31 09:17:08', 'ACTIVE', 0, NULL, NULL);

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
('18sddv7ahr4bpcl0epnt140c2h', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-06 06:30:55', 1),
('1nhpmdf7ls70c7flvipkmld8hc', 5, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-03 10:10:21', 1),
('3at4c468cf9ecqh4g9ngdnqkdt', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-01 06:23:13', 1),
('3jo6oldf6v3d1aj2mppolpsrjv', 9995, 'user', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-08 09:26:15', 1),
('3ou0rf4bkmt7822r1qia1att0t', 1, 'moderator', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-01 10:54:27', 1),
('46eecogp60jbsi6lhe37ei5peq', 2, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-02 14:46:27', 1),
('4fho5idqt5f02j4c4spbt5p7qu', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-02 18:54:06', 1),
('4gvf3i0ubv574p3guub4ud9ont', 9996, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-01 06:49:12', 1),
('5129bd2207ea42bc0fe7b85bda41f920', 9998, 'company', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-08 07:54:38', 1),
('5kmavd7fhkk5op1kirc2vji74m', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 11:52:04', 1),
('7rlq8o6eengs2p7egqpka4e0om', 9995, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-04 08:29:35', 1),
('9lkpcfthcsgtpc2nc0do8tkkic', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-06 08:07:46', 1),
('9p7708t3jvbri465pj0liu0ffh', 1, 'moderator', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-02 17:51:01', 1),
('9ub23fqjuan6cp7anmg349r6hc', 108, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-07 05:39:07', 1),
('a97d0242jephpvn7g7jeghvd0e', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-07 11:42:33', 1),
('aogn7f1uhaa44o2f6e863rplfd', 0, 'admin', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-02 16:54:11', 1),
('br1649krvvtgtb0coa2v6lbt10', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-01 18:33:47', 1),
('cr796jr4d1cd8o11gmippaueh6', 9995, 'user', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-03 12:35:50', 1),
('ds583ij4a5vmu45p2vd94fs2h9', 9996, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-04 08:39:39', 1),
('e864tcp0ggpi8e1f95pjtnqgv2', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-06 12:29:10', 1),
('f2cspselpf2tphm075j07diq9e', 1, 'moderator', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 08:59:17', 1),
('fm10cht8jvedkilcua70tg3vr0', 1, 'moderator', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-04 08:38:28', 1),
('foncegqmv7s7dj4ah531nv46no', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 10:48:21', 1),
('frlprno72tcshja545p7tltrvi', 9996, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-31 09:17:58', 1),
('g04hiidrpt5143ic2egc402lmm', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 08:59:03', 1),
('g5i2f56cotjk76gj91jp7lj7ko', 5, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-02 19:43:13', 1),
('gjts7s2lh0cprip3i6eq3m6r2g', 107, 'repairer', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 08:37:10', 1),
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
('n8ttretkf9lpudtffnnolavhvj', 0, 'admin', 'Desktop', 'Unknown', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.7920', '2026-03-04 11:12:23', 1),
('nd15skkfh28gk8vhp8ddst2lb8', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-01 18:49:57', 1),
('noflm5baj1cq1cqtd0bfbh2794', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-04 11:51:46', 1),
('oct8hk81i0gt2r7l2kre3nogd8', 0, 'admin', 'Desktop', 'Unknown', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.7920', '2026-03-04 10:43:06', 1),
('ors96h706kifk1imcr626s79pq', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-04 12:23:22', 1),
('ov7m0eut0ecl56alftee1qk038', 0, 'admin', 'Desktop', 'Chrome', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-04 09:00:01', 1),
('p744rr39ka2iobfl8u8ega858a', 9995, 'user', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-03 10:13:14', 1),
('q2h6u6sjdlsu2sr0r3ssg4hll0', 0, 'admin', 'Desktop', 'Unknown', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.7920', '2026-03-04 10:47:47', 1),
('r2a4f984u5p4can46q669dl1id', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-08 11:01:24', 1),
('sa1jk8onvo5l2evko0e981l628', 9997, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-06 13:56:02', 1),
('smgsta4e3mbmh9fess7v4mtg92', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-07 12:22:29', 1),
('t0s3p1gs7dqi48la989irr28uj', 5, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-04 07:58:20', 1),
('t6uc45h70q5927nd5k3g7gr9e0', 5, 'company', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-02 16:58:58', 1),
('tbdsih89ltf5k2qj81uc3f8g8j', 0, 'admin', 'Desktop', 'Unknown', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.7920', '2026-03-04 10:44:57', 1),
('tmdooouobsu7mboun006qts495', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-07 05:38:27', 1),
('u7lgltl0lcoce4lrtu49nva4ie', 107, 'repairer', 'Desktop', 'Edge', 'Windows 10/11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-03 12:53:51', 1),
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
  ADD KEY `idx_type` (`content_type`);

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
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  MODIFY `quotation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

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
  MODIFY `contract_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `contract_audit_log`
--
ALTER TABLE `contract_audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contract_budget_adjustments`
--
ALTER TABLE `contract_budget_adjustments`
  MODIFY `adjustment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contract_chats`
--
ALTER TABLE `contract_chats`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `milestone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `contract_notifications`
--
ALTER TABLE `contract_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `contract_payment_history`
--
ALTER TABLE `contract_payment_history`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `escrow_wallet`
--
ALTER TABLE `escrow_wallet`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
  MODIFY `issue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `promotion`
--
ALTER TABLE `promotion`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairer`
--
ALTER TABLE `repairer`
  MODIFY `repairer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

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
  MODIFY `quote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `staff_summary_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staticcontent`
--
ALTER TABLE `staticcontent`
  MODIFY `content_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `ticketmessage`
--
ALTER TABLE `ticketmessage`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9997;

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
