-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2026 at 10:47 AM
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
('admin', '$2y$10$.9dCKhH7pHfzttdX/mBF/eBmf8usZy8uSdqEVRR1VliJQNegsSTDm', 'admin@gmail.com', '2025-10-23 20:19:26');

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
  `type` enum('banner','featured','sponsored') NOT NULL,
  `budget` decimal(10,2) NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','active','expired','rejected','inactive','suspended','deleted','sent') DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `moderator_notes` text DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
(1, 'Plumbing', '2025-10-23 20:10:47'),
(2, 'Electrical', '2025-10-23 20:10:47'),
(3, 'HVAC', '2025-10-23 20:10:47'),
(4, 'Cleaning', '2025-10-23 20:10:47'),
(5, 'Carpentry', '2025-10-23 20:10:47'),
(6, 'Painting', '2025-10-23 20:10:47'),
(7, 'Appliance Repair', '2025-10-23 20:10:47'),
(8, 'Roofing', '2025-10-23 20:10:47'),
(9, 'Landscaping', '2025-10-23 20:10:47'),
(10, 'Pest Control', '2025-10-23 20:10:47'),
(11, 'Home Security', '2025-10-23 20:10:47'),
(12, 'Interior Design', '2025-10-23 20:10:47'),
(13, 'Flooring', '2025-10-23 20:10:47'),
(14, 'Masonry', '2025-10-23 20:10:47'),
(15, 'Welding', '2025-10-23 20:10:47'),
(16, 'Glass & Mirror', '2025-10-23 20:10:47'),
(17, 'Tile Work', '2025-10-23 20:10:47'),
(18, 'Drywall', '2025-10-23 20:10:47'),
(19, 'Insulation', '2025-10-23 20:10:47'),
(20, 'Window Installation', '2025-10-23 20:10:47');

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
  `rating` decimal(3,2) DEFAULT 0.00,
  `date_of_joined` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`company_id`, `name`, `business_type`, `registration_no`, `tax_id`, `address`, `email`, `website`, `contact_no`, `districts`, `password`, `description`, `rating`, `date_of_joined`) VALUES
