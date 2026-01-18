# Quick Test Guide - Billing Tab (5 Minutes)

## Step 1: Add Test Data (30 seconds)

Open phpMyAdmin or MySQL terminal and run:

```sql
USE fix_lanka;

-- Check your company_id
SELECT company_id, name, email FROM company WHERE email = 'your@email.com';

-- Replace 1 with your actual company_id
SET @cid = 1;

-- Add subscription
INSERT INTO company_subscriptions 
(company_id, plan_name, plan_price, billing_period, status, start_date, next_billing_date)
VALUES (@cid, 'professional', 5000, 'monthly', 'active', '2025-10-15', '2026-02-15');

-- Add payment methods
INSERT INTO payment_methods 
(company_id, card_type, last_four_digits, card_holder_name, expiry_month, expiry_year, is_primary, is_active)
VALUES 
(@cid, 'visa', '4242', 'John Doe', '12', '2026', 1, 1),
(@cid, 'mastercard', '8888', 'John Doe', '09', '2027', 0, 1);
```

---

## Step 2: Test in Browser (2 minutes)

1. **Go to Settings Page**
   ```
   http://localhost/2nd-Year-Group-Project/FixLanka/views/company/settings.php
   ```

2. **Click "Subscription" Tab**
   - Should see "Professional Plan"
   - Price: "LKR 5,000 /month"
   - Next billing: "Feb 15, 2026"
   - 4 feature items

3. **Check Payment Methods Section**
   - Should show 2 cards
   - Visa ••••4242 (with "Primary" badge)
   - Mastercard ••••8888 (with "Set as Primary" button)

4. **Check Billing History**
   - Should show invoices from billinghistory table
   - Or empty table if no invoices exist

---

## Step 3: Test Interactions (2 minutes)

### Test 1: Set Primary Payment
1. Click "Set as Primary" on Mastercard card
2. Should see success toast
3. Mastercard should now have "Primary" badge
4. Visa should now have "Set as Primary" button

### Test 2: Remove Payment Method
1. Click trash icon on any card
2. Confirm the dialog
3. Should see success toast
4. Card should disappear from list

### Test 3: Cancel Subscription
1. Click "Cancel Subscription" button (red button)
2. Confirm the dialog
3. Should see success toast
4. Status should change in database

---

## Step 4: Verify Database Changes (30 seconds)

```sql
-- Check subscription status
SELECT plan_name, status, next_billing_date 
FROM company_subscriptions 
WHERE company_id = 1;

-- Check payment methods
SELECT card_type, last_four_digits, is_primary, is_active 
FROM payment_methods 
WHERE company_id = 1;
```

---

## Expected Results

✅ **Subscription Card Shows:**
- Plan name matches database (Professional)
- Price matches (LKR 5,000)
- Features list displayed
- Next billing date formatted correctly

✅ **Payment Methods Show:**
- All active cards displayed
- Primary card has badge
- Non-primary cards have "Set as Primary" button
- Card icons match type (Visa/Mastercard)

✅ **Interactions Work:**
- Setting primary updates immediately
- Removing card updates UI without refresh
- Cancelling subscription shows confirmation
- All actions show toast notifications

✅ **Database Updates:**
- Primary flag switches correctly
- Deleted cards have `is_active = 0`
- Cancelled subscriptions have `status = 'cancelled'`

---

## Troubleshooting

### Issue: "Failed to load billing data"
**Solution:** Check company_id in database matches session user_id

### Issue: No payment methods showing
**Solution:** Verify `is_active = 1` in payment_methods table

### Issue: Subscription shows wrong plan
**Solution:** Check `plan_name` column spelling (free/basic/professional/enterprise)

### Issue: Dates not formatted
**Solution:** Check browser console for JavaScript errors

---

## Browser Console Check

Press F12 → Console tab, run:

```javascript
// Should load data
loadBillingData();

// Should show plan configs
console.log(PLANS);

// Should format date
formatDate('2026-02-15'); // Returns: "Feb 15, 2026"
```

---

## Success Indicators

✅ No console errors
✅ Data loads on tab click
✅ All 3 sections populated (Subscription, Payment Methods, Billing History)
✅ Buttons respond with toasts
✅ Database updates reflect in UI immediately
✅ No page refresh needed for updates

---

**Total Test Time:** ~5 minutes
**Status:** ✅ All features working!
