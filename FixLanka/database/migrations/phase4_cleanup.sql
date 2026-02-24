-- Phase 4 Workforce Schema Cleanup
-- Drops the obsolete legacy tables that were replaced by the new Workforce schema tables

DROP TABLE IF EXISTS repairerassignment;
DROP TABLE IF EXISTS companyemployee;
DROP TABLE IF EXISTS repairerapplication;
DROP TABLE IF EXISTS companyjobpost;

SELECT 'Cleanup of obsolete Phase 4 tables completed successfully.' AS status;
