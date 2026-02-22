-- Soft Deletes Migration for Data Governance
-- Creates is_deleted flag and hardens critical financial tables against accidental deletion

-- 1. Add Soft Delete Flags
ALTER TABLE `user` ADD COLUMN `is_deleted` TINYINT(1) DEFAULT 0;
ALTER TABLE `company` ADD COLUMN `is_deleted` TINYINT(1) DEFAULT 0;
ALTER TABLE `repairer` ADD COLUMN `is_deleted` TINYINT(1) DEFAULT 0;

-- 2. Protect Escrow Wallet and Financial Transactions from CASCADE deletion
ALTER TABLE `escrow_wallet` DROP FOREIGN KEY `escrow_wallet_user_fk`;
ALTER TABLE `escrow_wallet` ADD CONSTRAINT `escrow_wallet_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT;

ALTER TABLE `escrow_wallet` DROP FOREIGN KEY `escrow_wallet_company_fk`;
ALTER TABLE `escrow_wallet` ADD CONSTRAINT `escrow_wallet_company_fk` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE RESTRICT;

-- Protect Contracts from being deleted when a company is deleted
ALTER TABLE `contract` DROP FOREIGN KEY `contract_ibfk_2`;
ALTER TABLE `contract` ADD CONSTRAINT `contract_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE RESTRICT;
