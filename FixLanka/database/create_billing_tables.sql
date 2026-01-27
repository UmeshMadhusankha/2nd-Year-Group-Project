-- Billing and Subscription Tables for FixLanka
-- Created: January 18, 2026

USE fix_lanka;

-- Company Subscriptions Table
CREATE TABLE IF NOT EXISTS company_subscriptions (
    subscription_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    plan_name ENUM('free', 'basic', 'professional', 'enterprise') DEFAULT 'free',
    plan_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    billing_period ENUM('monthly', 'yearly') DEFAULT 'monthly',
    status ENUM('active', 'cancelled', 'expired', 'trial') DEFAULT 'trial',
    start_date DATE NOT NULL,
    end_date DATE NULL,
    next_billing_date DATE NULL,
    auto_renew TINYINT(1) DEFAULT 1,
    trial_ends_at DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES company(company_id) ON DELETE CASCADE,
    INDEX idx_company (company_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Payment Methods Table
CREATE TABLE IF NOT EXISTS payment_methods (
    payment_method_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    card_type ENUM('visa', 'mastercard', 'amex', 'discover') NOT NULL,
    last_four_digits CHAR(4) NOT NULL,
    card_holder_name VARCHAR(100) NOT NULL,
    expiry_month CHAR(2) NOT NULL,
    expiry_year CHAR(4) NOT NULL,
    billing_address VARCHAR(255) NULL,
    is_primary TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES company(company_id) ON DELETE CASCADE,
    INDEX idx_company (company_id),
    INDEX idx_primary (is_primary),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data will be inserted via PHP when a company first logs in
