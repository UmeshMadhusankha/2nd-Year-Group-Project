-- Phase 4 Workforce Sample Data
-- Inserts realistic sample data for the Workforce System to test the flow
-- Note: Dependent on user, company, repairer, project, and category data existing.

-- Get some existing IDs to link the sample data
SET @comp_id = 5;
SET @cat_id = (SELECT category_id FROM category LIMIT 1);
SET @rep1_id = (SELECT repairer_id FROM repairer LIMIT 1);
SET @rep2_id = (SELECT repairer_id FROM repairer WHERE repairer_id != @rep1_id LIMIT 1);
SET @proj_id = (SELECT project_id FROM project LIMIT 1);

-- 1. Create a Job Posting from the Company
INSERT INTO job_postings (company_id, title, category, category_id, employment_type, description, requirements, min_experience, min_budget, max_budget, location, location_id, status)
VALUES (@comp_id, 'Senior Plumber Needed for Commercial Project', 'Plumbing', @cat_id, 'contract', 'We are looking for an experienced plumber to help us with a large commercial building project.', 'Must have 5+ years of experience and commercial certifications.', 5, 50000.00, 80000.00, 'Colombo', NULL, 'open');

SET @job_id = LAST_INSERT_ID();

-- 2. Create Repairer Applications
-- Applicant 1: Pending Application
INSERT INTO repairer_applications (job_posting_id, repairer_id, cover_letter, expected_rate, status)
VALUES (@job_id, @rep1_id, 'Hi, I have 6 years of plumbing experience and would love to join your team for this project.', 3500.00, 'pending');

-- Applicant 2: Approved Application -> Leads to Onboarding
INSERT INTO repairer_applications (job_posting_id, repairer_id, cover_letter, expected_rate, status)
VALUES (@job_id, @rep2_id, 'I am a master plumber with extensive commercial experience.', 4000.00, 'approved');

-- 3. Company Employee (The Onboarded repairer)
-- This assumes the application approval script would do this, but we insert it manually since the script didn't run.
INSERT INTO company_employees (company_id, repairer_id, job_title, employment_type, status, hired_date, hourly_rate, notes)
VALUES (@comp_id, @rep2_id, 'Senior Plumber', 'contract', 'active', CURDATE(), 4000.00, 'Hired from the Commercial Plumber job posting.');

-- 4. Freelancer Assignment (Job Offer sent to the new employee)
INSERT INTO freelancer_assignments (company_id, repairer_id, project_id, pricing_model, rate_or_price, estimated_hours, start_date, deadline_date, notes, status)
VALUES (@comp_id, @rep2_id, @proj_id, 'hourly', 4000.00, 40.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'Please review the attached project blueprints. Initial focus will be on the ground floor facilities.', 'offered');

SELECT 'Successfully inserted Phase 4 Workforce Sample Data.' AS result;
