-- ============================================================
-- Migration: Split labour/material units per milestone + extra amount
-- Adds fields needed by the Projects "Submit Proof" completion form.
-- Run this once against your fix_lanka database.
-- ============================================================

ALTER TABLE `contract_milestone`
  ADD COLUMN `is_non_paying` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 if this milestone is non-paying (inspection / no measurable units)' AFTER `actual_amount`,
  ADD COLUMN `actual_labor_quantity` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Actual labour units submitted by company' AFTER `is_non_paying`,
  ADD COLUMN `actual_material_quantity` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Actual material units submitted by company' AFTER `actual_labor_quantity`,
  ADD COLUMN `actual_material_unit_rate` DECIMAL(12,2) NULL DEFAULT NULL COMMENT 'Actual material unit rate submitted by company (optional variation)' AFTER `actual_material_quantity`,
  ADD COLUMN `actual_extra_amount` DECIMAL(12,2) NULL DEFAULT NULL COMMENT 'Additional amount outside labour/material for this milestone' AFTER `actual_material_unit_rate`;
