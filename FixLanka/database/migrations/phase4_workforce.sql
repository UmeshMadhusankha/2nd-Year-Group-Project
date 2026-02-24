-- Phase 4 Workforce System Tables

-- 1. Job Postings Table
CREATE TABLE IF NOT EXISTS `job_postings` (
  `posting_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','freelance') NOT NULL,
  `description` text NOT NULL,
  `requirements` text,
  `min_experience` int(11) NOT NULL DEFAULT 0,
  `min_budget` decimal(10,2) NOT NULL,
  `max_budget` decimal(10,2) NOT NULL,
  `location` varchar(255) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
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
  FOREIGN KEY (`job_posting_id`) REFERENCES `job_postings` (`posting_id`) ON DELETE CASCADE,
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
