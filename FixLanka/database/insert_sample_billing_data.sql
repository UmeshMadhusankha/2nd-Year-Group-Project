-- Insert sample billing data for testing
-- Run this after logging in to see which company_id you have

USE fix_lanka;

-- Get your company_id first by checking the session or company table
-- Replace X with your actual company_id from the session

-- Example for company_id = 1 (adjust as needed)
SET @company_id = 1;

-- Insert subscription (if doesn't exist)
INSERT INTO company_subscriptions (company_id, plan_name, plan_price, billing_period, status, start_date, next_billing_date, auto_renew)
VALUES (@company_id, 'professional', 5000.00, 'monthly', 'active', '2025-10-15', '2026-02-15', 1)
ON DUPLICATE KEY UPDATE company_id = company_id;

-- Insert payment methods (if don't exist)
INSERT INTO payment_methods (company_id, card_type, last_four_digits, card_holder_name, expiry_month, expiry_year, is_primary, is_active)
VALUES 
(@company_id, 'visa', '4242', 'Company Name', '12', '2026', 1, 1),
(@company_id, 'mastercard', '8888', 'Company Name', '09', '2027', 0, 1)
ON DUPLICATE KEY UPDATE company_id = company_id;

-- Verify data
SELECT * FROM company_subscriptions WHERE company_id = @company_id;
SELECT * FROM payment_methods WHERE company_id = @company_id;
SELECT * FROM billinghistory WHERE company_id = @company_id;
