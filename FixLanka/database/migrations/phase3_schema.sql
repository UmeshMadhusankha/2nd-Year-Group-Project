-- Phase 3: Budget Flexibility System
-- Adds table for structured budget adjustment requests

CREATE TABLE IF NOT EXISTS `contract_budget_adjustments` (
  `adjustment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `contract_id` INT NOT NULL,
  
  -- Adjustment details
  `original_amount` DECIMAL(12,2) NOT NULL,
  `requested_amount` DECIMAL(12,2) NOT NULL,
  `adjustment_amount` DECIMAL(12,2) NOT NULL, -- difference
  `adjustment_percentage` DECIMAL(5,2) NOT NULL,
  
  -- Reason
  `reason` TEXT NOT NULL,
  `justification` TEXT NOT NULL,
  `supporting_documents` TEXT NULL, -- JSON array of document paths
  
  -- Status
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `requested_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `requested_by` INT NOT NULL, -- company user_id
  
  `reviewed_at` DATETIME NULL,
  `reviewed_by` INT NULL, -- customer user_id
  `review_notes` TEXT NULL,
  
  `approved_at` DATETIME NULL,
  `rejected_at` DATETIME NULL,
  `rejection_reason` TEXT NULL,
  
  -- Timestamps
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
  
  INDEX `idx_contract` (`contract_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
