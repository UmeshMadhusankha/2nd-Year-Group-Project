# Contract Management - Testing Guide

## Prerequisites

Before testing, ensure:
1. ✅ XAMPP is running (Apache + MySQL)
2. ✅ Database `fix_lanka` exists with all tables
3. ✅ You're logged in as a Company user
4. ✅ At least one accepted project exists in the database

---

## Quick Test Script

### Step 1: Verify Database Setup
```sql
-- Run in phpMyAdmin
USE fix_lanka;

-- Check if Contract table exists
SHOW TABLES LIKE 'Contract';

-- Check table structure
DESCRIBE Contract;

-- Check if you have any projects
SELECT * FROM Project WHERE status = 'accepted' LIMIT 5;
```

### Step 2: Access Contracts Page
1. Open browser: `http://localhost/FixLanka/views/company/contracts.php`
2. You should see:
   - Header with statistics (Active, Draft, Completed, Total Value)
   - View toggle buttons (Grid/List)
   - Filter dropdown
   - Search bar
   - "New Contract" button
   - Contract cards (if any exist)

---

## Test 1: CREATE Operation

### Test Case 1.1: Create New Contract Successfully

**Steps:**
1. Click "New Contract" button (top right)
2. **Step 1 - Select Project:**
   - Modal should open with "Create New Contract" title
   - You should see project cards (if projects exist)
   - If no projects: see "No Projects Available" message
   - Click on a project card
   - Selected card should highlight with checkmark
   - Click "Next" button

3. **Step 2 - Client Information:**
   - Client Name should be auto-filled (read-only)
   - Client Email should be auto-filled (read-only)
   - Client Phone should be auto-filled (read-only)
   - Project Name should be auto-filled (read-only)
   - Project Description should be auto-filled (read-only)
   - Click "Next" button

4. **Step 3 - Financial Terms:**
   - Fill Contract Title: "Website Development Contract"
   - Contract Value: 150000
   - Contract Type: Select "Fixed Price"
   - Start Date: Pick today's date
   - End Date: Pick future date (at least 7 days from start)
   - Terms & Conditions: "Standard payment terms apply"
   - Click "Review & Submit"

5. **Step 4 - Review:**
   - Verify all data is correct
   - Click "Create Contract"

**Expected Results:**
- ✅ Button shows spinner: "Creating Contract..."
- ✅ Success notification: "Contract created successfully!"
- ✅ Modal closes
- ✅ New contract appears at the top of the list
- ✅ Statistics update (Draft contracts count increases)

**Check Database:**
```sql
SELECT * FROM Contract ORDER BY created_at DESC LIMIT 1;
```

### Test Case 1.2: Validation Tests

**Test missing fields:**
1. Open new contract modal
2. Try clicking "Next" in Step 1 without selecting project
   - ✅ Should show error: "Please select a project"

3. In Step 3, leave Contract Title empty and submit
   - ✅ Should show validation error

4. Set End Date before Start Date
   - ✅ Should show error: "End date must be after start date"

---

## Test 2: READ Operation

### Test Case 2.1: View All Contracts

**Steps:**
1. Refresh the page
2. Observe the contract list

**Expected Results:**
- ✅ Page loads without errors
- ✅ Contracts display in grid view by default
- ✅ Each card shows:
  - Contract title
  - Contract number (CNT-YYYY-XXXX format)
  - Client name with initials avatar
  - Contract value (formatted as LKR)
  - Start and End dates
  - Status badge (Draft, Active, Completed, etc.)
  - Action buttons (View, Edit, Delete, Download)

### Test Case 2.2: View Contract Details

**Steps:**
1. Click "View Details" on any contract
2. Modal should open with full contract information

**Expected Results:**
- ✅ Modal opens with "Contract Details" title
- ✅ Shows all contract information
- ✅ Shows client details
- ✅ Shows financial information
- ✅ Shows dates and timeline
- ✅ Shows terms and conditions

### Test Case 2.3: Pagination

**If you have more than 9 contracts:**

**Steps:**
1. Scroll to bottom of page
2. Click "Load More" button

**Expected Results:**
- ✅ Button shows spinner: "Loading..."
- ✅ Next 9 contracts appear
- ✅ If more contracts exist, "Load More" remains visible
- ✅ If all contracts loaded, shows "You've reached the end"

### Test Case 2.4: Filter by Status

**Steps:**
1. Click the filter dropdown (top right)
2. Select a status (e.g., "Draft")

