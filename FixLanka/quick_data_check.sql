-- Quick Database Check and Data Insert
-- Run this in phpMyAdmin to verify and populate data

USE fix_lanka;

-- Check if tables exist
SHOW TABLES;

-- Check current data counts
SELECT 'Repairer Count:' as info, COUNT(*) as count FROM Repairer;
SELECT 'Company Count:' as info, COUNT(*) as count FROM Company;
SELECT 'Category Count:' as info, COUNT(*) as count FROM Category;

-- If no data, insert sample data below:

-- Insert categories if they don't exist
INSERT IGNORE INTO Category (name) VALUES
('Plumbing'),
('Electrical'),
('Cleaning'),
('HVAC'),
('Carpentry'),
('Painting'),
('Appliance Repair'),
('Roofing'),
('Landscaping'),
('Pest Control'),
('Home Security'),
('Interior Design'),
('Flooring'),
('Masonry'),
('Welding'),
('Glass & Mirror'),
('Tile Work'),
('Drywall'),
('Insulation'),
('Window Installation');

-- Insert sample repairers (password is 'password')
INSERT INTO Repairer (f_name, l_name, email, password, phoneNumber, about, ratings, completedJobsCount, districts, availability, category_id) VALUES
('Kamal', 'Silva', 'kamal.silva@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0771234567', 'Certified electrician with 15+ years of experience', 4.9, 156, 'Colombo, Dehiwala, Mount Lavinia', 'available', 2),
('Nimal', 'Perera', 'nimal.perera@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0772345678', 'Licensed plumber offering 24/7 emergency services', 4.8, 243, 'Colombo, Nugegoda, Maharagama', 'available', 1),
('Saman', 'Fernando', 'saman.fernando@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0773456789', 'Air conditioning and heating specialist', 4.7, 89, 'Colombo, Borella, Maradana', 'available', 4),
('Amara', 'Jayasinghe', 'amara.j@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0774567890', 'Professional cleaning service with eco-friendly products', 5.0, 178, 'Colombo, Wellawatta, Bambalapitiya', 'available', 3),
('Chaminda', 'Rathnayake', 'chaminda.r@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0775678901', 'Skilled carpenter specializing in custom furniture', 4.6, 134, 'Colombo, Rajagiriya, Kotte', 'available', 5),
('Lakshmi', 'Wijeratne', 'lakshmi.w@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0776789012', 'Professional painter with attention to detail', 4.9, 267, 'Colombo, Kiribathgoda, Kadawatha', 'available', 6),
('Roshan', 'Mendis', 'roshan.m@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0777890123', 'Expert in appliance repairs', 4.5, 98, 'Colombo, Piliyandala, Moratuwa', 'available', 7),
('Priya', 'Gunasekara', 'priya.g@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0778901234', 'Professional gardener', 4.8, 156, 'Colombo, Battaramulla, Thalawathugoda', 'available', 9)
ON DUPLICATE KEY UPDATE repairer_id=repairer_id;

-- Insert sample companies
INSERT INTO Company (name, registration_no, address, email, contact_no, rating) VALUES
('HomeFix Solutions Ltd', 'PV12345', 'No. 123, Galle Road, Colombo 03', 'info@homefixsolutions.lk', '0112345678', 4.8),
('ElectroTech Services', 'PV23456', 'No. 456, Duplication Road, Colombo 04', 'contact@electrotech.lk', '0112456789', 4.7),
('CleanPro Lanka', 'PV34567', 'No. 789, Baseline Road, Colombo 09', 'hello@cleanpro.lk', '0112567890', 4.9),
('AirCool HVAC', 'PV45678', 'No. 321, High Level Road, Nugegoda', 'service@aircool.lk', '0112678901', 4.6),
('BuildMaster Construction', 'PV56789', 'No. 654, Nawala Road, Rajagiriya', 'info@buildmaster.lk', '0112789012', 4.8)
ON DUPLICATE KEY UPDATE company_id=company_id;

-- Verify data was inserted
SELECT 'After Insert - Repairer Count:' as info, COUNT(*) as count FROM Repairer;
SELECT 'After Insert - Company Count:' as info, COUNT(*) as count FROM Company;

-- Show sample data
SELECT repairer_id, f_name, l_name, ratings, availability FROM Repairer LIMIT 5;
SELECT company_id, name, rating FROM Company LIMIT 5;
