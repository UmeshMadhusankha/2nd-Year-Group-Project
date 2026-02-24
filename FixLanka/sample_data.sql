-- Sample Data for Testing Fix Lanka Service Providers
-- Run this after creating the database using create_database.sql

USE fix_lanka;

-- Insert sample categories (skip if they already exist)
INSERT IGNORE INTO Category (name) VALUES
('Plumbing'),
('Electrical'),
('Cleaning'),
('HVAC'),
('Carpentry');

-- Clear existing sample data (optional - remove if you want to keep existing data)
DELETE FROM Repairer WHERE email LIKE '%@example.com';
DELETE FROM Company WHERE email LIKE '%@example.lk' OR email LIKE '%@example.com';

-- Insert sample repairers (Individual service providers)
INSERT INTO Repairer (f_name, l_name, email, password, phoneNumber, about, ratings, completedJobsCount, districts, availability, category_id) VALUES
('Kamal', 'Silva', 'kamal.silva@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0771234567', 'Certified electrician with 15+ years of experience. Specializes in residential and commercial electrical work.', 4.9, 156, 'Colombo, Dehiwala, Mount Lavinia', 'available', 2),

('Nimal', 'Perera', 'nimal.perera@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0772345678', 'Licensed plumber offering 24/7 emergency services. Expert in pipe repairs and bathroom installations.', 4.8, 243, 'Colombo, Nugegoda, Maharagama', 'available', 1),

('Saman', 'Fernando', 'saman.fernando@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0773456789', 'Air conditioning and heating specialist. Quick diagnostics and reliable repair services.', 4.7, 89, 'Colombo, Borella, Maradana', 'available', 4),

('Amara', 'Jayasinghe', 'amara.j@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0774567890', 'Professional cleaning service with eco-friendly products. Trusted by 200+ families.', 5.0, 178, 'Colombo, Wellawatta, Bambalapitiya', 'available', 3),

('Chaminda', 'Rathnayake', 'chaminda.r@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0775678901', 'Skilled carpenter specializing in custom furniture and home repairs. Quality craftsmanship guaranteed.', 4.6, 134, 'Colombo, Rajagiriya, Kotte', 'available', 5),

('Lakshmi', 'Wijeratne', 'lakshmi.w@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0776789012', 'Professional painter with attention to detail. Transforms spaces with quality finishes.', 4.9, 267, 'Colombo, Kiribathgoda, Kadawatha', 'available', 5),

('Roshan', 'Mendis', 'roshan.m@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0777890123', 'Expert in washing machine, refrigerator, and microwave repairs. Same-day service available.', 4.5, 98, 'Colombo, Piliyandala, Moratuwa', 'busy', 2),

('Priya', 'Gunasekara', 'priya.g@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0778901234', 'Professional gardener offering lawn care, pruning, and landscape design services.', 4.8, 156, 'Colombo, Battaramulla, Thalawathugoda', 'available', 3);

-- Insert sample companies
INSERT INTO Company (name, registration_no, address, email, contact_no, rating) VALUES
('HomeFix Solutions Ltd', 'PV12345', 'No. 123, Galle Road, Colombo 03', 'info@homefixsolutions.lk', '0112345678', 4.8),

('ElectroTech Services', 'PV23456', 'No. 456, Duplication Road, Colombo 04', 'contact@electrotech.lk', '0112456789', 4.7),

('CleanPro Lanka', 'PV34567', 'No. 789, Baseline Road, Colombo 09', 'hello@cleanpro.lk', '0112567890', 4.9),

('AirCool HVAC', 'PV45678', 'No. 321, High Level Road, Nugegoda', 'service@aircool.lk', '0112678901', 4.6),

('BuildMaster Construction', 'PV56789', 'No. 654, Nawala Road, Rajagiriya', 'info@buildmaster.lk', '0112789012', 4.8);

-- Note: Password is 'password' hashed with bcrypt
-- You'll need to update these with proper hashed passwords when implementing authentication

SELECT 'Sample data inserted successfully!' as message;
