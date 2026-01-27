# Phase 1 Business Logic - Testing Guide

## 🎯 Implementation Complete - Ready for Testing

**Date:** 2025-12-26  
**Status:** ✅ All components implemented  
**Breaking Changes:** ❌ None - Fully backwards compatible

---

## 📋 Testing Checklist

### 1. Budget Flexibility Tests

#### Test 1A: Fixed Budget (Default)
**Steps:**
1. Open a repair request quotation form
2. Fill in labor/material costs (e.g., Total = LKR 50,000)
3. Keep "Fixed Budget" radio button selected (default)
4. **Expected:** Budget range display should be hidden

#### Test 1B: Flexible Budget
**Steps:**
1. Same as above, but select "Flexible Budget (±10%)" radio button
2. **Expected:** 
   - Budget range display appears
   - Shows: "LKR 45,000.00 - LKR 55,000.00" (50k ± 10%)

#### Test 1C: Real-time Calculation
**Steps:**
1. Select Flexible Budget
2. Change labor cost to increase total to LKR 100,000
3. **Expected:**
   - Budget range updates automatically
   - Shows: "LKR 90,000.00 - LKR 110,000.00"

---

### 2. Payment Method Tests

#### Test 2A: Milestone-Based (Default)
**Steps:**
1. Keep "Milestone-Based Payment" selected in dropdown
2. **Expected:**
   - Info box displays: "Payment released at project milestones"
   - Blue border color
   - Hourly rate field hidden

#### Test 2B: 50-50 Split
**Steps:**
1. Select "50% Upfront, 50% on Completion"
2. **Expected:**
   - Info box updates: "50% payment before starting, 50% after completion"
   - Green border color
   - Hourly rate field hidden

#### Test 2C: Time & Material
**Steps:**
1. Select "Time & Material (Hourly Rate)"
2. **Expected:**
   - Info box: "Pay based on actual hours worked"
   - Orange border color
   - **Hourly Rate field appears** (required)
   - **Spending Cap field appears** (optional, default 1.5)

#### Test 2D: All Methods Cycle
**Steps:**
1. Cycle through all 5 payment methods:
   - Milestone-Based
   - 50% Upfront, 50% on Completion
   - 30% Upfront, 70% on Completion
   - 100% Upfront Payment
   - Time & Material (Hourly Rate)
2. **Expected:** Each method shows correct info and toggles hourly rate field appropriately

---

### 3. Form Submission Tests

#### Test 3A: Submit with Fixed Budget + Milestone
**Steps:**
1. Fill complete form:
   - Budget Type: Fixed
   - Payment Method: Milestone-Based
   - Total: LKR 75,000
2. Submit quotation
3. **Expected:**
   - Success message: "Quotation submitted successfully!"
   - Check database: `budget_type='fixed'`, `payment_method='milestone'`

#### Test 3B: Submit with Flexible + Time & Material
**Steps:**
1. Fill complete form:
   - Budget Type: Flexible (±10%)
   - Payment Method: Time & Material
   - Hourly Rate: LKR 2,500
   - Total: LKR 100,000
2. Submit quotation
3. **Expected:**
   - Success message
   - Database check:
     - `budget_type='flexible'`
     - `budget_min=90000`, `budget_max=110000`
     - `payment_method='time_material'`
     - `hourly_rate=2500.00`
     - `pricing_type='time_based'`

#### Test 3C: Submit with 50-50 Payment
**Steps:**
1. Fill complete form:
   - Budget Type: Fixed
   - Payment Method: 50% Upfront, 50% on Completion
   - Total: LKR 200,000
2. Submit quotation
3. **Expected:**
   - Database: `payment_method='50-50'`, `pricing_type='fixed_price'`

---

### 4. Auto-Determination Tests (pricing_type)

#### Test 4A: Fixed Price Detection
**Steps:**
1. Select Labor Pricing: **Fixed Amount**
2. Payment Method: Any (except Time & Material)
3. Submit
4. **Expected:** Database has `pricing_type='fixed_price'`

#### Test 4B: Time-Based Detection
**Steps:**
1. Select Labor Pricing: **Per Hour**
2. Payment Method: Milestone
3. Submit
4. **Expected:** Database has `pricing_type='time_based'`

