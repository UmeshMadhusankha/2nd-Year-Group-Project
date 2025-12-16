-- =====================================================
-- Sample Advertisement Data for Testing
-- Run this after creating the database
-- =====================================================

USE fix_lanka;

-- Insert sample companies first
INSERT INTO Company (name, business_type, registration_no, tax_id, address, email, website, contact_no, districts, password, description, rating) VALUES
('ABC Construction Ltd', 'Construction', 'REG001', 'TAX001', '123 Main St, Colombo', 'info@abcconstruction.lk', 'www.abcconstruction.lk', '0112345678', 'Colombo,Gampaha', '$2y$10$ehxhVNCL2nGx.e6FR/0jI.yCiIF.eu2cm8/fu80lilEF72Ql.3tbW', 'Leading construction company in Sri Lanka', 4.5),
('XYZ Repairs Co', 'Repair Services', 'REG002', 'TAX002', '456 Galle Road, Dehiwala', 'contact@xyzrepairs.lk', 'www.xyzrepairs.lk', '0117654321', 'Colombo,Kalutara', '$2y$10$ehxhVNCL2nGx.e6FR/0jI.yCiIF.eu2cm8/fu80lilEF72Ql.3tbW', 'Professional repair services', 4.2),
('BuildMax Solutions', 'Construction,Renovation', 'REG003', 'TAX003', '789 Kandy Road, Kadawatha', 'info@buildmax.lk', 'www.buildmax.lk', '0112223344', 'Gampaha,Colombo', '$2y$10$ehxhVNCL2nGx.e6FR/0jI.yCiIF.eu2cm8/fu80lilEF72Ql.3tbW', 'Your trusted building partner', 4.7);

-- Insert sample repairers
INSERT INTO Repairer (f_name, l_name, email, password, phoneNumber, about, profilePicture, ratings, completedJobsCount, districts, availability, category_id) VALUES
('John', 'Silva', 'john.silva@email.com', '$2y$10$ehxhVNCL2nGx.e6FR/0jI.yCiIF.eu2cm8/fu80lilEF72Ql.3tbW', '0771234567', 'Experienced plumber with 10 years experience', NULL, 4.5, 150, 'Colombo,Gampaha', 'available', 1),
('Sarah', 'Fernando', 'sarah.fernando@email.com', '$2y$10$ehxhVNCL2nGx.e6FR/0jI.yCiIF.eu2cm8/fu80lilEF72Ql.3tbW', '0767654321', 'Expert electrician serving Colombo area', NULL, 4.8, 200, 'Colombo', 'available', 2);

-- Insert sample advertisements
INSERT INTO Advertisement (provider_id, provider_type, title, type, budget, status) VALUES
(1, 'company', 'Premium Home Construction Services', 'banner', 50000.00, 'pending'),
(1, 'company', 'Special Discount on Renovation Projects', 'featured', 35000.00, 'approved'),
(2, 'company', 'Emergency Repair Services Available 24/7', 'sponsored', 25000.00, 'pending'),
(3, 'company', 'Quality Building Materials & Construction', 'banner', 45000.00, 'rejected'),
(3, 'company', 'Spring Sale on Home Improvement', 'featured', 30000.00, 'active'),
(1, 'repairer', 'Expert Plumbing Services - John Silva', 'sponsored', 15000.00, 'approved'),
(2, 'repairer', 'Professional Electrical Work', 'banner', 20000.00, 'pending'),
(1, 'company', 'New Year Construction Packages', 'featured', 55000.00, 'pending'),
(2, 'company', 'Fast & Reliable Repair Solutions', 'sponsored', 28000.00, 'approved'),
(3, 'company', 'BuildMax - Your Dream Home Partner', 'banner', 60000.00, 'active');

-- Insert sample ad schedules for active/approved ads
INSERT INTO AdSchedule (ad_id, start_date, end_date, start_time, end_time) VALUES
(2, '2025-01-01', '2025-03-31', '00:00:00', '23:59:59'),
(5, '2025-02-01', '2025-04-30', '00:00:00', '23:59:59'),
(6, '2025-01-15', '2025-03-15', '08:00:00', '18:00:00'),
(9, '2025-02-01', '2025-03-31', '00:00:00', '23:59:59'),
(10, '2025-01-01', '2025-12-31', '00:00:00', '23:59:59');

-- Display inserted data
SELECT 'Sample data inserted successfully!' as Message;
SELECT COUNT(*) as 'Total Advertisements' FROM Advertisement;
