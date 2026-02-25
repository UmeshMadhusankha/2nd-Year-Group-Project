-- Database Schema for Home Repair Service Platform
-- Version 1.3.0
-- =====================================================

DROP DATABASE IF EXISTS fix_lanka;
CREATE DATABASE fix_lanka;
USE fix_lanka;

-- User Table
<<<<<<< HEAD
CREATE TABLE User (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    f_name VARCHAR(100) NOT NULL,
    l_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profilePicture VARCHAR(500),
    address TEXT,
    district VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
=======
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `f_name` varchar(100) NOT NULL,
  `l_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(500) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
>>>>>>> bd66a9b8974472e2cf1a55731ef74f8d11d6ca1f

-- Issue Report Table
CREATE TABLE IssueReport (
    issue_id INT PRIMARY KEY AUTO_INCREMENT,
    reportedBy_id INT NOT NULL,
    target_id INT NOT NULL, -- Can reference User, Repairer, or Company
    target_type ENUM('user', 'repairer', 'company') NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open', 'investigating', 'resolved', 'closed') DEFAULT 'open',
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reportedBy_id) REFERENCES User(user_id) ON DELETE CASCADE,
    INDEX idx_reporter (reportedBy_id),
    INDEX idx_status (status)
);

-- Category Table
CREATE TABLE Category (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- JobRequest table
CREATE TABLE JobRequest (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'accepted', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    district VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    service_provider_type VARCHAR(50) NOT NULL, -- Can store 'individual', 'company', or 'both'
    urgency ENUM('medium', 'urgent') DEFAULT 'medium',
    finish_date DATE NOT NULL,
    dateCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    photos VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Category(category_id) ON DELETE RESTRICT,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_created (dateCreated),
    INDEX idx_district (district)
);

-- Repairer Table
CREATE TABLE Repairer (
    repairer_id INT PRIMARY KEY AUTO_INCREMENT,
    f_name VARCHAR(100) NOT NULL,
    l_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phoneNumber VARCHAR(20),
    about TEXT,
    profilePicture VARCHAR(500),
    ratings DECIMAL(3,2) DEFAULT 0.00,
    completedJobsCount INT DEFAULT 0,
    districts TEXT, -- Store multiple service districts (comma-separated)
    availability ENUM('available', 'busy', 'unavailable') DEFAULT 'available',
    dateJoined TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    category_id INT,
    FOREIGN KEY (category_id) REFERENCES Category(category_id) ON DELETE SET NULL,
    INDEX idx_email (email),
    INDEX idx_ratings (ratings)
);

<<<<<<< HEAD
-- Job Table
CREATE TABLE Job (
    job_id INT PRIMARY KEY AUTO_INCREMENT,
    job_request_id INT NOT NULL,
    fixer_id INT NOT NULL,
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    completionDate TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_request_id) REFERENCES JobRequest(request_id) ON DELETE CASCADE,
    FOREIGN KEY (fixer_id) REFERENCES Repairer(repairer_id) ON DELETE RESTRICT,
    INDEX idx_status (status),
    INDEX idx_fixer (fixer_id)
);
=======
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
  `rating` decimal(3,2) DEFAULT 0.00,
  `date_of_joined` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`company_id`),
  UNIQUE KEY `registration_no` (`registration_no`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
>>>>>>> bd66a9b8974472e2cf1a55731ef74f8d11d6ca1f

-- Payment Table
CREATE TABLE Payment (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    job_request_id INT NOT NULL,
    paymentType ENUM('credit_card', 'debit_card', 'cash', 'bank_transfer') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    paymentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    FOREIGN KEY (job_request_id) REFERENCES JobRequest(request_id) ON DELETE CASCADE,
    INDEX idx_job_request (job_request_id),
    INDEX idx_status (status)
);

-- Review Table
CREATE TABLE Review (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    service_provider_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comments TEXT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES Job(job_id) ON DELETE CASCADE,
    FOREIGN KEY (service_provider_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE,
    INDEX idx_job (job_id),
    INDEX idx_provider (service_provider_id)
);

-- =====================================================
-- Service Provider Tables
-- =====================================================

-- Repairer Quote Table
CREATE TABLE RepairerQuote (
    quote_id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    repairer_id INT NOT NULL,
    quoteAmount DECIMAL(10,2) NOT NULL,
    message TEXT,
    status ENUM('pending', 'accepted', 'rejected', 'expired') DEFAULT 'pending',
    dateSubmitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES JobRequest(request_id) ON DELETE CASCADE,
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE,
    INDEX idx_request (request_id),
    INDEX idx_repairer (repairer_id),
    INDEX idx_status (status)
);

-- Promotion Table (for repairers)
CREATE TABLE Promotion (
    promotion_id INT PRIMARY KEY AUTO_INCREMENT,
    repairer_id INT NOT NULL,
    plan_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE,
    INDEX idx_repairer (repairer_id),
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date)
);

-- Quotation Table
CREATE TABLE CompanyQuotation (
    quotation_id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(250) NOT NULL,
    description VARCHAR(500),
    labor_cost DECIMAL(10,2) NOT NULL,
    material_cost DECIMAL(10,2) NOT NULL,
    transport_cost DECIMAL(10,2) DEFAULT 0.00,
    other_charges DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    start_date DATE NOT NULL,
    completion_date DATE NOT NULL,
    estimated_duration INT NOT NULL,
    payment_terms VARCHAR(100),
    warranty_period VARCHAR(50),
    additional_terms TEXT,
    status ENUM('pending', 'accepted', 'rejected', 'successful') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES JobRequest(request_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    INDEX idx_request (request_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
);

<<<<<<< HEAD
-- Company Table
CREATE TABLE Company (
    company_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    business_type VARCHAR(255), -- Store multiple business types (comma-separated)
    registration_no VARCHAR(100) UNIQUE NOT NULL,
    tax_id VARCHAR(100),
    address TEXT NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    website VARCHAR(255),
    contact_no VARCHAR(20) NOT NULL,
    districts TEXT, -- Store multiple service districts (comma-separated)
    password VARCHAR(255) NOT NULL,
    description TEXT,
    rating DECIMAL(3,2) DEFAULT 0.00,
    date_of_joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);
=======
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
  `ratings` decimal(3,2) DEFAULT 0.00,
  `completed_jobs_count` int(11) DEFAULT 0,
  `districts` text DEFAULT NULL,
  `availability` enum('available','busy','unavailable') DEFAULT 'available',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`repairer_id`),
  UNIQUE KEY `email` (`email`),
  KEY `category_id` (`category_id`),
  KEY `idx_email` (`email`),
  KEY `idx_ratings` (`ratings`),
  CONSTRAINT `repairer_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
>>>>>>> bd66a9b8974472e2cf1a55731ef74f8d11d6ca1f

-- Company Job Posting Table
CREATE TABLE CompanyJobPost (
    posting_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100),
    location TEXT NOT NULL,
    posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('open', 'closed', 'filled') DEFAULT 'open',
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    INDEX idx_company (company_id),
    INDEX idx_status (status)
);



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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `photos` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  KEY `category_id` (`category_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`),
  KEY `idx_district` (`district`),
  CONSTRAINT `jobrequest_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `jobrequest_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Company Quotation
