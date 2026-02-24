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