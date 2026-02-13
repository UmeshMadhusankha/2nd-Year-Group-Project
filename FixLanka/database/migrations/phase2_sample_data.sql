-- ========================================
-- PHASE 2: Sample Data for Testing
-- Adds realistic test data to demonstrate all Phase 2 features
-- ========================================

USE fix_lanka;

-- Get existing contract ID (assuming you have at least one)
SET @contract_id = (SELECT contract_id FROM contract LIMIT 1);
SET @company_id = (SELECT company_id FROM contract WHERE contract_id = @contract_id);
SET @customer_id = (SELECT customer_id FROM contract WHERE contract_id = @contract_id);

-- ========================================
-- 1. UPDATE CONTRACT WITH PHASE 2 FIELDS
-- ========================================

UPDATE contract 
SET 
    budget_type = 'flexible',
    payment_method = 'milestone_based',
    undo_deadline = DATE_ADD(NOW(), INTERVAL 20 HOUR),  -- 20 hours remaining
    undo_requested = 0,
    chat_active = 1
WHERE contract_id = @contract_id;

-- ========================================
-- 2. CREATE ESCROW ACCOUNT
-- ========================================

INSERT INTO escrow_accounts (
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
    100000.00,  -- Total Rs. 100,000
    30000.00,   -- Released Rs. 30,000
    70000.00,   -- Held Rs. 70,000
    'active',
    NOW()
) ON DUPLICATE KEY UPDATE
    total_amount = 100000.00,
    released_amount = 30000.00,
    held_amount = 70000.00;

-- ========================================
-- 3. ADD SAMPLE NOTIFICATIONS
-- ========================================

INSERT INTO contract_notifications (
    contract_id,
    notification_type,
    title,
    message,
    recipient_type,
    recipient_id,
    priority,
    is_read,
    created_at
) VALUES
-- For Company
(
    @contract_id,
    'milestone_approved',
    'Milestone Approved',
    'Customer has approved Milestone #1. Payment will be released shortly.',
    'company',
    @company_id,
    'high',
    0,
    DATE_SUB(NOW(), INTERVAL 5 MINUTE)
),
(
    @contract_id,
    'payment_released',
    'Payment Released',
    'Rs. 30,000 has been released to your account.',
    'company',
    @company_id,
    'normal',
    0,
    DATE_SUB(NOW(), INTERVAL 2 HOUR)
),
-- For Customer
(
    @contract_id,
    'milestone_submitted',
    'Milestone Ready for Review',
    'Company has submitted Milestone #2 for your approval.',
    'customer',
    @customer_id,
    'high',
    0,
    DATE_SUB(NOW(), INTERVAL 10 MINUTE)
),
(
    @contract_id,
    'work_started',
    'Work Started',
    'Company has started work on Milestone #3.',
    'customer',
    @customer_id,
    'normal',
    1,
    DATE_SUB(NOW(), INTERVAL 1 DAY)
);

-- ========================================
-- 4. ADD CONTRACT TIMELINE EVENTS
-- ========================================

INSERT INTO contract_timeline (
    contract_id,
    event_type,
    event_title,
    event_description,
    actor_type,
    actor_id,
    created_at
) VALUES
(
    @contract_id,
    'created',
    'Contract Created',
    'Contract was created and sent to customer',
    'company',
    @company_id,
    DATE_SUB(NOW(), INTERVAL 5 DAY)
),
(
    @contract_id,
    'accepted',
    'Contract Accepted',
    'Customer accepted the contract terms',
    'customer',
    @customer_id,
    DATE_SUB(NOW(), INTERVAL 4 DAY)
),
(
    @contract_id,
    'milestone_submitted',
    'Milestone Submitted',
    'Milestone #1 submitted for customer approval',
    'company',
    @company_id,
    DATE_SUB(NOW(), INTERVAL 3 DAY)
),
(
    @contract_id,
    'milestone_approved',
    'Milestone Approved',
    'Customer approved Milestone #1',
    'customer',
    @customer_id,
    DATE_SUB(NOW(), INTERVAL 2 DAY)
),
(
    @contract_id,
    'payment_released',
    'Payment Released',
    'Rs. 30,000 released from escrow to company',
    'system',
    NULL,
    DATE_SUB(NOW(), INTERVAL 2 DAY)
),
(
    @contract_id,
    'work_started',
    'Work Started',
    'Company started work on Milestone #2',
    'company',
    @company_id,
    DATE_SUB(NOW(), INTERVAL 1 DAY)
),
(
    @contract_id,
    'work_completed',
    'Work Completed',
    'Company completed work on Milestone #2',
    'company',
    @company_id,
    DATE_SUB(NOW(), INTERVAL 12 HOUR)
),
(
    @contract_id,
    'milestone_submitted',
    'Milestone Submitted',
    'Milestone #2 submitted for customer approval',
    'company',
    @company_id,
    DATE_SUB(NOW(), INTERVAL 10 MINUTE)
);

