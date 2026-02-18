-- =====================================================
-- Database Schema for Home Repair Service Platform
-- Version 1.3.2 - FIXED FOREIGN KEY CONSTRAINTS
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
    phone VARCHAR(15),
    address TEXT,
    district VARCHAR(100),
    profile_picture VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Fixed IssueReport table with new columns for status history and internal notes
CREATE TABLE IssueReport (
    issue_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    reported_by INT NOT NULL,
    reporter_type ENUM('user', 'repairer', 'company') NOT NULL,
    target_id INT,
    target_type ENUM('user', 'repairer', 'company'),
    status ENUM('pending', 'investigating', 'resolved', 'escalated') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    assigned_to INT,
    resolution TEXT,
    admin_internal_notes TEXT NULL COMMENT 'Internal admin notes - not visible to users',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    FOREIGN KEY (reported_by) REFERENCES User(user_id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_reporter (reported_by, reporter_type),
    INDEX idx_target (target_id, target_type),
    INDEX idx_assigned (assigned_to),
    INDEX idx_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Issue Status History Table (NEW - Track all status changes)
CREATE TABLE IF NOT EXISTS IssueStatusHistory (
    history_id INT PRIMARY KEY AUTO_INCREMENT,
    issue_id INT NOT NULL,
    old_status ENUM('pending', 'investigating', 'resolved', 'dismissed', 'escalated', 'closed') NOT NULL,
    new_status ENUM('pending', 'investigating', 'resolved', 'dismissed', 'escalated', 'closed') NOT NULL,
    changed_by VARCHAR(100) NOT NULL COMMENT 'Admin username who made the change',
    change_reason TEXT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (issue_id) REFERENCES IssueReport(issue_id) ON DELETE CASCADE,
    INDEX idx_issue (issue_id),
    INDEX idx_date (changed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Issue Notification Table (NEW - Notify users of status changes)
CREATE TABLE IF NOT EXISTS IssueNotification (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    issue_id INT NOT NULL,
    recipient_type ENUM('reporter', 'target') NOT NULL,
    recipient_id INT NOT NULL,
    message TEXT NOT NULL,
    status_change VARCHAR(100) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (issue_id) REFERENCES IssueReport(issue_id) ON DELETE CASCADE,
    INDEX idx_issue (issue_id),
    INDEX idx_recipient (recipient_type, recipient_id),
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Category Table
CREATE TABLE Category (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- JobRequest table
CREATE TABLE JobRequest (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    description TEXT NOT NULL,
    location TEXT NOT NULL,
    district VARCHAR(100),
    preferred_date DATE,
    urgency ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('pending', 'accepted', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    budget_range VARCHAR(50),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Category(category_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_date (preferred_date),
    INDEX idx_urgency (urgency),
    INDEX idx_district (district)
);

-- =====================================================
-- CRITICAL: Create Moderator Table BEFORE Advertisement
-- (Advertisement references Moderator)
-- =====================================================
CREATE TABLE Moderator (
    moderator_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    assigned_section VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_username (username)
);
-- Company Table
CREATE TABLE Company (
    company_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    district VARCHAR(100),
    registration_number VARCHAR(100) UNIQUE,
    description TEXT,
    logo_url VARCHAR(255),
    website VARCHAR(255),
    rating DECIMAL(3, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_district (district),
    INDEX idx_email (email)
);

-- Repairer Table
CREATE TABLE Repairer (
    repairer_id INT PRIMARY KEY AUTO_INCREMENT,
    f_name VARCHAR(100) NOT NULL,
    l_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    district VARCHAR(100),
    category_id INT,
    experience_years INT DEFAULT 0,
    certifications TEXT,
    profile_picture VARCHAR(255),
    rating DECIMAL(3, 2) DEFAULT 0.00,
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES Category(category_id) ON DELETE SET NULL
);

-- =====================================================
-- Advertisement Table (FIXED FOREIGN KEY)
-- =====================================================
CREATE TABLE Advertisement (
    ad_id INT PRIMARY KEY AUTO_INCREMENT,
    provider_id INT NOT NULL,
    provider_type ENUM('company', 'repairer') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    type ENUM('banner', 'sponsored', 'featured') NOT NULL,
    budget DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    target_audience TEXT,
    start_date DATE,
    end_date DATE,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',  -- ✅ UPDATED: 3-status system only
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_by INT DEFAULT NULL,
    reviewed_at TIMESTAMP NULL,
    clicks INT DEFAULT 0,
    impressions INT DEFAULT 0,
    INDEX idx_provider (provider_id, provider_type),
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_type (type),
    -- ✅ FIXED: Foreign key with proper constraint
    CONSTRAINT fk_advertisement_moderator 
        FOREIGN KEY (reviewed_by) 
        REFERENCES Moderator(moderator_id) 
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Job Table
CREATE TABLE Job (
    job_id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    fixer_id INT NOT NULL,
    fixer_type ENUM('repairer', 'company') NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    start_date DATE,
    completion_date DATE,
    actual_cost DECIMAL(10, 2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES JobRequest(request_id) ON DELETE CASCADE,
    INDEX idx_request (request_id),
    INDEX idx_fixer (fixer_id)
);

-- Payment Table
CREATE TABLE Payment (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    job_request_id INT NOT NULL,
    paymentType ENUM('credit_card', 'debit_card', 'bank_transfer', 'cash', 'online') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_method_details TEXT,
    FOREIGN KEY (job_request_id) REFERENCES JobRequest(request_id) ON DELETE CASCADE,
    INDEX idx_request (job_request_id),
    INDEX idx_status (status)
);

-- Review Table
CREATE TABLE Review (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    service_provider_id INT NOT NULL,
    service_provider_type ENUM('repairer', 'company') NOT NULL,
    rating INT CHECK(rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES Job(job_id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES User(user_id) ON DELETE CASCADE,
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
    quoted_price DECIMAL(10, 2) NOT NULL,
    estimated_duration VARCHAR(100),
    notes TEXT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES JobRequest(request_id) ON DELETE CASCADE,
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE,
    INDEX idx_request (request_id),
    INDEX idx_status (status)
);

-- Promotion Table (for repairers)
CREATE TABLE Promotion (
    promotion_id INT PRIMARY KEY AUTO_INCREMENT,
    repairer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    discount_percentage DECIMAL(5, 2),
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE,
    INDEX idx_dates (start_date, end_date)
);

-- Quotation Table
CREATE TABLE CompanyQuotation (
    quotation_id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    company_id INT NOT NULL,
    quoted_price DECIMAL(10, 2) NOT NULL,
    estimated_duration VARCHAR(100),
    materials_cost DECIMAL(10, 2),
    labor_cost DECIMAL(10, 2),
    other_costs DECIMAL(10, 2),
    notes TEXT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    valid_until DATE,
    terms_and_conditions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES JobRequest(request_id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    INDEX idx_request (request_id),
    INDEX idx_status (status)
);

-- Company Job Posting Table
CREATE TABLE CompanyJobPost (
    posting_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    requirements TEXT,
    salary_range VARCHAR(100),
    location VARCHAR(255),
    status ENUM('open', 'closed', 'filled') DEFAULT 'open',
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Category(category_id) ON DELETE CASCADE,
    INDEX idx_status (status)
);

-- Repairer Application Table
CREATE TABLE RepairerApplication (
    app_id INT PRIMARY KEY AUTO_INCREMENT,
    posting_id INT NOT NULL,
    repairer_id INT NOT NULL,
    cover_letter TEXT,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posting_id) REFERENCES CompanyJobPost(posting_id) ON DELETE CASCADE,
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE,
    INDEX idx_posting (posting_id)
);

-- Moderator Message Table
CREATE TABLE ModeratorMsg (
    msg_id INT PRIMARY KEY AUTO_INCREMENT,
    moderator_id INT NOT NULL,
    recipient_id INT NOT NULL,
    recipient_type ENUM('user', 'repairer', 'company') NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (moderator_id) REFERENCES Moderator(moderator_id) ON DELETE CASCADE,
    INDEX idx_status (status)
);

-- Chat Message Table
CREATE TABLE ChatMessage (
    session_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    repairer_id INT NOT NULL,
    message TEXT NOT NULL,
    sender_type ENUM('user', 'repairer') NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
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
    email VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Static Content Table
CREATE TABLE StaticContent (
    content_id INT PRIMARY KEY AUTO_INCREMENT,
    content_type VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (content_type)
);

-- Report Table (Admin/Moderator Reports)
CREATE TABLE Report (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    reporter_id INT NOT NULL,
    reported_entity_id INT NOT NULL,
    entity_type ENUM('user', 'repairer', 'company') NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('pending', 'resolved', 'dismissed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
);

-- Activity Log Table
CREATE TABLE ActivityLog (
    activity_id INT PRIMARY KEY AUTO_INCREMENT,
    actor_id INT NOT NULL,
    actor_type ENUM('admin', 'moderator', 'user', 'repairer', 'company') NOT NULL,
    action VARCHAR(255) NOT NULL,
    target_id INT,
    target_type VARCHAR(100),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_actor (actor_id, actor_type),
    INDEX idx_timestamp (timestamp)
);

-- Alert Table (Updated for proper notification system)
CREATE TABLE Notification (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    recipient_type ENUM('all', 'user', 'repairer', 'company') NOT NULL DEFAULT 'all',
    status ENUM('sent', 'pending', 'failed') DEFAULT 'sent',
    send_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_recipient (recipient_type),
    INDEX idx_send_date (send_date)
);

-- =====================================================
-- Admin Alert System Table
-- =====================================================
CREATE TABLE IF NOT EXISTS AdminAlert (
    alert_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    target_role ENUM('All', 'User', 'Repairer', 'Company', 'Moderator') DEFAULT 'All',
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
    created_by VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    views INT DEFAULT 0,
    INDEX idx_status (status),
    INDEX idx_target (target_role),
    INDEX idx_priority (priority),
    INDEX idx_created (created_at),
    FOREIGN KEY (created_by) REFERENCES Admin(username) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- =====================================================
-- Moderator Management Log Table (Track Admin Actions)
-- =====================================================
CREATE TABLE IF NOT EXISTS moderator_management_log (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    admin_username VARCHAR(100) NOT NULL,
    moderator_id INT,
    action VARCHAR(50) NOT NULL,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin (admin_username),
    INDEX idx_moderator (moderator_id),
    INDEX idx_action (action),
    INDEX idx_date (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================
-- Advertisement Reports Table (For Moderator Reports Feature)
-- =====================================================
CREATE TABLE IF NOT EXISTS ad_reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    ad_id INT NOT NULL,
    reporter_id INT NOT NULL,
    reporter_type ENUM('user', 'repairer', 'company', 'moderator') NOT NULL DEFAULT 'user',
    report_category ENUM(
        'inappropriate_content',
        'misleading_information',
        'spam',
        'copyright_violation',
        'offensive_material',
        'false_advertising',
        'broken_link',
        'poor_quality',
        'other'
    ) NOT NULL,
    description TEXT NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
    status ENUM('pending', 'investigating', 'resolved', 'dismissed', 'escalated') NOT NULL DEFAULT 'pending',
    assigned_to INT NULL,
    resolution_notes TEXT NULL,
    resolved_at TIMESTAMP NULL,
    resolved_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ad_id) REFERENCES Advertisement(ad_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES Moderator(moderator_id) ON DELETE SET NULL,
    FOREIGN KEY (resolved_by) REFERENCES Moderator(moderator_id) ON DELETE SET NULL,
    INDEX idx_ad (ad_id),
    INDEX idx_status (status),
    INDEX idx_severity (severity),
    INDEX idx_reporter (reporter_id, reporter_type),
    INDEX idx_assigned (assigned_to),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- NEW TABLES ADDED (Updated Version)
-- =====================================================

-- =====================================================
-- ✅ UPDATED: Ad Schedules Table (PRODUCTION-READY)
-- =====================================================
-- =====================================================
-- ✅ FIXED: Ad Schedules Table with STATUS column
-- =====================================================
CREATE TABLE ad_schedules (
    schedule_id INT PRIMARY KEY AUTO_INCREMENT,
    ad_id INT NOT NULL,
    placement ENUM('banner', 'sponsored', 'featured') NOT NULL,
    status ENUM('scheduled', 'active', 'completed', 'cancelled') DEFAULT 'scheduled',  -- ✅ ADDED: Missing column
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME DEFAULT '00:00:00',
    end_time TIME DEFAULT '23:59:59',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT DEFAULT NULL,
    INDEX idx_ad (ad_id),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_placement (placement),
    INDEX idx_status (status),
    FOREIGN KEY (ad_id) REFERENCES Advertisement(ad_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES Moderator(moderator_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- =====================================================
-- ✅ NEW: Placement Slot Limits Configuration Table
-- =====================================================
CREATE TABLE placement_limits (
    placement_type VARCHAR(50) PRIMARY KEY,
    max_slots_per_day INT NOT NULL DEFAULT 5,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Moderator Activity Table (NEW - Track moderator actions)
CREATE TABLE moderator_activity (
    activity_id INT PRIMARY KEY AUTO_INCREMENT,
    moderator_id INT NOT NULL,
    activity_type ENUM(
        'ad_approved',
        'ad_rejected',
        'ad_activated',
        'ad_scheduled',
        'ad_paused',
        'ad_resumed',
        'report_reviewed',
        'content_moderated',
        'user_warned',
        'user_banned',
        'other'
    ) NOT NULL,
    target_id INT NULL,
    target_title VARCHAR(255) NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_moderator (moderator_id),
    INDEX idx_type (activity_type),
    INDEX idx_target (target_id),
    INDEX idx_created (created_at),
    FOREIGN KEY (moderator_id) REFERENCES Moderator(moderator_id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- =====================================================
-- Financial System Tables (Auto-Calculated)
-- =====================================================
-- Financial Report Table (Parent - Monthly Summary)
CREATE TABLE FinancialReport (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    month INT NOT NULL,
    year INT NOT NULL,
    total_revenue DECIMAL(12, 2) DEFAULT 0.00,
    total_expenses DECIMAL(12, 2) DEFAULT 0.00,
    net_profit DECIMAL(12, 2) DEFAULT 0.00,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    generated_by INT NULL,
    FOREIGN KEY (generated_by) REFERENCES Moderator(moderator_id) ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- Financial Report Transaction Table (Child - All Transactions)
CREATE TABLE FinancialReportTransaction (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    report_id INT NOT NULL,
    transaction_type ENUM(
        'job_payment',
        'advertisement_revenue',
        'subscription_fee',
        'commission',
        'refund',
        'platform_fee',
        'other_income',
        'operating_expense',
        'marketing_expense',
        'other_expense'
    ) NOT NULL,
    related_id INT NULL,
    related_type ENUM('job', 'advertisement', 'payment', 'subscription', 'other') NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_report (report_id),
    INDEX idx_type (transaction_type),
    INDEX idx_related (related_id, related_type),
    INDEX idx_date (transaction_date),
    FOREIGN KEY (report_id) REFERENCES FinancialReport(report_id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- =====================================================
-- Company and Project Tables
-- =====================================================
-- Project Table
CREATE TABLE Project (
    project_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    location TEXT NOT NULL,
    district VARCHAR(100),
    budget DECIMAL(12, 2) NOT NULL,
    start_date DATE,
    end_date DATE,
    status ENUM('planning', 'in_progress', 'on_hold', 'completed', 'cancelled') DEFAULT 'planning',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    progress_percentage INT DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Category(category_id) ON DELETE CASCADE,
    INDEX idx_company (company_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
);

-- Contract Table
CREATE TABLE Contract (
    contract_id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    contract_value DECIMAL(12, 2) NOT NULL,
    payment_terms TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    terms_and_conditions TEXT,
    status ENUM('draft', 'active', 'completed', 'terminated') DEFAULT 'draft',
    signed_by_company BOOLEAN DEFAULT FALSE,
    signed_by_user BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Project(project_id) ON DELETE CASCADE,
    INDEX idx_project (project_id),
    INDEX idx_status (status)
);

-- Milestone Table
CREATE TABLE Milestone (
    milestone_id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    due_date DATE,
    completion_date DATE,
    status ENUM('pending', 'in_progress', 'completed', 'delayed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Project(project_id) ON DELETE CASCADE,
    INDEX idx_project (project_id),
    INDEX idx_status (status)
);

-- Milestone Payment Table
CREATE TABLE MilestonePayment (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    milestone_id INT NOT NULL,
    project_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('credit_card', 'bank_transfer', 'cash', 'online') NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (milestone_id) REFERENCES Milestone(milestone_id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES Project(project_id) ON DELETE CASCADE,
    INDEX idx_milestone (milestone_id),
    INDEX idx_project (project_id),
    INDEX idx_status (status)
);

-- Repairer Assignment Table (for company projects)
CREATE TABLE RepairerAssignment (
    assignment_id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    repairer_id INT NOT NULL,
    role VARCHAR(100),
    assigned_date DATE NOT NULL,
    status ENUM('active', 'completed', 'removed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Project(project_id) ON DELETE CASCADE,
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE,
    INDEX idx_project (project_id),
    INDEX idx_repairer (repairer_id)
);

-- Feedback Table
CREATE TABLE Feedback (
    feedback_id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK(rating >= 1 AND rating <= 5),
    comment TEXT,
    response TEXT,
    responded_by INT,
    responded_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES Project(project_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE,
    FOREIGN KEY (responded_by) REFERENCES Company(company_id) ON DELETE SET NULL,
    INDEX idx_project (project_id)
);

-- Staff Summary Table
CREATE TABLE StaffSummary (
    staff_summary_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    total_staff INT DEFAULT 0,
    active_projects INT DEFAULT 0,
    summary_date DATE NOT NULL,
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    INDEX idx_company (company_id)
);

-- =====================================================
-- ACCOUNT MODERATION SYSTEM
-- =====================================================
CREATE TABLE IF NOT EXISTS account_moderation_status (
    status_id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT NOT NULL,
    account_type ENUM('User', 'Repairer', 'Company') NOT NULL,
    account_status ENUM('ACTIVE', 'SUSPENDED', 'BANNED') DEFAULT 'ACTIVE',
    banned_permanent TINYINT(1) DEFAULT 0,
    suspended_until DATETIME NULL,
    moderation_reason TEXT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by VARCHAR(100) NULL,
    UNIQUE KEY unique_account (account_id, account_type),
    INDEX idx_status (account_status),
    INDEX idx_suspended_until (suspended_until),
    INDEX idx_account (account_id, account_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS account_moderation_cases (
    case_id INT PRIMARY KEY AUTO_INCREMENT,
    target_id INT NOT NULL,
    target_type ENUM('User', 'Repairer', 'Company') NOT NULL,
    admin_username VARCHAR(100) NOT NULL,
    action_type ENUM('BAN', 'SUSPEND', 'RESTORE') NOT NULL,
    reason TEXT NOT NULL,
    duration_days INT NULL,
    start_date DATETIME NULL,
    end_date DATETIME NULL,
    status_before ENUM('ACTIVE', 'SUSPENDED', 'BANNED') NOT NULL,
    status_after ENUM('ACTIVE', 'SUSPENDED', 'BANNED') NOT NULL,
    is_permanent TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_target (target_id, target_type),
    INDEX idx_action_type (action_type),
    INDEX idx_admin (admin_username),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- Insert Default Categories
-- =====================================================
INSERT INTO Category (name)
VALUES ('Plumbing'),
    ('Electrical'),
    ('Carpentry'),
    ('Painting'),
    ('Roofing'),
    ('Flooring'),
    ('HVAC'),
    ('Masonry'),
    ('Landscaping'),
    ('Pest Control'),
    ('Cleaning'),
    ('Appliance Repair'),
    ('Welding'),
    ('Tiling'),
    ('Waterproofing'),
    ('Interior Design'),
    ('Window Installation');

-- =====================================================
-- ✅ CRITICAL FIX: Insert Admin FIRST (before Moderators)
-- =====================================================
INSERT INTO Admin (username, password, email)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin@fixlanka.com'
);

-- =====================================================
-- ✅ CRITICAL FIX: Insert Moderators (BEFORE Advertisement)
-- This ensures moderator_id = 1 and 2 exist for foreign key
-- =====================================================
INSERT INTO Moderator (username, password, email, assigned_section)
VALUES 
(
    'moderator1',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'mod1@fixlanka.com',
    'Advertisements'
),
(
    'moderator2',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'mod2@fixlanka.com',
    'User Reports'
);

-- =====================================================
-- Insert Sample Users (AFTER Categories)
-- =====================================================
INSERT INTO User (f_name, l_name, email, password, district)
VALUES (
    'John',
    'Doe',
    'john@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Colombo'
),
(
    'Jane',
    'Smith',
    'jane@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Kandy'
),
(
    'Bob',
    'Johnson',
    'bob@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Galle'
);

-- =====================================================
-- Insert Sample Companies
-- =====================================================
INSERT INTO Company (
    name,
    email,
    password,
    phone,
    address,
    district,
    registration_number
)
VALUES (
    'BuildPro Solutions',
    'info@buildpro.lk',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '0112345678',
    '123 Main Street, Colombo',
    'Colombo',
    'BR123456'
),
(
    'HomeFix Services',
    'contact@homefix.lk',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '0119876543',
    '456 Galle Road, Colombo',
    'Colombo',
    'HF789012'
);

-- =====================================================
-- Insert Sample Repairers
-- =====================================================
INSERT INTO Repairer (
    f_name,
    l_name,
    email,
    password,
    phone,
    address,
    district,
    category_id,
    experience_years
)
VALUES (
    'Mike',
    'Wilson',
    'mike@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '0771234567',
    '789 Kandy Road',
    'Kandy',
    1,
    5
),
(
    'Sarah',
    'Davis',
    'sarah@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '0779876543',
    '321 Galle Road',
    'Galle',
    2,
    3
);

-- =====================================================
-- ✅ Insert Sample Advertisements (AFTER Moderators exist)
-- =====================================================
INSERT INTO Advertisement (
    provider_id,
    provider_type,
    title,
    description,
    type,
    budget,
    status
)
VALUES (
    1,
    'company',
    'Nimal Constructions',
    'Expert home renovation services',
    'sponsored',
    50000.00,
    'pending'
),
(
    2,
    'company',
    'Expert Cleaning Services',
    'Professional cleaning for homes and offices',
    'banner',
    30000.00,
    'pending'
),
(
    1,
    'company',
    'Happy Customer Constructions',
    'Building your dreams',
    'banner',
    40000.00,
    'approved'
),
(
    1,
    'repairer',
    'Mike Wilson Plumbing',
    '24/7 emergency plumbing services',
    'sponsored',
    25000.00,
    'active'
),
(
    2,
    'repairer',
    'Sarah Davis Electrical',
    'Certified electrical repairs and installations',
    'featured',
    35000.00,
    'rejected'
);


-- =====================================================
-- ✅ Insert Placement Slot Limits (NEW)
-- =====================================================
INSERT INTO placement_limits (placement_type, max_slots_per_day, description) VALUES
('banner', 10, 'Maximum 10 banner ads per day'),
('featured', 5, 'Maximum 5 featured ads per day'),
('sponsored', 8, 'Maximum 8 sponsored ads per day');

-- =====================================================
-- ✅ Insert Sample Scheduled Ads (NEW - for testing)
-- =====================================================
INSERT INTO ad_schedules (ad_id, placement, start_date, end_date, priority, created_by)
VALUES
(3, 'banner', '2026-02-01', '2026-02-10', 'high', 1),
(4, 'featured', '2026-02-03', '2026-02-15', 'medium', 1);

-- =====================================================
-- Sample Advertisement Reports Data (For Testing)
-- =====================================================
INSERT INTO ad_reports (
    ad_id,
    reporter_id,
    reporter_type,
    report_category,
    description,
    severity,
    status,
    assigned_to,
    created_at
) VALUES 
(
    1,
    1,
    'user',
    'misleading_information',
    'The advertisement claims 24/7 service but their contact number is not reachable after 6 PM',
    'medium',
    'pending',
    1,
    DATE_SUB(NOW(), INTERVAL 2 HOUR)
),
(
    2,
    2,
    'user',
    'poor_quality',
    'Low resolution images, text is barely readable on mobile devices',
    'low',
    'investigating',
    1,
    DATE_SUB(NOW(), INTERVAL 1 DAY)
),
(
    3,
    3,
    'user',
    'spam',
    'This advertisement appears multiple times on the same page',
    'high',
    'resolved',
    1,
    DATE_SUB(NOW(), INTERVAL 3 HOUR)
),
(
    1,
    2,
    'company',
    'false_advertising',
    'Competitor spreading false information about pricing',
    'high',
    'escalated',
    2,
    DATE_SUB(NOW(), INTERVAL 2 DAY)
),
(
    2,
    1,
    'repairer',
    'inappropriate_content',
    'Advertisement contains unprofessional language',
    'medium',
    'dismissed',
    1,
    DATE_SUB(NOW(), INTERVAL 5 HOUR)
);

-- Insert Sample Admin Alerts
INSERT INTO AdminAlert (title, message, target_role, priority, created_by, status) VALUES
('System Maintenance Notice', 'System maintenance scheduled for August 5th, 10 PM – 12 AM', 'All', 'high', 'admin', 'active'),
('New Feature Available', 'New feature: Real-time chat with service providers now available', 'User', 'medium', 'admin', 'active'),
('Commission Update', 'Commission rate update effective from next month', 'Repairer', 'high', 'admin', 'active');

-- Insert Sample Job Requests
INSERT INTO JobRequest (
    user_id,
    category_id,
    description,
    location,
    district,
    preferred_date,
    urgency
)
VALUES (
    1,
    1,
    'Kitchen sink is leaking',
    '123 Main Street, Colombo',
    'Colombo',
    '2025-02-15',
    'high'
),
(
    2,
    2,
    'Need electrical wiring for new room',
    '456 Kandy Road',
    'Kandy',
    '2025-02-20',
    'medium'
),
(
    3,
    3,
    'Custom furniture needed for living room',
    '789 Galle Road',
    'Galle',
    '2025-03-01',
    'low'
);

-- Insert Sample Payments
INSERT INTO Payment (job_request_id, paymentType, amount, status)
VALUES (1, 'credit_card', 15000.00, 'completed'),
    (2, 'bank_transfer', 25000.00, 'completed'),
    (3, 'cash', 8000.00, 'pending');

-- Insert Sample Moderator Activities
INSERT INTO moderator_activity (
    moderator_id,
    activity_type,
    target_id,
    target_title,
    description
)
VALUES (
    1,
    'ad_approved',
    3,
    'Happy Customer Constructions',
    'Advertisement approved and ready for scheduling'
),
(
    1,
    'ad_scheduled',
    3,
    'Happy Customer Constructions',
    'Advertisement scheduled from Feb 1-10, 2026'
),
(
    1,
    'ad_scheduled',
    4,
    'Mike Wilson Plumbing',
    'Advertisement scheduled from Feb 3-15, 2026'
);

-- =====================================================
-- ✅ DATABASE TRIGGER: Prevent Invalid Status Transitions
-- This is the SECOND line of defense (after application logic)
-- =====================================================
DELIMITER $$

CREATE TRIGGER before_advertisement_update
BEFORE UPDATE ON Advertisement
FOR EACH ROW
BEGIN
    -- ❌ RULE 1: Rejected and Expired are FINAL (immutable)
    IF OLD.status IN ('rejected', 'expired') AND NEW.status != OLD.status THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = '🚫 DATABASE PROTECTION: Rejected/Expired ads are FINAL and cannot be modified.';
    END IF;
    
    -- ❌ RULE 2: Cannot reject after approval
    IF OLD.status = 'approved' AND NEW.status = 'rejected' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = '🚫 DATABASE PROTECTION: Cannot reject an already approved advertisement.';
    END IF;
    
    -- ❌ RULE 3: Cannot go backward (Active → Pending/Approved)
    IF OLD.status = 'active' AND NEW.status IN ('pending', 'approved') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = '🚫 DATABASE PROTECTION: Active ads cannot revert to Pending or Approved.';
    END IF;
    
    -- ✅ RULE 4: Validate reviewed_by is a real moderator
    IF NEW.reviewed_by IS NOT NULL THEN
        IF NOT EXISTS (SELECT 1 FROM Moderator WHERE moderator_id = NEW.reviewed_by) THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = '🚫 DATABASE PROTECTION: reviewed_by must reference a valid moderator_id.';
        END IF;
    END IF;
END$$

DELIMITER ;

SELECT '✅ Advertisement lifecycle trigger installed successfully!' as Status;
SELECT '✅ Database created with 2 moderators (ID 1 and 2) for testing!' as Info;
SELECT '✅ Foreign key constraints configured correctly!' as Info;