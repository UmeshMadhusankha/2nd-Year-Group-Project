-- Add status column to JobRequest safely

DELIMITER //

CREATE PROCEDURE AddStatusToJobRequest()
BEGIN
    IF NOT EXISTS (
        SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'JobRequest' 
        AND COLUMN_NAME = 'status'
    ) THEN
        ALTER TABLE `JobRequest` 
        ADD COLUMN `status` ENUM('Open', 'Assigned', 'Completed', 'Cancelled', 'Expired') 
        NOT NULL DEFAULT 'Open' 
        COMMENT 'Current status of the job request' 
        AFTER `created_at`;
        
        -- Add index for performance
        ALTER TABLE `JobRequest` ADD INDEX `idx_status` (`status`);
        
        SELECT 'Column added successfully' AS result;
    ELSE
        SELECT 'Column already exists' AS result;
    END IF;
END //

DELIMITER ;

CALL AddStatusToJobRequest();
DROP PROCEDURE AddStatusToJobRequest;
