-- ========================================
-- PHASE 2: Quick Sample Data Injection
-- Simple script to add test data for Phase 2 features
-- ========================================

USE fix_lanka;

-- Get existing contract
SET @contract_id = (SELECT contract_id FROM contract LIMIT 1);
SET @company_id = (SELECT company_id FROM contract WHERE contract_id = @contract_id);
SET @customer_id = (SELECT customer_id FROM contract WHERE contract_id = @contract_id);

SELECT CONCAT('Adding sample data for Contract ID: ', @contract_id) as status;

-- ========================================
-- 1. Update Contract for Phase 2
-- ========================================

UPDATE contract 
SET 
    budget_type = 'flexible',
    payment_method = 'milestone_based',
    undo_deadline = DATE_ADD(NOW(), INTERVAL 20 HOUR),
    undo_requested = 0,
    chat_active = 1,
    status = 'active'
WHERE contract_id = @contract_id;

-- ========================================
-- 2. Escrow Account
-- ========================================

INSERT IGNORE INTO escrow_accounts (
    contract_id,
    account_number,
    total_amount,
    released_amount,
    held_amount,
    status,
    created_at
) VALUES (
    @contract_id,
    CONCAT('ESC-', LPAD(@contract_id, 6, '0')),
    100000.00,
    30000.00,
    70000.00,
    'active',
    NOW()
);

-- ========================================
-- 3. Notifications (4 samples)
-- ========================================

DELETE FROM contract_notifications WHERE contract_id = @contract_id;

INSERT INTO contract_notifications (contract_id, notification_type, title, message, recipient_type, recipient_id, priority, is_read, created_at)
VALUES
(@contract_id, 'milestone_approved', 'Milestone Approved', 'Customer approved Milestone #1', 'company', @company_id, 'high', 0, DATE_SUB(NOW(), INTERVAL 5 MINUTE)),
(@contract_id, 'payment_released', 'Payment Released', 'Rs. 30,000 released', 'company', @company_id, 'normal', 0, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(@contract_id, 'milestone_submitted', 'Review Required', 'Milestone #2 ready for approval', 'customer', @customer_id, 'high', 0, DATE_SUB(NOW(), INTERVAL 10 MINUTE)),
(@contract_id, 'work_started', 'Work Started', 'Company started Milestone #3', 'customer', @customer_id, 'normal', 1, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- ========================================
-- 4. Timeline Events (8 events)
-- ========================================

DELETE FROM contract_timeline WHERE contract_id = @contract_id;

INSERT INTO contract_timeline (contract_id, event_type, event_title, event_description, actor_type, actor_id, created_at)
VALUES
(@contract_id, 'created', 'Contract Created', 'Contract sent to customer', 'company', @company_id, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(@contract_id, 'accepted', 'Contract Accepted', 'Customer accepted terms', 'customer', @customer_id, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(@contract_id, 'milestone_submitted', 'Milestone Submitted', 'Milestone #1 submitted', 'company', @company_id, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(@contract_id, 'milestone_approved', 'Milestone Approved', 'Milestone #1 approved', 'customer', @customer_id, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(@contract_id, 'payment_released', 'Payment Released', 'Rs. 30,000 released', 'system', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(@contract_id, 'work_started', 'Work Started', 'Milestone #2 started', 'company', @company_id, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(@contract_id, 'work_completed', 'Work Completed', 'Milestone #2 completed', 'company', @company_id, DATE_SUB(NOW(), INTERVAL 12 HOUR)),
(@contract_id, 'milestone_submitted', 'Milestone Submitted', 'Milestone #2 submitted', 'company', @company_id, DATE_SUB(NOW(), INTERVAL 10 MINUTE));

-- ========================================
-- 5. Chat Messages (5 messages)
-- ========================================

DELETE FROM contract_chats WHERE contract_id = @contract_id;

INSERT INTO contract_chats (contract_id, sender_type, sender_id, message, is_read, created_at)
VALUES
(@contract_id, 'company', @company_id, 'Started foundation work today!', 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(@contract_id, 'customer', @customer_id, 'Great! Keep me updated.', 1, DATE_SUB(NOW(), INTERVAL 22 HOUR)),
(@contract_id, 'company', @company_id, 'Foundation complete. Submitted Milestone #1.', 1, DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(@contract_id, 'customer', @customer_id, 'Approved! Excellent work.', 1, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(@contract_id, 'company', @company_id, 'Starting Milestone #2 tomorrow.', 0, DATE_SUB(NOW(), INTERVAL 30 MINUTE));

-- ========================================
-- 6. Invoices (3 invoices)
-- ========================================

DELETE FROM contract_invoices WHERE contract_id = @contract_id;

INSERT INTO contract_invoices (contract_id, invoice_number, invoice_type, amount, tax_percentage, tax_amount, total_amount, issue_date, due_date, payment_status, paid_date, notes)
VALUES
(@contract_id, CONCAT('INV-2026-', LPAD(@contract_id, 5, '0'), '1'), 'milestone', 30000.00, 0, 0, 30000.00, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 'paid', DATE_SUB(NOW(), INTERVAL 2 DAY), 'Milestone #1 payment'),
(@contract_id, CONCAT('INV-2026-', LPAD(@contract_id, 5, '0'), '2'), 'milestone', 35000.00, 0, 0, 35000.00, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 'pending', NULL, 'Milestone #2 payment'),
(@contract_id, CONCAT('INV-2026-', LPAD(@contract_id, 5, '0'), '3'), 'final', 35000.00, 0, 0, 35000.00, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY), 'pending', NULL, 'Final payment');

-- ========================================
-- 7. Budget Adjustment Request
-- ========================================

DELETE FROM contract_budget_adjustments WHERE contract_id = @contract_id;

INSERT INTO contract_budget_adjustments (contract_id, old_budget, new_budget, reason, status, requested_by, requester_id, requested_at)
VALUES
(@contract_id, 100000.00, 120000.00, 'Additional materials needed - site conditions require upgraded foundation', 'pending', 'company', @company_id, DATE_SUB(NOW(), INTERVAL 2 HOUR));

-- ========================================
-- RESULTS
-- ========================================

SELECT '✅ SAMPLE DATA ADDED!' as status;
SELECT 
    @contract_id as contract_id,
    (SELECT COUNT(*) FROM contract_notifications WHERE contract_id = @contract_id) as notifications,
    (SELECT COUNT(*) FROM contract_timeline WHERE contract_id = @contract_id) as timeline_events,
    (SELECT COUNT(*) FROM contract_invoices WHERE contract_id = @contract_id) as invoices,
    (SELECT COUNT(*) FROM contract_chats WHERE contract_id = @contract_id) as messages,
    (SELECT COUNT(*) FROM contract_budget_adjustments WHERE contract_id = @contract_id) as budget_requests,
    (SELECT status FROM escrow_accounts WHERE contract_id = @contract_id) as escrow_status;

SELECT '🎉 Phase 2 sample data ready for testing!' as message;
