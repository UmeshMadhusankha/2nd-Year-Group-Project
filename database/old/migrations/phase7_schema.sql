-- Phase 7: Milestone Management Schema Enhancements

-- Add columns to contract_milestone for tracking completion and approval
ALTER TABLE contract_milestone
ADD COLUMN completed_at DATETIME NULL,
ADD COLUMN approved_at DATETIME NULL,
ADD COLUMN proof_files TEXT NULL, -- JSON or comma-separated URLs
ADD COLUMN comments TEXT NULL; -- For rejection reasons or other notes

-- Ensure status column can handle new states if not already compatible (enum check or just standard VARCHAR)
-- Common statuses: 'pending', 'in_progress', 'completed' (by repairer), 'approved' (by customer), 'rejected'
