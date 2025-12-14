-- =====================================================
-- CORRECTED DUMMY DATA - WITH COMPANY QUOTATIONS
-- Matches actual database schema
-- =====================================================

-- Clean up (optional - uncomment if needed)
-- DELETE FROM CompanyQuotation WHERE quotation_id >= 2001;
-- DELETE FROM JobRequest WHERE request_id >= 2001;
-- DELETE FROM User WHERE user_id >= 2001;

-- =====================================================
-- 1. INSERT 2 TEST CUSTOMERS
-- =====================================================

INSERT INTO User (user_id, username, email, password, phone, address, user_role, registration_date, is_active, email_verified)
VALUES
(2001, 'John Silva', 'john.silva@test.com', '$2y$10$test', '0771234567', '123 Galle Road, Colombo', 'customer', NOW(), 1, 1),
(2002, 'Sarah Perera', 'sarah.perera@test.com', '$2y$10$test', '0772345678', '45 Kandy Road, Kandy', 'customer', NOW(), 1, 1);

-- =====================================================
-- 2. INSERT 2 JOB REQUESTS
-- =====================================================

INSERT INTO JobRequest (request_id, customer_id, title, description, category, location, district, address, budget_min, budget_max, urgency, finish_date, service_provider_type, status, posted_date, updated_date, is_active)
VALUES
(2001, 2001, 'Office Renovation Project', 'Complete office renovation including painting, electrical work, and furniture installation.', 'Renovation', 'Colombo 03', 'Colombo', '123 Galle Road, Colombo 03', 250000, 350000, 'medium', '2026-03-15', 'company', 'approved', NOW(), NOW(), 1),
(2002, 2002, 'Kitchen Plumbing Repair', 'Kitchen sink and pipe replacement needed urgently. Water leakage issue.', 'Plumbing', 'Kandy', 'Kandy', '45 Kandy Road, Kandy', 15000, 25000, 'high', '2026-01-22', 'company', 'approved', NOW(), NOW(), 1);

-- =====================================================
-- 3. INSERT 2 ACCEPTED COMPANY QUOTATIONS
-- =====================================================
-- IMPORTANT: Change user_id = 1 to YOUR company's user_id!
-- (In this system, companies are users with role='company')

INSERT INTO CompanyQuotation (quotation_id, request_id, user_id, title, description, labor_cost, material_cost, transport_cost, other_charges, total_amount, start_date, completion_date, estimated_duration, payment_terms, warranty_period, additional_terms, status, created_at, updated_at)
VALUES
-- Quotation 1: Office Renovation (Change user_id = 1 to your company's user_id)
(2001, 2001, 1, 'Office Renovation Project', 'Complete office renovation with premium materials and experienced team. Includes painting, electrical work, and furniture installation.', 95000, 180000, 5000, 15000, 295000, '2026-02-01', '2026-03-15', 45, '30% advance, 40% mid-progress, 30% completion', '12 months', 'Premium materials warranty included. Free maintenance for first 6 months.', 'accepted', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Quotation 2: Kitchen Plumbing (Change user_id = 1 to your company's user_id)
(2002, 2002, 1, 'Kitchen Plumbing Repair', 'Urgent kitchen plumbing repair with high-quality fixtures. Includes pipe replacement and leak fixing.', 9000, 8000, 500, 1000, 18500, '2026-01-20', '2026-01-22', 2, '100% upon completion', '6 months', 'Emergency service included. Quality fixtures warranty.', 'accepted', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- =====================================================
-- 4. VERIFY THE DATA
-- =====================================================

-- Check accepted quotations
SELECT 
    q.quotation_id,
    q.title AS project_title,
    u.username AS customer_name,
    q.total_amount AS quoted_price,
    q.start_date,
    q.completion_date,
    q.status,
    q.created_at
FROM CompanyQuotation q
JOIN JobRequest jr ON q.request_id = jr.request_id
JOIN User u ON jr.customer_id = u.user_id
WHERE q.quotation_id IN (2001, 2002)
AND q.status = 'accepted';

-- =====================================================
-- SUMMARY
-- =====================================================
-- This script created:
-- ✅ 2 Customers (user_id 2001-2002)
-- ✅ 2 Job Requests (request_id 2001-2002)
-- ✅ 2 Accepted Company Quotations (quotation_id 2001-2002)
--
-- Quotation 1: Office Renovation - LKR 295,000 - 45 days
-- Quotation 2: Kitchen Plumbing - LKR 18,500 - 2 days
--
-- ⚠️ CRITICAL: Update user_id = 1 to your company's user_id!
--
-- To find your company's user_id, run:
-- SELECT user_id, username, email FROM User WHERE user_role = 'company';
-- =====================================================