CREATE TABLE `companyquotation` (
  `quotation_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `payment_terms` varchar(100) DEFAULT NULL,
  `warranty_period` varchar(50) DEFAULT NULL,
  `additional_terms` text DEFAULT NULL,
  
  -- Phase 1: Budget Flexibility & Payment Methods
  `budget_type` enum('fixed','flexible') DEFAULT 'fixed' COMMENT 'Fixed or Flexible (±10%)',
  `budget_min` decimal(10,2) DEFAULT NULL COMMENT 'Minimum budget for flexible pricing',
  `budget_max` decimal(10,2) DEFAULT NULL COMMENT 'Maximum budget for flexible pricing',
  `payment_method` enum('full_upfront','milestone_based','50_50','30_70','completion') DEFAULT 'full_upfront' COMMENT 'Payment method selected',
  `pricing_type` enum('fixed_price','time_and_material') DEFAULT 'fixed_price' COMMENT 'Pricing structure type',
  `hourly_rate` decimal(10,2) DEFAULT NULL COMMENT 'Hourly rate for Time & Material',
  `spending_cap_multiplier` decimal(3,2) DEFAULT 1.10 COMMENT 'Spending cap multiplier for T&M (default 1.10 = 110%)',
  
  `status` enum('pending','accepted','rejected','successful') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`quotation_id`),
  KEY `idx_request` (`request_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_budget_type` (`budget_type`),
  KEY `idx_payment_method` (`payment_method`),
  KEY `idx_pricing_type` (`pricing_type`),
  CONSTRAINT `companyquotation_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE CASCADE,
  CONSTRAINT `companyquotation_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Admin Table
CREATE TABLE Admin (
    username VARCHAR(100) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contract (Enhanced 8-Section Legal Form)
CREATE TABLE `contract` (
  `contract_id` int(11) NOT NULL AUTO_INCREMENT,
  `contract_number` varchar(50) DEFAULT NULL,
  `quotation_id` int(11) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `job_request_id` int(11) DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  
  -- Section 2: Project Overview
  `project_title` varchar(255) DEFAULT NULL,
  `project_reference` varchar(100) DEFAULT NULL,
  `project_location` text DEFAULT NULL,
  `project_description` text DEFAULT NULL,
  
  -- Section 3: Scope of Work
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
  
  -- Section 5: Pricing & Payments
  `total_budget` decimal(12,2) NOT NULL,
  `budget_type` enum('fixed','time_based','flexible') DEFAULT 'fixed',
  `budget_min` decimal(12,2) DEFAULT NULL,
  `budget_max` decimal(12,2) DEFAULT NULL,
  `tax_inclusive` tinyint(1) DEFAULT 1,
  `payment_method` enum('full_upfront','milestone_based','50_50','30_70','completion') DEFAULT 'milestone_based',
  `advance_payment_pct` decimal(5,2) DEFAULT 0,
  `pricing_type` enum('fixed_price','time_and_material') DEFAULT 'fixed_price',
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `spending_cap` decimal(12,2) DEFAULT NULL,
  
  -- Section 5: Delay Handling
  `late_payment_penalty` text DEFAULT NULL,
  `pause_work_clause` tinyint(1) DEFAULT 1,
  `time_extension_clause` tinyint(1) DEFAULT 1,
  
  -- Section 6: Variations
  `variation_clause` tinyint(1) DEFAULT 1,
  
  -- Section 7: Communication & Disputes
  `communication_channel` varchar(100) DEFAULT 'system',
  `dispute_resolution` text DEFAULT NULL,
  
  -- Financial Tracking
  `actual_hours` decimal(10,2) DEFAULT 0.00,
  `actual_cost` decimal(12,2) DEFAULT 0.00,
  `amount_paid` decimal(12,2) DEFAULT 0.00,
  `amount_pending` decimal(12,2) DEFAULT 0.00,
  `payment_status` enum('pending','partial','completed','overdue') DEFAULT 'pending',
  
  -- Timeline
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `terms_accepted` tinyint(1) DEFAULT 0,
  `terms_accepted_at` datetime DEFAULT NULL,
  `contract_date` date NOT NULL,
  
  -- Signatures
  `user_signature` varchar(255) NOT NULL,
  `company_signature` varchar(255) NOT NULL,
  `customer_signature` text DEFAULT NULL,
  `signed_at` datetime DEFAULT NULL,
  
  `terms_conditions` text DEFAULT NULL,
  `status` enum('draft','pending_signature','active','in_progress','milestone_pending','completed','terminated','disputed') DEFAULT 'draft',
  
  -- Customer Response Tracking
  `sent_to_customer` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `customer_response` enum('pending','accepted','rejected','negotiating') DEFAULT 'pending',
  `customer_response_at` timestamp NULL DEFAULT NULL,
  `locked` tinyint(1) DEFAULT 0,
  
  PRIMARY KEY (`contract_id`),
  UNIQUE KEY `idx_contract_number` (`contract_number`),
  KEY `idx_project` (`project_id`),
  KEY `idx_company` (`company_id`),
  KEY `idx_customer` (`customer_id`),
  KEY `idx_quotation` (`quotation_id`),
  KEY `idx_job_request` (`job_request_id`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_status` (`payment_status`),
  CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE,
  CONSTRAINT `contract_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE RESTRICT,
  CONSTRAINT `contract_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `contract_ibfk_4` FOREIGN KEY (`quotation_id`) REFERENCES `companyquotation` (`quotation_id`) ON DELETE SET NULL,
  CONSTRAINT `contract_ibfk_5` FOREIGN KEY (`job_request_id`) REFERENCES `jobrequest` (`request_id`) ON DELETE SET NULL
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

-- Report Table (Admin/Moderator Reports)
CREATE TABLE Report (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    description TEXT NOT NULL,
    status ENUM('pending', 'investigating', 'resolved') DEFAULT 'pending',
    resolve_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT, -- Reference to Admin or Moderator
    INDEX idx_status (status)
);

-- Activity Log Table
CREATE TABLE ActivityLog (
    activity_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action_type VARCHAR(100) NOT NULL,
    description TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    INDEX idx_user (user_id),
    INDEX idx_timestamp (timestamp)
);

-- Alert Table
CREATE TABLE Notification (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    send_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    recipient_type ENUM('user', 'repairer', 'company', 'all') NOT NULL,
    status ENUM('sent', 'pending', 'failed') DEFAULT 'pending',
    INDEX idx_status (status)
);

-- Advertisement Table
CREATE TABLE Advertisement (
    ad_id INT PRIMARY KEY AUTO_INCREMENT,
    provider_id INT NOT NULL, -- Can be Repairer or Company
    provider_type ENUM('repairer', 'company') NOT NULL,
    title VARCHAR(255) NOT NULL,
    type ENUM('banner', 'featured', 'sponsored') NOT NULL,
    budget DECIMAL(10,2) NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'active', 'expired', 'rejected') DEFAULT 'pending',
    INDEX idx_provider (provider_id, provider_type),
    INDEX idx_status (status)
);