#### Test 4C: Hybrid Detection
**Steps:**
1. Select Labor Pricing: **Per SQM** or **Per Unit**
2. Submit
3. **Expected:** Database has `pricing_type='hybrid'`

#### Test 4D: Override by Time & Material
**Steps:**
1. Select Labor Pricing: Fixed Amount
2. Payment Method: **Time & Material**
3. Submit
4. **Expected:** Database has `pricing_type='time_based'` (overridden)

---

### 5. Validation Tests

#### Test 5A: Time & Material without Hourly Rate
**Steps:**
1. Select Payment Method: Time & Material
2. Leave Hourly Rate field empty
3. Try to submit
4. **Expected:** 
   - API returns 400 error
   - Error message: "Hourly rate is required for time-based or hybrid pricing"

#### Test 5B: Invalid Budget Type
**Steps:**
1. Use browser console to set invalid budget_type:
   ```javascript
   document.querySelector('input[name="budget_type"]:checked').value = 'invalid';
   ```
2. Submit
3. **Expected:** API returns 400 error with validation message

---

### 6. Backwards Compatibility Tests

#### Test 6A: Old API Endpoints Still Work
**Steps:**
1. Test old endpoint (without ?action parameter):
   ```javascript
   fetch('/api/company-quotes.php', {
       method: 'POST',
       body: JSON.stringify({
           request_id: 1,
           user_id: 1,
           title: "Old API Test",
           // ...basic fields only
       })
   })
   ```
2. **Expected:** Should work without errors (uses defaults for new fields)

#### Test 6B: Existing Features Unaffected
**Steps:**
1. Test existing features:
   - Labor pricing toggle (Fixed/Hourly/SQM/Unit)
   - Material cost checkbox (Include Materials)
   - Transport & Other costs
   - Date pickers
   - Terms & Conditions
2. **Expected:** All work exactly as before

---

### 7. Database Verification Tests

#### Test 7A: Direct Database Check
**SQL:**
```sql
SELECT 
    quotation_id,
    budget_type,
    budget_min,
    budget_max,
    payment_method,
    pricing_type,
    hourly_rate,
    spending_cap_multiplier,
    total_amount
FROM CompanyQuotation
ORDER BY created_at DESC
LIMIT 5;
```

**Expected Results:**
- All new columns present
- budget_type: 'fixed' or 'flexible'
- payment_method: one of 5 valid values
- pricing_type: 'fixed_price', 'time_based', or 'hybrid'
- budget_min/max: NULL for fixed, calculated for flexible

#### Test 7B: Budget Range Calculation Check
**Steps:**
1. Create quotation with total = LKR 80,000 and Flexible budget
2. Query database:
   ```sql
   SELECT budget_min, budget_max, total_amount 
   FROM CompanyQuotation 
   WHERE quotation_id = [LAST_INSERT_ID];
   ```
3. **Expected:**
   - budget_min = 72,000.00 (80k × 0.90)
   - budget_max = 88,000.00 (80k × 1.10)

---

### 8. API Response Tests

#### Test 8A: Enhanced Create Response
**Request:**
```javascript
POST /api/company-quotes.php?action=create_enhanced
{
    "budget_type": "flexible",
    "payment_method": "milestone",
    "pricing_type": "fixed_price",
    "total_amount": 100000,
    // ...other fields
}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Quotation created successfully",
    "quotation": {
        "quotation_id": 123,
        "budget_type": "flexible",
        "budget_min": 90000,
        "budget_max": 110000,
        "payment_method": "milestone",
        "pricing_type": "fixed_price"
    }
}
```

#### Test 8B: Enhanced Get Response
**Request:**
```
GET /api/company-quotes.php?action=get_enhanced&quotation_id=123
```

**Expected Response:**
```json
{
    "success": true,
    "quotation": {
        "quotation_id": 123,
        "budget_type": "flexible",
        "budget_range_text": "LKR 90,000.00 - LKR 110,000.00",
        "payment_method": "milestone",
        // ...all fields including business logic
    }
}
```

---

## 🐛 Known Issues to Watch For