-- ========================================
-- 5. ADD SAMPLE INVOICES
-- ========================================

INSERT INTO contract_invoices (
    contract_id,
    invoice_number,
    invoice_type,
    amount,
    issue_date,
    due_date,
    payment_status,
    paid_date,
    description,
    milestone_id
) VALUES
(
    @contract_id,
    CONCAT('INV-', YEAR(NOW()), '-', LPAD(@contract_id * 100 + 1, 5, '0')),
    'milestone',
    30000.00,
    DATE_SUB(NOW(), INTERVAL 3 DAY),
    DATE_SUB(NOW(), INTERVAL 1 DAY),
    'paid',
    DATE_SUB(NOW(), INTERVAL 2 DAY),
    'Payment for Milestone #1 - Foundation Work',
    NULL
),
(
    @contract_id,
    CONCAT('INV-', YEAR(NOW()), '-', LPAD(@contract_id * 100 + 2, 5, '0')),
    'milestone',
    35000.00,
    NOW(),
    DATE_ADD(NOW(), INTERVAL 7 DAY),
    'pending',
    NULL,
    'Payment for Milestone #2 - Main Construction',
    NULL
),
(
    @contract_id,
    CONCAT('INV-', YEAR(NOW()), '-', LPAD(@contract_id * 100 + 3, 5, '0')),
    'final',
    35000.00,
    NOW(),
    DATE_ADD(NOW(), INTERVAL 14 DAY),
    'pending',
    NULL,
    'Final payment - Milestone #3 completion',
    NULL
);

-- ========================================
-- 6. ADD BUDGET ADJUSTMENT REQUEST
-- ========================================

INSERT INTO contract_budget_adjustments (
    contract_id,
    old_budget,
    new_budget,
    reason,
    status,
    requested_by,
    requester_id,
    requested_at
) VALUES
(
    @contract_id,
    100000.00,
    120000.00,
    'Additional materials required due to site conditions not visible during initial assessment. Customer requested upgraded finishes.',
    'pending',
    'company',
    @company_id,
    DATE_SUB(NOW(), INTERVAL 2 HOUR)
);

-- ========================================
-- 7. ADD CHAT MESSAGES
-- ========================================

INSERT INTO contract_chats (
    contract_id,
    sender_type,
    sender_id,
    message,
    is_read,
    created_at
) VALUES
(
    @contract_id,
    'company',
    @company_id,
    'Hi! We have started the foundation work as per the contract.',
    1,
    DATE_SUB(NOW(), INTERVAL 1 DAY)
),
(
    @contract_id,
    'customer',
    @customer_id,
    'Great! Please keep me updated on the progress.',
    1,
    DATE_SUB(NOW(), INTERVAL 22 HOUR)
),
(
    @contract_id,
    'company',
    @company_id,
    'Foundation is complete. I''ve submitted Milestone #1 for your approval.',
    1,
    DATE_SUB(NOW(), INTERVAL 3 HOUR)
),
(
    @contract_id,
    'customer',
    @customer_id,
    'Approved! The work looks excellent. Thank you.',
    1,
    DATE_SUB(NOW(), INTERVAL 2 HOUR)
),
(
    @contract_id,
    'company',
    @company_id,
    'Starting on Milestone #2 tomorrow. We may need a small budget adjustment due to site conditions.',
    0,
    DATE_SUB(NOW(), INTERVAL 30 MINUTE)
);

