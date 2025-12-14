-- =====================================================
-- DATABASE UPDATES FOR COMPANY EMPLOYEES
-- Run this file to add/update tables for Company Employee Management
-- Date: November 20, 2025
-- =====================================================

USE fix_lanka;

-- =====================================================
-- Create CompanyEmployee Table (if not exists)
-- =====================================================
CREATE TABLE IF NOT EXISTS CompanyEmployee (
    employee_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    repairer_id INT NULL, -- Optional link to Repairer table if employee is also a registered repairer
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    specialty VARCHAR(100) NOT NULL, -- e.g., 'Plumbing', 'Electrical', 'Carpentry', 'HVAC'
    hourly_rate DECIMAL(10,2) DEFAULT 0.00,
    rating DECIMAL(3,2) DEFAULT 0.00,
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    hire_date DATE,
    experience_years INT DEFAULT 0,
    certification_details TEXT,
    profile_photo VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE SET NULL,
    INDEX idx_company (company_id),
    INDEX idx_specialty (specialty),
    INDEX idx_status (status),
    INDEX idx_rating (rating)
);

-- =====================================================
-- Update StaffSummary Table (add more fields)
-- =====================================================
-- Drop old table if exists and recreate with better structure
DROP TABLE IF EXISTS StaffSummary;

CREATE TABLE StaffSummary (
    staff_summary_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    specialty VARCHAR(100) NOT NULL,
    total_count INT DEFAULT 0,
    active_count INT DEFAULT 0,
    inactive_count INT DEFAULT 0,
    avg_rating DECIMAL(3,2) DEFAULT 0.00,
    avg_hourly_rate DECIMAL(10,2) DEFAULT 0.00,
    min_hourly_rate DECIMAL(10,2) DEFAULT 0.00,
    max_hourly_rate DECIMAL(10,2) DEFAULT 0.00,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    UNIQUE KEY unique_company_specialty (company_id, specialty),
    INDEX idx_company (company_id),
    INDEX idx_specialty (specialty)
);

-- =====================================================
-- Create Trigger to Auto-Update StaffSummary
-- =====================================================

-- Trigger after INSERT
DROP TRIGGER IF EXISTS after_employee_insert;
DELIMITER //
CREATE TRIGGER after_employee_insert
AFTER INSERT ON CompanyEmployee
FOR EACH ROW
BEGIN
    INSERT INTO StaffSummary (company_id, specialty, total_count, active_count, inactive_count, avg_rating, avg_hourly_rate, min_hourly_rate, max_hourly_rate)
    SELECT 
        NEW.company_id,
        NEW.specialty,
        COUNT(*) as total_count,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
        SUM(CASE WHEN status IN ('inactive', 'on_leave') THEN 1 ELSE 0 END) as inactive_count,
        AVG(rating) as avg_rating,
        AVG(hourly_rate) as avg_hourly_rate,
        MIN(hourly_rate) as min_hourly_rate,
        MAX(hourly_rate) as max_hourly_rate
    FROM CompanyEmployee 
    WHERE company_id = NEW.company_id AND specialty = NEW.specialty
    ON DUPLICATE KEY UPDATE
        total_count = VALUES(total_count),
        active_count = VALUES(active_count),
        inactive_count = VALUES(inactive_count),
        avg_rating = VALUES(avg_rating),
        avg_hourly_rate = VALUES(avg_hourly_rate),
        min_hourly_rate = VALUES(min_hourly_rate),
        max_hourly_rate = VALUES(max_hourly_rate),
        last_update = CURRENT_TIMESTAMP;
END//
DELIMITER ;

