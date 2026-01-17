-- Run this script in your MySQL database to create the missing tables

-- Company Bank Accounts
CREATE TABLE IF NOT EXISTS CompanyBankAccount (
    bank_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    bank_name VARCHAR(100) NOT NULL,
    branch_name VARCHAR(100) NOT NULL,
    account_number VARCHAR(50) NOT NULL,
    account_holder_name VARCHAR(150) NOT NULL,
    account_type ENUM('savings', 'current', 'business') NOT NULL,
    swift_code VARCHAR(50),
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    INDEX idx_company (company_id)
);

-- Company Digital Wallets
CREATE TABLE IF NOT EXISTS CompanyWallet (
    wallet_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    provider_name VARCHAR(100) NOT NULL, -- e.g., 'eZ Cash', 'PayHere'
    merchant_id VARCHAR(100),
    wallet_number VARCHAR(50),
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    is_connected BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    INDEX idx_company (company_id)
);

-- Company Expenses
CREATE TABLE IF NOT EXISTS CompanyExpense (
    expense_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    project_id INT, -- Optional link to project
    category ENUM('materials', 'labor', 'transport', 'equipment', 'permits', 'other') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    expense_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES Company(company_id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES Project(project_id) ON DELETE SET NULL,
    INDEX idx_company (company_id),
    INDEX idx_project (project_id)
);
