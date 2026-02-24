-- =====================================================
-- SAFE Moderator Table Update Script
-- Handles existing columns gracefully
-- =====================================================

USE fix_lanka;

-- Drop procedure if exists
DROP PROCEDURE IF EXISTS SafeModeratorUpdate;

DELIMITER //

CREATE PROCEDURE SafeModeratorUpdate()
BEGIN
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
    
    -- Try to add status column (will fail silently if exists)
    ALTER TABLE Moderator 
    ADD COLUMN status ENUM('Active', 'Inactive') DEFAULT 'Active' AFTER assigned_section;
    
    -- Try to add last_login column
    ALTER TABLE Moderator 
    ADD COLUMN last_login DATETIME NULL AFTER status;
    
    -- Try to add updated_at column
    ALTER TABLE Moderator 
    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER last_login;
    
END//

DELIMITER ;

-- Execute the procedure
CALL SafeModeratorUpdate();

-- Clean up
DROP PROCEDURE SafeModeratorUpdate;

-- Update existing moderators to have Active status (if column was just added)
UPDATE Moderator SET status = 'Active' WHERE status IS NULL;

-- Verify the structure
DESCRIBE Moderator;

SELECT ' Moderator table update completed successfully!' as Status;