-- Trigger after UPDATE
DROP TRIGGER IF EXISTS after_employee_update;
DELIMITER //
CREATE TRIGGER after_employee_update
AFTER UPDATE ON CompanyEmployee
FOR EACH ROW
BEGIN
    -- Update old specialty if changed
    IF OLD.specialty != NEW.specialty THEN
        INSERT INTO StaffSummary (company_id, specialty, total_count, active_count, inactive_count, avg_rating, avg_hourly_rate, min_hourly_rate, max_hourly_rate)
        SELECT 
            OLD.company_id,
            OLD.specialty,
            COUNT(*) as total_count,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
            SUM(CASE WHEN status IN ('inactive', 'on_leave') THEN 1 ELSE 0 END) as inactive_count,
            AVG(rating) as avg_rating,
            AVG(hourly_rate) as avg_hourly_rate,
            MIN(hourly_rate) as min_hourly_rate,
            MAX(hourly_rate) as max_hourly_rate
        FROM CompanyEmployee 
        WHERE company_id = OLD.company_id AND specialty = OLD.specialty
        ON DUPLICATE KEY UPDATE
            total_count = VALUES(total_count),
            active_count = VALUES(active_count),
            inactive_count = VALUES(inactive_count),
            avg_rating = VALUES(avg_rating),
            avg_hourly_rate = VALUES(avg_hourly_rate),
            min_hourly_rate = VALUES(min_hourly_rate),
            max_hourly_rate = VALUES(max_hourly_rate),
            last_update = CURRENT_TIMESTAMP;
    END IF;
    
    -- Update new specialty
    INSERT INTO StaffSummary (company_id, specialty, total_count, active_count, inactive_count, avg_rating, avg_hourly_rate, min_hourly_rate, max_hourly_rate)
    SELECT 
        NEW.company_id,
        NEW.specialty,
        COUNT(*) as total_count,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
        SUM(CASE WHEN status IN ('inactive', 'on_leave') THEN 1 ELSE 0 END) as inactive_count,
        AVG(rating) as avg_rating,
        AVG(hourly_rate) as avg_hourly_rate,
        MIN(hourly_rate) as min_hourly_rate,
        MAX(hourly_rate) as max_hourly_rate
    FROM CompanyEmployee 
    WHERE company_id = NEW.company_id AND specialty = NEW.specialty
    ON DUPLICATE KEY UPDATE
        total_count = VALUES(total_count),
        active_count = VALUES(active_count),
        inactive_count = VALUES(inactive_count),
        avg_rating = VALUES(avg_rating),
        avg_hourly_rate = VALUES(avg_hourly_rate),
        min_hourly_rate = VALUES(min_hourly_rate),
        max_hourly_rate = VALUES(max_hourly_rate),
        last_update = CURRENT_TIMESTAMP;
END//
DELIMITER ;

-- Trigger after DELETE
DROP TRIGGER IF EXISTS after_employee_delete;
DELIMITER //
CREATE TRIGGER after_employee_delete
AFTER DELETE ON CompanyEmployee
FOR EACH ROW
BEGIN
    INSERT INTO StaffSummary (company_id, specialty, total_count, active_count, inactive_count, avg_rating, avg_hourly_rate, min_hourly_rate, max_hourly_rate)
    SELECT 
        OLD.company_id,
        OLD.specialty,
        COUNT(*) as total_count,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
        SUM(CASE WHEN status IN ('inactive', 'on_leave') THEN 1 ELSE 0 END) as inactive_count,
        COALESCE(AVG(rating), 0) as avg_rating,
        COALESCE(AVG(hourly_rate), 0) as avg_hourly_rate,
        COALESCE(MIN(hourly_rate), 0) as min_hourly_rate,
        COALESCE(MAX(hourly_rate), 0) as max_hourly_rate
    FROM CompanyEmployee 
    WHERE company_id = OLD.company_id AND specialty = OLD.specialty
    ON DUPLICATE KEY UPDATE
        total_count = VALUES(total_count),
        active_count = VALUES(active_count),
        inactive_count = VALUES(inactive_count),
        avg_rating = VALUES(avg_rating),
        avg_hourly_rate = VALUES(avg_hourly_rate),
        min_hourly_rate = VALUES(min_hourly_rate),
        max_hourly_rate = VALUES(max_hourly_rate),
        last_update = CURRENT_TIMESTAMP;
END//
DELIMITER ;

