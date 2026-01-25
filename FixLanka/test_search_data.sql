-- =====================================================
-- TEST DATA FOR SEARCH FUNCTIONALITY
-- Run this in phpMyAdmin or MySQL command line
-- =====================================================

-- First, let's check what company_id you're logged in as
-- (You'll need to replace 2 with your actual company_id if different)

-- =====================================================
-- 1. INSERT TEST PROJECTS
-- =====================================================

-- Kitchen Repair Project
INSERT INTO project (company_id, title, description, budget, status, deadline, created_at)
VALUES 
(2, 'Kitchen Renovation Project', 'Complete kitchen renovation including sink repair, cabinet installation, and electrical work', 150000.00, 'active', '2026-03-15', NOW());

-- Outlet Installation Project
INSERT INTO project (company_id, title, description, budget, status, deadline, created_at)
VALUES 
(2, 'Office Outlet Installation', 'Install 15 new electrical outlets in office building second floor', 75000.00, 'active', '2026-02-20', NOW());

-- Bathroom Repair Project
INSERT INTO project (company_id, title, description, budget, status, deadline, created_at)
VALUES 
(2, 'Bathroom Plumbing Repair', 'Fix leaking pipes, replace bathroom fixtures, and install new water heater', 95000.00, 'pending', '2026-04-10', NOW());

-- AC Repair Project
INSERT INTO project (company_id, title, description, budget, status, deadline, created_at)
VALUES 
(2, 'Air Conditioning System Repair', 'Repair central AC system and replace filters in main office building', 120000.00, 'active', '2026-02-28', NOW());

-- Door Repair Project
INSERT INTO project (company_id, title, description, budget, status, deadline, created_at)
VALUES 
(2, 'Cabinet Door Replacement', 'Replace damaged cabinet doors in kitchen and repair hinges', 45000.00, 'completed', '2026-01-20', NOW());


-- =====================================================
-- 2. INSERT TEST REPAIR REQUESTS (JOB REQUESTS)
-- =====================================================

-- Note: We need to know the structure of your jobrequest table
-- I'll use common fields based on your create_database.sql

-- Kitchen Sink Repair Request
INSERT INTO jobrequest (user_id, company_id, category_id, title, description, address, district, urgency, status, created_at)
VALUES 
(1, 2, 1, 'Kitchen Sink Leaking', 'Kitchen sink has been leaking for 2 days. Water dripping from under the sink continuously. Needs urgent repair.', 'NO:115, Kumbukanda, Mahailuppallama', 'Anuradhapura', 'high', 'pending', NOW());

-- Outlet Not Working Request
INSERT INTO jobrequest (user_id, company_id, category_id, title, description, address, district, urgency, status, created_at)
VALUES 
(1, 2, 2, 'Outlet Not Working in Bedroom', 'Two electrical outlets in master bedroom stopped working. Cannot charge devices. May be circuit breaker issue.', 'NO:115, Kumbukanda, Mahailuppallama', 'Anuradhapura', 'medium', 'pending', NOW());

-- Door Repair Request
INSERT INTO jobrequest (user_id, company_id, category_id, title, description, address, district, urgency, status, created_at)
VALUES 
(1, 2, 3, 'Cabinet Door Broken', 'Kitchen cabinet door hinge broken. Door hanging loose and cannot close properly. Need replacement hinges.', 'NO:115, Kumbukanda, Mahailuppallama', 'Anuradhapura', 'low', 'accepted', NOW());

-- AC Not Cooling Request
INSERT INTO jobrequest (user_id, company_id, category_id, title, description, address, district, urgency, status, created_at)
VALUES 
(1, 2, 4, 'Air Conditioner Not Cooling', 'AC unit running but not cooling the room. May need refrigerant refill or compressor check. Very hot weather!', 'NO:115, Kumbukanda, Mahailuppallama', 'Anuradhapura', 'high', 'in_progress', NOW());

-- Pipe Repair Request
INSERT INTO jobrequest (user_id, company_id, category_id, title, description, address, district, urgency, status, created_at)
VALUES 
(1, 2, 1, 'Bathroom Pipe Burst', 'Main water pipe in bathroom burst this morning. Water flooding bathroom floor. Emergency repair needed immediately!', 'NO:115, Kumbukanda, Mahailuppallama', 'Anuradhapura', 'critical', 'pending', NOW());


-- =====================================================
-- 3. VERIFY DATA WAS INSERTED
-- =====================================================

-- Check projects
SELECT project_id, title, status, created_at 
FROM project 
WHERE company_id = 2 
ORDER BY created_at DESC;

-- Check repair requests
SELECT request_id, title, urgency, status, created_at 
FROM jobrequest 
WHERE company_id = 2 
ORDER BY created_at DESC;


-- =====================================================
-- 4. TEST SEARCH QUERIES
-- =====================================================

-- Test 1: Search for "kitchen"
SELECT 'Projects with kitchen:' as test_type;
SELECT project_id, title FROM project WHERE company_id = 2 AND (title LIKE '%kitchen%' OR description LIKE '%kitchen%');

SELECT 'Requests with kitchen:' as test_type;
SELECT request_id, title FROM jobrequest WHERE company_id = 2 AND (title LIKE '%kitchen%' OR description LIKE '%kitchen%');

-- Test 2: Search for "repair"
SELECT 'Projects with repair:' as test_type;
SELECT project_id, title FROM project WHERE company_id = 2 AND (title LIKE '%repair%' OR description LIKE '%repair%');

SELECT 'Requests with repair:' as test_type;
SELECT request_id, title FROM jobrequest WHERE company_id = 2 AND (title LIKE '%repair%' OR description LIKE '%repair%');

-- Test 3: Search for "outlet"
SELECT 'Items with outlet:' as test_type;
SELECT project_id, title, 'project' as type FROM project WHERE company_id = 2 AND (title LIKE '%outlet%' OR description LIKE '%outlet%')
UNION
SELECT request_id, title, 'request' as type FROM jobrequest WHERE company_id = 2 AND (title LIKE '%outlet%' OR description LIKE '%outlet%');


-- =====================================================
-- 5. CLEANUP (If you want to remove test data later)
-- =====================================================

-- Uncomment these lines to delete test data:

-- DELETE FROM project WHERE company_id = 2 AND title LIKE '%Kitchen Renovation%';
-- DELETE FROM project WHERE company_id = 2 AND title LIKE '%Office Outlet%';
-- DELETE FROM project WHERE company_id = 2 AND title LIKE '%Bathroom Plumbing%';
-- DELETE FROM project WHERE company_id = 2 AND title LIKE '%Air Conditioning%';
-- DELETE FROM project WHERE company_id = 2 AND title LIKE '%Cabinet Door Replacement%';

-- DELETE FROM jobrequest WHERE company_id = 2 AND title LIKE '%Kitchen Sink%';
-- DELETE FROM jobrequest WHERE company_id = 2 AND title LIKE '%Outlet Not Working%';
-- DELETE FROM jobrequest WHERE company_id = 2 AND title LIKE '%Cabinet Door Broken%';
-- DELETE FROM jobrequest WHERE company_id = 2 AND title LIKE '%Air Conditioner%';
-- DELETE FROM jobrequest WHERE company_id = 2 AND title LIKE '%Bathroom Pipe%';
