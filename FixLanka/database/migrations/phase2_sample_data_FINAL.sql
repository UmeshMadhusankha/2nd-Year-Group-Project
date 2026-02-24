-- ==================================================================
-- PHASE 2: Working Sample Data (Final Version)
-- ==================================================================

USE fix_lanka;

SET @contract_id = (SELECT contract_id FROM contract LIMIT 1);
SET @company_id = (SELECT company_id FROM contract WHERE contract_id = @contract_id);
SET @customer_id = (SELECT customer_id FROM contract WHERE contract_id = @contract_id);

SELECT CONCAT('✅ Adding Phase 2 sample data for Contract #', @contract_id) as status;

-- 1. Update contract for Phase 2
UPDATE contract SET 
    budget_type = 'flexible',
    payment_method = 'milestone_based',
    undo_deadline = DATE_ADD(NOW(), INTERVAL 20 HOUR),
    undo_requested = 0,
    chat_active = 1,
    status = 'active',
    total_budget = 100000.00
WHERE contract_id = @contract_id;

-- 2. Escrow
INSERT IGNORE INTO escrow_accounts (contract_id, account_number, total_amount, released_amount, held_amount, status, created_at)
VALUES (@contract_id, CONCAT('ESC-', LPAD(@contract_id, 6, '0')), 100000.00, 30000.00, 70000.00, 'active', NOW());

-- 3. Notifications
DELETE FROM contract_notifications WHERE contract_id = @contract_id;
INSERT INTO contract_notifications (contract_id, notification_type, title, message, recipient_type, recipient_id, priority, is_read, created_at) VALUES
(@contract_id, 'milestone_approved', 'Milestone Approved', 'Milestone #1 approved', 'company', @company_id, 'high', 0, DATE_SUB(NOW(), INTERVAL 5 MINUTE)),
(@contract_id, 'payment_released', 'Payment Released', 'Rs. 30,000 released', 'company', @company_id, 'normal', 0, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(@contract_id, 'milestone_submitted', 'Review Required', 'Milestone #2 ready', 'customer', @customer_id, 'high', 0, DATE_SUB(NOW(), INTERVAL 10 MINUTE));

-- 4. Timeline
DELETE FROM contract_timeline WHERE contract_id = @contract_id;
INSERT INTO contract_timeline (contract_id, event_type, event_title, event_description, actor_type, actor_id, created_at) VALUES
(@contract_id, 'created', 'Created', 'Contract created', 'company', @company_id, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(@contract_id, 'accepted', 'Accepted', 'Customer accepted', 'customer', @customer_id, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(@contract_id, 'milestone_approved', 'Milestone Approved', 'Milestone #1', 'customer', @customer_id, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(@contract_id, 'payment_released', 'Payment Released', 'Rs. 30,000', 'system', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY));

-- 5. Chats
DELETE FROM contract_chats WHERE contract_id = @contract_id;
INSERT INTO contract_chats (contract_id, sender_type, sender_id, message, is_read, created_at) VALUES
(@contract_id, 'company', @company_id, 'Started foundation work!', 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(@contract_id, 'customer', @customer_id, 'Great progress!', 1, DATE_SUB(NOW(), INTERVAL 22 HOUR)),
(@contract_id, 'company', @company_id, 'Milestone #1 complete.', 1, DATE_SUB(NOW(), INTERVAL 3 HOUR));

-- 6. Invoices
DELETE FROM contract_invoices WHERE contract_id = @contract_id;
INSERT INTO contract_invoices (contract_id, invoice_number, invoice_type, amount, tax_percentage, tax_amount, total_amount, issue_date, due_date, payment_status, paid_date, notes) VALUES
(@contract_id, CONCAT('INV-2026-',LPAD(@contract_id,5,'0'),'1'), 'milestone', 30000, 0, 0, 30000, DATE_SUB(NOW(),INTERVAL 3 DAY), DATE_SUB(NOW(),INTERVAL 1 DAY), 'paid', DATE_SUB(NOW(),INTERVAL 2 DAY), 'Milestone #1'),
(@contract_id, CONCAT('INV-2026-',LPAD(@contract_id,5,'0'),'2'), 'milestone', 35000, 0, 0, 35000, NOW(), DATE_ADD(NOW(),INTERVAL 7 DAY), 'pending', NULL, 'Milestone #2');

-- 7. Budget Adjustment
DELETE FROM contract_budget_adjustments WHERE contract_id = @contract_id;
INSERT INTO contract_budget_adjustments (contract_id, requested_by, requester_id, adjustment_type, original_amount, requested_amount, adjustment_amount, reason, status, created_at) VALUES
(@contract_id, 'company', @company_id, 'increase', 100000, 120000, 20000, 'Additional materials needed', 'pending', DATE_SUB(NOW(), INTERVAL 2 HOUR));

-- RESULTS
SELECT '✅ COMPLETE!' as status,
    @contract_id as contract_id,
    (SELECT COUNT(*) FROM contract_notifications WHERE contract_id = @contract_id) as notifications,
    (SELECT COUNT(*) FROM contract_timeline WHERE contract_id = @contract_id) as timeline,
    (SELECT COUNT(*) FROM contract_invoices WHERE contract_id = @contract_id) as invoices,
    (SELECT COUNT(*) FROM contract_chats WHERE contract_id = @contract_id) as chats,
    (SELECT status FROM escrow_accounts WHERE contract_id = @contract_id) as escrow;
