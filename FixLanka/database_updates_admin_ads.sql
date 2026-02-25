-- =====================================================
-- Admin Advertisement Review - Database Updates
-- Run this in phpMyAdmin after selecting fix_lanka database
-- =====================================================

USE fix_lanka;

-- Step 1: Update Advertisement table to support expanded statuses
ALTER TABLE Advertisement 
MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'active', 'inactive', 'suspended', 'scheduled', 'paused') 
DEFAULT 'pending';

-- Step 2: Add admin_notes column to Advertisement table (if not exists)
ALTER TABLE Advertisement 
ADD COLUMN IF NOT EXISTS admin_notes TEXT NULL AFTER reviewed_at;

-- Step 3: Add moderator_notes column to Advertisement table (if not exists)
ALTER TABLE Advertisement 
ADD COLUMN IF NOT EXISTS moderator_notes TEXT NULL AFTER admin_notes;

-- Step 4: Add rejection_reason column (if not exists)
ALTER TABLE Advertisement 
ADD COLUMN IF NOT EXISTS rejection_reason TEXT NULL AFTER moderator_notes;

-- Step 5: Create ad_status_history table
CREATE TABLE IF NOT EXISTS ad_status_history (
    history_id INT PRIMARY KEY AUTO_INCREMENT,
    ad_id INT NOT NULL,
    old_status ENUM('pending', 'approved', 'rejected', 'active', 'inactive', 'suspended', 'scheduled', 'paused') NOT NULL,
    new_status ENUM('pending', 'approved', 'rejected', 'active', 'inactive', 'suspended', 'scheduled', 'paused') NOT NULL,
    changed_by_role ENUM('admin', 'moderator', 'system') NOT NULL,
    changed_by_id VARCHAR(100) NOT NULL,
    changed_by_name VARCHAR(255) NOT NULL,
    reason TEXT NULL,
    is_override BOOLEAN DEFAULT FALSE,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ad_id (ad_id),
    INDEX idx_changed_at (changed_at),
    INDEX idx_changed_by (changed_by_role, changed_by_id),
    FOREIGN KEY (ad_id) REFERENCES Advertisement(ad_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Step 6: Add admin_reviewed_by and admin_reviewed_at columns
ALTER TABLE Advertisement 
ADD COLUMN IF NOT EXISTS admin_reviewed_by VARCHAR(100) NULL AFTER reviewed_at;

ALTER TABLE Advertisement 
ADD COLUMN IF NOT EXISTS admin_reviewed_at TIMESTAMP NULL AFTER admin_reviewed_by;

-- Step 7: Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_admin_reviewed ON Advertisement(admin_reviewed_by, admin_reviewed_at);
CREATE INDEX IF NOT EXISTS idx_status_date ON Advertisement(status, submission_date);

--  Success Message
SELECT ' Database updated successfully! Admin Advertisement Review backend is ready.' as Status;
