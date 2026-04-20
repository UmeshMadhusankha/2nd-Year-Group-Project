-- Update Company Profile Schema
-- This script adds the missing columns for the enhanced company profile features

ALTER TABLE `company`
ADD COLUMN IF NOT EXISTS `logo` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `established_year` int(11) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `city` varchar(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `province` varchar(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `postal_code` varchar(20) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `alternate_phone` varchar(20) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `whatsapp` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `facebook` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `instagram` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `linkedin` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `twitter` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `skills` text DEFAULT NULL;