**Expected Results:**
- ✅ Only contracts with selected status appear
- ✅ Contract count updates
- ✅ Select "All Contracts" to reset

### Test Case 2.5: Search Functionality

**Steps:**
1. Type in search bar (e.g., client name or contract title)
2. Results should filter in real-time

**Expected Results:**
- ✅ Matching contracts appear
- ✅ Non-matching contracts hide
- ✅ Clear search to show all

---

## Test 3: UPDATE Operation

### Test Case 3.1: Edit Contract Successfully

**Steps:**
1. Click "Edit" button on any contract
2. Modal opens with pre-filled form data
3. Modify the following:
   - Contract Title: Add " - Updated"
   - Contract Value: Change to different amount
   - Terms: Add additional terms
4. Click "Update Contract" (or similar button)

**Expected Results:**
- ✅ Form pre-fills with existing data
- ✅ Button shows spinner: "Updating Contract..."
- ✅ Success notification: "Contract updated successfully!"
- ✅ Modal closes
- ✅ Contract card reflects changes
- ✅ Statistics update if needed

**Check Database:**
```sql
SELECT * FROM Contract WHERE contract_id = YOUR_CONTRACT_ID;
-- Verify updated_at timestamp changed
-- Verify modified fields updated
```

### Test Case 3.2: Edit Validation

**Steps:**
1. Edit a contract
2. Clear the Contract Title field
3. Try to submit

**Expected Results:**
- ✅ Validation error appears
- ✅ Form doesn't submit

---

## Test 4: DELETE Operation

### Test Case 4.1: Delete Contract Successfully

**Steps:**
1. Click "Delete" button (red) on any contract
2. Confirmation modal should appear
3. Observe the modal content
4. Click "Cancel" first

**Expected Results:**
- ✅ Modal shows contract name/title
- ✅ Modal shows warning message
- ✅ Modal has Cancel and Delete buttons
- ✅ Clicking Cancel closes modal without deleting

**Now delete for real:**
1. Click "Delete" button again
2. Click "Delete Contract" in confirmation modal

**Expected Results:**
- ✅ Button shows spinner: "Deleting..."
- ✅ Success notification: "Contract deleted successfully!"
- ✅ Modal closes
- ✅ Contract card disappears from list
- ✅ Statistics update (contract count decreases)

**Check Database:**
```sql
SELECT * FROM Contract WHERE contract_id = DELETED_CONTRACT_ID;
-- Should return 0 rows (contract deleted)
```

### Test Case 4.2: Delete Authorization

**If you try to delete another company's contract (requires database manipulation):**

**Expected Results:**
- ✅ Should show error: "Unauthorized" or similar
- ✅ Contract should not be deleted

---

## Test 5: Error Handling

### Test Case 5.1: Network Error Simulation

**Steps:**
1. Open browser DevTools (F12)
2. Go to Network tab
3. Enable "Offline" mode
4. Try to create/edit/delete a contract

**Expected Results:**
- ✅ Error notification appears
- ✅ User-friendly message shown
- ✅ No JavaScript errors in console

### Test Case 5.2: Session Timeout

**Steps:**
1. Open `config/session.php`
2. Temporarily set session timeout to 1 minute
3. Wait 1 minute
4. Try to create/edit/delete a contract

**Expected Results:**
- ✅ 401 Unauthorized error
- ✅ Automatic redirect to login page
- ✅ Notification: "Session expired. Please login again."

### Test Case 5.3: Empty Database

**Steps:**
1. Delete all contracts from database:
```sql
DELETE FROM Contract WHERE company_id = YOUR_COMPANY_ID;
```
2. Refresh the page

**Expected Results:**
- ✅ Shows empty state message
- ✅ Shows illustration or icon
- ✅ Shows "Create New Contract" button
- ✅ Statistics show zeros
- ✅ No JavaScript errors

---

## Test 6: UI/UX Tests

### Test Case 6.1: Responsive Design

**Steps:**
1. Resize browser window to mobile size (375px width)
2. Test all functionality

**Expected Results:**
- ✅ Grid switches to single column
- ✅ All buttons accessible
- ✅ Modals fit on screen
- ✅ No horizontal scroll

### Test Case 6.2: Loading States

**Steps:**
1. Watch for spinners during operations
2. Buttons should disable during loading

**Expected Results:**
- ✅ Loading spinners appear
- ✅ Buttons disable to prevent double-submit
- ✅ Loading messages are clear

### Test Case 6.3: Form Accessibility

**Steps:**
1. Navigate form using Tab key
2. Try to submit using Enter key

