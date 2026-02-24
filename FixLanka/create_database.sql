-- Database Schema for Home Repair Service Platform
-- Version 1.3.0
-- =====================================================

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
  `status` enum('pending','approved','active','expired','rejected') DEFAULT 'pending',
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

-- Repairer Application Table
CREATE TABLE RepairerApplication (
    app_id INT PRIMARY KEY AUTO_INCREMENT,
    repairer_id INT NOT NULL,
    posting_id INT NOT NULL,
    date_applied TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    app_status ENUM('pending', 'reviewed', 'accepted', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE,
    FOREIGN KEY (posting_id) REFERENCES CompanyJobPost(posting_id) ON DELETE CASCADE,
    INDEX idx_repairer (repairer_id),
    INDEX idx_posting (posting_id)
);

-- Moderator Message Table
CREATE TABLE ModeratorMsg (
    msg_id INT PRIMARY KEY AUTO_INCREMENT,
    repairer_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    msg TEXT NOT NULL,
    date_sent TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    response TEXT,
    date_resolved TIMESTAMP NULL,
    status ENUM('open', 'pending', 'resolved', 'closed') DEFAULT 'open',
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE,
    INDEX idx_repairer (repairer_id),
    INDEX idx_status (status)
);

-- Chat Message Table
CREATE TABLE ChatMessage (
    session_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    repairer_id INT NOT NULL,
    firebase_chat_id VARCHAR(255), -- Reference to Firebase chat
    last_message TEXT,
    last_message_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_repairer (repairer_id)
);

-- =====================================================
-- Admin and Moderator Tables
-- =====================================================

-- Admin Table
CREATE TABLE Admin (
    username VARCHAR(100) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Moderator Table
CREATE TABLE Moderator (
    moderator_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    assigned_section VARCHAR(100), -- Section they are responsible for
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Static Content Table
CREATE TABLE StaticContent (
    content_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    content_type ENUM('terms', 'privacy', 'faq', 'about', 'help') NOT NULL,
    INDEX idx_type (content_type)
);

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

-- Milestone Payment Table
CREATE TABLE MilestonePayment (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    milestone_id INT ,
    amount DECIMAL(10,2) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    method ENUM('credit_card', 'debit_card', 'bank_transfer', 'check', 'cash') NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    FOREIGN KEY (milestone_id) REFERENCES Milestone(milestone_id) ON DELETE CASCADE,
    INDEX idx_milestone (milestone_id),
    INDEX idx_status (status)
);

-- Repairer Assignment Table (for company projects)
CREATE TABLE RepairerAssignment (
    assignment_id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    repairer_id INT NOT NULL,
    role VARCHAR(100),
    amount DECIMAL(10,2),
    assigned_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('assigned', 'active', 'completed', 'removed') DEFAULT 'assigned',
    FOREIGN KEY (project_id) REFERENCES Project(project_id) ON DELETE CASCADE,
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE,
    INDEX idx_project (project_id),
    INDEX idx_repairer (repairer_id)
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

-- =====================================================
-- Insert Default Categories
-- =====================================================
INSERT INTO Category (name) VALUES 
('Plumbing'),
('Electrical'),
('HVAC'),
('Cleaning'),
('Carpentry'),
('Painting'),
('Appliance Repair'),
('Roofing'),
('Landscaping'),
('Pest Control'),
('Home Security'),
('Interior Design'),
('Flooring'),
('Masonry'),
('Welding'),
('Glass & Mirror'),
('Tile Work'),
('Drywall'),
('Insulation'),
('Window Installation');