-- Ad Schedule Table
CREATE TABLE AdSchedule (
    schedule_id INT PRIMARY KEY AUTO_INCREMENT,
    ad_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    FOREIGN KEY (ad_id) REFERENCES Advertisement(ad_id) ON DELETE CASCADE,
    INDEX idx_ad (ad_id),
    INDEX idx_dates (start_date, end_date)
);

-- Financial Report Table
CREATE TABLE FinancialReport (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    month DATE NOT NULL,
    revenue DECIMAL(12,2) DEFAULT 0.00,
    pending_withdrawals DECIMAL(12,2) DEFAULT 0.00,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_month (month)
);

-- =====================================================
-- Company and Project Tables
-- =====================================================

-- Project Table
CREATE TABLE Project (
    project_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    customer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    project_type VARCHAR(100),
    location TEXT NOT NULL,
    budget DECIMAL(12,2),
    final_cost DECIMAL(12,2),
    start_date DATE,
    end_date DATE,
    attachment VARCHAR(500),
    status ENUM('planned', 'in_progress', 'completed', 'cancelled', 'on_hold') DEFAULT 'planned',
    progress INT DEFAULT 0 CHECK (progress >= 0 AND progress <= 100),
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES User(user_id) ON DELETE RESTRICT,
    INDEX idx_company (company_id),
    INDEX idx_customer (customer_id),
    INDEX idx_status (status)
);

