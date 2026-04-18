-- Adds support for material unit price variation at submission time.
-- Stores the actual unit rate used for billing (optional override of agreed unit_rate).

ALTER TABLE `contract_milestone`
    ADD COLUMN IF NOT EXISTS `actual_unit_rate` DECIMAL(12,2) NULL DEFAULT NULL COMMENT 'Actual unit rate submitted by company (e.g., material price variation)' AFTER `unit_rate`;

-- Optional: Update comment for actual_amount to reflect rate override
-- (MySQL does not support IF NOT EXISTS for MODIFY COLUMN in all versions; keep as a note).
