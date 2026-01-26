# 🚀 **FIXLANKA IMPLEMENTATION PLAN**

## 📋 **Document Overview**

This document provides a complete, phase-by-phase implementation plan for the FixLanka quotation-to-project-completion system with all 6 business logic solutions.

**Target:** Production-ready system with all features integrated
**Approach:** Incremental development with testing at each phase
**Timeline:** 12-16 weeks (3-4 months) for complete implementation

---

## ⚠️ **IMPORTANT: Building on Existing System**

### **🔍 Your Current System Status**

After analyzing your existing codebase, here's what you **ALREADY HAVE**:

✅ **Database Tables (Basic Structure):**
- `user` - Customer accounts
- `company` - Company accounts
- `jobrequest` - Service requests (your "requests")
- `companyquotation` - Quotations from companies
- `project` - Projects 
- `contract` - Basic contracts (connected to projects)
- `milestone` - Milestones
- `milestonepayment` - Milestone payments

✅ **API Endpoints:**
- `api/contracts.php` - Contract operations
- `api/projects.php` - Project management
- `api/company-quotes.php` - Quotation handling
- `api/payments.php` - Payment processing
- `api/notifications.php` - Notification system

✅ **Features Already Working:**
- User/Company authentication
- Service request posting
- Quotation submission
- Basic project tracking
- Advertisement system
- Support ticket system
- Billing/subscription system
- Settings management
- Session management

---

### **🎯 What This Plan Does**

**This is an ENHANCEMENT plan, NOT a complete rebuild!**

The plan will:
1. **Keep your existing tables** (user, company, jobrequest, etc.)
2. **Enhance existing tables** (add new columns to companyquotation, contract, etc.)
3. **Add new tables** (for chat, escrow, timeline, etc.)
4. **Build on existing API endpoints** (extend api/contracts.php, api/payments.php)
5. **Add new features** (24-hour undo, chat, escrow)
6. **Keep everything working** (backward compatibility)

---

### **📊 Mapping: Current System → New Features**

| Your Current Table | Maps To New Feature | Action Required |
|-------------------|---------------------|-----------------|
| `jobrequest` | Service Request | ✅ Keep as-is, add columns for budget type |
| `companyquotation` | Quotation | ✅ Add payment_method, budget_type columns |
| `contract` | Contract | ✅ Add undo_deadline, chat_active, escrow columns |
| `milestone` | Milestone | ✅ Add submission, approval workflow columns |
| `milestonepayment` | Payment | ✅ Add escrow tracking columns |
| `project` | Project | ✅ Keep as-is, already good |
| *(New)* | Contract Chat | ❌ Create new table: `contract_chats` |
| *(New)* | Escrow Accounts | ❌ Create new table: `escrow_accounts` |
| *(New)* | Timeline Events | ❌ Create new table: `contract_timeline` |
| *(New)* | Budget Adjustments | ❌ Create new table: `contract_budget_adjustments` |
| *(New)* | Time Logs | ❌ Create new table: `contract_time_logs` |

---

### **🔄 Implementation Approach**

**Option 1: Enhancement (Recommended) ✅**
- Keep all existing code working
- Add new columns to existing tables
- Create only new tables needed
- Extend existing API endpoints
- Add new features incrementally
- **Timeline: 12-16 weeks**

**Option 2: Complete Rebuild ❌ (NOT Recommended)**
- Would require rewriting everything
- High risk of breaking existing features
- Much longer timeline (6+ months)
- Not necessary since you have good foundation

---

### **✅ We're Going with Option 1: Enhancement**

**What this means:**
- Your existing features continue working
- We add new columns to your tables
- We create only new tables where needed
- We enhance your existing API files
- We add new UI on top of existing pages
- **No breaking changes!**

---

## 🎯 **Implementation Strategy**

### **Core Principles**

1. **Incremental Development**: Build and test one feature at a time
2. **Backward Compatibility**: Don't break existing functionality
3. **Database First**: Schema changes before application code
4. **API Layer**: Build reusable API endpoints
5. **Frontend Last**: UI after backend is stable
6. **Test Everything**: Unit tests, integration tests, user acceptance tests
7. **Documentation**: Update docs as you build

### **Technology Stack Assessment**

**Current Stack (Based on workspace):**
- Backend: PHP
- Database: MySQL
- Frontend: HTML/CSS/JavaScript
- Location: XAMPP (local development)

**Recommended Additions:**
- Testing: PHPUnit for backend tests
- Validation: JavaScript form validation library
- File Upload: Handle images for escrow verification
- Real-time: Consider WebSockets for chat (or long polling)
- Payment: Escrow account system (financial integration)

---

## 📊 **Implementation Phases Overview**

```
PHASE 1: Database Schema & Core Models (Week 1-2)
    ↓
PHASE 2: Contract Creation System (Week 3-4)
    ↓
PHASE 3: Budget Flexibility System (Week 5)
    ↓
PHASE 4: Payment Terms System (Week 6-7)
    ↓
PHASE 5: 24-Hour Undo Window (Week 8)
    ↓
PHASE 6: Chat System (Week 9-10)
    ↓
PHASE 7: Milestone Management (Week 11-12)
    ↓
PHASE 8: Escrow & Payment Protection (Week 13-14)
    ↓
PHASE 9: Notifications & UI Polish (Week 15)
    ↓
PHASE 10: Testing & Deployment (Week 16)
```

---

# 🔷 **PHASE 1: DATABASE SCHEMA ENHANCEMENT**

**Duration:** Week 1-2  
**Priority:** Critical (Foundation for everything)  
**Complexity:** Medium

## **Objectives**
- **Enhance existing tables** with new columns
- **Create new tables** only where needed
- Build base model classes for new features
- Set up relationships and constraints
- **Keep existing data intact**

---

## **1.1 Enhance Existing Tables**

### **Table Enhancement 1: `companyquotation` (ADD COLUMNS)**

**Current table:** Already exists with labor_cost, material_cost, total_amount, etc.

**ADD these columns:**

```sql
-- Add new columns to existing companyquotation table
ALTER TABLE `companyquotation`
ADD COLUMN `budget_type` ENUM('flexible', 'fixed') NOT NULL DEFAULT 'fixed' AFTER `total_amount`,
ADD COLUMN `budget_min` DECIMAL(10,2) NULL AFTER `budget_type`,
ADD COLUMN `budget_max` DECIMAL(10,2) NULL AFTER `budget_min`,
ADD COLUMN `payment_method` ENUM('milestone', 'upfront_final_50_50', 'upfront_final_30_70', 'after_completion', 'time_material') NOT NULL DEFAULT 'milestone' AFTER `payment_terms`,
ADD COLUMN `pricing_type` ENUM('fixed_price', 'hourly', 'daily') NOT NULL DEFAULT 'fixed_price' AFTER `payment_method`,
ADD COLUMN `hourly_rate` DECIMAL(10,2) NULL AFTER `pricing_type`,
ADD COLUMN `spending_cap_multiplier` DECIMAL(3,2) NULL DEFAULT 1.20 AFTER `hourly_rate`;

-- Update existing quotations to have default values
UPDATE `companyquotation` 
SET budget_type = 'fixed', payment_method = 'milestone', pricing_type = 'fixed_price'
WHERE budget_type IS NULL;
```

**Why:** This adds Issue #1 (budget flexibility) and Issue #2 (payment terms) to quotations.

---

### **Table Enhancement 2: `contract` (ADD COLUMNS)**

**Current table:** Already exists with project_id, total_budget, status, etc.

**ADD these columns:**

```sql
-- Add new columns to existing contract table
ALTER TABLE `contract`
ADD COLUMN `quotation_id` INT NULL AFTER `project_id`,
ADD COLUMN `request_id` INT NULL AFTER `quotation_id`,
ADD COLUMN `user_id` INT NULL AFTER `request_id`,
ADD COLUMN `company_id` INT NULL AFTER `user_id`,

-- Budget information (Issue #1)
ADD COLUMN `budget_type` ENUM('flexible', 'fixed') NOT NULL DEFAULT 'fixed' AFTER `total_budget`,
ADD COLUMN `budget_min` DECIMAL(12,2) NULL AFTER `budget_type`,
ADD COLUMN `budget_max` DECIMAL(12,2) NULL AFTER `budget_min`,
ADD COLUMN `final_amount` DECIMAL(12,2) NULL AFTER `budget_max`,

-- Payment method (Issue #2)
ADD COLUMN `payment_method` ENUM('milestone', 'upfront_final_50_50', 'upfront_final_30_70', 'after_completion', 'time_material') NOT NULL DEFAULT 'milestone' AFTER `final_amount`,
ADD COLUMN `pricing_type` ENUM('fixed_price', 'hourly', 'daily') NOT NULL DEFAULT 'fixed_price' AFTER `payment_method`,
ADD COLUMN `hourly_rate` DECIMAL(10,2) NULL AFTER `pricing_type`,
ADD COLUMN `spending_cap` DECIMAL(12,2) NULL AFTER `hourly_rate`,

-- 24-hour undo (Issue #3)
ADD COLUMN `accepted_at` DATETIME NULL AFTER `contract_date`,
ADD COLUMN `undo_deadline` DATETIME NULL AFTER `accepted_at`,
ADD COLUMN `undo_available` BOOLEAN DEFAULT TRUE AFTER `undo_deadline`,
ADD COLUMN `undone_at` DATETIME NULL AFTER `undo_available`,
ADD COLUMN `undo_reason` TEXT NULL AFTER `undone_at`,
ADD COLUMN `reminded_at` DATETIME NULL AFTER `undo_reason`,

-- Chat (Issue #4)
ADD COLUMN `chat_active` BOOLEAN DEFAULT FALSE AFTER `reminded_at`,
ADD COLUMN `chat_activated_at` DATETIME NULL AFTER `chat_active`,

-- Escrow protection
ADD COLUMN `escrow_enabled` BOOLEAN DEFAULT TRUE AFTER `chat_activated_at`,
ADD COLUMN `escrow_status` ENUM('none', 'holding', 'released', 'refunded') DEFAULT 'none' AFTER `escrow_enabled`,

-- Work verification (for upfront payments)
ADD COLUMN `work_started_verified` BOOLEAN DEFAULT FALSE AFTER `escrow_status`,
ADD COLUMN `work_started_verified_at` DATETIME NULL AFTER `work_started_verified`,
ADD COLUMN `work_start_proof_photo` VARCHAR(255) NULL AFTER `work_started_verified_at`,
ADD COLUMN `work_start_proof_location` VARCHAR(255) NULL AFTER `work_start_proof_photo`,
ADD COLUMN `work_start_submitted_at` DATETIME NULL AFTER `work_start_proof_location`,

-- Progress tracking
ADD COLUMN `progress_percentage` TINYINT DEFAULT 0 AFTER `work_start_submitted_at`,
ADD COLUMN `stage` VARCHAR(100) NULL AFTER `progress_percentage`,

-- Foreign keys
ADD CONSTRAINT `fk_contract_quotation` FOREIGN KEY (`quotation_id`) REFERENCES `companyquotation`(`quotation_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_contract_request` FOREIGN KEY (`request_id`) REFERENCES `jobrequest`(`request_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_contract_user` FOREIGN KEY (`user_id`) REFERENCES `user`(`user_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_contract_company` FOREIGN KEY (`company_id`) REFERENCES `company`(`company_id`) ON DELETE CASCADE;

-- Add indexes
ALTER TABLE `contract`
ADD INDEX `idx_quotation` (`quotation_id`),
ADD INDEX `idx_request` (`request_id`),
ADD INDEX `idx_user` (`user_id`),
ADD INDEX `idx_company` (`company_id`),
ADD INDEX `idx_undo_deadline` (`undo_deadline`),
ADD INDEX `idx_payment_method` (`payment_method`);
```

**Why:** This adds all 6 issues to contract tracking.

---

### **Table Enhancement 3: `milestone` (ADD COLUMNS)**

**Current table:** Already exists with contract_id, description, amount, etc.

**ADD these columns:**

```sql
-- Add new columns to existing milestone table
ALTER TABLE `milestone`
ADD COLUMN `milestone_number` INT NOT NULL DEFAULT 1 AFTER `contract_id`,
ADD COLUMN `milestone_name` VARCHAR(200) NOT NULL DEFAULT 'Milestone' AFTER `milestone_number`,
ADD COLUMN `payment_percentage` DECIMAL(5,2) NULL AFTER `amount`,
ADD COLUMN `duration_days` INT NULL AFTER `due_date`,
ADD COLUMN `start_date` DATE NULL AFTER `duration_days`,
ADD COLUMN `end_date` DATE NULL AFTER `start_date`,
ADD COLUMN `estimated_completion` DATE NULL AFTER `end_date`,
ADD COLUMN `deliverables` TEXT NULL AFTER `agreements`,
ADD COLUMN `dependencies` TEXT NULL AFTER `deliverables`,

-- Two-stage process (Issue #6)
ADD COLUMN `is_active` BOOLEAN DEFAULT FALSE AFTER `status`,
ADD COLUMN `submitted_at` DATETIME NULL AFTER `is_active`,
ADD COLUMN `submitted_by` INT NULL AFTER `submitted_at`,
ADD COLUMN `submission_notes` TEXT NULL AFTER `submitted_by`,
ADD COLUMN `submission_photos` TEXT NULL AFTER `submission_notes`,

-- Review
ADD COLUMN `reviewed_at` DATETIME NULL AFTER `submission_photos`,
ADD COLUMN `reviewed_by` INT NULL AFTER `reviewed_at`,
ADD COLUMN `review_notes` TEXT NULL AFTER `reviewed_by`,
ADD COLUMN `approved_at` DATETIME NULL AFTER `review_notes`,
ADD COLUMN `rejected_at` DATETIME NULL AFTER `approved_at`,
ADD COLUMN `rejection_reason` TEXT NULL AFTER `rejected_at`,

-- Payment tracking
ADD COLUMN `payment_requested_at` DATETIME NULL AFTER `rejection_reason`,
ADD COLUMN `payment_released_at` DATETIME NULL AFTER `payment_requested_at`,
ADD COLUMN `payment_transaction_id` VARCHAR(100) NULL AFTER `payment_released_at`,

-- Escrow
ADD COLUMN `escrow_held_at` DATETIME NULL AFTER `payment_transaction_id`,
ADD COLUMN `escrow_released_at` DATETIME NULL AFTER `escrow_held_at`,

ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `escrow_released_at`,
ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Update status enum to include more states
ALTER TABLE `milestone` 
MODIFY COLUMN `status` ENUM('pending','active','submitted','approved','rejected','in_progress','completed','overdue','paid') DEFAULT 'pending';

-- Add indexes
ALTER TABLE `milestone`
ADD INDEX `idx_milestone_number` (`contract_id`, `milestone_number`);
```

