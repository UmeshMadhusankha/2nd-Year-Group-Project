-- Migration: add structured JSON columns for contract change requests
-- Safe to run once; if columns already exist, MySQL will error.

ALTER TABLE `contract_change_requests`
  ADD COLUMN `proposed_changes_json` LONGTEXT NULL AFTER `request_text`,
  ADD COLUMN `original_snapshot_json` LONGTEXT NULL AFTER `proposed_changes_json`;