-- Contract Table
CREATE TABLE Contract (
    contract_id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    milestone_plan BOOLEAN NOT NULL DEFAULT TRUE,
    total_budget DECIMAL(12,2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    contract_date DATE NOT NULL,
    user_signature VARCHAR(255) NOT NULL,
    company_signature VARCHAR(255) NOT NULL,-- Store signature data/paths
    terms_conditions TEXT,
    status ENUM('draft', 'active', 'completed', 'terminated') DEFAULT 'draft',
    FOREIGN KEY (project_id) REFERENCES Project(project_id) ON DELETE CASCADE,
    INDEX idx_project (project_id),
    INDEX idx_status (status)
);

-- Milestone Table
CREATE TABLE Milestone (
    milestone_id INT PRIMARY KEY AUTO_INCREMENT,
    contract_id INT NOT NULL,
    description TEXT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    due_date DATE NOT NULL,
    agreements TEXT,
    status ENUM('pending', 'in_progress', 'completed', 'overdue') DEFAULT 'pending',
    FOREIGN KEY (contract_id) REFERENCES Contract(contract_id) ON DELETE CASCADE,
    INDEX idx_contract (contract_id),
    INDEX idx_status (status)
);


-- Feedback Table
CREATE TABLE Feedback (
    feedback_id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    given_by INT NOT NULL, -- User ID
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comments TEXT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Project(project_id) ON DELETE CASCADE,
    FOREIGN KEY (given_by) REFERENCES User(user_id) ON DELETE CASCADE,
    INDEX idx_project (project_id)
);

-- Staff Summary Table
CREATE TABLE StaffSummary (
    staff_summary_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    skill_category VARCHAR(100),
    count INT DEFAULT 0,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    INDEX idx_company (company_id)
);

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
  `body` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `content_type` enum('terms','privacy','faq','about','help') NOT NULL,
  PRIMARY KEY (`content_id`),
  KEY `idx_type` (`content_type`)
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
