-- ============================================================
-- Migration: Remove legacy auto-generated unit-billing milestones
--
-- Removes the old "Labour" and "Materials" milestones that were
-- previously added as separate phases for unit-priced contracts.
-- Unit quantities are now entered inside each milestone completion.
--
-- Safe-guards:
-- - Only deletes rows matching the exact legacy description templates.
-- ============================================================

DELETE FROM `contract_milestone`
WHERE (`title` = 'Labour' OR `title` = 'Materials')
  AND (
    `description` = 'Company submits actual labour units after completion; customer verifies before payment is finalized.'
    OR `description` = 'Company submits actual material units and any material price variation; customer verifies before payment is finalized.'
  );

-- Optional: if you rely on Contract.total_milestones for UI stats, you can resync it.
-- (Not strictly required for the Projects timeline rendering.)
UPDATE `contract` c
SET c.total_milestones = (
    SELECT COUNT(*)
    FROM `contract_milestone` m
    WHERE m.contract_id = c.contract_id
)
WHERE EXISTS (
    SELECT 1
    FROM `contract_milestone` m
    WHERE m.contract_id = c.contract_id
);