**Expected Results:**
- ✅ Tab order is logical
- ✅ Form submits with Enter (where appropriate)
- ✅ Escape closes modals

---

## Test 7: Advanced Features

### Test Case 7.1: Statistics Update

**Steps:**
1. Note current statistics
2. Create a new Draft contract
3. Observe statistics

**Expected Results:**
- ✅ Draft count increases by 1
- ✅ Total value increases
- ✅ Statistics update without page refresh

### Test Case 7.2: Contract Number Format

**Steps:**
1. Create multiple contracts
2. Check contract numbers

**Expected Results:**
- ✅ Format: CNT-2024-0001, CNT-2024-0002, etc.
- ✅ Numbers increment automatically
- ✅ Year matches current year

---

## Console Debugging

### Check JavaScript Console for Errors

Open DevTools (F12) > Console tab

**What to check:**
- ❌ No red error messages
- ✅ Blue info logs from `loadAcceptedProjects()` are OK
- ✅ Network requests return 200 OK status

### Network Tab Verification

Go to Network tab > Filter by "contracts.php"

**For CREATE:**
- Method: POST
- Status: 200
- Response: `{"success": true, "message": "..."}`

**For UPDATE:**
- Method: PUT
- Status: 200
- Response: `{"success": true, ...}`

**For DELETE:**
- Method: DELETE
- Status: 200
- Response: `{"success": true, ...}`

**For READ:**
- Method: GET
- Status: 200
- Response: `{"success": true, "data": [...]}`

---

## Common Issues & Solutions

### Issue 1: "No Projects Available"
**Cause:** No accepted projects in database
**Solution:**
```sql
-- Create a test project
INSERT INTO Project (company_id, client_name, client_email, client_phone, 
                     project_name, description, estimated_budget, status) 
VALUES (YOUR_COMPANY_ID, 'Test Client', 'client@test.com', '0771234567',
        'Test Project', 'Test project description', 100000, 'accepted');
```

### Issue 2: 401 Unauthorized Error
**Cause:** Not logged in or session expired
**Solution:**
1. Check if you're logged in
2. Check `$_SESSION['user_id']` exists
3. Re-login if needed

### Issue 3: Contract Not Appearing After Creation
**Cause:** May be filtered out or pagination issue
**Solution:**
1. Select "All Contracts" from filter
2. Clear search box
3. Refresh page
4. Check database directly

### Issue 4: Delete Button Not Visible
**Cause:** CSS not loading or button not added
**Solution:**
1. Hard refresh: Ctrl + Shift + R
2. Clear browser cache
3. Check if `contracts-enhanced.js` loaded

---

## Performance Testing

### Load Time Test
**Expected:** Page should load in < 2 seconds with 50 contracts

### Pagination Test
**Expected:** Loading 9 more contracts should take < 500ms

### Search Test
**Expected:** Search filtering should be instant (< 100ms)

---

## Security Testing

### SQL Injection Test
**Steps:**
1. Try entering `'; DROP TABLE Contract; --` in search
2. Try in contract title field

**Expected:**
- ✅ Input should be escaped
- ✅ No SQL error
- ✅ No tables dropped

### XSS Test
**Steps:**
1. Try entering `<script>alert('XSS')</script>` in contract title
2. Save and view contract

**Expected:**
- ✅ Script tags should be escaped as text
- ✅ No alert popup
- ✅ Renders as plain text

---

## Final Checklist

Before considering the system complete:

- [ ] All CRUD operations work without errors
- [ ] No console errors
- [ ] All network requests return 200 OK
- [ ] Database records created/updated/deleted correctly
- [ ] Session validation works
- [ ] Error messages are user-friendly
- [ ] Loading states appear during operations
- [ ] Success notifications appear
- [ ] Statistics update correctly
- [ ] Pagination works
- [ ] Filters work
- [ ] Search works
- [ ] Responsive on mobile
- [ ] No security vulnerabilities

---

## Bug Reporting Template

If you find issues, report using this format:

```
**Bug Title:** [Short description]

**Steps to Reproduce:**
1. Step one
2. Step two
3. Step three

**Expected Result:**
What should happen

**Actual Result:**
What actually happened

**Environment:**
- Browser: [Chrome/Firefox/etc.]
- OS: [Windows/Mac/Linux]
- User Role: Company

**Console Errors:**
[Paste any errors from browser console]

**Screenshots:**
[If applicable]
```

---

**Happy Testing! 🚀**

All CRUD operations should work flawlessly. If you encounter any issues, check the console for error messages and verify your database connection.