**Why:** This adds the two-stage milestone process (Issue #6) and approval workflow.

---

### **Table Enhancement 4: `milestonepayment` (ADD COLUMNS)**

**Current table:** Already exists with milestone_id, amount, payment_date, etc.

**ADD these columns:**

```sql
-- Add new columns to existing milestonepayment table
ALTER TABLE `milestonepayment`
ADD COLUMN `contract_id` INT NULL AFTER `milestone_id`,
ADD COLUMN `payment_type` ENUM('upfront', 'milestone', 'final', 'weekly', 'material') NOT NULL DEFAULT 'milestone' AFTER `contract_id`,
ADD COLUMN `payment_percentage` DECIMAL(5,2) NULL AFTER `payment_type`,
ADD COLUMN `description` TEXT NULL AFTER `payment_percentage`,

-- Escrow tracking
ADD COLUMN `escrow_enabled` BOOLEAN DEFAULT FALSE AFTER `status`,
ADD COLUMN `paid_to_escrow_at` DATETIME NULL AFTER `escrow_enabled`,
ADD COLUMN `escrow_release_condition` VARCHAR(255) NULL AFTER `paid_to_escrow_at`,
ADD COLUMN `escrow_released_at` DATETIME NULL AFTER `escrow_release_condition`,
ADD COLUMN `escrow_refunded_at` DATETIME NULL AFTER `escrow_released_at`,
ADD COLUMN `refund_reason` TEXT NULL AFTER `escrow_refunded_at`,

-- Work verification (for upfront payments)
ADD COLUMN `requires_work_verification` BOOLEAN DEFAULT FALSE AFTER `refund_reason`,
ADD COLUMN `work_verified` BOOLEAN DEFAULT FALSE AFTER `requires_work_verification`,
ADD COLUMN `work_verified_at` DATETIME NULL AFTER `work_verified`,
ADD COLUMN `verification_deadline` DATETIME NULL AFTER `work_verified_at`,

-- 7-day guarantee (for 100% final payment)
ADD COLUMN `guarantee_period_days` INT NULL DEFAULT 0 AFTER `verification_deadline`,
ADD COLUMN `guarantee_end_date` DATE NULL AFTER `guarantee_period_days`,
ADD COLUMN `quality_approved` BOOLEAN DEFAULT FALSE AFTER `guarantee_end_date`,
ADD COLUMN `quality_approved_at` DATETIME NULL AFTER `quality_approved`,

-- Transaction info
ADD COLUMN `transaction_reference` VARCHAR(255) NULL AFTER `quality_approved_at`,
ADD COLUMN `paid_by` INT NULL AFTER `transaction_reference`,
ADD COLUMN `paid_to` INT NULL AFTER `paid_by`,
ADD COLUMN `paid_at` DATETIME NULL AFTER `paid_to`,

ADD COLUMN `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP AFTER `paid_at`,
ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Update status enum
ALTER TABLE `milestonepayment` 
MODIFY COLUMN `status` ENUM('pending','paid','held_escrow','released','completed','failed','refunded') DEFAULT 'pending';

-- Add foreign key and indexes
ALTER TABLE `milestonepayment`
ADD CONSTRAINT `fk_payment_contract` FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
ADD INDEX `idx_contract` (`contract_id`),
ADD INDEX `idx_escrow_status` (`status`, `escrow_enabled`);
```

**Why:** This adds escrow tracking and payment protection features.

---

## **1.2 Create New Tables**

### **New Table 1: `contract_chats` (Issue #4)**

**Purpose:** Store chat messages between customer and company

```sql
CREATE TABLE `contract_chats` (
  `message_id` INT AUTO_INCREMENT PRIMARY KEY,
  `contract_id` INT NOT NULL,
  
  -- Message details
  `sender_id` INT NOT NULL,
  `sender_type` ENUM('customer', 'company') NOT NULL,
  `message_type` ENUM('text', 'photo', 'file', 'location', 'system') DEFAULT 'text',
  `message_text` TEXT NULL,
  
  -- Attachments
  `attachment_path` VARCHAR(255) NULL,
  `attachment_type` VARCHAR(50) NULL,
  `attachment_size` INT NULL,
  `attachment_name` VARCHAR(255) NULL,
  
  -- Location (if message_type = 'location')
  `location_lat` DECIMAL(10,8) NULL,
  `location_lng` DECIMAL(11,8) NULL,
  `location_address` VARCHAR(255) NULL,
  
  -- Status
  `is_read` BOOLEAN DEFAULT FALSE,
  `read_at` DATETIME NULL,
  `is_deleted` BOOLEAN DEFAULT FALSE,
  `deleted_at` DATETIME NULL,
  
  -- Reply threading
  `reply_to_message_id` INT NULL,
  
  -- Timestamps
  `sent_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
  FOREIGN KEY (`reply_to_message_id`) REFERENCES `contract_chats`(`message_id`) ON DELETE SET NULL,
  
  INDEX `idx_contract` (`contract_id`),
  INDEX `idx_sender` (`sender_id`),
  INDEX `idx_unread` (`contract_id`, `is_read`),
  INDEX `idx_sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

### **New Table 2: `contract_notifications`**

**Purpose:** Track all notifications sent for contracts

```sql
CREATE TABLE `contract_notifications` (
  `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
  `contract_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `user_type` ENUM('customer', 'company') NOT NULL,
  
  -- Notification details
  `type` VARCHAR(50) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  
  -- Delivery channels
  `sent_email` BOOLEAN DEFAULT FALSE,
  `email_sent_at` DATETIME NULL,
  `sent_sms` BOOLEAN DEFAULT FALSE,
  `sms_sent_at` DATETIME NULL,
  `sent_push` BOOLEAN DEFAULT FALSE,
  `push_sent_at` DATETIME NULL,
  
  -- Status
  `is_read` BOOLEAN DEFAULT FALSE,
  `read_at` DATETIME NULL,
  
  -- Action link
  `action_url` VARCHAR(255) NULL,
  `action_label` VARCHAR(100) NULL,
  
  -- Timestamps
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
  
  INDEX `idx_contract` (`contract_id`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_unread` (`user_id`, `is_read`),
  INDEX `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

### **New Table 3: `contract_timeline`**

**Purpose:** Audit trail of all contract events

```sql
CREATE TABLE `contract_timeline` (
  `event_id` INT AUTO_INCREMENT PRIMARY KEY,
  `contract_id` INT NOT NULL,
  
  -- Event details
  `event_type` VARCHAR(50) NOT NULL,
  `event_title` VARCHAR(255) NOT NULL,
  `event_description` TEXT NULL,
  
  -- Actor
  `actor_id` INT NULL,
  `actor_type` ENUM('customer', 'company', 'system', 'admin') NOT NULL,
  `actor_name` VARCHAR(255) NULL,
  
  -- Related entities
  `milestone_id` INT NULL,
  `payment_id` INT NULL,
  
  -- Metadata
  `metadata` JSON NULL,
  
  -- Timestamp
  `event_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
  
  INDEX `idx_contract` (`contract_id`),
  INDEX `idx_event_type` (`event_type`),
  INDEX `idx_event_at` (`event_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

### **New Table 4: `contract_time_logs`**

**Purpose:** Time tracking for Time & Material contracts

```sql
CREATE TABLE `contract_time_logs` (
  `log_id` INT AUTO_INCREMENT PRIMARY KEY,
  `contract_id` INT NOT NULL,
  
  -- Time details
  `log_date` DATE NOT NULL,
  `clock_in_time` TIME NULL,
  `clock_out_time` TIME NULL,
  `total_hours` DECIMAL(4,2) NOT NULL,
  
  -- Work details
  `work_description` TEXT NOT NULL,
  `tasks_completed` TEXT NULL,
  
  -- Location verification
  `location_lat` DECIMAL(10,8) NULL,
  `location_lng` DECIMAL(11,8) NULL,
  `on_site_verified` BOOLEAN DEFAULT FALSE,
  
  -- Photos
  `work_photos` TEXT NULL,
  
  -- Status
  `status` ENUM('draft', 'submitted', 'approved', 'disputed') DEFAULT 'draft',
  `submitted_at` DATETIME NULL,
  `approved_at` DATETIME NULL,
  `approved_by` INT NULL,
  
  -- Invoice
  `invoiced` BOOLEAN DEFAULT FALSE,
  `invoice_id` INT NULL,
  
  -- Timestamps
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
  
  INDEX `idx_contract` (`contract_id`),
  INDEX `idx_log_date` (`log_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

### **New Table 5: `contract_invoices`**

**Purpose:** Weekly invoices for Time & Material contracts

```sql
CREATE TABLE `contract_invoices` (
  `invoice_id` INT AUTO_INCREMENT PRIMARY KEY,
  `contract_id` INT NOT NULL,
  
  -- Invoice details
  `invoice_number` VARCHAR(50) UNIQUE NOT NULL,
  `invoice_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  
  -- Period
  `period_start` DATE NOT NULL,
  `period_end` DATE NOT NULL,
  
  -- Amounts
  `labor_hours` DECIMAL(6,2) NOT NULL,
  `labor_rate` DECIMAL(10,2) NOT NULL,
  `labor_total` DECIMAL(10,2) NOT NULL,
  
  `materials_cost` DECIMAL(10,2) DEFAULT 0,
  `materials_markup_percentage` DECIMAL(5,2) DEFAULT 10.00,
  `materials_markup` DECIMAL(10,2) DEFAULT 0,
  `materials_total` DECIMAL(10,2) DEFAULT 0,
  
  `subtotal` DECIMAL(10,2) NOT NULL,
  `tax_percentage` DECIMAL(5,2) DEFAULT 0,
  `tax_amount` DECIMAL(10,2) DEFAULT 0,
  `total_amount` DECIMAL(10,2) NOT NULL,
  
  -- Status
  `status` ENUM('draft', 'submitted', 'approved', 'disputed', 'paid') DEFAULT 'draft',
  `submitted_at` DATETIME NULL,
  `approved_at` DATETIME NULL,
  `paid_at` DATETIME NULL,
  
  -- Dispute
  `disputed_at` DATETIME NULL,
  `dispute_reason` TEXT NULL,
  `dispute_resolved_at` DATETIME NULL,
  
  -- Attachments
  `time_log_ids` TEXT NULL,
  `receipt_paths` TEXT NULL,
  `invoice_document_path` VARCHAR(255) NULL,
  
  -- Timestamps
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
  
  INDEX `idx_contract` (`contract_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_invoice_date` (`invoice_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

### **New Table 6: `contract_budget_adjustments`**

**Purpose:** Track budget adjustments for flexible budgets (Issue #1)

```sql
CREATE TABLE `contract_budget_adjustments` (
  `adjustment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `contract_id` INT NOT NULL,
  
  -- Adjustment details
  `original_amount` DECIMAL(12,2) NOT NULL,
  `requested_amount` DECIMAL(12,2) NOT NULL,
  `adjustment_amount` DECIMAL(12,2) NOT NULL,
  `adjustment_percentage` DECIMAL(5,2) NOT NULL,
  
  -- Reason
  `reason` TEXT NOT NULL,
  `justification` TEXT NOT NULL,
  `supporting_documents` TEXT NULL,
  
  -- Status
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `requested_at` DATETIME NOT NULL,
  `requested_by` INT NOT NULL,
  
  `reviewed_at` DATETIME NULL,
  `reviewed_by` INT NULL,
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
```

---

### **New Table 7: `escrow_accounts`**

**Purpose:** Track escrow payments for protection

```sql
CREATE TABLE `escrow_accounts` (
  `escrow_id` INT AUTO_INCREMENT PRIMARY KEY,
  `payment_id` INT NOT NULL,
  `contract_id` INT NOT NULL,
  
  -- Amount
  `amount` DECIMAL(12,2) NOT NULL,
  `currency` VARCHAR(3) DEFAULT 'LKR',
  
  -- Status
  `status` ENUM('holding', 'released', 'refunded', 'disputed') DEFAULT 'holding',
  
  -- Holding details
  `held_at` DATETIME NOT NULL,
  `release_condition` VARCHAR(255) NOT NULL,
  `auto_release_date` DATE NULL,
  
  -- Release/refund
  `released_at` DATETIME NULL,
  `released_to` INT NULL,
  `refunded_at` DATETIME NULL,
  `refunded_to` INT NULL,
  `refund_reason` TEXT NULL,
  
  -- Verification
  `verification_required` BOOLEAN DEFAULT FALSE,
  `verification_deadline` DATETIME NULL,
  `verified_at` DATETIME NULL,
  `verified_by` INT NULL,
  
  -- Transaction IDs
  `payment_transaction_id` VARCHAR(100) NULL,
  `release_transaction_id` VARCHAR(100) NULL,
  `refund_transaction_id` VARCHAR(100) NULL,
  
  -- Timestamps
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (`payment_id`) REFERENCES `milestonepayment`(`payment_id`) ON DELETE CASCADE,
  FOREIGN KEY (`contract_id`) REFERENCES `contract`(`contract_id`) ON DELETE CASCADE,
  
  INDEX `idx_status` (`status`),
  INDEX `idx_contract` (`contract_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

## **1.3 Summary of Database Changes**

### **✅ Tables to ENHANCE (4 tables):**
1. `companyquotation` - Add 7 new columns
2. `contract` - Add ~30 new columns
3. `milestone` - Add ~20 new columns
4. `milestonepayment` - Add ~20 new columns

### **❌ Tables to CREATE (7 new tables):**
1. `contract_chats` - Chat system
2. `contract_notifications` - Notification tracking
3. `contract_timeline` - Event audit trail
4. `contract_time_logs` - Time tracking
5. `contract_invoices` - Weekly invoices
6. `contract_budget_adjustments` - Budget changes
7. `escrow_accounts` - Payment protection

### **✅ Tables to KEEP AS-IS:**
- `user` - No changes needed
- `company` - No changes needed
- `jobrequest` - No changes needed
- `project` - No changes needed
- All other existing tables

---
    -- Primary identification
    contract_id INT AUTO_INCREMENT PRIMARY KEY,
    contract_number VARCHAR(50) UNIQUE NOT NULL, -- CNT-2026-001
    
    -- References
    request_id INT NOT NULL,
    quotation_id INT NOT NULL,
    customer_id INT NOT NULL,
    company_id INT NOT NULL,
    
    -- Budget information (Issue #1)
    budget_amount DECIMAL(10,2) NOT NULL,
    budget_type ENUM('flexible', 'fixed') NOT NULL,
    budget_min DECIMAL(10,2) NULL, -- For flexible: budget * 0.9
    budget_max DECIMAL(10,2) NULL, -- For flexible: budget * 1.1
    final_amount DECIMAL(10,2) NULL, -- Actual final cost
    
    -- Payment information (Issue #2)
    payment_method ENUM('milestone', 'upfront_final_50_50', 'upfront_final_30_70', 'after_completion', 'time_material') NOT NULL,
    pricing_type ENUM('fixed_price', 'hourly', 'daily') NOT NULL DEFAULT 'fixed_price',
    hourly_rate DECIMAL(10,2) NULL, -- For time & material
    spending_cap DECIMAL(10,2) NULL, -- For time & material
    
    -- Timeline
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    duration_days INT NOT NULL,
    actual_start_date DATE NULL,
    actual_end_date DATE NULL,
    
    -- Status tracking
    status ENUM('pending_plan', 'pending_payment', 'pending_work_start', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'pending_plan',
    stage VARCHAR(100) NULL, -- Current stage description
    progress_percentage TINYINT DEFAULT 0,
    
    -- 24-hour undo (Issue #3)
    accepted_at DATETIME NOT NULL,
    undo_deadline DATETIME NOT NULL, -- accepted_at + 24 hours
    undo_available BOOLEAN DEFAULT TRUE,
    undone_at DATETIME NULL,
    undo_reason TEXT NULL,
    
    -- Chat (Issue #4)
    chat_active BOOLEAN DEFAULT FALSE,
    chat_activated_at DATETIME NULL,
    
    -- Escrow protection
    escrow_enabled BOOLEAN DEFAULT TRUE,
    escrow_status ENUM('none', 'holding', 'released', 'refunded') DEFAULT 'none',
    
    -- Work verification (for upfront payments)
    work_started_verified BOOLEAN DEFAULT FALSE,
    work_started_verified_at DATETIME NULL,
    work_start_proof_photo VARCHAR(255) NULL,
    work_start_proof_location VARCHAR(255) NULL,
    
    -- Documents
    contract_document_path VARCHAR(255) NULL,
    quotation_document_path VARCHAR(255) NULL,
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys
    FOREIGN KEY (request_id) REFERENCES service_requests(request_id),
    FOREIGN KEY (quotation_id) REFERENCES quotations(quotation_id),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (company_id) REFERENCES companies(company_id),
    
    -- Indexes
    INDEX idx_customer (customer_id),
    INDEX idx_company (company_id),
    INDEX idx_status (status),
    INDEX idx_undo_deadline (undo_deadline)
);
```

---

### **Table 2: `contract_milestones`**

**Purpose:** Store milestone information for milestone-based contracts

```sql
CREATE TABLE contract_milestones (
    milestone_id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    
    -- Milestone details
    milestone_number INT NOT NULL, -- 1, 2, 3, 4
    milestone_name VARCHAR(200) NOT NULL,
    milestone_description TEXT NOT NULL,
    
    -- Payment
    payment_percentage DECIMAL(5,2) NOT NULL, -- 25.00 for 25%
    payment_amount DECIMAL(10,2) NOT NULL,
    
    -- Timeline
    duration_days INT NOT NULL,
    start_date DATE NULL,
    end_date DATE NULL,
    estimated_completion DATE NULL,
    
    -- Status
    status ENUM('pending', 'active', 'submitted', 'approved', 'rejected', 'paid') DEFAULT 'pending',
    is_active BOOLEAN DEFAULT FALSE,
    
    -- Deliverables
    deliverables TEXT NOT NULL, -- JSON or comma-separated
    dependencies TEXT NULL, -- Which milestones must complete first
    
    -- Submission
    submitted_at DATETIME NULL,
    submitted_by INT NULL, -- company user_id
    submission_notes TEXT NULL,
    submission_photos TEXT NULL, -- JSON array of photo paths
    
    -- Review
    reviewed_at DATETIME NULL,
    reviewed_by INT NULL, -- customer user_id
    review_notes TEXT NULL,
    approved_at DATETIME NULL,
    rejected_at DATETIME NULL,
    rejection_reason TEXT NULL,
    
    -- Payment
    payment_requested_at DATETIME NULL,
    payment_released_at DATETIME NULL,
    payment_transaction_id VARCHAR(100) NULL,
    
    -- Escrow
    escrow_held_at DATETIME NULL,
    escrow_released_at DATETIME NULL,
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (contract_id) REFERENCES contracts(contract_id) ON DELETE CASCADE,
    
    INDEX idx_contract (contract_id),
    INDEX idx_status (status),
    INDEX idx_milestone_number (contract_id, milestone_number)
);
```

---

### **Table 3: `contract_payments`**

**Purpose:** Track all payments for contracts

```sql
CREATE TABLE contract_payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    milestone_id INT NULL, -- NULL for non-milestone payments
    
    -- Payment details
    payment_type ENUM('upfront', 'milestone', 'final', 'weekly', 'material') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_percentage DECIMAL(5,2) NULL,
    description TEXT NULL,
    
    -- Status
    status ENUM('pending', 'paid', 'held_escrow', 'released', 'refunded') DEFAULT 'pending',
    
    -- Escrow tracking
    escrow_enabled BOOLEAN DEFAULT FALSE,
    paid_to_escrow_at DATETIME NULL,
    escrow_release_condition VARCHAR(255) NULL, -- 'work_start', 'milestone_approval', '7_day_guarantee'
    escrow_released_at DATETIME NULL,
    escrow_refunded_at DATETIME NULL,
    refund_reason TEXT NULL,
    
    -- Work verification (for upfront payments)
    requires_work_verification BOOLEAN DEFAULT FALSE,
    work_verified BOOLEAN DEFAULT FALSE,
    work_verified_at DATETIME NULL,
    verification_deadline DATETIME NULL,
    
    -- 7-day guarantee (for 100% final payment)
    guarantee_period_days INT NULL DEFAULT 0,
    guarantee_end_date DATE NULL,
    quality_approved BOOLEAN DEFAULT FALSE,
    quality_approved_at DATETIME NULL,
    
    -- Transaction info
    payment_method VARCHAR(50) NULL, -- credit_card, bank_transfer, etc.
    transaction_id VARCHAR(100) NULL,
    transaction_reference VARCHAR(255) NULL,
    
    -- Paid by/to
    paid_by INT NULL, -- customer user_id
    paid_to INT NULL, -- company user_id
    paid_at DATETIME NULL,
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (contract_id) REFERENCES contracts(contract_id) ON DELETE CASCADE,
    FOREIGN KEY (milestone_id) REFERENCES contract_milestones(milestone_id) ON DELETE SET NULL,
    
    INDEX idx_contract (contract_id),
    INDEX idx_status (status),
    INDEX idx_escrow_status (status, escrow_enabled)
);
```

---

### **Table 4: `contract_chats`**

**Purpose:** Store chat messages between customer and company (Issue #4)

```sql
CREATE TABLE contract_chats (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    
    -- Message details
    sender_id INT NOT NULL, -- user_id (customer or company)
    sender_type ENUM('customer', 'company') NOT NULL,
    message_type ENUM('text', 'photo', 'file', 'location', 'system') DEFAULT 'text',
    message_text TEXT NULL,
    
    -- Attachments
    attachment_path VARCHAR(255) NULL,
    attachment_type VARCHAR(50) NULL, -- image/jpeg, application/pdf, etc.
    attachment_size INT NULL, -- bytes
    
    -- Location (if message_type = 'location')
    location_lat DECIMAL(10,8) NULL,
    location_lng DECIMAL(11,8) NULL,
    location_address VARCHAR(255) NULL,
    
    -- Status
    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    deleted_at DATETIME NULL,
    
    -- Reply threading
    reply_to_message_id INT NULL,
    
    -- Timestamps
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (contract_id) REFERENCES contracts(contract_id) ON DELETE CASCADE,
    FOREIGN KEY (reply_to_message_id) REFERENCES contract_chats(message_id) ON DELETE SET NULL,
    
    INDEX idx_contract (contract_id),
    INDEX idx_sender (sender_id),
    INDEX idx_unread (contract_id, is_read),
    INDEX idx_sent_at (sent_at)
);
```

---

### **Table 5: `contract_notifications`**

**Purpose:** Track all notifications sent for contracts

```sql
CREATE TABLE contract_notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    user_id INT NOT NULL,
    user_type ENUM('customer', 'company') NOT NULL,
    
    -- Notification details
    type VARCHAR(50) NOT NULL, -- 'contract_created', 'plan_submitted', 'undo_reminder', etc.
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    
    -- Delivery channels
    sent_email BOOLEAN DEFAULT FALSE,
    email_sent_at DATETIME NULL,
    sent_sms BOOLEAN DEFAULT FALSE,
    sms_sent_at DATETIME NULL,
    sent_push BOOLEAN DEFAULT FALSE,
    push_sent_at DATETIME NULL,
    
    -- Status
    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME NULL,
    
    -- Action link
    action_url VARCHAR(255) NULL,
    action_label VARCHAR(100) NULL,
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (contract_id) REFERENCES contracts(contract_id) ON DELETE CASCADE,
    
    INDEX idx_contract (contract_id),
    INDEX idx_user (user_id),
    INDEX idx_unread (user_id, is_read),
    INDEX idx_type (type)
);
```

---

### **Table 6: `contract_timeline`**

**Purpose:** Audit trail of all contract events

```sql
CREATE TABLE contract_timeline (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    
    -- Event details
    event_type VARCHAR(50) NOT NULL, -- 'created', 'plan_submitted', 'payment_made', etc.
    event_title VARCHAR(255) NOT NULL,
    event_description TEXT NULL,
    
    -- Actor
    actor_id INT NULL,
    actor_type ENUM('customer', 'company', 'system', 'admin') NOT NULL,
    actor_name VARCHAR(255) NULL,
    
    -- Related entities
    milestone_id INT NULL,
    payment_id INT NULL,
    
    -- Metadata
    metadata JSON NULL, -- Additional event data
    
    -- Timestamp
    event_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (contract_id) REFERENCES contracts(contract_id) ON DELETE CASCADE,
    
    INDEX idx_contract (contract_id),
    INDEX idx_event_type (event_type),
    INDEX idx_event_at (event_at)
);
```

---

### **Table 7: `contract_time_logs`**

**Purpose:** Time tracking for Time & Material contracts

```sql
CREATE TABLE contract_time_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    
    -- Time details
    log_date DATE NOT NULL,
    clock_in_time TIME NULL,
    clock_out_time TIME NULL,
    total_hours DECIMAL(4,2) NOT NULL,
    
    -- Work details
    work_description TEXT NOT NULL,
    tasks_completed TEXT NULL,
    
    -- Location verification
    location_lat DECIMAL(10,8) NULL,
    location_lng DECIMAL(11,8) NULL,
    on_site_verified BOOLEAN DEFAULT FALSE,
    
    -- Photos
    work_photos TEXT NULL, -- JSON array of photo paths
    
    -- Status
    status ENUM('draft', 'submitted', 'approved', 'disputed') DEFAULT 'draft',
    submitted_at DATETIME NULL,
    approved_at DATETIME NULL,
    approved_by INT NULL, -- customer user_id
    
    -- Invoice
    invoiced BOOLEAN DEFAULT FALSE,
    invoice_id INT NULL,
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (contract_id) REFERENCES contracts(contract_id) ON DELETE CASCADE,
    
    INDEX idx_contract (contract_id),
    INDEX idx_log_date (log_date),
    INDEX idx_status (status)
);
```

---

### **Table 8: `contract_invoices`**

**Purpose:** Weekly invoices for Time & Material contracts

```sql
CREATE TABLE contract_invoices (
    invoice_id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    
    -- Invoice details
    invoice_number VARCHAR(50) UNIQUE NOT NULL, -- INV-2026-001
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    
    -- Period
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    
    -- Amounts
    labor_hours DECIMAL(6,2) NOT NULL,
    labor_rate DECIMAL(10,2) NOT NULL,
    labor_total DECIMAL(10,2) NOT NULL,
    
    materials_cost DECIMAL(10,2) DEFAULT 0,
    materials_markup_percentage DECIMAL(5,2) DEFAULT 10.00,
    materials_markup DECIMAL(10,2) DEFAULT 0,
    materials_total DECIMAL(10,2) DEFAULT 0,
    
    subtotal DECIMAL(10,2) NOT NULL,
    tax_percentage DECIMAL(5,2) DEFAULT 0,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    
    -- Status
    status ENUM('draft', 'submitted', 'approved', 'disputed', 'paid') DEFAULT 'draft',
    submitted_at DATETIME NULL,
    approved_at DATETIME NULL,
    paid_at DATETIME NULL,
    
    -- Dispute
    disputed_at DATETIME NULL,
    dispute_reason TEXT NULL,
    dispute_resolved_at DATETIME NULL,
    
    -- Attachments
    time_log_ids TEXT NULL, -- JSON array of log_ids
    receipt_paths TEXT NULL, -- JSON array of receipt paths
    invoice_document_path VARCHAR(255) NULL,
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (contract_id) REFERENCES contracts(contract_id) ON DELETE CASCADE,
    
    INDEX idx_contract (contract_id),
    INDEX idx_status (status),
    INDEX idx_invoice_date (invoice_date)
);
```

---

### **Table 9: `contract_budget_adjustments`**

**Purpose:** Track budget adjustments for flexible budgets (Issue #1)

```sql
CREATE TABLE contract_budget_adjustments (
    adjustment_id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    
    -- Adjustment details
    original_amount DECIMAL(10,2) NOT NULL,
    requested_amount DECIMAL(10,2) NOT NULL,
    adjustment_amount DECIMAL(10,2) NOT NULL, -- difference
    adjustment_percentage DECIMAL(5,2) NOT NULL,
    
    -- Reason
    reason TEXT NOT NULL,
    justification TEXT NOT NULL,
    supporting_documents TEXT NULL, -- JSON array of document paths
    
    -- Status
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    requested_at DATETIME NOT NULL,
    requested_by INT NOT NULL, -- company user_id
    
    reviewed_at DATETIME NULL,
    reviewed_by INT NULL, -- customer user_id
    review_notes TEXT NULL,
    
    approved_at DATETIME NULL,
    rejected_at DATETIME NULL,
    rejection_reason TEXT NULL,
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (contract_id) REFERENCES contracts(contract_id) ON DELETE CASCADE,
    
    INDEX idx_contract (contract_id),
    INDEX idx_status (status)
);
```

---

### **Table 10: Update `quotations` table**

**Add new fields to existing table:**

```sql
ALTER TABLE quotations
ADD COLUMN budget_type ENUM('flexible', 'fixed') NOT NULL DEFAULT 'fixed' AFTER budget_amount,
ADD COLUMN payment_method ENUM('milestone', 'upfront_final_50_50', 'upfront_final_30_70', 'after_completion', 'time_material') NOT NULL DEFAULT 'milestone' AFTER budget_type,
ADD COLUMN pricing_type ENUM('fixed_price', 'hourly', 'daily') NOT NULL DEFAULT 'fixed_price' AFTER payment_method,
ADD COLUMN hourly_rate DECIMAL(10,2) NULL AFTER pricing_type,
ADD COLUMN spending_cap_multiplier DECIMAL(3,2) NULL DEFAULT 1.20 AFTER hourly_rate;
```

---

## **1.2 Model Classes to Create**

### **File: `models/Contract.php`**

```php
<?php
class Contract {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    // Create contract from accepted quotation
    public function createFromQuotation($quotation_id, $accepted_at = null) {
        // Implementation
    }
    
    // Get contract by ID
    public function getById($contract_id) {
        // Implementation
    }
    
    // Get contract by number
    public function getByNumber($contract_number) {
        // Implementation
    }
    
    // Get contracts for customer
    public function getByCustomer($customer_id, $filters = []) {
        // Implementation
    }
    
    // Get contracts for company
    public function getByCompany($company_id, $filters = []) {
        // Implementation
    }
    
    // Update contract status
    public function updateStatus($contract_id, $status, $stage = null) {
        // Implementation
    }
    
    // Check if undo is still available
    public function canUndo($contract_id) {
        // Implementation
    }
    
    // Undo contract acceptance
    public function undo($contract_id, $reason) {
        // Implementation
    }
    
    // Activate chat
    public function activateChat($contract_id) {
        // Implementation
    }
    
    // Update progress
    public function updateProgress($contract_id, $percentage) {
        // Implementation
    }
}
```

### **File: `models/ContractMilestone.php`**
### **File: `models/ContractPayment.php`**
### **File: `models/ContractChat.php`**
### **File: `models/ContractNotification.php`**
### **File: `models/ContractTimeline.php`**

*(Similar structure for each model)*

---

## **1.3 Deliverables for Phase 1**

✅ All database tables created  
✅ All model classes created  
✅ Database migrations documented  
✅ Model unit tests written  
✅ API endpoint stubs created  
✅ Documentation updated

---

# 🔷 **PHASE 2: CONTRACT CREATION SYSTEM**

**Duration:** Week 3-4  
**Priority:** Critical  
**Complexity:** Medium

## **Objectives**
- Implement automatic contract creation when quotation accepted
- Generate unique contract numbers
- Set up initial contract status
- Create contract visibility in 5 locations (Issue #5)
- Initialize 24-hour undo countdown (Issue #3)

---

## **2.1 Backend Implementation**

### **File: `api/quotations.php` (update existing)**

Add endpoint for accepting quotation:

```php
// POST /api/quotations.php?action=accept
case 'accept':
    $quotation_id = $_POST['quotation_id'];
    $customer_id = $_SESSION['user_id'];
    
    // 1. Verify quotation belongs to customer's request
    // 2. Check quotation not already accepted
    // 3. Accept quotation
    // 4. Create contract automatically
    // 5. Initialize undo window
    // 6. Send notifications
    // 7. Return contract details
    
    $result = $quotationController->acceptQuotation($quotation_id, $customer_id);
    echo json_encode($result);
    break;
```

### **File: `controllers/QuotationController.php`**

```php
public function acceptQuotation($quotation_id, $customer_id) {
    try {
        $this->db->beginTransaction();
        
        // 1. Get quotation details
        $quotation = $this->quotationModel->getById($quotation_id);
        
        // 2. Verify quotation
        if (!$quotation || $quotation['customer_id'] != $customer_id) {
            throw new Exception("Invalid quotation");
        }
        
        // 3. Update quotation status
        $this->quotationModel->updateStatus($quotation_id, 'accepted');
        
        // 4. Create contract
        $contract = $this->contractModel->createFromQuotation($quotation_id);
        
        // 5. Initialize undo window (24 hours)
        $undo_deadline = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $this->contractModel->setUndoDeadline($contract['contract_id'], $undo_deadline);
        
        // 6. Create timeline event
        $this->timelineModel->addEvent($contract['contract_id'], 'contract_created', 'customer');
        
        // 7. Send notifications
        $this->notificationService->sendContractCreated($contract['contract_id']);
        
        // 8. Schedule undo reminder (23 hours from now)
        $this->reminderService->scheduleUndoReminder($contract['contract_id'], $undo_deadline);
        
        $this->db->commit();
        
        return [
            'success' => true,
            'contract' => $contract,
            'message' => 'Contract created successfully'
        ];
        
    } catch (Exception $e) {
        $this->db->rollBack();
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
```

### **File: `models/Contract.php` (implement method)**

```php
public function createFromQuotation($quotation_id, $accepted_at = null) {
    // Get quotation details
    $quotation = $this->db->query(
        "SELECT q.*, r.*, c.company_id, cu.customer_id 
         FROM quotations q
         JOIN service_requests r ON q.request_id = r.request_id
         JOIN companies c ON q.company_id = c.company_id
         JOIN customers cu ON r.customer_id = cu.customer_id
         WHERE q.quotation_id = ?",
        [$quotation_id]
    )->fetch();
    
    if (!$quotation) {
        throw new Exception("Quotation not found");
    }
    
    // Generate contract number
    $contract_number = $this->generateContractNumber();
    
    // Calculate dates
    $accepted_at = $accepted_at ?: date('Y-m-d H:i:s');
    $undo_deadline = date('Y-m-d H:i:s', strtotime($accepted_at . ' +24 hours'));
    
    // Calculate budget range (if flexible)
    $budget_min = null;
    $budget_max = null;
    if ($quotation['budget_type'] == 'flexible') {
        $budget_min = $quotation['budget_amount'] * 0.9;
        $budget_max = $quotation['budget_amount'] * 1.1;
    }
    
    // Determine initial status based on payment method
    $initial_status = 'pending_plan'; // Default for milestone
    if ($quotation['payment_method'] == 'upfront_final_50_50' || 
        $quotation['payment_method'] == 'upfront_final_30_70') {
        $initial_status = 'pending_payment';
    } elseif ($quotation['payment_method'] == 'after_completion' || 
              $quotation['payment_method'] == 'time_material') {
        $initial_status = 'active';
    }
    
    // Insert contract
    $contract_id = $this->db->insert('contracts', [
        'contract_number' => $contract_number,
        'request_id' => $quotation['request_id'],
        'quotation_id' => $quotation_id,
        'customer_id' => $quotation['customer_id'],
        'company_id' => $quotation['company_id'],
        'budget_amount' => $quotation['budget_amount'],
        'budget_type' => $quotation['budget_type'],
        'budget_min' => $budget_min,
        'budget_max' => $budget_max,
        'payment_method' => $quotation['payment_method'],
        'pricing_type' => $quotation['pricing_type'],
        'hourly_rate' => $quotation['hourly_rate'],
        'spending_cap' => $quotation['budget_amount'] * ($quotation['spending_cap_multiplier'] ?? 1.2),
        'start_date' => $quotation['estimated_start_date'],
        'end_date' => $quotation['estimated_end_date'],
        'duration_days' => $quotation['estimated_duration_days'],
        'status' => $initial_status,
        'accepted_at' => $accepted_at,
        'undo_deadline' => $undo_deadline,
        'undo_available' => 1,
        'created_at' => $accepted_at
    ]);
    
    return $this->getById($contract_id);
}

private function generateContractNumber() {
    $year = date('Y');
    $prefix = 'CNT-' . $year . '-';
    
    // Get last contract number for this year
    $last = $this->db->query(
        "SELECT contract_number FROM contracts 
         WHERE contract_number LIKE ? 
         ORDER BY contract_id DESC LIMIT 1",
        [$prefix . '%']
    )->fetch();
    
    if ($last) {
        $last_number = intval(substr($last['contract_number'], -3));
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    
    return $prefix . str_pad($new_number, 3, '0', STR_PAD_LEFT);
}
```

---

## **2.2 Frontend Implementation**

### **File: `views/customer/quotations.php` (update)**

Add accept quotation dialog:

```html
<!-- Accept Quotation Dialog -->
<div id="acceptQuotationDialog" class="modal">
    <div class="modal-content">
        <h2>Accept Quotation</h2>
        
        <div class="quotation-summary">
            <!-- Show quotation details -->
        </div>
        
        <div class="important-info">
            <h3>⏰ 24-Hour Undo Window</h3>
            <p>You can undo this acceptance within 24 hours with no penalty.</p>
        </div>
        
        <div class="confirmation">
            <input type="checkbox" id="confirmAccept" required>
            <label for="confirmAccept">
                I understand the terms and want to proceed
            </label>
        </div>
        
        <div class="actions">
            <button onclick="closeAcceptDialog()">Cancel</button>
            <button onclick="confirmAcceptQuotation()" class="primary" disabled>
                Accept Quotation
            </button>
        </div>
    </div>
</div>

<script>
function acceptQuotation(quotationId) {
    // Show dialog with quotation details
    showAcceptDialog(quotationId);
}

function confirmAcceptQuotation() {
    const quotationId = document.getElementById('quotationIdToAccept').value;
    
    fetch('/api/quotations.php?action=accept', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({quotation_id: quotationId})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showSuccess('Contract created successfully!');
            
            // Redirect to contract page
            window.location.href = '/views/customer/contract-details.php?id=' + data.contract.contract_id;
        } else {
            showError(data.message);
        }
    });
}
</script>
```

### **File: `views/customer/contract-details.php` (create new)**

Full contract details page with all 5 access points integrated.

### **File: `views/customer/contracts.php` (create new)**

Main contracts list page (Access Point #1).

---

## **2.3 Contract Visibility (Issue #5)**

### **Access Point 1: My Contracts Tab**

**File: `views/customer/contracts.php`**

```php
// List all contracts with filters
// Show contract cards with key info
// Status indicators
// Quick actions
```

### **Access Point 2: Original Request Page**

**File: `views/customer/request-details.php` (update)**

```php
// Add "Contract Created" section at bottom
// Show journey: Request → Quotations → Contract
// Link to contract details
```

### **Access Point 3: Notification Bell**

**File: `views/includes/header.php` (update)**

```php
// Add notification bell icon
// Show unread count badge
// Dropdown with recent notifications
// Link to contract from notification
```

### **Access Point 4: Email Links**

**File: `services/EmailService.php`**

```php
// Send contract created email
// Include contract summary
// Direct link to contract details
// All CTAs lead to contract page
```

### **Access Point 5: Dashboard Widget**

**File: `views/customer/dashboard.php` (update)**

```php
// Add "Active Contracts" widget
// Show contract cards
// Progress indicators
// Quick actions
```

---

## **2.4 Testing Tasks**

- [ ] Test contract creation from quotation acceptance
- [ ] Verify unique contract numbers generated
- [ ] Check initial status set correctly based on payment method
- [ ] Test undo deadline calculation (24 hours)
- [ ] Verify contract visible in all 5 locations
- [ ] Test with all 5 payment methods
- [ ] Test with flexible and fixed budgets
- [ ] Verify notifications sent correctly
- [ ] Test timeline events created
- [ ] Check database constraints and foreign keys

---

## **2.5 Deliverables for Phase 2**

✅ Contract auto-creation working  
✅ Unique contract IDs generated  
✅ 24-hour undo window initialized  
✅ All 5 access points implemented  
✅ Contract details page created  
✅ Email notifications working  
✅ Timeline tracking active  
✅ Tests passing  

---

# 🔷 **PHASE 3: BUDGET FLEXIBILITY SYSTEM (Issue #1)**

**Duration:** Week 5  
**Priority:** High  
**Complexity:** Low-Medium

## **Objectives**
- Display budget type (flexible/fixed) throughout system
- Calculate and show budget ranges for flexible budgets
- Implement budget adjustment request system
- Build approval workflow

---

## **3.1 Budget Display**

### **Update: `views/customer/contract-details.php`**

```html
<div class="budget-section">
    <h3>💰 Budget Information</h3>
    
    <div class="budget-main">
        <label>Total Budget:</label>
        <span class="amount">LKR <?= number_format($contract['budget_amount'], 2) ?></span>
    </div>
    
    <div class="budget-type">
        <label>Budget Type:</label>
        <?php if ($contract['budget_type'] == 'flexible'): ?>
            <span class="badge badge-flexible">🟢 Flexible (±10%)</span>
            
            <div class="budget-range">
                <p>Allowed Range:</p>
                <ul>
                    <li>Minimum: LKR <?= number_format($contract['budget_min'], 2) ?> (-10%)</li>
                    <li>Maximum: LKR <?= number_format($contract['budget_max'], 2) ?> (+10%)</li>
                </ul>
            </div>
            
            <div class="budget-explanation">
                <p><strong>What this means:</strong></p>
                <p>Final cost can vary within this range to accommodate material price changes or necessary adjustments. All changes require your approval.</p>
            </div>
        <?php else: ?>
            <span class="badge badge-fixed">🔒 Fixed</span>
            
            <div class="budget-explanation">
                <p><strong>What this means:</strong></p>
                <p>Total cost is locked at LKR <?= number_format($contract['budget_amount'], 2) ?>. No adjustments allowed.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($contract['budget_type'] == 'flexible' && count($adjustments) > 0): ?>
        <div class="budget-adjustments">
            <h4>Budget Adjustment History</h4>
            <!-- Show past adjustments -->
        </div>
    <?php endif; ?>
</div>
```

---

## **3.2 Budget Adjustment Request (Company Side)**

### **File: `api/budget-adjustments.php` (create new)**

```php
// POST /api/budget-adjustments.php?action=request
case 'request':
    $contract_id = $_POST['contract_id'];
    $requested_amount = $_POST['requested_amount'];
    $reason = $_POST['reason'];
    $justification = $_POST['justification'];
    
    $result = $budgetAdjustmentController->requestAdjustment(
        $contract_id, 
        $requested_amount, 
        $reason, 
        $justification
    );
    
    echo json_encode($result);
    break;
```

### **File: `controllers/BudgetAdjustmentController.php` (create new)**

```php
public function requestAdjustment($contract_id, $requested_amount, $reason, $justification) {
    // 1. Get contract
    $contract = $this->contractModel->getById($contract_id);
    
    // 2. Verify budget is flexible
    if ($contract['budget_type'] != 'flexible') {
        return ['success' => false, 'message' => 'Budget is fixed, no adjustments allowed'];
    }
    
    // 3. Check requested amount is within ±10%
    if ($requested_amount < $contract['budget_min'] || $requested_amount > $contract['budget_max']) {
        return ['success' => false, 'message' => 'Amount exceeds allowed range'];
    }
    
    // 4. Create adjustment request
    $adjustment_id = $this->adjustmentModel->create([
        'contract_id' => $contract_id,
        'original_amount' => $contract['budget_amount'],
        'requested_amount' => $requested_amount,
        'adjustment_amount' => $requested_amount - $contract['budget_amount'],
        'adjustment_percentage' => (($requested_amount - $contract['budget_amount']) / $contract['budget_amount']) * 100,
        'reason' => $reason,
        'justification' => $justification,
        'requested_by' => $_SESSION['user_id'],
        'requested_at' => date('Y-m-d H:i:s')
    ]);
    
    // 5. Notify customer
    $this->notificationService->sendBudgetAdjustmentRequest($contract_id, $adjustment_id);
    
    // 6. Create timeline event
    $this->timelineModel->addEvent($contract_id, 'budget_adjustment_requested', 'company');
    
    return ['success' => true, 'adjustment_id' => $adjustment_id];
}
```

---

## **3.3 Budget Adjustment Approval (Customer Side)**

### **File: `views/customer/contract-details.php` (update)**

Show pending adjustment requests:

```html
<?php if ($pending_adjustment): ?>
<div class="adjustment-request alert alert-warning">
    <h4>⚠️ Budget Adjustment Request Pending</h4>
    
    <div class="adjustment-details">
        <table>
            <tr>
                <td>Current Budget:</td>
                <td>LKR <?= number_format($contract['budget_amount'], 2) ?></td>
            </tr>
            <tr>
                <td>Requested Budget:</td>
                <td>LKR <?= number_format($pending_adjustment['requested_amount'], 2) ?></td>
            </tr>
            <tr>
                <td>Adjustment:</td>
                <td class="<?= $pending_adjustment['adjustment_amount'] > 0 ? 'increase' : 'decrease' ?>">
                    <?= $pending_adjustment['adjustment_amount'] > 0 ? '+' : '' ?>
                    LKR <?= number_format($pending_adjustment['adjustment_amount'], 2) ?>
                    (<?= number_format($pending_adjustment['adjustment_percentage'], 1) ?>%)
                </td>
            </tr>
        </table>
    </div>
    
    <div class="adjustment-reason">
        <h5>Reason for Adjustment:</h5>
        <p><?= htmlspecialchars($pending_adjustment['reason']) ?></p>
        
        <h5>Justification:</h5>
        <p><?= nl2br(htmlspecialchars($pending_adjustment['justification'])) ?></p>
    </div>
    
    <?php if ($pending_adjustment['supporting_documents']): ?>
        <div class="supporting-docs">
            <h5>Supporting Documents:</h5>
            <!-- Show documents -->
        </div>
    <?php endif; ?>
    
    <div class="actions">
        <button onclick="openChat()">💬 Discuss via Chat</button>
        <button onclick="rejectAdjustment(<?= $pending_adjustment['adjustment_id'] ?>)" class="btn-danger">
            ❌ Reject
        </button>
        <button onclick="approveAdjustment(<?= $pending_adjustment['adjustment_id'] ?>)" class="btn-success">
            ✅ Approve Adjustment
        </button>
    </div>
</div>
<?php endif; ?>
```

---

## **3.4 Testing Tasks**

- [ ] Test flexible budget display
- [ ] Test fixed budget display
- [ ] Test budget range calculations (±10%)
- [ ] Test adjustment request creation
- [ ] Test within-range validation
- [ ] Test out-of-range rejection
- [ ] Test adjustment approval flow
- [ ] Test adjustment rejection flow
- [ ] Test notifications sent correctly
- [ ] Test timeline events

---

## **3.5 Deliverables for Phase 3**

✅ Budget type displayed everywhere  
✅ Budget ranges calculated correctly  
✅ Adjustment request system working  
✅ Approval/rejection workflow complete  
✅ Validation in place  
✅ Notifications working  
✅ Tests passing  

---

# 🔷 **PHASE 4: PAYMENT TERMS SYSTEM (Issue #2)**

**Duration:** Week 6-7  
**Priority:** Critical  
**Complexity:** High

This is the most complex phase as it handles 5 different payment methods.

---

## **4.1 Payment Method Display**

### **Update: `views/customer/contract-details.php`**

```php
<div class="payment-section">
    <h3>💳 Payment Information</h3>
    
    <?php
    switch($contract['payment_method']) {
        case 'milestone':
            include 'partials/payment-milestone.php';
            break;
        case 'upfront_final_50_50':
            include 'partials/payment-upfront-50-50.php';
            break;
        case 'upfront_final_30_70':
            include 'partials/payment-upfront-30-70.php';
            break;
        case 'after_completion':
            include 'partials/payment-after-completion.php';
            break;
        case 'time_material':
            include 'partials/payment-time-material.php';
            break;
    }
    ?>
</div>
```

---

## **4.2 Milestone-Based Payment**

### **File: `api/milestones.php` (create new)**

```php
// POST /api/milestones.php?action=submit_plan
case 'submit_plan':
    $contract_id = $_POST['contract_id'];
    $milestones = $_POST['milestones']; // Array of milestone data
    
    $result = $milestoneController->submitPlan($contract_id, $milestones);
    echo json_encode($result);
    break;

// POST /api/milestones.php?action=approve_plan
case 'approve_plan':
    $contract_id = $_POST['contract_id'];
    $result = $milestoneController->approvePlan($contract_id);
    echo json_encode($result);
    break;

// POST /api/milestones.php?action=mark_complete
case 'mark_complete':
    $milestone_id = $_POST['milestone_id'];
    $result = $milestoneController->markComplete($milestone_id);
    echo json_encode($result);
    break;

// POST /api/milestones.php?action=approve_milestone
case 'approve_milestone':
    $milestone_id = $_POST['milestone_id'];
    $result = $milestoneController->approveMilestone($milestone_id);
    echo json_encode($result);
    break;
```

### **Milestone Plan Submission Flow:**

1. Company receives "Action Required" notification (48hr deadline)
2. Company fills milestone planning form
3. System validates plan (budget match, timeline fit)
4. Plan submitted
5. Chat activates automatically
6. Customer receives notification
7. Customer reviews plan
8. Customer approves/rejects/requests changes

---

## **4.3 Upfront + Final Payment (50-50 or 30-70)**

### **File: `api/payments.php` (create new)**

```php
// POST /api/payments.php?action=process_upfront
case 'process_upfront':
    $contract_id = $_POST['contract_id'];
    $payment_method = $_POST['payment_method']; // credit_card, bank, etc.
    
    $result = $paymentController->processUpfrontPayment($contract_id, $payment_method);
    echo json_encode($result);
    break;
```

### **Upfront Payment Flow:**

1. Contract created → Status: "Pending Payment"
2. Customer sees payment request with escrow protection info
3. Customer makes payment → Goes to escrow
4. Contract status → "Pending Work Start"
5. Chat activates immediately
6. On start date, company submits work-start proof
7. Customer verifies work started (24hr window)
8. Escrow releases payment to company
9. Work continues → Final payment at completion

---

## **4.4 After Completion (100%)**

### **After Completion Flow:**

1. Contract created → Status: "Active" immediately
2. NO upfront payment required
3. Chat activates immediately
4. Company works on trust
5. Company marks work complete
6. Customer reviews work
7. Customer makes full payment → Goes to escrow
8. 7-day quality guarantee period begins
9. Customer can request fixes (free)
10. Customer approves quality
11. Escrow releases payment

---

## **4.5 Time & Material**

### **File: `api/time-logs.php` (create new)**

```php
// POST /api/time-logs.php?action=clock_in
// POST /api/time-logs.php?action=clock_out
// POST /api/time-logs.php?action=submit_daily_log
// GET /api/time-logs.php?action=get_logs&contract_id=X
```

### **File: `api/invoices.php` (create new)**

```php
// POST /api/invoices.php?action=generate_weekly
// POST /api/invoices.php?action=submit
// POST /api/invoices.php?action=approve
// POST /api/invoices.php?action=dispute
```

### **Time & Material Flow:**

1. Contract created → Status: "Active" immediately
2. Time tracking enabled
3. Company clocks in/out daily
4. Customer can view time logs real-time
5. Every Friday, system generates weekly invoice
6. Company submits invoice
7. Customer reviews and approves/disputes
8. Payment within 3 days of approval
9. Spending cap monitoring
10. Project continues until complete

---

## **4.6 Testing Tasks**

- [ ] Test milestone plan submission
- [ ] Test milestone plan approval/rejection
- [ ] Test milestone completion and approval
- [ ] Test upfront payment processing
- [ ] Test escrow holding
- [ ] Test work start verification
- [ ] Test 50-50 and 30-70 splits
- [ ] Test after completion 100% payment
- [ ] Test 7-day guarantee period
- [ ] Test time logging (clock in/out)
- [ ] Test weekly invoice generation
- [ ] Test invoice approval/dispute
- [ ] Test spending cap enforcement
- [ ] Test all payment flows end-to-end

---

## **4.7 Deliverables for Phase 4**

✅ All 5 payment methods implemented  
✅ Milestone system working  
✅ Upfront payment with escrow  
✅ After completion with guarantee  
✅ Time tracking and invoicing  
✅ Payment processing integrated  
✅ All workflows tested  

---

# 🔷 **PHASE 5: 24-HOUR UNDO WINDOW (Issue #3)**

**Duration:** Week 8  
**Priority:** High  
**Complexity:** Low-Medium

## **Objectives**
- Implement undo functionality
- Create countdown timer display
- Schedule reminder notifications
- Handle undo deadline expiration
- Reopen request after undo

---

## **5.1 Undo Functionality**

### **File: `api/contracts.php` (create new)**

```php
// POST /api/contracts.php?action=undo
case 'undo':
    $contract_id = $_POST['contract_id'];
    $reason = $_POST['reason'];
    
    $result = $contractController->undoAcceptance($contract_id, $reason);
    echo json_encode($result);
    break;
```

### **File: `controllers/ContractController.php`**

```php
public function undoAcceptance($contract_id, $reason) {
    try {
        $this->db->beginTransaction();
        
        // 1. Get contract
        $contract = $this->contractModel->getById($contract_id);
        
        // 2. Check undo still available
        if (!$contract['undo_available'] || strtotime($contract['undo_deadline']) < time()) {
            throw new Exception("Undo deadline has passed");
        }
        
        // 3. Check user has permission
        if ($contract['customer_id'] != $_SESSION['user_id']) {
            throw new Exception("Unauthorized");
        }
        
        // 4. Check no payments made yet
        $payments = $this->paymentModel->getByContract($contract_id);
        if (count($payments) > 0) {
            throw new Exception("Cannot undo after payment made");
        }
        
        // 5. Update contract status
        $this->contractModel->update($contract_id, [
            'status' => 'cancelled',
            'undo_available' => 0,
            'undone_at' => date('Y-m-d H:i:s'),
            'undo_reason' => $reason
        ]);
        
        // 6. Reopen quotation
        $this->quotationModel->updateStatus($contract['quotation_id'], 'pending');
        
        // 7. Reopen request
        $this->requestModel->updateStatus($contract['request_id'], 'open');
        
        // 8. Notify company
        $this->notificationService->sendContractUndone($contract_id);
        
        // 9. Create timeline event
        $this->timelineModel->addEvent($contract_id, 'contract_undone', 'customer', $reason);
        
        $this->db->commit();
        
        return ['success' => true, 'message' => 'Contract cancelled successfully'];
        
    } catch (Exception $e) {
        $this->db->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
```

---

## **5.2 Countdown Timer**

### **File: `assets/js/contract-countdown.js` (create new)**

```javascript
class ContractCountdown {
    constructor(contractId, deadline) {
        this.contractId = contractId;
        this.deadline = new Date(deadline).getTime();
        this.timers = [];
        this.start();
    }
    
    start() {
        // Update every second
        this.interval = setInterval(() => this.update(), 1000);
        this.update(); // Initial update
    }
    
    update() {
        const now = new Date().getTime();
        const distance = this.deadline - now;
        
        if (distance < 0) {
            this.expired();
            return;
        }
        
        const hours = Math.floor(distance / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        const formatted = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Update all countdown displays
        document.querySelectorAll('.undo-countdown').forEach(el => {
            el.textContent = formatted;
            
            // Warning color if less than 2 hours
            if (distance < 2 * 60 * 60 * 1000) {
                el.classList.add('countdown-warning');
            }
        });
    }
    
    expired() {
        clearInterval(this.interval);
        
        // Hide undo button
        document.querySelectorAll('.undo-button').forEach(el => {
            el.style.display = 'none';
        });
        
        // Show expired message
        document.querySelectorAll('.undo-countdown-container').forEach(el => {
            el.innerHTML = '<span class="undo-expired">Undo window expired</span>';
        });
        
        // Notify backend
        fetch(`/api/contracts.php?action=expire_undo&contract_id=${this.contractId}`);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const contractId = document.getElementById('contractId')?.value;
    const undoDeadline = document.getElementById('undoDeadline')?.value;
    
    if (contractId && undoDeadline) {
        new ContractCountdown(contractId, undoDeadline);
    }
});
```

---

## **5.3 Reminder System**

### **File: `cron/undo-reminders.php` (create new)**

```php
<?php
// Run every hour via cron job

require_once '../config/database.php';
require_once '../services/NotificationService.php';

$db = new Database();
$notificationService = new NotificationService($db);

// Get contracts with undo deadline in next hour that haven't been reminded
$contracts = $db->query("
    SELECT c.*, cu.email, cu.phone, cu.name as customer_name
    FROM contracts c
    JOIN customers cu ON c.customer_id = cu.customer_id
    WHERE c.undo_available = 1
    AND c.undo_deadline BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 HOUR)
    AND c.reminded_at IS NULL
")->fetchAll();

foreach ($contracts as $contract) {
    // Send reminder
    $notificationService->sendUndoReminder($contract['contract_id']);
    
    // Mark as reminded
    $db->update('contracts', ['reminded_at' => date('Y-m-d H:i:s')], ['contract_id' => $contract['contract_id']]);
    
    echo "Reminder sent for contract " . $contract['contract_number'] . "\n";
}

echo "Undo reminder cron completed\n";
```

### **Cron Job Setup (Linux/Mac):**

```bash
# Run every hour
0 * * * * php /path/to/FixLanka/cron/undo-reminders.php >> /path/to/logs/undo-reminders.log 2>&1
```

### **Task Scheduler Setup (Windows XAMPP):**

Create batch file `run-undo-reminders.bat`:
```batch
@echo off
"C:\xampp\php\php.exe" "C:\xampp\htdocs\FixLanka\cron\undo-reminders.php"
```

Schedule in Windows Task Scheduler to run hourly.

---

## **5.4 Testing Tasks**

- [ ] Test undo functionality within 24 hours
- [ ] Test undo blocked after deadline
- [ ] Test undo blocked after payment
- [ ] Test countdown timer display
- [ ] Test countdown updates every second
- [ ] Test countdown warning state (<2 hours)
- [ ] Test countdown expiration handling
- [ ] Test reminder sent 1 hour before deadline
- [ ] Test reminder not sent twice
- [ ] Test request reopened after undo
- [ ] Test quotation status reverted
- [ ] Test company notification sent
- [ ] Test timeline event created

---

## **5.5 Deliverables for Phase 5**

✅ Undo functionality working  
✅ Countdown timer implemented  
✅ Reminder system active  
✅ Cron job configured  
✅ Request reopening working  
✅ Notifications sent correctly  
✅ Tests passing  

---

# 🔷 **PHASE 6: CHAT SYSTEM (Issue #4)**

**Duration:** Week 9-10  
**Priority:** High  
**Complexity:** High

## **Objectives**
- Build real-time chat interface
- Implement file/photo sharing
- Create 5 access points for chat
- Set up chat activation logic
- Implement message history and notifications

---

## **6.1 Chat Backend**

### **File: `api/chat.php` (create new)**

```php
// GET /api/chat.php?action=get_messages&contract_id=X
case 'get_messages':
    $contract_id = $_GET['contract_id'];
    $since_id = $_GET['since_id'] ?? 0; // For pagination
    
    $messages = $chatModel->getMessages($contract_id, $since_id);
    echo json_encode(['success' => true, 'messages' => $messages]);
    break;

// POST /api/chat.php?action=send_message
case 'send_message':
    $contract_id = $_POST['contract_id'];
    $message_text = $_POST['message_text'];
    $reply_to = $_POST['reply_to'] ?? null;
    
    $result = $chatController->sendMessage($contract_id, $message_text, $reply_to);
    echo json_encode($result);
    break;

// POST /api/chat.php?action=upload_photo
case 'upload_photo':
    $contract_id = $_POST['contract_id'];
    $photo = $_FILES['photo'];
    
    $result = $chatController->uploadPhoto($contract_id, $photo);
    echo json_encode($result);
    break;

// POST /api/chat.php?action=mark_read
case 'mark_read':
    $contract_id = $_POST['contract_id'];
    
    $chatModel->markAllRead($contract_id, $_SESSION['user_id']);
    echo json_encode(['success' => true]);
    break;
```

---

## **6.2 Chat Frontend**

### **File: `views/includes/chat-widget.php` (create new)**

```html
<div id="chatWidget" class="chat-widget" data-contract-id="<?= $contract_id ?>">
    <div class="chat-header">
        <div class="chat-title">
            <strong><?= $other_party_name ?></strong>
            <span class="chat-status <?= $chat_active ? 'active' : 'inactive' ?>">
                <?= $chat_active ? '● Online' : 'Offline' ?>
            </span>
        </div>
        <div class="chat-actions">
            <button onclick="minimizeChat()">−</button>
            <button onclick="closeChat()">×</button>
        </div>
    </div>
    
    <div class="chat-messages" id="chatMessages">
        <!-- Messages loaded via JavaScript -->
    </div>
    
    <div class="chat-input-area">
        <button class="attach-button" onclick="showAttachOptions()">
            📎
        </button>
        
        <textarea 
            id="messageInput" 
            placeholder="Type a message..."
            rows="1"
            onkeypress="handleEnterKey(event)"
        ></textarea>
        
        <button class="send-button" onclick="sendMessage()">
            Send
        </button>
    </div>
    
    <div class="attach-menu" id="attachMenu" style="display: none;">
        <button onclick="attachPhoto()">📷 Photo</button>
        <button onclick="attachFile()">📎 File</button>
        <button onclick="attachLocation()">📍 Location</button>
    </div>
</div>

<input type="file" id="photoInput" accept="image/*" style="display: none;" onchange="handlePhotoUpload(event)">
<input type="file" id="fileInput" style="display: none;" onchange="handleFileUpload(event)">
```

### **File: `assets/js/chat.js` (create new)**

```javascript
class Chat {
    constructor(contractId) {
        this.contractId = contractId;
        this.lastMessageId = 0;
        this.isLoadingMessages = false;
        this.pollInterval = null;
        
        this.init();
    }
    
    init() {
        // Load initial messages
        this.loadMessages();
        
        // Start polling for new messages (every 3 seconds)
        this.startPolling();
        
        // Mark messages as read when chat is opened
        this.markAsRead();
    }
    
    async loadMessages(append = false) {
        if (this.isLoadingMessages) return;
        
        this.isLoadingMessages = true;
        
        try {
            const response = await fetch(
                `/api/chat.php?action=get_messages&contract_id=${this.contractId}&since_id=${this.lastMessageId}`
            );
            const data = await response.json();
            
            if (data.success && data.messages.length > 0) {
                this.renderMessages(data.messages, append);
                this.lastMessageId = data.messages[data.messages.length - 1].message_id;
            }
        } catch (error) {
            console.error('Failed to load messages:', error);
        } finally {
            this.isLoadingMessages = false;
        }
    }
    
    renderMessages(messages, append = false) {
        const container = document.getElementById('chatMessages');
        
        if (!append) {
            container.innerHTML = '';
        }
        
        messages.forEach(message => {
            const messageEl = this.createMessageElement(message);
            container.appendChild(messageEl);
        });
        
        // Scroll to bottom
        this.scrollToBottom();
    }
    
    createMessageElement(message) {
        const div = document.createElement('div');
        div.className = `chat-message ${message.sender_type === 'customer' ? 'sent' : 'received'}`;
        div.dataset.messageId = message.message_id;
        
        let content = '';
        
        if (message.message_type === 'text') {
            content = `<p>${this.escapeHtml(message.message_text)}</p>`;
        } else if (message.message_type === 'photo') {
            content = `
                <img src="${message.attachment_path}" 
                     alt="Photo" 
                     onclick="viewFullImage('${message.attachment_path}')"
                     class="chat-image">
            `;
        } else if (message.message_type === 'file') {
            content = `
                <a href="${message.attachment_path}" 
                   download 
                   class="chat-file">
                    📎 ${message.attachment_name}
                </a>
            `;
        }
        
        div.innerHTML = `
            ${content}
            <span class="message-time">${this.formatTime(message.sent_at)}</span>
        `;
        
        return div;
    }
    
    async sendMessage() {
        const input = document.getElementById('messageInput');
        const text = input.value.trim();
        
        if (!text) return;
        
        // Clear input
        input.value = '';
        
        // Show sending indicator
        this.showSendingIndicator();
        
        try {
            const response = await fetch('/api/chat.php?action=send_message', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    contract_id: this.contractId,
                    message_text: text
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Message will appear via polling
                this.hideSendingIndicator();
            } else {
                alert('Failed to send message');
                // Restore text
                input.value = text;
            }
        } catch (error) {
            console.error('Failed to send message:', error);
            alert('Failed to send message');
            input.value = text;
        }
    }
    
    startPolling() {
        this.pollInterval = setInterval(() => {
            this.loadMessages(true); // Append new messages
        }, 3000); // Poll every 3 seconds
    }
    
    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
        }
    }
    
    markAsRead() {
        fetch('/api/chat.php?action=mark_read', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({contract_id: this.contractId})
        });
    }
    
    scrollToBottom() {
        const container = document.getElementById('chatMessages');
        container.scrollTop = container.scrollHeight;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        
        if (date.toDateString() === now.toDateString()) {
            // Today - show time only
            return date.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'});
        } else {
            // Other day - show date and time
            return date.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}

// Initialize chat when page loads
let chatInstance;
document.addEventListener('DOMContentLoaded', function() {
    const contractId = document.getElementById('chatWidget')?.dataset.contractId;
    if (contractId) {
        chatInstance = new Chat(contractId);
    }
});
```

---

## **6.3 Chat Activation Logic**

### **File: `models/Contract.php` (add method)**

```php
public function activateChat($contract_id) {
    // Check if chat should be activated based on payment method
    $contract = $this->getById($contract_id);
    
    $should_activate = false;
    
    switch ($contract['payment_method']) {
        case 'milestone':
            // Activate after milestone plan submitted
            $plan_submitted = $this->db->query(
                "SELECT COUNT(*) as count FROM contract_milestones WHERE contract_id = ?",
                [$contract_id]
            )->fetch()['count'] > 0;
            
            $should_activate = $plan_submitted;
            break;
            
        case 'upfront_final_50_50':
        case 'upfront_final_30_70':
        case 'after_completion':
        case 'time_material':
            // Activate immediately
            $should_activate = true;
            break;
    }
    
    if ($should_activate && !$contract['chat_active']) {
        $this->db->update('contracts', [
            'chat_active' => 1,
            'chat_activated_at' => date('Y-m-d H:i:s')
        ], ['contract_id' => $contract_id]);
        
        // Send notification
        $this->notificationService->sendChatActivated($contract_id);
        
        // Timeline event
        $this->timelineModel->addEvent($contract_id, 'chat_activated', 'system');
        
        return true;
    }
    
    return false;
}
```

---

## **6.4 Chat Access Points (5 Locations)**

### **Access Point 1: Contract Details Page**

```html
<!-- Main chat button on contract page -->
<button onclick="openChat()" class="btn-primary btn-large">
    💬 Chat with <?= $other_party_name ?>
    <?php if ($unread_count > 0): ?>
        <span class="badge"><?= $unread_count ?></span>
    <?php endif; ?>
</button>
```

### **Access Point 2: Notification Bell**

```html
<!-- In notification dropdown -->
<div class="notification" onclick="openChatFromNotification(<?= $contract_id ?>)">
    💬 New message from <?= $sender_name ?>
    <span><?= $message_preview ?></span>
</div>
```

### **Access Point 3: Dashboard Widget**

```html
<!-- Quick chat access from dashboard -->
<div class="contract-widget">
    <h4><?= $contract_number ?></h4>
    <button onclick="openChat(<?= $contract_id ?>)" class="btn-small">
        💬 Chat
        <?php if ($unread > 0): ?>
            <span class="badge"><?= $unread ?></span>
        <?php endif; ?>
    </button>
</div>
```

### **Access Point 4: Email Links**

```html
<!-- In notification emails -->
<a href="https://fixlanka.lk/contract/<?= $contract_id ?>?open_chat=1" 
   style="background: #0066cc; color: white; padding: 10px 20px;">
    💬 Reply via Chat
</a>
```

### **Access Point 5: Mobile App / Dedicated Chat Tab**

*(Implementation depends on mobile app framework)*

---

## **6.5 Testing Tasks**

- [ ] Test chat message sending
- [ ] Test message receiving (polling)
- [ ] Test photo upload and display
- [ ] Test file upload and download
- [ ] Test message history loading
- [ ] Test read/unread status
- [ ] Test chat activation (milestone plan submitted)
- [ ] Test chat activation (immediate for other methods)
- [ ] Test all 5 access points
- [ ] Test unread count badges
- [ ] Test notifications for new messages
- [ ] Test chat with multiple contracts
- [ ] Test XSS protection (message escaping)
- [ ] Test file upload validation

---

## **6.6 Deliverables for Phase 6**

✅ Chat interface built  
✅ Real-time messaging working (polling)  
✅ Photo/file sharing functional  
✅ Chat activation logic correct  
✅ All 5 access points working  
✅ Unread counts displayed  
✅ Notifications sent  
✅ Tests passing  

---

# 🔷 **PHASE 7: MILESTONE MANAGEMENT (Issue #6)**

**Duration:** Week 11-12  
**Priority:** High (for milestone-based contracts)  
**Complexity:** High

## **Objectives**
- Implement two-stage milestone process
- Build milestone plan submission form
- Create customer review and approval flow
- Implement milestone completion workflow
- Build payment release automation

---

## **7.1 Milestone Plan Submission (Company)**

### **File: `views/company/milestone-plan-form.php` (create new)**

```html
<form id="milestonePlanForm" onsubmit="submitMilestonePlan(event)">
    <input type="hidden" name="contract_id" value="<?= $contract_id ?>">
    
    <div class="form-header">
        <h2>Create Milestone Plan</h2>
        <p>Deadline: <span class="deadline-countdown"></span></p>
    </div>
    
    <div class="plan-summary">
        <div>Total Budget: LKR <?= number_format($contract['budget_amount'], 2) ?></div>
        <div>Duration: <?= $contract['duration_days'] ?> days</div>
        <div>Recommended: 3-5 milestones</div>
    </div>
    
    <div id="milestonesContainer">
        <!-- Milestone 1 -->
        <div class="milestone-form" data-milestone-number="1">
            <h3>Milestone 1</h3>
            
            <div class="form-group">
                <label>Milestone Name *</label>
                <input type="text" name="milestones[1][name]" required 
                       placeholder="e.g., Material Procurement">
            </div>
            
            <div class="form-group">
                <label>Description *</label>
                <textarea name="milestones[1][description]" required 
                          rows="3"
                          placeholder="Describe what will be done in this milestone"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Payment Percentage *</label>
                    <input type="number" name="milestones[1][payment_percentage]" 
                           min="1" max="100" step="0.01" required
                           oninput="calculateAmount(1)">
                    <span>%</span>
                </div>
                
                <div class="form-group">
                    <label>Payment Amount</label>
                    <input type="text" id="milestone_1_amount" readonly>
                </div>
            </div>
            
            <div class="form-group">
                <label>Duration (days) *</label>
                <input type="number" name="milestones[1][duration_days]" 
                       min="1" required
                       oninput="calculateTotalDuration()">
            </div>
            
            <div class="form-group">
                <label>Deliverables *</label>
                <textarea name="milestones[1][deliverables]" required 
                          rows="3"
                          placeholder="List what will be delivered (one per line)"></textarea>
            </div>
            
            <div class="form-group">
                <label>Dependencies</label>
                <select name="milestones[1][dependencies][]" multiple>
                    <option value="">None (can start immediately)</option>
                </select>
            </div>
        </div>
        
        <!-- More milestones added dynamically -->
    </div>
    
    <div class="plan-validation">
        <div class="validation-item">
            <span>Total Payment Percentage:</span>
            <span id="totalPercentage" class="validation-value">0%</span>
            <span id="percentageStatus"></span>
        </div>
        <div class="validation-item">
            <span>Total Duration:</span>
            <span id="totalDuration" class="validation-value">0 days</span>
            <span id="durationStatus"></span>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="button" onclick="addMilestone()">+ Add Milestone</button>
        <button type="button" onclick="saveDraft()">Save Draft</button>
        <button type="submit" class="btn-primary">Submit Plan</button>
    </div>
</form>

<script src="/assets/js/milestone-plan.js"></script>
```

### **File: `assets/js/milestone-plan.js` (create new)**

```javascript
let milestoneCount = 1;
const totalBudget = parseFloat(document.getElementById('totalBudget').value);
const totalDays = parseInt(document.getElementById('totalDays').value);

function addMilestone() {
    milestoneCount++;
    
    // Create new milestone form (similar structure)
    // Append to container
    // Update dependencies dropdowns
}

function calculateAmount(milestoneNumber) {
    const percentage = parseFloat(document.querySelector(`[name="milestones[${milestoneNumber}][payment_percentage]"]`).value) || 0;
    const amount = (totalBudget * percentage / 100).toFixed(2);
    document.getElementById(`milestone_${milestoneNumber}_amount`).value = `LKR ${amount}`;
    
    validateTotalPercentage();
}

function validateTotalPercentage() {
    let total = 0;
    document.querySelectorAll('[name$="[payment_percentage]"]').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    
    document.getElementById('totalPercentage').textContent = total.toFixed(2) + '%';
    
    const status = document.getElementById('percentageStatus');
    if (Math.abs(total - 100) < 0.01) {
        status.textContent = '✅';
        status.className = 'valid';
    } else {
        status.textContent = '❌ Must equal 100%';
        status.className = 'invalid';
    }
    
    return Math.abs(total - 100) < 0.01;
}

function calculateTotalDuration() {
    let total = 0;
    document.querySelectorAll('[name$="[duration_days]"]').forEach(input => {
        total += parseInt(input.value) || 0;
    });
    
    document.getElementById('totalDuration').textContent = total + ' days';
    
    const status = document.getElementById('durationStatus');
    if (total == totalDays) {
        status.textContent = '✅';
        status.className = 'valid';
    } else {
        status.textContent = `⚠️ Should be ${totalDays} days`;
        status.className = 'warning';
    }
}

async function submitMilestonePlan(event) {
    event.preventDefault();
    
    // Validate
    if (!validateTotalPercentage()) {
        alert('Payment percentages must total 100%');
        return;
    }
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/api/milestones.php?action=submit_plan', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Milestone plan submitted successfully!');
            window.location.href = '/views/company/contract-details.php?id=' + data.contract_id;
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Failed to submit plan');
        console.error(error);
    }
}
```

---

## **7.2 Milestone Plan Review (Customer)**

### **File: `views/customer/milestone-plan-review.php` (create new)**

```html
<div class="plan-review-container">
    <h2>Review Milestone Plan</h2>
    
    <div class="plan-info">
        <p>ABC Construction has submitted a milestone plan for your review.</p>
        <p>Please carefully review each milestone before approving.</p>
    </div>
    
    <div class="milestones-review">
        <?php foreach ($milestones as $i => $milestone): ?>
        <div class="milestone-card">
            <div class="milestone-header">
                <h3>Milestone <?= $milestone['milestone_number'] ?>: <?= htmlspecialchars($milestone['milestone_name']) ?></h3>
                <div class="milestone-payment">
                    <strong>LKR <?= number_format($milestone['payment_amount'], 2) ?></strong>
                    <span>(<?= $milestone['payment_percentage'] ?>%)</span>
                </div>
            </div>
            
            <div class="milestone-body">
                <div class="milestone-section">
                    <label>Description:</label>
                    <p><?= nl2br(htmlspecialchars($milestone['milestone_description'])) ?></p>
                </div>
                
                <div class="milestone-section">
                    <label>Duration:</label>
                    <p><?= $milestone['duration_days'] ?> days</p>
                </div>
                
                <div class="milestone-section">
                    <label>Deliverables:</label>
                    <ul>
                        <?php foreach (explode("\n", $milestone['deliverables']) as $deliverable): ?>
                            <?php if (trim($deliverable)): ?>
                                <li><?= htmlspecialchars(trim($deliverable)) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <?php if ($milestone['dependencies']): ?>
                    <div class="milestone-section">
                        <label>Dependencies:</label>
                        <p><?= htmlspecialchars($milestone['dependencies']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="plan-summary">
        <div class="summary-item">
            <label>Total Milestones:</label>
            <span><?= count($milestones) ?></span>
        </div>
        <div class="summary-item">
            <label>Total Budget:</label>
            <span>LKR <?= number_format($contract['budget_amount'], 2) ?></span>
        </div>
        <div class="summary-item">
            <label>Total Duration:</label>
            <span><?= array_sum(array_column($milestones, 'duration_days')) ?> days</span>
        </div>
    </div>
    
    <div class="chat-prompt">
        <p>💬 Have questions? <a href="#" onclick="openChat()">Chat with ABC Construction</a></p>
    </div>
    
    <div class="review-actions">
        <button onclick="requestChanges()" class="btn-secondary">
            📝 Request Changes
        </button>
        <button onclick="rejectPlan()" class="btn-danger">
            ❌ Reject Plan
        </button>
        <button onclick="approvePlan()" class="btn-success">
            ✅ Approve & Activate Contract
        </button>
    </div>
</div>

<script>
async function approvePlan() {
    if (!confirm('Approve this milestone plan and activate the contract?')) {
        return;
    }
    
    const response = await fetch('/api/milestones.php?action=approve_plan', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({contract_id: <?= $contract_id ?>})
    });
    
    const data = await response.json();
    
    if (data.success) {
        alert('Plan approved! Contract is now active.');
        window.location.reload();
    } else {
        alert('Error: ' + data.message);
    }
}
</script>
```

---

## **7.3 Milestone Completion & Payment**

### **Flow:**

1. Company works on Milestone 1
2. Company marks Milestone 1 complete (uploads photos, notes)
3. Customer receives notification
4. Customer reviews deliverables
5. Customer approves milestone
6. Payment automatically released from escrow
7. Milestone 2 becomes active
8. Repeat for all milestones

### **File: `api/milestones.php` (add endpoint)**

```php
// POST /api/milestones.php?action=approve_milestone
case 'approve_milestone':
    $milestone_id = $_POST['milestone_id'];
    $customer_id = $_SESSION['user_id'];
    
    try {
        $this->db->beginTransaction();
        
        // 1. Get milestone and contract
        $milestone = $this->milestoneModel->getById($milestone_id);
        $contract = $this->contractModel->getById($milestone['contract_id']);
        
        // 2. Verify customer owns contract
        if ($contract['customer_id'] != $customer_id) {
            throw new Exception("Unauthorized");
        }
        
        // 3. Update milestone status
        $this->milestoneModel->update($milestone_id, [
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'reviewed_by' => $customer_id
        ]);
        
        // 4. Release payment
        $payment = $this->paymentModel->getByMilestone($milestone_id);
        $this->paymentModel->releaseFromEscrow($payment['payment_id']);
        
        // 5. Activate next milestone
        $next_milestone = $this->milestoneModel->getNextMilestone($milestone['contract_id'], $milestone['milestone_number']);
        if ($next_milestone) {
            $this->milestoneModel->update($next_milestone['milestone_id'], [
                'status' => 'active',
                'is_active' => 1,
                'start_date' => date('Y-m-d')
            ]);
        }
        
        // 6. Update contract progress
        $this->contractModel->updateProgress($contract['contract_id']);
        
        // 7. Notifications
        $this->notificationService->sendMilestoneApproved($milestone_id);
        $this->notificationService->sendPaymentReleased($payment['payment_id']);
        
        // 8. Timeline events
        $this->timelineModel->addEvent($contract['contract_id'], 'milestone_approved', 'customer', "Milestone {$milestone['milestone_number']} approved");
        
        $this->db->commit();
        
        return ['success' => true];
        
    } catch (Exception $e) {
        $this->db->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
    break;
```

---

## **7.4 Testing Tasks**

- [ ] Test milestone plan form validation
- [ ] Test percentage totaling 100%
- [ ] Test duration calculation
- [ ] Test plan submission
- [ ] Test chat activation after submission
- [ ] Test customer review interface
- [ ] Test plan approval
- [ ] Test plan rejection
- [ ] Test request changes flow
- [ ] Test milestone completion
- [ ] Test milestone approval
- [ ] Test payment release automation
- [ ] Test next milestone activation
- [ ] Test progress calculation
- [ ] Test final milestone completion

---

## **7.5 Deliverables for Phase 7**

✅ Milestone plan form built  
✅ Plan submission working  
✅ Customer review interface complete  
✅ Approval/rejection workflow functional  
✅ Milestone completion flow working  
✅ Payment release automated  
✅ Progress tracking accurate  
✅ Tests passing  

---

# 🔷 **PHASE 8: ESCROW & PAYMENT PROTECTION**

**Duration:** Week 13-14  
**Priority:** Critical  
**Complexity:** Very High

## **Objectives**
- Implement escrow payment holding system
- Build work start verification flow
- Create 7-day quality guarantee
- Implement payment release logic
- Build dispute resolution framework

---

*(This phase is extremely complex and involves payment processing integration)*

## **8.1 Escrow System Overview**

**Escrow Account Setup:**
- Create separate database table for escrow transactions
- Integrate with payment gateway (Stripe, PayPal, local banks)
- Implement funds holding mechanism
- Build release/refund workflows
- Set up compliance and regulations

### **Table: `escrow_accounts`**

```sql
CREATE TABLE escrow_accounts (
    escrow_id INT AUTO_INCREMENT PRIMARY KEY,
    payment_id INT NOT NULL,
    contract_id INT NOT NULL,
    
    -- Amount
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'LKR',
    
    -- Status
    status ENUM('holding', 'released', 'refunded', 'disputed') DEFAULT 'holding',
    
    -- Holding details
    held_at DATETIME NOT NULL,
    release_condition VARCHAR(255) NOT NULL,
    auto_release_date DATE NULL,
    
    -- Release/refund
    released_at DATETIME NULL,
    released_to INT NULL, -- company user_id
    refunded_at DATETIME NULL,
    refunded_to INT NULL, -- customer user_id
    refund_reason TEXT NULL,
    
    -- Verification
    verification_required BOOLEAN DEFAULT FALSE,
    verification_deadline DATETIME NULL,
    verified_at DATETIME NULL,
    verified_by INT NULL,
    
    -- Transaction IDs
    payment_transaction_id VARCHAR(100) NULL,
    release_transaction_id VARCHAR(100) NULL,
    refund_transaction_id VARCHAR(100) NULL,
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (payment_id) REFERENCES contract_payments(payment_id),
    FOREIGN KEY (contract_id) REFERENCES contracts(contract_id),
    
    INDEX idx_status (status),
    INDEX idx_contract (contract_id)
);
```

---

## **8.2 Work Start Verification**

### **File: `views/company/work-start-verification.php` (create new)**

```html
<form id="workStartForm" onsubmit="submitWorkStartProof(event)">
    <h2>Submit Work Start Confirmation</h2>
    
    <div class="form-group">
        <label>Start Date *</label>
        <input type="date" name="start_date" value="<?= date('Y-m-d') ?>" required>
        <button type="button" onclick="setToday()">Today</button>
    </div>
    
    <div class="form-group">
        <label>Start Time *</label>
        <input type="time" name="start_time" required>
    </div>
    
    <div class="form-group">
        <label>On-Site Photo * (Required for verification)</label>
        <input type="file" name="site_photo" accept="image/*" required onchange="previewPhoto(event)">
        
        <div class="photo-requirements">
            <p><strong>Photo must show:</strong></p>
            <ul>
                <li>Your team on-site</li>
                <li>Work area/location</li>
                <li>Today's date visible (newspaper, phone screen, etc.)</li>
            </ul>
        </div>
        
        <div id="photoPreview" style="display: none;">
            <img id="previewImage" style="max-width: 300px;">
            <div class="photo-verification">
                <span id="timestampBadge">📅 Timestamp: Verifying...</span>
                <span id="locationBadge">📍 Location: Verifying...</span>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <label>Work Description *</label>
        <textarea name="work_description" rows="4" required 
                  placeholder="Describe what work you started today"></textarea>
    </div>
    
    <div class="confirmation-checkboxes">
        <div>
            <input type="checkbox" id="confirm1" required>
            <label for="confirm1">I confirm that work has physically started on-site today</label>
        </div>
        <div>
            <input type="checkbox" id="confirm2" required>
            <label for="confirm2">I understand that false verification may result in account suspension</label>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn-primary">Submit Work Start Confirmation</button>
    </div>
</form>

<script>
// Get location
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(position => {
        document.getElementById('latitude').value = position.coords.latitude;
        document.getElementById('longitude').value = position.coords.longitude;
        document.getElementById('locationBadge').textContent = '📍 Location: Verified ✅';
    });
}

// Photo preview with EXIF data extraction
function previewPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('photoPreview').style.display = 'block';
            
            // Extract EXIF data (timestamp, GPS)
            EXIF.getData(file, function() {
                const timestamp = EXIF.getTag(this, "DateTime");
                const lat = EXIF.getTag(this, "GPSLatitude");
                const lng = EXIF.getTag(this, "GPSLongitude");
                
                if (timestamp) {
                    document.getElementById('timestampBadge').textContent = '📅 Timestamp: ' + timestamp + ' ✅';
                }
                
                if (lat && lng) {
                    document.getElementById('locationBadge').textContent = '📍 Location: GPS verified ✅';
                }
            });
        };
        reader.readAsDataURL(file);
    }
}
</script>
```

### **File: `api/work-verification.php` (create new)**

```php
// POST /api/work-verification.php?action=submit_proof
case 'submit_proof':
    $contract_id = $_POST['contract_id'];
    $start_date = $_POST['start_date'];
    $start_time = $_POST['start_time'];
    $work_description = $_POST['work_description'];
    $photo = $_FILES['site_photo'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    
    // 1. Upload and verify photo
    $photo_path = $this->uploadAndVerifyPhoto($photo);
    
    // 2. Store verification
    $this->contractModel->update($contract_id, [
        'actual_start_date' => $start_date,
        'work_start_proof_photo' => $photo_path,
        'work_start_proof_location' => $latitude . ',' . $longitude,
        'work_start_submitted_at' => date('Y-m-d H:i:s')
    ]);
    
    // 3. Notify customer for verification
    $this->notificationService->sendWorkStartVerificationRequest($contract_id);
    
    // 4. Set 24-hour auto-verify deadline
    $this->scheduleAutoVerification($contract_id, '+24 hours');
    
    echo json_encode(['success' => true]);
    break;
```

---

## **8.3 Customer Verification (24-Hour Window)**

### **File: `views/customer/verify-work-start.php` (create new)**

```html
<div class="verification-request">
    <h2>✅ Company Started Work - Verify Now</h2>
    
    <div class="alert alert-info">
        <p><strong>ABC Construction</strong> has submitted proof that work has started.</p>
        <p>Please verify to release the upfront payment from escrow.</p>
    </div>
    
    <div class="verification-proof">
        <h3>Submitted Proof</h3>
        
        <div class="proof-photo">
            <img src="<?= $proof_photo_url ?>" alt="Work start photo">
            <div class="photo-metadata">
                <span>📅 <?= $submitted_timestamp ?></span>
                <span>📍 <?= $location_address ?></span>
            </div>
        </div>
        
        <div class="proof-details">
            <div class="detail-item">
                <label>Start Date:</label>
                <span><?= $start_date ?></span>
            </div>
            <div class="detail-item">
                <label>Start Time:</label>
                <span><?= $start_time ?></span>
            </div>
            <div class="detail-item">
                <label>Work Description:</label>
                <p><?= nl2br(htmlspecialchars($work_description)) ?></p>
            </div>
        </div>
    </div>
    
    <div class="escrow-info">
        <h3>🔒 Escrow Payment Pending Your Verification</h3>
        <p class="amount">LKR <?= number_format($escrow_amount, 2) ?></p>
        <p>This payment is currently held in escrow and will be released to the company after you verify.</p>
    </div>
    
    <div class="verification-timer">
        <p><strong>⏰ You have 24 hours to verify</strong></p>
        <p class="countdown" id="verificationCountdown">23:45:12</p>
        <p class="note">If you don't respond within 24 hours, work will be automatically verified.</p>
    </div>
    
    <div class="verification-actions">
        <h3>Choose an option:</h3>
        
        <div class="action-option option-confirm">
            <h4>✅ OPTION 1: Confirm Work Started</h4>
            <p>Yes, I can confirm that work has physically started as described.</p>
            <button onclick="confirmWorkStarted()" class="btn-success btn-large">
                YES, WORK STARTED - Release Payment
            </button>
            <p class="note">Payment will be released to company immediately.</p>
        </div>
        
        <div class="action-option option-issue">
            <h4>❌ OPTION 2: Report Issue</h4>
            <p>No, work has not started or there's a problem.</p>
            <button onclick="reportIssue()" class="btn-danger">
                NO, DIDN'T START / REPORT ISSUE
            </button>
            <p class="note">Payment will remain in escrow. Support will investigate.</p>
        </div>
        
        <div class="action-option option-wait">
            <h4>🕐 OPTION 3: Need More Time</h4>
            <p>I need more time to verify (will check later today).</p>
            <button onclick="waitToVerify()" class="btn-secondary">
                I'LL VERIFY LATER
            </button>
            <p class="note">You have <span class="countdown-inline"></span> remaining.</p>
        </div>
    </div>
    
    <div class="verification-tips">
        <h4>💡 Verification Tips:</h4>
        <ul>
            <li>Check if the photo shows your actual project location</li>
            <li>Verify the date matches the scheduled start date</li>
            <li>Contact company via chat if you need clarification</li>
            <li>If you're unsure, it's better to verify in person</li>
        </ul>
    </div>
    
    <div class="chat-link">
        <button onclick="openChat()">💬 Chat with Company</button>
    </div>
</div>

<script>
async function confirmWorkStarted() {
    if (!confirm('Confirm that work has started and release payment from escrow?')) {
        return;
    }
    
    const response = await fetch('/api/work-verification.php?action=confirm', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({contract_id: <?= $contract_id ?>})
    });
    
    const data = await response.json();
    
    if (data.success) {
        alert('Work start confirmed! Payment released from escrow.');
        window.location.reload();
    }
}

async function reportIssue() {
    const reason = prompt('Please describe the issue:');
    if (!reason) return;
    
    const response = await fetch('/api/work-verification.php?action=report_issue', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            contract_id: <?= $contract_id ?>,
            issue_reason: reason
        })
    });
    
    const data = await response.json();
    
    if (data.success) {
        alert('Issue reported. Support will investigate. Payment remains in escrow.');
        window.location.reload();
    }
}
</script>
```

---

## **8.4 7-Day Quality Guarantee (100% Final Payment)**

### **File: `views/customer/quality-guarantee.php` (create new)**

```html
<div class="quality-guarantee-period">
    <h2>🔒 7-Day Quality Guarantee Period</h2>
    
    <div class="guarantee-info">
        <p>Your payment of <strong>LKR <?= number_format($payment_amount, 2) ?></strong> is held in escrow for 7 days.</p>
        <p>During this time, you can:</p>
        <ul>
            <li>Thoroughly inspect the completed work</li>
            <li>Test functionality</li>
            <li>Request fixes if issues found (FREE)</li>
            <li>Approve when fully satisfied</li>
        </ul>
    </div>
    
    <div class="guarantee-timer">
        <h3>Time Remaining in Guarantee Period</h3>
        <div class="countdown-large" id="guaranteeCountdown">
            6 days 15 hours 23 minutes
        </div>
        <p>Ends: <?= date('F j, Y g:i A', strtotime('+7 days', strtotime($payment_date))) ?></p>
    </div>
    
    <div class="inspection-checklist">
        <h3>📋 Inspection Checklist</h3>
        <div class="checklist-items">
            <label><input type="checkbox"> Visual inspection complete</label>
            <label><input type="checkbox"> Functionality tested</label>
            <label><input type="checkbox"> Quality meets expectations</label>
            <label><input type="checkbox"> All deliverables received</label>
            <label><input type="checkbox"> Documentation provided</label>
            <label><input type="checkbox"> Clean-up completed</label>
            <label><input type="checkbox"> No defects found</label>
            <label><input type="checkbox"> Satisfied with result</label>
        </div>
    </div>
    
    <div class="guarantee-actions">
        <h3>Choose an action:</h3>
        
        <div class="action-card action-approve">
            <h4>✅ Approve & Release Payment</h4>
            <p>Everything looks good. Release payment to company.</p>
            <button onclick="approveQuality()" class="btn-success btn-large">
                APPROVE WORK & RELEASE PAYMENT
            </button>
        </div>
        
        <div class="action-card action-fixes">
            <h4>🔧 Request Fixes</h4>
            <p>Found issues that need correction (FREE during guarantee period).</p>
            <button onclick="requestFixes()" class="btn-warning">
                REQUEST FIXES
            </button>
        </div>
        
        <div class="action-card action-dispute">
            <h4>⚠️ Raise Dispute</h4>
            <p>Serious issues that can't be easily fixed.</p>
            <button onclick="raiseDispute()" class="btn-danger">
                RAISE DISPUTE
            </button>
        </div>
        
        <div class="action-card action-wait">
            <h4>⏳ Continue Inspecting</h4>
            <p>Need more time to inspect (payment auto-releases after 7 days).</p>
            <p class="note">No action needed. Keep inspecting.</p>
        </div>
    </div>
</div>

<script>
async function approveQuality() {
    if (!confirm('Approve work quality and release payment from escrow?')) {
        return;
    }
    
    const response = await fetch('/api/quality-guarantee.php?action=approve', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({contract_id: <?= $contract_id ?>})
    });
    
    const data = await response.json();
    
    if (data.success) {
        alert('Quality approved! Payment released from escrow.');
        window.location.href = '/views/customer/contract-complete.php?id=<?= $contract_id ?>';
    }
}

async function requestFixes() {
    const issues = prompt('Please describe what needs to be fixed:');
    if (!issues) return;
    
    const response = await fetch('/api/quality-guarantee.php?action=request_fixes', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            contract_id: <?= $contract_id ?>,
            issues: issues
        })
    });
    
    const data = await response.json();
    
    if (data.success) {
        alert('Fix request sent to company. Guarantee period extended until resolved.');
        window.location.reload();
    }
}
</script>
```

---

## **8.5 Automatic Payment Release**

### **File: `cron/auto-release-escrow.php` (create new)**

```php
<?php
// Run daily via cron