-- ========================================
-- 8. UPDATE MILESTONES WITH WORKFLOW DATA
-- ========================================

-- Assuming milestones exist for this contract
UPDATE milestone SET
    work_started = 1,
    work_started_at = DATE_SUB(NOW(), INTERVAL 3 DAY),
    work_completed = 1,
    work_completed_at = DATE_SUB(NOW(), INTERVAL 2 DAY),
    submitted_for_approval = 1,
    submitted_at = DATE_SUB(NOW(), INTERVAL 2 DAY),
    customer_approved = 1,
    customer_approved_at = DATE_SUB(NOW(), INTERVAL 2 DAY),
    payment_released = 1,
    payment_released_at = DATE_SUB(NOW(), INTERVAL 2 DAY)
WHERE contract_id = @contract_id AND milestone_number = 1
LIMIT 1;

UPDATE milestone SET
    work_started = 1,
    work_started_at = DATE_SUB(NOW(), INTERVAL 1 DAY),
    work_completed = 1,
    work_completed_at = DATE_SUB(NOW(), INTERVAL 12 HOUR),
    submitted_for_approval = 1,
    submitted_at = DATE_SUB(NOW(), INTERVAL 10 MINUTE),
    customer_approved = 0
WHERE contract_id = @contract_id AND milestone_number = 2
LIMIT 1;

UPDATE milestone SET
    work_started = 0,
    customer_approved = 0
WHERE contract_id = @contract_id AND milestone_number = 3
LIMIT 1;

-- ========================================
-- VERIFICATION QUERY
-- ========================================

SELECT 
    '✅ SAMPLE DATA ADDED SUCCESSFULLY!' as status,
    @contract_id as contract_id,
    (SELECT COUNT(*) FROM contract_notifications WHERE contract_id = @contract_id) as notifications_count,
    (SELECT COUNT(*) FROM contract_timeline WHERE contract_id = @contract_id) as timeline_events,
    (SELECT COUNT(*) FROM contract_invoices WHERE contract_id = @contract_id) as invoices_count,
    (SELECT COUNT(*) FROM contract_chats WHERE contract_id = @contract_id) as chat_messages,
    (SELECT COUNT(*) FROM contract_budget_adjustments WHERE contract_id = @contract_id) as budget_requests,
    (SELECT status FROM escrow_accounts WHERE contract_id = @contract_id) as escrow_status;

-- ========================================
-- Quick View Queries
-- ========================================

-- View notifications
SELECT 'NOTIFICATIONS:' as section;
SELECT notification_type, title, recipient_type, is_read, created_at 
FROM contract_notifications 
WHERE contract_id = @contract_id 
ORDER BY created_at DESC;

-- View timeline
SELECT 'TIMELINE:' as section;
SELECT event_type, event_title, actor_type, created_at 
FROM contract_timeline 
WHERE contract_id = @contract_id 
ORDER BY created_at DESC;

-- View invoices
SELECT 'INVOICES:' as section;
SELECT invoice_number, amount, payment_status, issue_date, due_date 
FROM contract_invoices 
WHERE contract_id = @contract_id;

-- View escrow
SELECT 'ESCROW:' as section;
SELECT account_number, total_amount, released_amount, held_amount, status 
FROM escrow_accounts 
WHERE contract_id = @contract_id;

-- View chats
SELECT 'CHAT MESSAGES:' as section;
SELECT sender_type, LEFT(message, 50) as message_preview, is_read, created_at 
FROM contract_chats 
WHERE contract_id = @contract_id 
ORDER BY created_at;

SELECT '✅ All Phase 2 sample data loaded successfully!' as completion_message;
SELECT 'You can now test all Phase 2 features with realistic data!' as next_step;
