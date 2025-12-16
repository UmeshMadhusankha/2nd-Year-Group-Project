-- =====================================================
-- Database Schema for Home Repair Service Platform
-- Version 1.3.0
-- =====================================================

DROP DATABASE IF EXISTS fix_lanka;
CREATE DATABASE fix_lanka;
USE fix_lanka;

-- User Table
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

CREATE TABLE IssueReport (
    issue_id INT PRIMARY KEY AUTO_INCREMENT,
    reportedBy_id INT NOT NULL,
    target_id INT NOT NULL,
    target_type ENUM('user', 'repairer', 'company') NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',  ✅ ADD THIS
    status ENUM('pending', 'investigating', 'resolved', 'escalated', 'closed') DEFAULT 'pending',  ✅ FIX THIS
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  ✅ ADD THIS
    FOREIGN KEY (reportedBy_id) REFERENCES User(user_id) ON DELETE CASCADE,
    INDEX idx_reporter (reportedBy_id),
    INDEX idx_status (status),
    INDEX idx_priority (priority)  ✅ ADD THIS
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
-- =====================================================
-- NEW TABLES ADDED (Updated Version)
-- =====================================================

-- Ad Schedules Table (NEW - Added for conflict detection)
CREATE TABLE ad_schedules (
    schedule_id INT PRIMARY KEY AUTO_INCREMENT,
    ad_id INT NOT NULL,
    placement VARCHAR(50) NOT NULL DEFAULT 'banner',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME NOT NULL DEFAULT '00:00:00',
    end_time TIME NOT NULL DEFAULT '23:59:59',
    status ENUM('scheduled', 'active', 'expired', 'cancelled') DEFAULT 'scheduled',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_ad_schedule (ad_id),
    INDEX idx_placement (placement),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_status (status),
    FOREIGN KEY (ad_id) REFERENCES Advertisement(ad_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Moderator Activity Table (NEW - Track moderator actions)
CREATE TABLE moderator_activity (
    activity_id INT PRIMARY KEY AUTO_INCREMENT,
    moderator_id INT NOT NULL DEFAULT 1,
    activity_type ENUM('ad_approved', 'ad_rejected', 'ad_activated', 'user_banned', 'content_updated') NOT NULL,
    target_id INT NOT NULL,
    target_title VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_moderator (moderator_id),
    INDEX idx_type (activity_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================
-- Financial System Tables (Auto-Calculated)
-- =====================================================

-- Financial Report Table (Parent - Monthly Summary)
CREATE TABLE FinancialReport (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    month DATE NOT NULL UNIQUE,
    active_subscriptions INT DEFAULT 0,
    generated_by INT,
    notes TEXT,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_month (month),
    FOREIGN KEY (generated_by) REFERENCES Moderator(moderator_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Financial Report Transaction Table (Child - All Transactions)
CREATE TABLE FinancialReportTransaction (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    report_id INT NOT NULL,
    transaction_type ENUM('Payment', 'Commission', 'Withdrawal', 'Subscription', 'Advertisement', 'Refund') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description VARCHAR(500),
    reference_id INT,
    reference_type VARCHAR(50),
    status ENUM('Completed', 'Pending', 'Failed', 'Cancelled') DEFAULT 'Completed',
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_report (report_id),
    INDEX idx_type (transaction_type),
    INDEX idx_status (status),
    INDEX idx_date (transaction_date),
    FOREIGN KEY (report_id) REFERENCES FinancialReport(report_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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


-- Insert Sample Users
INSERT INTO User (f_name, l_name, email, password, district) VALUES
('John', 'Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Colombo'),
('Jane', 'Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kandy'),
('Bob', 'Wilson', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Galle'),
('Alice', 'Brown', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Colombo');

-- Insert Sample Companies
INSERT INTO Company (name, registration_no, email, contact_no, address, password, business_type, districts) VALUES
('BuildPro Solutions', 'REG001', 'info@buildpro.com', '0771234567', '123 Main St, Colombo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Construction', 'Colombo,Gampaha'),
('ABC Construction Ltd', 'REG002', 'contact@abc.com', '0779876543', '456 Park Ave, Kandy', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Construction', 'Kandy,Matale');

-- Insert Sample Repairers
INSERT INTO Repairer (f_name, l_name, email, password, phoneNumber, districts, category_id, ratings, completedJobsCount) VALUES
('Mike', 'Johnson', 'mike@fixlanka.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0771112233', 'Colombo,Gampaha', 1, 4.5, 45),
('Sarah', 'Davis', 'sarah@fixlanka.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0772223344', 'Kandy,Matale', 2, 4.8, 67);

-- Insert Sample Advertisements
INSERT INTO Advertisement (provider_id, provider_type, title, type, budget, status) VALUES
(1, 'company', 'New Year Construction Packages', 'sponsored', 28000.00, 'approved'),
(1, 'company', 'Premium Home Construction Services', 'banner', 50000.00, 'approved'),
(2, 'company', 'Special Discount on Renovations - 20% OFF', 'featured', 35000.00, 'approved');

-- Insert Admin
INSERT INTO Admin (username, password, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@fixlanka.com');

-- Insert Moderators
INSERT INTO Moderator (username, password, email, assigned_section) VALUES
('moderator1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mod1@fixlanka.com', 'Advertisements');

-- Insert Sample Job Requests (REQUIRED before payments)
INSERT INTO JobRequest (
    user_id, category_id, title, description, district, address,
    service_provider_type, finish_date
) VALUES
(1, 1, 'Fix Bathroom Leak', 'Leaking pipe issue', 'Colombo', 'No 12, Main Street', 'individual', '2025-01-10'),
(2, 2, 'Electrical Wiring Repair', 'Short circuit problem', 'Kandy', 'No 45, Lake Road', 'individual', '2025-01-12'),
(3, 3, 'Roof Repair', 'Roof tiles broken', 'Galle', 'No 78, Beach Road', 'company', '2025-01-15');

-- Insert Sample Payments
INSERT INTO Payment (job_request_id, paymentType, amount, status) VALUES
(1, 'credit_card', 15000.00, 'completed'),
(2, 'bank_transfer', 25000.00, 'completed'),
(3, 'cash', 8000.00, 'pending');

-- Insert Sample Moderator Activities with RECENT timestamps
INSERT INTO moderator_activity (moderator_id, activity_type, target_id, target_title, description, created_at) VALUES
(1, 'ad_approved', 1, 'Door &amp; Window Installation - Modern Designs', 'Advertisement approved for sponsored placement', DATE_SUB(NOW(), INTERVAL 3 MINUTE)),
(1, 'ad_approved', 2, 'Home Appliance Repair - All Brands', 'Advertisement approved for featured placement', DATE_SUB(NOW(), INTERVAL 3 MINUTE)),
(1, 'ad_approved', 3, 'Home Appliance Repair - All Brands', 'Advertisement approved for featured placement', DATE_SUB(NOW(), INTERVAL 11 HOUR)),
(1, 'ad_approved', 1, 'Garden Landscaping &amp; Maintenance Services', 'Advertisement approved for banner placement', DATE_SUB(NOW(), INTERVAL 11 HOUR)),
(1, 'payment_verified', 1, 'Payment of LKR 15,000', 'Payment verified and processed', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'user_registered', 1, 'New user account created', 'User registration approved', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 'report_resolved', 1, 'User complaint resolved', 'Issue report closed successfully', DATE_SUB(NOW(), INTERVAL 1 WEEK));

-- =====================================================
-- Insert Sample Financial Reports (Auto-Calculated System)
-- =====================================================

-- December 2025 Report
INSERT INTO FinancialReport (month, active_subscriptions, generated_by, notes) VALUES
('2025-12-01', 145, 1, 'December 2025 Monthly Report');

SET @dec_report_id = LAST_INSERT_ID();

-- December Transactions
INSERT INTO FinancialReportTransaction (report_id, transaction_type, amount, description, reference_id, reference_type, status, transaction_date) VALUES
-- Payments
(@dec_report_id, 'Payment', 15000.00, 'Job completion payment - Plumbing service', 1, 'JobRequest', 'Completed', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(@dec_report_id, 'Payment', 25000.00, 'Job completion payment - Electrical repair', 2, 'JobRequest', 'Completed', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(@dec_report_id, 'Payment', 18500.00, 'Job completion payment - HVAC installation', 3, 'JobRequest', 'Completed', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(@dec_report_id, 'Payment', 32000.00, 'Project milestone payment', 1, 'Project', 'Completed', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(@dec_report_id, 'Payment', 22500.00, 'Emergency repair service', 4, 'JobRequest', 'Completed', DATE_SUB(NOW(), INTERVAL 12 DAY)),

-- Commissions (15% of payments)
(@dec_report_id, 'Commission', 2250.00, 'Platform commission - Job #1', 1, 'Payment', 'Completed', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(@dec_report_id, 'Commission', 3750.00, 'Platform commission - Job #2', 2, 'Payment', 'Completed', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(@dec_report_id, 'Commission', 2775.00, 'Platform commission - Job #3', 3, 'Payment', 'Completed', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(@dec_report_id, 'Commission', 4800.00, 'Platform commission - Project milestone', 1, 'Project', 'Completed', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(@dec_report_id, 'Commission', 3375.00, 'Platform commission - Emergency service', 4, 'Payment', 'Completed', DATE_SUB(NOW(), INTERVAL 12 DAY)),

-- Subscriptions
(@dec_report_id, 'Subscription', 5000.00, 'Premium repairer subscription - Monthly', 1, 'Repairer', 'Completed', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(@dec_report_id, 'Subscription', 5000.00, 'Premium repairer subscription - Monthly', 2, 'Repairer', 'Completed', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(@dec_report_id, 'Subscription', 8000.00, 'Business company subscription - Monthly', 1, 'Company', 'Completed', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(@dec_report_id, 'Subscription', 8000.00, 'Business company subscription - Monthly', 2, 'Company', 'Completed', DATE_SUB(NOW(), INTERVAL 6 DAY)),

-- Advertisements
(@dec_report_id, 'Advertisement', 28000.00, 'Sponsored ad placement - 30 days', 1, 'Advertisement', 'Completed', DATE_SUB(NOW(), INTERVAL 7 DAY)),
(@dec_report_id, 'Advertisement', 50000.00, 'Banner ad placement - Premium location', 2, 'Advertisement', 'Completed', DATE_SUB(NOW(), INTERVAL 9 DAY)),
(@dec_report_id, 'Advertisement', 35000.00, 'Featured listing - Homepage', 3, 'Advertisement', 'Completed', DATE_SUB(NOW(), INTERVAL 11 DAY)),

-- Withdrawals (Pending)
(@dec_report_id, 'Withdrawal', 45000.00, 'Repairer earnings withdrawal request', 1, 'Repairer', 'Pending', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(@dec_report_id, 'Withdrawal', 28000.00, 'Company earnings withdrawal request', 1, 'Company', 'Pending', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(@dec_report_id, 'Withdrawal', 13000.00, 'Repairer earnings withdrawal request', 2, 'Repairer', 'Pending', DATE_SUB(NOW(), INTERVAL 3 DAY));

-- November 2025 Report (for growth comparison)
INSERT INTO FinancialReport (month, active_subscriptions, generated_by, notes) VALUES
('2025-11-01', 132, 1, 'November 2025 Monthly Report');

SET @nov_report_id = LAST_INSERT_ID();

-- November Transactions (slightly less revenue for growth calculation)
INSERT INTO FinancialReportTransaction (report_id, transaction_type, amount, description, reference_id, reference_type, status, transaction_date) VALUES
-- Payments
(@nov_report_id, 'Payment', 12000.00, 'Job completion payment', 5, 'JobRequest', 'Completed', '2025-11-25 10:30:00'),
(@nov_report_id, 'Payment', 18000.00, 'Job completion payment', 6, 'JobRequest', 'Completed', '2025-11-22 14:15:00'),
(@nov_report_id, 'Payment', 15500.00, 'Project payment', 2, 'Project', 'Completed', '2025-11-20 09:45:00'),
(@nov_report_id, 'Payment', 22000.00, 'Job completion payment', 7, 'JobRequest', 'Completed', '2025-11-18 16:20:00'),

-- Commissions
(@nov_report_id, 'Commission', 1800.00, 'Platform commission', 5, 'Payment', 'Completed', '2025-11-25 10:30:00'),
(@nov_report_id, 'Commission', 2700.00, 'Platform commission', 6, 'Payment', 'Completed', '2025-11-22 14:15:00'),
(@nov_report_id, 'Commission', 2325.00, 'Platform commission', 2, 'Project', 'Completed', '2025-11-20 09:45:00'),
(@nov_report_id, 'Commission', 3300.00, 'Platform commission', 7, 'Payment', 'Completed', '2025-11-18 16:20:00'),

-- Subscriptions
(@nov_report_id, 'Subscription', 5000.00, 'Premium subscription', 1, 'Repairer', 'Completed', '2025-11-05 08:00:00'),
(@nov_report_id, 'Subscription', 5000.00, 'Premium subscription', 2, 'Repairer', 'Completed', '2025-11-07 08:00:00'),
(@nov_report_id, 'Subscription', 8000.00, 'Business subscription', 1, 'Company', 'Completed', '2025-11-10 08:00:00'),

-- Advertisements
(@nov_report_id, 'Advertisement', 25000.00, 'Sponsored ad placement', 4, 'Advertisement', 'Completed', '2025-11-15 11:00:00'),
(@nov_report_id, 'Advertisement', 40000.00, 'Banner ad placement', 5, 'Advertisement', 'Completed', '2025-11-12 13:30:00');

-- =====================================================
-- Insert Sample Issue Reports
-- =====================================================
INSERT INTO IssueReport (reportedBy_id, target_id, target_type, description, priority, status, date) VALUES
(1, 1, 'user', 'This advertisement contains misleading information about certification and qualifications', 'high', 'pending', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 2, 'company', 'Advertisement displays different prices than what is actually charged during service', 'high', 'investigating', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(3, 1, 'repairer', 'Advertisement contains inappropriate images and unprofessional language', 'high', 'escalated', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(4, 2, 'user', 'Same advertisement posted multiple times with different prices', 'medium', 'resolved', DATE_SUB(NOW(), INTERVAL 12 DAY)),
(1, 3, 'company', 'Advertisement contains spam links and irrelevant promotional content', 'medium', 'pending', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(2, 1, 'user', 'Service provider not responding to messages after payment was made', 'high', 'investigating', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 2, 'repairer', 'Repairer demanding extra payment not mentioned in original quote', 'high', 'pending', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(4, 3, 'company', 'Company failed to complete project within agreed timeline', 'medium', 'resolved', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(1, 1, 'user', 'Inappropriate behavior and harassment from service provider', 'high', 'escalated', DATE_SUB(NOW(), INTERVAL 6 DAY)),
(2, 2, 'repairer', 'Poor quality work and refusal to fix issues under warranty', 'medium', 'investigating', DATE_SUB(NOW(), INTERVAL 7 DAY));