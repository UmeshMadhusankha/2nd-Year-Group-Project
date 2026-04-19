-- Repairer settings table
CREATE TABLE `repairersettings` (
  `setting_id` int(11) NOT NULL,
  `repairer_id` int(11) NOT NULL,
  `email_job_requests` tinyint(1) DEFAULT 1,
  `email_quote_responses` tinyint(1) DEFAULT 1,
  `email_payment_notifications` tinyint(1) DEFAULT 1,
  `email_reviews_ratings` tinyint(1) DEFAULT 1,
  `email_weekly_summary` tinyint(1) DEFAULT 0,
  `push_browser_notifications` tinyint(1) DEFAULT 0,
  `push_sound_alerts` tinyint(1) DEFAULT 1,
  `privacy_profile_visibility` tinyint(1) DEFAULT 1,
  `privacy_show_contact` tinyint(1) DEFAULT 0,
  `privacy_location_sharing` tinyint(1) DEFAULT 1,
  `security_login_alerts` tinyint(1) DEFAULT 1,
  `security_session_timeout` varchar(20) DEFAULT '30 minutes',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `repairersettings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `unique_repairer` (`repairer_id`);

ALTER TABLE `repairersettings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `repairersettings`
  ADD CONSTRAINT `repairersettings_ibfk_1` FOREIGN KEY (`repairer_id`) REFERENCES `repairer` (`repairer_id`) ON DELETE CASCADE;