(1, 'Fix Masters Ltd', 'Plumbing,Electrical,Carpentry', 'REG001', NULL, '456 Business Avenue, Colombo', 'company@gmail.com', NULL, '0771234567', 'Colombo,Gampaha,Kandy', '$2y$10$o6SZCYOGjee02YYmLhTdCegHXWxylPFT7GnaVOqZ1sMbrZBGChu8K', 'Professional repair and maintenance services', 0.00, '2025-10-23 20:19:26'),
(2, 'HomeFix Solutions Ltd', NULL, 'PV12345', NULL, 'No. 123, Galle Road, Colombo 03', 'info@homefixsolutions.lk', NULL, '0112345678', NULL, '', NULL, 4.80, '2025-12-18 05:25:45'),
(3, 'ElectroTech Services', NULL, 'PV23456', NULL, 'No. 456, Duplication Road, Colombo 04', 'contact@electrotech.lk', NULL, '0112456789', NULL, '', NULL, 4.70, '2025-12-18 05:25:45'),
(4, 'CleanPro Lanka', NULL, 'PV34567', NULL, 'No. 789, Baseline Road, Colombo 09', 'hello@cleanpro.lk', NULL, '0112567890', NULL, '', NULL, 4.90, '2025-12-18 05:25:45'),
(5, 'AirCool HVAC', NULL, 'PV45678', NULL, 'No. 321, High Level Road, Nugegoda', 'service@aircool.lk', NULL, '0112678901', NULL, '', NULL, 4.60, '2025-12-18 05:25:45'),
(6, 'BuildMaster Construction', NULL, 'PV56789', NULL, 'No. 654, Nawala Road, Rajagiriya', 'info@buildmaster.lk', NULL, '0112789012', NULL, '', NULL, 4.80, '2025-12-18 05:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `companyjobpost`
--

CREATE TABLE `companyjobpost` (
  `posting_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `location` text NOT NULL,
  `posted_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('open','closed','filled') DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companyquotation`
--

CREATE TABLE `companyquotation` (
  `quotation_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companyquotation`
--

INSERT INTO `companyquotation` (`quotation_id`, `company_id`, `request_id`, `user_id`, `title`, `description`, `labor_cost`, `material_cost`, `transport_cost`, `other_charges`, `total_amount`, `start_date`, `completion_date`, `estimated_duration`, `payment_terms`, `warranty_period`, `additional_terms`, `status`, `created_at`, `updated_at`) VALUES
(3, 1, 4, 4, 'Kitchen Renovation Quote', 'Full renovation including plumbing + tiling.', 20000.00, 25000.00, 2000.00, 1000.00, 48000.00, '2026-02-01', '2026-02-10', 9, '50_50', '6_months', 'Includes cleanup.', 'pending', '2026-01-28 07:40:12', '2026-01-28 07:40:12');

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--

CREATE TABLE `contract` (
  `contract_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `quotation_id` int(11) DEFAULT NULL,
  `job_request_id` int(11) DEFAULT NULL,
  `contract_number` varchar(50) DEFAULT NULL,
  `milestone_plan` tinyint(1) NOT NULL DEFAULT 1,
  `total_budget` decimal(12,2) NOT NULL,
  `budget_type` varchar(20) DEFAULT 'fixed',
  `budget_min` decimal(12,2) DEFAULT NULL,
  `budget_max` decimal(12,2) DEFAULT NULL,
  `tax_inclusive` tinyint(1) DEFAULT 0,
  `payment_method` enum('milestone_based','full_upfront','50_50','30_70','completion') DEFAULT 'milestone_based',
  `pricing_type` varchar(50) DEFAULT 'fixed_price',
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `spending_cap` decimal(12,2) DEFAULT NULL,
  `advance_payment_pct` decimal(5,2) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `contract_date` date NOT NULL,
  `user_signature` varchar(255) NOT NULL,
  `company_signature` varchar(255) NOT NULL,
  `terms_conditions` text DEFAULT NULL,
  `project_title` varchar(255) DEFAULT NULL,
  `project_description` text DEFAULT NULL,
  `project_location` text DEFAULT NULL,
  `project_reference` varchar(100) DEFAULT NULL,
  `scope_description` text DEFAULT NULL,
  `scope_inclusions` text DEFAULT NULL,
  `scope_exclusions` text DEFAULT NULL,
  `scope_standards` text DEFAULT NULL,
  `materials_responsibility` varchar(100) DEFAULT NULL,
  `communication_channel` varchar(100) DEFAULT NULL,
  `dispute_resolution` varchar(100) DEFAULT NULL,
  `late_payment_penalty` text DEFAULT NULL,
  `pause_work_clause` text DEFAULT NULL,
  `time_extension_clause` text DEFAULT NULL,
  `variation_clause` text DEFAULT NULL,
  `sent_to_customer` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `customer_response` varchar(50) DEFAULT NULL,
  `customer_response_at` timestamp NULL DEFAULT NULL,
  `terms_accepted` tinyint(1) DEFAULT 0,
  `progress_percentage` int(11) DEFAULT 0,
  `chat_active` tinyint(1) DEFAULT 0,
  `escrow_enabled` tinyint(1) DEFAULT 0,
  `auto_generated` tinyint(1) DEFAULT 0,
  `amount_pending` decimal(12,2) DEFAULT NULL,
  `payment_status` enum('pending','partial','completed') DEFAULT 'pending',
  `undo_deadline` datetime DEFAULT NULL,
  `undo_requested` tinyint(1) DEFAULT 0,
  `total_milestones` int(11) DEFAULT 0,
  `status` enum('draft','active','completed','terminated','sent') DEFAULT 'draft',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `photos` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobrequest`
--

INSERT INTO `jobrequest` (`request_id`, `user_id`, `category_id`, `title`, `description`, `status`, `district`, `address`, `service_provider_type`, `urgency`, `finish_date`, `dateCreated`, `photos`) VALUES
(2, 1, 3, 'AC install', 'install AC', 'pending', 'Vavuniya', 'example, address', 'individual,company', 'medium', '2025-10-25', '2025-10-23 20:27:27', NULL),
(3, 2, 2, 'Sink', 'light up', 'pending', 'Trincomalee', 'trinco nilaweli', 'individual,company', 'medium', '2025-10-28', '2025-10-23 21:36:12', NULL),
(4, 4, 1, 'Kitchen Sink', 'Broken pipes', 'pending', 'Colombo', '119, olket road', 'both', 'medium', '2026-02-03', '2026-01-28 05:51:53', NULL),
(5, 5, 1, 'czdxxc', 'vxbncv', 'pending', 'Batticaloa', 'fg,jhgfgb b', 'individual,company', 'medium', '2026-02-23', '2026-02-13 18:10:13', NULL);

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
  `status` enum('pending','in_progress','completed','overdue') DEFAULT 'pending'
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
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `moderator`
--

INSERT INTO `moderator` (`moderator_id`, `username`, `password`, `email`, `assigned_section`, `status`, `created_at`) VALUES
(1, 'moderator', '$2y$10$7rPUIUbWnLm623o78dLOHulOyL2rd.M.KqfJGPwluccb1vPA9r.Le', 'moderator@gmail.com', 'User Support', 'active', '2025-10-23 20:19:26');

-- --------------------------------------------------------

--
-- Table structure for table `moderator_management_log`
--

CREATE TABLE `moderator_management_log` (
  `log_id` int(11) NOT NULL,
  `admin_username` varchar(100) NOT NULL,
  `moderator_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
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
  `status` enum('sent','pending','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notification_id`, `title`, `message`, `send_date`, `recipient_type`, `status`) VALUES
(1, 'welcome all..!!!', 'first notification..', '2025-10-23 20:29:43', 'repairer', 'sent'),
(3, 'System Update', 'Maintenance tonight at 10 PM.', '2026-01-28 06:23:56', 'all', 'sent'),
(4, 'Welcome!', 'Thanks for joining FixLanka.', '2026-01-28 06:23:56', 'user', 'sent'),
(5, 'Reminder', 'Complete your profile to get better matches.', '2026-01-28 06:23:56', 'user', 'sent');

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
  `phoneNumber` varchar(20) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `profilePicture` varchar(500) DEFAULT NULL,
  `ratings` decimal(3,2) DEFAULT 0.00,
  `completedJobsCount` int(11) DEFAULT 0,
  `districts` text DEFAULT NULL,
  `availability` enum('available','busy','unavailable') DEFAULT 'available',
  `dateJoined` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repairer`
--

INSERT INTO `repairer` (`repairer_id`, `f_name`, `l_name`, `email`, `password`, `phoneNumber`, `about`, `profilePicture`, `ratings`, `completedJobsCount`, `districts`, `availability`, `dateJoined`, `category_id`) VALUES
(1, 'John', 'Doe', 'repairer@gmail.com', '$2y$10$oWmNn0oiOO9qcZQux47tzO6IzbgfH4FaMZxAX/CI2XJlNeSz.Vl.C', '0777654321', 'Experienced plumber with 10 years in the field', NULL, 4.50, 25, 'Colombo,Gampaha', 'available', '2025-10-23 20:19:26', 1),
(2, 'John', 'Cena', 'johnCena@email.com', 'hashed_password_123', '0712345678', 'Experienced electrician with 5 years in home repairs.', 'path/to/profile.jpg', 4.50, 12, 'Colombo, Gampaha', 'available', '2025-12-18 04:37:47', 2),
(3, 'Kamal', 'Silva', 'kamal.silva@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0771234567', 'Certified electrician with 15+ years of experience. Specializes in residential and commercial electrical work.', NULL, 4.90, 156, 'Colombo, Dehiwala, Mount Lavinia', 'available', '2025-12-18 05:25:45', 2),
(4, 'Nimal', 'Perera', 'nimal.perera@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0772345678', 'Licensed plumber offering 24/7 emergency services. Expert in pipe repairs and bathroom installations.', NULL, 4.80, 243, 'Colombo, Nugegoda, Maharagama', 'available', '2025-12-18 05:25:45', 1),
(5, 'Saman', 'Fernando', 'saman.fernando@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0773456789', 'Air conditioning and heating specialist. Quick diagnostics and reliable repair services.', NULL, 4.70, 89, 'Colombo, Borella, Maradana', 'available', '2025-12-18 05:25:45', 4),
(6, 'Amara', 'Jayasinghe', 'amara.j@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0774567890', 'Professional cleaning service with eco-friendly products. Trusted by 200+ families.', NULL, 5.00, 178, 'Colombo, Wellawatta, Bambalapitiya', 'available', '2025-12-18 05:25:45', 3),
(7, 'Chaminda', 'Rathnayake', 'chaminda.r@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0775678901', 'Skilled carpenter specializing in custom furniture and home repairs. Quality craftsmanship guaranteed.', NULL, 4.60, 134, 'Colombo, Rajagiriya, Kotte', 'available', '2025-12-18 05:25:45', 5),
(8, 'Lakshmi', 'Wijeratne', 'lakshmi.w@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0776789012', 'Professional painter with attention to detail. Transforms spaces with quality finishes.', NULL, 4.90, 267, 'Colombo, Kiribathgoda, Kadawatha', 'available', '2025-12-18 05:25:45', 5),
(9, 'Roshan', 'Mendis', 'roshan.m@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0777890123', 'Expert in washing machine, refrigerator, and microwave repairs. Same-day service available.', NULL, 4.50, 98, 'Colombo, Piliyandala, Moratuwa', 'busy', '2025-12-18 05:25:45', 2),
(10, 'Priya', 'Gunasekara', 'priya.g@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0778901234', 'Professional gardener offering lawn care, pruning, and landscape design services.', NULL, 4.80, 156, 'Colombo, Battaramulla, Thalawathugoda', 'available', '2025-12-18 05:25:45', 3);

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

--
-- Dumping data for table `repairerquote`
--

INSERT INTO `repairerquote` (`quote_id`, `request_id`, `repairer_id`, `quoteAmount`, `estimatedDays`, `warrantyPeriod`, `validUntil`, `materialsIncluded`, `message`, `status`, `dateSubmitted`) VALUES
(1, 4, 1, 45000.00, 3, 30, '2026-02-15', 1, 'Can start tomorrow. Includes materials.', 'pending', '2026-01-28 07:35:32'),
(2, 4, 4, 45000.00, 3, 30, '2026-02-15', 1, 'Can start tomorrow. Includes materials.', 'pending', '2026-01-28 07:35:32');

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
  `skill_category` varchar(100) DEFAULT NULL,
  `count` int(11) DEFAULT 0,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staticcontent`
--

CREATE TABLE `staticcontent` (
  `content_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `content_type` enum('terms','privacy','faq','about','help') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_activity_logs`
--

CREATE TABLE `system_activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_role` varchar(50) DEFAULT NULL,
  `activity_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ad_status_history`
--

CREATE TABLE `ad_status_history` (
  `history_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_by_role` varchar(50) DEFAULT NULL,
  `changed_by_id` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `is_override` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ad_reports`
--

CREATE TABLE `ad_reports` (
  `report_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `reporter_type` enum('user','repairer','company','moderator') NOT NULL,
  `report_category` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `severity` enum('low','medium','high','critical') DEFAULT 'medium',
  `status` enum('pending','investigating','resolved','dismissed','escalated') DEFAULT 'pending',
  `assigned_to` int(11) DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `moderator_activity`
--

CREATE TABLE `moderator_activity` (
  `activity_id` int(11) NOT NULL,
  `moderator_id` int(11) NOT NULL,
  `action_type` varchar(100) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `AdminAlert`
--

CREATE TABLE `AdminAlert` (
  `alert_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `target_role` enum('User','Repairer','Company','Moderator','All') DEFAULT 'All',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_moderation_status`
--

CREATE TABLE `account_moderation_status` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `account_type` enum('User','Repairer','Company') NOT NULL,
  `account_status` enum('ACTIVE','SUSPENDED','BANNED') DEFAULT 'ACTIVE',
  `banned_permanent` tinyint(1) DEFAULT 0,
  `suspended_until` datetime DEFAULT NULL,
  `moderation_reason` text DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_moderation_cases`
--

CREATE TABLE `account_moderation_cases` (
  `case_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `target_type` enum('User','Repairer','Company') NOT NULL,
  `admin_username` varchar(100) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `duration_days` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status_before` varchar(50) DEFAULT NULL,
  `status_after` varchar(50) DEFAULT NULL,
  `is_permanent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `notification_id` int(11) NOT NULL,
  `admin_username` varchar(100) NOT NULL,
  `notification_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `account_type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_milestone`
--

CREATE TABLE `contract_milestone` (
  `milestone_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `milestone_number` int(11) DEFAULT 1,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `status` enum('pending','in_progress','submitted','approved','rejected','completed','overdue') DEFAULT 'pending',
  `proof_files` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `escrow_held` decimal(10,2) DEFAULT 0.00,
  `payment_released` decimal(10,2) DEFAULT 0.00,
  `payment_released_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_budget_adjustments`
--

CREATE TABLE `contract_budget_adjustments` (
  `adjustment_id` int(11) NOT NULL,
  `contract_id` int(11) NOT NULL,
  `original_amount` decimal(12,2) NOT NULL,
  `requested_amount` decimal(12,2) NOT NULL,
  `adjustment_amount` decimal(12,2) NOT NULL,
  `adjustment_percentage` decimal(5,2) DEFAULT 0.00,
  `reason` text DEFAULT NULL,
  `justification` text DEFAULT NULL,
  `supporting_documents` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `requested_by` int(11) DEFAULT NULL,
  `requested_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
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
  `balance` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `escrow_transaction`
--

CREATE TABLE `escrow_transaction` (
  `transaction_id` int(11) NOT NULL,
  `wallet_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `type` enum('deposit','hold','release','refund') NOT NULL,
  `description` text DEFAULT NULL,
  `related_contract_id` int(11) DEFAULT NULL,
  `related_milestone_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SupportTicket`
--

CREATE TABLE `SupportTicket` (
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('user','company','repairer') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `urgency` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') DEFAULT 'open',
  `project_id` int(11) DEFAULT NULL,
  `attachment` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_employees`
--

CREATE TABLE `company_employees` (
  `employee_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `specialty` varchar(100) DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `status` enum('active','inactive','on_leave') DEFAULT 'active',
  `joined_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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
  `phone` varchar(20) DEFAULT NULL,
  `profilePicture` varchar(500) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `f_name`, `l_name`, `email`, `password`, `profilePicture`, `address`, `district`, `created_at`, `updated_at`) VALUES
(1, 'Test', 'User', 'user@gmail.com', '$2y$10$9HGubb30rUiHC4Cmmqaf8uF5lqi/5y8V.AFh4yhKVZIY8IrFDOq6q', NULL, '123 User Street, Colombo', 'Colombo', '2025-10-23 20:19:26', '2025-10-23 20:19:26'),
(2, 'umesh', 'yapa', 'umesh19@gmail.com', '$2y$10$P16O3lDNY2WlLvoViXjPEOF4viBt5wZIwKkCCiiUfoa7qRw50nkrm', NULL, 'anuradhapura', NULL, '2025-10-23 21:33:02', '2025-10-23 21:33:02'),
(3, 'yapa', 'bandara', 'yapa123@gmail.com', '$2y$10$RYctFmMHkp5pUpBAQ4L8cuMZJfqtJutQxsRLYNf6MZF8/6GHJ0RF2', NULL, '', NULL, '2025-12-19 06:36:58', '2025-12-19 06:36:58'),
(4, 'yapa', 'bandara', 'yapabandara@gmail.com', '$2y$10$/l3Qp72dgaTyjiCULJxHZuwVyXXrnvs3LVRrmJN65q8lGTqYe7q7m', NULL, '119, abcd', NULL, '2026-01-28 04:38:15', '2026-01-28 04:38:15'),
(5, 'john', 'smith', 'js@gmail.com', '$2y$10$em85LJ0vTcD/2NokG7CgxuBPxjogvh7vLGW3ND6TnWSjmx4RAmDz2', NULL, 'Colombo', 'Sri Lanka', '2026-02-13 10:35:09', '2026-02-14 11:38:03');

--
-- Indexes for dumped tables
--

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
  ADD KEY `idx_status` (`status`);

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
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `companyquotation`
--
ALTER TABLE `companyquotation`
  ADD PRIMARY KEY (`quotation_id`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_request` (`request_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `contract`
--
ALTER TABLE `contract`
  ADD PRIMARY KEY (`contract_id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_status` (`status`);

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
  ADD KEY `idx_created` (`dateCreated`),
  ADD KEY `idx_district` (`district`);

--
-- Indexes for table `milestone`
--
ALTER TABLE `milestone`
  ADD PRIMARY KEY (`milestone_id`),
  ADD KEY `idx_contract` (`contract_id`),
  ADD KEY `idx_status` (`status`);

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
  ADD KEY `idx_mod_status` (`status`);

--
-- Indexes for table `moderator_management_log`
--
ALTER TABLE `moderator_management_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_mml_moderator` (`moderator_id`),
  ADD KEY `idx_mml_created` (`created_at`);

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
  ADD KEY `idx_ratings` (`ratings`);

--
-- Indexes for table `repairerapplication`
--
ALTER TABLE `repairerapplication`
  ADD PRIMARY KEY (`app_id`),
  ADD KEY `idx_repairer` (`repairer_id`),
  ADD KEY `idx_posting` (`posting_id`);

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
  ADD KEY `idx_company` (`company_id`);

--
-- Indexes for table `staticcontent`
--
ALTER TABLE `staticcontent`
  ADD PRIMARY KEY (`content_id`),
  ADD KEY `idx_type` (`content_type`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `system_activity_logs`
--
ALTER TABLE `system_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sal_user` (`user_id`),
  ADD KEY `idx_sal_type` (`activity_type`),
  ADD KEY `idx_sal_created` (`created_at`);

--
-- Indexes for table `ad_status_history`
--
ALTER TABLE `ad_status_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_ash_ad` (`ad_id`),
  ADD KEY `idx_ash_created` (`created_at`);

--
-- Indexes for table `ad_reports`
--
ALTER TABLE `ad_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_adr_ad` (`ad_id`),
  ADD KEY `idx_adr_status` (`status`),
  ADD KEY `idx_adr_severity` (`severity`);

--
-- Indexes for table `moderator_activity`
--
ALTER TABLE `moderator_activity`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `idx_ma_moderator` (`moderator_id`),
  ADD KEY `idx_ma_created` (`created_at`);

--
-- Indexes for table `AdminAlert`
--
ALTER TABLE `AdminAlert`
  ADD PRIMARY KEY (`alert_id`),
  ADD KEY `idx_aa_status` (`status`),
  ADD KEY `idx_aa_target` (`target_role`);

--
-- Indexes for table `account_moderation_status`
--
ALTER TABLE `account_moderation_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ams_account` (`account_id`, `account_type`),
  ADD KEY `idx_ams_status` (`account_status`);

--
-- Indexes for table `account_moderation_cases`
--
ALTER TABLE `account_moderation_cases`
  ADD PRIMARY KEY (`case_id`),
  ADD KEY `idx_amc_target` (`target_id`, `target_type`),
  ADD KEY `idx_amc_created` (`created_at`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_an_admin` (`admin_username`),
  ADD KEY `idx_an_read` (`is_read`);

--
-- Indexes for table `contract_milestone`
--
ALTER TABLE `contract_milestone`
  ADD PRIMARY KEY (`milestone_id`),
  ADD KEY `idx_cm_contract` (`contract_id`),
  ADD KEY `idx_cm_status` (`status`);

--
-- Indexes for table `contract_budget_adjustments`
--
ALTER TABLE `contract_budget_adjustments`
  ADD PRIMARY KEY (`adjustment_id`),
  ADD KEY `idx_cba_contract` (`contract_id`),
  ADD KEY `idx_cba_status` (`status`);

--
-- Indexes for table `escrow_wallet`
--
ALTER TABLE `escrow_wallet`
  ADD PRIMARY KEY (`wallet_id`),
  ADD KEY `idx_ew_user` (`user_id`),
  ADD KEY `idx_ew_company` (`company_id`);

--
-- Indexes for table `escrow_transaction`
--
ALTER TABLE `escrow_transaction`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `idx_et_wallet` (`wallet_id`),
  ADD KEY `idx_et_type` (`type`);

--
-- Indexes for table `SupportTicket`
--
ALTER TABLE `SupportTicket`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `idx_st_user` (`user_id`, `user_type`),
  ADD KEY `idx_st_status` (`status`);

--
-- Indexes for table `company_employees`
--
ALTER TABLE `company_employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `idx_ce_company` (`company_id`),
  ADD KEY `idx_ce_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activitylog`
--
ALTER TABLE `activitylog`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `chatmessage`
--
ALTER TABLE `chatmessage`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `companyjobpost`
--
ALTER TABLE `companyjobpost`
  MODIFY `posting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companyquotation`
--
ALTER TABLE `companyquotation`
  MODIFY `quotation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contract`
--
ALTER TABLE `contract`
  MODIFY `contract_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `moderator_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `moderator_management_log`
--
ALTER TABLE `moderator_management_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `moderatormsg`
--
ALTER TABLE `moderatormsg`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotion`
--
ALTER TABLE `promotion`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairer`
--
ALTER TABLE `repairer`
  MODIFY `repairer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `quote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `system_activity_logs`
--
ALTER TABLE `system_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ad_status_history`
--
ALTER TABLE `ad_status_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ad_reports`
--
ALTER TABLE `ad_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `moderator_activity`
--
ALTER TABLE `moderator_activity`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `AdminAlert`
--
ALTER TABLE `AdminAlert`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_moderation_status`
--
ALTER TABLE `account_moderation_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_moderation_cases`
--
ALTER TABLE `account_moderation_cases`
  MODIFY `case_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_milestone`
--
ALTER TABLE `contract_milestone`
  MODIFY `milestone_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_budget_adjustments`
--
ALTER TABLE `contract_budget_adjustments`
  MODIFY `adjustment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `escrow_wallet`
--
ALTER TABLE `escrow_wallet`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `escrow_transaction`
--
ALTER TABLE `escrow_transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SupportTicket`
--
ALTER TABLE `SupportTicket`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_employees`
--
ALTER TABLE `company_employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adschedule`
--
ALTER TABLE `adschedule`
  ADD CONSTRAINT `adschedule_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `companyquotation_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `companyquotation_ibfk_2` FOREIGN KEY (`request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `companyquotation_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract`
--
ALTER TABLE `contract`
  ADD CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE;

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
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`given_by`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

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
-- Constraints for table `contract` (new FKs)
--
ALTER TABLE `contract`
  ADD CONSTRAINT `contract_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contract_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contract_ibfk_4` FOREIGN KEY (`quotation_id`) REFERENCES `companyquotation` (`quotation_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contract_ibfk_5` FOREIGN KEY (`job_request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE SET NULL;

--
-- Constraints for table `advertisement` (new FKs)
--
ALTER TABLE `advertisement`
  ADD CONSTRAINT `advertisement_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `advertisement_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `moderator` (`moderator_id`) ON DELETE SET NULL;

--
-- Constraints for table `ad_status_history`
--
ALTER TABLE `ad_status_history`
  ADD CONSTRAINT `ad_status_history_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE;

--
-- Constraints for table `ad_reports`
--
ALTER TABLE `ad_reports`
  ADD CONSTRAINT `ad_reports_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `advertisement` (`ad_id`) ON DELETE CASCADE;

--
-- Constraints for table `moderator_activity`
--
ALTER TABLE `moderator_activity`
  ADD CONSTRAINT `moderator_activity_ibfk_1` FOREIGN KEY (`moderator_id`) REFERENCES `moderator` (`moderator_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_milestone`
--
ALTER TABLE `contract_milestone`
  ADD CONSTRAINT `contract_milestone_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_budget_adjustments`
--
ALTER TABLE `contract_budget_adjustments`
  ADD CONSTRAINT `contract_budget_adjustments_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE;

--
-- Constraints for table `escrow_wallet`
--
ALTER TABLE `escrow_wallet`
  ADD CONSTRAINT `escrow_wallet_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `escrow_wallet_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE SET NULL;

--
-- Constraints for table `escrow_transaction`
--
ALTER TABLE `escrow_transaction`
  ADD CONSTRAINT `escrow_transaction_ibfk_1` FOREIGN KEY (`wallet_id`) REFERENCES `escrow_wallet` (`wallet_id`) ON DELETE CASCADE;

--
-- Constraints for table `company_employees`
--
ALTER TABLE `company_employees`
  ADD CONSTRAINT `company_employees_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