require_once '../config/database.php';
require_once '../models/EscrowAccount.php';
require_once '../services/PaymentService.php';

$db = new Database();
$escrowModel = new EscrowAccount($db);
$paymentService = new PaymentService($db);

// 1. Auto-verify work start (24 hours passed)
$pending_verifications = $db->query("
    SELECT c.*, e.escrow_id, e.payment_id
    FROM contracts c
    JOIN contract_payments p ON c.contract_id = p.contract_id
    JOIN escrow_accounts e ON p.payment_id = e.payment_id
    WHERE c.status = 'pending_work_start'
    AND c.work_start_submitted_at IS NOT NULL
    AND c.work_started_verified = 0
    AND e.verification_deadline < NOW()
")->fetchAll();

foreach ($pending_verifications as $contract) {
    // Auto-verify
    $db->update('contracts', [
        'work_started_verified' => 1,
        'work_started_verified_at' => date('Y-m-d H:i:s'),
        'status' => 'active'
    ], ['contract_id' => $contract['contract_id']]);
    
    // Release from escrow
    $paymentService->releaseFromEscrow($contract['escrow_id'], 'auto_verified_24hr');
    
    echo "Auto-verified and released: Contract " . $contract['contract_number'] . "\n";
}

// 2. Auto-release 7-day guarantee (7 days passed)
$guarantee_expired = $db->query("
    SELECT e.*, p.contract_id
    FROM escrow_accounts e
    JOIN contract_payments p ON e.payment_id = p.payment_id
    WHERE e.status = 'holding'
    AND e.auto_release_date < CURDATE()
    AND e.release_condition = '7_day_guarantee'
")->fetchAll();

foreach ($guarantee_expired as $escrow) {
    // Auto-approve and release
    $paymentService->releaseFromEscrow($escrow['escrow_id'], 'auto_release_7_days');
    
    echo "Auto-released 7-day guarantee: Escrow " . $escrow['escrow_id'] . "\n";
}

echo "Auto-release cron completed\n";
```

---

## **8.6 Testing Tasks**

- [ ] Test escrow account creation
- [ ] Test payment holding in escrow
- [ ] Test work start proof submission
- [ ] Test photo upload and verification
- [ ] Test location verification
- [ ] Test customer verification flow
- [ ] Test 24-hour auto-verification
- [ ] Test payment release after verification
- [ ] Test issue reporting (payment stays in escrow)
- [ ] Test 7-day guarantee period
- [ ] Test quality approval and release
- [ ] Test fix requests (extend guarantee)
- [ ] Test auto-release after 7 days
- [ ] Test dispute handling
- [ ] Test refund processing
- [ ] Test all escrow states and transitions

---

## **8.7 Deliverables for Phase 8**

✅ Escrow system implemented  
✅ Payment holding working  
✅ Work verification flow complete  
✅ 24-hour verification window functional  
✅ Auto-verification working  
✅ 7-day quality guarantee implemented  
✅ Auto-release after 7 days working  
✅ Dispute framework in place  
✅ Refund processing functional  
✅ Tests passing  

---

# 🔷 **PHASE 9: NOTIFICATIONS & UI POLISH**

**Duration:** Week 15  
**Priority:** High  
**Complexity:** Medium

## **Objectives**
- Implement comprehensive notification system
- Polish UI/UX across all pages
- Add loading states and error handling
- Implement responsive design
- Optimize performance

---

## **9.1 Notification System**

### **Email Notifications:**
- Contract created
- Quotation accepted
- Undo deadline reminder (1 hour before)
- Milestone plan submitted
- Chat messages received
- Work start verification request
- Payment released
- Milestone completed
- Quality guarantee reminders
- Auto-release notifications

### **SMS Notifications:**
- Critical events only (reduce spam)
- Payment releases
- Verification requests
- Deadlines approaching

### **In-App Notifications:**
- Real-time updates via notification bell
- Toast messages for actions
- Progress indicators

---

## **9.2 UI/UX Polish**

### **Consistent Design System:**
- Color palette
- Typography
- Button styles
- Form elements
- Card layouts
- Icons

### **Loading States:**
- Skeleton screens
- Spinners
- Progress bars
- Disabled states

### **Error Handling:**
- User-friendly error messages
- Form validation feedback
- Network error handling
- Retry mechanisms

### **Responsive Design:**
- Mobile-first approach
- Tablet optimization
- Desktop enhancements
- Touch-friendly interactions

---

## **9.3 Performance Optimization**

- Database query optimization
- Caching strategy
- Image optimization
- Lazy loading
- Code minification
- CDN integration

---

## **9.4 Deliverables for Phase 9**

✅ All notifications working  
✅ UI/UX polished  
✅ Responsive design complete  
✅ Loading states added  
✅ Error handling robust  
✅ Performance optimized  

---

# 🔷 **PHASE 10: TESTING & DEPLOYMENT**

**Duration:** Week 16  
**Priority:** Critical  
**Complexity:** High

## **Objectives**
- Comprehensive testing
- Bug fixing
- User acceptance testing
- Deployment preparation
- Production launch

---

## **10.1 Testing Checklist**

### **Unit Tests:**
- [ ] Model classes
- [ ] Controller methods
- [ ] Helper functions
- [ ] Validation logic

### **Integration Tests:**
- [ ] API endpoints
- [ ] Database operations
- [ ] Payment processing
- [ ] File uploads
- [ ] Email sending

### **End-to-End Tests:**
- [ ] Complete user journeys
- [ ] All 5 payment methods
- [ ] Undo window
- [ ] Chat system
- [ ] Escrow flows
- [ ] Milestone workflows

### **Cross-Browser Testing:**
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge

### **Device Testing:**
- [ ] Mobile (iOS)
- [ ] Mobile (Android)
- [ ] Tablet
- [ ] Desktop

### **Performance Testing:**
- [ ] Load testing
- [ ] Stress testing
- [ ] Database performance
- [ ] API response times

### **Security Testing:**
- [ ] SQL injection
- [ ] XSS attacks
- [ ] CSRF protection
- [ ] File upload validation
- [ ] Authentication/authorization
- [ ] Payment security

---

## **10.2 User Acceptance Testing (UAT)**

### **Test Scenarios:**

**Scenario 1: Milestone-Based Contract**
- Customer posts request
- Company sends quotation (milestone, flexible budget)
- Customer accepts
- Test 24-hour undo window
- Company submits milestone plan
- Customer reviews and approves
- Chat activates
- Complete all 4 milestones
- Test escrow release per milestone
- Customer rates company

**Scenario 2: 50-50 Upfront+Final**
- Customer accepts quotation (50-50 split)
- Customer makes upfront payment → Escrow
- Company submits work start proof
- Customer verifies within 24 hours
- Escrow releases upfront payment
- Company completes work
- Customer makes final payment → Escrow
- Customer approves quality
- Escrow releases final payment

**Scenario 3: After Completion (100%)**
- Customer accepts quotation (100% final)
- No upfront payment
- Company works on trust
- Company completes work
- Customer makes full payment → Escrow
- 7-day quality guarantee begins
- Customer inspects for 7 days
- Customer approves quality
- Escrow releases payment

**Scenario 4: Time & Material**
- Customer accepts quotation (hourly rate)
- Company clocks in/out daily
- Customer monitors time logs real-time
- Friday: Weekly invoice generated
- Customer reviews and approves invoice
- Payment processed
- Continue for 3 weeks
- Project completes

**Scenario 5: Undo Testing**
- Customer accepts quotation
- Customer undos within 24 hours
- Verify contract cancelled
- Verify request reopened
- Verify quotation available again
- Verify company notified

---

## **10.3 Deployment Checklist**

### **Pre-Deployment:**
- [ ] All tests passing
- [ ] UAT approved
- [ ] Documentation complete
- [ ] Database backup created
- [ ] Environment variables configured
- [ ] SSL certificate installed
- [ ] Payment gateway configured (production)
- [ ] Email service configured (production)
- [ ] SMS service configured (production)
- [ ] Cron jobs scheduled
- [ ] Monitoring tools set up
- [ ] Error logging configured

### **Deployment:**
- [ ] Deploy to staging environment
- [ ] Smoke tests on staging
- [ ] Deploy to production
- [ ] Smoke tests on production
- [ ] Monitor for errors
- [ ] Performance monitoring

### **Post-Deployment:**
- [ ] Monitor user activity
- [ ] Track error rates
- [ ] Monitor payment processing
- [ ] Check notification delivery
- [ ] Review performance metrics
- [ ] Gather user feedback

---

## **10.4 Deliverables for Phase 10**

✅ All tests passing  
✅ UAT completed and approved  
✅ Bugs fixed  
✅ Deployment successful  
✅ Production monitoring active  
✅ System stable and performant  

---

# 📊 **IMPLEMENTATION SUMMARY**

## **Timeline Recap**

| Phase | Duration | Focus | Priority |
|-------|----------|-------|----------|
| 1 | Week 1-2 | Database & Models | Critical |
| 2 | Week 3-4 | Contract Creation | Critical |
| 3 | Week 5 | Budget Flexibility | High |
| 4 | Week 6-7 | Payment Terms | Critical |
| 5 | Week 8 | 24-Hour Undo | High |
| 6 | Week 9-10 | Chat System | High |
| 7 | Week 11-12 | Milestone Management | High |
| 8 | Week 13-14 | Escrow & Protection | Critical |
| 9 | Week 15 | Notifications & Polish | High |
| 10 | Week 16 | Testing & Deployment | Critical |

**Total:** 16 weeks (4 months)

---

## **Complexity Assessment**

**Most Complex Phases:**
1. **Phase 8 (Escrow)** - Payment processing, verification, security
2. **Phase 4 (Payment Terms)** - 5 different workflows
3. **Phase 7 (Milestones)** - Two-stage process, automation
4. **Phase 6 (Chat)** - Real-time messaging, file handling
5. **Phase 10 (Testing)** - Comprehensive coverage

**Simplest Phases:**
1. **Phase 3 (Budget Flexibility)** - Straightforward calculations
2. **Phase 5 (Undo Window)** - Clear logic
3. **Phase 9 (Polish)** - Refinement work

---

## **Resource Requirements**

### **Team Composition:**
- **1 Full-Stack Developer** (PHP, JavaScript, MySQL)
- **1 Frontend Developer** (HTML, CSS, JavaScript, responsive design)
- **1 QA Engineer** (Testing, bug tracking)
- **1 Project Manager** (Coordination, timeline management)

### **Part-Time:**
- **1 UI/UX Designer** (Design system, mockups) - 2 weeks
- **1 Payment Integration Specialist** (Escrow setup) - 2 weeks
- **1 DevOps Engineer** (Deployment, cron jobs) - 1 week

---

## **Risk Mitigation**

### **High-Risk Areas:**

1. **Payment/Escrow Integration**
   - **Risk:** Payment gateway issues, compliance problems
   - **Mitigation:** Use established gateways, test thoroughly, have backup options

2. **Photo Verification**
   - **Risk:** EXIF data can be faked
   - **Mitigation:** Multiple verification points, manual review option, GPS cross-check

3. **Real-Time Chat**
   - **Risk:** Performance issues with polling
   - **Mitigation:** Optimize queries, consider WebSockets upgrade later

4. **Cron Job Reliability**
   - **Risk:** Cron jobs may not run
   - **Mitigation:** Monitoring, alerts, backup manual processes

5. **Database Performance**
   - **Risk:** Slow queries with large data
   - **Mitigation:** Proper indexing, query optimization, caching

---

## **Success Metrics**

### **Technical Metrics:**
- All automated tests passing (>90% coverage)
- Page load time < 3 seconds
- API response time < 500ms
- Zero critical bugs in production
- 99.9% uptime

### **Business Metrics:**
- Contract completion rate > 80%
- Dispute rate < 5%
- Customer satisfaction > 4.5/5
- Escrow release time < 48 hours
- Undo rate < 10%

---

## **Post-Launch Enhancements**

### **Phase 11 (Future):**
- Mobile app (iOS/Android)
- WebSocket real-time chat
- Advanced analytics dashboard
- AI-powered milestone suggestions
- Automated quality inspection (photo analysis)
- Multi-currency support
- International payment gateways
- Review system enhancements
- Referral program
- Company portfolio showcase

---

## **Documentation Requirements**

### **Technical Documentation:**
- [ ] API documentation
- [ ] Database schema documentation
- [ ] Code comments and inline docs
- [ ] Deployment guide
- [ ] Server setup guide
- [ ] Cron job configuration
- [ ] Environment variables reference

### **User Documentation:**
- [ ] Customer user guide
- [ ] Company user guide
- [ ] FAQ section
- [ ] Video tutorials
- [ ] Feature walkthroughs
- [ ] Troubleshooting guide

### **Business Documentation:**
- [ ] Business logic flow (already complete)
- [ ] Payment terms explained
- [ ] Escrow process explained
- [ ] Dispute resolution policy
- [ ] Terms of service
- [ ] Privacy policy

---

# 🎯 **GETTING STARTED**

## **Immediate Next Steps:**

### **Week 1 - Day 1:**

1. **Review Current Codebase**
   - Understand existing structure
   - Identify what can be reused
   - Note what needs refactoring

2. **Set Up Development Environment**
   - Ensure XAMPP configured
   - Install PHPUnit for testing
   - Set up version control (Git)
   - Create development branch

3. **Database Planning**
   - Review schema designs above
   - Adjust for your specific needs
   - Create migration scripts
   - Plan rollback strategies

4. **Create Project Roadmap**
   - Break down phases into tasks
   - Assign responsibilities
   - Set milestone deadlines
   - Schedule daily standups

5. **Start Phase 1**
   - Create database tables
   - Write model classes
   - Set up basic API structure
   - Write first tests

---

## **Daily Development Workflow:**

1. **Morning:** Review progress, plan daily tasks
2. **Development:** Code, commit frequently
3. **Testing:** Write/run tests as you code
4. **Afternoon:** Integration, bug fixes
5. **Evening:** Code review, documentation
6. **End-of-day:** Commit, push, update tracker

---

## **Questions to Answer Before Starting:**

1. **Which payment gateway will you use?**
   - Stripe, PayPal, local bank integration?
   - Test mode available?
   - API documentation accessible?

2. **How will escrow accounts work legally?**
   - Do you need licenses?
   - What are local regulations?
   - Need legal consultation?

3. **What's your hosting plan?**
   - Shared hosting, VPS, cloud?
   - Cron job support?
   - Database size limits?

4. **Who are your beta testers?**
   - Real customers?
   - Real companies?
   - How will you recruit?

5. **What's your launch strategy?**
   - Soft launch vs. full launch?
   - Marketing plan?
   - Support system ready?

---

# 🎉 **CONCLUSION**

This implementation plan provides a **complete roadmap** to build your FixLanka platform with all 6 business logic solutions integrated.

**Key Success Factors:**
1. ✅ Follow phases sequentially
2. ✅ Test thoroughly at each phase
3. ✅ Don't skip documentation
4. ✅ Get user feedback early
5. ✅ Iterate based on feedback
6. ✅ Monitor production closely

**You now have:**
- Complete database schema
- Detailed implementation phases
- API endpoint specifications
- UI/UX mockups and flows
- Testing strategies
- Deployment checklist
- Risk mitigation plans
- Success metrics
- Future enhancement roadmap

**Ready to build!** 🚀

Start with Phase 1 tomorrow and work through each phase systematically. You'll have a production-ready system in 16 weeks.

Good luck with implementation! 💪
