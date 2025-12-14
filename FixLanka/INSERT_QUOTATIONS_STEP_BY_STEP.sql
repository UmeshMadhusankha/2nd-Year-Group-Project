-- =====================================================
-- STEP 1: Find your company's user_id
-- Run this query FIRST in phpMyAdmin
-- =====================================================

SELECT user_id, username, email, user_role 
FROM User 
WHERE user_role = 'company';

-- Copy the user_id from the result above, then use it in the next queries

-- =====================================================
-- STEP 2: Clean up old test data (optional)
-- =====================================================

DELETE FROM CompanyQuotation WHERE quotation_id >= 2001;
DELETE FROM JobRequest WHERE request_id >= 2001;
DELETE FROM User WHERE user_id >= 2001;

-- =====================================================
-- STEP 3: Insert test data
-- REPLACE 'YOUR_COMPANY_USER_ID' with the user_id from Step 1
-- =====================================================

-- Insert 2 test customers
INSERT INTO User (user_id, username, email, password, phone, address, user_role, registration_date, is_active, email_verified)
VALUES
(2001, 'John Silva', 'john.silva@test.com', '$2y$10$test', '0771234567', '123 Galle Road, Colombo', 'customer', NOW(), 1, 1),
(2002, 'Sarah Perera', 'sarah.perera@test.com', '$2y$10$test', '0772345678', '45 Kandy Road, Kandy', 'customer', NOW(), 1, 1);

-- Insert 2 job requests
INSERT INTO JobRequest (request_id, customer_id, title, description, category, location, district, address, budget_min, budget_max, urgency, finish_date, service_provider_type, status, posted_date, is_active)
VALUES
(2001, 2001, 'Office Renovation Project', 'Complete office renovation including painting, electrical work, and furniture installation.', 'Renovation', 'Colombo 03', 'Colombo', '123 Galle Road, Colombo 03', 250000, 350000, 'medium', '2026-03-15', 'company', 'approved', NOW(), 1),
(2002, 2002, 'Kitchen Plumbing Repair', 'Kitchen sink and pipe replacement needed urgently. Water leakage issue.', 'Plumbing', 'Kandy', 'Kandy', '45 Kandy Road, Kandy', 15000, 25000, 'high', '2026-01-22', 'company', 'approved', NOW(), 1);

-- Insert 2 accepted quotations (REPLACE 1 with YOUR_COMPANY_USER_ID)
INSERT INTO CompanyQuotation (quotation_id, request_id, user_id, title, description, labor_cost, material_cost, transport_cost, other_charges, total_amount, start_date, completion_date, estimated_duration, payment_terms, warranty_period, status)
VALUES
(2001, 2001, 1, 'Office Renovation Project', 'Complete renovation with premium materials', 95000, 180000, 5000, 15000, 295000, '2026-02-01', '2026-03-15', 45, '30% advance, 40% mid, 30% final', '12 months', 'accepted'),
(2002, 2002, 1, 'Kitchen Plumbing Repair', 'Urgent plumbing repair with quality fixtures', 9000, 8000, 500, 1000, 18500, '2026-01-20', '2026-01-22', 2, '100% upon completion', '6 months', 'accepted');

-- =====================================================
-- STEP 4: Verify the data
-- =====================================================

SELECT 
    q.quotation_id,
    q.title,
    u.username AS customer_name,
    q.total_amount,
    q.start_date,
    q.status
FROM CompanyQuotation q
JOIN JobRequest jr ON q.request_id = jr.request_id
JOIN User u ON jr.customer_id = u.user_id
WHERE q.user_id = 1;  -- REPLACE 1 with YOUR_COMPANY_USER_ID

-- =====================================================
-- INSTRUCTIONS:
-- 1. Run Step 1 query to find your company's user_id
-- 2. Replace ALL occurrences of "1" with your actual user_id in the INSERT statements (lines 39 and 40)
-- 3. Replace "1" in the final SELECT query (line 52)
-- 4. Run Steps 2, 3, and 4 in phpMyAdmin
-- 5. Refresh your contracts page and click "New Contract"
-- 6. You should see 2 project cards!
-- =====================================================