### Issue 1: JavaScript Console Errors
**Check:** Browser console (F12) for any errors when:
- Changing budget type
- Changing payment method
- Changing total amount

**Expected:** No errors

### Issue 2: Budget Range Not Updating
**If Happens:**
- Check if MutationObserver is attached to #total-amount
- Verify updateBudgetDisplay() is called on radio change

### Issue 3: Hourly Rate Field Not Showing
**If Happens:**
- Check payment_method value is exactly 'time_material'
- Verify updatePaymentMethodInfo() is called on dropdown change

---

## ✅ Success Criteria

**Phase 1 is considered successful if:**

1. ✅ All 8 test sections pass without errors
2. ✅ Database correctly stores all 7 new fields
3. ✅ Budget range calculates accurately (±10%)
4. ✅ Payment method info displays for all 5 options
5. ✅ Hourly rate field shows/hides appropriately
6. ✅ pricing_type auto-determines correctly
7. ✅ API validation catches invalid data
8. ✅ Old quotations still display and work
9. ✅ No console errors in browser
10. ✅ No breaking changes to existing features

---

## 📝 Test Results Template

**Tester:** [Your Name]  
**Date:** [Test Date]  
**Browser:** [Chrome/Firefox/Edge]  
**Environment:** [Development/Staging]

| Test ID | Test Name | Status | Notes |
|---------|-----------|--------|-------|
| 1A | Fixed Budget | ⬜ Pass / ❌ Fail | |
| 1B | Flexible Budget | ⬜ Pass / ❌ Fail | |
| 1C | Real-time Calc | ⬜ Pass / ❌ Fail | |
| 2A | Milestone Method | ⬜ Pass / ❌ Fail | |
| 2B | 50-50 Method | ⬜ Pass / ❌ Fail | |
| 2C | Time & Material | ⬜ Pass / ❌ Fail | |
| 2D | All Methods | ⬜ Pass / ❌ Fail | |
| 3A | Submit Fixed+Milestone | ⬜ Pass / ❌ Fail | |
| 3B | Submit Flexible+T&M | ⬜ Pass / ❌ Fail | |
| 3C | Submit 50-50 | ⬜ Pass / ❌ Fail | |
| 4A | Fixed Price Type | ⬜ Pass / ❌ Fail | |
| 4B | Time-Based Type | ⬜ Pass / ❌ Fail | |
| 4C | Hybrid Type | ⬜ Pass / ❌ Fail | |
| 4D | T&M Override | ⬜ Pass / ❌ Fail | |
| 5A | Hourly Rate Required | ⬜ Pass / ❌ Fail | |
| 5B | Invalid Budget Type | ⬜ Pass / ❌ Fail | |
| 6A | Old API Works | ⬜ Pass / ❌ Fail | |
| 6B | Existing Features | ⬜ Pass / ❌ Fail | |
| 7A | Database Schema | ⬜ Pass / ❌ Fail | |
| 7B | Budget Calc | ⬜ Pass / ❌ Fail | |
| 8A | Create Response | ⬜ Pass / ❌ Fail | |
| 8B | Get Response | ⬜ Pass / ❌ Fail | |

**Overall Result:** ⬜ All Pass / ❌ Some Failures

**Issues Found:**
1. [Issue description]
2. [Issue description]

**Recommendations:**
1. [Recommendation]
2. [Recommendation]

---

## 🚀 Next Steps After Testing

1. **If All Tests Pass:**
   - Mark Phase 1 as complete ✅
   - Document any edge cases found
   - Prepare for Phase 2 (Contract Management)

2. **If Tests Fail:**
   - Document specific failures
   - Check browser console for errors
   - Review database entries
   - Report issues for fixing

3. **Performance Check:**
   - Test with 10+ quotations
   - Check page load time
   - Verify no memory leaks

---

## 📞 Support

**For issues, check:**
1. Browser console (F12 → Console tab)
2. Network tab (F12 → Network) for API errors
3. Database entries (MySQL)
4. PHP error log

**Common Solutions:**
- Clear browser cache
- Restart XAMPP MySQL/Apache
- Check file permissions
- Verify database schema matches expectations
