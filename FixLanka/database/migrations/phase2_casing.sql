-- Phase 2 Migration: Change CamelCase to snake_case

ALTER TABLE `user` CHANGE COLUMN `profilePicture` `profile_picture` varchar(500) DEFAULT NULL;
ALTER TABLE `repairer` CHANGE COLUMN `profilePicture` `profile_picture` varchar(500) DEFAULT NULL;

ALTER TABLE `jobrequest` CHANGE COLUMN `dateCreated` `created_at` timestamp NOT NULL DEFAULT current_timestamp();
ALTER TABLE `repairer` CHANGE COLUMN `dateJoined` `joined_at` timestamp NOT NULL DEFAULT current_timestamp();
ALTER TABLE `repairer` CHANGE COLUMN `completedJobsCount` `completed_jobs_count` int(11) DEFAULT 0;
