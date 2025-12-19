# Debugging Quotations Display Issue

## Changes Made

I've added comprehensive debugging throughout the entire quotation submission and display pipeline to help identify where the issue occurs.

### 1. Frontend JavaScript Logging (repair-requests-db.js)

**Location**: `assets/javascript/company/repair-requests-db.js`

**Added logging to:**

1. **Page Initialization** (Line ~19-28)
   - Logs when DOM is loaded
   - Confirms modal elements are found
   
2. **Data Loading** (Line ~71-80)
   - Logs when loading starts
   - Shows full API response
   - Shows number of quotations retrieved
   
3. **Rendering Pipeline** (Line ~188-250)
   - Checks if DOM elements exist before rendering
   - Shows quotation breakdown by status (pending/accepted/rejected/successful)
   - Logs pending quotation data
   - Shows when rendering starts and completes
   - Shows HTML generation details
   
4. **Quotation Submission** (Line ~563-580)
   - Logs when quotation is saved
   - Confirms data reload completion
   - Shows tab switching action
   - Added 100ms delay before tab switch to ensure DOM updates

### 2. Backend API Logging (company-quotes.php)

**Location**: `api/company-quotes.php`

**Added logging to:**

1. **POST (Create) Handler** (Line ~149-170)
   - Logs incoming data
   - Confirms quotation creation with ID
   - Shows retrieved quotation data
   - Logs any failures or exceptions
   
2. **GET (Retrieve) Handler** (Line ~60-95)
   - Logs applied filters
   - Shows number of quotations retrieved
   - Shows sample quotation data
   - Logs any exceptions

### 3. Log Files Locations

**PHP Errors**: Check XAMPP error logs
- Windows: `C:\xampp\apache\logs\error.log`
- Look for entries with "Creating quotation", "Retrieved quotation", "GET request with filters"

**Browser Console**: Open Developer Tools (F12)
- Console tab will show all JavaScript logs
- Look for the sequence of events after clicking submit

## How to Debug

### Step 1: Clear Previous Data
1. Open browser Developer Tools (F12)
2. Go to Console tab
3. Clear the console
4. Refresh the page

### Step 2: Submit a Quotation
1. Fill out the quotation form
2. Click Submit
3. Watch the console in real-time

### Step 3: Expected Console Output

You should see this sequence:

```
✓ DOM Content Loaded - Initializing page...
✓ Modal elements: [object Object] [object Object] [object Object]
✓ Submit quotation called, editing ID: null
✓ Form data: {request_id: X, user_id: 1, ...}
✓ Creating new quotation with data: {request_id: X, ...}
✓ Response: {success: true, message: "...", data: {...}}
✓ Quotation saved successfully. Reloading data...
✓ Loading submitted quotations...
✓ Quotations API response: {success: true, data: [...], count: X}
✓ Number of quotations: X
✓ Submitted quotations loaded: [...]
✓ Rendering submitted quotations
✓ DOM elements check: { successfulList: [object], pendingList: [object], ... }
✓ Quotations by status: { pending: X, accepted: X, rejected: X, successful: X }
✓ Pending quotations data: [...]
✓ Rendering pending list. Count: X
✓ Rendering X pending quotations
✓ Generated HTML length: XXXX
✓ Pending quotations rendered successfully
✓ Data reloaded. Switching to logs tab...
✓ Clicking logs tab
```

### Step 4: Identify the Problem

**If you see this sequence STOP at any point, that's where the issue is:**

1. **No "DOM Content Loaded"** → JavaScript file not loading
2. **Modal elements show null/undefined** → HTML structure issue
3. **API response shows success: false** → Backend validation failing
4. **Number of quotations: 0** → Database not storing or retrieving
5. **DOM elements check shows null for pendingList** → HTML structure missing element
6. **Pending quotations count: 0** → Status field mismatch or wrong filtering
7. **"Logs tab not found"** → HTML structure issue

### Step 5: Check PHP Logs

1. Open `C:\xampp\apache\logs\error.log`
2. Look for recent entries (scroll to bottom)
3. Find entries with:
   - "Creating quotation with data:"
   - "Quotation created successfully with ID:"
   - "Retrieved quotation:"
   - "GET request with filters:"
   - "Retrieved X quotations"

## Common Issues and Solutions

### Issue 1: Quotations Created but Not Retrieved
**Symptom**: PHP log shows "Quotation created successfully" but GET returns 0 quotations

**Solution**: Check user_id mismatch
- JavaScript uses `currentCompanyId = 1`
- Verify database has quotations with `user_id = 1`
- Run: `SELECT * FROM CompanyQuotation WHERE user_id = 1;`

### Issue 2: Quotations Retrieved but Not Rendered
**Symptom**: Console shows quotations loaded but nothing appears

**Solution**: Check status field
- Verify quotations have `status = 'pending'` (exactly, case-sensitive)
- Run: `SELECT quotation_id, status FROM CompanyQuotation;`
- Check console for "Quotations by status" - should show pending > 0

### Issue 3: DOM Elements Not Found
**Symptom**: Console shows "pendingList element not found"

**Solution**: Verify HTML structure in `repair-requests.php`
- Check `id="pending-quotations-list"` exists
- Check it's inside the correct tab panel
- Check it's not inside a hidden/display:none element

### Issue 4: Tab Not Switching
**Symptom**: Data loads but tab doesn't switch

**Solution**: Check tab structure
- Verify `data-tab="logs"` attribute exists on tab button
- Check querySelector is finding the element
- Console will show "Logs tab not found!" if missing

## Database Verification

Run these queries to verify data:

```sql
-- Check if quotations exist
SELECT COUNT(*) FROM CompanyQuotation;

-- Check quotations for user_id 1
SELECT * FROM CompanyQuotation WHERE user_id = 1;

-- Check quotation statuses
SELECT status, COUNT(*) as count 
FROM CompanyQuotation 
GROUP BY status;

-- Check most recent quotation
SELECT * FROM CompanyQuotation 
ORDER BY created_at DESC 
LIMIT 1;
```

## Quick Fixes

### Fix 1: Clear Browser Cache
```
Ctrl + Shift + Delete → Clear cached files
OR
Hard refresh: Ctrl + F5
```

### Fix 2: Verify Database Connection
Check `config/database.php` has correct credentials

### Fix 3: Restart Apache
Sometimes PDO connections get stuck
- XAMPP Control Panel → Stop Apache → Start Apache

## Next Steps

After reviewing the console output:
1. Share the exact console output sequence
2. Share any PHP error log entries
3. Share database query results
4. I can then pinpoint the exact issue and provide a targeted fix

## Contact Points

The debugging now covers:
- ✅ Frontend initialization
- ✅ Form submission
- ✅ API request/response
- ✅ Backend database operations
- ✅ Data retrieval
- ✅ Filtering by status
- ✅ DOM rendering
- ✅ Tab switching

Every step is logged, so we'll find exactly where it breaks!