-- =====================================================
-- Insert Sample Data (Optional - for testing)
-- =====================================================
/*
-- Sample company employees (company_id = 1)
INSERT INTO CompanyEmployee (company_id, first_name, last_name, email, phone, specialty, hourly_rate, rating, status, hire_date, experience_years) VALUES
(1, 'Kasun', 'Perera', 'kasun.p@fixlanka.lk', '0771234567', 'Plumbing', 2500.00, 4.8, 'active', '2023-01-15', 5),
(1, 'Nimal', 'Silva', 'nimal.s@fixlanka.lk', '0772345678', 'Plumbing', 2200.00, 4.6, 'active', '2023-03-20', 3),
(1, 'Saman', 'Fernando', 'saman.f@fixlanka.lk', '0773456789', 'Electrical', 2800.00, 4.9, 'active', '2022-11-10', 7),
(1, 'Dinesh', 'Wickrama', 'dinesh.w@fixlanka.lk', '0774567890', 'Electrical', 2500.00, 4.7, 'active', '2023-05-15', 4),
(1, 'Pradeep', 'Jayasinghe', 'pradeep.j@fixlanka.lk', '0775678901', 'Carpentry', 2400.00, 4.8, 'active', '2023-02-28', 6),
(1, 'Sunil', 'Bandara', 'sunil.b@fixlanka.lk', '0776789012', 'Carpentry', 2000.00, 4.5, 'active', '2023-07-10', 2),
(1, 'Mahesh', 'Gunasekara', 'mahesh.g@fixlanka.lk', '0777890123', 'HVAC', 3000.00, 4.9, 'active', '2022-08-15', 8),
(1, 'Chandana', 'Rodrigo', 'chandana.r@fixlanka.lk', '0778901234', 'HVAC', 2800.00, 4.6, 'active', '2023-04-20', 5);
*/

-- =====================================================
-- Verification Queries
-- =====================================================
-- Check if tables were created
-- SELECT * FROM CompanyEmployee;
-- SELECT * FROM StaffSummary;

-- Check triggers
-- SHOW TRIGGERS WHERE `Table` = 'CompanyEmployee';

-- =====================================================
-- ENHANCED COMPANY JOB POSTING TABLE
-- Date: November 21, 2025
-- =====================================================

-- Backup existing data if any
CREATE TABLE IF NOT EXISTS CompanyJobPost_backup AS SELECT * FROM CompanyJobPost WHERE 1=0;
INSERT INTO CompanyJobPost_backup SELECT * FROM CompanyJobPost;

-- Drop old RepairerApplication table first (has foreign key dependency)
DROP TABLE IF EXISTS RepairerApplication;

-- Drop the old job post table
DROP TABLE IF EXISTS CompanyJobPost;

-- Create enhanced job posting table
CREATE TABLE CompanyJobPost (
    posting_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    
    -- Basic Information
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    employment_type ENUM('freelance', 'contract', 'part-time', 'project-based') NOT NULL,
    related_project_id INT NULL,
    
    -- Job Details
    description TEXT NOT NULL,
    min_experience ENUM('entry', 'junior', 'mid', 'senior', 'expert') NOT NULL,
    priority_level ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    min_budget DECIMAL(10,2) NOT NULL,
    max_budget DECIMAL(10,2) NOT NULL,
    application_deadline DATE NULL,
    required_skills TEXT NULL,
    location VARCHAR(500) NOT NULL,
    location_requirements VARCHAR(500) NULL,
    
    -- Status and Options
    status ENUM('draft', 'open', 'closed', 'filled') DEFAULT 'draft',
    notify_repairers BOOLEAN DEFAULT TRUE,
    allow_direct_applications BOOLEAN DEFAULT TRUE,
    
    -- Metadata
    created_by INT NULL,
    posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    closed_date TIMESTAMP NULL,
    
    -- Foreign Keys
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_company (company_id),
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_posted_date (posted_date),
    INDEX idx_deadline (application_deadline)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Recreate RepairerApplication table
CREATE TABLE RepairerApplication (
    app_id INT PRIMARY KEY AUTO_INCREMENT,
    repairer_id INT NOT NULL,
    posting_id INT NOT NULL,
    date_applied TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    app_status ENUM('pending', 'reviewed', 'accepted', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE,
    FOREIGN KEY (posting_id) REFERENCES CompanyJobPost(posting_id) ON DELETE CASCADE,
    INDEX idx_repairer (repairer_id),
    INDEX idx_posting (posting_id),
    INDEX idx_status (app_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Verification queries
SELECT 'CompanyJobPost table updated successfully!' AS Status;
DESCRIBE CompanyJobPost;

