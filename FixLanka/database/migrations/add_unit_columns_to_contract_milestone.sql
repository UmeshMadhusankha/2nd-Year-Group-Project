-- ============================================================
-- Migration: Dynamic Unit-Based Milestone Billing
-- Adds quantity/rate fields to contract_milestone table
-- Run this once against your fix_lanka database
-- ============================================================

-- Add new columns (safe: uses IF NOT EXISTS guard via separate column checks)
ALTER TABLE `contract_milestone`
    ADD COLUMN IF NOT EXISTS `unit_label`         VARCHAR(50)     NULL    DEFAULT NULL COMMENT 'e.g. hours, sqft, units',
    ADD COLUMN IF NOT EXISTS `unit_rate`          DECIMAL(12,2)   NULL    DEFAULT NULL COMMENT 'Price per unit (agreed in contract)',
    ADD COLUMN IF NOT EXISTS `estimated_quantity` DECIMAL(10,2)   NULL    DEFAULT NULL COMMENT 'Quantity expected (from quotation)',
    ADD COLUMN IF NOT EXISTS `actual_quantity`    DECIMAL(10,2)   NULL    DEFAULT NULL COMMENT 'Actual units submitted by company',
    ADD COLUMN IF NOT EXISTS `actual_amount`      DECIMAL(12,2)   NULL    DEFAULT NULL COMMENT 'actual_quantity * unit_rate (calculated on submission)',
    ADD COLUMN IF NOT EXISTS `approved_at`        DATETIME        NULL    DEFAULT NULL COMMENT 'When customer approved this milestone',
    ADD COLUMN IF NOT EXISTS `proof_files`        TEXT            NULL    DEFAULT NULL COMMENT 'JSON array of uploaded proof file paths',
    ADD COLUMN IF NOT EXISTS `comments`           TEXT            NULL    DEFAULT NULL;

