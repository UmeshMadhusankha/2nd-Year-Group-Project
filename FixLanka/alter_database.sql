-- =====================================================
-- Database Migration Script for JobRequest Table
-- Version 1.1.0 - Updates to JobRequest structure
-- =====================================================
-- Run this script if you already have the fix_lanka database created
-- This will alter the existing JobRequest table to add new fields

USE fix_lanka;

-- Add title field
ALTER TABLE JobRequest 
ADD COLUMN title VARCHAR(255) NOT NULL AFTER category_id;

-- Add district field
ALTER TABLE JobRequest 
ADD COLUMN district VARCHAR(100) NOT NULL AFTER status;

-- Rename location to address
ALTER TABLE JobRequest 
CHANGE COLUMN location address TEXT NOT NULL;

-- Modify service_provider_type to allow 'both' option
ALTER TABLE JobRequest 
MODIFY COLUMN service_provider_type VARCHAR(50) NOT NULL;

-- Update urgency enum to only medium and urgent
ALTER TABLE JobRequest 
MODIFY COLUMN urgency ENUM('medium', 'urgent') DEFAULT 'medium';

-- Add finish_date field
ALTER TABLE JobRequest 
ADD COLUMN finish_date DATE NOT NULL AFTER urgency;

-- Add index for district
ALTER TABLE JobRequest 
ADD INDEX idx_district (district);

-- Update any existing records with 'low' or 'high' urgency to 'medium'
UPDATE JobRequest 
SET urgency = 'medium' 
WHERE urgency NOT IN ('medium', 'urgent');

-- =====================================================
-- Migration Complete
-- =====================================================
-- Note: You may need to update existing records' title, district, 
-- and finish_date fields with appropriate values if you have data
