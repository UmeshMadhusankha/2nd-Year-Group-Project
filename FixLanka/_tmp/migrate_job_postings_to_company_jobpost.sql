-- Migration helper: move away from `job_postings` to `companyjobpost`
-- Use ONLY if your existing database still has `job_postings`.
-- Read each section and choose the one that matches your current DB state.

-- ---------------------------------------------------------------------
-- A) If you have `job_postings` and you do NOT have `companyjobpost`:
--    (Recommended) Rename the table.
--    MySQL will automatically update foreign key references on RENAME.
-- ---------------------------------------------------------------------
-- RENAME TABLE `job_postings` TO `companyjobpost`;

-- ---------------------------------------------------------------------
-- B) If you already have BOTH tables and want to move data:
--    Assumes both tables share the same columns.
-- ---------------------------------------------------------------------
-- 1) Optional: check row counts
-- SELECT 'job_postings' AS tbl, COUNT(*) AS rows FROM job_postings
-- UNION ALL
-- SELECT 'companyjobpost' AS tbl, COUNT(*) AS rows FROM companyjobpost;

-- 2) Copy rows (choose column list to be safe if schema differs)
-- INSERT INTO companyjobpost (
--   posting_id, company_id, title, category, category_id,
--   employment_type, description, requirements, min_experience,
--   priority_level, min_budget, max_budget, location, location_id,
--   location_requirements, required_skills, application_deadline,
--   status, created_at, updated_at
-- )
-- SELECT
--   posting_id, company_id, title, category, category_id,
--   employment_type, description, requirements, min_experience,
--   priority_level, min_budget, max_budget, location, location_id,
--   location_requirements, required_skills, application_deadline,
--   status, created_at, updated_at
-- FROM job_postings;

-- 3) Drop the old table (ONLY after confirming the app works)
-- DROP TABLE job_postings;

-- ---------------------------------------------------------------------
-- C) Quick verification
-- ---------------------------------------------------------------------
-- SELECT posting_id, company_id, title, application_deadline, priority_level
-- FROM companyjobpost
-- ORDER BY posting_id DESC
-- LIMIT 5;
