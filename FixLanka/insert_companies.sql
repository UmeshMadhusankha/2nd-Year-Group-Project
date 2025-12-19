-- Insert Company Data for Testing
-- Run this in phpMyAdmin SQL tab

USE fix_lanka;

-- First, let's check what's already there
SELECT 'Current Company Count:' as info, COUNT(*) as count FROM Company;

-- Insert companies (will skip if email already exists)
INSERT INTO Company (name, registration_no, address, email, contact_no, rating) VALUES
('HomeFix Solutions Ltd', 'PV12345', 'No. 123, Galle Road, Colombo 03', 'info@homefixsolutions.lk', '0112345678', 4.8),
('ElectroTech Services', 'PV23456', 'No. 456, Duplication Road, Colombo 04', 'contact@electrotech.lk', '0112456789', 4.7),
('CleanPro Lanka', 'PV34567', 'No. 789, Baseline Road, Colombo 09', 'hello@cleanpro.lk', '0112567890', 4.9),
('AirCool HVAC', 'PV45678', 'No. 321, High Level Road, Nugegoda', 'service@aircool.lk', '0112678901', 4.6),
('BuildMaster Construction', 'PV56789', 'No. 654, Nawala Road, Rajagiriya', 'info@buildmaster.lk', '0112789012', 4.8),
('PlumbPro Services', 'PV67890', 'No. 234, Kandy Road, Kiribathgoda', 'contact@plumbpro.lk', '0112890123', 4.7),
('SparkElectric Ltd', 'PV78901', 'No. 567, Negombo Road, Wattala', 'info@sparkelectric.lk', '0112901234', 4.5),
('GreenClean Lanka', 'PV89012', 'No. 890, Galle Road, Wellawatta', 'hello@greenclean.lk', '0113012345', 4.9),
('CoolAir Solutions', 'PV90123', 'No. 123, Main Street, Maharagama', 'service@coolair.lk', '0113123456', 4.6),
('HomeRepair Masters', 'PV01234', 'No. 456, Horana Road, Panadura', 'contact@homerepair.lk', '0113234567', 4.8)
ON DUPLICATE KEY UPDATE company_id=company_id;

-- Verify insertion
SELECT 'After Insert - Company Count:' as info, COUNT(*) as count FROM Company;

-- Show the companies
SELECT company_id, name, rating, email FROM Company ORDER BY rating DESC;
